<?php 
include_once "storage.php";

class Order{
    public $id = null;
    public $carid;
    public $email;
    public $datestart;
    public $dateend;



public function __construct($carid = null, $email = null, $datestart = null, $dateend = null)
    {
        $this->carid = $carid;
        $this->email = $email;
        $this->datestart = $datestart;
        $this->dateend = $dateend;
    }

    public static function from_array(array $arr): Order
    {
        $instance = new Order();
        $instance->id = $arr['id'] ?? null;
        $instance->carid = $arr['carid'] ?? null;
        $instance->email = $arr['email'] ?? null;
        $instance->datestart = $arr['datestart'] ?? null;
        $instance->dateend = $arr['dateend'] ?? null;
        return $instance;
    }

    public static function from_object( $obj): Order
    {
        return self::from_array((array) $obj);
    }

}


class OrderRepository
{
    private $storage;
    public function __construct()
    {
        $filen = new JsonIO('media/bookings.json');
        $this->storage = new Storage($filen);
    }
    private function convert( array $arr): array
    {
        return array_map([Order::class, 'from_object'], $arr);
    }
    public function All()
    {
        return $this->convert($this->storage->findAll());
    }
    public function add(Order $order): string
    {
        return $this->storage->add($order);
    }

    public function deleteOrder(callable $condition): void
    {
        $this->storage->deleteMany($condition);
    }

    public function findByEmail(string $email): array {
        return $this->convert($this->storage->findMany(function ($order) use ($email) {
            return $order['email'] === $email;
        }));
    }

    public function getCarIdsByEmail(string $email): array {
        $orders = $this->findByEmail($email);
        return array_map(fn($order) => $order, $orders);
    }


    public function updateOrders(callable $condition, callable $updater): void
    {
        $this->storage->updateMany($condition, $updater);
    }
    public function findByCarId(string $carId): array
    {
        $allOrders = $this->storage->findAll(); // Retrieve all orders
        //error_log("All orders: " . print_r($allOrders, true)); // Debug: Log all orders
    
        $filteredOrders = array_filter($allOrders, function ($order) use ($carId) {
            return isset($order['carid']) && $order['carid'] === (int)$carId;
        });
    
        //error_log("Filtered orders for carId {$carId}: " . print_r($filteredOrders, true)); // Debug: Log filtered orders
    
        return $this->convert($filteredOrders);
    }
    


}
?>