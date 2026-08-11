<?php

declare(strict_types=1);

namespace App\Modules\Library\services;

use Exception;
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
            VALUES (:college_id, :title, :isbn, :author, :publisher, :category, :total_copies, :avail_copies, 1)
        ');
        $copies = max(1, (int) ($data['total_copies'] ?? 1));
        return $stmt->execute([
            ':college_id'    => $data['college_id'] ?? 1,
            ':title'         => $data['title'],
            ':isbn'          => $data['isbn'] ?? null,
            ':author'        => $data['author'] ?? 'Unknown',
            ':publisher'     => $data['publisher'] ?? null,
            ':category'      => $data['category'] ?? 'General',
            ':total_copies'  => $copies,
            ':avail_copies'  => $copies,
        ]);
    }

    public function issueBook(array $data): array
    {
        $bookId = (int) $data['book_id'];
        $toType = $data['issued_to_type'] ?? 'student';
        $toId   = (int) $data['issued_to_id'];
        $dueDays = (int) ($data['due_days'] ?? 14);

        $bStmt = db()->prepare('SELECT available_copies, title FROM books WHERE id = :id FOR UPDATE');
        
        db()->beginTransaction();
        try {
            $bStmt->execute([':id' => $bookId]);
            $book = $bStmt->fetch();

            if (!$book || (int)$book['available_copies'] < 1) {
                db()->rollBack();
                return ['success' => false, 'message' => 'This book has zero available copies in the library inventory.'];
            }

            $issueDate = date('Y-m-d');
            $dueDate   = date('Y-m-d', strtotime("+{$dueDays} days"));

            $insStmt = db()->prepare('
                INSERT INTO book_issues (
                    book_id, issued_to_type, issued_to_id, issue_date, due_date, status, created_at
                ) VALUES (
                    :book_id, :to_type, :to_id, :issue_date, :due_date, "issued", NOW()
                )
            ');
            $insStmt->execute([
                ':book_id'    => $bookId,
                ':to_type'    => $toType,
                ':to_id'      => $toId,
                ':issue_date' => $issueDate,
                ':due_date'   => $dueDate,
            ]);

            // Decrement copies
            $decStmt = db()->prepare('UPDATE books SET available_copies = available_copies - 1 WHERE id = :id');
            $decStmt->execute([':id' => $bookId]);

            db()->commit();
            return ['success' => true, 'message' => "Book '{$book['title']}' issued successfully! Due date: {$dueDate}"];
        } catch (Exception $e) {
            db()->rollBack();
            return ['success' => false, 'message' => 'Failed to issue book: ' . $e->getMessage()];
        }
    }

    public function returnBook(int $issueId): array
    {
        db()->beginTransaction();
        try {
            $stmt = db()->prepare('SELECT * FROM book_issues WHERE id = :id FOR UPDATE');
            $stmt->execute([':id' => $issueId]);
            $issue = $stmt->fetch();

            if (!$issue) {
                db()->rollBack();
                return ['success' => false, 'message' => 'Book issue record not found.'];
            }

            if ($issue['status'] === 'returned') {
                db()->rollBack();
                return ['success' => false, 'message' => 'Book has already been returned.'];
            }

            // Calculate overdue fine (₹5 per day overdue)
            $dueDate = strtotime($issue['due_date']);
            $today   = strtotime(date('Y-m-d'));
            $overdueDays = max(0, (int) floor(($today - $dueDate) / 86400));
            $fineAmount = (float) ($overdueDays * 5.00);

            $upStmt = db()->prepare('
                UPDATE book_issues
                SET status = "returned", returned_date = NOW(), fine_amount = :fine
                WHERE id = :id
            ');
            $upStmt->execute([':fine' => $fineAmount, ':id' => $issueId]);

            // Increment copies
            $incStmt = db()->prepare('UPDATE books SET available_copies = available_copies + 1 WHERE id = :id');
            $incStmt->execute([':id' => $issue['book_id']]);

            db()->commit();

            $fineMsg = $fineAmount > 0 ? " (Overdue fine assessed: ₹" . number_format($fineAmount, 2) . " for {$overdueDays} overdue days)" : "";
            return ['success' => true, 'message' => "Book returned successfully to library catalogue!{$fineMsg}"];
        } catch (Exception $e) {
            db()->rollBack();
            return ['success' => false, 'message' => 'Failed to return book: ' . $e->getMessage()];
        }
    }

    public function getBookIssues(): array
    {
        $stmt = db()->prepare('
            SELECT bi.*, b.title AS book_title, b.isbn, s.roll_number, s.first_name, s.last_name
            FROM book_issues bi
            JOIN books b ON b.id = bi.book_id
            LEFT JOIN students s ON bi.issued_to_type = "student" AND s.id = bi.issued_to_id
            ORDER BY bi.status ASC, bi.id DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }
}
