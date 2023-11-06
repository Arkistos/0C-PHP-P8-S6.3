<?php 

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase{
    public function testDefault(){
        $task = new Task();
        $task->setTitle('Task1');
        
        $this->assertSame('Task1', $task->getTitle());
    }

    public function testTaskLiaisonUser(){
        $task = new Task();
        $user = new User();
        $task->setUser($user);
        $this->assertEquals('App\Entity\User',get_class($task->getUser()));
    }

}