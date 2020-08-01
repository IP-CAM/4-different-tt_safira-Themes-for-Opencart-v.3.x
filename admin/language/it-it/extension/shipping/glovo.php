<?php
$i18n = [
    'vex_glovo_store_config' => '[LLAMA YA] Delivery - Configurazione del Negozio',

    'text_view_orders' => 'Visualizza ordini',
    'text_my_stores' => 'I miei negozi',
    'text_configuration' => 'Configurazione',

    'text_create_new_store' => 'Crea nuovo negozio',
    'text_new_store' => 'Nuovo negozio',

    'text_order' => 'Ordine',
    'text_glovo' => 'LLAMA YA',
    'text_status' => 'Stato',
    'text_cost' => 'Costo',
    'text_ship_to' => 'Spedire a',
    'text_city' => 'Città',
    'text_aita_code' => 'Codice IATA',
    'text_created_date' => 'Data di creazione',
    'text_date_delivered' => 'Data di consegna',

    'text_country' => 'Nazione',
    'text_location' => 'Posizione',
    'text_contact_person' => 'Referente',
    'text_contact_phone' => 'Contatto telefonico',
    'text_active' => 'Attivo',
    'text_toggle_status' => 'Attiva / disattiva stato',
    'text_delete_store' => 'Rimuovere',
    'text_edit' => 'modificare',
    'text_action' => 'Azione',
    'text_currency' => 'Moneta',

    'text_enable' => 'Abilitare',
    'text_delivery_addresses' => 'Indirizzi di consegna',
    'text_store_address' => 'Indirizzo del negozio',
    'text_address_2' => 'Indirizzo 2',
    'text_reference' => 'Riferimento',
    'text_postal_code' => 'codice postale',
    'text_find_lat_lng' => 'Visualizza Lat e Long in questo URL: <a href="https://www.latlong.net/"> https://www.latlong.net/ </a>',
    'text_find_aita_code' => 'Trova il tuo codice AITA con questo URL: <a href="https://www.world-airport-codes.com/search/"> https://www.world-airport-codes.com/search/</a >',
    'text_working_hours' => 'Orari di servizio',
    'text_holidays' => 'Vacanze',
    'text_city_geo_lat' => 'Latitudine',
    'text_city_geo_lng' => 'Longitudine',

    'text_module_configuration' => 'Configurazione del modulo',
    'text_glovo_configuration' => 'Configurazione di Glovo',
    'text_find_glovo_keys' => 'Trova le tue credenziali glovo con questo URL: <a href="https://business.glovoapp.com/dashboard/profile" target="_blank"> https://business.glovoapp.com/dashboard/profile </a>',
    'text_cost_shipping' => 'Spese di spedizione',
    'text_status_configuration' => 'Configurazione dello stato',
    'text_gmaps_configuration' => 'Configurazione di Google Maps',

    'text_preparation_time_configuration' => 'Preparazione del tempo di preparazione',
    'text_preparation_time_help_1' => 'È il tempo necessario per realizzare il prodotto in <b> minuti </b>.',
    'text_preparation_time_help_url' => 'Puoi creare un nuovo attributo qui',
    'text_preparation_time_attribute' => 'Attributo del prodotto',
    'text_preparation_time_help_2' => 'Se non si desidera utilizzare la funzionalità del tempo di preparazione, selezionare l\'opzione "- Nessuna -" per 0 minuti.',

    'text_free' => 'Gratuito',
    'text_pulled_automatic' => 'Estratto automaticamente da Glovo Api',
    'text_based_price' => 'Basato su un prezzo fisso',
    'text_order_status' => 'Lo stato dell\'ordine',
    'text_order_change_status' => 'Cambiare stato',
    'text_order_new_status' => 'Nuovo stato',
    'text_order_help_1' => 'Crea un ordine Glovo quando l\'ordine del negozio passa a uno di questi stati.',
    'text_order_help_2' => 'Abilita modifica dello stato dell\'ordine del negozio durante la creazione dell\'ordine Glovo.',
    'text_order_help_3' => 'Cambia gli ordini del negozio in questo stato quando viene creato un ordine Glovo.',
    'text_map_zoom' => 'Zoom della mappa',
    'text_store_icon' => 'Icona Store',
    'text_gmaps_configuration_help_1' => '
        Devi creare un account console Google qui: <a href="https://console.cloud.google.com"> https://console.cloud.google.com </a> <br>
         Come ottenere la chiave API: <a href="https://developers.google.com/maps/documentation/javascript/get-api-key"> https://developers.google.com/maps/documentation/javascript/ get-api-chiave </a> <br>
         E deve avere la seguente API: API di Places, API di indicazioni stradali, API di geocodifica, API Javascript di Maps
    ',
    'text_map_zoom_help' => 'Lo zoom predefinito per tutte le mappe. (Valore predefinito: 16)',
    'text_store_icon_help' => 'L\'icona predefinita dei tuoi negozi per le mappe.',

    'entry_day_0' => 'Domenica',
    'entry_day_1' => 'Lunedi',
    'entry_day_2' => 'Martedì',
    'entry_day_3' => 'Mercoledì',
    'entry_day_4' => 'Giovedi',
    'entry_day_5' => 'Venerdì',
    'entry_day_6' => 'Sabato',

    'entry_license' => 'Licenza',
    'error_license' => 'È importante scrivere una licenza valida acquistata.',

    'entry_api_key' => 'Chiave Glovo Api',
    'entry_api_secret' => 'Glovo Api Secret',
    'entry_google_maps_key' => 'Chiave API di Google Maps',

    'error_api_key' => 'Scrivi la tua chiave Glovo Api',
    'error_api_secret' => 'Per favore, scrivi il tuo segreto di Glovo Api',
    'error_google_maps_key' => 'Scrivi la tua chiave API di Google Maps',

    'text_holiday_1' => 'Definir feriados. Hoje em dia não haverá serviço de entrega.',
    'text_holiday_2' => 'Aggiungi festività',
//    -------------------------------------------

    'heading_title' => 'TuSuper - Delivery',
    'text_extension' => 'Estensione',

    'text_success' => '! Molto bene, hai modificato Glovo!',
];

$_ = array_merge($_, $i18n);
