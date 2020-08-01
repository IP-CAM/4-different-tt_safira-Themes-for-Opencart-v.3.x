<?php

use Vexsoluciones\Framework\Service;
use Vexsoluciones\Module\ShippingGlovoConstants as ModuleConstants;

class ControllerExtensionModuleGlovo extends Controller
{
    use \Vexsoluciones\Framework\Config\SettingsConfig;

    private $events = [
        ['glovo_admin_menu', 'admin/view/common/column_left/before', 'injectAdminMenuItem'],
        ['glovo_create_order', 'catalog/model/checkout/order/addOrderHistory/before', 'handleOrderUpdateHistory'],
        ['glovo_add_checkout_data', 'catalog/view/checkout/shipping_method/before', 'addDataToCheckoutStage'],  //---

        ['glovo_checkout_header_js', 'catalog/controller/checkout/checkout/before', 'addRequiredJsToHeader'],
        ['glovo_checkout_shipping_js', 'catalog/controller/checkout/shipping_method/before', 'addJsInCheckoutShippingStep'],    //---

        ['glovo_catalog_order_data', 'catalog/view/account/order_info/before', 'addGlovoCatalogOrderData'],    //---
    ];

    public function install()
    {
        $this->load->model('setting/event');
        foreach ($this->events as $event)
            $this->model_setting_event->addEvent($event[0], $event[1], "extension/module/glovo/{$event[2]}");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "glovo_order` (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `order_id` INT(11) NOT NULL,
			  `glovo_order_id` VARCHAR(200) NOT NULL,
			  `status` CHAR(15) NOT NULL,
			  `glovo_status` CHAR(15) NOT NULL,
			  `address` VARCHAR(100) NOT NULL,
			  `address_lat` CHAR(15) NOT NULL,
			  `address_lng` CHAR(15) NOT NULL,
			  `cost` DECIMAL( 10, 2 ) NOT NULL,
			  `city_code` CHAR(5) NOT NULL,
			  `date_added` DATETIME NOT NULL,
			  `date_schedule` INT(11) NOT NULL,
			  `date_delivered` INT(11) NOT NULL,
			  `description` LONGTEXT NOT NULL,
			  `last_error` LONGTEXT NOT NULL,
			  `reference` LONGTEXT NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "vex_custom_address` (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `address_id` INT(11) NOT NULL,
			  `lat` LONGTEXT NOT NULL,
			  `lng` LONGTEXT NOT NULL,
			  `address` VARCHAR(150) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "vex_glovo_city_config`;");
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "vex_glovo_city_config` (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `country_id` INT(11) NOT NULL,
			  `region_id` INT(11) NOT NULL,
			  `currency_id` CHAR(5) NOT NULL DEFAULT '',
			  `code` CHAR(5) NOT NULL DEFAULT '',
			  `lat` CHAR(15) NOT NULL DEFAULT '',
			  `lng` CHAR(15) NOT NULL DEFAULT '',
			  `radius` CHAR(15) NOT NULL DEFAULT '',
			  `max_orders` INT(11) NOT NULL DEFAULT 10,
			  `zoom` CHAR(15) NOT NULL DEFAULT '',
			  `address` VARCHAR(250) NOT NULL DEFAULT '',
			  `address2` VARCHAR(250) NOT NULL DEFAULT '',
			  `details` VARCHAR(250) NOT NULL DEFAULT '',
			  `postal_code` VARCHAR(250) NOT NULL DEFAULT '',
			  `contact_person` VARCHAR(250) NOT NULL DEFAULT '',
			  `contact_phone` VARCHAR(50) NOT NULL DEFAULT '',
			  `active` CHAR(2) NOT NULL DEFAULT 0,
			  `working_times` LONGTEXT NOT NULL,
			  `holidays` LONGTEXT NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "vex_glovo_working_areas` (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `code` CHAR(5) NOT NULL,
			  `polygons` LONGTEXT NOT NULL,
			  `working_times` LONGTEXT NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
    }

    public function uninstall()
    {
        $this->load->model('setting/event');
        foreach ($this->events as $event)
            $this->model_setting_event->deleteEventByCode($event[0]);
    }

    public function injectAdminMenuItem($route, &$data)
    {
        $this->load->language('extension/shipping/glovo');

        $data['menus'][4]['children'][] = [
            'name' => 'TuSuper - Delivery',
            'href' => '',
            'children' => [
                [
                    'name' => $this->language->get('text_view_orders'),
                    'href' => $this->url->link('extension/module/glovo/order_list', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => []
                ],
                [
                    'name' => $this->language->get('text_my_stores'),
                    'href' => $this->url->link('extension/module/glovo/store_list', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => []
                ],
                [
                    'name' => $this->language->get('text_configuration'),
                    'href' => $this->url->link('extension/shipping/glovo', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => []
                ]
            ]
        ];
    }

    public function order()
    {
        $this->loadLayout($data, 'Test Glovo');

        $this->response->setOutput($this->load->view('sale/order_list', $data));
    }

    public function order_list()
    {
        $userToken = $this->session->data['user_token'];
        $orders = \Vexsoluciones\Module\Glovo\ActiveRecord\Order::findAll();
        /** @var \Vexsoluciones\Module\Glovo\ActiveRecord\Order $order */
        foreach ($orders as $order)
        {
            $order->setViewLink($this->url->link('sale/order/info', "user_token={$userToken}&order_id={$order->order_id}", true));
            $order->setCancelLink($this->url->link('extension/module/glovo/cancel_order', "user_token={$userToken}&id={$order->id}", true));
            $order->setCompletedLink($this->url->link('extension/module/glovo/complete_order', "user_token={$userToken}&id={$order->id}", true));
        }

        $data['orders'] = Service\Collection::toJSON($orders);

        $this->loadLayout($data, 'TuSuper - Delivery Order List');
        $this->response->setOutput($this->load->view('extension/module/glovo_order_list', $data));
    }

    public function store_list()
    {
        $token = $this->session->data['user_token'];
        $data['stores'] = \Vexsoluciones\Module\Glovo\Model\City::findAll();
        $data['store_create_url'] = $this->url->link('extension/module/glovo/store_create', "user_token={$token}", true);

        foreach ($data['stores'] as &$store)
        {
            $store['edit_url'] = $this->url->link('extension/module/glovo/store_info', "user_token={$token}&store_id={$store['id']}", true);
            $store['toggle_url'] = $this->url->link('extension/module/glovo/store_toggle_status', "user_token={$token}&store_id={$store['id']}", true);
            $store['delete_url'] = $this->url->link('extension/module/glovo/store_delete', "user_token={$token}&store_id={$store['id']}", true);
        }

        $this->loadLayout($data, 'TuSuper - Delivery Store List');
        $this->response->setOutput($this->load->view('extension/module/glovo_store_list', $data));
    }

    public function cancel_order()
    {
        $db = \Codeigniter\Service::db();
        $order = $db->where('id', $this->request->get['id'])
            ->get('oc_glovo_order');

        if (null === $order)
        {
            $token = $this->session->data['user_token'];
            $this->response->redirect($this->url->link('extension/module/glovo/order_list', "user_token={$token}", true));
            return;
        }

        $db->update(
            'oc_glovo_order',
            ['status' => 'canceled'],
            ['id' => $this->request->get['id']]
        );

        $token = $this->session->data['user_token'];
        $this->response->redirect($this->url->link('extension/module/glovo/order_list', "user_token={$token}", true));
    }
    public function complete_order()
    {
        $db = \Codeigniter\Service::db();
        $order = $db->where('id', $this->request->get['id'])
            ->get('oc_glovo_order');

        if (null === $order)
        {
            $token = $this->session->data['user_token'];
            $this->response->redirect($this->url->link('extension/module/glovo/order_list', "user_token={$token}", true));
            return;
        }

        $db->update(
            'oc_glovo_order',
            ['status' => 'completed'],
            ['id' => $this->request->get['id']]
        );

        $token = $this->session->data['user_token'];
        $this->response->redirect($this->url->link('extension/module/glovo/order_list', "user_token={$token}", true));
    }

    public function store_info()
    {
        $this->load->language('extension/shipping/glovo');
        $city = \Vexsoluciones\Module\Glovo\Model\City::loadById($this->request->get['store_id']);

        // --------------------------------------------------------------------
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');
        $this->load->model('localisation/currency');
        $this->load->model('extension/shipping/glovo');
        $token = $this->session->data['user_token'];

        $data['url_back'] = $this->url->link('extension/module/glovo/store_list', "user_token={$token}", true);
        $data['url_save'] = $this->url->link('extension/module/glovo/store_save', "user_token={$token}", true);

        $data['user_token'] = $token;
        $data['store'] = (array) $city;
        $data['currencies'] = $this->model_localisation_currency->getCurrencies();
        $data['countries'] = $this->model_localisation_country->getCountries();
        $data['states'] = $this->model_localisation_zone->getZonesByCountryId($city->countryId);
        $data['days'] = $this->model_extension_shipping_glovo->getDayHourTime($city->workingTimes);
        $data['holidays'] = $this->model_extension_shipping_glovo->getHolidays($city->holidays);
        $data['aita_codes'] = $this->model_extension_shipping_glovo->getAITACodes();
        $data['lang_code'] = $this->language->data['code'];
        $data['schedule_hours'] = $this->getScheduleHoursAvailability($city);

        $this->loadLayout($data, $this->language->get('vex_glovo_store_config'));
        $this->response->setOutput($this->load->view('extension/module/glovo_store_info', $data));
    }

    /**
     * @param \Vexsoluciones\Module\Glovo\Model\City $city
     * @return array
     */
    private function getScheduleHoursAvailability(\Vexsoluciones\Module\Glovo\Model\City $city)
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $db = \Codeigniter\Service::db();
        $data = [];
        $timeNow = new \DateTime();
        $dateNow = \DateTime::createFromFormat('d-m-Y H:i:s', "{$timeNow->format('d-m-Y')} 00:00:00");

        for ($i = 0; $i <= 3; $i++)
        {
            $test = (new \DateTime())->setTimestamp($dateNow->getTimestamp());

            $start = (new \DateTime())->setTimestamp($test->getTimestamp());
            $end = (new \DateTime())->setTimestamp($test->getTimestamp() + 86400);
            $hours = [];

            while ($start->getTimestamp() < $end->getTimestamp())
            {
                $temp = \DateTime::createFromFormat('d-m-Y H:i:s', $start->format('d-m-Y H:i:s'));
                $endRow = $temp->modify('+60 minutes');

                $hours[] = [
                    'name' => $start->format('H:i') . ' ~ ' . $endRow->format('H:i'),
                    'timestamp' => $start->getTimestamp(),
                    'max_orders' => $city->maxOrders,
                    'current_orders' => $db
                        ->where('date_schedule >=', $start->getTimestamp())
                        ->where('date_schedule <', $endRow->getTimestamp())
                        ->where('status', 'pending')
                        ->count_all_results('oc_glovo_order'),
                ];

                $start->modify('+60 minutes');
            }

            $day = [
                'label' => $test->format('d-m-Y'),
                'hours' => $hours,
            ];
            $data[] = $day;

            $dateNow->modify('+1 day');
        }

        $result = [
            'days' => [],
            'hours' => [],
        ];

        foreach ($data as $day)
        {
            $result['days'][] = ['label' => $day['label']];

            foreach ($day['hours'] as $key => $value)
            {
                $result['hours'][$value['name']][] = [
                    'day' => $day['label'],
                    'name' => $value['name'],
                    'timestamp' => $value['timestamp'],
                    'max_orders' => $value['max_orders'],
                    'current_orders' => $value['current_orders'],
                ];
            }
        }

        return $result;
    }

    public function store_toggle_status()
    {
        $this->load->language('extension/shipping/glovo');
        $store = \Vexsoluciones\Module\Glovo\Model\City::loadById($this->request->get['store_id']);
        // --------------------------------------------------------------------

        $store->active = !$store->active;
        $store->save();

        $token = $this->session->data['user_token'];
        $this->response->redirect($this->url->link('extension/module/glovo/store_list', "user_token={$token}", true));
    }

    public function store_create()
    {
        $db = \Codeigniter\Service::db();

        $store = $db->get_where('vex_glovo_city_config', ['country_id' => 0, 'active' => 0])->row();

        if (null === $store)
        {
            $db->insert('vex_glovo_city_config', [
                'id' => null,
                'country_id' => 0,
                'region_id' => 0,
                'currency_id' => 0,
                'code' => '',
                'lat' => '',
                'lng' => '',
                'radius' => '5000',
                'max_orders' => '10',
                'zoom' => '16',
                'address' => '',
                'address2' => '',
                'details' => '',
                'postal_code' => '',
                'contact_person' => '',
                'contact_phone' => '',
                'active' => 0,
                'working_times' => '1,09:00,20:00,1,09:00,20:00,1,09:00,20:00,1,09:00,20:00,1,09:00,20:00,1,09:00,20:00,1,09:00,20:00',
                'holidays' => '',
            ]);

            $storeId = $db->insert_id();
        } else {
            $storeId = $store->id;
        }

        $token = $this->session->data['user_token'];
        $this->response->redirect($this->url->link('extension/module/glovo/store_info', "user_token={$token}&store_id={$storeId}", true));
    }

    public function store_delete()
    {
        $this->load->language('extension/shipping/glovo');
        $store = \Vexsoluciones\Module\Glovo\Model\City::loadById($this->request->get['store_id']);
        // --------------------------------------------------------------------

        $db = \Codeigniter\Service::db();
        $db->delete('vex_glovo_city_config', ['id' => $this->request->get['store_id']]);

        $token = $this->session->data['user_token'];
        $this->response->redirect($this->url->link('extension/module/glovo/store_list', "user_token={$token}", true));
    }

    public function store_save()
    {
        if (!isset($this->request->post['store_id']) || $this->request->post['store_id'] == 0)
        {
            $this->request->post['store_id'] = null;
        }

        $city = \Vexsoluciones\Module\Glovo\Model\City::createFromPost($this->request->post);
        $city->save();

        $token = $this->session->data['user_token'];
        $this->response->redirect($this->url->link('extension/module/glovo/store_list', "user_token={$token}", true));
    }

    public function import_demo()
    {
        $db = \Codeigniter\Service::db();
        $store = $db->get_where('vex_glovo_city_config', ['country_id' => 0, 'active' => 0])->row();
        $db->insert('vex_glovo_city_config', [
            'id' => null,
            'country_id' => 167,
            'region_id' => 2537,
            'currency_id' => 'USD',
            'code' => '',
            'lat' => '-14.0638821',
            'lng' => '-75.7300713',
            'radius' => '600',
            'max_orders' => '10',
            'zoom' => '16',
            'address' => 'Av. Municipalidad 209, Ica 11000',
            'address2' => '',
            'details' => 'W7PC+F4 Ica',
            'postal_code' => '11005',
            'contact_person' => 'Reniec',
            'contact_phone' => '+51955914562',
            'active' => 1,
            'working_times' => '1,00:00,23:30,1,00:00,23:30,1,00:00,23:30,1,00:00,23:30,1,00:00,23:30,1,00:00,23:30,1,00:00,23:30',
            'holidays' => '',
        ]);
        $db->insert('vex_glovo_city_config', [
            'id' => null,
            'country_id' => 10,
            'region_id' => 173,
            'currency_id' => 'USD',
            'code' => '',
            'lat' => '-31.5351074',
            'lng' => '-68.5385941',
            'radius' => '5000',
            'max_orders' => '10',
            'zoom' => '16',
            'address' => 'Lateral de Circunvalacion Norte',
            'address2' => '',
            'details' => '-',
            'postal_code' => '5400',
            'contact_person' => 'TU SUPER',
            'contact_phone' => '+99 9999999',
            'active' => 1,
            'working_times' => '1,00:00,23:30,1,00:00,23:30,1,00:00,23:30,1,00:00,23:30,1,00:00,23:30,1,00:00,23:30,1,00:00,23:30',
            'holidays' => '',
        ]);

        $token = $this->session->data['user_token'];
        $this->response->redirect($this->url->link('extension/module/glovo/store_list', "user_token={$token}", true));
    }

    private function loadLayout(&$data, $title)
    {
        $this->document->setTitle($title);
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['module_version'] = ModuleConstants::version();
        $data['heading_title'] = $title;
    }
}
