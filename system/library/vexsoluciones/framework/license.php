<?php
namespace Vexsoluciones\Framework;

use Vexsoluciones\Module\ShippingGlovoConstants;

/**
 * Class License - Only for Opencart 3.x
 * @package Vexsoluciones\Framework
 * @property \ModelSettingSetting $settings
 * @property \Log $logger
 */
class License
{
    const SERVER_URL = 'https://www.pasarelasdepagos.com/';
    const SECRET_KEY = '587423b988e403.69821411';
    const PRETTY_SERVER_URL = '<a href="'.self::SERVER_URL.'" target="_blank"><b>'.self::SERVER_URL.'</b></a>';

    const FIELD_LICENSE = 'shipping_glovo_license';
    const FIELD_LICENSE_ACTIVATED = 'shipping_glovo_license_activated';
    const FIELD_LICENSE_DATE = 'shipping_glovo_license_date';
    const FIELD_STATUS = 'shipping_glovo_status';

    public $logger;

    public function __construct()
    {
        $this->logger = new \Log('vex_license.log');
    }

    public function check($licenseCode)
    {
        if ($licenseCode == '')
            throw new \Exception('Please enter a valid license');

        return $this->runValidation($licenseCode);
    }

    private function runValidation($licenseCode)
    {
        $postFields = [
            'slm_action' => 'slm_check',
            'secret_key' => static::SECRET_KEY,
            'license_key' => $licenseCode
        ];

        return $this->performPostRequest($postFields);
    }

    public function registry($licenseCode, $itemReference)
    {
        $postFields = [
            'slm_action' => 'slm_activate',
            'secret_key' => static::SECRET_KEY,
            'license_key' => $licenseCode,
            'registered_domain' => $_SERVER['SERVER_NAME'],
            'item_reference' => urlencode($itemReference)
        ];

        return $this->performPostRequest($postFields);
    }

    /**
     * @param array $data
     * @return bool|mixed|string
     * @throws \Exception
     */
    private function performPostRequest(array $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, static::SERVER_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $content = curl_exec($ch);

        if (curl_error($ch))
        {
            $error = curl_error($ch);
            curl_close($ch);

            throw new \Exception($error);
        }
        curl_close($ch);

        $content = json_decode($content);

        if ($content->result == 'error')
        {
            $this->logger->write('Error: ' . json_encode($content->message) . '| data requested: ' . json_encode($data));
            throw new \Exception($content->message);
        }

        return $content;
    }
}