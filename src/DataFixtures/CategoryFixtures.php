<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Film;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $films = $manager->getRepository(Film::class)->findAll();
        
        
        for ($i = 0; $i < 15; $i++) {
            $category = new Category();
            $category->setName($faker->word);
            
            
            foreach ($films as $film) {
                $category->addFilm($film);
            }
            
            $manager->persist($category);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FilmFixtures::class, 
        ];
    }
}
