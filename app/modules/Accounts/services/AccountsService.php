<?php

declare(strict_types=1);

namespace App\Modules\Accounts\services;

use PDO;

class AccountsService
{
    public function getStaffPayroll(int $collegeId = 1): array
    {
        $stmt = db()->prepare('
            SELECT st.*, u.username
            FROM staff st
            LEFT JOIN users u ON u.linked_id = st.id AND u.linked_type = "staff"
            WHERE st.college_id = :college_id
            ORDER BY st.id DESC
        ');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }
}
