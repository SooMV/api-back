<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Film;
use App\Entity\Like;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FilmFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $film = new Film();
            $film->setTitle($faker->sentence(3))
                  ->setDuration($faker->numberBetween(80, 180))
                  ->setRealisateurFirstName($faker->firstName)
                  ->setRealisateurLastName($faker->lastName)
                  ->setReleaseYear($faker->numberBetween(1990, 2022))
                  ->setDescription($faker->sentence(30));

            for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {
                $category = new Category();
                $category->setName($faker->word);
                $manager->persist($category);
                $film->addCategory($category);
            }

            $manager->persist($film);

            for ($k = 0; $k < $faker->numberBetween(0, 10); $k++) {
                $like = new Like();
                $like->setFilm($film);
                $manager->persist($like);
            }

            // Ajout d'une référence à chaque film pour pouvoir y accéder dans d'autres fixtures
            $this->addReference('film_' . $i, $film);
        }

        $manager->flush();
    }
}