<?php

declare(strict_types=1);

namespace App\Modules\Library\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Library\services\LibraryService;
use App\Modules\Student\services\StudentService;

class LibraryController extends Controller
{
    private LibraryService $libraryService;
    private StudentService $studentService;

    public function __construct()
    {
        $this->libraryService = new LibraryService();
        $this->studentService = new StudentService();
    }

    /**
     * High-Level Overview Dashboard ONLY (For Librarian).
     */
    public function index(): void
    {
        $canManage = Permission::has('library.manage');
        $role = auth_role();

        if (in_array($role, ['student', 'parent'], true)) {
            $this->redirect('/library/catalog');
            return;
        }

        $stats          = $this->libraryService->getStatistics(1);
        $overdueList    = $this->libraryService->getOverdueBooks();
        $popularBooks   = $this->libraryService->getPopularBooks();
        $deptUsage      = $this->libraryService->getDepartmentUsage();
        $recentActivity = $this->libraryService->getRecentActivity(5);
        $announcements  = $this->libraryService->getLibraryAnnouncements(3);

        $this->render('Library/views/index', [
            'title'          => 'Library Dashboard — Kuppam Engineering College',
            'stats'          => $stats,
            'overdueList'    => $overdueList,
            'popularBooks'   => $popularBooks,
            'deptUsage'      => $deptUsage,
            'recentActivity' => $recentActivity,
            'announcements'  => $announcements,
            'canManage'      => $canManage,
        ], 'layout');
    }

