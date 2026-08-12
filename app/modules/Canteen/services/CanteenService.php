<?php

declare(strict_types=1);

namespace App\Modules\Canteen\services;

use App\Modules\Settings\services\NotificationService;
use Exception;
use PDO;

class CanteenService
{
    private NotificationService $notifSvc;

    public function __construct()
    {
        $this->notifSvc = new NotificationService();
    }

    public function getCanteenItems(int $collegeId = 1): array
    {
        $stmt = db()->prepare('SELECT * FROM canteen_items WHERE college_id = :college_id ORDER BY stock_status ASC, category ASC, item_name ASC');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function createCanteenItem(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO canteen_items (college_id, item_name, category, price, stock_quantity, stock_status)
            VALUES (:college_id, :item_name, :category, :price, :stock_quantity, :stock_status)
        ');
        $qty = max(0, (int) ($data['stock_quantity'] ?? 50));
        return $stmt->execute([
            ':college_id'      => $data['college_id'] ?? 1,
            ':item_name'       => $data['item_name'],
            ':category'        => $data['category'] ?? 'Snacks',
            ':price'           => (float) ($data['price'] ?? 0.00),
            ':stock_quantity'  => $qty,
            ':stock_status'    => $qty > 0 ? ($data['stock_status'] ?? 'available') : 'out_of_stock',
        ]);
    }

    /**
     * Place multi-item cart order with atomic inventory decrement.
     */
    public function placeCartOrder(array $cartItems, int $userId, ?int $studentId, string $payMethod = 'pay_at_counter', ?string $notes = null): array
    {
        if (empty($cartItems)) {
            return ['success' => false, 'message' => 'Cart is empty. Please add food items to order.'];
        }

        db()->beginTransaction();

        try {
            $totalAmount = 0.0;
            $firstItemName = '';
            $firstItemId = 0;
            $totalQty = 0;
            $validatedItems = [];

            // 1. Verify and lock items
            $itemStmt = db()->prepare('SELECT * FROM canteen_items WHERE id = :id FOR UPDATE');
            $decStmt  = db()->prepare('
                UPDATE canteen_items 
                SET stock_quantity = GREATEST(0, stock_quantity - :qty),
                    stock_status = IF(stock_quantity <= 0, "out_of_stock", stock_status)
                WHERE id = :id
            ');

            foreach ($cartItems as $c) {
                $itemId = (int) ($c['item_id'] ?? 0);
                $qty    = max(1, (int) ($c['quantity'] ?? 1));

                $itemStmt->execute([':id' => $itemId]);
                $item = $itemStmt->fetch();

                if (!$item || $item['stock_status'] === 'out_of_stock' || (int)$item['stock_quantity'] < $qty) {
                    db()->rollBack();
                    $itemName = $item['item_name'] ?? 'Selected item';
                    return ['success' => false, 'message' => "Insufficient stock for {$itemName} (Available: " . ($item['stock_quantity'] ?? 0) . ")"];
                }

                $uPrice = (float) $item['price'];
                $subtotal = $uPrice * $qty;
                $totalAmount += $subtotal;
                $totalQty += $qty;

                if (empty($firstItemName)) {
                    $firstItemName = $item['item_name'];
                    $firstItemId = $itemId;
                }

                // Decrement inventory
                $decStmt->execute([':qty' => $qty, ':id' => $itemId]);

                $validatedItems[] = [
                    'item_id'    => $itemId,
                    'item_name'  => $item['item_name'],
                    'quantity'   => $qty,
                    'unit_price' => $uPrice,
                    'subtotal'   => $subtotal,
                ];
            }

            // Summary description
            $summaryItemName = count($validatedItems) > 1 
                ? "{$firstItemName} + " . (count($validatedItems) - 1) . " more items" 
                : $firstItemName;

            $orderNum = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

            // 2. Insert Order Header
            $ordStmt = db()->prepare('
                INSERT INTO canteen_orders (
                    order_number, college_id, user_id, student_id, item_id, item_name,
                    quantity, unit_price, total_price, payment_method, payment_status, order_status, notes, created_at
                ) VALUES (
                    :order_number, 1, :user_id, :student_id, :item_id, :item_name,
                    :quantity, :unit_price, :total_price, :payment_method, "pending", "placed", :notes, NOW()
                )
            ');

            $ordStmt->execute([
                ':order_number'   => $orderNum,
                ':user_id'        => $userId,
                ':student_id'     => $studentId,
                ':item_id'        => $firstItemId,
                ':item_name'      => $summaryItemName,
                ':quantity'       => $totalQty,
                ':unit_price'     => $validatedItems[0]['unit_price'],
                ':total_price'    => $totalAmount,
                ':payment_method' => $payMethod,
                ':notes'          => $notes,
            ]);

            $orderId = (int) db()->lastInsertId();

            // 3. Insert Line Items
            $lineStmt = db()->prepare('
                INSERT INTO canteen_order_items (order_id, item_id, item_name, quantity, unit_price, subtotal)
                VALUES (:order_id, :item_id, :item_name, :quantity, :unit_price, :subtotal)
            ');

            foreach ($validatedItems as $vi) {
                $lineStmt->execute([
                    ':order_id'   => $orderId,
                    ':item_id'    => $vi['item_id'],
                    ':item_name'  => $vi['item_name'],
                    ':quantity'   => $vi['quantity'],
                    ':unit_price' => $vi['unit_price'],
                    ':subtotal'   => $vi['subtotal'],
                ]);
            }

            db()->commit();

            return [
                'success'      => true,
                'message'      => "Order placed successfully! Food Token: {$orderNum}",
                'order_id'     => $orderId,
                'order_number' => $orderNum,
                'total_price'  => $totalAmount,
            ];
        } catch (Exception $e) {
            db()->rollBack();
            return ['success' => false, 'message' => 'Order failed: ' . $e->getMessage()];
        }
    }

    /**
     * Get user order history with line items.
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
        $orders = $stmt->fetchAll() ?: [];

        $lineStmt = db()->prepare('SELECT * FROM canteen_order_items WHERE order_id = :oid');
        foreach ($orders as &$ord) {
            $lineStmt->execute([':oid' => (int)$ord['id']]);
            $ord['items'] = $lineStmt->fetchAll() ?: [];
        }

        return $orders;
    }

    /**
     * Get all canteen orders for Manager view.
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
        $orders = $stmt->fetchAll() ?: [];

        $lineStmt = db()->prepare('SELECT * FROM canteen_order_items WHERE order_id = :oid');
        foreach ($orders as &$ord) {
            $lineStmt->execute([':oid' => (int)$ord['id']]);
            $ord['items'] = $lineStmt->fetchAll() ?: [];
        }

        return $orders;
    }

    /**
     * Update order status with notification alerts.
     */
    public function updateOrderStatus(int $orderId, string $status, ?string $paymentStatus = null): bool
    {
        $validStatuses = ['placed', 'preparing', 'ready', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses, true)) {
            return false;
        }

        $ordStmt = db()->prepare('SELECT * FROM canteen_orders WHERE id = :id LIMIT 1');
        $ordStmt->execute([':id' => $orderId]);
        $order = $ordStmt->fetch();

        if (!$order) {
            return false;
        }

        $sql = 'UPDATE canteen_orders SET order_status = :status';
        $params = [':status' => $status, ':id' => $orderId];

        if ($paymentStatus !== null && in_array($paymentStatus, ['pending', 'paid', 'failed'], true)) {
            $sql .= ', payment_status = :pay_status';
            $params[':pay_status'] = $paymentStatus;
        }

        $sql .= ' WHERE id = :id';

        $stmt = db()->prepare($sql);
        $ok = $stmt->execute($params);

        if ($ok && $status === 'ready') {
            $this->notifSvc->notify(
                (int) $order['user_id'],
                "Food Order Ready! (Token: {$order['order_number']})",
                "Your canteen order for '{$order['item_name']}' is prepared and ready for pickup at the counter.",
                '/canteen',
                'success',
                'high'
            );
        }

        return $ok;
    }
}
