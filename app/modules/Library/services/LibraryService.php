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

    /**
     * Calculate how many books a student has taken/reserved during a specific calendar month.
     * Note: Returning a book does NOT reduce this count.
     */
    public function getStudentMonthlyCount(int $studentId, ?string $yearMonth = null): int
    {
        if (empty($yearMonth)) {
            $yearMonth = date('Y-m');
        }
        $startDate = $yearMonth . '-01';
        $endDate   = date('Y-m-t', strtotime($startDate));

        $stmt = db()->prepare('
            SELECT COUNT(*) 
            FROM book_issues 
            WHERE issued_to_type = "student" AND issued_to_id = :sid
              AND issued_date >= :start_date AND issued_date <= :end_date
        ');
        $stmt->execute([
            ':sid'        => $studentId,
            ':start_date' => $startDate,
            ':end_date'   => $endDate,
        ]);
        return (int) $stmt->fetchColumn();
    }

    public function issueBook(array $data): array
    {
        $bookId  = (int) $data['book_id'];
        $toType  = $data['issued_to_type'] ?? 'student';
        $toId    = (int) ($data['issued_to_id'] ?? $data['student_id'] ?? 0);
        $dueDays = (int) ($data['due_days'] ?? 14);

        // Enforce 4-book monthly limit for students
        if ($toType === 'student') {
            $takenThisMonth = $this->getStudentMonthlyCount($toId);
            if ($takenThisMonth >= 4) {
                return [
                    'success' => false,
                    'message' => "Cannot issue this book. The student has reached the maximum limit of 4 books for this month. (Books taken: {$takenThisMonth} / 4)"
                ];
            }
        }

        db()->beginTransaction();
        try {
            $bStmt = db()->prepare('SELECT available_copies, title FROM books WHERE id = :id FOR UPDATE');
            $bStmt->execute([':id' => $bookId]);
            $book = $bStmt->fetch();

            if (!$book || (int)$book['available_copies'] < 1) {
                db()->rollBack();
                return ['success' => false, 'message' => 'This book has zero available copies in the library inventory.'];
            }

            $issuedDate = date('Y-m-d');
            $dueDate    = date('Y-m-d', strtotime("+{$dueDays} days"));
            $issuerId   = (int) ($data['issued_by'] ?? auth_id() ?? 1);

            $insStmt = db()->prepare('
                INSERT INTO book_issues (
                    book_id, issued_to_type, issued_to_id, issued_by, issued_date, due_date, status, created_at
                ) VALUES (
                    :book_id, :to_type, :to_id, :issued_by, :issued_date, :due_date, "issued", NOW()
                )
            ');
            $insStmt->execute([
                ':book_id'     => $bookId,
                ':to_type'     => $toType,
                ':to_id'       => $toId,
                ':issued_by'   => $issuerId,
                ':issued_date' => $issuedDate,
                ':due_date'    => $dueDate,
            ]);

            $decStmt = db()->prepare('UPDATE books SET available_copies = GREATEST(0, available_copies - 1) WHERE id = :id');
            $decStmt->execute([':id' => $bookId]);

            db()->commit();
            return ['success' => true, 'message' => "Book '{$book['title']}' issued successfully! Due date: {$dueDate}"];
        } catch (Exception $e) {
            db()->rollBack();
            return ['success' => false, 'message' => 'Failed to issue book: ' . $e->getMessage()];
        }
    }

    /**
     * Reserve / Book a library book by a student with 4-book monthly quota enforcement.
     */
    public function reserveBook(int $bookId, int $studentId): array
    {
        // Enforce 4-book monthly limit
        $takenThisMonth = $this->getStudentMonthlyCount($studentId);
        if ($takenThisMonth >= 4) {
            return [
                'success' => false,
                'message' => "Monthly Book Limit Reached! You can take a maximum of 4 books per month. (Books taken this month: {$takenThisMonth} / 4)"
            ];
        }

        db()->beginTransaction();
        try {
            $bStmt = db()->prepare('SELECT available_copies, title FROM books WHERE id = :id FOR UPDATE');
            $bStmt->execute([':id' => $bookId]);
            $book = $bStmt->fetch();

            if (!$book || (int)$book['available_copies'] < 1) {
                db()->rollBack();
                return ['success' => false, 'message' => 'Sorry, this book has no available copies for reservation currently.'];
            }

            // Check existing active reservation
            $chkStmt = db()->prepare('
                SELECT id FROM book_issues 
                WHERE book_id = :bid AND issued_to_type = "student" AND issued_to_id = :sid AND status IN ("reserved", "issued")
                LIMIT 1
            ');
            $chkStmt->execute([':bid' => $bookId, ':sid' => $studentId]);
            if ($chkStmt->fetch()) {
                db()->rollBack();
                return ['success' => false, 'message' => "You already have an active reservation or issued copy for '{$book['title']}'."];
            }

            $issuedDate = date('Y-m-d');
            $dueDate    = date('Y-m-d', strtotime('+14 days'));
            $issuerId   = auth_id() ?: null;

            $insStmt = db()->prepare('
                INSERT INTO book_issues (
                    book_id, issued_to_type, issued_to_id, issued_by, issued_date, due_date, status, created_at
                ) VALUES (
                    :book_id, "student", :to_id, :issued_by, :issued_date, :due_date, "reserved", NOW()
                )
            ');
            $insStmt->execute([
                ':book_id'     => $bookId,
                ':to_id'       => $studentId,
                ':issued_by'   => $issuerId,
                ':issued_date' => $issuedDate,
                ':due_date'    => $dueDate,
            ]);

            $issueId = (int) db()->lastInsertId();

            // Decrement copies
            $decStmt = db()->prepare('UPDATE books SET available_copies = GREATEST(0, available_copies - 1) WHERE id = :id');
            $decStmt->execute([':id' => $bookId]);

            db()->commit();

            return [
                'success'       => true,
                'message'       => "Book '{$book['title']}' reserved successfully! Please collect from the library desk.",
                'issue_id'      => $issueId,
                'expected_date' => $dueDate,
            ];
        } catch (Exception $e) {
            db()->rollBack();
            return ['success' => false, 'message' => 'Failed to reserve book: ' . $e->getMessage()];
        }
    }

    /**
     * Approve reservation / Issue reserved book by Librarian.
     */
    public function approveReservation(int $issueId): bool
    {
        $stmt = db()->prepare('UPDATE book_issues SET status = "issued", issued_date = CURDATE() WHERE id = :id AND status = "reserved"');
        return $stmt->execute([':id' => $issueId]);
    }

    /**
     * Return book without fine penalty calculation.
     */
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

            $upStmt = db()->prepare('
                UPDATE book_issues
                SET status = "returned", returned_date = NOW()
                WHERE id = :id
            ');
            $upStmt->execute([':id' => $issueId]);

            // Increment available copies
            $incStmt = db()->prepare('UPDATE books SET available_copies = available_copies + 1 WHERE id = :id');
            $incStmt->execute([':id' => $issue['book_id']]);

            db()->commit();

            return ['success' => true, 'message' => "Book returned successfully to library catalogue!"];
        } catch (Exception $e) {
            db()->rollBack();
            return ['success' => false, 'message' => 'Failed to return book: ' . $e->getMessage()];
        }
    }

    public function getBookIssues(): array
    {
        $stmt = db()->prepare('
            SELECT bi.*, b.title AS book_title, b.isbn, b.author, s.roll_number, s.first_name, s.last_name
            FROM book_issues bi
            JOIN books b ON b.id = bi.book_id
            LEFT JOIN students s ON bi.issued_to_type = "student" AND s.id = bi.issued_to_id
            ORDER BY bi.status ASC, bi.id DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Get Student Issued & Reserved Books (No fine details).
     */
    public function getStudentIssuedBooks(int $studentId): array
    {
        $stmt = db()->prepare('
            SELECT bi.*, b.title AS book_title, b.author, b.category, b.isbn
            FROM book_issues bi
            JOIN books b ON b.id = bi.book_id
            WHERE bi.issued_to_type = "student" AND bi.issued_to_id = :sid
            ORDER BY bi.id DESC
        ');
        $stmt->execute([':sid' => $studentId]);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Get Student Month-wise Library History.
     */
    public function getStudentMonthlyHistory(int $studentId, ?string $yearMonth = null): array
    {
        if (empty($yearMonth)) {
            $yearMonth = date('Y-m');
        }

        $startDate = $yearMonth . '-01';
        $endDate   = date('Y-m-t', strtotime($startDate));

        $stmt = db()->prepare('
            SELECT bi.*, b.title AS book_title, b.author, b.category, b.isbn
            FROM book_issues bi
            JOIN books b ON b.id = bi.book_id
            WHERE bi.issued_to_type = "student" AND bi.issued_to_id = :sid
              AND bi.issued_date >= :start_date AND bi.issued_date <= :end_date
            ORDER BY bi.issued_date DESC, bi.id DESC
        ');
        $stmt->execute([
            ':sid'        => $studentId,
            ':start_date' => $startDate,
            ':end_date'   => $endDate,
        ]);
        $history = $stmt->fetchAll() ?: [];

        // Build list of distinct available months for dropdown
        $mStmt = db()->prepare('
            SELECT DISTINCT DATE_FORMAT(issued_date, "%Y-%m") AS month_key,
                   DATE_FORMAT(issued_date, "%M %Y") AS month_name,
                   COUNT(*) AS total_books
            FROM book_issues
            WHERE issued_to_type = "student" AND issued_to_id = :sid
            GROUP BY month_key, month_name
            ORDER BY month_key DESC
        ');
        $mStmt->execute([':sid' => $studentId]);
        $availableMonths = $mStmt->fetchAll() ?: [];

        if (empty($availableMonths)) {
            $availableMonths = [
                ['month_key' => date('Y-m'), 'month_name' => date('F Y'), 'total_books' => count($history)]
            ];
        }

        $takenThisMonth = count($history);

        return [
            'selected_month'   => $yearMonth,
            'history'          => $history,
            'available_months' => $availableMonths,
            'monthly_taken'    => $takenThisMonth,
            'monthly_limit'    => 4,
            'monthly_remaining'=> max(0, 4 - $takenThisMonth),
        ];
    }

    /**
     * Get Student Library Summary with 4-Book Monthly Quota.
     */
    public function getStudentBookSummary(int $studentId): array
    {
        try {
            $stmt = db()->prepare('
                SELECT 
                    SUM(CASE WHEN status = "issued" THEN 1 ELSE 0 END) AS books_issued,
                    SUM(CASE WHEN status = "reserved" THEN 1 ELSE 0 END) AS books_reserved,
                    SUM(CASE WHEN status = "issued" AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY) THEN 1 ELSE 0 END) AS due_soon,
                    SUM(CASE WHEN status = "overdue" OR (status = "issued" AND due_date < CURDATE()) THEN 1 ELSE 0 END) AS overdue_books
                FROM book_issues
                WHERE issued_to_type = "student" AND issued_to_id = :sid
            ');
            $stmt->execute([':sid' => $studentId]);
            $row = $stmt->fetch() ?: [];

            $monthlyTaken = $this->getStudentMonthlyCount($studentId);

            return [
                'books_issued'      => (int)($row['books_issued'] ?? 0),
                'books_reserved'    => (int)($row['books_reserved'] ?? 0),
                'due_soon'          => (int)($row['due_soon'] ?? 0),
                'overdue_books'     => (int)($row['overdue_books'] ?? 0),
                'monthly_taken'     => $monthlyTaken,
                'monthly_limit'     => 4,
                'monthly_remaining' => max(0, 4 - $monthlyTaken),
            ];
        } catch (\Throwable $e) {
            return [
                'books_issued'      => 0,
                'books_reserved'    => 0,
                'due_soon'          => 0,
                'overdue_books'     => 0,
                'monthly_taken'     => 0,
                'monthly_limit'     => 4,
                'monthly_remaining' => 4,
            ];
        }
    }

    public function getStatistics(int $collegeId = 1): array
    {
        try {
            $stmt = db()->query('SELECT COALESCE(SUM(total_copies), 0) as total_copies, COALESCE(SUM(available_copies), 0) as available_copies FROM books');
            $bookTotals = $stmt->fetch() ?: ['total_copies' => 0, 'available_copies' => 0];

            $stmt = db()->query("SELECT COUNT(*) FROM book_issues WHERE status IN ('issued', 'overdue')");
            $issuedCount = (int) $stmt->fetchColumn();

            $stmt = db()->query("SELECT COUNT(*) FROM book_issues WHERE status = 'overdue' OR (status = 'issued' AND due_date < CURDATE())");
            $overdueCount = (int) $stmt->fetchColumn();

            $stmt = db()->query("SELECT (SELECT COUNT(*) FROM students) + (SELECT COUNT(*) FROM faculty) AS members");
            $registeredMembers = (int) $stmt->fetchColumn();

            $stmt = db()->query("SELECT COUNT(*) FROM book_issues WHERE status = 'issued' AND due_date = CURDATE()");
            $dueTodayCount = (int) $stmt->fetchColumn();

            return [
                'total_books'        => (int) $bookTotals['total_copies'],
                'available_books'    => (int) $bookTotals['available_copies'],
                'issued_books'       => $issuedCount,
                'overdue_books'      => $overdueCount,
                'registered_members' => $registeredMembers,
                'due_today'          => $dueTodayCount,
            ];
        } catch (\Throwable $e) {
            return [
                'total_books'        => 0,
                'available_books'    => 0,
                'issued_books'       => 0,
                'overdue_books'      => 0,
                'registered_members' => 0,
                'due_today'          => 0,
            ];
        }
    }

    public function getOverdueBooks(): array
    {
        $stmt = db()->prepare('
            SELECT bi.*, b.title AS book_title, b.author, s.roll_number, s.first_name, s.last_name, s.mobile, s.email,
                   DATEDIFF(CURDATE(), bi.due_date) AS days_overdue
            FROM book_issues bi
            JOIN books b ON b.id = bi.book_id
            JOIN students s ON bi.issued_to_type = "student" AND s.id = bi.issued_to_id
            WHERE bi.status = "overdue" OR (bi.status = "issued" AND bi.due_date < CURDATE())
            ORDER BY bi.due_date ASC
        ');
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    public function getPopularBooks(): array
    {
        $stmt = db()->prepare('
            SELECT b.*, COUNT(bi.id) AS borrow_count
            FROM books b
            LEFT JOIN book_issues bi ON bi.book_id = b.id
            GROUP BY b.id
            ORDER BY borrow_count DESC, b.id ASC
            LIMIT 5
        ');
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    public function getDepartmentUsage(): array
    {
        try {
            $stmt = db()->query('
                SELECT d.code, d.name,
                       COUNT(DISTINCT s.id) AS active_members,
                       COUNT(DISTINCT bi.id) AS books_issued
                FROM departments d
                LEFT JOIN courses c ON c.department_id = d.id
                LEFT JOIN student_academics sa ON sa.department_id = d.id
                LEFT JOIN students s ON s.id = sa.student_id
                LEFT JOIN book_issues bi ON bi.issued_to_type = "student" AND bi.issued_to_id = s.id AND bi.status = "issued"
                GROUP BY d.id
                ORDER BY d.id ASC
            ');
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getRecentActivity(int $limit = 5): array
    {
        try {
            $stmt = db()->prepare('
                SELECT bi.*, b.title AS book_title,
                       COALESCE(CONCAT(s.first_name, " ", s.last_name, " (", s.roll_number, ")"), CONCAT(f.first_name, " ", f.last_name, " (Faculty)")) AS member_name
                FROM book_issues bi
                JOIN books b ON b.id = bi.book_id
                LEFT JOIN students s ON bi.issued_to_type = "student" AND s.id = bi.issued_to_id
                LEFT JOIN faculty f ON bi.issued_to_type = "faculty" AND f.id = bi.issued_to_id
                ORDER BY bi.id DESC
                LIMIT :lim
            ');
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getLibraryAnnouncements(int $limit = 3): array
    {
        try {
            $stmt = db()->prepare('
                SELECT * FROM announcements
                WHERE (target_role = "all" OR target_role = "student" OR target_role = "faculty")
                  AND (end_date IS NULL OR end_date >= CURDATE())
                ORDER BY id DESC
                LIMIT :lim
            ');
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
