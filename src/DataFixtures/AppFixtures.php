<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Products;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $product = new Products();
        $product->setNameProduct('Рыба');
        $product->setPriceProduct(1200);
        $product->setDescriptionProduct('Вкусная красная рыба, всем советую');
        $product->setTypeProducts('Замороженная');
        $product->setQuantityProduct(11);




        $manager->flush();
    }
}
