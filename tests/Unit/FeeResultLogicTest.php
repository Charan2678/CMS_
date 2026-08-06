<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FeeResultLogicTest extends TestCase
{
    public function testFeeBalanceCalculation(): void
    {
        $totalFee = 50000.00;
        $discount = 5000.00;
        $paid = 20000.00;

        $netFee = $totalFee - $discount;
        $balance = $netFee - $paid;

        $this->assertEquals(45000.00, $netFee);
        $this->assertEquals(25000.00, $balance);
    }

    public function testGradeBoundaryCalculation(): void
    {
        $calculateGrade = function (float $marks): string {
            return match (true) {
                $marks >= 90.0 => 'A+',
                $marks >= 80.0 => 'A',
                $marks >= 70.0 => 'B',
                $marks >= 60.0 => 'C',
                $marks >= 50.0 => 'D',
                default        => 'F',
            };
        };

        $this->assertEquals('A+', $calculateGrade(95.0));
        $this->assertEquals('A', $calculateGrade(80.0));
        $this->assertEquals('C', $calculateGrade(65.0));
        $this->assertEquals('F', $calculateGrade(49.9));
    }
}
