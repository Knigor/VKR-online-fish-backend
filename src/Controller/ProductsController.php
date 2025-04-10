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

    // Метод для получения товаров по категории с фильтрацией и поиском
    #[Route('/api/products/category', name: 'api_get_products_by_category', methods: ['GET'])]
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
    #[Route('/api/products', name: 'api_add_product_form_data', methods: ['POST'])]
    public function addProductFormData(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $nameProduct = $request->get('nameProduct');
        $descriptionProduct = $request->get('descriptionProduct');
        $priceProduct = $request->get('priceProduct');
        $quantityProduct = $request->get('quantityProduct');
        $productWeight = $request->get('productWeight');
        $imageUrlProduct = $request->files->get('imageUrlProduct');
        $typeProducts = $request->get('typeProducts');
        $categoryId = $request->get('categoryId');
        $userId = $request->get('userId'); // Получаем userId

        // Получаем категорию и пользователя
        $category = $em->getRepository(Categories::class)->find($categoryId);
        $user = $em->getRepository(Users::class)->find($userId);

        if (!$category) {
            return $this->json(['message' => 'Category not found'], Response::HTTP_BAD_REQUEST);
        }

        if (!$user) {
            return $this->json(['message' => 'User not found'], Response::HTTP_BAD_REQUEST);
        }

        // Перемещаем загруженное изображение в папку
        $imageUrl = null;
        if ($imageUrlProduct instanceof UploadedFile) {
            $imageUrl = $this->uploadImage($imageUrlProduct);
        }

        // Создаем новый товар
        $product = new Products();
        $product->setNameProduct($nameProduct)
            ->setDescriptionProduct($descriptionProduct)
            ->setPriceProduct($priceProduct)
            ->setQuantityProduct($quantityProduct)
            ->setProductWeight($productWeight)
            ->setImageUrlProduct($imageUrl)
            ->setTypeProducts($typeProducts)
            ->setCategories($category)
            ->setUsers($user); // Устанавливаем пользователя

        $em->persist($product);
        $em->flush();

        return $this->json(['message' => 'Product added successfully']);
    }

    // Метод для редактирования товара с загрузкой изображения
    #[Route('/api/products/{id}', name: 'api_edit_product_form_data', methods: ['POST'])]
    public function editProductFormData(Request $request, ProductsRepository $productsRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        $product = $productsRepository->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        // Получаем данные из FormData
        $nameProduct = $request->get('nameProduct', $product->getNameProduct());
        $descriptionProduct = $request->get('descriptionProduct', $product->getDescriptionProduct());
        $priceProduct = $request->get('priceProduct', $product->getPriceProduct());
        $quantityProduct = $request->get('quantityProduct', $product->getQuantityProduct());
        $productWeight = $request->get('productWeight', $product->getProductWeight());
        $imageUrlProduct = $request->files->get('imageUrlProduct');
        $typeProducts = $request->get('typeProducts', $product->getTypeProducts());
        $categoryId = $request->get('categoryId');
        $userId = $request->get('userId'); // Получаем userId для редактирования

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
            ->setTypeProducts($typeProducts)
            ->setCategories($category)
            ->setUsers($user); // Обновляем пользователя

        // Если файл изображения был загружен, сохраняем его
        if ($imageUrlProduct instanceof UploadedFile) {
            $imageUrl = $this->uploadImage($imageUrlProduct);
            $product->setImageUrlProduct($imageUrl);
        }

        $em->flush();

        return $this->json(['message' => 'Product updated successfully']);
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
