<?php
declare(strict_types=1);

/**
 * CMS Cross-Role Data Sync & Integration Test Suite
 *
 * Usage: php database/test_role_sync.php
 */

require_once __DIR__ . '/../app/core/Environment.php';
\App\Core\Environment::load(__DIR__ . '/../.env');

// Autoload PSR-4
require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Database Configuration
$dbConfig = require __DIR__ . '/../app/config/database.php';
\App\Core\Database::configure($dbConfig);

use App\Modules\Leave\services\LeaveService;
use App\Modules\Hostel\services\HostelService;
use App\Modules\Library\services\LibraryService;
use App\Modules\Transport\services\TransportService;
use App\Modules\Canteen\services\CanteenService;
use App\Modules\Attendance\services\AttendanceService;
use App\Modules\Result\services\ResultService;
use App\Modules\Settings\services\NotificationService;

echo "=======================================================\n";
echo "    CMS CROSS-ROLE DATA SYNC INTEGRATION TEST SUITE    \n";
echo "=======================================================\n\n";

$passedCount = 0;
$failedCount = 0;

function assertTest(string $testName, bool $condition, string $details = ''): void {
    global $passedCount, $failedCount;
    if ($condition) {
        echo "  [PASS] $testName" . ($details ? " ($details)" : "") . "\n";
        $passedCount++;
    } else {
        echo "  [FAIL] $testName" . ($details ? " - $details" : "") . "\n";
        $failedCount++;
    }
}

