<?php
namespace Vexsoluciones\Framework\Config;

/**
 * Trait SettingsModel
 * @package Vexsoluciones\Framework\Config
 * @property \ModelSettingSetting model_setting_setting
 */
trait SettingsConfig
{
    protected $_settingPattern = '';

    public function getSettingValue($key)
    {
        return $this->model_setting_setting->getSettingValue(sprintf($this->_settingPattern, $key));
    }
}