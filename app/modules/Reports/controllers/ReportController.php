<?php

declare(strict_types=1);

namespace App\Modules\Reports\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Master\services\MasterService;
use App\Modules\Reports\services\ReportService;

class ReportController extends Controller
{
    private ReportService $reportService;
    private MasterService $masterService;

    public function __construct()
    {
        $this->reportService = new ReportService();
        $this->masterService = new MasterService();
    }

    /**
     * Academic Enrollment & Results Analytics.
     */
    public function academic(): void
    {
        Permission::enforce('reports.academic');

        $data = $this->reportService->getAcademicSummary(1);

        $this->render('Reports/views/academic', [
            'title'                 => 'Academic Reports & Analytics',
            'departmentEnrollments' => $data['departmentEnrollments'],
            'courseEnrollments'     => $data['courseEnrollments'],
            'resultsDistribution'   => $data['resultsDistribution'],
        ], 'layout');
    }

    /**
     * Financial Revenue & Ledger Reports.
     */
    public function financial(): void
    {
        Permission::enforce('reports.financial');

        $data = $this->reportService->getFinancialSummary(1);

        $this->render('Reports/views/financial', [
            'title'             => 'Financial Revenue & Fee Dues Report',
            'totalBilled'       => $data['totalBilled'],
            'totalCollected'    => $data['totalCollected'],
            'totalPending'      => $data['totalPending'],
            'methodBreakdown'   => $data['methodBreakdown'],
            'categoryBreakdown' => $data['categoryBreakdown'],
        ], 'layout');
    }

    /**
     * Attendance Shortage & Audit Reports.
     */
    public function attendance(): void
    {
        Permission::enforce('reports.attendance');

        $sectionId = (int) query('section_id', '0');
        $shortageList = [];

        if ($sectionId > 0) {
            $shortageList = $this->reportService->getAttendanceShortageReport($sectionId);
        }

        $sections = $this->masterService->getSections();

        $this->render('Reports/views/attendance', [
            'title'        => 'Attendance Audit & Shortage Report',
            'sections'     => $sections,
            'sectionId'    => $sectionId,
            'shortageList' => $shortageList,
        ], 'layout');
    }
}
