<?php

namespace App\DataFixtures;

use App\Entity\Like;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Film;

class LikeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $filmRepository = $manager->getRepository(Film::class);
        $films = $filmRepository->findAll();

        if (empty($films)) {
            throw new \LogicException('No films found. Please load FilmFixtures first.');
        }

        foreach ($films as $film) {
            // Ajouter un nombre de likes aléatoire pour chaque film
            $numLikes = rand(5, 20); // Nombre de likes aléatoire entre 5 et 20
            for ($i = 0; $i < $numLikes; $i++) {
                $like = new Like();
                $like->setFilm($film);
                $manager->persist($like);
            }
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