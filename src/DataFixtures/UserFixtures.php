<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->makeUser('Admin', 'password', 'email@admin.com', ['ROLE_ADMIN'], $manager);
        $this->makeUser('Anonyme', '', '', ['ROLE_USER'], $manager);
        $this->makeUser('User', 'password', 'email@user.com', ['ROLE_USER'], $manager);

        $manager->flush();
    }

    private function makeUser(
        string $username,
        string $password,
        string $email,
        array $roles,
        ObjectManager $manager
    ): void {
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setEmail($email);
        $user->setRoles($roles);

        $this->addReference($user->getUsername(), $user);

        $manager->persist($user);
    }
}
