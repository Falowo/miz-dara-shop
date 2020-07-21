<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Size;
use App\Entity\Stock;
use App\Entity\Tag;
use App\Entity\Tint;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
         $faker = Factory::create('en_US');


        $ts = [];
        for($i=0; $i<9; $i++){
            $ts[]=$faker->colorName;
        }
        $tts = array_unique($ts);


        $tints = [];
       foreach ($tts as $tt){
            $tint = new Tint();
            $tint->setName($tt);
            $manager->persist($tint);
            $tints[] = $tint;
        }

        $uniqueSize= new Size();
        $uniqueSize->setName('Unique size');
        $manager->persist($uniqueSize);


        $smallNumberSizes=[];
        for ($i=20; $i<36; $i++){
            $size= new Size();
            $size -> setName($i);
            $manager->persist($size);
            $smallNumberSizes[] = $size;
        }

        $bigNumberSizes = [];
        for ($i=36; $i<48; $i++){
            $size= new Size();
            $size -> setName($i);
            $manager->persist($size);
            $bigNumberSizes[] = $size;
        }

        $lSizes=['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        $letterSizes=[];
        foreach ($lSizes as $l){
            $size = new Size();
            $size->setName($l);
            $manager->persist($size);
            $letterSizes[] = $size;

        }


        foreach (['Adult', 'Kid', 'Baby'] as $age){
            $ancestorCategory = new Category();
            $ancestorCategory
                ->setName($age)
                ->setHasParent(false)
                ;
            $manager->persist($ancestorCategory);
            foreach (['Male', 'Female'] as $sex){
                $grandParentCategory = new Category();
                $grandParentCategory
                    ->setName($sex)
                    ->setParent($ancestorCategory)
                    ->setHasParent(true)
                    ;
                $ancestorCategory->addCategory($grandParentCategory);
                $manager->persist($grandParentCategory);
                foreach (['Clothes', 'Shoes', 'Accessories'] as $pCategory){
                    $parentCategory = new Category();
                    $parentCategory
                        ->setName($pCategory)
                        ->setHasParent(true);
                    $grandParentCategory->addCategory($parentCategory);
                    $manager->persist($parentCategory);
                    for($i=0; $i<4; $i++){
                        $category = new Category();
                        $category
                            ->setName($faker->words(2, true))
                            ->setHasParent(true);
                        $parentCategory->addCategory($category);
                        $manager->persist($category);
                        for($j=0; $j<4; $j++){
                            $product = new Product();
                            $tag = new Tag();
                            $tag->setName($faker->word);
                            $manager->persist($tag);
                            $product
                                ->setName($faker->words(3, true))
                                ->setInfo($faker->sentence(16))
                                ->addCategory($category)
                                ->setPrice($faker->numberBetween(10000, 50000))
                                ->addTag($tag)

                            ;



                            foreach ($letterSizes as $size){
                                foreach ($tints as $tint){
                                    $stock = new Stock();
                                    $stock
                                        ->setProduct($product)
                                        ->setTint($tint)
                                        ->setQuantity($faker->numberBetween(0, 50))
                                        ->setSize($size)
                                    ;

                                    $manager->persist($stock);
                                    $product->addStock($stock)
                                        ->setHasStock(null)
                                    ;

                                }
                            }

                            $manager->persist($product);

                        }
                        for($j=0; $j<4; $j++){
                            $tag = new Tag();
                            $tag->setName($faker->word);
                            $manager->persist($tag);
                            $product
                                ->setName($faker->words(3, true))
                                ->setInfo($faker->sentence(16))
                                ->addCategory($category)
                                ->setPrice($faker->numberBetween(30000, 50000))
                                ->setDiscountPrice($faker->numberBetween(10000, 29000))
                                ->addTag($tag)

                            ;



                            foreach ($tints as $tint){
                                $stock = new Stock();
                                $stock
                                    ->setProduct($product)
                                    ->setTint($tint)
                                    ->setQuantity($faker->numberBetween(0, 50))
                                    ->setSize($uniqueSize)
                                ;
                                $manager->persist($stock);
                                $product->addStock($stock)
                                    ->setHasStock(null);
                            }

                            $manager->persist($product);

                        }
                        for($j=0; $j<4; $j++){
                            $product = new Product();
                            $tag = new Tag();
                            $tag->setName($faker->word);
                            $manager->persist($tag);
                            $product
                                ->setName($faker->words(3, true))
                                ->setInfo($faker->sentence(16))
                                ->addCategory($category)
                                ->setPrice($faker->numberBetween(30000, 50000))
                                ->setDiscountPrice($faker->numberBetween(10000, 29000))
                                ->addTag($tag)
                            ;




                            foreach ($bigNumberSizes as $size){
                                $stock = new Stock();
                                $stock
                                    ->setProduct($product)
                                    ->setTint($tints[3])
                                    ->setQuantity($faker->numberBetween(0, 50))
                                    ->setSize($size)
                                ;
                                $manager->persist($stock);
                                $product->addStock($stock);
                                $product->setHasStock(null);
                            }

                            $manager->persist($product);

                        }

                        for($j=0; $j<4; $j++){
                            $product = new Product();
                            $tag = new Tag();
                            $tag->setName($faker->word);
                            $manager->persist($tag);
                            $product
                                ->setName($faker->words(3, true))
                                ->setInfo($faker->sentence(16))
                                ->addCategory($category)
                                ->setPrice($faker->numberBetween(30000, 50000))
                                ->setDiscountPrice($faker->numberBetween(10000, 29000))
                                ->addTag($tag)
                            ;


                            $stock = new Stock();
                            $stock
                                ->setProduct($product)
                                ->setTint($tints[0])
                                ->setQuantity($faker->numberBetween(0, 50))
                                ->setSize($uniqueSize)
                            ;

                            $manager->persist($stock);
                            $product->addStock($stock);
                            $product->setHasStock(null);

                            $manager->persist($product);

                        }

                    }
                }
            }
        }

        $manager->flush();
    }
}
