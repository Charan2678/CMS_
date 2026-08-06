<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Core\Security;

class SecurityTest extends TestCase
{
    public function testPasswordHashingAndVerification(): void
    {
        $password = 'TestSecret123!';
        $hash = Security::hashPassword($password);

        $this->assertNotEmpty($hash);
        $this->assertTrue(Security::verifyPassword($password, $hash));
        $this->assertFalse(Security::verifyPassword('WrongPassword', $hash));
    }
}
