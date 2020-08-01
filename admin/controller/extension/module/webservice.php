<?php

class ControllerExtensionModuleWebService extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('extension/module/webservice');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        if(VERSION >= '2.2.0.0'){
            $this->load->model('customer/custom_field');
            $results = $this->model_customer_custom_field->getCustomFields();
        } else{
            $this->load->model('sale/custom_field');
            $results = $this->model_sale_custom_field->getCustomFields();
        }
        
        if($results){
            $data['custom_fields'] = $results;
        }else{
            $data['custom_fields'] = array();
        }
        $this->load->model('webservice/webservice');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        //getting settings from post or db if nothing is recived in post parameter
        if (isset($this->request->post['webservice'])) {
            $settings = $this->request->post;
        } else if ($this->model_setting_setting->getSetting('webservice', 0)) {
            $settings = $this->model_setting_setting->getSetting('webservice', 0);
        }
        $spinWin_settings = $this->model_setting_setting->getSetting('spinwin', 0);
        if(isset($spinWin_settings['spinwin_enable'])){
            $data['spin_win_enable'] = 1;
        } else{
            $data['spin_win_enable'] = 0;
        }
        //setting default parameter for general tab
        if (empty($settings['webservice'])) {
            $data['custom_css'] = '';
            $data['category_image_width'] = '200';
            $data['category_image_height'] = '200';
            $data['product_image_width'] = '200';
            $data['product_image_height'] = '200';
            $data['whatsapp_chat_status'] = 0;
            $data['tab_bar_enabled'] = 1;
            $data['mobile_app_spin_win'] = 0;
            $data['fingerprint_login_status'] = 0;
            $data['phone_registration_status'] = 0;
            $data['mandatory_phone_registration_status'] = 0;
            $data['whatsapp_phone_number'] = '';
            $data['app_button_color'] = '#00a781';
            $data['app_theme_color'] = '#c3000f';
            $data['app_button_text_color'] = '#ffffff';
            $data['app_background_color'] = '#ffffff';
            $data['logo_status'] = 0;
            $data['image_for_logo'] = '';
            $data['redirect_cart_status'] = 0;
        } else {
            $data['webservice_status'] = $settings['webservice']['webservice_status'];
            $data['webservice_log_status'] = $settings['webservice']['webservice_log_status'];
            $data['webservice_chat_status'] = $settings['webservice']['webservice_chat_status'];
            $data['category_image_width'] = $settings['webservice']['category_image_width'];
            $data['category_image_height'] = $settings['webservice']['category_image_height'];
            $data['product_image_width'] = $settings['webservice']['product_image_width'];
            $data['product_image_height'] = $settings['webservice']['product_image_height'];
            $data['whatsapp_chat_status'] = $settings['webservice']['whatsapp_chat_status'];
            if(isset($settings['webservice']['tab_bar_enabled'])){
                $data['tab_bar_enabled'] = $settings['webservice']['tab_bar_enabled'];
            }else{
                $data['tab_bar_enabled'] = 0;
            }
            if(isset($settings['webservice']['mobile_app_spin_win'])){
                $data['mobile_app_spin_win'] = $settings['webservice']['mobile_app_spin_win'];
            }else{
                $data['mobile_app_spin_win'] = 0;
            }
            $data['fingerprint_login_status'] = $settings['webservice']['fingerprint_login_status'];
            $data['phone_registration_status'] = $settings['webservice']['phone_registration_status'];
            $data['mandatory_phone_registration_status'] = $settings['webservice']['mandatory_phone_registration_status'];
            $data['whatsapp_phone_number'] = $settings['webservice']['whatsapp_phone_number'];
            $data['app_button_color'] = $settings['webservice']['app_button_color'];
            $data['app_theme_color'] = $settings['webservice']['app_theme_color'];
            $data['app_button_text_color'] = $settings['webservice']['app_button_text_color'];
            $data['app_background_color'] = $settings['webservice']['app_background_color'];
            $data['logo_status'] = $settings['webservice']['logo_status'];
            $data['redirect_cart_status'] = $settings['webservice']['redirect_cart_status'];
            $data['url_encoding_status'] = $settings['webservice']['url_encoding_status'];
        }
        
        if (isset($this->request->post['webservice']['image_for_logo'])) {
            $data['image_for_logo'] = $this->request->post['webservice']['image_for_logo'];
        } else if (!empty($settings['webservice']['image_for_logo'])) {
            $data['image_for_logo'] = $settings['webservice']['image_for_logo'];
        } else {
            $data['image_for_logo'] = '';
        }
        
        /*         * *********************** Home Page Layout tab********************** */
        
        $layouts = $this->model_webservice_webservice->getHomePageLayouts();
        if(!empty($layouts)) {
            foreach($layouts as $layout){
                $data['layouts'][] = array(
                    'layout_id' =>  $layout['id_layout'],
                    'layout_name' => $layout['layout_name'],
                    'layout_url' => "",
                );
            }
        }
        if (isset($this->request->post['webservice']['homepage_layout'])) {
            $data['homepage_layout'] = $this->request->post['webservice']['homepage_layout'];
        } else if (!empty($settings['webservice']['homepage_layout'])) {
            $data['homepage_layout'] = $settings['webservice']['homepage_layout'];
        } else {
            $data['homepage_layout'] = 1;
        }
        
        /*         * *********************** Push Notifications Settings tab********************** */

        if (isset($this->request->post['webservice']['firebase_server_key'])) {
            $data['firebase_server_key'] = $this->request->post['webservice']['firebase_server_key'];
        } else if (!empty($settings['webservice']['firebase_server_key'])) {
            $data['firebase_server_key'] = $settings['webservice']['firebase_server_key'];
        } else {
            $data['firebase_server_key'] = '';
        }
        
        if (isset($this->request->post['webservice']['chat_key'])) {
            $data['chat_key'] = $this->request->post['webservice']['chat_key'];
        } else if (!empty($settings['webservice']['chat_key'])) {
            $data['chat_key'] = $settings['webservice']['chat_key'];
        } else {
            $data['chat_key'] = '';
        }

        //Order Status
        if (isset($this->request->post['webservice']['order_success_status'])) {
            $data['order_success_status'] = $this->request->post['webservice']['order_success_status'];
        } else if (!empty($settings['webservice']['order_success_status'])) {
            $data['order_success_status'] = $settings['webservice']['order_success_status'];
        } else {
            $data['order_success_status'] = '';
        }

        if (isset($this->request->post['order_success_title'])) {
            $data['order_success_title'] = $this->request->post['order_success_title'];
        } else if (!empty($settings['webservice']['order_success_title'])) {
            $data['order_success_title'] = $settings['webservice']['order_success_title'];
        } else {
            $data['order_success_title'] = 'Order Successfully Placed';
        }

        if (isset($this->request->post['webservice']['order_success_message'])) {
            $data['order_success_message'] = $this->request->post['webservice']['order_success_message'];
        } else if (!empty($settings['webservice']['order_success_message'])) {
            $data['order_success_message'] = $settings['webservice']['order_success_message'];
        } else {
            $data['order_success_message'] = 'Hi Thanks for your interest. Keep shopping with us to become our premium customer.';
        }

        //Order Status Change
        if (isset($this->request->post['webservice']['status_change_status'])) {
            $data['status_change_status'] = $this->request->post['webservice']['status_change_status'];
        } else if (!empty($settings['webservice']['status_change_status'])) {
            $data['status_change_status'] = $settings['webservice']['status_change_status'];
        } else {
            $data['status_change_status'] = '';
        }

        if (isset($this->request->post['webservice']['status_change_title'])) {
            $data['status_change_title'] = $this->request->post['webservice']['status_change_title'];
        } else if (!empty($settings['webservice']['status_change_title'])) {
            $data['status_change_title'] = $settings['webservice']['status_change_title'];
        } else {
            $data['status_change_title'] = 'Order status update';
        }

        if (isset($this->request->post['webservice']['status_change_message'])) {
            $data['status_change_message'] = $this->request->post['webservice']['status_change_message'];
        } else if (!empty($settings['webservice']['status_change_message'])) {
            $data['status_change_message'] = $settings['webservice']['status_change_message'];
        } else {
            $data['status_change_message'] = 'Hi Your order status has been changed to {{STATUS}}.';
        }

        if (isset($this->request->get['filter_title'])) {
            $filter_title = $this->request->get['filter_title'];
        } else {
            $filter_title = null;
        }

        if (isset($this->request->get['filter_redirect_activity'])) {
            $filter_redirect_activity = $this->request->get['filter_redirect_activity'];
        } else {
            $filter_redirect_activity = null;
        }

        if (isset($this->request->get['filter_from_date'])) {
            $filter_from_date = $this->request->get['filter_from_date'];
        } else {
            $filter_from_date = null;
        }

        if (isset($this->request->get['filter_to_date'])) {
            $filter_to_date = $this->request->get['filter_to_date'];
        } else {
            $filter_to_date = null;
        }


        //Abandoned
        if (isset($this->request->post['webservice']['abandoned_cart_notification_status'])) {
            $data['abandoned_cart_notification_status'] = $this->request->post['webservice']['abandoned_cart_notification_status'];
        } else if (!empty($settings['webservice']['abandoned_cart_notification_status'])) {
            $data['abandoned_cart_notification_status'] = $settings['webservice']['abandoned_cart_notification_status'];
        } else {
            $data['abandoned_cart_notification_status'] = '';
        }

        if (isset($this->request->post['webservice']['abandoned_cart_notification_title'])) {
            $data['abandoned_cart_notification_title'] = $this->request->post['webservice']['abandoned_cart_notification_title'];
        } else if (!empty($settings['webservice']['abandoned_cart_notification_title'])) {
            $data['abandoned_cart_notification_title'] = $settings['webservice']['abandoned_cart_notification_title'];
        } else {
            $data['abandoned_cart_notification_title'] = 'Hurry!';
        }

        if (isset($this->request->post['webservice']['abandoned_cart_notification_message'])) {
            $data['abandoned_cart_notification_message'] = $this->request->post['webservice']['abandoned_cart_notification_message'];
        } else if (!empty($settings['webservice']['abandoned_cart_notification_message'])) {
            $data['abandoned_cart_notification_message'] = $settings['webservice']['abandoned_cart_notification_message'];
        } else {
            $data['abandoned_cart_notification_message'] = 'Hi, Complete your order to get extra benefits on your next order.';
        }

        if (isset($this->request->post['webservice']['abandoned_cart_notification_interval'])) {
            $data['abandoned_cart_notification_interval'] = $this->request->post['webservice']['abandoned_cart_notification_interval'];
        } else if (!empty($settings['webservice']['abandoned_cart_notification_interval'])) {
            $data['abandoned_cart_notification_interval'] = $settings['webservice']['abandoned_cart_notification_interval'];
        } else {
            $data['abandoned_cart_notification_interval'] = '1';
        }

        //Payment Method
        if (isset($this->request->post['webservice']['paypal_status'])) {
            $data['paypal_status'] = $this->request->post['webservice']['paypal_status'];
        } else if (!empty($settings['webservice']['paypal_status'])) {
            $data['paypal_status'] = $settings['webservice']['paypal_status'];
        } else {
            $data['paypal_status'] = '0';
        }

        if (isset($this->request->post['webservice']['paypal_name'])) {
            $data['paypal_name'] = $this->request->post['webservice']['paypal_name'];
        } else if (!empty($settings['webservice']['paypal_name'])) {
            $data['paypal_name'] = $settings['webservice']['paypal_name'];
        } else {
            $data['paypal_name'] = '';
        }

        if (isset($this->request->post['webservice']['paypal_code'])) {
            $data['paypal_code'] = $this->request->post['webservice']['paypal_code'];
        } else if (!empty($settings['webservice']['paypal_code'])) {
            $data['paypal_code'] = $settings['webservice']['paypal_code'];
        } else {
            $data['paypal_code'] = '';
        }

        if (isset($this->request->post['webservice']['paypal_mode'])) {
            $data['paypal_mode'] = $this->request->post['webservice']['paypal_mode'];
        } else if (!empty($settings['webservice']['paypal_mode'])) {
            $data['paypal_mode'] = $settings['webservice']['paypal_mode'];
        } else {
            $data['paypal_mode'] = '';
        }

        if (isset($this->request->post['webservice']['paypal_clientid'])) {
            $data['paypal_clientid'] = $this->request->post['webservice']['paypal_clientid'];
        } else if (!empty($settings['webservice']['paypal_clientid'])) {
            $data['paypal_clientid'] = $settings['webservice']['paypal_clientid'];
        } else {
            $data['paypal_clientid'] = '';
        }

        if (isset($this->request->post['webservice']['cod_status'])) {
            $data['cod_status'] = $this->request->post['webservice']['cod_status'];
        } else if (!empty($settings['webservice']['cod_status'])) {
            $data['cod_status'] = $settings['webservice']['cod_status'];
        } else {
            $data['cod_status'] = '0';
        }

        if (isset($this->request->post['webservice']['cod_name'])) {
            $data['cod_name'] = $this->request->post['webservice']['cod_name'];
        } else if (!empty($settings['webservice']['cod_name'])) {
            $data['cod_name'] = $settings['webservice']['cod_name'];
        } else {
            $data['cod_name'] = '';
        }

        if (isset($this->request->post['webservice']['cod_code'])) {
            $data['cod_code'] = $this->request->post['webservice']['cod_code'];
        } else if (!empty($settings['webservice']['cod_code'])) {
            $data['cod_code'] = $settings['webservice']['cod_code'];
        } else {
            $data['cod_code'] = '';
        }
        if (isset($this->request->post['webservice']['new_product_start_date'])) {
	    $data['new_product_start_date'] = $this->request->post['webservice']['new_product_start_date'];
        } else if (!empty($settings['webservice']['new_product_start_date'])) {
	    $data['new_product_start_date'] = $settings['webservice']['new_product_start_date'];
        } else {
	    $data['new_product_start_date'] = date('Y-m-d', strtotime("- 30 days"));
        }

        if (isset($this->request->post['webservice']['new_product_limit'])) {
            $data['new_product_limit'] = $this->request->post['webservice']['new_product_limit'];
        } else if (!empty($settings['webservice']['new_product_limit'])) {
            $data['new_product_limit'] = $settings['webservice']['new_product_limit'];
        } else {
            $data['new_product_limit'] = '24';
        }

        if (isset($this->request->post['webservice']['product_show_price'])) {
            $data['product_show_price'] = $this->request->post['webservice']['product_show_price'];
        } else if (!empty($settings['webservice']['product_show_price'])) {
            $data['product_show_price'] = $settings['webservice']['product_show_price'];
        } else {
            $data['product_show_price'] = '1';
        }
        
        if (isset($this->request->post['webservice']['phone_field'])) {
            $data['phone_field'] = $this->request->post['webservice']['phone_field'];
        } else if (!empty($settings['webservice']['phone_field'])) {
            $data['phone_field'] = $settings['webservice']['phone_field'];
        } else {
            $data['phone_field'] = '1';
        }
        
        if (isset($this->request->post['webservice']['title_field'])) {
            $data['title_field'] = $this->request->post['webservice']['title_field'];
        } else if (!empty($settings['webservice']['title_field'])) {
            $data['title_field'] = $settings['webservice']['title_field'];
        } else {
            $data['title_field'] = '1';
        }
        
        if (isset($this->request->post['webservice']['gender_field'])) {
            $data['gender_field'] = $this->request->post['webservice']['gender_field'];
        } else if (!empty($settings['webservice']['gender_field'])) {
            $data['gender_field'] = $settings['webservice']['gender_field'];
        } else {
            $data['gender_field'] = '1';
        }
        
        if (isset($this->request->post['webservice']['dob_field'])) {
            $data['dob_field'] = $this->request->post['webservice']['dob_field'];
        } else if (!empty($settings['webservice']['dob_field'])) {
            $data['dob_field'] = $settings['webservice']['dob_field'];
        } else {
            $data['dob_field'] = '1';
        }


        /*         * *********************** Push Notifications Settings tab********************** */


        /*         * *********************** Slideshow tab********************** */

        //slideshow id
        if (isset($this->request->post['slideshow_id'])) {
            $data['slideshow_id'] = $this->request->post['slideshow_id'];
        } else if (!empty($settings['webservice']['slideshow_id'])) {
            $data['slideshow_id'] = $settings['webservice']['slideshow_id'];
        } else {
            $data['slideshow_id'] = '';
        }

        if ($data['slideshow_id'] != '') {
            $slideshow_banner_detail = $this->model_webservice_webservice->getSlideshow($data['slideshow_id']);
        }

        //slideshow name
        if (isset($this->request->post['slideshow_name'])) {
            $data['slideshow_name'] = $this->request->post['slideshow_name'];
        } else if (!empty($slideshow_banner_detail)) {
            $data['slideshow_name'] = $slideshow_banner_detail['name'];
        } else {
            $data['slideshow_name'] = 'Test Name';
        }

        //slideshow status
        if (isset($this->request->post['slideshow_status'])) {
            $data['slideshow_status'] = $this->request->post['slideshow_status'];
        } else if (!empty($slideshow_banner_detail)) {
            $data['slideshow_status'] = $slideshow_banner_detail['status'];
        } else {
            $data['slideshow_status'] = '';
        }

        //slideshow limit
        if (isset($this->request->post['slideshow_limit'])) {
            $data['slideshow_limit'] = $this->request->post['slideshow_limit'];
        } else if (!empty($slideshow_banner_detail)) {
            $data['slideshow_limit'] = $slideshow_banner_detail['banner_limit'];
        } else {
            $data['slideshow_limit'] = 4;
        }

        //slideshow image width
        if (!empty($settings['webservice']['slideshow_width'])) {
            $data['slideshow_width'] = $settings['webservice']['slideshow_width'];
        } else {
            $data['slideshow_width'] = '600';
        }

        //slideshow image height
        if (!empty($settings['webservice']['slideshow_height'])) {
            $data['slideshow_height'] = $settings['webservice']['slideshow_height'];
        } else {
            $data['slideshow_height'] = '200';
        }

        /*         * *********************** Banners tab********************** */

        //banner id
        if (isset($this->request->post['banner_id'])) {
            $data['banner_id'] = $this->request->post['banner_id'];
        } else if (!empty($settings['webservice']['banner_id'])) {
            $data['banner_id'] = $settings['webservice']['banner_id'];
        } else {
            $data['banner_id'] = '';
        }

        if ($data['banner_id'] != '') {
            $banner_banner_detail = $this->model_webservice_webservice->getBanner($data['banner_id']);
        }

        //banner name
        if (isset($this->request->post['banner_name'])) {
            $data['banner_name'] = $this->request->post['banner_name'];
        } else if (!empty($banner_banner_detail)) {
            $data['banner_name'] = $banner_banner_detail['name'];
        } else {
            $data['banner_name'] = 'Test Name';
        }

        //banner status
        if (isset($this->request->post['banner_status'])) {
            $data['banner_status'] = $this->request->post['banner_status'];
        } else if (!empty($banner_banner_detail)) {
            $data['banner_status'] = $banner_banner_detail['status'];
        } else {
            $data['banner_status'] = '';
        }

        //banner limit
        if (isset($this->request->post['banner_limit'])) {
            $data['banner_limit'] = $this->request->post['banner_limit'];
        } else if (!empty($banner_banner_detail)) {
            $data['banner_limit'] = $banner_banner_detail['banner_limit'];
        } else {
            $data['banner_limit'] = 4;
        }

        //banner image width
        if (!empty($settings['webservice']['banner_width'])) {
            $data['banner_width'] = $settings['webservice']['banner_width'];
        } else {
            $data['banner_width'] = '600';
        }

        //banner image height
        if (!empty($settings['webservice']['banner_height'])) {
            $data['banner_height'] = $settings['webservice']['banner_height'];
        } else {
            $data['banner_height'] = '200';
        }
        /*         * *********************** Featured tab********************** */
        //featured status
        if (!empty($settings['webservice']['featured_status'])) {
            $data['featured_status'] = $settings['webservice']['featured_status'];
        }

        //featured limit
        if (!empty($settings['webservice']['featured_limit'])) {
            $data['featured_limit'] = $settings['webservice']['featured_limit'];
        } else {
            $data['featured_limit'] = 4;
        }

        //featured products
        if (!empty($settings['webservice']['featured_product'])) {
            $data['featured_product'] = $settings['webservice']['featured_product'];
        } else {
            $data['featured_product'] = '';
        }
        if (!empty($settings['webservice']['shipping_method'])) {
            $data['shipping_method'] = $settings['webservice']['shipping_method'];
        } else {
            $data['shipping_method'] = '';
        }

        //featured image width 
        if (!empty($settings['webservice']['featured_width'])) {
            $data['featured_width'] = $settings['webservice']['featured_width'];
        } else {
            $data['featured_width'] = '200';
        }

        //featured image height
        if (!empty($settings['webservice']['featured_height'])) {
            $data['featured_height'] = $settings['webservice']['featured_height'];
        } else {
            $data['featured_height'] = '200';
        }
        /*         * *********************** Specials tab********************** */

        //Specials status
        if (!empty($settings['webservice']['special_status'])) {
            $data['special_status'] = $settings['webservice']['special_status'];
        }

        //Specials limit
        if (!empty($settings['webservice']['special_limit'])) {
            $data['special_limit'] = $settings['webservice']['special_limit'];
        } else {
            $data['special_limit'] = 4;
        }

        //Specials image width 
        if (!empty($settings['webservice']['special_width'])) {
            $data['special_width'] = $settings['webservice']['special_width'];
        } else {
            $data['special_width'] = '200';
        }

        //Specials image height
        if (!empty($settings['webservice']['special_height'])) {
            $data['special_height'] = $settings['webservice']['special_height'];
        } else {
            $data['special_height'] = '200';
        }
        /*         * *********************** Best sellers tab********************** */
        //Best Sellers status
        if (!empty($settings['webservice']['bestseller_status'])) {
            $data['bestseller_status'] = $settings['webservice']['bestseller_status'];
        }

        //Best Sellers limit
        if (!empty($settings['webservice']['bestseller_limit'])) {
            $data['bestseller_limit'] = $settings['webservice']['bestseller_limit'];
        } else {
            $data['bestseller_limit'] = 4;
        }

        //Best Sellers image width 
        if (!empty($settings['webservice']['bestseller_width'])) {
            $data['bestseller_width'] = $settings['webservice']['bestseller_width'];
        } else {
            $data['bestseller_width'] = '200';
        }

        //Best Sellers image height
        if (!empty($settings['webservice']['bestseller_height'])) {
            $data['bestseller_height'] = $settings['webservice']['bestseller_height'];
        } else {
            $data['bestseller_height'] = '200';
        }
        /*         * *********************** Latest tab********************** */

        //Latest status
        if (!empty($settings['webservice']['latest_status'])) {
            $data['latest_status'] = $settings['webservice']['latest_status'];
        }

        //Latest limit
        if (!empty($settings['webservice']['latest_limit'])) {
            $data['latest_limit'] = $settings['webservice']['latest_limit'];
        } else {
            $data['latest_limit'] = 4;
        }

        //Latest image width 
        if (!empty($settings['webservice']['latest_width'])) {
            $data['latest_width'] = $settings['webservice']['latest_width'];
        } else {
            $data['latest_width'] = '200';
        }

        //Latest image height
        if (!empty($settings['webservice']['latest_height'])) {
            $data['latest_height'] = $settings['webservice']['latest_height'];
        } else {
            $data['latest_height'] = '200';
        }

        //getting products for featured tab
        $this->load->model('catalog/product');

        if (!empty($settings['webservice']['featured_product'])) {
            $featured_products = explode(',', $settings['webservice']['featured_product']);
        } else {
            $featured_products = array();
        }
        
        if (!empty($settings['webservice']['shipping_method'])) {
            $shipping_methods = explode(',', $settings['webservice']['shipping_method']);
        } else {
            $shipping_methods = array();
        }
        
        $data['shipping_methods'] = array();
        
        foreach($shipping_methods as $method) {
            $data['shipping_methods'][] = array(
                    'code' => $method,
                );
        }
        
        $data['featured_products'] = array();

        foreach ($featured_products as $featured_product_id) {
            $featured_product_info = $this->model_catalog_product->getProduct($featured_product_id);

            if ($featured_product_info) {
                $data['featured_products'][] = array(
                    'product_id' => $featured_product_info['product_id'],
                    'name' => $featured_product_info['name']
                );
            }
        }
        //checking request method and sending request for validation
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            
            //adding slideshow
            if ($data['slideshow_id'] == '') {
                $slideshow_id = $this->model_webservice_webservice->addSlideshow($this->request->post);
            } else {//editing slideshow
                $slideshow_id = $data['slideshow_id'];
                $this->model_webservice_webservice->editSlideshow($slideshow_id, $this->request->post);
            }
            //adding banners
            if ($data['banner_id'] == '') {
                $banner_id = $this->model_webservice_webservice->addBanner($this->request->post);
            } else {//editing banners
                $banner_id = $data['banner_id'];
                $this->model_webservice_webservice->editBanner($banner_id, $this->request->post);
            }
            $this->request->post['webservice']['slideshow_id'] = $slideshow_id;
            $this->request->post['webservice']['banner_id'] = $banner_id;
            $this->model_setting_setting->editSetting('webservice', $this->request->post);
            
            $data['module_webservice_status'] = $this->request->post['webservice']['webservice_status'];
            $this->model_setting_setting->editSetting('module_webservice',$data);
            
            $this->model_webservice_webservice->addCmsData($this->request->post['cms']);

                $this->session->data['success'] = $this->language->get('text_success');

                $this->response->redirect($this->url->link('extension/module/webservice', 'user_token=' . $this->session->data['user_token'], 'SSL'));
            }
		
        $data['user_token'] = $this->session->data['user_token'];
        //information link
        $data['information'] = $this->url->link('catalog/information', 'user_token=' . $this->session->data['user_token'], 'SSL');

        //getting language data from language file

        $data['heading_title'] = $this->language->get('heading_title');
        $data['filter_redirect_types'] = array(
            'home' => "Home",
            'category' => "Category",
            'product' => "Product"
        );
        $filter_notifications = array(
            'filter_title' => $filter_title,
            'filter_redirect_activity' => $filter_redirect_activity,
            'filter_from_date' => $filter_from_date,
            'filter_to_date' => $filter_to_date,
        );
        $notification_histories = $this->model_webservice_webservice->getNotificationHistory($filter_notifications);
        if ($notification_histories) {
            $this->load->model('catalog/category');
            $this->load->model('catalog/product');
            $this->load->model('tool/image');
            foreach ($notification_histories as $notification_history) {
                if ($notification_history['category_id']) {
                    $category_info = $this->model_catalog_category->getCategory($notification_history['category_id']);
                    if($category_info) {
                        $category_name = $category_info['name'];
                    }
                } else {
                    $category_name = '--';
                }
                if ($notification_history['product_id']) {
                    $product_info = $this->model_catalog_product->getProduct($notification_history['product_id']);
                    if ($product_info) {
                        $product_name = $product_info['name'];
                    } else {
                        $product_name = '--';
                    }
                } else {
                    $product_name = '--';
                }
                $m_image = $this->model_tool_image->resize($notification_history['image_url'], 100, 100); //HTTP_CATALOG.$notification_history['image_url'];

                $data['notification_histories'][] = array(
                    'notification_id' => $notification_history['notification_id'],
                    'title' => $notification_history['title'],
                    'message' => $notification_history['message'],
                    'image' => $m_image,
                    'redirect_activity' => $notification_history['redirect_activity'],
                    'category' => $category_name,
                    'product' => $product_name,
                    'date_added' => $notification_history['date_added'],
                );
            }
        } else {
            $data['notification_histories'] = array();
        }

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_no_payment_methods'] = $this->language->get('text_no_payment_methods');
        $data['text_add_payment_method'] = $this->language->get('text_add_payment_method');
        $data['text_send_notification'] = $this->language->get('text_send_notification');
        $data['text_select'] = $this->language->get('text_select');
        $data['button_refresh'] = $this->language->get('button_refresh');
        $data['text_no_history'] = $this->language->get('text_no_history');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['entry_notification_id'] = $this->language->get('entry_notification_id');
        $data['entry_title'] = $this->language->get('entry_title');
        $data['entry_redirect_activity'] = $this->language->get('entry_redirect_activity');
        $data['entry_category'] = $this->language->get('entry_category');
        $data['entry_product'] = $this->language->get('entry_product');
        $data['entry_sent_date'] = $this->language->get('entry_sent_date');
        $data['entry_details'] = $this->language->get('entry_details');
        $data['entry_from_date'] = $this->language->get('entry_from_date');
        $data['entry_to_date'] = $this->language->get('entry_to_date');
        $data['button_submit'] = $this->language->get('button_submit');
        $this->load->model('tool/image');
        $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['category_entry_width'] = $this->language->get('category_entry_width');
        $data['category_entry_height'] = $this->language->get('category_entry_height');
        $data['product_entry_width'] = $this->language->get('product_entry_width');
        $data['product_entry_height'] = $this->language->get('product_entry_height');
        //$data['entry_height'] = $this->language->get('entry_height');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_log_status'] = $this->language->get('entry_log_status');
        $data['entry_chat_status'] = $this->language->get('entry_chat_status');
        $data['entry_chat_key'] = $this->language->get('entry_chat_key');
        $data['entry_custom_css'] = $this->language->get('entry_custom_css');
        $data['entry_firebase_server_key'] = $this->language->get('entry_firebase_server_key');
        $data['entry_notification_title'] = $this->language->get('entry_notification_title');
        $data['entry_notification_message'] = $this->language->get('entry_notification_message');
        $data['entry_abandoned_cart_notification_interval'] = $this->language->get('entry_abandoned_cart_notification_interval');
        $data['entry_sr_no'] = $this->language->get('entry_sr_no');
        $data['entry_code'] = $this->language->get('entry_code');
        $data['entry_method_name'] = $this->language->get('entry_method_name');
        $data['entry_action'] = $this->language->get('entry_action');
        $data['entry_select_payment_method'] = $this->language->get('entry_select_payment_method');
        $data['entry_payment_method_name'] = $this->language->get('entry_payment_method_name');
        $data['entry_payment_method_code'] = $this->language->get('entry_payment_method_code');
        $data['entry_mode'] = $this->language->get('entry_mode');
        $data['entry_payment_method_clientid'] = $this->language->get('entry_payment_method_clientid');
        $data['text_sandbox'] = $this->language->get('text_sandbox');
        $data['text_live'] = $this->language->get('text_live');
        $data['button_save'] = $this->language->get('button_save');
        $data['entry_new_product_limit'] = $this->language->get('entry_new_product_limit');
        $data['entry_new_product_date'] = $this->language->get('entry_new_product_date');
		$data['text_select_device'] = $this->language->get('text_select_device');
        $data['text_no_device'] = $this->language->get('text_no_device');
        $data['text_android'] = $this->language->get('text_android');
        $data['text_ios'] = $this->language->get('text_ios');
        $data['text_both'] = $this->language->get('text_both');
        
        $data['entry_app_button_color'] = $this->language->get('entry_app_button_color');
        $data['descp_app_button_color'] = $this->language->get('descp_app_button_color');
        $data['entry_app_theme_color'] = $this->language->get('entry_app_theme_color');
        $data['descp_app_theme_color'] = $this->language->get('descp_app_theme_color');
        $data['entry_select_layout'] = $this->language->get('entry_select_layout');
        $data['entry_tab_bar_enabled'] = $this->language->get('entry_tab_bar_enabled');
        $data['entry_enable_spin_win'] = $this->language->get('entry_enable_spin_win');
        $data['mobile_app_spin_win_hint'] = $this->language->get('mobile_app_spin_win_hint');
        $data['entry_enable_logo'] = $this->language->get('entry_enable_logo');
        $data['entry_image_for_logo'] = $this->language->get('entry_image_for_logo');
        $data['entry_whatsapp_chat_status'] = $this->language->get('entry_whatsapp_chat_status');
        $data['entry_whatsapp_contact'] = $this->language->get('entry_whatsapp_contact');
        $data['descp_whatsapp_contact'] = $this->language->get('descp_whatsapp_contact');
        $data['entry_fingerprint_login'] = $this->language->get('entry_fingerprint_login');
        $data['descp_fingerprint_login'] = $this->language->get('descp_fingerprint_login');
        $data['entry_phone_registration'] = $this->language->get('entry_phone_registration');
        $data['descp_phone_registration'] = $this->language->get('descp_phone_registration');
        $data['entry_mandatory_phone_registration'] = $this->language->get('entry_mandatory_phone_registration');
        $data['descp_mandatory_phone_registration'] = $this->language->get('descp_mandatory_phone_registration');
        
        //Entry titles for slideshow panel
        $data['entry_slideshow_name'] = $this->language->get('entry_slideshow_name');
        $data['entry_slideshow_limit'] = $this->language->get('entry_slideshow_limit');
        $data['entry_slideshow_title'] = $this->language->get('entry_slideshow_title');
        $data['entry_slideshow_link_type'] = $this->language->get('entry_slideshow_link_type');
        $data['entry_slideshow_link_to'] = $this->language->get('entry_slideshow_link_to');
        $data['entry_slideshow_image'] = $this->language->get('entry_slideshow_image');
        $data['entry_slideshow_sort_order'] = $this->language->get('entry_slideshow_sort_order');
        $data['button_slideshow_add'] = $this->language->get('button_slideshow_add');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['button_slideshow_remove'] = $this->language->get('button_slideshow_remove');
        $data['entry_slideshow_image_size'] = $this->language->get('entry_slideshow_image_size');
        $data['entry_slideshow_width'] = $this->language->get('entry_slideshow_width');
        $data['entry_slideshow_height'] = $this->language->get('entry_slideshow_height');

        //Entry title for banner panel
        $data['entry_banner_name'] = $this->language->get('entry_banner_name');
        $data['entry_banner_limit'] = $this->language->get('entry_banner_limit');
        $data['entry_banner_title'] = $this->language->get('entry_banner_title');
        $data['entry_banner_link_type'] = $this->language->get('entry_banner_link_type');
        $data['entry_banner_link_to'] = $this->language->get('entry_banner_link_to');
        $data['entry_banner_image'] = $this->language->get('entry_banner_image');
        $data['entry_banner_sort_order'] = $this->language->get('entry_banner_sort_order');
        $data['button_banner_add'] = $this->language->get('button_banner_add');
        $data['button_banner_remove'] = $this->language->get('button_banner_remove');
        $data['entry_banner_image_size'] = $this->language->get('entry_banner_image_size');
        $data['entry_banner_width'] = $this->language->get('entry_banner_width');
        $data['entry_banner_height'] = $this->language->get('entry_banner_height');

        //Entry for Featured panel
        $data['entry_featured_limit'] = $this->language->get('entry_featured_limit');
        $data['entry_featured_image'] = $this->language->get('entry_featured_image');
        $data['entry_featured_width'] = $this->language->get('entry_featured_width');
        $data['entry_featured_height'] = $this->language->get('entry_featured_height');
        $data['entry_featured_product'] = $this->language->get('entry_featured_product');

        //Entry for Specials panel
        $data['entry_special_limit'] = $this->language->get('entry_special_limit');
        $data['entry_special_image'] = $this->language->get('entry_special_image');
        $data['entry_special_width'] = $this->language->get('entry_special_width');
        $data['entry_special_height'] = $this->language->get('entry_special_height');

        //Entry for Best Sellers
        $data['entry_bestseller_limit'] = $this->language->get('entry_bestseller_limit');
        $data['entry_bestseller_image'] = $this->language->get('entry_bestseller_image');
        $data['entry_bestseller_width'] = $this->language->get('entry_bestseller_width');
        $data['entry_bestseller_height'] = $this->language->get('entry_bestseller_height');

        //Entry for Latest
        $data['entry_latest_limit'] = $this->language->get('entry_latest_limit');
        $data['entry_latest_image'] = $this->language->get('entry_latest_image');
        $data['entry_latest_width'] = $this->language->get('entry_latest_width');
        $data['entry_latest_height'] = $this->language->get('entry_latest_height');

        //Entry for cms pages
        $data['entry_cms_title'] = $this->language->get('entry_cms_title');
        $data['entry_cms_link_to'] = $this->language->get('entry_cms_link_to');
        $data['entry_cms_status'] = $this->language->get('entry_cms_status');
        $data['entry_cms_sort_order'] = $this->language->get('entry_cms_sort_order');
        
        
        $data['entry_tab_general_settings']       = $this->language->get('entry_tab_general_settings');
        $data['entry_tab_homepage_layout']       = $this->language->get('entry_tab_homepage_layout');
        $data['entry_tab_pns']       = $this->language->get('entry_tab_pns');
        $data['entry_tab_payment']       = $this->language->get('entry_tab_payment');
        $data['entry_tab_pnh']       = $this->language->get('entry_tab_pnh');
        $data['entry_tab_cms']       = $this->language->get('entry_tab_cms');
        $data['entry_status_descp']       = $this->language->get('entry_status_descp');
        $data['entry_errorlog_descp']       = $this->language->get('entry_errorlog_descp');
        $data['entry_livechat_descp']       = $this->language->get('entry_livechat_descp');
        $data['entry_apikey_descp']       = $this->language->get('entry_apikey_descp');
        $data['entry_buttontext_color']       = $this->language->get('entry_buttontext_color');
        $data['entry_appbackground_color']       = $this->language->get('entry_appbackground_color');
        $data['entry_redirect_cart']       = $this->language->get('entry_redirect_cart');
        $data['descp_redirect_cart']       = $this->language->get('descp_redirect_cart');
        $data['entry_disabled_shipping']       = $this->language->get('entry_disabled_shipping');
        $data['descp_disabled_shipping']       = $this->language->get('descp_disabled_shipping');
        $data['entry_url_encoding']       = $this->language->get('entry_url_encoding');
        $data['descp_url_encoding']       = $this->language->get('descp_url_encoding');
        $data['error_range'] = $this->language->get('error_range');
        $data['descp_firebase_key'] = $this->language->get('descp_firebase_key');
        $data['descp_order_success_pn'] = $this->language->get('descp_order_success_pn');
        $data['title_order_succes_notification'] = $this->language->get('title_order_succes_notification');
        $data['message_order_succes_notification'] = $this->language->get('message_order_succes_notification');
        $data['entry_order_success_notification'] = $this->language->get('entry_order_success_notification');
        $data['entry_order_update_notification'] = $this->language->get('entry_order_update_notification');
        $data['descp_pn_status'] = $this->language->get('descp_pn_status');
        $data['descp_status_change_notification'] = $this->language->get('descp_status_change_notification');
        $data['descp_status_change_message'] = $this->language->get('descp_status_change_message');
        $data['entry_ab_cart'] = $this->language->get('entry_ab_cart');
        $data['descp_firebase_key'] = $this->language->get('descp_firebase_key');
        $data['descp_sc_message'] = $this->language->get('descp_sc_message');
        $data['descp_status_change_notification'] = $this->language->get('descp_status_change_notification');
        $data['entry_in_hours'] = $this->language->get('entry_in_hours');
        $data['entry_serial_number'] = $this->language->get('entry_serial_number');
        $data['entry_layout_name'] = $this->language->get('entry_layout_name');
        $data['entry_layout_id'] = $this->language->get('entry_layout_id');
        $data['entry_action'] = $this->language->get('entry_action');
        $data['entry_paypal'] = $this->language->get('entry_paypal');
        $data['descp_paypal_status'] = $this->language->get('descp_paypal_status');
        $data['descp_payment_name'] = $this->language->get('descp_payment_name');
        $data['descp_paypal_sandbox'] = $this->language->get('descp_paypal_sandbox');
        $data['descp_cod_status'] = $this->language->get('descp_cod_status');
        $data['entry_cod'] = $this->language->get('entry_cod');
        $data['descp_whatsapp_chat_status'] = $this->language->get('descp_whatsapp_chat_status');
        $data['error_layout_title'] = $this->language->get('error_layout_title');
        $data['success_layout_message'] = $this->language->get('success_layout_message');
        $data['entry_add_layout'] = $this->language->get('entry_add_layout');
        $data['entry_add_layout_name'] = $this->language->get('entry_add_layout_name');
        
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        /*         * *********************** Defining Error variables********************** */
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        //errors for general settings
        if (isset($this->error['firebase_server_key'])) {
            $data['error_firebase_server_key'] = $this->error['firebase_server_key'];
        } else {
            $data['error_firebase_server_key'] = '';
        }

        if (isset($this->error['order_success_title'])) {
            $data['error_order_success_title'] = $this->error['order_success_title'];
        } else {
            $data['error_order_success_title'] = '';
        }

        if (isset($this->error['order_success_message'])) {
            $data['error_order_success_message'] = $this->error['order_success_message'];
        } else {
            $data['error_order_success_message'] = '';
        }

        if (isset($this->error['status_change_title'])) {
            $data['error_status_change_title'] = $this->error['status_change_title'];
        } else {
            $data['error_status_change_title'] = '';
        }

        if (isset($this->error['status_change_message'])) {
            $data['error_status_change_message'] = $this->error['status_change_message'];
        } else {
            $data['error_status_change_message'] = '';
        }

        if (isset($this->error['abandoned_cart_notification_title'])) {
            $data['error_abandoned_cart_notification_title'] = $this->error['abandoned_cart_notification_title'];
        } else {
            $data['error_abandoned_cart_notification_title'] = '';
        }

        if (isset($this->error['abandoned_cart_notification_message'])) {
            $data['error_abandoned_cart_notification_message'] = $this->error['abandoned_cart_notification_message'];
        } else {
            $data['error_abandoned_cart_notification_message'] = '';
        }

        if (isset($this->error['abandoned_cart_notification_interval'])) {
            $data['error_abandoned_cart_notification_interval'] = $this->error['abandoned_cart_notification_interval'];
        } else {
            $data['error_abandoned_cart_notification_interval'] = '';
        }

        //Payment Methids
        if (isset($this->error['paypal_name'])) {
            $data['error_paypal_name'] = $this->error['paypal_name'];
        } else {
            $data['error_paypal_name'] = '';
        }

        if (isset($this->error['paypal_code'])) {
            $data['error_paypal_code'] = $this->error['paypal_code'];
        } else {
            $data['error_paypal_code'] = '';
        }

        if (isset($this->error['cod_name'])) {
            $data['error_cod_name'] = $this->error['cod_name'];
        } else {
            $data['error_cod_name'] = '';
        }

        if (isset($this->error['cod_code'])) {
            $data['error_cod_code'] = $this->error['cod_code'];
        } else {
            $data['error_cod_code'] = '';
        }

        if (isset($this->error['new_product_limit'])) {
            $data['error_new_product_limit'] = $this->error['new_product_limit'];
        } else {
            $data['error_new_product_limit'] = '';
        }
        
        // New Error message
        if (isset($this->error['chat_key'])) {
            $data['error_chat_key'] = $this->error['chat_key'];
        } else {
            $data['error_chat_key'] = '';
        }
        if (isset($this->error['homepage_layout'])) {
            $data['error_homepage_layout'] = $this->error['homepage_layout'];
        } else {
            $data['error_homepage_layout'] = '';
        }
        if (isset($this->error['whatsapp_number'])) {
            $data['error_whatsapp_number'] = $this->error['whatsapp_number'];
        } else {
            $data['error_whatsapp_number'] = '';
        }
        if (isset($this->error['app_button_color'])) {
            $data['error_app_button_color'] = $this->error['app_button_color'];
        } else {
            $data['error_app_button_color'] = '';
        }
        if (isset($this->error['app_background_color'])) {
            $data['error_app_background_color'] = $this->error['app_background_color'];
        } else {
            $data['error_app_background_color'] = '';
        }
        if (isset($this->error['app_theme_color'])) {
            $data['error_app_theme_color'] = $this->error['app_theme_color'];
        } else {
            $data['error_app_theme_color'] = '';
        }
        if (isset($this->error['app_button_text_color'])) {
            $data['error_app_button_text_color'] = $this->error['app_button_text_color'];
        } else {
            $data['error_app_button_text_color'] = '';
        }
        if (isset($this->error['cod_name'])) {
            $data['error_cod_name'] = $this->error['cod_name'];
        } else {
            $data['error_cod_name'] = '';
        }
        if (isset($this->error['paypal_name'])) {
            $data['error_paypal_name'] = $this->error['paypal_name'];
        } else {
            $data['error_paypal_name'] = '';
        }
        if (isset($this->error['paypal_clientid'])) {
            $data['error_paypal_clientid'] = $this->error['paypal_clientid'];
        } else {
            $data['error_paypal_clientid'] = '';
        }

        if (isset($this->error['category_image_width'])) {
            $data['error_category_width'] = $this->error['category_image_width'];
        } else {
            $data['error_category_width'] = '';
        }

        if (isset($this->error['category_image_height'])) {
            $data['error_category_height'] = $this->error['category_image_height'];
        } else {
            $data['error_category_height'] = '';
        }

        if (isset($this->error['product_image_width'])) {
            $data['error_product_width'] = $this->error['product_image_width'];
        } else {
            $data['error_product_width'] = '';
        }

        if (isset($this->error['product_image_height'])) {
            $data['error_product_height'] = $this->error['product_image_height'];
        } else {
            $data['error_product_height'] = '';
        }
        //errors for general settings ends
        //
                //errors for slideshow starts
        if (isset($this->error['slideshow_name'])) {
            $data['error_slideshow_name'] = $this->error['slideshow_name'];
        } else {
            $data['error_slideshow_name'] = '';
        }

        if (isset($this->error['slideshow_limit'])) {
            $data['error_slideshow_limit'] = $this->error['slideshow_limit'];
        } else {
            $data['error_slideshow_limit'] = '';
        }

        if (isset($this->error['slideshow_image'])) {
            $data['error_slideshow_image'] = $this->error['slideshow_image'];
        } else {
            $data['error_slideshow_image'] = array();
        }

        if (isset($this->error['slideshow_image_size'])) {
            $data['error_slideshow_image_size'] = $this->error['slideshow_image_size'];
        } else {
            $data['error_slideshow_image_size'] = '';
        }
        //errors for slideshow ends
        //
                //errors for banner starts
        if (isset($this->error['banner_name'])) {
            $data['error_banner_name'] = $this->error['banner_name'];
        } else {
            $data['error_banner_name'] = '';
        }

        if (isset($this->error['banner_limit'])) {
            $data['error_banner_limit'] = $this->error['banner_limit'];
        } else {
            $data['error_banner_limit'] = '';
        }

        if (isset($this->error['banner_image'])) {
            $data['error_banner_image'] = $this->error['banner_image'];
        } else {
            $data['error_banner_image'] = array();
        }

        if (isset($this->error['banner_image_size'])) {
            $data['error_banner_image_size'] = $this->error['banner_image_size'];
        } else {
            $data['error_banner_image_size'] = '';
        }
        //errors for banner ends
        //
                //errors for featured starts
        if (isset($this->error['featured_limit'])) {
            $data['error_featured_limit'] = $this->error['featured_limit'];
        } else {
            $data['error_featured_limit'] = '';
        }

        if (isset($this->error['featured_image'])) {
            $data['error_featured_image'] = $this->error['featured_image'];
        } else {
            $data['error_featured_image'] = '';
        }
        //errors for featured ends
        //
                //errors for specials starts                
        if (isset($this->error['special_limit'])) {
            $data['error_special_limit'] = $this->error['special_limit'];
        } else {
            $data['error_special_limit'] = '';
        }

        if (isset($this->error['special_image'])) {
            $data['error_special_image'] = $this->error['special_image'];
        } else {
            $data['error_special_image'] = '';
        }
        //errors for specials ends
        //
                //errors for best sellers starts
        if (isset($this->error['bestseller_limit'])) {
            $data['error_bestseller_limit'] = $this->error['bestseller_limit'];
        } else {
            $data['error_bestseller_limit'] = '';
        }

        if (isset($this->error['bestseller_image'])) {
            $data['error_bestseller_image'] = $this->error['bestseller_image'];
        } else {
            $data['error_bestseller_image'] = '';
        }
        //errors for best seller ends
        //
                //errors for latest starts
        if (isset($this->error['latest_limit'])) {
            $data['error_latest_limit'] = $this->error['latest_limit'];
        } else {
            $data['error_latest_limit'] = '';
        }

        if (isset($this->error['latest_image'])) {
            $data['error_latest_image'] = $this->error['latest_image'];
        } else {
            $data['error_latest_image'] = '';
        }
        //errors for latest ends
        //
                //errors for cms pages starts
        if (isset($this->error['cms'])) {
            $data['error_cms_title'] = $this->error['cms'];
        } else {
            $data['error_cms_title'] = array();
        }
        //errors for cms pages ends
        $this->load->model('tool/image');

        /*         * ********getting slideshow images code starts *********** */
        if (isset($this->request->post['slideshow_image'])) {
            $slideshow_images = $this->request->post['slideshow_image'];
        } else if ($data['slideshow_id'] != '') {
            $slideshow_images = $this->model_webservice_webservice->getSlideshowImages($data['slideshow_id']);
        } else {
            $slideshow_images = array();
        }

        $data['slideshow_images'] = array();

        if (count($slideshow_images) > 0) {
            $data['category_array'] = $this->selectCategoryFilter();
            $data['product_array'] = $this->selectProductFilter();
        }

        foreach ($slideshow_images as $slideshow_image) {
            if (is_file(DIR_IMAGE . $slideshow_image['image'])) {
                $image = $slideshow_image['image'];
                $thumb = $slideshow_image['image'];
            } else {
                $image = '';
                $thumb = 'no_image.png';
            }

            $data['slideshow_images'][] = array(
                'slideshow_image_description' => $slideshow_image['slideshow_image_description'],
                'linktype' => $slideshow_image['link_type'],
                'linkto' => $slideshow_image['link_to'],
                'image' => $image,
                'thumb' => $this->model_tool_image->resize($thumb, 100, 100),
                'sort_order' => $slideshow_image['sort_order']
            );
        }


        //getting slideshow images code ends
        //
                //getting banner images code starts

        if (isset($this->request->post['banner_image'])) {
            $banner_images = $this->request->post['banner_image'];
        } else if ($data['banner_id'] != '') {
            $banner_images = $this->model_webservice_webservice->getBannerImages($data['banner_id']);
        } else {
            $banner_images = array();
        }

        $data['banner_images'] = array();

        if (count($banner_images) > 0) {
            $data['banner_category_array'] = $this->selectCategoryFilter();
            $data['banner_product_array'] = $this->selectProductFilter();
        }

        foreach ($banner_images as $banner_image) {
            if (is_file(DIR_IMAGE . $banner_image['image'])) {
                $image = $banner_image['image'];
                $thumb = $banner_image['image'];
            } else {
                $image = '';
                $thumb = 'no_image.png';
            }

            $data['banner_images'][] = array(
                'banner_image_description' => $banner_image['banner_image_description'],
                'linktype' => $banner_image['link_type'],
                'linkto' => $banner_image['link_to'],
                'image' => $image,
                'thumb' => $this->model_tool_image->resize($thumb, 100, 100),
                'sort_order' => $banner_image['sort_order']
            );
        }

        //getting banner images code ends
        //                
        //getting informations code starts

        $this->load->model('catalog/information');

        $data['informations'] = array();

        foreach ($this->model_catalog_information->getInformations() as $result) {
            $data['informations'][] = array(
                'title' => $result['title'],
                'information_id' => $result['information_id']
            );
        }
        $cms_pages = $this->model_webservice_webservice->getCmsPages();
        if (isset($this->request->post['cms'])) {
            $data['cms'] = $this->request->post['cms'];
        } else if (isset($cms_pages) && !empty($cms_pages)) {
            $data['cms'] = $cms_pages;
        } else {
            $data['cms'] = array();
        }
        
        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/webservice', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );


        $data['action'] = $this->url->link('extension/module/webservice', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['filter_title'] = $filter_title;
        $data['filter_redirect_activity'] = $filter_redirect_activity;
        $data['filter_from_date'] = $filter_from_date;
        $data['filter_to_date'] = $filter_to_date;
        
        $this->document->addScript('view/javascript/mab_layout/jquery.growl.js');
        $this->document->addStyle('view/stylesheet/mab_layout/jquery.growl.css');
        
        $data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['sendNotificationUrl'] = str_replace('amp;', '', $this->url->link('extension/module/webservice/sendNotification', 'user_token=' . $this->session->data['user_token'], 'SSL'));

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['available_payment_methods'] = array(array('name' => 'Paypal', "code" => "paypal"), array('name' => 'Cash On Delivery', "code" => "cod"));

        /*start:changes done by aayushi on 1st May 2020 to add preview changes*/
        $general_settings_data = array();
        $general_settings_data['app_theme_color'] = $settings['webservice']['app_theme_color'];
        $general_settings_data['logo_status'] = $settings['webservice']['logo_status'];
        $general_settings_data['live_app_gif'] = $settings['webservice']['logo_status'];
        $general_settings_data['image_for_logo'] = $settings['webservice']['image_for_logo'];
        $general_settings_data['entry_home'] = $this->language->get('Home');
        $general_settings_data['whatsapp_chat_status'] = $settings['webservice']['whatsapp_chat_status'];
 	$this->document->addStyle('view/stylesheet/mab_layout/layout_preview.css');
 	$this->response->setOutput($this->load->view('extension/module/webservice', $data).$this->load->view('extension/module/general_settings_preview', $general_settings_data));
        
    }
    public function deleteLayout() {
        if(isset($this->request->get['layout_id'])) {
            $this->db->query('DELETE FROM '.DB_PREFIX.'kb_mobileapp_layouts where id_layout="'.$this->request->get['layout_id'].'"');
            $query = $this->db->query('SELECT id_component  FROM '.DB_PREFIX.'kb_mobileapp_layout_component where id_layout="'.$this->request->get['layout_id'].'"');
            foreach($query->rows as $component_id){
                $this->db->query('DELETE FROM '.DB_PREFIX.'kbmobileapp_banners where id_component="'.$component_id['id_component'].'"');
                $this->db->query('DELETE FROM '.DB_PREFIX.'kbmobileapp_top_category where id_component="'.$component_id['id_component'].'"');
                $this->db->query('DELETE FROM '.DB_PREFIX.'kbmobileapp_product_data where id_component="'.$component_id['id_component'].'"');
            }
            $this->db->query('DELETE FROM '.DB_PREFIX.'kbb_components_heading where id_layout="'.$this->request->get['layout_id'].'"');
            $this->db->query('DELETE FROM '.DB_PREFIX.'kb_mobileapp_layout_component where id_layout="'.$this->request->get['layout_id'].'"');
            $this->response->redirect($this->url->link('extension/module/webservice', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        } else {
            $this->response->redirect($this->url->link('extension/module/webservice', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        }
    }
    public function deleteImage() {
        $this->db->query('DELETE FROM '.DB_PREFIX.'kbmobileapp_banners where id_component="'.$this->request->post['id_component'].'" and id="'.$this->request->post['banner_id'].'"');
        $this->db->query('DELETE FROM '.DB_PREFIX.'kbb_components_heading where id_component="'.$this->request->post['id_component'].'"');
    }
    
    public function saveHeading() {
        if(isset($this->request->post['heading'])) {
            $this->db->query('DELETE FROM '.DB_PREFIX.'kbb_components_heading WHERE id_component = "'.(int)$this->request->post['id_component'].'" and language_id = "'.(int)$this->request->post['language_id'].'" ');
            $this->db->query('INSERT INTO '.DB_PREFIX.'kbb_components_heading(id_layout,id_component,heading,language_id) VALUES ("'.(int)$this->request->post['id_layout'].'","'.(int)$this->request->post['id_component'].'","'.$this->request->post['heading'].'","'.(int)$this->request->post['language_id'].'")');
        }
    }
    
    public function saveLayout() {
            if(isset($this->request->post['name'])) {
                $this->db->query('INSERT INTO '.DB_PREFIX.'kb_mobileapp_layouts (layout_name) VALUES ("'.$this->request->post['name'].'")');
                $id_layout=$this->db->getLastId();
                echo $id_layout; die;
            } else {
                $this->response->redirect($this->url->link('extension/module/webservice', 'user_token=' . $this->session->data['user_token'], 'SSL'));
            }
        
    }
    public function deleteComponent() {
        if(isset($this->request->get['deleteTopcategorycomponent'])&& $this->request->get['deleteTopcategorycomponent']=='true') {
            $this->db->query('DELETE FROM '.DB_PREFIX.'kb_mobileapp_layout_component where id_layout="'.$this->request->post['id_layout'].'" and id_component="'.$this->request->post['id_component'].'"');
            $this->db->query('DELETE FROM '.DB_PREFIX.'kbmobileapp_top_category where id_component="'.$this->request->post['id_component'].'"');
            $this->db->query('DELETE FROM '.DB_PREFIX.'kbb_components_heading where id_component="'.$this->request->post['id_component'].'"');
            
        } else if(isset($this->request->get['deleteBannercomponent'])&& $this->request->get['deleteBannercomponent']=='true') {
            $this->db->query('DELETE FROM '.DB_PREFIX.'kb_mobileapp_layout_component where id_layout="'.$this->request->post['id_layout'].'" and id_component="'.$this->request->post['id_component'].'"');
            $this->db->query('DELETE FROM '.DB_PREFIX.'kbmobileapp_banners where id_component="'.$this->request->post['id_component'].'"');
            $this->db->query('DELETE FROM '.DB_PREFIX.'kbb_components_heading where id_component="'.$this->request->post['id_component'].'"');
            
        } else if(isset($this->request->get['deleteProductcomponent'])&& $this->request->get['deleteProductcomponent']=='true') {
            $this->db->query('DELETE FROM '.DB_PREFIX.'kb_mobileapp_layout_component where id_layout="'.$this->request->post['id_layout'].'" and id_component="'.$this->request->post['id_component'].'"');
            $this->db->query('DELETE FROM '.DB_PREFIX.'kbmobileapp_product_data where id_component="'.$this->request->post['id_component'].'"');
            $this->db->query('DELETE FROM '.DB_PREFIX.'kbb_components_heading where id_component="'.$this->request->post['id_component'].'"');
            
        } else {
            $this->db->query('DELETE FROM '.DB_PREFIX.'kb_mobileapp_layout_component where id_layout="'.$this->request->post['id_layout'].'" and id_component="'.$this->request->post['id_component'].'"');
            $this->db->query('DELETE FROM '.DB_PREFIX.'kbb_components_heading where id_component="'.$this->request->post['id_component'].'"');
        }
    }
    public function getBestSellerProducts($number_of_products=10,$image_content_mode=1,$setting_data) {
        
        $this->load->model('webservice/webservice');
        $this->load->model('setting/setting');

        $store_id = $this->config->get('config_store_id');
        $webservice_setting = $this->model_setting_setting->getSetting('webservice', $store_id);

        $productInfoData = $this->model_webservice_webservice->getBestSellerProducts($number_of_products);

            if (!empty($productInfoData)) {
                foreach ($productInfoData as $productInfo) {

                    $image = HTTPS_CATALOG.'image/'.$productInfo['image'];
                    //Check if webp image exists
                    $info = pathinfo($image);
                    if (isset($info['extension'])) {
                        $directory = str_replace(HTTP_SERVER . 'image/cache/', DIR_IMAGE . 'cache/', $info['dirname']);
                        if (file_exists($directory . '/' . $info['filename'] . '.webp')) {
                            $image = $info['dirname'] . '/' . $info['filename'] . '.webp';
                        }
                    }
                    if (isset($productInfo['special'])) {
                        if($productInfo['price']>0) {
                            $discount_price = $this->currency->format($this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                            $act_price = $this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                            $disc_price = $this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                            $discount_perc = (($act_price - $disc_price) / $act_price) * 100;
                            $discount_percentage = number_format($discount_perc, 2);
                        } else{
                            $productInfo['price']=$productInfo['special'];
                            $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                            $discount_percentage = 0;
                            
                        }
                    } else {
                        $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                        $discount_percentage = 0;
                    }

                    if ($productInfo['date_added'] >= $setting_data['new_product_start_date']) {
                        $is_new = 1;
                    } else {
                        $is_new = 0;
                    }
                    $data['products'][] = array(
                        'id' => $productInfo['product_id'],
                        'name' => html_entity_decode(str_replace('&quot;', '"', $productInfo['name'])),
                        'available_for_order' => 1,
                        'new_products' => $is_new,
                        'on_sale_products' => $discount_percentage ? 1 : 0,
                        'ClickActivityName' => 'ProductActivity',
                        'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency')),
                        'src' => $image,
                        'discount_price' => $discount_price,
                        'discount_percentage' => $discount_percentage,
                        'image_contentMode' => $image_content_mode,
                    );
                }
            }
        return $data['products'];
    }
    
    public function getLatestProducts($number_of_products=10,$image_content_mode=1,$setting_data) {
        
        $this->load->model('setting/setting');
        $this->load->model('catalog/product');
        $store_id = $this->config->get('config_store_id');
        $webservice_setting = $this->model_setting_setting->getSetting('webservice', $store_id);
        
        //print_r($setting_data); die;
        
        $filter_data = array(
            'sort' => 'p.date_added',
            'order' => 'DESC',
            'start' => 0,
            'limit' => $number_of_products
        );

        $productInfoData = $this->model_catalog_product->getProducts($filter_data);

        if (!empty($productInfoData)) {
            $i = 0;
            foreach ($productInfoData as $productInfo) {
                if($i<$number_of_products) {
                    $image = HTTPS_CATALOG.'image/'.$productInfo['image'];
                    //Check if webp image exists
                    $info = pathinfo($image);
                    if (isset($info['extension'])) {
                        $directory = str_replace(HTTP_SERVER . 'image/cache/', DIR_IMAGE . 'cache/', $info['dirname']);
                        if (file_exists($directory . '/' . $info['filename'] . '.webp')) {
                            $image = $info['dirname'] . '/' . $info['filename'] . '.webp';
                        }
                    }

                    if (isset($productInfo['special'])) {
                        if($productInfo['price']>0) {
                            $discount_price = $this->currency->format($this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                            $act_price = $this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                            $disc_price = $this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                            $discount_perc = (($act_price - $disc_price) / $act_price) * 100;
                            $discount_percentage = number_format($discount_perc, 2);
                        } else{
                            $productInfo['price']=$productInfo['special'];
                            $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                            $discount_percentage = 0;

                        }
                    } else {
                        $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                        $discount_percentage = 0;
                    }
                    if ($productInfo['date_added'] >= $setting_data['new_product_start_date']) {
                        $is_new = 1;
                    } else {
                        $is_new = 0;
                    }
                    $data['products'][] = array(
                        'id' => $productInfo['product_id'],
                        'name' => html_entity_decode(str_replace('&quot;', '"', $productInfo['name'])),
                        'available_for_order' => 1,
                        'new_products' => $is_new,
                        'on_sale_products' => $discount_percentage ? 1 : 0,
                        //'category_name' => html_entity_decode(isset($parent_category['name']) ? $parent_category['name'] : ''),
                        'ClickActivityName' => 'ProductActivity',
                        'category_id' => isset($parent_category['category_id']) ? $parent_category['category_id'] : '',
                        'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency')),
                        'src' => $image,
                        'discount_price' => $discount_price,
                        'discount_percentage' => $discount_percentage,
                        //'is_in_wishlist' => $this->isInWishlist($productInfo['product_id']),
                        'image_contentMode' => $image_content_mode,
                    );
                } else {
                    break;
                }
            }
        }
        return $data['products'];
    }
    
    public function getProductsComponentData($id_component){
        $setting_data = $this->config->get('webservice');
        $query = $this->db->query('Select * from  ' . DB_PREFIX . 'kbmobileapp_product_data where id_component =' . (int) $id_component);
        $product_data = $query->row;
        if (count($product_data) > 0) {
            $product_type = $product_data['product_type'];
            $number_of_products = $product_data['number_of_products'];
            $image_content_mode = $product_data['image_content_mode']?'scaleAspectFit':'scaleAspectFill';
            $products = array();
            if ($product_type == 'best_seller') {
                $products = $this->getBestSellerProducts($number_of_products, $image_content_mode,$setting_data);
            } elseif ($product_type == 'new_products') {
                $products = $this->getLatestProducts($number_of_products,$image_content_mode,$setting_data);
            } elseif ($product_type == 'featured_products') {
                $products = $this->getFeaturedProducts($number_of_products,$image_content_mode,$setting_data);
            } elseif ($product_type == 'special_products') {
                $products = $this->getSpecialProducts($number_of_products,$image_content_mode,$setting_data);
            } elseif ($product_type == 'from_category') {
                $product_list = array();
                $product_list = explode(',', $product_data['category_products']);
                $products = $this->getProducts($product_list, $number_of_products, $image_content_mode);
            } elseif ($product_type == 'custom_product') {
                $product_list = array();
                $product_list = explode(',', $product_data['custom_products']);
                $products = $this->getProducts($product_list, $number_of_products, $image_content_mode);
            }
            $sliced_product = array();
            $sliced_product = array_slice($products, 0, $number_of_products);
            return $sliced_product;
        }
    }
    
    public function getProducts($product_list, $number_of_products, $image_content_mode)
    {
        $data = array();
        $results = array();
        $products = array();
        
        
        $this->load->model('catalog/product');
        $this->load->model('setting/setting');
        $i=0;
        $store_id = $this->config->get('config_store_id');
        $setting_data = $this->config->get('webservice');
        $webservice_setting = $this->model_setting_setting->getSetting('webservice', $store_id);
        
        foreach($product_list as $product){
            if($i<$number_of_products) {
                $productInfo = $this->model_catalog_product->getProduct($product);
                if ($productInfo) {
                     $image = HTTPS_CATALOG.'image/'.($productInfo['image']);
                    //Check if webp image exists
                    $info = pathinfo($image);
                    if (isset($info['extension'])) {
                        $directory = str_replace(HTTP_SERVER . 'image/cache/', DIR_IMAGE . 'cache/', $info['dirname']);
                        if (file_exists($directory . '/' . $info['filename'] . '.webp')) {
                            $image = $info['dirname'] . '/' . $info['filename'] . '.webp';
                        }
                    }

                    if (isset($productInfo['special'])) {
                        if($productInfo['price']>0) {
                            $discount_price = $this->currency->format($this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                            $act_price = $this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                            $disc_price = $this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                            $discount_perc = (($act_price - $disc_price) / $act_price) * 100;
                            $discount_percentage = number_format($discount_perc, 2);
                        } else{
                            $productInfo['price']=$productInfo['special'];
                            $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                            $discount_percentage = 0;

                        }
                    } else {
                        $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                        $discount_percentage = 0;
                    }

                    
                    if ($productInfo['date_added'] >= $setting_data['new_product_start_date']) {
                        $is_new = 1;
                    } else {
                        $is_new = 0;
                    }
                
                    $products[$i] = array(
                        'id' => $productInfo['product_id'],
                        //'is_in_wishlist' => $this->isInWishlist($productInfo['product_id']),
                        'name' => html_entity_decode(str_replace('&quot;', '"', $productInfo['name'])),
                        'available_for_order' => 1,
                        'new_products' => $is_new,
                        'on_sale_products' => $discount_percentage ? 1 : 0,
                        //'category_name' => html_entity_decode(isset($parent_category['name']) ? $parent_category['name'] : ''),
                        'image_contentMode' => $image_content_mode,
                        'ClickActivityName' => 'ProductActivity',
                        //'category_id' => isset($parent_category['category_id']) ? $parent_category['category_id'] : '',
                        'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency')),
                        'src' => $image,
                        'discount_price' => $discount_price,
                        'discount_percentage' => $discount_percentage,
                    );
                }
            } else {
                break;
            }
            $i++;
        }
        
        return $products;
    }
    
    public function getSpecialProducts($number_of_products=10,$image_content_mode=1,$setting_data) {
    
        $webservice_setting = $this->model_setting_setting->getSetting('webservice', $store_id);

            $filter_data = array(
                'sort' => 'pd.name',
                'order' => 'ASC',
                'start' => 0,
                'limit' => $number_of_products
            );
            $this->load->model('catalog/product');
            $productInfoData = $this->model_catalog_product->getProductSpecials($filter_data);

            if (!empty($productInfoData)) {
                foreach ($productInfoData as $productInfo) {

                    $image = HTTPS_CATALOG.'image/'.($productInfo['image']);
                    //Check if webp image exists
                    $info = pathinfo($image);
                    if (isset($info['extension'])) {
                        $directory = str_replace(HTTP_SERVER . 'image/cache/', DIR_IMAGE . 'cache/', $info['dirname']);
                        if (file_exists($directory . '/' . $info['filename'] . '.webp')) {
                            $image = $info['dirname'] . '/' . $info['filename'] . '.webp';
                        }
                    }
                    
                    if (isset($productInfo['special'])) {
                        if($productInfo['price']>0) {
                            $discount_price = $this->currency->format($this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                            $act_price = $this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                            $disc_price = $this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                            $discount_perc = (($act_price - $disc_price) / $act_price) * 100;
                            $discount_percentage = number_format($discount_perc, 2);
                        } else{
                            $productInfo['price']=$productInfo['special'];
                            $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                            $discount_percentage = 0;
                            
                        }
                    } else {
                        $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                        $discount_percentage = 0;
                    }

                    if ($productInfo['date_added'] >= $setting_data['new_product_start_date']) {
                        $is_new = 1;
                    } else {
                        $is_new = 0;
                    }
                    $data['products'][] = array(
                        'id' => $productInfo['product_id'],
                        'name' => str_replace('&quot;', '"', $productInfo['name']),
                        'available_for_order' => 1,
                        'new_products' => $is_new,
                        'on_sale_products' => $discount_percentage ? 1 : 0,
                        //'category_name' => html_entity_decode(isset($parent_category['name']) ? $parent_category['name'] : ''),
                        'ClickActivityName' => 'ProductActivity',
                        //'category_id' => isset($parent_category['category_id']) ? $parent_category['category_id'] : '',
                        'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency')),
                        'src' => $image,
                        'discount_price' => $discount_price,
                        'discount_percentage' => $discount_percentage,
                        //'is_in_wishlist' => $this->isInWishlist($productInfo['product_id']),
                        'image_contentMode' => $image_content_mode,
                    );
                }
            }
        return $data['products'];
    }
    public function layout() {
        
        ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
        
        $data = array();
        
        $this->load->language('extension/module/webservice');
        
        
        
        $this->document->addStyle('view/stylesheet/mab_layout/jquery.growl.css');
        $this->document->addStyle('view/stylesheet/mab_layout/admin-theme.css');
        $this->document->addStyle('view/stylesheet/mab_layout/layout.css');
        $this->document->addStyle('view/stylesheet/mab_layout/layout_preview.css');
        $this->document->addStyle('view/stylesheet/mab_layout/CustomScrollbar.css');
        $this->document->addScript('view/javascript/mab_layout/jquery.growl.js');
        $this->document->addScript('view/javascript/mab_layout/jquery-ui.min.js');
        $this->document->addScript('view/javascript/mab_layout/CustomScrollbar.min.js');
        
        $data['heading_title'] = $this->language->get('heading_title');
        
        $data['user_token'] = $this->session->data['user_token'];
        if($this->request->get['layout_id']) {
            $data['id_layout'] = $this->request->get['layout_id'];
        } else {
            $this->response->redirect($this->url->link('extension/module/webservice', '', 'SSL'));
        }
        $this->document->addScript('view/javascript/velovalidation.js');
        
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
           unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/webservice', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );
        
        $data['breadcrumbs'][] = array(
            'text' => 'Home Page Layout',
            'href' => $this->url->link('extension/module/webservice/layout', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );
        
        $this->load->model('webservice/webservice');
        $components = array();
        $components = $this->model_webservice_webservice->getComponents($data['id_layout']);
        if(!empty($components)) {
            foreach($components as $component) {
                $query = $this->db->query('Select id_component_type from ' . DB_PREFIX . 'kb_mobileapp_layout_component where id_component = '.  $component['id_component']) ;
                $component_type_id = $query->row['id_component_type'];
                $query = $this->db->query('Select component_name from ' . DB_PREFIX . 'kbmobileapp_component_types where id = '.  $component_type_id) ;
                $component_type = $query->row['component_name'];
                $products = array();
                $component_data = array();
                if ($component_type == 'top_category') {
                    $component_data = $this->model_webservice_webservice->getTopCategoryData($component['id_component']);
                } else if($component_type == 'products_square' || $component_type == 'products_grid' || $component_type == 'products_horizontal'){
                    $component_data = $this->model_webservice_webservice->getProductsByComponent($component['id_layout'],$component['id_component']);
                    $component_data[0]['heading'] = $this->model_webservice_webservice->getComponentHeading($component['id_layout'],$component['id_component']);
                    $products = $this->getProductsComponentData($component['id_component']);
                } else if($component_type == 'banner_square' || $component_type == 'banners_countdown' || $component_type == 'banners_grid' || $component_type == 'banner_horizontal_slider'){
                    $component_data = $this->model_webservice_webservice->getBannerByComponent($component['id_layout'],$component['id_component']);
                    $component_data[0]['heading'] = $this->model_webservice_webservice->getComponentHeading($component['id_layout'],$component['id_component']);
                } 
                $data['components'][] = array(
                    'id_component' => $component['id_component'],
                    'id_layout' => $component['id_layout'],
                    'component_type' => $this->model_webservice_webservice->getComponentTypeByID($component['id_component_type']),
                    'component_heading' => @$component['component_heading'],
                    'data' => $component_data,
                    'product_data' =>  $products  
                );
            }
        }
        $setting_data = $this->config->get('webservice');
        $data['recent_products'] = $this->getLatestProducts(3,'scaleAspectFit',$setting_data);
        $data['number_of_components'] = count($components);
        $this->load->model('catalog/category');
        $this->load->model('localisation/language');
        
        
        $this->load->model('tool/image');
        
        $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        
        $data['entry_serial_number'] = $this->language->get('entry_serial_number');
        $data['entry_component_heading'] = $this->language->get('entry_component_heading');
        $data['entry_tab_top_categories'] = $this->language->get('entry_tab_top_categories');
        $data['entry_tab_banner_square'] = $this->language->get('entry_tab_banner_square');
        $data['entry_tab_banner_grid'] = $this->language->get('entry_tab_banner_grid');
        $data['entry_tab_banner_countdown'] = $this->language->get('entry_tab_banner_countdown');
        $data['entry_tab_banner_sliding'] = $this->language->get('entry_tab_banner_sliding');
        $data['entry_tab_product_square'] = $this->language->get('entry_tab_product_square');
        $data['entry_tab_product_horizontal'] = $this->language->get('entry_tab_product_horizontal');
        $data['entry_tab_product_grid'] = $this->language->get('entry_tab_product_grid');
        $data['entry_tab_product_recent'] = $this->language->get('entry_tab_product_recent');
        $data['entry_edit_component'] = $this->language->get('entry_edit_component');
        $data['select_scale_aspect_fit'] = $this->language->get('select_scale_aspect_fit');
        $data['select_scale_aspect_fill'] = $this->language->get('select_scale_aspect_fill');
        $data['entry_image_content_mode'] = $this->language->get('entry_image_content_mode');
        $data['entry_select_category'] = $this->language->get('entry_select_category');
        $data['entry_select_first_category'] = $this->language->get('entry_select_first_category');
        $data['entry_select_second_category'] = $this->language->get('entry_select_second_category');
        $data['entry_select_third_category'] = $this->language->get('entry_select_third_category');
        $data['entry_select_fourth_category'] = $this->language->get('entry_select_fourth_category');
        $data['entry_select_fifth_category'] = $this->language->get('entry_select_fifth_category');
        $data['entry_select_sixth_category'] = $this->language->get('entry_select_sixth_category');
        $data['entry_select_seventh_category'] = $this->language->get('entry_select_seventh_category');
        $data['entry_select_eigth_category'] = $this->language->get('entry_select_eigth_category');
        $data['entry_select_redirect_activity'] = $this->language->get('entry_select_redirect_activity');
        $data['entry_heading'] = $this->language->get('entry_heading');
        $data['entry_category_image'] = $this->language->get('entry_category_image');
        $data['column_image'] = $this->language->get('column_image');
        $data['column_redirect'] = $this->language->get('column_redirect');
        $data['column_category_id'] = $this->language->get('column_category_id');
        $data['column_product_id'] = $this->language->get('column_product_id');
        $data['column_delete'] = $this->language->get('column_delete');
        $data['select_home'] = $this->language->get('select_home');
        $data['select_category'] = $this->language->get('select_category');
        $data['select_product'] = $this->language->get('select_product');
        $data['entry_link_to'] = $this->language->get('entry_link_to');
        $data['select_link_type_first'] = $this->language->get('select_link_type_first');
        $data['entry_number_of_products'] = $this->language->get('entry_number_of_products');
        $data['select_product_type'] = $this->language->get('select_product_type');
        $data['select_best_seller_product'] = $this->language->get('select_best_seller_product');
        $data['select_featured_product'] = $this->language->get('select_featured_product');
        $data['select_new_products'] = $this->language->get('select_new_products');
        $data['select_from_category'] = $this->language->get('select_from_category');
        $data['select_custom_product'] = $this->language->get('select_custom_product');
        $data['entry_enabled'] = $this->language->get('entry_enabled');
        $data['entry_disabled'] = $this->language->get('entry_disabled');
        $data['entry_back_color'] = $this->language->get('entry_back_color');
        $data['entry_timer_back_color'] = $this->language->get('entry_timer_back_color');
        $data['entry_time_text_color'] = $this->language->get('entry_time_text_color');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['success_message_js'] =  $this->language->get('success_message_js');
        $data['min_category_limit_js'] =  $this->language->get('min_category_limit_js');
        $data['error_check_message_js'] =  $this->language->get('error_check_message_js');     
        $data['component_add_js'] =  $this->language->get('component_add_js');
        $data['component_delete'] =  $this->language->get('component_delete');
        $data['limit_reached'] =  $this->language->get('limit_reached');
        $data['position_update'] =  $this->language->get('position_update');
        $data['banner_delete_message'] =  $this->language->get('banner_delete_message');
        $data['banner_success_message'] =  $this->language->get('banner_success_message');
        $data['select_category_error'] =  $this->language->get('select_category_error');
        $data['empty_heading_error'] =  $this->language->get('empty_heading_error');
        $data['success_heading_message'] =  $this->language->get('success_heading_message');
        
        $data['error_text_color'] = $this->language->get('error_text_color');
        $data['error_background_color'] = $this->language->get('error_background_color');
        $data['error_countdown'] = $this->language->get('error_countdown');
        $data['select_redirect_activity_error'] = $this->language->get('select_redirect_activity_error');
        
        $results = $this->model_catalog_category->getCategories();

        $this->load->model('setting/setting');
        $settings = $this->model_setting_setting->getSetting('webservice', 0);
        
        if (empty($settings['webservice'])) {
            $data['custom_css'] = '';
            $data['category_image_width'] = '200';
            $data['category_image_height'] = '200';
            $data['product_image_width'] = '200';
            $data['product_image_height'] = '200';
            $data['whatsapp_chat_status'] = 0;
            $data['tab_bar_enabled'] = 1;
            $data['mobile_app_spin_win'] = 0;
            $data['fingerprint_login_status'] = 0;
            $data['phone_registration_status'] = 0;
            $data['mandatory_phone_registration_status'] = 0;
            $data['whatsapp_phone_number'] = '';
            $data['app_button_color'] = '#00a781';
            $data['app_theme_color'] = '#c3000f';
            $data['app_button_text_color'] = '#ffffff';
            $data['app_background_color'] = '#ffffff';
            $data['logo_status'] = 0;
            $data['image_for_logo'] = '';
            $data['redirect_cart_status'] = 0;
            $data['image_for_logo'] = '';
        } else {
            $data['webservice_status'] = $settings['webservice']['webservice_status'];
            $data['webservice_log_status'] = $settings['webservice']['webservice_log_status'];
            $data['webservice_chat_status'] = $settings['webservice']['webservice_chat_status'];
            $data['category_image_width'] = $settings['webservice']['category_image_width'];
            $data['category_image_height'] = $settings['webservice']['category_image_height'];
            $data['product_image_width'] = $settings['webservice']['product_image_width'];
            $data['product_image_height'] = $settings['webservice']['product_image_height'];
            $data['whatsapp_chat_status'] = $settings['webservice']['whatsapp_chat_status'];
            if(isset($settings['webservice']['tab_bar_enabled'])){
                $data['tab_bar_enabled'] = $settings['webservice']['tab_bar_enabled'];
            }else{
                $data['tab_bar_enabled'] = 0;
            }
            if(isset($settings['webservice']['mobile_app_spin_win'])){
                $data['mobile_app_spin_win'] = $settings['webservice']['mobile_app_spin_win'];
            }else{
                $data['mobile_app_spin_win'] = 0;
            }
            $data['fingerprint_login_status'] = $settings['webservice']['fingerprint_login_status'];
            $data['phone_registration_status'] = $settings['webservice']['phone_registration_status'];
            $data['mandatory_phone_registration_status'] = $settings['webservice']['mandatory_phone_registration_status'];
            $data['whatsapp_phone_number'] = $settings['webservice']['whatsapp_phone_number'];
            $data['app_button_color'] = $settings['webservice']['app_button_color'];
            $data['app_theme_color'] = $settings['webservice']['app_theme_color'];
            $data['app_button_text_color'] = $settings['webservice']['app_button_text_color'];
            $data['app_background_color'] = $settings['webservice']['app_background_color'];
            $data['logo_status'] = $settings['webservice']['logo_status'];
            $data['redirect_cart_status'] = $settings['webservice']['redirect_cart_status'];
            $data['url_encoding_status'] = $settings['webservice']['url_encoding_status'];
            $data['image_for_logo'] = $settings['webservice']['image_for_logo'];
            $data['entry_home'] = $this->language->get('entry_home');
        }
        
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        foreach($data['languages'] as $language) {
            if($language['status']==1) {
                $data['active_languages'][] = $language['language_id'];
            }
        }
        
        foreach ($results as $result) {
            $response[] = array(
                'category_id' => $result['category_id'],
                'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
            );
        }
        $sort_order = array();
        foreach ($response as $key => $value) {
            $sort_order[$key] = $value['name'];
        }
        array_multisort($sort_order, SORT_ASC, $response);
        
        $data['categories'] = $response;
        
        $data['https_catalog'] = HTTPS_CATALOG;
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['cancel'] = $this->url->link('extension/module/webservice', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $this->response->setOutput($this->load->view('extension/module/webservice_layout',$data));
    }

    protected function validate() {


        if (!$this->user->hasPermission('modify', 'extension/module/webservice')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
        if (!$this->request->post['webservice']['category_image_width']) {
            $this->error['category_image_width'] = $this->language->get('error_width');
        } else if (filter_var($this->request->post['webservice']['category_image_width'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 999))) === false) {
            $this->error['category_image_width'] = $this->language->get('error_width_not_int');
        }
        
        if (!$this->request->post['webservice']['category_image_width']) {
            $this->error['category_image_width'] = $this->language->get('error_width');
        } else if (filter_var($this->request->post['webservice']['category_image_width'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 999))) === false) {
            $this->error['category_image_width'] = $this->language->get('error_width_not_int');
        }
        
        if (!$this->request->post['webservice']['category_image_width']) {
            $this->error['category_image_width'] = $this->language->get('error_width');
        } else if (filter_var($this->request->post['webservice']['category_image_width'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 999))) === false) {
            $this->error['category_image_width'] = $this->language->get('error_width_not_int');
        }
        
        if (!$this->request->post['webservice']['category_image_width']) {
            $this->error['category_image_width'] = $this->language->get('error_width');
        } else if (filter_var($this->request->post['webservice']['category_image_width'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 999))) === false) {
            $this->error['category_image_width'] = $this->language->get('error_width_not_int');
        }
        
        
        
        
        
        //validations of general settings
        if (!$this->request->post['webservice']['category_image_width']) {
            $this->error['category_image_width'] = $this->language->get('error_width');
        } else if (filter_var($this->request->post['webservice']['category_image_width'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 999))) === false) {
            $this->error['category_image_width'] = $this->language->get('error_width_not_int');
        }
        if (!$this->request->post['webservice']['category_image_height']) {
            $this->error['category_image_height'] = $this->language->get('error_height');
        } else if (filter_var($this->request->post['webservice']['category_image_height'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 999))) === false) {
            $this->error['category_image_height'] = $this->language->get('error_height_not_int');
        }

        if (!$this->request->post['webservice']['product_image_width']) {
            $this->error['product_image_width'] = $this->language->get('error_width');
        } else if (filter_var($this->request->post['webservice']['product_image_width'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 999))) === false) {
            $this->error['product_image_width'] = $this->language->get('error_width_not_int');
        }

        if (!$this->request->post['webservice']['product_image_height']) {
            $this->error['product_image_height'] = $this->language->get('error_height');
        } else if (filter_var($this->request->post['webservice']['product_image_height'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 999))) === false) {
            $this->error['product_image_height'] = $this->language->get('error_height_not_int');
        }
        //validations of general settings ends
        //
                // validations of slideshow starts

        if (isset($this->request->post['slideshow_status']) && $this->request->post['slideshow_status'] == 1) {
            if ((utf8_strlen($this->request->post['slideshow_name']) < 3) || (utf8_strlen($this->request->post['slideshow_name']) > 64)) {
                $this->error['slideshow_name'] = $this->language->get('error_slideshow_name');
            }

            if (filter_var($this->request->post['slideshow_limit'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 5))) === false) {
                $this->error['slideshow_limit'] = $this->language->get('error_slideshow_limit');
            }

            if (isset($this->request->post['slideshow_image'])) {
                $slide_index = 0;
                foreach ($this->request->post['slideshow_image'] as $slideshow_image_id => $slideshow_image) {
                    foreach ($slideshow_image['slideshow_image_description'] as $language_id => $slideshow_image_description) {
                        if ((utf8_strlen($slideshow_image_description['title']) < 2) || (utf8_strlen($slideshow_image_description['title']) > 64)) {
                            $this->error['slideshow_image'][$slide_index][$language_id] = $this->language->get('error_slideshow_title');
                        }
                    }
                    $slide_index++;
                }
            }

            if ((!$this->request->post['webservice']['slideshow_width']) || (!$this->request->post['webservice']['slideshow_height'])) {
                $this->error['slideshow_image_size'] = $this->language->get('error_slideshow_image_size');
            } else if (filter_var($this->request->post['webservice']['slideshow_width'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 600, "max_range" => 9999))) === false) {
                $this->error['slideshow_image_size'] = $this->language->get('error_slide_banner_width_height_not_int');
            } else if (filter_var($this->request->post['webservice']['slideshow_height'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 200, "max_range" => 999))) === false) {
                $this->error['slideshow_image_size'] = $this->language->get('error_slide_banner_width_height_not_int');
            }
        }
        //validation of slideshow ends
        //
                //validation of banner starts

        if (isset($this->request->post['banner_status']) && $this->request->post['banner_status'] == 1) {
            if ((utf8_strlen($this->request->post['banner_name']) < 3) || (utf8_strlen($this->request->post['banner_name']) > 64)) {
                $this->error['banner_name'] = $this->language->get('error_banner_name');
            }

            if (filter_var($this->request->post['banner_limit'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 5))) === false) {
                $this->error['banner_limit'] = $this->language->get('error_banner_limit');
            }

            if (isset($this->request->post['banner_image'])) {
                $index = 0;
                foreach ($this->request->post['banner_image'] as $banner_image_id => $banner_image) {
                    foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
                        if ((utf8_strlen($banner_image_description['title']) < 2) || (utf8_strlen($banner_image_description['title']) > 64)) {
                            $this->error['banner_image'][$index][$language_id] = $this->language->get('error_banner_title');
                        }
                    }
                    $index++;
                }
            }

            if ((!$this->request->post['webservice']['banner_width']) || (!$this->request->post['webservice']['banner_height'])) {
                $this->error['banner_image_size'] = $this->language->get('error_banner_image_size');
            } else if (filter_var($this->request->post['webservice']['banner_width'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 600, "max_range" => 9999))) === false) {
                $this->error['banner_image_size'] = $this->language->get('error_slide_banner_width_height_not_int');
            } else if (filter_var($this->request->post['webservice']['banner_height'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 200, "max_range" => 999))) === false) {
                $this->error['banner_image_size'] = $this->language->get('error_slide_banner_width_height_not_int');
            }
        }
        //validation of banner ends
        //
                //validation of featured starts

        if (isset($this->request->post['webservice']['featured_status']) && $this->request->post['webservice']['featured_status'] == 1) {
            if ((!$this->request->post['webservice']['featured_width']) || (!$this->request->post['webservice']['featured_height'])) {
                $this->error['featured_image'] = $this->language->get('error_featured_image');
            } else if (filter_var($this->request->post['webservice']['featured_width'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1))) === false) {
                $this->error['featured_image'] = $this->language->get('error_width_height_not_int');
            } else if (filter_var($this->request->post['webservice']['featured_height'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1))) === false) {
                $this->error['featured_image'] = $this->language->get('error_width_height_not_int');
            }

            if (filter_var($this->request->post['webservice']['featured_limit'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 20))) === false) {
                $this->error['featured_limit'] = $this->language->get('error_featured_limit');
            }
        }
        //validation of featured ends
        //
                //validation of specials starts
        if (isset($this->request->post['webservice']['special_status']) && $this->request->post['webservice']['special_status'] == 1) {
            if ((!$this->request->post['webservice']['special_width']) || (!$this->request->post['webservice']['special_height'])) {
                $this->error['special_image'] = $this->language->get('error_special_image');
            } else if (filter_var($this->request->post['webservice']['special_width'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1))) === false) {
                $this->error['special_image'] = $this->language->get('error_width_height_not_int');
            } else if (filter_var($this->request->post['webservice']['special_height'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1))) === false) {
                $this->error['special_image'] = $this->language->get('error_width_height_not_int');
            }
            if (filter_var($this->request->post['webservice']['special_limit'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 40))) === false) {
                $this->error['special_limit'] = $this->language->get('error_special_limit');
            }
        }
        //validation of specials ends
        //
                //validation of bestseller starts
        if (isset($this->request->post['webservice']['bestseller_status']) && $this->request->post['webservice']['bestseller_status'] == 1) {
            if ((!$this->request->post['webservice']['bestseller_width']) || (!$this->request->post['webservice']['bestseller_height'])) {
                $this->error['bestseller_image'] = $this->language->get('error_bestseller_image');
            } else if (filter_var($this->request->post['webservice']['bestseller_width'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1))) === false) {
                $this->error['bestseller_image'] = $this->language->get('error_width_height_not_int');
            } else if (filter_var($this->request->post['webservice']['bestseller_height'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1))) === false) {
                $this->error['bestseller_image'] = $this->language->get('error_width_height_not_int');
            }
            if (filter_var($this->request->post['webservice']['bestseller_limit'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 40))) === false) {
                $this->error['bestseller_limit'] = $this->language->get('error_bestseller_limit');
            }
        }
        //validation of bestseller starts
        //
                //validation of latest starts
        if (isset($this->request->post['webservice']['latest_status']) && $this->request->post['webservice']['latest_status'] == 1) {
            if ((!$this->request->post['webservice']['latest_width']) || (!$this->request->post['webservice']['latest_height'])) {
                $this->error['latest_image'] = $this->language->get('error_latest_image');
            } else if (filter_var($this->request->post['webservice']['latest_width'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1))) === false) {
                $this->error['latest_image'] = $this->language->get('error_width_height_not_int');
            } else if (filter_var($this->request->post['webservice']['latest_height'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1))) === false) {
                $this->error['latest_image'] = $this->language->get('error_width_height_not_int');
            }

            if (filter_var($this->request->post['webservice']['latest_limit'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 40))) === false) {
                $this->error['latest_limit'] = $this->language->get('error_latest_limit');
            }
        }
        //validation of latest ends
        //
                //validation of cms pages tab starts
        if (isset($this->request->post['cms'])) {
            foreach ($this->request->post['cms'] as $cms_row_id => $cms_row) {
                foreach ($cms_row['cms_title'] as $language_id => $cms_title) {
                    if ((utf8_strlen($cms_title['title']) < 2) || (utf8_strlen($cms_title['title']) > 64)) {
                        $this->error['cms'][$cms_row_id][$language_id] = $this->language->get('error_cms_title');
                    }
                }
            }
        }
        //validation of cms pages tab ends

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }
        return !$this->error;
    }

    public function selectCategory() {
        $json = array();

        $json = $this->selectCategoryFilter();

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function selectCategoryProduct() {
        $response = array();
        
        $this->load->model('catalog/product');
        $results = $this->model_catalog_product->getProductsByCategoryId($this->request->get['category_id']);

        foreach ($results as $result) {

            $response[] = array(
                'product_id' => $result['product_id'],
                'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
            );
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($response));
    }

    //Function to get categories
    public function selectCategoryFilter() {

        $response = array();

        $this->load->model('catalog/category');

        $filter_data = array(
            'filter_name' => ''
        );

        $results = $this->model_catalog_category->getCategories($filter_data);

        foreach ($results as $result) {
            $response[] = array(
                'category_id' => $result['category_id'],
                'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
            );
        }


        $sort_order = array();

        foreach ($response as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $response);

        return $response;
    }

    public function selectProduct() {
        $json = array();

        $json = $this->selectProductFilter();

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    //Function to get products
    public function selectProductFilter() {

        $response = array();

        $this->load->model('catalog/product');
        $this->load->model('catalog/option');

        $filter_data = array(
            'filter_name' => ''
        );

        $results = $this->model_catalog_product->getProducts($filter_data);

        foreach ($results as $result) {

            $response[] = array(
                'product_id' => $result['product_id'],
                'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
            );
        }

        return $response;
    }

    public function sendNotification() {
        $data = $this->request->post;
        if ($data['redirect_activity'] == '0') {
            $ra = "home";
            $product = 'null';
            $category = 'null';
        } elseif ($data['redirect_activity'] == '1') {
            $ra = "category";
            $product = 'null';
            $category = $data['activity_link_to'];
        } elseif ($data['redirect_activity'] == '2') {
            $ra = "product";
            $product = $data['activity_link_to'];
            $category = 'null';
        }
        $this->load->model('webservice/webservice');
        $new_data = array(
            'title' => $data['title'],
            'message' => $data['message'],
            'redirect_activity' => $ra,
            'image' => $data['image'],
	    'device' => $data['select_device'],
            'category' => $category,
            'product' => $product,
            'date_added' => date('Y-m-d H:i:s'),
        );
       
        $this->model_webservice_webservice->saveNotification($new_data);
        $firebase_data = array();
        $firebase_data['data']['title'] = $data['title'];
        $firebase_data['data']['is_background'] = false;
        $firebase_data['data']['message'] = $data['message'];
        $firebase_data['data']['image'] = str_replace(" ", '%20',HTTP_CATALOG . "image/" . $data['image']);
        $firebase_data['data']['payload'] = '';
        $firebase_data['data']['user_id'] = "";
        $firebase_data['data']['push_type'] = "promotional_".$ra;
        $firebase_data['data']['category_id'] = $category;
        $firebase_data['data']['product_id'] = $product;
        $firebase_data['data']['filters'] = "";
        $firebase_data['data']['category_name'] = 'Test';
	$firebase_data['data']['device']=$data['select_device'];
        $setting_data = $this->config->get('webservice');
        $firebase_server_key = $setting_data['firebase_server_key'];
        
        $fcm_data = $this->model_webservice_webservice->getFcmData();
        
        if ($fcm_data) {
			//foreach ($fcm_data as $user) {
                if($firebase_data['data']['device']=="Android") {  
                    print_R($this->sendToTopic("ANDROID_USERS", $firebase_data, $firebase_server_key, $firebase_data['data']['device']));
                    //print_R($this->sendMultiple($user['fcm_id'], $firebase_data, $firebase_server_key,$firebase_data['data']['device']));
                }
                else if($firebase_data['data']['device']=="iOS") {
                    print_R($this->sendToTopic("IOS_USERS", $firebase_data, $firebase_server_key, $firebase_data['data']['device']));
                    //print_R($this->sendMultiple($user['fcm_id'], $firebase_data, $firebase_server_key,$firebase_data['data']['device']));
                }
                else {
                    print_R($this->sendToTopic("ANDROID_USERS", $firebase_data, $firebase_server_key, "Android"));
                    print_R($this->sendToTopic("IOS_USERS", $firebase_data, $firebase_server_key, "iOS"));
                }
          }
        die;
    }
    private function captureStoreURL($type)
    {
        $ch = curl_init();
        $postdata =  array();
        $store_url = HTTPS_CATALOG;
        $postdata['store_url'] = $store_url;
        $postdata['platform'] = 'Opencart';
        $postdata['mobile_number'] = '';
        $postdata['request_type'] = '';
        if ($type == false) {
            $postdata['module_installed'] = 'no';
        } else {
            $postdata['module_installed'] = 'yes';
        }
        curl_setopt($ch, CURLOPT_URL, "https://tracker.knowband.com/demo.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
    }
    //Function to create all tables related to module  if module is installed
    public function install() {
        
        $create_table_webservice_banner = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "webservice_banner` (
                                            `webservice_banner_id` int(11) NOT NULL auto_increment,                                                                                
                                            `name`  varchar(40) NOT NULL,
                                            `status` TINYINT(1) NOT NULL,                                       
                                            `banner_limit` INT(11) NOT NULL,
                                            PRIMARY KEY  (`webservice_banner_id`)
                                            )";

        $create_table_webservice_banner_image = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "webservice_banner_image` (
                                                    `webservice_banner_image_id` int(11) NOT NULL auto_increment,
                                                    `webservice_banner_id` int(11) NOT NULL,
                                                    `link_type`  varchar(11) NOT NULL,
                                                    `link_to`  varchar(11) NOT NULL,
                                                    `image`  varchar(255) NOT NULL,
                                                    `sort_order` INT(3) NOT NULL DEFAULT '0',                                       
                                                    PRIMARY KEY  (`webservice_banner_image_id`)
                                                    )";

        $create_table_webservice_banner_image_description = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "webservice_banner_image_description` (
                                                                `webservice_banner_image_id` int(11) NOT NULL,
                                                                `language_id` int(11) NOT NULL,
                                                                `webservice_banner_id` int(11) NOT NULL,
                                                                `title`  varchar(64) NOT NULL,                                       
                                                                PRIMARY KEY  (`webservice_banner_image_id`, `language_id`)
                                                                )";

        
        $create_table_webservice_cms_page_data = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "webservice_cms_page_data` (
                                                    `cms_id` int(11) NOT NULL auto_increment,
                                                    `cms_row_id` int(11) NOT NULL,
                                                    `link_to`  varchar(11) NOT NULL,
                                                    `status` TINYINT(2) NOT NULL,
                                                    `sort_order` INT(3) NOT NULL DEFAULT '0',                                                                                     
                                                    `date_added` DATETIME NOT NULL,
                                                    `date_modified` TIMESTAMP NOT NULL,
                                                    PRIMARY KEY  (`cms_id`)
                                                    ) DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;";

        $create_table_webservice_cms_page_title = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "webservice_cms_page_title` (
                                                    `cms_page_title_id` int(11) NOT NULL auto_increment,
                                                    `cms_row_id` int(11) NOT NULL,
                                                    `language_id` int(11) NOT NULL,
                                                    `title`  varchar(64) NOT NULL,
                                                    `date_added` DATETIME NOT NULL,
                                                    `date_modified` TIMESTAMP NOT NULL,
                                                    PRIMARY KEY  (`cms_page_title_id`)
                                                    ) DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci; ";
        $create_table_webservice_push_notifications = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "push_notification_history` (
                                                        `notification_id` int(11) NOT NULL auto_increment,
                                                        `title` varchar(1000) NOT NULL,
                                                        `message` varchar(1000) DEFAULT NULL,
                                                        `image_url` text NOT NULL,
                                                        `device` varchar(100) NOT NULL,
                                                        `redirect_activity` enum('home','category','product') NOT NULL,
                                                        `category_id` int(10) DEFAULT NULL,
                                                        `product_id` int(10) DEFAULT NULL,
                                                        `date_added` datetime NOT NULL,
                                                        PRIMARY KEY  (`notification_id`)
                                                      ) DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;";
        $create_table_webservice_fcm = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "fcm_details` (
                                                `fcm_details_id` int(11) NOT NULL auto_increment,
                                                `email_id` varchar(1000) DEFAULT NULL,
                                                `order_id` int(11) DEFAULT NULL,
                                                `cart` text,
                                                `session_id` varchar(1000) DEFAULT NULL,
                                                `fcm_id` text NOT NULL,
                                                `notification_sent_status` int(2) DEFAULT NULL,
                                                `last_order_status` int(11) NOT NULL DEFAULT '0',
                                                `date_add` datetime NOT NULL,
                                                `date_upd` datetime NOT NULL,
                                                PRIMARY KEY (`fcm_details_id`)
                                              ) DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;";
        //Changes
        $create_table_component_heading = "
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "kbb_components_heading` (
              `heading_id` int(100) NOT NULL AUTO_INCREMENT,
              `id_layout` int(100) NOT NULL,
              `id_component` int(100) NOT NULL,
              `heading` varchar(1000) NOT NULL,
              `language_id` int(100) NOT NULL,
              PRIMARY KEY (`heading_id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;";
        
        $this->db->query($create_table_component_heading);
        
        $create_table_banner_data = "
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "kbmobileapp_banners` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `id_component` int(11) NOT NULL,
              `id_banner_type` int(11) NOT NULL,
              `countdown` varchar(200) DEFAULT NULL,
              `product_id` int(10) DEFAULT NULL,
              `category_id` int(10) DEFAULT NULL,
              `redirect_activity` enum('category','product','home') DEFAULT NULL,
              `image_url` longtext,
              `image_type` enum('url','image') DEFAULT NULL,
              `image_content_mode` varchar(200) NOT NULL,
              `banner_heading` varchar(200) DEFAULT NULL,
              `background_color` varchar(11) DEFAULT NULL,
              `is_enabled_background_color` int(10) NOT NULL DEFAULT '1',
              `text_color` varchar(11) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;";
        
        $this->db->query($create_table_banner_data);
        
        $create_table_component_type = "
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "kbmobileapp_component_types` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `component_name` varchar(200) NOT NULL,
             PRIMARY KEY (`id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;";
        
        $this->db->query($create_table_component_type);
        
        $count_component_types = $this->db->query("SELECT * FROM `" . DB_PREFIX . "kbmobileapp_component_types`");
        if(!($count_component_types->num_rows)) {
            $insert_component_type = "INSERT INTO `" . DB_PREFIX . "kbmobileapp_component_types` (`id`, `component_name`) VALUES
                (1, 'top_category'),
                (2, 'banner_square'),
                (3, 'banners_countdown'),
                (4, 'banners_grid'),
                (5, 'banner_horizontal_slider'),
                (6, 'products_square'),
                (7, 'products_horizontal'),
                (8, 'products_recent'),
                (9, 'products_grid');";
        
        $this->db->query($insert_component_type);
        }
        
        $create_table_product_data = "
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "kbmobileapp_product_data` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `product_type` varchar(200) NOT NULL,
          `category_products` text,
          `custom_products` text,
          `image_content_mode` varchar(200) NOT NULL,
          `number_of_products` int(11) NOT NULL,
          `id_category` int(11) DEFAULT NULL,
          `id_component` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;";
        
        $this->db->query($create_table_product_data);
        
        $create_table_top_category = "
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "kbmobileapp_top_category` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `id_component` int(11) NOT NULL,
          `id_category` varchar(200) NOT NULL,
          `image_url` longtext,
          `image_content_mode` varchar(200) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;";
        
        $this->db->query($create_table_top_category);
        
        $create_table_unique_verification = "
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "kbmobileapp_unique_verification` (
          `id_verification` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          `id_customer` int(10) UNSIGNED NOT NULL,
          `mobile_number` varchar(100) DEFAULT NULL,
          `country_code` varchar(10) DEFAULT NULL,
          `fid` varchar(100) DEFAULT NULL,
          `date_added` datetime NOT NULL,
          `date_update` datetime NOT NULL,
          PRIMARY KEY (`id_verification`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;";
        
        $this->db->query($create_table_unique_verification);
        
        $create_table_layouts = "
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "kb_mobileapp_layouts` (
          `id_layout` int(11) NOT NULL AUTO_INCREMENT,
          `layout_name` varchar(200) NOT NULL,
          PRIMARY KEY (`id_layout`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;";
        
        $this->db->query($create_table_layouts);
        
        $create_table_component_layout = "
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "kb_mobileapp_layout_component` (
          `id_component` int(11) NOT NULL AUTO_INCREMENT,
          `id_layout` int(11) NOT NULL,
          `id_component_type` int(11) NOT NULL,
          `position` int(11) NOT NULL,
          PRIMARY KEY (`id_component`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8  COLLATE=utf8_general_ci;";
        
        $this->db->query($create_table_component_layout);
        
        $count_layout = $this->db->query("SELECT * FROM `" . DB_PREFIX . "kb_mobileapp_layouts`");
        
        if(!($count_layout->num_rows)) {
        
            $this->db->query("INSERT INTO `" . DB_PREFIX . "kbmobileapp_product_data` (`id`, `product_type`, `category_products`, `custom_products`, `image_content_mode`, `number_of_products`, `id_category`, `id_component`) VALUES
    (29, 'new_products', '', '', '1', 4, 0, 99);");


            $this->db->query("INSERT INTO `" . DB_PREFIX . "kbb_components_heading` (`heading_id`, `id_layout`, `id_component`, `heading`, `language_id`) VALUES
    (23, 1, 99, 'New Products', 1)");

            $this->db->query("INSERT INTO `" . DB_PREFIX . "kbmobileapp_banners` (`id`, `id_component`, `id_banner_type`, `countdown`, `product_id`, `category_id`, `redirect_activity`, `image_url`, `image_type`, `image_content_mode`, `banner_heading`, `background_color`, `is_enabled_background_color`, `text_color`) VALUES
    (128, 97, 0, NULL, 0, 0, 'home', 'catalog/mab/6.jpg', NULL, '0', NULL, NULL, 1, NULL),
    (124, 98, 0, NULL, 0, 0, 'home', 'catalog/mab/bn4.png', NULL, '0', NULL, NULL, 1, NULL),
    (123, 98, 0, NULL, 0, 0, 'home', 'catalog/mab/9.jpg', NULL, '0', NULL, NULL, 1, NULL),
    (122, 96, 0, NULL, 0, 0, 'home', 'catalog/mab/bn2.png', NULL, '0', NULL, NULL, 1, NULL),
    (121, 96, 0, NULL, 0, 0, 'home', 'catalog/mab/sl32-1170x500.jpg', NULL, '0', NULL, NULL, 1, NULL);");

            $this->db->query("INSERT INTO `" . DB_PREFIX . "kb_mobileapp_layouts` (`id_layout`, `layout_name`) VALUES
    (1, 'Default Layout');");

            $this->db->query("INSERT INTO `" . DB_PREFIX . "kb_mobileapp_layout_component` (`id_component`, `id_layout`, `id_component_type`, `position`) VALUES
    (96, 1, 5, 0),
    (97, 1, 2, 3),
    (98, 1, 4, 1),
(99, 1, 9, 4);");
        
        }
        
        $this->db->query($create_table_webservice_cms_page_data);
        $this->db->query($create_table_webservice_cms_page_title);
        $this->db->query($create_table_webservice_push_notifications);
        $this->db->query($create_table_webservice_banner);
        $this->db->query($create_table_webservice_banner_image);
        $this->db->query($create_table_webservice_banner_image_description);
        $this->db->query($create_table_webservice_fcm);
        
//        $this->db->query("ALTER TABLE `" . DB_PREFIX . "webservice_cms_page_title` CHANGE `title` `title` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
//        $this->db->query("ALTER TABLE `" . DB_PREFIX . "kbmobileapp_banners` CHANGE `image_url` `image_url` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
//        $this->db->query("ALTER TABLE `" . DB_PREFIX . "kbb_components_heading` CHANGE `heading` `heading` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");

        if (VERSION < '2.0.1.0') {
            $this->load->model('tool/event');
            $this->model_tool_event->addEvent('mailchimp_automation', 'post.order.add', 'webservices/firebase/on_order_add');
            $this->model_tool_event->addEvent('mailchimp_automation', 'post.order.history.add', 'webservices/firebase/on_order_update');
        } elseif (VERSION >= '2.0.1.0') {
            $this->load->model('setting/event');
            $this->model_setting_event->addEvent('mailchimp_automation', 'post.order.add', 'webservices/firebase/on_order_add');
            $this->model_setting_event->addEvent('mailchimp_automation', 'post.order.history.add', 'webservices/firebase/on_order_update');
        }
        $this->captureStoreURL(true);
    }

    //Function to delete all tables related to module and settings if module is uninstalled
    public function uninstall() {
        $this->load->model('setting/setting');
        $this->load->model('setting/store');
        $results = $this->model_setting_store->getStores();
        if ($results) {
            $this->model_setting_setting->deleteSetting('webservice', 0);
            foreach ($results as $result) {
                $this->model_setting_setting->deleteSetting('webservice', $result['store_id']);
            }
        } else {
            $this->model_setting_setting->deleteSetting('webservice');
        }
        if (VERSION < '2.0.1.0') {
            $this->load->model('tool/event');
            $this->model_tool_event->deleteEvent('mailchimp_automation');
        } elseif (VERSION >= '2.0.1.0') {
            $this->load->model('setting/event');
            $this->model_setting_event->deleteEvent('mailchimp_automation');
        }
        $this->captureStoreURL(false);
    }

public function send($to, $message, $server_key, $device = 'both') {
        if ($device == 'Android') {
        $fields = array(
            'to' => $to,
            'data' => $message,
            'priority'=> "high",
            'mutable_content' => true,
            'content_available' => true
            );
            return $this->sendPushNotification($fields, $server_key);
        } else if ($device == 'iOS') {
            $fields = array(
                'to' => $to,
                'data' => $message,
                'priority'=> "high",
                'mutable_content' => true,
                'content_available' => true,
                'notification'=> array(
                'title'=> $message['data']['title'],
                'body'=> $message['data']['message']
            ),
        );
        return $this->sendPushNotification($fields, $server_key);
        } else {
            $fields = array(
                'to' => $to,
                'data' => $message,
                'priority'=> "high",
                'mutable_content' => true,
                'content_available' => true,
                'notification'=> array(
                    'title'=> $message['data']['title'],
                    'body'=> $message['data']['message']
                ),
            );
            return $this->sendPushNotification($fields, $server_key);
    }
    }
    
    // Sending message to a topic by topic name
    public function sendToTopic($to, $message, $server_key, $device = 'Both') {
        if ($device == 'Android') {
        $fields = array(
            'to' => '/topics/' . $to,
            'data' => $message,
            'priority'=> "high",
            'mutable_content' => true,
            'content_available' => true
            );
        } elseif ($device == 'iOS') {
            $fields = array(
                'to' => '/topics/' . $to,
                'data' => $message,
                'priority'=> "high",
                'mutable_content' => true,
                'content_available' => true,
                'notification'=> array(
                'title'=> $message['data']['title'],
                'body'=> $message['data']['message']
            ),
        );
        } else {
            $fields = array(
                'to' => '/topics/' . $to,
                'data' => $message,
                'priority'=> "high",
                'mutable_content' => true,
                'content_available' => true,
                'notification'=> array(
                    'title'=> $message['data']['title'],
                    'body'=> $message['data']['message']
                ),
            );
        }
        
        return $this->sendPushNotification($fields, $server_key);
    }

    
    
    public function sendMultiple($registration_ids, $message, $server_key,$device='both') {
        if($device=='Android')
        {
            $fields = array(
                'to' => $registration_ids,
                'data' => $message,
                'priority'=> "high",
                'mutable_content' => true,
                'content_available' => true,
            );
           
        }
        else if($message['data']['device']=='iOS'){
            $fields = array(
                'to' => $registration_ids,
                'data' => $message,
                'priority'=> "high",
                'mutable_content' => true,
                'content_available' => true,
                'notification'=> array(
                    'title'=> $message['data']['title'],
                    'body'=> $message['data']['message']
                ),
            );
        }
        else {
            $fields = array(
                'to' => $registration_ids,
                'data' => $message,
                'priority'=> "high",
                'mutable_content' => true,
                'content_available' => true,
                'notification'=> array(
                    'title'=> $message['data']['title'],
                    'body'=> $message['data']['message']
                ),
            );
        }
        return $this->sendPushNotification($fields, $server_key);
    }


    // function makes curl request to firebase servers
    private function sendPushNotification($fields, $server_key = '') {
	
	

        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=' . $server_key,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));


        // Execute post
        $result = curl_exec($ch);
	
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        return $result;
    }
    public function shipping_autocomplete() {
            
            $this->load->model('extension/extension'); 
            
            $methods = $this->model_extension_extension->getInstalled('shipping');
            
            foreach($methods as $method)
            {
                $json[] = array(
                        'code' => $method,
                );
            }
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
    }
    
    public function saveComponent() {
        $this->load->model('webservice/webservice');
        $component_id = $this->model_webservice_webservice->saveComponent($this->request->post);
        echo $component_id; die;
    }
    public function savePosition() {
            $this->load->model('webservice/webservice');
            $position_array = explode(',',$this->request->post['position_array']);
            foreach($position_array as $position=>$component) {
                $component_array = explode('_',$component);
                if(isset($component_array[3])) {
                    $component_id = $component_array[3];
                    $this->model_webservice_webservice->updateLayoutPosition($position,$this->request->post['id_layout'],$component_id);
                }    
            }
//        $this->load->model('webservice/webservice');
//        $component_id = $this->model_webservice_webservice->saveComponent($this->request->post);
//        echo $component_id; die;
    }
    
    public function getComponentType() {
        
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $this->load->model('webservice/webservice');
        
        $component['id_layout'] = $this->request->post['id_layout'];
        $component['id_component'] = $this->request->post['id_component'];
        
        $query = $this->db->query('Select id_component_type from ' . DB_PREFIX . 'kb_mobileapp_layout_component where id_component = '.  $this->request->post['id_component']) ;
        $component_type_id = $query->row['id_component_type'];
        $query = $this->db->query('Select component_name from ' . DB_PREFIX . 'kbmobileapp_component_types where id = '.  $component_type_id) ;
        $component_type = $query->row['component_name'];
        $products = array();
        if ($component_type == 'top_category') {
            $component_data = $this->model_webservice_webservice->getTopCategoryData($component['id_component']);
        } else if($component_type == 'products_square' || $component_type == 'products_grid' || $component_type == 'products_horizontal'){
            $component_data = $this->model_webservice_webservice->getProductsByComponent($component['id_layout'],$component['id_component']);
            $component_data[0]['heading'] = $this->model_webservice_webservice->getComponentHeading($component['id_layout'],$component['id_component']);
             $products = $this->getProductsComponentData($component['id_component']);
        } else if($component_type == 'banner_square' || $component_type == 'banners_countdown' || $component_type == 'banners_grid' || $component_type == 'banner_horizontal_slider'){
            $component_data = $this->model_webservice_webservice->getBannerByComponent($component['id_layout'],$component['id_component']);
            $component_data[0]['heading'] = $this->model_webservice_webservice->getComponentHeading($component['id_layout'],$component['id_component']);
        } 
        $data = array(
            'type' => $component_type,
            'component_data' => $component_data,
            'products' => $products
        );
        echo json_encode($data); die;
    }
    public function saveElement() {
        $this->load->model('webservice/webservice');
        if(isset($this->request->get['saveTopcategoryFormData']) && $this->request->get['saveTopcategoryFormData']=='true') {
            $this->model_webservice_webservice->saveTopCategoryData($this->request->post);
            $category_data = $this->model_webservice_webservice->getTopCategoryData($this->request->post['id_component']);
            echo json_encode($category_data); die;
        } else if(isset($this->request->get['saveBannerSquare']) && $this->request->get['saveBannerSquare']=='true') {
            $bs_image_row = $this->request->post['bs_image_row'];
            $sno = $bs_image_row+1;
            $product_id = 0;
            $category_id = 0;
            if($this->request->post['redirect_activity']==2) {
                $product_id =  $this->request->post['link_to'];
                $redirect_activity = 'Product';
            } else if($this->request->post['redirect_activity']==1) {
                $category_id = $this->request->post['link_to'];
                $redirect_activity = 'Category';
            } else {
                $redirect_activity = 'Home';
            }
            $id=$this->model_webservice_webservice->saveBannerSquareData($this->request->post);
            
            $html = "<tr id='bs-image-".$bs_image_row."'>";
            $html .= "<td class='text-left'>".$sno."</td>";                                                                                           
            $html .= "<td class='text-center'>";
            if ($this->request->post['image_url']) {
                $html .= "<img src='../image/".$this->request->post['image_url']."' class='img-thumbnail' style='width:60px !important; height:60px !important;'/>";
            } else {
            $html .= "<span class='img-thumbnail list'><i class='fa fa-camera fa-2x'></i></span>";
            }                                                                                           
            $html .= "<td class='text-left'>".$redirect_activity."</td>";                                                                                           
            $html .= "<td class='text-left'>".$category_id."</td>";                                                                                           
            $html .= "<td class='text-left'>".$product_id."</td>";      
            $html .='<input type="hidden" value="'.$id.'" id="bs-image-id-'.$bs_image_row.'"/>';
            $html .="<td><button type='button' onclick='deleteImage(".$bs_image_row.");' data-toggle='tooltip' title='' class='btn btn-danger' data-original-title='Delete Banner'><i class='fa fa-minus'></i></button>";
            $html .= "</tr>";
            echo $html; die;
        } else if(isset($this->request->get['saveProductFormData']) && $this->request->get['saveProductFormData']=='true') {
            $this->load->model('webservice/webservice');
            $this->model_webservice_webservice->saveProductData($this->request->post);
        } else if(isset($this->request->get['saveBannerCountdown']) && $this->request->get['saveBannerCountdown']=='true') {
            $bc_image_row = $this->request->post['bc_image_row'];
            $sno = $bc_image_row+1;
            $product_id = 0;
            $category_id = 0;
            if($this->request->post['redirect_activity']==2) {
                $product_id =  $this->request->post['link_to'];
                $redirect_activity = 'Product';
            } else if($this->request->post['redirect_activity']==1) {
                $category_id = $this->request->post['link_to'];
                $redirect_activity = 'Category';
            } else {
                $redirect_activity = 'Home';
            }
            $id = $this->model_webservice_webservice->saveBannerCountdownData($this->request->post);
            $html = "<tr id='bc-image-".$bc_image_row."'>";
            $html .= "<td class='text-left'>".$sno."</td>";                                                                                           
            $html .= "<td class='text-center'>";
            if ($this->request->post['image_url']) {
                $html .= "<img src='../image/".$this->request->post['image_url']."' class='img-thumbnail' style='width:60px !important; height:60px !important;'/>";
            } else {
            $html .= "<span class='img-thumbnail list'><i class='fa fa-camera fa-2x'></i></span>";
            }
            $html .= "<td class='text-left'>".$this->request->post['bc_countdown_validity']."</td>";
            $html .= "<td class='text-left'>".$this->request->post['bc_background_color']."</td>";
            $html .= "<td class='text-left'>".$this->request->post['bc_text_color']."</td>"; 
            $html .= "<td class='text-left'>".$redirect_activity."</td>";                                                                                           
            $html .= "<td class='text-left'>".$category_id."</td>";                                                                                           
            $html .= "<td class='text-left'>".$product_id."</td>"; 
            $html .='<input type="hidden" value="'.$id.'" id="bc-image-id-'.$bc_image_row.'"/>';
            $html .="<td><button type='button' onclick='deleteBCImage(.$bc_image_row.);' data-toggle='tooltip' title='' class='btn btn-danger' data-original-title='Delete Banner'><i class='fa fa-minus'></i></button>";
            $html .= "</tr>";
            echo $html; die;
        }
        die();
    }
    
    public function getComponent() {
       $this->load->model('webservice/webservice');
       $this->load->model('catalog/product');
        if(isset($this->request->get['getCategoryForm']) && $this->request->get['getCategoryForm'] =='true') {
            $category_data = $this->model_webservice_webservice->getTopCategoryData($this->request->post['id_component']);
            $this->response->setOutput(json_encode($category_data));
        }
        if(isset($this->request->get['getBannerForm']) && $this->request->get['getBannerForm'] =='true') {
            $banner_data = array();
            $banner_data = $this->model_webservice_webservice->getBannerByComponent($this->request->post['id_layout'],$this->request->post['id_component']);
            $banner_data[0]['heading'] = $this->model_webservice_webservice->getComponentHeading($this->request->post['id_layout'],$this->request->post['id_component']);
            $this->response->setOutput(json_encode($banner_data));
        }
        if(isset($this->request->get['getBannerCountdownForm']) && $this->request->get['getBannerCountdownForm'] =='true') {
            $banner_data = array();
            $banner_data = $this->model_webservice_webservice->getBannerByComponent($this->request->post['id_layout'],$this->request->post['id_component']);
            $banner_data[0]['heading'] = $this->model_webservice_webservice->getComponentHeading($this->request->post['id_layout'],$this->request->post['id_component']);
            $this->response->setOutput(json_encode($banner_data));
        }
        if(isset($this->request->get['getProductForm']) && $this->request->get['getProductForm'] =='true') {
            $product_data = array();
            $product_data = $this->model_webservice_webservice->getProductsByComponent($this->request->post['id_layout'],$this->request->post['id_component']);
            $product_data[0]['heading'] = $this->model_webservice_webservice->getComponentHeading($this->request->post['id_layout'],$this->request->post['id_component']);
            $this->response->setOutput(json_encode($product_data));
        } else if(isset($this->request->get['ProductRecent']) && $this->request->get['ProductRecent'] =='true') {
            $heading = $this->model_webservice_webservice->getComponentHeading($this->request->post['id_layout'],$this->request->post['id_component']);
            $this->response->setOutput(json_encode($heading));
        }
    }
    public function getFeaturedProducts($number_of_products=10,$image_content_mode=1,$setting_data) {
        
        $store_id = $this->config->get('config_store_id');
        $this->load->model('setting/setting');
        $webservice_setting = $this->model_setting_setting->getSetting('webservice', $store_id);

        $layout_id = '1';
        $code = 'featured';
        $this->load->model('webservice/webservice');
        $this->load->model('design/layout');
        $this->load->model('setting/module');
        $this->load->model('catalog/product');
        $getHomeTopFeaturedProducts = $this->model_webservice_webservice->getHomeLayoutModules($layout_id, $code);
        $data['products'] = array();
        if (count($getHomeTopFeaturedProducts) > 0) {

                $modules = $this->model_design_layout->getLayoutModules($layout_id, 'content_top');
                    
                    foreach ($modules as $module) {

                        $part = explode('.', $module['code']);
                        if (isset($part[0])) {
                            $code1 = $part[0];
}

                        if ($code1 && $code1 == $code) {
                            if (isset($part[1])) {
                                $setting_info = $this->model_setting_module->getModule($part[1]);

                                if ($setting_info && $setting_info['status']) {
                                    $getModulesSettingArray = $setting_info;
                                    if (array_key_exists('product', $getModulesSettingArray) && count($getModulesSettingArray['product']) > 0) {
                                        $featuredProducts = array_slice($getModulesSettingArray['product'], 0, $getModulesSettingArray['limit'], true);
                                        $width = $getModulesSettingArray['width'];
                                        $height = $getModulesSettingArray['height'];
                                        
                                        if (!empty($featuredProducts)) {
                                            foreach ($featuredProducts as $featuredProduct) {
                                                $productInfo = $this->model_catalog_product->getProduct($featuredProduct);
                                                if ($productInfo) {
                                                     $image = HTTPS_CATALOG.'image/'.($productInfo['image']);
                                                    //Check if webp image exists
                                                    $info = pathinfo($image);
                                                    if (isset($info['extension'])) {
                                                        $directory = str_replace(HTTP_SERVER . 'image/cache/', DIR_IMAGE . 'cache/', $info['dirname']);
                                                        if (file_exists($directory . '/' . $info['filename'] . '.webp')) {
                                                            $image = $info['dirname'] . '/' . $info['filename'] . '.webp';
                                                        }
                                                    }
                                                    

                                                    if (isset($productInfo['special'])) {
                                                        if($productInfo['price']>0) {
                                                            $discount_price = $this->currency->format($this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                                                            $act_price = $this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                                                            $disc_price = $this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                                                            $discount_perc = (($act_price - $disc_price) / $act_price) * 100;
                                                            $discount_percentage = number_format($discount_perc, 2);
                                                        } else{
                                                            $productInfo['price']=$productInfo['special'];
                                                            $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                                                            $discount_percentage = 0;

                                                        }
                                                    } else {
                                                        $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
                                                        $discount_percentage = 0;
                                                    }
                                                    if ($productInfo['date_added'] >= $setting_data['new_product_start_date']) {
                                                        $is_new = 1;
                                                    } else {
                                                        $is_new = 0;
                                                    }
                                                    $data['products'][] = array(
                                                        'id' => $productInfo['product_id'],
                                                        'name' => html_entity_decode(str_replace('&quot;', '"', $productInfo['name'])),
                                                        'available_for_order' => 1,
                                                        'new_products' => $is_new,
                                                        'on_sale_products' => $discount_percentage ? 1 : 0,
                                                        //'category_name' => html_entity_decode(isset($parent_category['name']) ? $parent_category['name'] : ''),
                                                        'ClickActivityName' => 'ProductActivity',
                                                        //'category_id' => isset($parent_category['category_id']) ? $parent_category['category_id'] : '',
                                                        'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency')),
                                                        'src' => $image,
                                                        'discount_price' => $discount_price,
                                                        'discount_percentage' => $discount_percentage,
                                                        //'is_in_wishlist' => $this->isInWishlist($productInfo['product_id']),
                                                        'image_contentMode' => $image_content_mode,
                                                    );
                                                }
                                            }
                                        }
                                        break;
                                    }
                                }
                            } 
                        }
                    }
                }
        
        return $data['products'];
    }
}
