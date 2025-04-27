<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserProperties()
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);

        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }
}
