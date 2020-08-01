<?php
namespace Vexsoluciones\Module\Glovo\Model;

use Codeigniter\Service;
use Vexsoluciones\Module\Glovo\Helper;

class City
{
    const TABLE_NAME = 'vex_glovo_city_config';

    public $id;
    public $countryId;
    public $regionId;
    public $currencyId;
    public $code;
    public $lat;
    public $lng;
    public $radius;
    public $maxOrders;
    public $zoom;
    public $address;
    public $address2;
    public $details;
    public $contactPerson;
    public $contactPhone;
    public $postalCode;
    public $active;
    public $workingTimes;
    public $holidays;

    public function __construct($data)
    {
        $this->id = $data->id;
        $this->countryId = $data->country_id;
        $this->regionId = $data->region_id;
        $this->currencyId = $data->currency_id;
        $this->code = $data->code;
        $this->lat = $data->lat;
        $this->lng = $data->lng;
        $this->radius = $data->radius;
        $this->maxOrders = $data->max_orders;
//        $this->zoom = $data->zoom;
        $this->address = $data->address;
        $this->address2 = $data->address2 ?? '';
        $this->details = $data->details;
        $this->contactPerson = $data->contact_person;
        $this->contactPhone = $data->contact_phone;
        $this->active = $data->active;
        $this->workingTimes = $data->working_times;
        $this->holidays = $data->holidays;
        $this->postalCode = $data->postal_code;
    }

    public function toArray()
    {
        $data = [
            'country_id' => (int) $this->countryId,
            'region_id' => (int) $this->regionId,
            'currency_id' => $this->currencyId,
            'code' => $this->code,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'radius' => $this->radius,
            'max_orders' => $this->maxOrders,
            'zoom' => $this->zoom,
            'address' => $this->address,
            'address2' => $this->address2,
            'details' => $this->details,
            'postal_code' => $this->postalCode,
            'contact_person' => $this->contactPerson,
            'contact_phone' => $this->contactPhone,
            'active' => (int) $this->active,
            'working_times' => $this->workingTimes,
            'holidays' => $this->holidays,
        ];

        return $data;
    }

    public function toJSON()
    {
        return [
            'id' => (int) $this->id,
            'country_id' => (int) $this->countryId,
            'zone_id' => (int) $this->regionId,
            'code' => $this->code,
            'geo_lat' => (float) $this->lat,
            'geo_lng' => (float) $this->lng,
            'geo_radius' => (float) $this->radius,
            'geo_zoom' => (float) $this->zoom,
            'store_address' => $this->address,
            'store_address2' => $this->address2,
            'postal_code' => $this->postalCode,
            'phone' => $this->contactPhone,
            'contact' => $this->contactPerson,
        ];
    }

    /**
     * @param \Language $language
     * @return array
     */
    public function getWorkingTimes(\Language $language)
    {
        $days = [
            $language->get('entry_day_0'),
            $language->get('entry_day_1'),
            $language->get('entry_day_2'),
            $language->get('entry_day_3'),
            $language->get('entry_day_4'),
            $language->get('entry_day_5'),
            $language->get('entry_day_6')
        ];
        $response = [];
        $cityHour = explode(',', $this->workingTimes);

        for ($i = 1; $i <= 7; $i++)
        {
            $response[] = [
                'd' => $i-1,
                'name' => $days[$i-1],
                'status' => $cityHour[($i*3)-3],
                'hour_start' => $cityHour[($i*3)-2],
                'hour_end' => $cityHour[($i*3)-1]
            ];
        }

        return $response;
    }

