<?php

use Vexsoluciones\Module\Glovo\TimestampWorking;
use Vexsoluciones\Module\ShippingGlovoConstants;
use Vexsoluciones\Module\Glovo\Model\Address as GlovoAddress;
use Vexsoluciones\Module\Glovo\Model\City as CityConfig;

/**
 * @property ModelExtensionShippingGlovo $model_extension_shipping_glovo
 * @property ModelExtensionShippingGlovo $myModel
 */
class ControllerExtensionShippingGlovo extends Controller
{
    private $myModel = null;

	public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model(ShippingGlovoConstants::ADMIN_MAIN_MODEL_ROUTE);
        $this->myModel =& $this->model_extension_shipping_glovo;
        date_default_timezone_set('America/Lima');
    }

    private function outputJSON(array $data)
    {
//        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function test()
    {
        $store = CityConfig::loadByCode('LIM');

        echo '<pre>';
        var_dump($store->getWorkingTimesFrontendTest($this->language, 0));
        echo '</pre>';
    }

    public function estimate_order()
    {
        $post = $this->request->post;
        $requiredFields = [
            'lat', 'lng', 'address', 'reference', 'contact_person', 'contact_phone',
            'city', 'scheduled_time', 'address_id', 'riders', 'force_error'
        ];
        foreach ($requiredFields as $field)
        {
            if (!isset($post[$field]) || $post[$field] == '')
            {
                return $this->outputJSON(['status' => false, 'message' => sprintf('Please fill all fields (%s).', $field)]);
            }
        }

        if ($post['force_error'] === 'true')
        {
            unset($this->session->data['glovo_customer_settings']);
            return $this->outputJSON(['status' => false, 'message' => 'Esta zona no está disponible para envío.']);
        }

        $this->load->model('account/customer');
        $sessionData = $this->session->data;
        $customer = null;

        if (isset($sessionData['customer_id']))
        {
            $customer = $this->model_account_customer->getCustomer($sessionData['customer_id']);
        }

//        $post['scheduled_time'] = (TimestampWorking::createFromLocalTimeZone())->fromTimestamp($post['scheduled_time']);

        if ($post['scheduled_time'] < time()) $post['scheduled_time'] = 0;

        $destination = new GlovoAddress();
        $destination->setType(GlovoAddress::TYPE_DELIVERY)
            ->setLat($post['lat'])
            ->setLng($post['lng'])
            ->setLabel("{$sessionData['shipping_address']['address_1']} {$sessionData['shipping_address']['address_2']}")
            /*->setDetails($post['reference'])
            ->setContactPerson($post['contact_person'])
            ->setContactPhone($post['contact_phone'])*/
            ->setDetails('-')
            ->setContactPerson("{$sessionData['shipping_address']['firstname']} {$sessionData['shipping_address']['lastname']}")
            ->setContactPhone(null !== $customer ? $customer['telephone'] : '-');

        try {
            $city = CityConfig::loadByID($post['city']);
            /** @var \Vexsoluciones\Module\Glovo\Helper\EstimateOrder $total */
            $total = $this->myModel->estimateOrder(0, $city, $destination, $post['scheduled_time'], $post['riders']);

            $total->calculateFreeShippingCost($this->cart, $this->myModel->getSettingValue('free_shipping_cost'));
            $total->calculate($this->currency, $this->session, $this->language);

        } catch (Exception $e) {
            unset($this->session->data['glovo_customer_settings']);
            return $this->outputJSON(['status' => false, 'message' => $this->language->get('error_checkout_address_select')]);
        }

//        $amountFixedForOc = $this->currency->convert($total->amount, $this->session->data['currency'], $this->currency->getId($total->currency));
        $this->session->data['shipping_methods']['glovo']['quote']['glovo']['cost'] = $total->amountInUSD;
//        $this->session->data['shipping_methods']['glovo']['quote']['glovo']['cost'] = (string) ($amountFixedForOc / 100);

        $data = [
            'a_test' => $total,
            'status' => true,
            'lat' => (float) $post['lat'],
            'lng' => (float) $post['lng'],
            'address' => $post['address'],
            'amount' => (string) $total->baseAmount,
            'currency' => $total->currency,
            'price' => $total->amountFormatted,
            'city' => $post['city'],
            'scheduled_time' => $post['scheduled_time'],
            'session' => $this->session->data['shipping_methods']['glovo']['quote']['glovo'],
        ];
        $this->session->data['glovo_customer_settings'] = $data;
        //$this->myModel->updateLatLngAddress($post['address_id'], $data['lat'], $data['lng'], $data['address']);

        return $this->outputJSON($data);
    }
}