try {
    // -------------------------------------------------------------
    // Test Case 1: Leave & Outpass Cross-Role Sync
    // Roles: Student (User 10, Student ID 1) <-> Warden (User 7) <-> Parent (User 11)
    // -------------------------------------------------------------
    echo "[1/6] Testing Leave & Outpass Cross-Role Sync...\n";
    $leaveSvc = new LeaveService();
    $notifSvc = new NotificationService();

    // Student applies for leave/outpass
    $applyRes = $leaveSvc->applyLeave([
        'applicant_type'       => 'student',
        'applicant_id'         => 1,
        'leave_type'           => 'hostel_outpass',
        'from_date'            => date('Y-m-d'),
        'to_date'              => date('Y-m-d', strtotime('+2 days')),
        'reason'               => 'Role Sync Test Outpass',
        'expected_return_time' => date('Y-m-d 18:00:00', strtotime('+2 days'))
    ], 10);

    assertTest("Student applies for hostel outpass", $applyRes['success'] === true, $applyRes['message'] ?? '');

    // Retrieve leave request in Student portal
    $studentLeaves = $leaveSvc->getMyLeaves('student', 1);
    $latestLeave = $studentLeaves[0] ?? null;
    assertTest("Leave request synced to Student portal with 'pending' status", 
        !empty($latestLeave) && $latestLeave['status'] === 'pending');

    if ($latestLeave) {
        $requestId = (int) $latestLeave['id'];

        // Warden reviews & approves request
        $reviewRes = $leaveSvc->reviewLeave($requestId, 'approved', 'Approved by Warden', 7);
        assertTest("Warden reviews and approves outpass request", $reviewRes['success'] === true, $reviewRes['message'] ?? '');

        // Verify status updated in Student portal
        $updatedLeaves = $leaveSvc->getMyLeaves('student', 1);
        $approvedLeave = null;
        foreach ($updatedLeaves as $l) {
            if ((int)$l['id'] === $requestId) {
                $approvedLeave = $l;
                break;
            }
        }
        assertTest("Outpass status synced to 'approved' in Student portal", 
            !empty($approvedLeave) && $approvedLeave['status'] === 'approved');

        // Check notification delivered to Student (user 10)
        $unreadNotifs = $notifSvc->getUnreadNotifications(10);
        $hasNotif = false;
        foreach ($unreadNotifs['items'] as $item) {
            if (str_contains(strtolower($item['title']), 'outpass') || str_contains(strtolower($item['message']), 'approved')) {
                $hasNotif = true;
                break;
            }
        }
        assertTest("Approval notification automatically synced & delivered to Student", $hasNotif);
    }

    echo "\n";

    // -------------------------------------------------------------
    // Test Case 2: Hostel Booking & Warden Management Sync
    // Roles: Student (User 10, Student ID 1) <-> Warden (User 7)
    // -------------------------------------------------------------
    echo "[2/6] Testing Hostel Booking & Warden Management Sync...\n";
    $hostelSvc = new HostelService();

    // Verify hostel blocks exist
    $blocks = $hostelSvc->getHostelBlocks();
    assertTest("Hostel blocks accessible", !empty($blocks));

    $blockId = !empty($blocks) ? (int)$blocks[0]['id'] : 1;

    // Student creates room booking
    $bookingRes = $hostelSvc->createStudentBooking(1, $blockId, 1, 1, 25000.00);
    assertTest("Student submits hostel room booking", $bookingRes['success'] === true, $bookingRes['message'] ?? '');

    // Warden checks pending requests
    $wardenRequests = $hostelSvc->getWardenBookingRequests();
    $pendingBooking = null;
    foreach ($wardenRequests as $wr) {
        if ((int)$wr['student_id'] === 1 && $wr['booking_status'] === 'payment_pending') {
            $pendingBooking = $wr;
            break;
        }
    }
    assertTest("Hostel booking appears in Warden management requests list", !empty($pendingBooking));

    if (!empty($pendingBooking)) {
        $bookingId = (int)$pendingBooking['id'];

        // Warden approves/confirms booking request
        $wardenAction = $hostelSvc->processWardenBookingAction($bookingId, 'confirm', 'Approved by Warden', 7);
        assertTest("Warden approves hostel room booking", $wardenAction['success'] === true, $wardenAction['message'] ?? '');

        // Verify active booking for Student
        $activeBooking = $hostelSvc->getStudentActiveBooking(1);
        assertTest("Booking status synced to 'confirmed' in Student active booking", 
            !empty($activeBooking) && $activeBooking['booking_status'] === 'confirmed');
    }

    echo "\n";

    // -------------------------------------------------------------
    // Test Case 3: Library Book Circulation & Stock Sync
    // Roles: Student (Student ID 1) <-> Librarian (User 6)
    // -------------------------------------------------------------
    echo "[3/6] Testing Library Book Issue & Student 'My Books' Sync...\n";
    $libSvc = new LibraryService();

    // Librarian issues book ID 1 to Student ID 1
    $issueRes = $libSvc->issueBook([
        'book_id'    => 1,
        'student_id' => 1,
        'due_date'   => date('Y-m-d', strtotime('+14 days'))
    ]);
    assertTest("Librarian issues book to Student", $issueRes['success'] === true, $issueRes['message'] ?? '');

    // Student checks 'My Books'
    $myBooks = $libSvc->getStudentIssuedBooks(1);
    $issuedFound = false;
    $issueId = 0;
    foreach ($myBooks as $mb) {
        if ((int)$mb['book_id'] === 1 && $mb['status'] === 'issued') {
            $issuedFound = true;
            $issueId = (int)$mb['id'];
            break;
        }
    }
    assertTest("Issued book appears immediately in Student's 'My Books' portal", $issuedFound);

    if ($issueId > 0) {
        // Librarian accepts book return
        $returnRes = $libSvc->returnBook($issueId);
        assertTest("Librarian processes book return", $returnRes['success'] === true, $returnRes['message'] ?? '');

        // Verify status updated in Student view
        $myBooksAfter = $libSvc->getStudentIssuedBooks(1);
        $returnedFound = false;
        foreach ($myBooksAfter as $mb) {
            if ((int)$mb['id'] === $issueId && $mb['status'] === 'returned') {
                $returnedFound = true;
                break;
            }
        }
        assertTest("Book status synced to 'returned' in Student view", $returnedFound);
    }

    echo "\n";

    // -------------------------------------------------------------
    // Test Case 4: Canteen Order Queue & Inventory Sync
    // Roles: Student (User 10, Student ID 1) <-> Canteen Staff (User 9)
    // -------------------------------------------------------------
    echo "[4/6] Testing Canteen Order Queue & Status Sync...\n";
    $canteenSvc = new CanteenService();

    // Get menu items
    $items = $canteenSvc->getCanteenItems();
    $itemId = !empty($items) ? (int)$items[0]['id'] : 1;
    $itemPrice = !empty($items) ? (float)$items[0]['price'] : 50.00;

    // Student places cart order
    $orderRes = $canteenSvc->placeCartOrder([
        ['item_id' => $itemId, 'quantity' => 1, 'price' => $itemPrice]
    ], 10, 1, 'pay_at_counter', 'Role Sync Order Test');

    assertTest("Student places canteen order", $orderRes['success'] === true, $orderRes['message'] ?? '');

    if (!empty($orderRes['order_id'])) {
        $orderId = (int)$orderRes['order_id'];

        // Canteen staff checks live orders
        $allOrders = $canteenSvc->getAllOrders();
        $inStaffQueue = false;
        foreach ($allOrders as $o) {
            if ((int)$o['id'] === $orderId) {
                $inStaffQueue = true;
                break;
            }
        }
        assertTest("Order appears instantly in Canteen Staff order list", $inStaffQueue);

        // Canteen staff updates order status to completed
        $updateStatus = $canteenSvc->updateOrderStatus($orderId, 'completed', 'paid');
        assertTest("Canteen Staff updates order to completed/paid", $updateStatus === true);

        // Verify status synced in Student orders
        $userOrders = $canteenSvc->getUserOrders(10);
        $statusSynced = false;
        foreach ($userOrders as $uo) {
            if ((int)$uo['id'] === $orderId && $uo['order_status'] === 'completed') {
                $statusSynced = true;
                break;
            }
        }
        assertTest("Order status synced to 'completed' in Student portal", $statusSynced);
    }

    echo "\n";

    // -------------------------------------------------------------
    // Test Case 5: Transport Registration & Roster Sync
    // Roles: Student (Student ID 1) <-> Transport Staff (User 8)
    // -------------------------------------------------------------
    echo "[5/6] Testing Transport Registration & Roster Sync...\n";
    $transSvc = new TransportService();

    // Student selects route
    $routeRes = $transSvc->selectRoute(1, 1, 'Main Gate Stop');
    assertTest("Student registers for transport route", $routeRes['success'] === true, $routeRes['message'] ?? '');

    // Transport staff checks allocations
    $allocations = $transSvc->getAllocations();
    $inAllocationRoster = false;
    foreach ($allocations as $al) {
        if ((int)$al['student_id'] === 1) {
            $inAllocationRoster = true;
            break;
        }
    }
    assertTest("Transport registration synced to Transport Manager allocation roster", $inAllocationRoster);

    // Check student transport summary
    $transSummary = $transSvc->getStudentTransportSummary(1);
    assertTest("Transport status synced to Student summary profile", !empty($transSummary));

    echo "\n";

    // -------------------------------------------------------------
    // Test Case 6: Academic Attendance & Marks Sync
    // Roles: Faculty (User 3) <-> Student (Student ID 1) & Parent (User 11)
    // -------------------------------------------------------------
    echo "[6/6] Testing Academic Attendance & Marks Cross-Role Sync...\n";
    $attSvc = new AttendanceService();
    $resSvc = new ResultService();

    // Faculty marks bulk attendance for section 1, subject 1
    $today = date('Y-m-d');
    $markAttRes = $attSvc->saveBulkAttendance(1, 1, 1, $today, [
        1 => 'present'
    ]);
    assertTest("Faculty submits section attendance", $markAttRes === true);

    // Verify Student attendance overall summary
    $studentAtt = $attSvc->getStudentOverallSummary(1);
    assertTest("Attendance records immediately synced to Student summary portal", 
        !empty($studentAtt) && isset($studentAtt['total_conducted']));

    // Faculty inputs CIA1 internal marks
    $saveMarks = $resSvc->saveInternalMarks(1, 1, 1, 'cia1', [
        1 => 23.5
    ], 25.0);
    assertTest("Faculty enters CIA1 internal marks for student", $saveMarks === true);

    // Verify Student exam results view
    $studentResults = $resSvc->getStudentFullExamResults(1);
    assertTest("Internal marks synced immediately to Student exam results profile", !empty($studentResults));

    echo "\n=======================================================\n";
    echo sprintf("RESULTS: %d PASSED | %d FAILED\n", $passedCount, $failedCount);
    echo "=======================================================\n";

    exit($failedCount > 0 ? 1 : 0);

} catch (\Throwable $e) {
    echo "\n[CRITICAL ERROR] Test Suite Exception: " . $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