    /**
     * @version 1.0.0
     * @param \Language $language
     * @param $preparationTime
     * @return array
     * @throws \Exception
     */
    public function getWorkingTimesFrontendTest(\Language $language, $preparationTime)
    {
        $days = [];
        $daysTemp = $this->getWorkingTimes($language);
        $timeNow = new \DateTime();
        $daysAvailable = [];
        $daysNotAvailable = [];
        $dayEnd = new \DateTime();

        for ($i = 0; $i <= 3; $i++)
        {
            $test = (new \DateTime())->setTimestamp($timeNow->getTimestamp());
            $test->modify("{$i} day");
            $dayEnd = $test;

            if ($daysTemp[$test->format('w')]['status'] == 0)
            {
                $daysNotAvailable[] = (int) strtotime($test->format('d-m-Y'));
                continue;
            }

            $daysAvailable[] = [
                'time' => $test,
                'start' => $daysTemp[$test->format('w')]['hour_start'],
                'end' => $daysTemp[$test->format('w')]['hour_end'],
            ];
        }

        $estimateIntervals = $this->_estimateIntervals($daysAvailable, $preparationTime);

        if (count($estimateIntervals[0]['intervals']) < 1)
            $daysNotAvailable[] = (int) strtotime($estimateIntervals[0]['date']);

        $daysNotAvailable = $this->calculateHolidaysFrontend($daysNotAvailable);

        return [
            'daysNotAvailable' => $daysNotAvailable,
            'daysAvailable' => $estimateIntervals,
            'day_start' => $timeNow->format('d-m-Y'),
            'day_end' => $dayEnd->format('d-m-Y'),
            'time' => $timeNow->getTimestamp()
        ];
    }

    private function _estimateIntervals(array $days, $preparationTime)
    {
        $db = Service::db();
        $dateNow = new \DateTime();
        $response = [];

        foreach ($days as $day)
        {
            $start = \DateTime::createFromFormat('d-m-Y H:i', "{$day['time']->format('d-m-Y')} {$day['start']}");
            $end = \DateTime::createFromFormat('d-m-Y H:i', "{$day['time']->format('d-m-Y')} {$day['end']}");
            $isToday = $dateNow->format('d-m-Y') == $day['time']->format('d-m-Y');

            $dayData = [
                'date' => $day['time']->format('d-m-Y'),
                'intervals' => [],
                'range' => "{$day['start']} ~ {$day['end']}"
            ];

            if ($dateNow->format('d') === $start->format('d'))
            {
                $hourMinNow = $dateNow->format('H:i:s');
                // If do not want to use min and second from now, then starts
                // to calculate to next closest hour with 00 min.
                // Example: 11:52 => 12:00
                // Note: This feature has been added for TuSuper.com.ar for hour schedule
                // and order limit per hour.
                if (true)
                {
                    $tempDate = $dateNow;
                    $minutes = $tempDate->format('i');
                    if ($minutes > 0)
                    {
                        $tempDate->modify('+1 hour');
                        $tempDate->modify(sprintf('-%s minutes', $minutes));
                    }

                    $hourMinNow = $tempDate->format('H:i:s');
                }
                // ========================

                $start = \DateTime::createFromFormat('d-m-Y H:i:s', "{$day['time']->format('d-m-Y')} {$hourMinNow}");
            }

            if ($isToday)
                $start->modify("+{$preparationTime} minutes");

            while ($start->getTimestamp() < $end->getTimestamp())
            {
                $temp = \DateTime::createFromFormat('d-m-Y H:i:s', $start->format('d-m-Y H:i:s'));
                $endRow = $temp->modify('+60 minutes');

                // Special feature for TuSuper.com.ar
                $currentOrders = $db
                    ->where('date_schedule >=', $start->getTimestamp())
                    ->where('date_schedule <', $endRow->getTimestamp())
                    ->where('status', 'pending')
                    ->count_all_results('oc_glovo_order');

                if ($currentOrders > 0)
                {
                    $start->modify('+60 minutes');
                    continue;
                }
                // =========================

                if ($endRow->getTimestamp() <= $end->getTimestamp())
                {
                    $dayData['intervals'][] = $this->createIntervalItem($start, $endRow);
                } else {
                    if (!$isToday)
                    {
                        $dayData['intervals'][] = $this->createIntervalItem($start, $end);
                    }
                }

                $start->modify('+60 minutes');
            }

//            if (count($dayData['intervals']) > 0)
            $response[] = $dayData;
        }

        return $response;
    }

