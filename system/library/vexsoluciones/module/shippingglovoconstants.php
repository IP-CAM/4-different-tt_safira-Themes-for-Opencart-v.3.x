<?php
namespace Vexsoluciones\Module;

class ShippingGlovoConstants extends BaseConstants
{
    const STORE_ITEM_REFERENCE = 'Glovo - Opencart 3';
    const MODULE_VERSION = '1.0.10';

    const ADMIN_MAIN_MODEL_ROUTE = 'extension/shipping/glovo';
    const ADMIN_MAIN_MODEL_PREFIX = 'model_extension_shipping_glovo';

    const CATALOG_MAIN_MODEL_ROUTE = 'extension/shipping/glovo';
    const CATALOG_MAIN_MODEL_PREFIX = 'model_extension_shipping_glovo';

    const SETTINGS_CODE = 'shipping_glovo';
    const PREFIX_CODE = 'glovo';

    const SETTINGS_FIELDS = [
        'license' => '',
        'license_activated' => '1',
        'license_date' => '',
        'status' => 1,
        'test' => 1,
        'api_key' => '',
        'api_secret' => '',
        'sort_order' => '0',
        'order_automatic_send' => '0',
        'order_status_id' => '5',

        'tax_class_id' => '',
        'geo_zone_id' => '',
        'currency_code' => 'PEN',

        'cost_shipping_type' => '1',
        'cost_shipping_price' => '0',
        'free_shipping_cost' => '0',

        'working_areas_last_update' => '0',
        'google_maps_key' => '',
        'google_map_zoom' => '16',
        'google_store_icon' => 'catalog/glovo/shipping_store_icon.png',

        'preparation_time_status' => 1,
        'preparation_time_attrib' => 0,
        'preparation_time_additional' => 0,
    ];

    // ------------------------------------------------------------------------

    const LOG_FILE = 'glovo.log';
}