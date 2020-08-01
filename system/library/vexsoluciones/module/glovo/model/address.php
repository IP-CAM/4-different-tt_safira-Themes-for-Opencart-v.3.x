<?php
namespace Vexsoluciones\Module\Glovo\Model;

class Address
{
    const TYPE_PICKUP = 'PICKUP';
    const TYPE_DELIVERY = 'DELIVERY';

    private $lat;
    private $lng;
    private $type;
    private $label;
    private $details;
    private $contactPhone;
    private $contactPerson;

    public function __construct()
    {
    }

    public function toArray()
    {
        $data = [
            'type' => $this->type,
            'lat' => (float) $this->lat,
            'lon' => (float) $this->lng,
            'label' => $this->label,
        ];

        if ($this->details !== null)
            $data['details'] = $this->details;

        if ($this->contactPerson !== null)
            $data['contactPhone'] = $this->contactPerson;

        if ($this->contactPhone !== null)
            $data['contactPerson'] = $this->contactPhone;

        return $data;
    }

    public function setDataFromCityConfig(City $city)
    {
        $this->setLat($city->lat)
            ->setLng($city->lng)
            ->setLabel("{$city->address} {$city->address2}")
            ->setDetails($city->details)
            ->setContactPerson($city->contactPerson)
            ->setContactPhone($city->contactPhone);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param mixed $lat
     * @return $this
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param mixed $lng
     * @return $this
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param mixed $details
     * @return $this
     */
    public function setDetails($details)
    {
        $this->details = $details;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContactPhone()
    {
        return $this->contactPhone;
    }

    /**
     * @param mixed $contactPhone
     * @return $this
     */
    public function setContactPhone($contactPhone)
    {
        $this->contactPhone = $contactPhone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * @param mixed $contactPerson
     * @return $this
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = $contactPerson;
        return $this;
    }
}