<?php 

namespace App\Tests\Entity;


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
        
        assertSame('userTest', $user->getUsername());
        assertSame('userTest', $user->getUserIdentifier());
        assertSame('passwordTest', $user->getPassword());
        assertSame('emailTest', $user->getEmail());
        assertSame(['ROLE_ADMIN','ROLE_USER'], $user->getRoles());
        assertSame(true, $user->isAdmin());

    }



}