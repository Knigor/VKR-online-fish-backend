<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Products;
use App\Entity\Users;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    // Метод для получения всех товаров с фильтрацией и поиском
    #[Route('/api/products', name: 'api_get_products', methods: ['GET'])]
    public function getProducts(
        Request $request,
        ProductsRepository $productsRepository
    ): JsonResponse {
        // Получаем параметры запроса
        $sort = $request->query->get('sort', 'default');
        $search = $request->query->get('search', '');

        // Получаем товары с учетом фильтрации и поиска
        $products = $productsRepository->findWithFiltersAndSearch($sort, $search);

        $productData = [];
        foreach ($products as $product) {
            $productData[] = [
                'id' => $product->getId(),
                'nameProduct' => $product->getNameProduct(),
                'descriptionProduct' => $product->getDescriptionProduct(),
                'priceProduct' => $product->getPriceProduct(),
                'quantityProduct' => $product->getQuantityProduct(),
                'imageUrlProduct' => $product->getImageUrlProduct(),
                'typeProducts' => $product->getTypeProducts(),
                'categoryId' => $product->getCategories()->getId(),
                'productWeight' => $product->getProductWeight(),
            ];
        }

        return $this->json($productData);
    }

    // Метод на получение категорий

    #[Route('/api/categories', name: 'api_get_categories', methods: ['GET'])]
    public function getCategories(CategoriesRepository $categoriesRepository): JsonResponse
    {
        $categories = $categoriesRepository->findBy([], ['id' => 'ASC']);

        $categoriesData = [];
        foreach ($categories as $category) {
            $categoriesData[] = [
                'id' => $category->getId(),
                'name' => $category->getNameCategories(),
            ];
        }

        return $this->json($categoriesData);
    }

    // получаем товар по категории
    #[Route('/api/products/{id}', name: 'api_get_product_by_id', methods: ['GET'])]
    public function getProductById(int $id, ProductsRepository $productsRepository): JsonResponse
    {
        $product = $productsRepository->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $productData = [
            'id' => $product->getId(),
            'nameProduct' => $product->getNameProduct(),
            'descriptionProduct' => $product->getDescriptionProduct(),
            'priceProduct' => $product->getPriceProduct(),
            'quantityProduct' => $product->getQuantityProduct(),
            'imageUrlProduct' => $product->getImageUrlProduct(),
            'typeProducts' => $product->getTypeProducts(),
            'categoryId' => $product->getCategories()->getId(),
            'productWeight' => $product->getProductWeight(),
        ];

        return $this->json($productData);
    }


    // Метод для получения товаров по категории с фильтрацией и поиском
    #[Route('/api/productsCategory/', name: 'api_get_products_by_category', methods: ['GET'])]
    public function getProductsByCategory(
        Request $request,
        ProductsRepository $productsRepository,
        CategoriesRepository $categoriesRepository
    ): JsonResponse {
        // Получаем параметры запроса
        $categoryId = $request->query->get('category_id');
        $sort = $request->query->get('sort', 'default');
        $search = $request->query->get('search', '');

        if (!$categoryId) {
            return $this->json(['message' => 'Category ID is required'], Response::HTTP_BAD_REQUEST);
        }

        // Ищем категорию по ID
        $category = $categoriesRepository->find($categoryId);

        if (!$category) {
            return $this->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        // Получаем товары с учетом фильтрации и поиска
        $products = $productsRepository->findByCategoryWithFiltersAndSearch($category, $sort, $search);

        $productData = [];
        foreach ($products as $product) {
            $productData[] = [
                'id' => $product->getId(),
                'nameProduct' => $product->getNameProduct(),
                'descriptionProduct' => $product->getDescriptionProduct(),
                'priceProduct' => $product->getPriceProduct(),
                'quantityProduct' => $product->getQuantityProduct(),
                'imageUrlProduct' => $product->getImageUrlProduct(),
                'typeProducts' => $product->getTypeProducts(),
                'categoryId' => $product->getCategories()->getId(),
                'productWeight' => $product->getProductWeight(),
            ];
        }

        return $this->json($productData);
    }


    // Метод для добавления товара с загрузкой изображения
    #[Route('/api/products', name: 'api_add_product', methods: ['POST'])]
    public function addProduct(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Валидация обязательных полей
        if (!isset($data['nameProduct']) || !isset($data['categoryId']) || !isset($data['userId'])) {
            return $this->json(['message' => 'Required fields are missing'], Response::HTTP_BAD_REQUEST);
        }

        // Получаем данные из JSON
        $nameProduct = $data['nameProduct'];
        $descriptionProduct = $data['descriptionProduct'] ?? null;
        $priceProduct = $data['priceProduct'] ?? 0;
        $quantityProduct = $data['quantityProduct'] ?? 0;
        $productWeight = $data['productWeight'] ?? 0;
        $imageUrl = $data['imageUrl'] ?? null; // Теперь просто принимаем ссылку
        $typeProducts = $data['typeProducts'] ?? null;
        $categoryId = $data['categoryId'];
        $userId = $data['userId'];

        // Получаем категорию и пользователя
        $category = $em->getRepository(Categories::class)->find($categoryId);
        $user = $em->getRepository(Users::class)->find($userId);

        if (!$category) {
            return $this->json(['message' => 'Category not found'], Response::HTTP_BAD_REQUEST);
        }

        if (!$user) {
            return $this->json(['message' => 'User not found'], Response::HTTP_BAD_REQUEST);
        }

        // Создаем новый товар
        $product = new Products();
        $product->setNameProduct($nameProduct)
            ->setDescriptionProduct($descriptionProduct)
            ->setPriceProduct($priceProduct)
            ->setQuantityProduct($quantityProduct)
            ->setProductWeight($productWeight)
            ->setImageUrlProduct($imageUrl) // Просто сохраняем ссылку
            ->setTypeProducts($typeProducts)
            ->setCategories($category)
            ->setUsers($user);

        $em->persist($product);
        $em->flush();

        return $this->json([
            'message' => 'Product added successfully',
            'productId' => $product->getId()
        ], Response::HTTP_CREATED);
    }

    // Метод для удаления товара
    #[Route('/api/products/{id}', name: 'api_delete_product', methods: ['DELETE'])]
    public function deleteProduct(
        int $id,
        ProductsRepository $productsRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $product = $productsRepository->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($product);
        $em->flush();

        return $this->json(['message' => 'Product deleted successfully']);
    }

    // Метод для редактирования товара с загрузкой изображения
    #[Route('/api/products/{id}', name: 'api_edit_product', methods: ['PUT'])]
    public function editProduct(Request $request, ProductsRepository $productsRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        $product = $productsRepository->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Получаем данные из JSON (используем существующие значения как fallback)
        $nameProduct = $data['nameProduct'] ?? $product->getNameProduct();
        $descriptionProduct = $data['descriptionProduct'] ?? $product->getDescriptionProduct();
        $priceProduct = $data['priceProduct'] ?? $product->getPriceProduct();
        $quantityProduct = $data['quantityProduct'] ?? $product->getQuantityProduct();
        $productWeight = $data['productWeight'] ?? $product->getProductWeight();
        $imageUrl = $data['imageUrl'] ?? $product->getImageUrlProduct(); // Просто принимаем ссылку
        $typeProducts = $data['typeProducts'] ?? $product->getTypeProducts();

        // Категория и пользователь - если не указаны, оставляем текущие
        $categoryId = $data['categoryId'] ?? $product->getCategories()->getId();
        $userId = $data['userId'] ?? $product->getUsers()->getId();

        // Получаем категорию и пользователя
        $category = $em->getRepository(Categories::class)->find($categoryId);
        $user = $em->getRepository(Users::class)->find($userId);

        if (!$category) {
            return $this->json(['message' => 'Category not found'], Response::HTTP_BAD_REQUEST);
        }

        if (!$user) {
            return $this->json(['message' => 'User not found'], Response::HTTP_BAD_REQUEST);
        }

        // Обновляем товар
        $product->setNameProduct($nameProduct)
            ->setDescriptionProduct($descriptionProduct)
            ->setPriceProduct($priceProduct)
            ->setQuantityProduct($quantityProduct)
            ->setProductWeight($productWeight)
            ->setImageUrlProduct($imageUrl) // Просто сохраняем ссылку
            ->setTypeProducts($typeProducts)
            ->setCategories($category)
            ->setUsers($user);

        $em->flush();

        return $this->json([
            'message' => 'Product updated successfully',
            'productId' => $product->getId()
        ]);
    }

    // Вспомогательный метод для загрузки изображения
    private function uploadImage(UploadedFile $image): string
    {
        // Генерация уникального имени для файла
        $newFilename = uniqid('', true) . '.' . $image->guessExtension();

        // Перемещение файла в папку public/uploads/products
        $image->move(
            $this->getParameter('kernel.project_dir') . '/public/uploads/products',
            $newFilename
        );

        return 'uploads/products/' . $newFilename;
    }
}
