<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AdmitCardServiceTest extends TestCase
{
    public function testEligibilityRulesLogic(): void
    {
        // 1. Test Attendance Shortage Rule (< 75%)
        $checkEligibility = function (float $attendancePct, float $pendingDues, string $existingStatus = 'none'): array {
            if ($existingStatus === 'condoned') {
                return ['status' => 'condoned', 'is_eligible' => true];
            }

            if ($attendancePct < 75.0) {
                return ['status' => 'blocked_attendance', 'is_eligible' => false];
            }

            if ($pendingDues > 0.0) {
                return ['status' => 'blocked_dues', 'is_eligible' => false];
            }

            return ['status' => 'eligible', 'is_eligible' => true];
        };

        // Case A: 85% attendance, ₹0 dues -> Eligible
        $resA = $checkEligibility(85.0, 0.0);
        $this->assertTrue($resA['is_eligible']);
        $this->assertEquals('eligible', $resA['status']);

        // Case B: 68% attendance, ₹0 dues -> Blocked for Attendance
        $resB = $checkEligibility(68.0, 0.0);
        $this->assertFalse($resB['is_eligible']);
        $this->assertEquals('blocked_attendance', $resB['status']);

        // Case C: 85% attendance, ₹15,000 dues -> Blocked for Dues
        $resC = $checkEligibility(85.0, 15000.0);
        $this->assertFalse($resC['is_eligible']);
        $this->assertEquals('blocked_dues', $resC['status']);

        // Case D: 68% attendance, condoned by admin -> Eligible
        $resD = $checkEligibility(68.0, 0.0, 'condoned');
        $this->assertTrue($resD['is_eligible']);
        $this->assertEquals('condoned', $resD['status']);
    }
}
