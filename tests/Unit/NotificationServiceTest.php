<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Modules\Settings\services\NotificationService;

class NotificationServiceTest extends TestCase
{
    public function testNotificationServiceMethodsExist(): void
    {
        $service = new NotificationService();
        $this->assertTrue(method_exists($service, 'getAnnouncements'));
        $this->assertTrue(method_exists($service, 'createAnnouncement'));
        $this->assertTrue(method_exists($service, 'getAuditLogs'));
    }
}
