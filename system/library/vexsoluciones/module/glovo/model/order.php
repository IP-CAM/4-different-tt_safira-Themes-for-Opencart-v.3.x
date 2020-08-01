<?php
namespace Vexsoluciones\Module\Glovo\Model;

class Order
{
    private $time = 0;
    private $description;
    private $addresses = [];

    public function __construct()
    {
    }

    public function toArray()
    {
        return [
            'scheduleTime' => $this->time,
            'description' => $this->description,
            'addresses' => $this->addresses
        ];
    }

    public function setScheduleTime($time)
    {
        $this->time = $time;
        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function addAddress(Address $address)
    {
        $this->addresses[] = $address->toArray();

        return $this;
    }
}