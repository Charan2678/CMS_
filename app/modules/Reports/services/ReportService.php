<?php

declare(strict_types=1);

namespace App\Modules\Reports\services;

use PDO;

class ReportService
{
    /**
     * Get Academic enrollment & result statistics.
     */
    public function getAcademicSummary(int $collegeId = 1): array
    {
        // Enrollment by Department
        $deptStmt = db()->prepare('
            SELECT d.name AS department_name, d.code AS department_code, COUNT(s.id) AS student_count
            FROM departments d
            LEFT JOIN student_academics sa ON sa.department_id = d.id AND sa.is_current = 1
            LEFT JOIN students s ON s.id = sa.student_id AND s.status = "active"
            WHERE d.college_id = :college_id
            GROUP BY d.id
            ORDER BY student_count DESC
        ');
        $deptStmt->execute([':college_id' => $collegeId]);
        $departmentEnrollments = $deptStmt->fetchAll() ?: [];

        // Enrollment by Course
        $courseStmt = db()->prepare('
            SELECT c.name AS course_name, c.code AS course_code, COUNT(s.id) AS student_count
            FROM courses c
            JOIN departments d ON d.id = c.department_id
            LEFT JOIN student_academics sa ON sa.course_id = c.id AND sa.is_current = 1
            LEFT JOIN students s ON s.id = sa.student_id AND s.status = "active"
            WHERE d.college_id = :college_id
            GROUP BY c.id
            ORDER BY student_count DESC
        ');
        $courseStmt->execute([':college_id' => $collegeId]);
        $courseEnrollments = $courseStmt->fetchAll() ?: [];

        // Result Pass/Fail Stats
        $resStmt = db()->prepare('
            SELECT result, COUNT(*) AS count
            FROM results
            GROUP BY result
        ');
        $resStmt->execute();
        $resultsDistribution = $resStmt->fetchAll() ?: [];

        return [
            'departmentEnrollments' => $departmentEnrollments,
            'courseEnrollments'     => $courseEnrollments,
            'resultsDistribution'   => $resultsDistribution,
        ];
    }

    /**
     * Get Financial revenue & pending dues summary.
     */
    public function getFinancialSummary(int $collegeId = 1): array
    {
        // Total Fees Billed & Pending
        $feeStmt = db()->prepare('
            SELECT
                COALESCE(SUM(sf.final_amount), 0.00) AS total_billed,
                COALESCE((SELECT SUM(amount_paid) FROM payments), 0.00) AS total_collected
            FROM student_fees sf
            JOIN students s ON s.id = sf.student_id
            WHERE s.college_id = :college_id
        ');
        $feeStmt->execute([':college_id' => $collegeId]);
        $totals = $feeStmt->fetch() ?: ['total_billed' => 0.00, 'total_collected' => 0.00];

        $totalBilled    = (float) $totals['total_billed'];
        $totalCollected = (float) $totals['total_collected'];
        $totalPending   = max(0.00, $totalBilled - $totalCollected);

        // Revenue by Payment Method
        $pmStmt = db()->prepare('
            SELECT payment_method, SUM(amount_paid) AS total_amount, COUNT(*) AS count
            FROM payments
            GROUP BY payment_method
        ');
        $pmStmt->execute();
        $methodBreakdown = $pmStmt->fetchAll() ?: [];

        // Dues by Category
        $catStmt = db()->prepare('
            SELECT fc.name AS category_name, SUM(sf.final_amount) AS total_amount
            FROM student_fees sf
            JOIN fee_structures fs ON fs.id = sf.fee_structure_id
            JOIN fee_categories fc ON fc.id = fs.fee_category_id
            GROUP BY fc.id
        ');
        $catStmt->execute();
        $categoryBreakdown = $catStmt->fetchAll() ?: [];

        return [
            'totalBilled'       => $totalBilled,
            'totalCollected'    => $totalCollected,
            'totalPending'      => $totalPending,
            'methodBreakdown'   => $methodBreakdown,
            'categoryBreakdown' => $categoryBreakdown,
        ];
    }

    /**
     * Get Attendance shortage (<75%) report for a section.
     */
    public function getAttendanceShortageReport(int $sectionId): array
    {
        $stmt = db()->prepare('
            SELECT s.id, s.roll_number, s.first_name, s.last_name,
                   COUNT(a.id) AS total_classes,
                   SUM(CASE WHEN a.status = "present" THEN 1 ELSE 0 END) AS present_classes,
                   ROUND((SUM(CASE WHEN a.status = "present" THEN 1 ELSE 0 END) / COUNT(a.id)) * 100, 2) AS attendance_pct
            FROM students s
            JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
            JOIN attendance a ON a.student_id = s.id
            WHERE sa.section_id = :sec_id
            GROUP BY s.id
            HAVING attendance_pct < 75.00
            ORDER BY attendance_pct ASC
        ');
        $stmt->execute([':sec_id' => $sectionId]);
        return $stmt->fetchAll() ?: [];
    }
}
