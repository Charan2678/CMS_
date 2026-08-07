<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Core\Permission;

class PermissionTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        Permission::clearCache();
    }

    public function testUnauthenticatedUserHasNoPermissions(): void
    {
        $this->assertFalse(Permission::has('student.view'));
        $this->assertFalse(Permission::has('any.permission'));
    }

    public function testSuperAdminAlwaysHasPermission(): void
    {
        $_SESSION['user_id']   = 1;
        $_SESSION['role_code'] = 'super_admin';

        $this->assertTrue(Permission::has('student.view'));
        $this->assertTrue(Permission::has('any.custom.permission'));
    }
}
