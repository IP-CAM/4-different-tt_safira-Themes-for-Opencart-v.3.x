<?php

/**
 * @property Language $language
 * @property ModelSettingSetting $model_setting_setting
 * @property Registry $registry
 */
class VexRegistryLibrary
{
    protected $registry;
    protected $code;
    protected $error = [];
    protected $configData = [];

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    public function init($code, $error = [])
    {
        $this->code = $code;
        $this->error = $error;

        $this->configData = $this->model_setting_setting->getSetting($this->code);
    }

    public function getConfig($key)
    {
        return $this->configData["{$this->code}_{$key}"];
    }
}