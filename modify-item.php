<?php
require_once "storage/item-storage.php";
require_once "storage/auth.php";
require_once "storage/booking-storage.php";
$orderRepository = new OrderRepository();
session_start();
$auth = new Auth();
function is_empty($input, $key)
{
    return !(isset($input[$key]) && trim($input[$key]) !== "");
} 
if (isset($_GET['car_id'])) {
    $carId = (int)$_GET['car_id'];
    $repository = new CarRepository();
    $car = $repository->findById($carId);
}
else {
    $car = null;
}

$ordersforthiscar = $orderRepository->findByCarId((string)$carId);

$year = $car->year;
$brand = $car->brand;
$model = $car->model;
$pass = $car->passengers;
$price = $car->daily_price_huf;





function NewCar($input, &$errors,  &$fueltNew, &$car, &$transNew)
{
    if (is_empty($input, "fuel_type"))
        {
            $fueltNew = $car->fuel_type;
     
    }
    else{
        $fueltNew = $_POST['fuel_type'];
    }

    if (is_empty($input, "transmission"))
    {
        $transNew = $car->transmission;
 
}
else{
    $transNew = $_POST['transmission'];
}
if (isset($_POST['year'])){
    
        $year = $_POST['year'];
        if (is_numeric($year) && strlen($year) === 4) {
        } else {
        $errors[] = "Invalid year."; 
}

if (isset($_POST['daily_price_huf'])){
    
    $p = $_POST['daily_price_huf'];
    if (is_numeric($p)) {
    } else {
    $errors[] = "Invalid price."; 
}}



    return !(bool) $errors;
}

}



$errors = [];
if (count($_POST)>= 1){
    if (NewCar($_POST, $errors, $fueltNew, $car ,$transNew) ) {
        $newBrand = $_POST['brand'];
        $newModel = $_POST['model'];
        $newPass = $_POST['passengers'];
        $yearNew = $_POST['year'];
        $pricenew = $_POST['daily_price_huf'];
        
    $carnew = new Car($newBrand, $newModel,$yearNew, $transNew, $fueltNew,$newPass, $pricenew, $car->image, );
    $repository->storage->update($carId, (array)$carnew);
    header("Location: modify-item.php?car_id=$carId");
        
    
}
}






$itemsPerRow = 5;
$totalItems = count($ordersforthiscar);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href=".media/style3.css">
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
<?php if ($errors) {?>
        <?php foreach ($errors as $error) {?>
        <a><?=$error?></a>
        <?php }?>
    <?php }?>



<div class="car-details">
        <h2><?php echo htmlspecialchars($car->brand . " " . $car->model); ?></h2>
        <p>Year: <?php echo htmlspecialchars($car->year); ?></p>
        <p>Transmission: <?php echo htmlspecialchars($car->transmission); ?></p>
        <p>Fuel Type: <?php echo htmlspecialchars($car->fuel_type); ?></p>
        <p>Passengers: <?php echo htmlspecialchars($car->passengers); ?></p>
        <p>Price: <?php echo htmlspecialchars($car->daily_price_huf); ?> HUF/day</p>
        <img src="<?php echo htmlspecialchars($car->image); ?>" alt="Car Image">
    </div>

    <?php
echo "<div class='container'>";
for ($i = 0; $i < $totalItems; $i += $itemsPerRow) {
    $row = array_slice($ordersforthiscar, $i, $itemsPerRow);
    echo "<div class='row'>";
    foreach ($row as $item) {
    
        if ($car) {
            echo "<div class='box' style='background-image: url(\"" . htmlspecialchars($car->image) . "\");'> 
                <p>" . htmlspecialchars($item->email ) . "<br>
                " . htmlspecialchars($item->datestart) . " - " . htmlspecialchars($item->dateend) . "</p>
                    <p>" . htmlspecialchars($car->brand) . " " . htmlspecialchars($car->model) . "</p>
                    <p>Passengers: " . htmlspecialchars($car->passengers) . "</p>
                    <p>Price: " . htmlspecialchars($car->daily_price_huf) . " HUF/day</p>"
            ;
                $id = (string)$item->id;
                echo "<form method='GET' action='delete-booking.php' class='delete-form'>
                        <input type='hidden' name='order_id' value='" . htmlspecialchars($id) . "'>
                        <button type='submit' class='delete-button'>Delete</button>
                      </form>";
             "
                  
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
<form action="" method="post">
    <label for="year">Year: </label>
    <input id="year" name="year" type="text" value="<?= htmlspecialchars($car->year) ?>"><br>

    <label for="brand">Brand: </label>
    <input id="brand" name="brand" type="text" value="<?= htmlspecialchars($car->brand) ?>"> <br>

    <label for="model">Model: </label>
    <input id="model" name="model" type="text" value="<?= htmlspecialchars($car->model) ?>"><br>

    <label for="transmission">Transmission: </label>
    <input type="radio" id="Man" name="transmission" value="Manual" <?= $car->transmission === 'Manual' ? 'checked' : '' ?>>
    <label for="Man">Manual</label>
    <input type="radio" id="Aut" name="transmission" value="Automatic" <?= $car->transmission === 'Automatic' ? 'checked' : '' ?>>
    <label for="Aut">Automatic</label><br>

    <label for="fuel_type">FuelType: </label>
    <input type="radio" id="Pe" name="fuel_type" value="Petrol" <?= $car->fuel_type === 'Petrol' ? 'checked' : '' ?>>
    <label for="Pe">Petrol</label>
    <input type="radio" id="Di" name="fuel_type" value="Diesel" <?= $car->fuel_type === 'Diesel' ? 'checked' : '' ?>>
    <label for="Di">Diesel</label>
    <input type="radio" id="El" name="fuel_type" value="Electric" <?= $car->fuel_type === 'Electric' ? 'checked' : '' ?>>
    <label for="El">Electric</label><br>

    <label for="passengers">Passengers: </label>
    <input type="number" id="passengers" name="passengers" value="<?= htmlspecialchars($car->passengers) ?>" min="1" max="20"><br>

    <label for="daily_price_huf">Daily price in HUF: </label>
    <input id="daily_price_huf" name="daily_price_huf" type="text" value="<?= htmlspecialchars($car->daily_price_huf) ?>"><br>

    <input type="submit" value="Update">
</form>
    
</body>
</html>
