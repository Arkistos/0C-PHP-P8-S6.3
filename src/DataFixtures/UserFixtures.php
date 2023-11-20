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
        $user = $this->makeUser('Admin', 'password', 'email@admin.com', ['ROLE_ADMIN']);
        $manager->persist($user);

        $manager->flush();
    }

    private function makeUser(
        string $username, 
        string $password, 
        string $email, 
        array $roles
    ):User{
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setEmail($email);
        $user->setRoles($roles);

        $this->addReference($user->getUsername(), $user);

        return $user;
    }
}
