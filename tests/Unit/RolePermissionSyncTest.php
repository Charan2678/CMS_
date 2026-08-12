<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Core\Permission;

class RolePermissionSyncTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
        Permission::clearCache();
    }

    public function testSuperAdminHasFullPermissions(): void
    {
        $_SESSION['user_id']   = 1;
        $_SESSION['role_id']   = 1;
        $_SESSION['role_code'] = 'super_admin';
        Permission::clearCache();

        $this->assertTrue(Permission::has('student.view'));
        $this->assertTrue(Permission::has('fee.manage'));
        $this->assertTrue(Permission::has('hostel.manage'));
        $this->assertTrue(Permission::has('library.manage'));
        $this->assertTrue(Permission::has('transport.manage'));
        $this->assertTrue(Permission::has('canteen.manage'));
    }

    public function testStudentRoleRestrictedFromAdminActions(): void
    {
        $_SESSION['user_id']   = 10;
        $_SESSION['role_id']   = 10;
        $_SESSION['role_code'] = 'student';
        Permission::clearCache();

        $this->assertFalse(Permission::has('fee.manage'));
        $this->assertFalse(Permission::has('hostel.manage'));
        $this->assertFalse(Permission::has('leave.review'));
        $this->assertFalse(Permission::has('library.issue'));
    }

    public function testHostelWardenPermissions(): void
    {
        $_SESSION['user_id']   = 7;
        $_SESSION['role_id']   = 7;
        $_SESSION['role_code'] = 'warden';
        Permission::clearCache();

        $this->assertFalse(Permission::has('academic.edit_marks'));
        $this->assertFalse(Permission::has('canteen.manage'));
    }

    public function testFacultyPermissions(): void
    {
        $_SESSION['user_id']   = 3;
        $_SESSION['role_id']   = 3;
        $_SESSION['role_code'] = 'faculty';
        Permission::clearCache();

        $this->assertFalse(Permission::has('fee.collect'));
        $this->assertFalse(Permission::has('transport.manage'));
    }
}
