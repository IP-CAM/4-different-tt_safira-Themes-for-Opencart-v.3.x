<?php
namespace Vexsoluciones\Module;

class BaseConstants
{
    const STORE_ITEM_REFERENCE = '';
    const MODULE_VERSION = '1.0.0';

    const ADMIN_MAIN_MODEL_ROUTE = '';
    const ADMIN_MAIN_MODEL_PREFIX = '';

    const CATALOG_MAIN_MODEL_ROUTE = '';
    const CATALOG_MAIN_MODEL_PREFIX = '';

    const SETTINGS_CODE = '';
    const PREFIX_CODE = '';

    const SETTINGS_FIELDS = [
        'license' => '',
        'license_activated' => '0',
        'license_date' => '',
        'status' => 1,
        'test' => 1,
        'sort_order' => '',
    ];

    // ------------------------------------------------------------------------

    const LOG_FILE = 'main.log';

    public static function get_instance()
    {
        return new static();
    }

    public static function version()
    {
        return '<b style="font-size:11px; background:#777; color:#fff; padding: 3px;font-weight:500;">v'.static::MODULE_VERSION.'</b>';
    }

    public static function logger()
    {
        return new \Log(static::LOG_FILE);
    }
}