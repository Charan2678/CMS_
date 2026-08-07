<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Modules\Authentication\services\AuthService;

class AuthServiceTest extends TestCase
{
    public function testRoleValidationLogic(): void
    {
        $authService = new AuthService();
        $reflector = new \ReflectionClass(AuthService::class);
        $method = $reflector->getMethod('login');
        
        $params = array_map(fn($p) => $p->getName(), $method->getParameters());
        $this->assertContains('roleType', $params);
    }
}
