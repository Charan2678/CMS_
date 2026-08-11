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
        $this->libraryService  = new LibraryService();
        $this->studentService = new StudentService();
    }

    public function index(): void
    {
        $canManage = Permission::has('library.manage');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            Permission::enforce('library.manage');
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $action = $this->input('_action', 'create_book');

                if ($action === 'create_book') {
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
                } elseif ($action === 'issue_book') {
                    $data = [
                        'book_id'        => (int) $this->input('book_id'),
                        'issued_to_type' => 'student',
                        'issued_to_id'   => (int) $this->input('student_id'),
                        'due_days'       => (int) $this->input('due_days', '14'),
                    ];

                    if (empty($data['book_id']) || empty($data['issued_to_id'])) {
                        $error = 'Please select both a book and a student.';
                    } else {
                        $res = $this->libraryService->issueBook($data);
                        if ($res['success']) {
                            $success = $res['message'];
                        } else {
                            $error = $res['message'];
                        }
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

        $this->render('Library/views/index', [
            'title'     => 'Library Catalog & Circulation Desk',
            'books'     => $books,
            'issues'    => $issues,
            'students'  => $students,
            'canManage' => $canManage,
            'error'     => $error,
            'success'   => $success,
        ], 'layout');
    }
}
