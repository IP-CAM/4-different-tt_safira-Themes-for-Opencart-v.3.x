<?php
namespace Vexsoluciones\Module\Glovo\ActiveRecord;

class Order extends BaseModel
{
    const TABLE_NAME = 'glovo_order';
    const PRIMARY_KEY = 'id';

    const STATUS_PENDING = 'pending';
    const STATUS_SHIPPING = 'shipping';
    const STATUS_COMPLETE = 'complete';
    const STATUS_CANCELED = 'canceled';

    public $id;
    public $order_id;
    public $glovo_order_id;
    public $status;
    public $glovo_status;
    public $address;
    public $address_lat;
    public $address_lng;
    public $cost;
    public $city_code;
    public $date_added;
    public $date_schedule;
    public $date_delivered;
    public $description;
    public $last_error;
    public $reference;

    protected $_viewLink = '';
    protected $_cancelLink = '';
    protected $_completedLink = '';

    public static function findByGlovoId($glovoId)
    {
        return self::findByKeyValuePair('glovo_id', $glovoId);
    }

    public function toJSON()
    {
        return [
            'order_id' => (int) $this->order_id,
            'glovo_order_id' => (int) $this->glovo_order_id ?: '-',
            'status' => $this->status,
            'glovo_status' => $this->glovo_status,
            'address' => $this->address,
            'address_lat' => $this->address_lat,
            'address_lng' => $this->address_lng,
            'cost' => $this->cost,
            'city_code' => $this->city_code,
            'date_added' => date('d/m/y h:i A', strtotime($this->date_added)),
            'date_schedule' => $this->date_schedule,
            'date_delivered' => $this->date_delivered > 0 ? date('d/m/y h:i A', strtotime($this->date_delivered)) : '-',
            'description' => $this->description,
            'last_error' => $this->last_error,
            'reference' => $this->reference,

            'view_link' => $this->_viewLink,
            'cancel_link' => $this->_cancelLink,
            'completed_link' => $this->_completedLink,

            'date_schedule_pretty' => date('d/m/y h:i A', $this->date_schedule),
        ];
    }

    public function setViewLink($viewLink = '')
    {
        $this->_viewLink = $viewLink;
    }

    public function setCancelLink($cancelLink = '')
    {
        $this->_cancelLink = $cancelLink;
    }

    public function setCompletedLink($completedLink = '')
    {
        $this->_completedLink = $completedLink;
    }
}