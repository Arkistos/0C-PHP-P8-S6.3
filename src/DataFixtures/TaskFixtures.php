<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->makeTask('Task 1', 'This is a task', 'Admin', $manager);
        $this->makeTask('testAnonymous', 'This is a test task to try anonymous', 'Anonyme', $manager);

        $manager->flush();
    }

    private function makeTask(string $title, string $content, string $username, ObjectManager $manager): void
    {
        $task = new Task();
        $task->setTitle($title);
        $task->setContent($content);
        $task->setCreatedAt(new \DateTime());
        $task->setDone(false);
        $task->setUser($this->getReference($username, User::class));

        $manager->persist($task);
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
