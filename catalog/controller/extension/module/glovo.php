<?php

use Vexsoluciones\Framework\Service;
use Vexsoluciones\Framework\QueryBuilder;
use Vexsoluciones\Module\Glovo\Client as GlovoClient;
use Vexsoluciones\Module\ShippingGlovoConstants;
use Vexsoluciones\Module\Glovo\ActiveRecord\Order as OrderEntity;
use Vexsoluciones\Module\Glovo\Model\City as StoreConfig;

/**
 * @property ModelExtensionShippingGlovo model_extension_shipping_glovo
 */
class ControllerExtensionModuleGlovo extends Controller
{
    private $logger;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('setting/setting');
        $this->logger = new Log(ShippingGlovoConstants::LOG_FILE);
    }

    public function addRequiredJsToHeader($route, $data)
    {
        $this->document->addScript('catalog/view/javascript/glovo/jquery-ui.min.js');
        $this->document->addStyle('catalog/view/javascript/glovo/jquery-ui.min.css');
        $this->document->addScript('catalog/view/javascript/glovo/jquery-ui-timepicker-addon.js');
        $this->document->addStyle('catalog/view/javascript/glovo/jquery-ui-timepicker-addon.css');

        $this->document->addScript('catalog/view/javascript/glovo/datepicker/dist/datepicker.js');
        $this->document->addStyle('catalog/view/javascript/glovo/datepicker/dist/datepicker.min.css');

        $this->document->addScript('catalog/view/javascript/glovo/jquery.timepicker.min.js');
        $this->document->addStyle('catalog/view/javascript/glovo/jquery.timepicker.min.css');
        $this->document->addStyle('catalog/view/javascript/glovo/actions.css');

        $this->document->addStyle('catalog/view/javascript/glovo/bs3_toggle.css');
    }

    public function addJsInCheckoutShippingStep($route, $data)
    {
        $this->load->model(ShippingGlovoConstants::ADMIN_MAIN_MODEL_ROUTE);
        $address = $this->session->data['shipping_address'];

        if ($this->model_extension_shipping_glovo->isActive($address))
        {
            $checkoutData = $this->model_extension_shipping_glovo->getCheckoutSettings($address);

            if (true || count($checkoutData['working_time']['daysAvailable'][0]['intervals']) > 0)
            {
                echo '<script async defer src="https://maps.googleapis.com/maps/api/js?key='.$this->getSettingValue('google_maps_key').'&libraries=places"></script>';
                echo "<script>var glovoSettings = JSON.parse('".json_encode($checkoutData)."');</script>";
                echo '<script src="catalog/view/javascript/glovo/gmaps.js" type="text/javascript"></script>';
                echo '<script src="catalog/view/javascript/glovo/glovo_actions.js" type="text/javascript"></script>';
            } else {
                echo "<script>$('[name=\"shipping_method\"][value=\"glovo.glovo\"]').parent().parent().prev().html('');</script>";
                echo "<script>$('[name=\"shipping_method\"][value=\"glovo.glovo\"]').parent().parent().html('');</script>";
            }
        }
    }

    // ----------------------------------------

    public function handleOrderUpdateHistory($route, $data)
    {
        $this->handleGlovoOrder($this->model_checkout_order->getOrder((int) $data[0]), $data[1]);
    }

    public function addGlovoCatalogOrderData($route, &$data)
    {
        try {
            $order = OrderEntity::findByKeyValuePair('order_id', $data['order_id']);
        } catch (Exception $e) {
            return;
        }

        if (null !== $order)
        {
            $this->load->language('extension/shipping/glovo');

            $data['html_glovo_details'] = [
                $this->language->get('entry_order_info_id') => $order->glovo_order_id,
                $this->language->get('entry_order_info_cost') => $order->cost,
                $this->language->get('entry_order_info_status') => $order->status,
                $this->language->get('entry_order_info_address') => $order->address,
                $this->language->get('entry_order_info_reference') => $order->reference,
                $this->language->get('entry_order_info_city') => $order->city_code,
                $this->language->get('entry_order_info_date_delivered') => $order->date_added,
            ];

            $city = \Vexsoluciones\Module\Glovo\Model\City::loadByID($order->city_code);

            $mapData = [
                'client' => [
                    'lat' => $order->address_lat,
                    'lng' => $order->address_lng,
                    'description' => "<p><b>{$order->address}</b></p>",
                ],
                'store' => [
                    'lat' => $city->lat,
                    'lng' => $city->lng,
                    'icon' => 'image/catalog/glovo/shipping_store_icon.png',
                    'description' => sprintf(
                        "<p><b>{$city->address}</b></p><p>%s: {$city->details}</p><p>%s: <b>{$city->contactPhone}</b></p><p>%s: <b>{$city->contactPerson}</b></p>",
                        $this->language->get('entry_checkout_reference'),
                        $this->language->get('entry_contact_phone'),
                        $this->language->get('entry_contact_person')
                    ),
                ],
                'track_url' => "https://api.glovoapp.com/b2b/orders/{$order->glovo_order_id}/tracking",
                'authorization' => base64_encode($this->getSettingValue('api_key') . ':' . $this->getSettingValue('api_secret')),
                'order_id' => $order->order_id
            ];


            if ($order->status !== OrderEntity::STATUS_PENDING)
            {
                $client = new GlovoClient($this->getSettingValue('api_key'), $this->getSettingValue('api_secret'));
                $track = $client->trackByOrderId($order->glovo_order_id);
                $courier = $client->getCourierContact($order->glovo_order_id);

                $mapData['track'] = [
                    'lat' => $track->lat,
                    'lng' => $track->lon,
                    'icon' => 'image/catalog/shipping_glovo_icon.png',
                    'description' => sprintf("<h3>{$courier->courierName}</h3><p>%s: <b>{$courier->phone}</b></p>", $this->language->get('entry_checkout_reference')),
                ];
            }

            $js = '<div id="glovo_tracking_map" style="width: 100% !important; height: 450px;"></div>';
            $js .= '<script async defer src="https://maps.googleapis.com/maps/api/js?key='.$this->getSettingValue('google_maps_key').'&libraries=places"></script>';
            $js .= '<script src="catalog/view/javascript/glovo/gmaps.js" type="text/javascript"></script>';
            $js .= '<script src="catalog/view/javascript/glovo/glovo_tracking.js" type="text/javascript"></script>';
            $js .= "<script>waitToLoadAssets(JSON.parse('". json_encode($mapData) ."'));</script>";

            $data['html_glovo_details'][$this->language->get('entry_order_info_date_tracking')] = $js;
        }

    }

    /**
     * @deprecated
     * @param $route
     * @param $data
     * @throws Exception
     */
    public function addDataToCheckoutStage($route, &$data)
    {
    }

    // ************************************************************************

    public function handleGlovoOrder($order, $newStatus)
    {
        if ($order['shipping_code'] != 'glovo.glovo') return false;

        $glovoOrder = $this->findGlovoOrderByOrderId($order['order_id']);
        if ($glovoOrder === false && isset($this->session->data['glovo_customer_settings']))
            $this->createOrder($order);

        if (null === $glovoOrder)
            $glovoOrder = $this->findGlovoOrderByOrderId($order['order_id']);

        // ---------------
        if ($newStatus == $this->getSettingValue('order_status_id') && $glovoOrder['status'] != 'complete')
        {
            $this->load->model(ShippingGlovoConstants::ADMIN_MAIN_MODEL_ROUTE);
            try {
                $response = $this->model_extension_shipping_glovo->createOrder(
                    StoreConfig::loadByCode($glovoOrder['city_code']),
                    (string)$glovoOrder['address_lat'],
                    (string)$glovoOrder['address_lng'],
                    (string)$glovoOrder['address'],
                    $glovoOrder['description'],
                    0
                );

//                $response = $this->model_extension_shipping_glovo->createOrderOld(
//                    $glovoOrder['city_code'],
//                    (string)$glovoOrder['address_lat'],
//                    (string)$glovoOrder['address_lng'],
//                    (string)$glovoOrder['address'],
//                    $glovoOrder['description'],
//                    1557871200
////                    $glovoOrder['date_schedule']
//                );
            } catch (Exception $e) {
                \Codeigniter\Service::db()
                    ->set('last_error', "(".date('Y-m-d h:i a').") Error: {$e->getMessage()}")
                    ->where('order_id', $order['order_id'])
                    ->update('glovo_order');
                $this->logger->write("Error while creating order #{$order['order_id']}: {$e->getMessage()}");
                return false;
            }

            \Codeigniter\Service::db()
                ->set('glovo_order_id', $response->id)
                ->set('status', 'complete')
                ->set('date_delivered', time())
                ->set('last_error', '')
                ->where('order_id', $order['order_id'])
                ->update('glovo_order');
        }

        return true;
    }

    private function createOrder($order)
    {
        \Codeigniter\Service::db()
            ->set('order_id', $order['order_id'])
            ->set('date_added', $order['date_added'])
            ->set('date_schedule', $this->session->data['glovo_customer_settings']['scheduled_time'])
            ->set('date_delivered', 0)
            ->set('address', $this->session->data['glovo_customer_settings']['address'])
            ->set('address_lat', $this->session->data['glovo_customer_settings']['lat'])
            ->set('address_lng', $this->session->data['glovo_customer_settings']['lng'])
            ->set('city_code', $this->session->data['glovo_customer_settings']['city'])
            ->set('cost', (float) $this->session->data['glovo_customer_settings']['amount'])
            ->set('description', $this->createOrderDescription($order))
            ->set('last_error', '')
            ->set('status', 'pending')
            ->set('glovo_status', 'pending')
            ->insert('glovo_order');
    }

    private function findGlovoOrderByOrderId($orderId)
    {
        $sql = Service::queryBuilder($this->db)
            ->select('*')
            ->from('glovo_order')
            ->where('order_id', $orderId);
        $query = $this->db->query($sql->output());

        if ($query->num_rows)
        {
            return $query->row;
        }

        return false;
    }

    private function createOrderDescription($order)
    {
        $pp = [];
        $products = $this->model_checkout_order->getOrderProducts($order['order_id']);

        foreach ($products as $product)
            $pp[] = "{$product['name']} x {$product['quantity']}";

        return implode(', ', $pp);
    }

    public function getSettingValue($key)
    {
        return $this->model_setting_setting->getSettingValue("shipping_glovo_{$key}");
    }

    /**
     * @deprecated
     * @return array
     */
    private function getHeaders()
    {
        return array(
            'Authorization: Basic ' . base64_encode($this->getSettingValue('api_key') . ':' . $this->getSettingValue('api_secret')),
            'Content-Type: application/json'
        );
    }
}
