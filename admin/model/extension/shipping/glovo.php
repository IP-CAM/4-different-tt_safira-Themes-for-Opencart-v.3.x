<?php

use Vexsoluciones\Framework\License as VexLicense;
use \Vexsoluciones\Module\ShippingGlovoConstants as ModuleConstants;

class ModelExtensionShippingGlovo extends Model
{
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('setting/setting');
        $this->load->language('extension/shipping/glovo');
    }

    public function getDayHourTime($rawData)
    {
        $days = [
            $this->language->get('entry_day_0'),
            $this->language->get('entry_day_1'),
            $this->language->get('entry_day_2'),
            $this->language->get('entry_day_3'),
            $this->language->get('entry_day_4'),
            $this->language->get('entry_day_5'),
            $this->language->get('entry_day_6')
        ];
        $response = [];
        $cityHour = explode(',', $rawData);

        for ($i = 1; $i <= 7; $i++)
        {
            $response[] = [
                'name' => $days[$i-1],
                'status' => $cityHour[($i*3)-3],
                'hour_start' => $cityHour[($i*3)-2],
                'hour_end' => $cityHour[($i*3)-1]
            ];
        }

        return $response;
    }

    public function getHolidays($rawData)
    {
        $response = [];
        $cityHour = explode(',', $rawData);

        for ($i = 1; $i <= (sizeof($cityHour)/3); $i++)
        {
            $response[] = [
                'text' => $cityHour[($i*3)-3],
                'day' => $cityHour[($i*3)-2],
                'month' => $cityHour[($i*3)-1]
            ];
        }

        return $response;
    }

    public function getAITACodes()
    {
        $db = \Codeigniter\Service::db();

        return $db->get('vex_glovo_working_areas')->result();
    }

    public function getSettingValue($key)
    {
        return $this->model_setting_setting->getSettingValue(ModuleConstants::SETTINGS_CODE .'_'. $key);
    }

    public function editSettingValue($key, $value)
    {
        $this->model_setting_setting->editSettingValue(ModuleConstants::SETTINGS_CODE, ModuleConstants::SETTINGS_CODE .'_'. $key, $value);
    }

    public function isValidSubscription($licenseCode)
    {
        $lastTime = $this->getSettingValue('license_date');
        $isActivated = $this->getSettingValue('license_activated');

        if (
            $lastTime == ''
            || $licenseCode == ''
        )
        {
            throw new Exception('Invalid License');
        }

        if ($isActivated == '0' || (int)$lastTime + 86400 < time())
        {
            return $this->forceValidation($licenseCode);
        }

        return true;
    }

    public function forceValidation($licenseCode)
    {
        $isActivated = $this->getSettingValue('license_activated');
        $license = new VexLicense();

        if ($isActivated == '0' && $licenseCode != '')
        {
            return $this->doActivate($license, $licenseCode);
        }

        try{
            $response = $license->check($licenseCode);
        } catch (Exception $e) {
            $license->logger->write("Error while license validation: {$e->getMessage()}");
            throw new Exception($e->getMessage());
        }

        $license->logger->write("result: " . json_encode($response));

        if ($response->result != 'success')
        {
            $this->editSettingValue('license', $licenseCode);
            $this->editSettingValue('license_date', time());
            $this->editSettingValue('status', 0);
            throw new Exception('Invalid license');
        }

        if (
            $response->status == 'expired'
            || $response->status == 'blocked'
            || $response->status == 'pending'
        )
        {
            $this->editSettingValue('license', $licenseCode);
            $this->editSettingValue('license_date', 0);
            $this->editSettingValue('status', 0);
            throw new Exception("License has status of <b>{$response->status}</b>, please go to " . $license::PRETTY_SERVER_URL);
        }

        $this->editSettingValue('license', $licenseCode);
        $this->editSettingValue('license_date', time());
        $this->editSettingValue('status', 1);
        return true;
    }

    public function doActivate(VexLicense $license, $licenseCode)
    {
        $license->logger->write('activando licencia');

        try {
            $license->registry($licenseCode, ModuleConstants::STORE_ITEM_REFERENCE);
        } catch (Exception $e) {
            $this->editSettingValue('license_activated', 0);
            $this->editSettingValue('status', 0);
            throw new Exception($e->getMessage());
        }

        $this->editSettingValue('license_activated', 1);
        $this->editSettingValue('license', $licenseCode);
        $this->editSettingValue('license_date', time());
        $this->editSettingValue('status', 1);

        return true;
    }
}