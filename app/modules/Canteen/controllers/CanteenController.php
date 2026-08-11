<?php

declare(strict_types=1);

namespace App\Modules\Canteen\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Canteen\services\CanteenService;

class CanteenController extends Controller
{
    private CanteenService $canteenService;

    public function __construct()
    {
        $this->canteenService = new CanteenService();
    }

    public function index(): void
    {
        $canManage = Permission::has('canteen.manage');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $action = $this->input('_action', 'add_item');

                if ($action === 'place_order' || $action === 'place_cart_order') {
                    $userId = auth_id();
                    if (!$userId) {
                        $error = 'You must be logged in to place an order.';
                    } else {
                        // Check if cart JSON or single item
                        $cartJson = $this->input('cart_json');
                        $cartItems = [];

                        if (!empty($cartJson)) {
                            $decoded = json_decode($cartJson, true);
                            if (is_array($decoded)) {
                                $cartItems = $decoded;
                            }
                        } else {
                            $cartItems[] = [
                                'item_id'  => (int) $this->input('item_id'),
                                'quantity' => (int) $this->input('quantity', '1'),
                            ];
                        }

                        $res = $this->canteenService->placeCartOrder(
                            $cartItems,
                            (int) $userId,
                            $_SESSION['linked_id'] ?? null,
                            $this->input('payment_method', 'pay_at_counter'),
                            $this->input('notes')
                        );

                        if ($res['success']) {
                            $success = $res['message'];
                        } else {
                            $error = $res['message'];
                        }
                    }
                } elseif ($action === 'update_order_status') {
                    Permission::enforce('canteen.manage');
                    $orderId   = (int) $this->input('order_id');
                    $status    = $this->input('order_status');
                    $payStatus = $this->input('payment_status'); // optional

                    if ($this->canteenService->updateOrderStatus($orderId, $status, $payStatus)) {
                        $success = 'Order status updated successfully.';
                    } else {
                        $error = 'Failed to update order status.';
                    }
                } else {
                    Permission::enforce('canteen.manage');
                    $data = [
                        'college_id'      => 1,
                        'item_name'       => $this->input('item_name'),
                        'category'        => $this->input('category', 'Snacks'),
                        'price'           => (float) $this->input('price', '0.00'),
                        'stock_quantity'  => (int) $this->input('stock_quantity', '50'),
                        'stock_status'    => $this->input('stock_status', 'available'),
                    ];

                    if (empty($data['item_name'])) {
                        $error = 'Food item name is required.';
                    } else {
                        if ($this->canteenService->createCanteenItem($data)) {
                            $success = 'Canteen food item added successfully.';
                        } else {
                            $error = 'Failed to add food item.';
                        }
                    }
                }
            }
        }

        $items     = $this->canteenService->getCanteenItems(1);
        $userId    = auth_id();
        $myOrders  = $userId ? $this->canteenService->getUserOrders((int)$userId) : [];
        $allOrders = $canManage ? $this->canteenService->getAllOrders(1) : [];

        $this->render('Canteen/views/index', [
            'title'     => 'Canteen & Mess Menu',
            'items'     => $items,
            'myOrders'  => $myOrders,
            'allOrders' => $allOrders,
            'canManage' => $canManage,
            'error'     => $error,
            'success'   => $success,
        ], 'layout');
    }
}
