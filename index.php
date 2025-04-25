<?php
require_once "storage/item-storage.php";
require_once "storage/auth.php";
require_once "storage/booking-storage.php";
$orderRepository = new OrderRepository();
$users = new UserRepository();

$auth = new Auth();
function is_empty( $key)
{
    return !(isset($_POST[$key]) && trim($_POST[$key]) !== "");
}
$repository = new CarRepository();
$carsf = $repository->all();

if (!(is_empty( "transmission"))) {
    $trans = $_POST["transmission"];
    $carsf = array_filter($carsf, function ($car) use ( $trans) {
        return $car->transmission === $trans;
    });
}
if (!(is_empty("filtercapacity"))) {
    $capacity = $_POST["filtercapacity"];
    $carsf = array_filter($carsf, function ($car) use ($capacity) {
        return $car->passengers >= $capacity;
    });
}

    if (!(is_empty( "max_price")) && !(is_empty( "min_price"))) {
        $minp = $_POST["min_price"];
        $maxp = $_POST["max_price"];
        if ($maxp >= $minp){
            $carsf = array_filter($carsf, function ($car) use ( $minp, $maxp) {
                return $car->daily_price_usd <= $maxp && $car->daily_price_usd >= $minp ;
            });

        }
        else{
            echo"invalid price range ";
        }
       
}

if (!(is_empty( "datefrom")) && !(is_empty( "dateuntil"))) {
    $dateFrom = isset($_POST["datefrom"]) ? strtotime($_POST["datefrom"]) : null;
    $dateUntil = isset($_POST["dateuntil"]) ? strtotime($_POST["dateuntil"]) : null;
    if ($dateFrom && $dateUntil && $dateUntil < $dateFrom) {
         
        $carsf = [];
    } elseif (!$dateFrom || !$dateUntil) {
        
        $carsf = [];
    }
    $carsfdate = [];
    foreach($carsf as $car)
    {
        $carId =$car->id;
       
        $ordersforthiscar = $orderRepository->findByCarId((string)$carId);
        $okay = true;
        foreach ($ordersforthiscar as $order) {
            $odateS = strtotime($order->datestart);
            $odateE = strtotime($order->dateend);
            if ( $odateS <= $dateUntil && $odateS >= $dateFrom ||  $dateFrom >= $odateS && $dateFrom <= $odateE ){$okay = false;}
        }
        if($okay){
            array_push($carsfdate, $car);
        }
       
    }

    $carsf = $carsfdate;
}


$itemsPerRow = 5;
$totalItems = count($carsf);

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
                    $user = null;
                 ?>
                
                    <a href="login.php" class="button1">Login</a>
               
                    <a href="register.php" class="button1">Register</a>
             
            <?php }
            else{
                $email = $_SESSION["user"];
                    $user = $users->findOne(['email' => $email]);
                    echo $email;

                ?>
                
                    <a href="logout.php" class="button1">Log out</a>
                
                    <a href="account.php" class="button1">Account</a>
          
            <?php }
            ?>
        </nav>
    </div>
</header>

<body>
    <!-- <a href="register.php">Register</a> -->
    <div id="szures">
        <form action="" method="post">
            <div class="inline-group">
                <label for="transmission">Transmission type</label>
                <select name="transmission" id="transmission">
                <option>Choose</option>
                    <option value="Automatic">Automatic</option>
                    <option value="Manual">Manual</option>
                </select>
                <label for="filtercapacity">Seating capacity</label>
                <input type="number" name="filtercapacity"  value="<?=isset($_POST["filtercapacity"]) ? $_POST["filtercapacity"] : ""?>"  min="1" max="20">
                        
                <label for="min_price">Price range:</label>
                <input type="number" name="min_price" id="min_price" min="0" step="100" 
                value="<?= isset($_POST['min_price']) ? htmlspecialchars($_POST['min_price']) : '' ?>" 
                oninput="updateMaxMin()">

                <label for="max_price">-</label>
                <input type="number" name="max_price" id="max_price" min="0" step="100" 
                value="<?= isset($_POST['max_price']) ? htmlspecialchars($_POST['max_price']) : '' ?>">
            </div>
            <div class="inline-group">
                <label for="datefrom">Dates: </label>
                <input id="datefrom" name="datefrom" type="date"> - 
                <input id="dateuntil" name="dateuntil" type="date"> 

            </div>
            <button type="submit"> Submit</button>
        </form>

    </div>


    <?php
    $isAdmin = $user && isset($user['admin']) && $user['admin'] == true;
    $totalItems = count($carsf) + ($isAdmin ? 1 : 0);

    echo "<div class='container'>";
    for ($i = 0; $i < $totalItems; $i += $itemsPerRow) {
        $row = array_slice($carsf, $i, $itemsPerRow);
        
        echo "<div class='row'>";
        foreach ($row as $item) {
          
            $id = (string)$item->id;
            $brand = htmlspecialchars($item->brand); 
            $model = htmlspecialchars($item->model ?? "");
            $price = htmlspecialchars($item->daily_price_usd ?? ""); 
            $passengers = htmlspecialchars($item->passengers ?? ""); 
            $imagePath = htmlspecialchars($item->image ?? "default.jpg"); 
            echo "
            <div class='box'\">
                 <img src='" . htmlspecialchars($imagePath) . "' alt='" . htmlspecialchars($brand . ' ' . $model) . "' class='car-image'>
            <h2>$brand $model</h2>
             <div class='inline-group'>
                <p> $price HUF
                <br>Passengers: $passengers</p>";
                if  ($user && isset($user['admin']) && $user['admin'] == true) {
                    echo "
                    <div class='under' >
                    <form method='GET' action='delete-item.php' class='delete-form'>
                            <input type='hidden' name='car_id' value='" . htmlspecialchars($id) . "'>
                            <button type='submit' class='delete-button'>Delete</button>
                          </form>
                          ";
                          echo "<form method='GET' action='modify-item.php' class='delete-form'>
                          <input type='hidden' name='car_id' value='" . htmlspecialchars($id) . "'>
                          <button type='submit' class='delete-button'>Modify</button>
                        </form>

                        </div>
                        ";
                        
                }
                else{
                    echo "<form method='GET' action='item-details.php?' class='delete-form'>
                          <input type='hidden' name='id' value='" . htmlspecialchars($id) . "'>
                          <button type='submit' class='delete-button'>Select</button>
                        </form>
                        ";
                    
                
            
        }
        echo " </div>
        </div>";
    }

      if ($isAdmin && $i + $itemsPerRow >= $totalItems) {
        echo "
        <div class='box'>
            <h2>Admin Box</h2>
            <p>This is a special admin box.</p>
            <button onclick=\"window.location.href='admin-actions.php'\">Admin Action</button>
        </div>";
    }


       
    $remainingSlots = $itemsPerRow - count($row) - ($isAdmin && $i + $itemsPerRow >= $totalItems ? 1 : 0);
    for ($j = 0; $j < $remainingSlots; $j++) {
        echo "<div class='box hidden'></div>";
    }
       
        echo "</div>";}
    
   
    echo "</div>";
    
       
         ?>
</body>

</html>