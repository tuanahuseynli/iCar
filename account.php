<?php
require_once "storage/item-storage.php";
require_once "storage/auth.php";
require_once "storage/booking-storage.php";
$auth = new Auth();

$carRepository = new CarRepository();
$email = $_SESSION["user"];
$users = new UserRepository();
$user = $users->findOne(['email' => $email]);

$orderRepository = new OrderRepository();
$profileOrders = $orderRepository->getCarIdsByEmail($email);
if ($user['admin'] == true){
    $profileOrders = $orderRepository->All();
}
//$carsf = $carRepository->findByIds($profileOrders);

$itemsPerRow = 5;
$totalItems = count($profileOrders);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iCar</title>
    <link rel="stylesheet" href="./media/style3.css">
</head>
<header>
    <div id="top-header">
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
    <h2>Logged in as: <?=$user['fullname']?></h2>

    <?php
echo "<div class='container'>";
for ($i = 0; $i < $totalItems; $i += $itemsPerRow) {
    $row = array_slice($profileOrders, $i, $itemsPerRow);
    echo "<div class='row'>";
    foreach ($row as $item) {
        $car = $carRepository->findById((string)$item->carid); // Fetch car details by ID
        if ($car) {
            echo "<div class='box' style='background-image: url(\"" . htmlspecialchars($car->image) . "\");'> 
                <p>" . htmlspecialchars($item->email ) . "<br>
                " . htmlspecialchars($item->datestart) . " - " . htmlspecialchars($item->dateend) . "</p>
                    <p>" . htmlspecialchars($car->brand) . " " . htmlspecialchars($car->model) . "</p>
                    <p>Passengers: " . htmlspecialchars($car->passengers) . "</p>
                    <p>Price: " . htmlspecialchars($car->daily_price_usd) . " HUF/day</p>"
            ;
            if ($user['admin'] == true) {
                $id = (string)$item->id;
                echo "<form method='GET' action='delete-booking.php' class='delete-form'>
                        <input type='hidden' name='order_id' value='" . htmlspecialchars($id) . "'>
                        <button type='submit' class='delete-button'>Delete</button>
                      </form>";
            } "
                  
                </div>";
        }
    }
    $remainingSlots = $itemsPerRow - count($row);
    for ($j = 0; $j < $remainingSlots; $j++) {
        echo "<div class='box hidden'></div>";
    }
    echo "</div>";
}
echo "</div>";
?>

</body>

</html>
