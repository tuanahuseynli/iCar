<?php
require_once "storage/item-storage.php";
require_once "storage/auth.php";
session_start();
$auth = new Auth();
if (isset($_GET['Car'])) {
    $carId = (int)$_GET['Car'];
    $repository = new CarRepository();
    $car = $repository->findById($carId);
}
else {
    $car = null;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./media/style3.css">
</head>
<header>
    <div id="top-header">
        <div id="logo">
            <a href="index.php">CarRental</a>
        </div>
        <nav>
                <?php
                 if (!$auth->is_authenticated()) {
                 ?>
                 <ul>
                 <li>
                    <a href="login.php">Login</a>
                </li>
                <li>
                    <a href="register.php">Register</a>
                </li>
            </ul>
            <?php }
            else{?>
                <ul>
                 <li>
                    <a href="logout.php">Logout</a>
                </li>
                <li>
                    <a href="account.php">Profile</a>
                </li>
            </ul>
            <?php } ?>
        </nav>
    </div>
    <div id="header-image-menu">
    </div>
</header>
<body>
<h2>Successful Booking</h2>
<p>The <?=$car->brand?> <?=$car->model?> has been successfully booked for the interval from <?=$_GET['datef']?> to <?=$_GET['dateu']?>. You can track the status of your booking in your profile.</p>
<a href='account.php'> Back to the vehicle page</a>
</body>
</html>
