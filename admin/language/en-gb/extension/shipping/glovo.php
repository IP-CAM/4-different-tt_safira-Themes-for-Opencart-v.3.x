<?php
$i18n = [
    'vex_glovo_store_config' => '[LLAMA YA] Delivery - Store Config',

    'text_view_orders' => 'View Orders',
    'text_my_stores' => 'My Stores',
    'text_configuration' => 'Configuration',

    'text_create_new_store' => 'Create New Store',
    'text_new_store' => 'New Store',

    'text_order' => 'Order',
    'text_glovo' => 'LLAMA YA',
    'text_status' => 'Status',
    'text_cost' => 'Cost',
    'text_ship_to' => 'Ship to',
    'text_city' => 'City',
    'text_aita_code' => 'IATA Code',
    'text_created_date' => 'Creation Date',
    'text_date_delivered' => 'Delivered Date',

    'text_country' => 'Country',
    'text_location' => 'Location',
    'text_contact_person' => 'Contact Person',
    'text_contact_phone' => 'Contact Phone',
    'text_active' => 'Active',
    'text_toggle_status' => 'Toggle Status',
    'text_delete_store' => 'Remove',
    'text_edit' => 'Edit',
    'text_action' => 'Action',
    'text_currency' => 'Currency',

    'text_enable' => 'Enable',
    'text_delivery_addresses' => 'Delivery addresses',
    'text_store_address' => 'Store Address',
    'text_address_2' => 'Address 2',
    'text_reference' => 'Reference',
    'text_postal_code' => 'Postal Code',
    'text_find_lat_lng' => 'View Lat and Long in this url: <a href="https://www.latlong.net/">https://www.latlong.net/</a>',
    'text_find_aita_code' => 'Find your AITA Code with this url: <a href="https://www.world-airport-codes.com/search/">https://www.world-airport-codes.com/search/</a>',
    'text_working_hours' => 'Service Hours',
    'text_holidays' => 'Holidays',
    'text_city_geo_lat' => 'Latitude',
    'text_city_geo_lng' => 'Longitude',

    'text_module_configuration' => 'Module Configuration',
    'text_glovo_configuration' => 'Glovo Configuration',
    'text_find_glovo_keys' => 'Find your glovo credentials with this url: <a href="https://business.glovoapp.com/dashboard/profile" target="_blank">https://business.glovoapp.com/dashboard/profile</a>',
    'text_cost_shipping' => 'Cost of Shipping',
    'text_status_configuration' => 'Status Configuration',
    'text_gmaps_configuration' => 'Google Maps Configuration',

    'text_preparation_time_configuration' => 'Preparation Time Configuration',
    'text_preparation_time_help_1' => 'Is the time it takes to make the product in <b>minutes</b>.',
    'text_preparation_time_help_url' => 'You can create a new attribute here',
    'text_preparation_time_attribute' => 'Product Attribute',
    'text_preparation_time_additional' => 'Additional Time',
    'text_preparation_time_help_2' => 'If you do not want to use preparation time functionality, select "-- None --" option for 0 minutes.',

    'text_free' => 'Free',
    'text_pulled_automatic' => 'Pulled automatically from Glovo Api',
    'text_based_price' => 'Based on a fixed price',
    'text_order_status' => 'Order Status',
    'text_order_change_status' => 'Change Status',
    'text_order_new_status' => 'New Status',
    'text_order_help_1' => 'Create a Glovo order when the shop order changes to any of these statuses.',
    'text_order_help_2' => 'Enable shop order status change on Glovo order creation.',
    'text_order_help_3' => 'Change shop orders to this status when a Glovo order is created.',
    'text_map_zoom' => 'Map\'s Zoom',
    'text_store_icon' => 'Store Icon',
    'text_gmaps_configuration_help_1' => '
        You have to create a google console account here: <a href="https://console.cloud.google.com">https://console.cloud.google.com</a><br>
        How to obtain API Key: <a href="https://developers.google.com/maps/documentation/javascript/get-api-key">https://developers.google.com/maps/documentation/javascript/get-api-key</a><br>
        And it must have the following API: Places API, Directions API, Geocoding API, Maps Javascript API
    ',
    'text_map_zoom_help' => 'The default zoom for all maps. (Default value: 16)',
    'text_store_icon_help' => 'The default icon of your stores for maps.',

    'entry_day_0' => 'Sunday',
    'entry_day_1' => 'Monday',
    'entry_day_2' => 'Tuesday',
    'entry_day_3' => 'Wednesday',
    'entry_day_4' => 'Thursday',
    'entry_day_5' => 'Friday',
    'entry_day_6' => 'Saturday',

    'entry_license' => 'License',
    'error_license' => 'Is important that you write a valid license that you\'ve purchased.',

    'entry_api_key' => 'Glovo Api Key',
    'entry_api_secret' => 'Glovo Api Secret',
    'entry_google_maps_key' => 'Google Maps API Key',

    'error_api_key' => 'Please write your Glovo Api Key',
    'error_api_secret' => 'Please write your Glovo Api Secret',
    'error_google_maps_key' => 'Please write your Google Maps Api Key',

    'text_holiday_1' => 'Set holidays. These days there will be no delivery service.',
    'text_holiday_2' => 'Add holiday',
//    -------------------------------------------

    'heading_title' => 'TuSuper - Delivery',
    'text_extension' => 'Extension',

    'text_success' => '!Very Good, you have modified Glovo!',
];

$_ = array_merge($_, $i18n);
