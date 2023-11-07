<?php 

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase{
    public function testTask(){
        $task = new Task();
        $task->setTitle('Task1');
        $task->setContent('test task');
        $task->setDone(true);
        $user = new User();
        $task->setUser($user);
        
        
        $this->assertSame('Task1', $task->getTitle());
        $this->assertSame('test task', $task->getContent());
        $this->assertSame(true, $task->isDone());

       
        $this->assertEquals('App\Entity\User',get_class($task->getUser()));
    }



}