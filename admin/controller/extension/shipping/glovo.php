<?php

use Vexsoluciones\Module\ShippingGlovoConstants as ModuleConstants;
use Vexsoluciones\Module\Glovo\Client as GlovoClient;
use Vexsoluciones\Module\Glovo\Model\WorkingArea;

/**
 * @property ModelExtensionShippingGlovo model_extension_shipping_glovo
 */
class ControllerExtensionShippingGlovo extends Vexsoluciones\AdminController
{
    public $_code = ModuleConstants::SETTINGS_CODE;

    public function __construct($registry)
    {
        parent::__construct($registry);
        self::$constants = ModuleConstants::get_instance();
        $this->init();
    }

    public function index()
    {
        $this->load->model('tool/image');
        $data = [];

        if (!$this->isValidSubscription($data))
        {
            return false;
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->hasPermission() /*&& isset($this->request->post['shipping_glovo_api_key'])*/)
        {
            $this->request->post['shipping_glovo_cost_shipping_price'] = $this->request->post['shipping_glovo_cost_shipping_type'] == '3'
                ? $this->request->post['shipping_glovo_cost_shipping_price']
                : 0;

            if ($this->request->post['shipping_glovo_google_store_icon'] == '')
                unset($this->request->post['shipping_glovo_google_store_icon']);


            $this->saveSettingValues($this->request->post);
//            $this->model_setting_setting->editSetting($this->settingCode, $this->request->post);
            $data['alert_messages'][] = $this->addAlertMessage('success', $this->language->get('text_success'));

            // ----------------------------------------------------------------

            $this->load->model('setting/setting');
            /*if (\Codeigniter\Service::db()->count_all_results('vex_glovo_working_areas') <= 0)
            {
                $client = new GlovoClient($this->getSettingValue('api_key'), $this->getSettingValue('api_secret'));

                foreach ($client->workingAreas()->workingAreas as $area)
                {
                    WorkingArea::createNewAndSave((object) [
                        'code' => $area->code,
                        'polygons' => $area->polygons,
                        'workingTimes' => $area->workingTimes
                    ]);
                }
            }*/
        }
        $this->loadBreadcrumbs($data);

        $this->document->addScript('catalog/view/javascript/glovo/moment.js');
        $this->document->addScript('catalog/view/javascript/glovo/jquery.timepicker.min.js');
        $this->document->addStyle('catalog/view/javascript/glovo/jquery.timepicker.min.css');

        $this->showConfigPage($data);
    }

    private function showConfigPage($data)
    {
        $this->load->model('localisation/tax_class');
        $this->load->model('localisation/geo_zone');
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');
        $this->load->model('localisation/currency');
        $this->load->model('catalog/attribute');

        $this->addConfigValueForView($data, ['status', 'test', 'tax_class_id', 'geo_zone_id', 'order_status_id', 'country_id', 'zone_id', 'city_hour', 'currency_code', 'google_map_zoom', 'cost_shipping_type', 'cost_shipping_price', 'google_store_icon', 'preparation_time_attrib', 'preparation_time_additional', 'free_shipping_cost']);
        $this->setAdminFieldsForView($data, ['api_key', 'api_secret', 'google_maps_key']);
        $this->setAdminHiddenFieldsForView($data, ['sort_order']);

        $data['image_thumb'] = $this->model_tool_image->resize($data['shipping_glovo_google_store_icon'], 40, 40);
        $data['image_placeholder'] = $data['shipping_glovo_google_store_icon'];

        $data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $data['countries'] = $this->model_localisation_country->getCountries();
        $data['states'] = $this->model_localisation_zone->getZonesByCountryId($data['shipping_glovo_country_id']);
        $data['user_token'] = $this->session->data['user_token'];
        $data['currencies'] = $this->model_localisation_currency->getCurrencies();
        $data['product_attributes'] = $this->model_catalog_attribute->getAttributes();
        $data['product_attributes'][] = ['attribute_id' => 0, 'name' => '-- None --'];
        $data['url_product_attribute'] = $this->url->link('catalog/attribute', 'user_token='.$this->session->data['user_token'], true);
        $data['currency_code'] = $this->config->get('config_currency');

        $data['thumb'] = $this->model_tool_image->resize('no_image.png', 240, 100);
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 240, 100);
        $data['stores_configuration_url'] = $this->url->link('extension/module/glovo/store_list', 'user_token='.$this->session->data['user_token'], true);
        $data['stores_order_list_url'] = $this->url->link('extension/module/glovo/order_list', 'user_token='.$this->session->data['user_token'], true);

        $this->response->setOutput($this->load->view('extension/shipping/glovo', $data));
    }

    protected function loadBreadcrumbs(&$data)
    {
        $userToken = $this->session->data['user_token'];

        $data['breadcrumbs'] = [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', "user_token={$userToken}", true)
            ],
            [
                'text' => $this->language->get('text_extension'),
                'href' => $this->url->link('marketplace/extension', "user_token={$userToken}&type=shipping", true)
            ],
            [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/shipping/glovo', "user_token={$userToken}", true)
            ]
        ];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['action'] = $this->url->link('extension/shipping/glovo', "user_token={$userToken}", true);
        $data['cancel'] = $this->url->link('marketplace/extension', "user_token={$userToken}&type=shipping", true);
        $data['error_warning'] = '';
        $data['module_version'] = ModuleConstants::version();

        if (isset($this->error['warning']))
            $data['error_warning'] = $this->error['warning'];
    }

    public function get_zones()
    {
        $this->load->model('localisation/country');

        $json = [];
        $country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

        if ($country_info) {
            $this->load->model('localisation/zone');

            $json = array(
                'country_id'        => $country_info['country_id'],
                'name'              => $country_info['name'],
                'iso_code_2'        => $country_info['iso_code_2'],
                'iso_code_3'        => $country_info['iso_code_3'],
                'address_format'    => $country_info['address_format'],
                'postcode_required' => $country_info['postcode_required'],
                'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
                'status'            => $country_info['status']
            );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

    }
}