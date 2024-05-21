<?php

namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Commentaire;
use App\Entity\Like;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Remplacer l'importation

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setUsername($faker->userName);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));

            $manager->persist($user);

            for ($j = 0; $j < 100; $j++) {
                $commentaire = new Commentaire();
                $commentaire->setContent($faker->paragraph);
                $commentaire->setPublishedAt(new \DateTimeImmutable()); 
                $commentaire->setUser($user);

                $manager->persist($commentaire);
            }
        }

        $manager->flush();
    }
}
