<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\Review;
use App\Entity\Users;
use App\Repository\ProductsRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/reviews')]
class ReviewController extends AbstractController
{
    // Получение всех отзывов (с возможностью фильтрации по продукту и пользователю)
    #[Route('/', name: 'api_get_reviews', methods: ['GET'])]
    public function getReviews(
        Request $request,
        ReviewRepository $reviewRepository
    ): JsonResponse {
        $productId = $request->query->get('product_id');
        $userId = $request->query->get('user_id');
        $moderated = $request->query->get('is_moderate');

        $criteria = [];
        if ($productId) {
            $criteria['idProduct'] = $productId;
        }
        if ($userId) {
            $criteria['users'] = $userId;
        }
        if ($moderated !== null) {
            // Преобразуем строковый параметр в boolean
            $isModerate = filter_var($moderated, FILTER_VALIDATE_BOOLEAN);
            $criteria['isModerate'] = $isModerate;
        }

        $reviews = $reviewRepository->findBy($criteria, ['createdAt' => 'DESC']);
        $reviewsData = array_map([$this, 'serializeReview'], $reviews);

        return $this->json($reviewsData);
    }

    // Получение одного отзыва по ID
    #[Route('/{id}', name: 'api_get_review', methods: ['GET'])]
    public function getReview(Review $review): JsonResponse
    {
        return $this->json($this->serializeReview($review));
    }

    // Создание нового отзыва
    #[Route('/', name: 'api_create_review', methods: ['POST'])]
    public function createReview(
        Request $request,
        EntityManagerInterface $em,
        ProductsRepository $productsRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Валидация входных данных
        $constraints = new Assert\Collection([
            'product_id' => [new Assert\NotBlank(), new Assert\Type('numeric')],
            'user_id' => [new Assert\NotBlank(), new Assert\Type('numeric')],
            'rating' => [
                new Assert\NotBlank(),
                new Assert\Type('numeric'),
                new Assert\Range(['min' => 1, 'max' => 5])
            ],
            'text' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 10, 'max' => 2000])
            ],
        ]);

        $errors = $validator->validate($data, $constraints);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string)$errors], Response::HTTP_BAD_REQUEST);
        }

        // Поиск продукта и пользователя
        $product = $productsRepository->find($data['product_id']);
        $user = $em->getRepository(Users::class)->find($data['user_id']);

        if (!$product || !$user) {
            return $this->json(
                ['message' => 'Product or user not found'],
                Response::HTTP_NOT_FOUND
            );
        }



        // Создание отзыва
        $review = new Review();
        $review->setIdProduct($product);
        $review->setUsers($user);
        $review->setRating($data['rating']);
        $review->setTextReview($data['text']);
        $review->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Moscow')));
        $review->setIsModerate(false); // По умолчанию отзыв не модерирован

        $em->persist($review);
        $em->flush();

        return $this->json(
            $this->serializeReview($review),
            Response::HTTP_CREATED
        );
    }

    // Обновление отзыва
    #[Route('/{id}', name: 'api_update_review', methods: ['PUT'])]
    public function updateReview(
        Review $review,
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Валидация
        $constraints = new Assert\Collection([
            'is_moderate' => [new Assert\Type('bool')]
        ]);

        $errors = $validator->validate($data, $constraints);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string)$errors], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['is_moderate'])) {
            $review->setIsModerate($data['is_moderate']);
        }

        $em->flush();

        return $this->json($this->serializeReview($review));
    }

    // Удаление отзыва
    #[Route('/{id}', name: 'api_delete_review', methods: ['DELETE'])]
    public function deleteReview(
        Review $review,
        EntityManagerInterface $em
    ): JsonResponse {
        $em->remove($review);
        $em->flush();

        return $this->json(
            ['message' => 'Review deleted successfully'],
            Response::HTTP_ACCEPTED
        );
    }

    // Получение отзывов для конкретного продукта
    #[Route('/product/{productId}', name: 'api_get_product_reviews', methods: ['GET'])]
    public function getProductReviews(
        int $productId,
        ReviewRepository $reviewRepository,
        ProductsRepository $productsRepository
    ): JsonResponse {
        $product = $productsRepository->find($productId);
        if (!$product) {
            return $this->json(
                ['message' => 'Product not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $reviews = $reviewRepository->findBy(
            ['idProduct' => $product, 'isModerate' => true],
            ['createdAt' => 'DESC']
        );

        $reviewsData = array_map([$this, 'serializeReview'], $reviews);

        // Расчет среднего рейтинга
        $averageRating = $reviewRepository->getAverageRatingForProduct($product);

        return $this->json([
            'product_id' => $productId,
            'average_rating' => $averageRating,
            'total_reviews' => count($reviews),
            'reviews' => $reviewsData
        ]);
    }


    #[Route('/moderation-status/{status}', name: 'api_get_reviews_by_moderation', methods: ['GET'])]
    public function getReviewsByModerationStatus(
        string $status,
        ReviewRepository $reviewRepository
    ): JsonResponse {
        // Валидация статуса
        if (!in_array($status, ['moderated', 'unmoderated'])) {
            return $this->json(
                ['message' => 'Invalid status. Use "moderated" or "unmoderated"'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $isModerate = $status === 'moderated';
        $reviews = $reviewRepository->findBy(
            ['isModerate' => $isModerate],
            ['createdAt' => 'DESC']
        );

        return $this->json(array_map([$this, 'serializeReview'], $reviews));
    }


    // Вспомогательный метод для сериализации отзыва
    private function serializeReview(Review $review): array
    {
        $user = $review->getUsers();

        return [
            'id' => $review->getId(),
            'product_id' => $review->getIdProduct()->getId(),
            'product_name' => $review->getIdProduct()->getNameProduct(), // Добавлено название продукта
            'user' => [
                'id' => $user ? $user->getId() : null,
                'name' => $user ? $user->getNameUser() : null,
                'email' => $user ? $user->getEmail() : null, // Добавлен email
            ],
            'rating' => $review->getRating(),
            'text' => $review->getTextReview(),
            'created_at' => $review->getCreatedAt()->format('Y-m-d H:i:s'),
            'is_moderate' => $review->isModerate(),
            'moderation_status' => $review->isModerate() ? 'moderated' : 'unmoderated' // Добавлен текстовый статус
        ];
    }
}