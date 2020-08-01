<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Vexsoluciones\Framework\Config\SettingsConfig as VexSettingsConfig;
use Vexsoluciones\Module\Glovo\Helper\EstimateOrder as EstimateOrderHelper;
use Vexsoluciones\Module\Glovo\Model\Address as GlovoAddress;
use Vexsoluciones\Module\Glovo\Model\City as StoreConfig;
use Vexsoluciones\Module\Glovo\Model\Order as GlovoOrder;
use Vexsoluciones\Module\Glovo\Client as GlovoClient;
use Vexsoluciones\Module\Glovo\GlovoException;
use Vexsoluciones\Module\Glovo\Model\WorkingArea;
use Vexsoluciones\Module\Glovo\PreparationTime;

class ModelExtensionShippingGlovo extends Model
{
    use VexSettingsConfig;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('setting/setting');
        $this->load->language('extension/shipping/glovo');
        date_default_timezone_set('America/Argentina/Buenos_Aires');

        $this->_settingPattern = 'shipping_glovo_%s';
    }

    function isActive($address)
    {
        if ($this->getSettingValue('status') == '0')
            return false;

        try {
            $store = StoreConfig::loadByRegionId($address['zone_id']);
        } catch (Exception $e) {
            return false;
        }

        if (!$store->active)
            return false;

        $workingNow = $store->getWorkingTimes($this->language)[date('w')];

        if (!$workingNow['status'])
            return false;

        $dateNow = new DateTime();
        $start = \DateTime::createFromFormat('d-m-Y H:i', "{$dateNow->format('d-m-Y')} {$workingNow['hour_start']}");
        $end = \DateTime::createFromFormat('d-m-Y H:i', "{$dateNow->format('d-m-Y')} {$workingNow['hour_end']}");

        /*if ($dateNow->getTimestamp() < $start->getTimestamp() || $dateNow->getTimestamp() > $end->getTimestamp())
            return false;*/

        return true;
    }

    function getQuote($address)
    {
        /*$quote_data = array();

        $quote_data['glovo'] = array(
            'code'         => 'glovo.glovo',
            'title'        => 'Testing',
            'cost'         => 0.00,
            'tax_class_id' => 0,
            'text'         => $this->currency->format(0.00, $this->session->data['currency'])
        );

        $method_data = array(
            'code'       => 'glovo',
            'title'      => $this->language->get('text_title'),
            'quote'      => $quote_data,
            'sort_order' => $this->config->get('shipping_free_sort_order'),
            'error'      => false
        );

        return $method_data;*/
        $method_data = array();

        if (!$this->isActive($address))
        {
            return $method_data;
        }

        $status = false;
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_flat_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
        if (!$this->config->get('shipping_flat_geo_zone_id')) $status = true;
        elseif ($query->num_rows) $status = true;

        if ($status) {
            $store = StoreConfig::loadByRegionId($address['zone_id']);
            $destination = (new GlovoAddress())->setDataFromCityConfig($store)->setType(GlovoAddress::TYPE_PICKUP);
            /** @var EstimateOrderHelper $estimatedPrice */
            $estimatedPrice = $this->estimateOrder(
                0,
                $store,
                $destination,
                0,
                1
            );

            $estimatedPrice->calculateFreeShippingCost($this->cart, $this->getSettingValue('free_shipping_cost'));
            $estimatedPrice->calculate($this->currency, $this->session, $this->language);

            $quote_data = array();
            $quote_data['glovo'] = array(
                'code' => 'glovo.glovo',
                'title' => 'Delivery',
                'cost' => 0.00,
                'tax_class_id' => /*$this->config->get('shipping_flat_tax_class_id')*/0,
                'text' => "<span id='shipping_glovo_price'>{$estimatedPrice->amountFormatted}</span> <i class='' id='shipping_glovo_loader'></i>"
            );

            $method_data = array(
                'code' => 'glovo',
                'title' => 'TuSuper',
                'quote' => $quote_data,
                'sort_order' => /*$this->config->get('shipping_flat_sort_order')*/0,
                'error' => false
            );
        }

        return $method_data;
    }

    /**
     * Returns Glovo Setting to Checkout Page.
     */
    public function getCheckoutSettings($address)
    {
        try {
            $store = StoreConfig::loadByRegionId($address['zone_id']);
            $mapPoints = $this->getMapPoints($store->code);
        } catch (Exception $e) {
            \Vexsoluciones\Module\ShippingGlovoConstants::logger()->write('Error: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }

        $orderWeightSize = $this->estimateOrderWeightSize();

        $preparationTime = PreparationTime::instance($this->getSettingValue('preparation_time_attrib'), $this->config->get('config_language_id'))
            ->calculateFromCartItems($this->cart->getProducts());

        $preparationTime += (int)$this->getSettingValue('preparation_time_additional');

        return [
            'map_points' => json_encode($mapPoints),
            'estimate_url' => $this->url->link('extension/shipping/glovo/estimate_order', '', true),
            'city' => $store->toJSON(),
            'store_icon' => 'image/catalog/glovo/shipping_store_icon.png',
            'carrier_icon' => 'image/catalog/glovo/shipping_glovo_icon.png',
            'zoom' => (float) $this->getSettingValue('google_map_zoom'),
            'customer' => [
                'full_name' => '',
                'address' => "{$address['address_1']} {$address['address_2']}",
            ],
            'volume' => [
                'max' => (40*40*30)/1000,
                'total' => $orderWeightSize['volume'] / 1000
            ],
            'weight' => [
                'max' => 9,
                'total' => $orderWeightSize['weight']
            ],
            'riders' => $this->estimateRidersToOrder($orderWeightSize),
            'preparation_time' => sprintf($this->language->get('entry_preparation_time'), "<b>{$preparationTime} min</b>"),

            'working_time' => $store->getWorkingTimesFrontendTest($this->language, $preparationTime),

            'i18n' => [
                'day' => $this->language->get('entry_day'),
                'hour' => $this->language->get('entry_hour'),
                'free' => $this->language->get('entry_free'),
                'address' => $this->language->get('entry_checkout_address'),
                'contact_person' => $this->language->get('entry_contact_person'),
                'contact_phone' => $this->language->get('entry_contact_phone'),
                'reference' => $this->language->get('entry_checkout_reference'),
                'riders' => $this->language->get('entry_checkout_riders'),
                'destination' => $this->language->get('entry_checkout_destination'),
                'scheduled' => $this->language->get('entry_checkout_scheduled'),
                'confirm_in_map' => $this->language->get('entry_checkout_confirm_in_map'),
                'working_hour' => $this->language->get('entry_checkout_working_hour'),
                'wrong_address' => $this->language->get('entry_checkout_wrong_address'),

                'scheduled_help1' => $this->language->get('entry_checkout_scheduled_help1'),
                'store_name' => $this->config->get('config_name'),
                'customer_name' => $this->language->get('entry_checkout_customer_name'),
            ],
            'google_address' => $this->getLatLngFromAddress($address),
            'address_reference' => $address['address_2'],
            'address_search' => "{$address['address_1']}, {$address['city']}, {$address['country']}",
        ];
    }

    public function estimateRidersToOrder(array $order)
    {
        $total = 1;
        $nroByVolume = 1;
        $nroByWeight = 1;

        if ($order['volume'] > 40*40*30) $nroByVolume = ceil($order['volume'] / (40*40*30));
        if ($order['weight'] > 9) $nroByWeight = ceil($order['weight'] / 9);

        return max($total, $nroByWeight, $nroByVolume);
    }

    public function getLatLngFromAddress($address)
    {
        // If Customer is a Guest
        if (!isset($address['address_id']))
        {
            return [
                'address_id' => 0,
                'lat' => 0,
                'lng' => 0,
                'address' => $address['address_1'],
            ];
        }

        $db = \Codeigniter\Service::db();
//        \Vexsoluciones\Module\ShippingGlovoConstants::logger()->write(json_encode($address));
        $response = $db->get_where('vex_custom_address', ['address_id' => $address['address_id']])->row();
        if (null === $response)
        {
            $db->insert('vex_custom_address', ['address_id' => $address['address_id']]);
            $response = $this->updateLatLngAddress($address['address_id'], '', '', $address['address_1']);
        }

        return $response;
    }

    /** @deprecated  */
    public function updateLatLngAddress($addressId, $lat = '', $lng = '', $address = '')
    {
        $db = \Codeigniter\Service::db();
        $db->where('address_id', $addressId)
            ->update('vex_custom_address', ['lat' => $lat, 'lng' => $lng, 'address' => $address]);
        return $db->get_where('vex_custom_address', ['address_id' => $addressId])->row();
    }

    public function getMapPoints($cityCode)
    {
        /*if (($this->getSettingValue('working_areas_last_update') + 86400) < time())
        {
            $db = \Codeigniter\Service::db();
            $client = new GlovoClient($this->getSettingValue('api_key'), $this->getSettingValue('api_secret'));

            $db->truncate(WorkingArea::TABLE_NAME);
            $db->update('setting', ['value' => time()], ['key' => 'shipping_glovo_working_areas_last_update']);

            foreach ($client->workingAreas()->workingAreas as $area)
            {
                WorkingArea::createNewAndSave((object) [
                    'code' => $area->code,
                    'polygons' => $area->polygons,
                    'workingTimes' => $area->workingTimes
                ]);
            }
        }*/

        return [];

        /*$store = WorkingArea::loadByCode($cityCode);
        return $store->polygons;*/
    }

    public function estimateOrderWeightSize()
    {
        $result = ['weight' => 0, 'volume' => 0];
        $products = $this->cart->getProducts();

        foreach ($products as $product)
        {
            $result['weight'] += (float) $this->weight->convert($product['weight'], $product['weight_class_id'], 1);

            $result['volume'] += (float) $this->length->convert(
                $product['width'] * $product['length'] * $product['height'],
                $product['length_class_id'], 1
            );
        }

        return $result;
    }

    // ------------------------------------------------------------------------
    // ------------------------------------------------------------------------
    // ------------------------------------------------------------------------

    /**
     * @param float $orderCost
     * @param StoreConfig $city
     * @param GlovoAddress $destination
     * @param int $scheduledTime
     * @param int $totalRiders
     * @return \Vexsoluciones\Module\Glovo\Helper\EstimateOrder
     */
    public function estimateOrder(float $orderCost, StoreConfig $city, GlovoAddress $destination, $scheduledTime = 0, $totalRiders = 1)
    {
        $source = new GlovoAddress();
        $source->setType(GlovoAddress::TYPE_PICKUP);
        $source->setDataFromCityConfig($city);

        $order = new GlovoOrder();
        $order->addAddress($source)
            ->addAddress($destination)
            ->setScheduleTime($scheduledTime)
            ->setDescription('A 30cm by 30cm box');

        /*$response = (new GlovoClient($this->getSettingValue('api_key'), $this->getSettingValue('api_secret')))
            ->estimateOrder($order);*/

        $response = (object)[
            'amount' => 0,
            'currency' => 'USD',
        ];

        if ($totalRiders > 1) $response->amount *= $totalRiders;
        if ($this->getSettingValue('cost_shipping_type') == '1') $response->amount = 0;
        if ($this->getSettingValue('cost_shipping_type') == '3') $response->amount = ((float) $this->getSettingValue('cost_shipping_price'))*100;

        $helper = new EstimateOrderHelper($response->amount, $response->currency);

        return $helper->calculate($this->currency, $this->session, $this->language);
    }

    public function createOrder(StoreConfig $store, $lat, $lng, $address, $description, $scheduledTime = 0)
    {
        $body = [
            'scheduleTime' => $scheduledTime == 0 ? null : (int) $scheduledTime,
            'description' => $description,
            'addresses' => [
                [
                    'type' => 'PICKUP',
                    'lat' => $store->lat,
                    'lon' => $store->lng,
                    'label' => $store->address,
                    'details' => $store->details,
                    'contactPhone' => $store->contactPhone,
                    'contactPerson' => $store->contactPerson,
                ],
                [
                    'type' => 'DELIVERY',
                    'lat' => $lat,
                    'lon' => $lng,
                    'label' => $address,
                    'details' => '',
                    'contactPhone' => '',
                    'contactPerson' => '',
                ]
            ]
        ];
        \Vexsoluciones\Module\ShippingGlovoConstants::logger()->write('[POST] Orders | body: ' . json_encode($body));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'https://api.glovoapp.com/b2b/orders');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);

        if (curl_error($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new Exception($error);
        }
        curl_close($ch);
        $content = json_decode($content);

        if (isset($content->error))
            throw new Exception($content->error);

        return $content;
    }

    /**
     * @deprecated
     */
    public function createOrderOld($cityCode, $lat, $lng, $address, $description, $scheduledTime)
    {
        $body = [
            'scheduleTime' => $scheduledTime == 0 ? null : (int) $scheduledTime,
            'description' => $description,
            'addresses' => [
                [
                    'type' => 'PICKUP',
                    'lat' => $this->getSettingValue('city_geo_lat'),
                    'lon' => $this->getSettingValue('city_geo_lng'),
                    'label' => $this->getSettingValue('city_store_address')
                ],
                [
                    'type' => 'DELIVERY',
                    'lat' => $lat,
                    'lon' => $lng,
                    'label' => $address
                ]
            ]
        ];
        \Vexsoluciones\Module\ShippingGlovoConstants::logger()->write('[POST] Orders | body: ' . json_encode($body));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'https://api.glovoapp.com/b2b/orders');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);

        if (curl_error($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new Exception($error);
        }
        curl_close($ch);
        $content = json_decode($content);

        if (isset($content->error))
            throw new Exception($content->error);

        return $content;
    }

    /** @deprecated  */
    private function getHeaders()
    {
        return array(
            'Authorization: Basic ' . base64_encode($this->getSettingValue('api_key') . ':' . $this->getSettingValue('api_secret')),
            'Content-Type: application/json'
        );
    }

    public function calculatePriceForEstimateOrder($amount, $currency)
    {
        // 1° PEN to USD
        // 2° USD to CLP

        if ($this->currency->getId($currency) === 0)
            throw new Exception("Glovo API returns currency {$currency} and this is not configured yet, please go to admin and add this currency.");

        $amountInUSD = $this->currency->convert($amount, $currency, 'USD');

        return $this->currency->convert($amountInUSD, 'USD', $this->session->data['currency']);
    }

    public function formatPriceForEstimateOrder($amount)
    {
        return $this->currency->format($amount, $this->session->data['currency'], 1);
    }

    public function preparePricesForEstimatedOrder($amount, $currency)
    {

    }
}