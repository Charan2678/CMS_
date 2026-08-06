<?php

declare(strict_types=1);

namespace App\Modules\Library\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Library\services\LibraryService;

class LibraryController extends Controller
{
    private LibraryService $libraryService;

    public function __construct()
    {
        $this->libraryService = new LibraryService();
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

        $books  = $this->libraryService->getBooks(1);
        $issues = $this->libraryService->getBookIssues();

        $this->render('Library/views/index', [
            'title'     => 'Library Catalog & Books',
            'books'     => $books,
            'issues'    => $issues,
            'canManage' => $canManage,
            'error'     => $error,
            'success'   => $success,
        ], 'layout');
    }
}
