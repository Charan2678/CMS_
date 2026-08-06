<?php

declare(strict_types=1);

namespace App\Modules\Canteen\services;

use PDO;

class CanteenService
{
    public function getCanteenItems(int $collegeId = 1): array
    {
        $stmt = db()->prepare('SELECT * FROM canteen_items WHERE college_id = :college_id ORDER BY id DESC');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function createCanteenItem(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO canteen_items (college_id, item_name, category, price, stock_status)
            VALUES (:college_id, :item_name, :category, :price, :stock_status)
        ');
        return $stmt->execute([
            ':college_id'   => $data['college_id'] ?? 1,
            ':item_name'    => $data['item_name'],
            ':category'     => $data['category'] ?? 'Snacks',
            ':price'        => (float) ($data['price'] ?? 0.00),
            ':stock_status' => $data['stock_status'] ?? 'available',
        ]);
    }

    /**
     * Place a new food order.
     */
    public function placeOrder(array $data): array
    {
        $itemId   = (int) $data['item_id'];
        $quantity = max(1, (int) ($data['quantity'] ?? 1));
        $userId   = (int) $data['user_id'];

        $itemStmt = db()->prepare('SELECT * FROM canteen_items WHERE id = :id AND stock_status = "available" LIMIT 1');
        $itemStmt->execute([':id' => $itemId]);
        $item = $itemStmt->fetch();

        if (!$item) {
            return ['success' => false, 'message' => 'Food item is currently out of stock or unavailable.'];
        }

        $unitPrice  = (float) $item['price'];
        $totalPrice = $unitPrice * $quantity;
        $orderNum   = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

        $stmt = db()->prepare('
            INSERT INTO canteen_orders (
                order_number, college_id, user_id, student_id, item_id, item_name,
                quantity, unit_price, total_price, payment_method, payment_status, order_status, notes, created_at
            ) VALUES (
                :order_number, :college_id, :user_id, :student_id, :item_id, :item_name,
                :quantity, :unit_price, :total_price, :payment_method, :payment_status, "placed", :notes, NOW()
            )
        ');

        $success = $stmt->execute([
            ':order_number'   => $orderNum,
            ':college_id'     => $data['college_id'] ?? 1,
            ':user_id'        => $userId,
            ':student_id'     => $data['student_id'] ?? null,
            ':item_id'        => $itemId,
            ':item_name'      => $item['item_name'],
            ':quantity'       => $quantity,
            ':unit_price'     => $unitPrice,
            ':total_price'    => $totalPrice,
            ':payment_method' => $data['payment_method'] ?? 'pay_at_counter',
            ':payment_status' => ($data['payment_method'] ?? '') === 'online_upi' ? 'paid' : 'pending',
            ':notes'          => $data['notes'] ?? null,
        ]);

        if ($success) {
            return [
                'success'      => true,
                'message'      => "Order placed successfully! Food Token: {$orderNum}",
                'order_number' => $orderNum,
                'total_price'  => $totalPrice
            ];
        }

        return ['success' => false, 'message' => 'Failed to place order.'];
    }

    /**
     * Get orders placed by a specific user.
     */
    public function getUserOrders(int $userId): array
    {
        $stmt = db()->prepare('
            SELECT * FROM canteen_orders
            WHERE user_id = :user_id
            ORDER BY id DESC
            LIMIT 50
        ');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Get all canteen orders for Canteen Manager dashboard.
     */
    public function getAllOrders(int $collegeId = 1): array
    {
        $stmt = db()->prepare('
            SELECT co.*, u.username, u.email
            FROM canteen_orders co
            LEFT JOIN users u ON u.id = co.user_id
            WHERE co.college_id = :college_id
            ORDER BY co.id DESC
            LIMIT 100
        ');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(int $orderId, string $status): bool
    {
        $validStatuses = ['placed', 'preparing', 'ready', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses, true)) {
            return false;
        }

        $paymentUpdate = ($status === 'completed') ? ', payment_status = "paid"' : '';

        $stmt = db()->prepare("
            UPDATE canteen_orders
            SET order_status = :status {$paymentUpdate}
            WHERE id = :id
        ");
        return $stmt->execute([':status' => $status, ':id' => $orderId]);
    }
}
