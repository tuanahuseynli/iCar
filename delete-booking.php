<?php
require_once "storage/booking-storage.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $orderId = $_GET['order_id'] ?? null;
    if ($orderId) {
        $orderRepository = new OrderRepository();
        $orderRepository->deleteOrder(function ($order) use ($orderId) {
            return $order['id'] == $orderId;
        });
    }
}
header('Location: account.php');
exit();
?>