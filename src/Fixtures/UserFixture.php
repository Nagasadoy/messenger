<?php

namespace App\Fixtures;

use App\Model\User\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class UserFixture extends Fixture
{

    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i=0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $password = $this->hasher->hashPassword($user, $faker->word);
            $user->setPassword($password);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
