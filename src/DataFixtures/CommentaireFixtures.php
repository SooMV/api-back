<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Like;
use App\Entity\User;
use App\Entity\Commentaire;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Récupérer tous les utilisateurs disponibles
        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 0; $i < 200; $i++) {
            $commentaire = new Commentaire();
            $dateTime = $faker->dateTimeThisDecade();
            $dateTimeImmutable = \DateTimeImmutable::createFromMutable($dateTime);

            $commentaire->setPublishedAt($dateTimeImmutable)
                        ->setContent($faker->sentence(25));

            // Récupération d'un film aléatoire parmi les références
            $film = $this->getReference('film_' . $faker->numberBetween(0, 19));
            $commentaire->setFilm($film);

            // Sélectionner aléatoirement un utilisateur parmi ceux disponibles
            $user = $faker->randomElement($users);
            $commentaire->setUser($user);

            // Ajout de likes pour le commentaire
            for ($j = 0; $j < $faker->numberBetween(0, 10); $j++) {
                $like = new Like();
                $like->setCommentaire($commentaire);
                $manager->persist($like);
            }

            $manager->persist($commentaire);
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
