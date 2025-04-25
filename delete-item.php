<?php
require_once "storage/item-storage.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $car_id = $_GET['car_id'] ?? null;
    if ($car_id) {
        $CRepository = new CarRepository();
        $CRepository->deleteCars(function ($car) use ($car_id) {
            return $car['id'] == $car_id;
        });
    }
}
header('Location: index.php');
exit();
?>