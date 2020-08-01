<?php
$i18n = [
    'vex_glovo_store_config' => '[LLAMA YA] Delivery - Configuração de Loja',

    'text_view_orders' => 'Ver pedidos',
    'text_my_stores' => 'Minhas Lojas',
    'text_configuration' => 'Configuração',

    'text_create_new_store' => 'Criar nova loja',
    'text_new_store' => 'Nova Loja',

    'text_order' => 'Ordem',
    'text_glovo' => 'LLAMA YA',
    'text_status' => 'Status',
    'text_cost' => 'Custo',
    'text_ship_to' => 'Enviar para',
    'text_city' => 'Cidade',
    'text_aita_code' => 'Código IATA',
    'text_created_date' => 'Data de criação',
    'text_date_delivered' => 'Data de entrega',

    'text_country' => 'País',
    'text_location' => 'Localização',
    'text_contact_person' => 'Pessoa de contato',
    'text_contact_phone' => 'telefone de contato',
    'text_active' => 'Ativo',
    'text_toggle_status' => 'Alternar status',
    'text_delete_store' => 'Remover',
    'text_edit' => 'Editar',
    'text_action' => 'Açao',
    'text_currency' => 'Moeda',

    'text_enable' => 'Habilitar',
    'text_delivery_addresses' => 'Endereços de entrega',
    'text_store_address' => 'Endereço da loja',
    'text_address_2' => 'Endereço 2',
    'text_reference' => 'Referência',
    'text_postal_code' => 'Código postal',
    'text_find_lat_lng' => 'Veja Lat e Long neste URL: <a href="https://www.latlong.net/"> https://www.latlong.net/ </a>',
    'text_find_aita_code' => 'Encontre o seu Código AITA com este url: <a href="https://www.world-airport-codes.com/search/"> https://www.world-airport-codes.com/search/</a >',
    'text_working_hours' => 'Horas de serviço',
    'text_holidays' => 'Feriados',
    'text_city_geo_lat' => 'Latitude',
    'text_city_geo_lng' => 'Longitude',

    'text_module_configuration' => 'Configuração do Módulo',
    'text_glovo_configuration' => 'Configuração Glovo',
    'text_find_glovo_keys' => 'Encontre suas credenciais do glovo com este URL: <a href="https://business.glovoapp.com/dashboard/profile" target="_blank"> https://business.glovoapp.com/dashboard/profile </a>',
    'text_cost_shipping' => 'Custo de envio',
    'text_status_configuration' => 'Configuração de Status',
    'text_gmaps_configuration' => 'Configuração do Google Maps',

    'text_preparation_time_configuration' => 'Configuração do tempo de preparação',
    'text_preparation_time_help_1' => 'É o tempo necessário para produzir o produto em <b> minutos </ b>.',
    'text_preparation_time_help_url' => 'Você pode criar um novo atributo aqui',
    'text_preparation_time_attribute' => 'Atributo do Produto',
    'text_preparation_time_help_2' => 'Se você não quiser usar a funcionalidade de tempo de preparação, selecione a opção "- Nenhuma -" por 0 minutos.',

    'text_free' => 'Livre',
    'text_pulled_automatic' => 'Puxado automaticamente de Glovo Api',
    'text_based_price' => 'Baseado em um preço fixo',
    'text_order_status' => 'Status do pedido',
    'text_order_change_status' => 'Alterar status',
    'text_order_new_status' => 'Novo status',
    'text_order_help_1' => 'Crie um pedido Glovo quando o pedido da loja for alterado para qualquer um desses status.',
    'text_order_help_2' => 'Ative a mudança de status do pedido de loja na criação de pedidos do Glovo.',
    'text_order_help_3' => 'Altere os pedidos da loja para este status quando um pedido Glovo for criado.',
    'text_map_zoom' => 'Zoom do mapa',
    'text_store_icon' => 'Ícone da loja',
    'text_gmaps_configuration_help_1' => '
        Você precisa criar uma conta do console do Google aqui: <a href="https://console.cloud.google.com"> https://console.cloud.google.com </a> <br>
         Como obter a Chave da API: <a href="https://developers.google.com/maps/documentation/javascript/get-api-key"> https://developers.google.com/maps/documentation/javascript/ get-api-key </a> <br>
         E deve ter a seguinte API: Places API, Directions API, Geocoding API, Maps Javascript API
    ',
    'text_map_zoom_help' => 'O zoom padrão para todos os mapas. (Valor padrão: 16)',
    'text_store_icon_help' => 'O ícone padrão de suas lojas para mapas.',

    'entry_day_0' => 'Domingo',
    'entry_day_1' => 'Segunda-feira',
    'entry_day_2' => 'Terça-feira ',
    'entry_day_3' => 'Quarta-feira',
    'entry_day_4' => 'Quinta-feira',
    'entry_day_5' => 'Sexta-feira',
    'entry_day_6' => 'Sábado',

    'entry_license' => 'Licença',
    'error_license' => 'É importante que você escreva uma licença válida que você comprou.',

    'entry_api_key' => 'Chave Gli Api',
    'entry_api_secret' => 'Glovo Api Secret',
    'entry_google_maps_key' => 'Chave da API do Google Maps',

    'error_api_key' => 'Por favor, escreva sua chave Glovo Api',
    'error_api_secret' => 'Por favor, escreva seu Glovo Api Secret',
    'error_google_maps_key' => 'Por favor, escreva sua chave de API do Google Maps',

    'text_holiday_1' => 'Definir feriados. Hoje em dia não haverá serviço de entrega.',
    'text_holiday_2' => 'Adicionar feriado',
//    -------------------------------------------

    'heading_title' => 'TuSuper - Delivery',
    'text_extension' => 'Extensão',

    'text_success' => 'Muito bem, você modificou o Glovo!',
];

$_ = array_merge($_, $i18n);