    private function createIntervalItem(\DateTime $start, \DateTime $end)
    {
        return [
            'name' => $start->format('H:i') . ' ~ ' . $end->format('H:i'),
            'timestamp' => $start->getTimestamp(),
        ];
    }

    public function save()
    {
        $db = Service::db();

        if (null === $this->id)
        {
            $this->validateInsertEntity($db);
            $db->insert(self::TABLE_NAME, $this->toArray());
            $this->id = $db->insert_id();

            return $this;
        }

        $db->update(self::TABLE_NAME, $this->toArray(), ['id' => $this->id]);
        return $this;
    }

    /**
     * @param $db \CI_DB_mysql_driver|\CI_DB_query_builder
     * @throws \Exception
     */
    private function validateInsertEntity($db)
    {
        $data = $db->get_where(self::TABLE_NAME, ['code' => $this->code])->row();

        if (null !== $data)
        {
            throw new \Exception("The city with code {$this->code} exists in DB.");
        }
    }

    public static function findAll($where = [])
    {
        $result = Service::db()->select('CC.*, CO.name as country, Z.name as region')
            ->from(self::TABLE_NAME . ' AS CC')
            ->join('country AS CO', 'CC.country_id = CO.country_id')
            ->join('zone AS Z', 'CC.region_id = Z.zone_id');

        return $result->get()->result_array();
    }

    public static function createFromPost($data)
    {
        return new static((object) [
            'id' => $data['store_id'],
            'active' => isset($data['active']) ? 1 : 0,
            'currency_id' => $data['currency_id'],
            'country_id' => $data['country_id'],
            'region_id' => $data['zone_id'],
            'code' => $data['code'] ?? '',
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'radius' => $data['radius'],
            'max_orders' => $data['max_orders'],
//            'zoom' => $data['zoom'],
            'address' => $data['address'],
            'details' => $data['details'],
            'postal_code' => $data['postal_code'],
            'contact_person' => $data['contact_person'],
            'contact_phone' => $data['contact_phone'],
            'working_times' => $data['city_hour'],
            'holidays' => $data['holidays'],
        ]);
    }

    public static function loadByCode($code)
    {
        return new static(self::loadFromKeyValuePair('code', $code));
    }

    public static function loadByID($id)
    {
        return new static(self::loadFromKeyValuePair('id', $id));
    }

    public static function loadByRegionId($code)
    {
        return new static(self::loadFromKeyValuePair('region_id', $code));
    }

    private static function loadFromKeyValuePair($key, $value)
    {
        $city = Service::db()->get_where(self::TABLE_NAME, [$key => $value])->row();

        if (null === $city)
        {
            throw new \Exception("City {$value} not configured in admin");
        }

        return $city;
    }

    // ------------------------------------------------------------------------

    public function calculateHolidaysFrontend($daysNotAvailable = [])
    {
        $holidaysString = explode(',', $this->holidays);

        for ($i = 1; $i <= (count($holidaysString)/3); $i++)
        {
            $day = $holidaysString[($i*3)-2];
            $month = $holidaysString[($i*3)-1];

            $date = \DateTime::createFromFormat('d-m', "{$day}-{$month}");
            $daysNotAvailable[] = (int) strtotime($date->format('d-m-Y'));
        }

        return $daysNotAvailable;

    }
}

/*
City Test:
INSERT INTO `oc_vex_glovo_city_config` VALUES(null, 167, 2541, 'PEN', 'LIM', '-12.0853499', '-77.0393415', '14', 'Avenida José Leal 549, Lima Lince', '', 'A la vuelta del parque castilla', 'Sebastian Yabiku Sifuentes', '923708059', 1, '1,09:00,20:00,1,09:00,20:00,1,09:00,20:00,1,09:00,20:00,1,09:00,20:00,1,09:00,20:00,1,09:00,20:00');

*/