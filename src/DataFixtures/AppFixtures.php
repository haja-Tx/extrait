<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Property\Property;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create();

        for ($i = 0; $i<10; $i++){
            $property = new Property();
             
            $property->setTitle($faker->name())
                     ->setDescription($faker->text())
                     ->setEstimatePrice($faker->randomNumber(5))
                     ->setPartPrice($faker->randomNumber(2))
                     ->setRateOfReturn(0.01)
                     ->setPlusValue(0.5)
                     ->setStatus("published")
                     ->setThumbnail('img-1.png')
                     ->setCreatedAt(new \DateTime())
                     ->setUpdatedAt(new \DateTime());
            $manager->persist($property);

        }

        $manager->flush();
    }
}
