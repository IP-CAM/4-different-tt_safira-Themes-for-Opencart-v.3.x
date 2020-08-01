<?php
$i18n = [
    'vex_glovo_store_config' => '[LLAMA YA] Delivery - Configuration du magasin',

    'text_view_orders' => 'Voir les commandes',
    'text_my_stores' => 'Mes magasins',
    'text_configuration' => 'Configuration',

    'text_create_new_store' => 'Créer un nouveau magasin',
    'text_new_store' => 'Nouveau magasin',

    'text_order' => 'Ordre',
    'text_glovo' => 'LLAMA YA',
    'text_status' => 'Statut',
    'text_cost' => 'Coût',
    'text_ship_to' => 'Envoyez à',
    'text_city' => 'Ville',
    'text_aita_code' => 'Code IATA',
    'text_created_date' => 'Date de création',
    'text_date_delivered' => 'Date de livraison',

    'text_country' => 'Pays',
    'text_location' => 'Emplacement',
    'text_contact_person' => 'Contact',
    'text_contact_phone' => 'Numéro du contact',
    'text_active' => 'actif',
    'text_toggle_status' => 'Basculer le statut',
    'text_delete_store' => 'Retirer',
    'text_edit' => 'modifier',
    'text_action' => 'action',
    'text_currency' => 'Devise',

    'text_enable' => 'Activer',
    'text_delivery_addresses' => 'Adresses de livraison',
    'text_store_address' => 'Adresse du magasin',
    'text_address_2' => 'Adresse 2',
    'text_reference' => 'Référence',
    'text_postal_code' => 'code postal',
    'text_find_lat_lng' => 'Voir Lat et Long dans cette URL: <a href="https://www.latlong.net/"> https://www.latlong.net/ </a>',
    'text_find_aita_code' => 'Trouvez votre code AITA avec l\'URL suivante: <a href="https://www.world-airport-codes.com/search/"> https://www.world-airport-codes.com/search/</a >',
    'text_working_hours' => 'Heures de service',
    'text_holidays' => 'Vacances',
    'text_city_geo_lat' => 'Latitude',
    'text_city_geo_lng' => 'Longitude',

    'text_module_configuration' => 'Configuration du module',
    'text_glovo_configuration' => 'Configuration Glovo',
    'text_find_glovo_keys' => 'Recherchez vos identifiants glovo avec cette adresse: <a href="https://business.glovoapp.com/dashboard/profile" target="_blank"> https://business.glovoapp.com/dashboard/profile </a>.',
    'text_cost_shipping' => 'Coût de l\'expédition',
    'text_status_configuration' => 'Statut de configuration',
    'text_gmaps_configuration' => 'Configuration de Google Maps',

    'text_preparation_time_configuration' => 'Configuration du temps de préparation',
    'text_preparation_time_help_1' => 'Est-ce le temps qu\'il faut pour fabriquer le produit en <b> minutes </ b>.',
    'text_preparation_time_help_url' => 'Vous pouvez créer un nouvel attribut ici',
    'text_preparation_time_attribute' => 'Attribut du produit',
    'text_preparation_time_help_2' => 'Si vous ne souhaitez pas utiliser la fonctionnalité de temps de préparation, sélectionnez l\'option "- Aucune -" pendant 0 minutes.',

    'text_free' => 'Libre',
    'text_pulled_automatic' => 'Tiré automatiquement de Glovo Api',
    'text_based_price' => 'Basé sur un prix fixe',
    'text_order_status' => 'Statut de la commande',
    'text_order_change_status' => 'Changer le statut',
    'text_order_new_status' => 'Nouveau statut',
    'text_order_help_1' => 'Créez une commande Glovo lorsque la commande en magasin passe à l’un de ces statuts.',
    'text_order_help_2' => 'Activer le changement d\'état de la commande boutique lors de la création de la commande Glovo.',
    'text_order_help_3' => 'Modifiez les commandes en magasin à cet état lors de la création d\'une commande Glovo.',
    'text_map_zoom' => 'Zoom de la carte',
    'text_store_icon' => 'Icône de magasin',
    'text_gmaps_configuration_help_1' => '
        Vous devez créer un compte de console Google ici: <a href="https://console.cloud.google.com"> https://console.cloud.google.com </a> <br>
         Comment obtenir une clé API: <a href="https://developers.google.com/maps/documentation/javascript/get-api-key"> https://developers.google.com/maps/documentation/javascript/ get-api-key </a> <br>
         Et il doit avoir les API suivantes: API Lieux, API Directions, API de géocodage, API Javascript Javascript
    ',
    'text_map_zoom_help' => 'Le zoom par défaut pour toutes les cartes. (Valeur par défaut: 16)',
    'text_store_icon_help' => 'L\'icône par défaut de vos magasins pour les cartes.',

    'entry_day_0' => 'Dimanche',
    'entry_day_1' => 'Lundi',
    'entry_day_2' => 'Mardi',
    'entry_day_3' => 'Mercredi',
    'entry_day_4' => 'Jeudi',
    'entry_day_5' => 'Vendredi',
    'entry_day_6' => 'Samedi',

    'entry_license' => 'Licence',
    'error_license' => 'Il est important que vous écriviez une licence valide que vous avez achetée.',

    'entry_api_key' => 'Clé Glovo Api',
    'entry_api_secret' => 'Glovo Api Secret',
    'entry_google_maps_key' => 'Clé API Google Maps',

    'error_api_key' => 'S\'il vous plaît écrivez votre clé Glovo Api',
    'error_api_secret' => 'S\'il vous plaît écrivez votre Glovo Api Secret',
    'error_google_maps_key' => 'S\'il vous plaît écrivez votre clé Google Maps Api',

    'text_holiday_1' => 'Définir des vacances. Ces jours il n\'y aura pas de service de livraison.',
    'text_holiday_2' => 'Ajouter des vacances',
//    -------------------------------------------

    'heading_title' => 'TuSuper - Delivery',
    'text_extension' => 'Extension',

    'text_success' => 'Très bien, vous avez modifié Glovo!',
];

$_ = array_merge($_, $i18n);
