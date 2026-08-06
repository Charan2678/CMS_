<?php

declare(strict_types=1);

namespace App\Modules\Library\services;

use PDO;

class LibraryService
{
    public function getBooks(int $collegeId = 1): array
    {
        $stmt = db()->prepare('SELECT * FROM books WHERE college_id = :college_id ORDER BY id DESC');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function createBook(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO books (college_id, title, isbn, author, publisher, category, total_copies, available_copies, status)
            VALUES (:college_id, :title, :isbn, :author, :publisher, :category, :copies, :copies, 1)
        ');
        return $stmt->execute([
            ':college_id' => $data['college_id'] ?? 1,
            ':title'      => $data['title'],
            ':isbn'       => $data['isbn'] ?? null,
            ':author'     => $data['author'] ?? 'Unknown',
            ':publisher'  => $data['publisher'] ?? null,
            ':category'   => $data['category'] ?? 'General',
            ':copies'     => (int) ($data['total_copies'] ?? 1),
        ]);
    }

    public function getBookIssues(): array
    {
        $stmt = db()->prepare('
            SELECT bi.*, b.title AS book_title, s.roll_number, s.first_name, s.last_name
            FROM book_issues bi
            JOIN books b ON b.id = bi.book_id
            LEFT JOIN students s ON bi.issued_to_type = "student" AND s.id = bi.issued_to_id
            ORDER BY bi.id DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }
}
