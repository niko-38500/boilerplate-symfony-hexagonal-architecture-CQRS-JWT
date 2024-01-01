<?php

namespace App\FrameworkInfrastructure\Infrastructure\DataFixtures;

use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User('johnnySilverhand', 'john@doe.fr', 'root');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'root'));

        $manager->persist($user);
        $manager->flush();
    }
}