    /**
     * Complete Library Catalog Page (Supports both Librarian & Student Reservations).
     */
    public function catalog(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $canManage = Permission::has('library.manage');
        $role      = auth_role();

        $error   = null;
        $success = null;
        $reservationInfo = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $action = $this->input('_action', 'add_book');

                if ($action === 'reserve_book') {
                    $bookId    = (int) $this->input('book_id');
                    $studentId = (int) ($_SESSION['linked_id'] ?? 1);
                    $res       = $this->libraryService->reserveBook($bookId, $studentId);

                    if ($res['success']) {
                        $success = $res['message'];
                        $reservationInfo = $res;
                    } else {
                        $error = $res['message'];
                    }
                } else {
                    Permission::enforce('library.manage');
                    $data = [
                        'college_id'   => 1,
                        'title'        => $this->input('title'),
                        'isbn'         => $this->input('isbn'),
                        'author'       => $this->input('author'),
                        'publisher'    => $this->input('publisher'),
                        'category'     => $this->input('category'),
                        'total_copies' => (int) $this->input('total_copies', '1'),
                    ];

                    if (empty($data['title'])) {
                        $error = 'Book title is required.';
                    } else {
                        if ($this->libraryService->createBook($data)) {
                            $success = 'Book added to library catalog successfully.';
                        } else {
                            $error = 'Failed to add book.';
                        }
                    }
                }
            }
        }

        $search    = query('search', '');
        $books     = $this->libraryService->getBooks(1);
        $studentId = (int) ($_SESSION['linked_id'] ?? 1);
        $summary   = $this->libraryService->getStudentBookSummary($studentId);

        if (!empty($search)) {
            $searchLower = strtolower($search);
            $books = array_filter($books, function($b) use ($searchLower) {
                return str_contains(strtolower($b['title']), $searchLower)
                    || str_contains(strtolower($b['author'] ?? ''), $searchLower)
                    || str_contains(strtolower($b['category'] ?? ''), $searchLower);
            });
        }

        $this->render('Library/views/catalog', [
            'title'           => 'Library Catalog & Books Repository',
            'books'           => $books,
            'canManage'       => $canManage,
            'search'          => $search,
            'summary'         => $summary,
            'error'           => $error,
            'success'         => $success,
            'reservationInfo' => $reservationInfo,
        ], 'layout');
    }

    /**
     * Student — Reserve a specific book.
     */
    public function reserveBook(string $id): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $bookId    = (int) $id;
        $studentId = (int) ($_SESSION['linked_id'] ?? 1);
        $res       = $this->libraryService->reserveBook($bookId, $studentId);

        if ($res['success']) {
            flash('success', $res['message']);
        } else {
            flash('error', $res['message']);
        }

        $this->redirect('/library/catalog');
    }

    /**
     * Student — My Issued & Reserved Books.
     */
    public function myBooks(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $studentId   = (int) ($_SESSION['linked_id'] ?? 1);
        $issuedBooks = $this->libraryService->getStudentIssuedBooks($studentId);
        $summary     = $this->libraryService->getStudentBookSummary($studentId);

        $this->render('Library/views/my_books', [
            'title'       => 'My Issued & Reserved Books',
            'issuedBooks' => $issuedBooks,
            'summary'     => $summary,
            'success'     => flash_get('success'),
            'error'       => flash_get('error'),
        ], 'layout');
    }

    /**
     * Student — Monthly Book History.
     */
    public function history(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $studentId     = (int) ($_SESSION['linked_id'] ?? 1);
        $selectedMonth = query('month', date('Y-m'));

        $monthlyData = $this->libraryService->getStudentMonthlyHistory($studentId, $selectedMonth);
        $summary     = $this->libraryService->getStudentBookSummary($studentId);

        $this->render('Library/views/history', [
            'title'       => 'Monthly Book History & Quota Log',
            'monthlyData' => $monthlyData,
            'summary'     => $summary,
            'success'     => flash_get('success'),
            'error'       => flash_get('error'),
        ], 'layout');
    }

    /**
     * Dedicated Issue & Return Desk Page (Librarian).
     */
    public function issue(): void
    {
        Permission::enforce('library.manage');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $action = $this->input('_action', 'issue_book');
                if ($action === 'issue_book') {
                    $data = [
                        'book_id'        => (int) $this->input('book_id'),
                        'issued_to_type' => 'student',
                        'issued_to_id'   => (int) $this->input('student_id'),
                        'due_days'       => (int) $this->input('due_days', '14'),
                    ];
                    $res = $this->libraryService->issueBook($data);
                    if ($res['success']) {
                        $success = $res['message'];
                    } else {
                        $error = $res['message'];
                    }
                } elseif ($action === 'approve_reservation') {
                    $issueId = (int) $this->input('issue_id');
                    if ($this->libraryService->approveReservation($issueId)) {
                        $success = 'Student book reservation approved and issued successfully!';
                    } else {
                        $error = 'Failed to approve reservation.';
                    }
                } elseif ($action === 'return_book') {
                    $issueId = (int) $this->input('issue_id');
                    $res = $this->libraryService->returnBook($issueId);
                    if ($res['success']) {
                        $success = $res['message'];
                    } else {
                        $error = $res['message'];
                    }
                }
            }
        }

        $books    = $this->libraryService->getBooks(1);
        $issues   = $this->libraryService->getBookIssues();
        $students = $this->studentService->getStudents(1, 1, 100)['data'] ?? [];

        $this->render('Library/views/issue', [
            'title'    => 'Book Issue & Return Operations Desk',
            'books'    => $books,
            'issues'   => $issues,
            'students' => $students,
            'error'    => $error,
            'success'  => $success,
        ], 'layout');
    }

    /**
     * Book Reservations Page (Librarian).
     */
    public function reservations(): void
    {
        Permission::enforce('library.manage');
        $issues = $this->libraryService->getBookIssues();
        $reservations = array_filter($issues, fn($i) => $i['status'] === 'reserved');

        $this->render('Library/views/reservations', [
            'title'        => 'Book Reservations & Hold Requests',
            'reservations' => $reservations,
        ], 'layout');
    }

    /**
     * Library Members Directory Page (Librarian).
     */
    public function members(): void
    {
        Permission::enforce('library.manage');
        $students = $this->studentService->getStudents(1, 1, 100)['data'] ?? [];

        $this->render('Library/views/members', [
            'title'    => 'Library Members Directory',
            'students' => $students,
        ], 'layout');
    }

    /**
     * Circulation Reports Page.
     */
    public function circulationReport(): void
    {
        Permission::enforce('library.manage');
        $issues = $this->libraryService->getBookIssues();

        $this->render('Library/views/circulation_report', [
            'title'  => 'Circulation Analytics & Transaction Report',
            'issues' => $issues,
        ], 'layout');
    }

    /**
     * Overdue & Penalties Report Page.
     */
    public function overdueReport(): void
    {
        Permission::enforce('library.manage');
        $overdueList = $this->libraryService->getOverdueBooks();

        $this->render('Library/views/overdue_report', [
            'title'       => 'Overdue Books Log',
            'overdueList' => $overdueList,
        ], 'layout');
    }

    /**
     * Inventory Report Page.
     */
    public function inventoryReport(): void
    {
        Permission::enforce('library.manage');
        $stats = $this->libraryService->getStatistics(1);
        $books = $this->libraryService->getBooks(1);

        $this->render('Library/views/inventory_report', [
            'title' => 'Library Inventory & Stock Audit Report',
            'stats' => $stats,
            'books' => $books,
        ], 'layout');
    }

    /**
     * Student Usage & Monthly History Report Page (Librarian View).
     */
    public function studentReport(): void
    {
        Permission::enforce('library.manage');
        $deptUsage = $this->libraryService->getDepartmentUsage();
        $students  = $this->studentService->getStudents(1, 1, 100)['data'] ?? [];

        $selectedStudentId = (int) query('student_id', '1');
        $selectedMonth     = query('month', date('Y-m'));

        $studentMonthlyHistory = $this->libraryService->getStudentMonthlyHistory($selectedStudentId, $selectedMonth);
        $selectedStudentProfile = $this->studentService->getStudentProfile($selectedStudentId);

        $this->render('Library/views/student_report', [
            'title'                  => 'Student Monthly Library History & Usage',
            'deptUsage'              => $deptUsage,
            'students'               => $students,
            'selectedStudentId'      => $selectedStudentId,
            'selectedMonth'          => $selectedMonth,
            'studentMonthlyHistory'  => $studentMonthlyHistory,
            'selectedStudentProfile' => $selectedStudentProfile,
        ], 'layout');
    }
}
