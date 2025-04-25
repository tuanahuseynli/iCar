<?php 
include_once "storage.php";

class Car{
    public $id = null;
    public $brand;
    public $model;
    public $year;
    public $transmission;
    public $fuel_type;
    public $passengers;
    public $daily_price_usd;
    public $image;


public function __construct($brand = null, $model = null, $year = null, $transmission = null, $fuel_type = null,$passengers = null,$daily_price_usd = null, $image = null)
    {
        $this->brand = $brand;
        $this->model = $model;
        $this->year = $year;
        $this->transmission = $transmission;
        $this->fuel_type = $fuel_type;
        $this->passengers = $passengers;
        $this->daily_price_usd = $daily_price_usd;
        $this->image = $image;
    }

    public static function from_array(array $arr): Car
    {
        $instance = new Car();
        $instance->id = $arr['id'] ?? null;
        $instance->brand = $arr['brand'] ?? null;
        $instance->model = $arr['model'] ?? null;
        $instance->year = $arr['year'] ?? null;
        $instance->transmission = $arr['transmission'] ?? null;
        $instance->fuel_type = $arr['fuel_type'] ?? null;
        $instance->passengers = $arr['passengers'] ?? null;
        $instance->daily_price_usd = $arr['daily_price_usd'] ?? null;
        $instance->image = $arr['image'] ?? null;
        return $instance;
    }

    public static function from_object( $obj): Car
    {
        return self::from_array((array) $obj);
    }

}


class CarRepository
{
    public $storage;
    public function __construct()
    {
        $filen = new JsonIO('media/items.json');
        $this->storage = new Storage($filen);
    }
    private function convert( array $arr): array
    {
        return array_map([Car::class, 'from_object'], $arr);
    }
    public function All()
    {
        return $this->convert($this->storage->findAll());
    }
    public function add(Car $car): string
    {
        return $this->storage->insert($car);
    }
    public function findById(string $id): ?Car
    {
        $carData = $this->storage->findById($id);
        return $carData ? Car::from_object((object) $carData) : null;
    }

    public function deleteCars(callable $condition): void
    {
        $this->storage->deleteMany($condition);
    }

    public function findByBrand(string $brand): array
    {
        return $this->findCarsByCondition(function ($car) use ($brand) {
            return $car->brand === $brand;
        });
    }



    public function findCarsByCondition(callable $condition): array
    {
        return $this->convert($this->storage->findMany($condition));
    }


    public function updateCars(callable $condition, callable $updater): void
    {
        $this->storage->updateMany($condition, $updater);
    }
    public function findByIds(array $ids): array
{
    return $this->findCarsByCondition(function ($car) use ($ids) {
        return in_array($car["id"], $ids);
    });
}


}
?>