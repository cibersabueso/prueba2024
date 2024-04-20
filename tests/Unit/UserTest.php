<?php

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserTest extends TestCase {
    public function testCreateUserWithValidData() {
        $user = new User();
        $user->fullName = "Juan Pérez";
        $user->document = "12345678";
        $user->email = "juan@example.com";
        $user->password = "securepassword";
        $user->userType = "common";

        $this->assertEquals("Juan Pérez", $user->fullName);
        $this->assertEquals("12345678", $user->document);
        $this->assertEquals("juan@example.com", $user->email);
        $this->assertEquals("securepassword", $user->password);
        $this->assertEquals("common", $user->userType);
    }

    public function testUserEmailMustBeUnique() {
        
        
        $this->expectException(Exception::class);
        $user1 = new User();
        $user1->email = "juan@example.com";


        $user2 = new User();
        $user2->email = "juan@example.com";
       
    }

    
}