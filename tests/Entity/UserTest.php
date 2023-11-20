<?php 

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertSame;

class UserTest extends TestCase{
    public function testUser(){
        $user = new User();
        $user->setUsername('userTest');
        $user->setPassword('passwordTest');
        $user->setEmail('emailTest');
        $user->setRoles(['ROLE_ADMIN']);

        $task = new Task();
        $task->setTitle('new task');
        $task->setContent('new task');
        
        $user->addTask($task);

        assertSame($task, $user->getTasks()[0]);
        
        $user->removeTask($task);

        assertSame(0, count($user->getTasks()));
        
        assertSame('userTest', $user->getUsername());
        assertSame('userTest', $user->getUserIdentifier());
        assertSame('passwordTest', $user->getPassword());
        assertSame('emailTest', $user->getEmail());
        assertSame(['ROLE_ADMIN','ROLE_USER'], $user->getRoles());
        assertSame(true, $user->isAdmin());

    }



}