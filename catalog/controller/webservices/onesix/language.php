<?php

/*
 * File: language.php
 * Description: It contains language class and functions for web-services
 * Created Date: 14-December-2016
 */

class ControllerWebServicesOneSixlanguage extends Controller {

    public function setLanguage() {

        //Response Array
        $json = array();
        $this->load->model('webservices/home');
        $this->load->model('webservices/language');
        $this->load->language('webservices/config');
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            $this->model_webservices_home->logKnowbandAppRequest("setLanguage", json_encode($json));
            die;
        } else {
            $json['install_module'] = '';
        }


        if (isset($this->request->post['iso_code']) && !empty($this->request->post['iso_code'])) {
            $language_code = $this->request->post['iso_code'];
        } else {
            $language_code = '';
        }
        if ($language_code != "") {
            $lang_code = $this->model_webservices_language->checkLanguageCode($language_code);
            if (isset($lang_code) && $lang_code != "") {
                $this->session->data['language'] = $lang_code;
                $json['status'] = "success";
                $json['message'] = "";
            } else {
                $json['status'] = "failure";
                $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_language_error'));
            }
        } else {
            $json['status'] = "failure";
            $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_language_error'));
        }


        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("setLanguage", json_encode($json));
        $this->response->output();
        die;
    }

    //Function definition to prepare JSON response of Language data and send to requested application
    public function appGetTranslations() {

        //Response Array
        $json = array();
        $this->load->model('webservices/home');
        $this->load->model('webservices/language');
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            $this->model_webservices_home->logKnowbandAppRequest("getTranslation", json_encode($json));
            die;
        } else {
            $json['install_module'] = '';
        }

        $data = array(
            'lang_list' => array()
        );
        $data = $this->model_webservices_home->getLanguages();
        $langdata = array();
        foreach ($data['lang_list'] as $key => $val) {
            if(file_exists(DIR_LANGUAGE . $val["iso_code"] . "/webservices/config.php")) {
                $langdata[] = array("iso_code" => $val["iso_code"], "timestamp" => filemtime(DIR_LANGUAGE . $val["iso_code"] . "/webservices/config.php"));
            } else {
                if(file_exists(DIR_LANGUAGE . $val["directory"] . "/webservices/config.php")) {
                    $langdata[] = array("iso_code" => $val["iso_code"], "timestamp" => filemtime(DIR_LANGUAGE . $val["directory"] . "/webservices/config.php"));
                } else {
                    $langdata[] = array("iso_code" => $val["iso_code"], "timestamp" => filemtime(DIR_LANGUAGE ."en-gb/webservices/config.php"));
                }   
            }    
        }
        $json['languages_record'] = $langdata;

        if(!isset($_POST['iso_code'])) {
            $_POST['iso_code']='en-gb';
        }
        
        if (isset($_POST['all_app_texts']) && $_POST['all_app_texts']) {
            $json['translated_texts'] = $this->getTranslatedText($_POST['iso_code']);
        } else {
            $json['translated_texts'] = array();
        }

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("getTranslation", json_encode($json));
        $this->response->output();
        die;
    }

    public function getTranslatedText($iso_code) {

        $this->load->language('webservices/config');
        //Home Page
        $data['app_text_home'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_home'));
        $data['app_text_category'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_category'));
        $data['app_text_login'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_login'));
        $data['app_text_logout'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_logout'));
        $data['app_text_login_signup'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_login_signup'));
        $data['app_text_account'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_account'));
        $data['app_text_contact'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_contact'));
        $data['app_text_about'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_about'));
        $data['app_text_wishlist'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_wishlist'));
        $data['app_text_wishlist_title'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_wishlist_title'));
        $data['app_text_cart'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_cart'));
        $data['app_text_off'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_off'));
        $data['app_text_all'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_all'));
        $data['app_text_languages'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_languages'));

        $data["app_text_search"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_search'));
        $data["title_activity_search"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_search'));
        $data["app_text_cart_tab"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_cart_tab'));
        $data["app_text_review_your_order"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_review_your_order'));
        $data["app_text_percent_off"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_percent_off'));
        $data["app_text_press_back_again_to_exit"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_press_back_again_to_exit'));
        $data['app_text_continue_to_checkout_click_msg'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_continue_to_checkout_click_msg'));

        
        //Category Page
        $data['app_text_sort'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_sort'));
        $data['app_text_filter'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_filter'));
        $data['app_text_sort_order'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_sort_order'));
        $data['app_text_price_asce'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_price_asce'));
        $data['app_text_price_desc'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_price_desc'));

        //Product Page
        $data['app_text_product'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_product'));
        $data['app_text_select'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_select'));
        $data['app_text_instock'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_instock'));
        $data['app_text_outstock'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_outstock'));
        $data['app_text_product_info'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_product_info'));
        $data['app_text_customization'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_customization'));
        $data['app_text_accessories'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_accessories'));
        $data['app_text_view_less'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_view_less'));
        $data['app_text_view_more'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_view_more'));
        $data['app_text_provide_details'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_provide_details'));

        //Cart page
        $data['app_text_bag'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_bag'));
        $data['app_text_apply_voucher'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_apply_voucher'));
        $data['app_text_entry_voucher_code'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_entry_voucher_code'));
        $data['app_text_apply'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_apply'));
        $data['app_text_gift'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_gift'));
        $data['app_text_entry_message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_entry_message'));
        $data['app_text_available_first'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_available_first'));
        $data['app_text_continue_shopping'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_continue_shopping'));
        $data['app_text_continue_checkout'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_continue_checkout'));
        $data['app_text_update_quantity'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_update_quantity'));
        $data['app_text_enter_quantity'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_enter_quantity'));
        $data['app_text_empty_cart'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_empty_cart'));
        $data['app_text_goto_home'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_goto_home'));

        //Checkout Page
        $data['app_text_review_checkout'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_review_checkout'));
        $data['app_text_add_new_shipping_address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_add_new_shipping_address'));
        $data['app_text_history'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_history'));
        $data['app_text_summary'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_summary'));
        $data['app_text_shipping_methods'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_shipping_methods'));
        $data['app_text_comment'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_comment'));
        $data['app_text_payment_summary'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_payment_summary'));
        $data['app_text_proceed'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_proceed'));
        $data['app_text_make_payment'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_make_payment'));

        //Order History Page
        $data['app_text_congratulations'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_congratulations'));
        $data['app_text_shipping_addresses'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_shipping_addresses'));
        $data['app_text_order_detail'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_order_detail'));
        $data['app_text_update_personal'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_update_personal'));
        $data['app_text_update_password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_update_password'));
        $data['app_text_current_password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_current_password'));
        $data['app_text_new_password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_new_password'));
        $data['app_text_confirm_password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_confirm_password'));
        $data['app_text_cancel'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_cancel'));
        $data['app_text_save'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_save'));
        $data['app_text_order_ref'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_order_ref'));
        $data['app_text_status'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_status'));
        $data['app_text_placed'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_placed'));
        $data['app_text_reorder'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_reorder'));


        //Order Detail Page
        $data['app_text_order_details'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_order_details'));
        $data['app_text_status_history'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_status_history'));
        $data['app_text_shipping_address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_shipping_address'));
        $data['app_text_order_summary'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_order_summary'));
        $data['app_text_shipping_method'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_shipping_method'));
        $data['app_text_payment_method'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_payment_method'));
        $data['app_text_gift_wrapping'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_gift_wrapping'));
        $data['app_text_no_shipping_address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_no_shipping_address'));
        $data['app_text_no_order_details'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_no_order_details'));
        $data['app_text_payment_summary'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_payment_summary'));


        //Login Page
        $data['app_text_login'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_login'));
        $data['app_text_signup'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_signup'));
        $data['app_text_continue_guest'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_continue_guest'));
        $data['app_text_email'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_email'));
        $data['app_text_password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_password'));
        $data['app_text_forgot_password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_forgot_password'));
        $data['app_text_login_social_account'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_login_social_account'));
        $data['app_text_login_with_google'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_login_with_google'));
        $data['app_text_login_with_facebook'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_login_with_facebook'));
        $data['app_text_signup_with_google'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_signup_with_google'));
        $data['app_text_signup_with_facebook'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_signup_with_facebook'));
        $data['app_text_msg_enter_valid_email'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_enter_valid_email'));
        $data['app_text_msg_enter_email'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_enter_email'));
        $data['app_text_msg_enter_password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_enter_password'));
        $data['app_text_msg_password_characters_less_than_3'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_password_characters_less_than_3'));
        $data['app_text_msg_first_name'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_first_name'));
        $data['app_text_msg_last_name'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_last_name'));
        $data['app_text_msg_dob'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_dob'));
        $data['app_text_msg_personal_details'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_personal_details'));
        $data['app_text_msg_pass_not_matched'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_pass_not_matched'));
        $data['app_text_msg_phone_required'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_phone_required'));
        $data['app_text_msg_not_in_stock'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_not_in_stock'));

        //Seller Review Page
        $data['app_text_sold_by'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_sold_by'));
        $data['app_text_write_review'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_write_review'));
        $data['app_text_view_review'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_view_review'));
        $data['app_text_view_seller_products'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_view_seller_products'));
        $data['app_text_sellers'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_sellers'));
        $data['app_text_seller'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_seller'));
        $data['app_text_seller_shipping_policy'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_seller_shipping_policy'));
        $data['app_text_seller_return_policy'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_seller_return_policy'));

        //Message Page
        $data['app_text_wait'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_wait'));
        $data['app_text_loading'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_loading'));
        $data['app_text_msg_went_wrong'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_went_wrong'));
        $data['app_text_msg_no_internet_found'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_no_internet_found'));
        $data['app_text_msg_no_internet_title'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_no_internet_title'));
        $data['app_text_msg_request'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_request'));

        //Home Page Showcase
        $data['app_text_msg_order_placed_email'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_order_placed_email'));
        $data['app_text_msg_product_look'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_product_look'));
        $data['app_text_msg_search_product'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_search_product'));
        $data['app_text_msg_view_cart'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_view_cart'));
        $data['app_text_msg_logout'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_logout'));
        $data['app_text_msg_cancel_transaction'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_cancel_transaction'));
        $data['app_text_msg_confirm_payemnt_cancel'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_confirm_payemnt_cancel'));

        //Category Page Showcase
        $data['app_text_msg_view_product'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_view_product'));
        $data['app_text_msg_sort_product'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_sort_product'));
        $data['app_text_msg_filter_product'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_filter_product'));
        $data['app_text_text_next'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_text_next'));
        $data['app_text_text_okay'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_text_okay'));

        //Product Page Showcase
        $data['app_text_required'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_required'));
        $data['app_text_msg_rating'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_rating'));
        $data['app_text_msg_add_cart'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_add_cart'));
        $data['app_text_msg_add_wishlist'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_add_wishlist'));
        $data['app_text_msg_product_price'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_product_price'));
        $data['app_text_msg_product_not_available'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_product_not_available'));
        $data['app_text_msg_combination'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_combination'));
        $data['app_text_msg_product_no_stock'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_product_no_stock'));

        //Cart Page Showcase
        $data['app_text_msg_out_stock'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_out_stock'));
        $data['app_text_msg_checkout_products'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_checkout_products'));
        $data['app_text_msg_apply_voucher'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_apply_voucher'));
        $data['app_text_msg_update_quantity'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_update_quantity'));
        $data['app_text_msg_view_details'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_view_details'));
        $data['app_text_msg_success'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_success'));
        $data['app_text_msg_failure'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_failure'));
        $data['app_text_msg_enter_quantity'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_enter_quantity'));
        $data['app_text_quantity'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_quantity'));
        $data['app_text_remove'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_remove'));
        $data['app_text_customization_details'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_customization_details'));

        //Address Page Showcase
        $data['app_text_msg_add_address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_add_address'));
        $data['app_text_msg_update_address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_update_address'));
        $data['app_text_msg_select_address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_select_address'));
        $data['app_text_select_address_text'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_select_address_text'));
        $data['app_text_msg_no_product'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_no_product'));
        $data['app_text_msg_no_comment_available'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_no_comment_available'));

        //Login Page Showcase
        $data['app_text_msg_reset_password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_reset_password'));
        $data['app_text_msg_login'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_login'));
        $data['app_text_msg_login_fb'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_login_fb'));
        $data['app_text_msg_login_google'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_login_google'));

        //Order detail Page Showcase
        $data['app_text_msg_update_profile'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_update_profile'));

        //Added by Nitin Jain
        $data['app_text_product_removed'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_product_removed'));
        $data['app_text_msg_product_not_in_stock'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_product_not_in_stock'));
        $data['app_text_msg_product_removed_from_wishlist'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_product_removed_from_wishlist'));
        $data['app_text_msg_product_not_removed_from_wishlist'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_product_not_removed_from_wishlist'));
        $data['app_text_msg_product_not_added_to_wishlist'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_product_not_added_to_wishlist'));
        $data['app_text_currencies'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_currencies'));
        $data['app_text_msg_empty_payment_methods'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_empty_payment_methods'));
        $data['app_text_msg_choose_payment_method'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_choose_payment_method'));
        $data['app_text_addresses'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_addresses'));
        $data['app_text_billing_addresses'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_billing_addresses'));
        $data['app_text_msg_mobile_number'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_mobile_number'));
        $data['app_text_msg_Address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_Address'));
        $data['app_text_msg_Address_line_1'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_Address_line_1'));
        $data['app_text_msg_Address_line_2'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_Address_line_2'));
        $data['app_text_msg_city'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_city'));
        $data['app_text_msg_post_code'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_post_code'));
        $data['app_text_move_to_cart'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_move_to_cart'));
        $data['app_text_msg_cart_updated'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_cart_updated'));
        $data['app_text_msg_discount_removed'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_discount_removed'));
        $data['app_text_msg_select_shipping_method'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_select_shipping_method'));
        $data['app_text_msg_update_choose_shipping_address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_update_choose_shipping_address'));
        $data['app_text_same_as_shipping_address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_same_as_shipping_address'));
        $data['app_text_update_address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_update_address'));
        $data['app_text_add_new_address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_add_new_address'));
        $data['app_text_add_new_shipping_address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_add_new_shipping_address'));
        $data['app_text_add_new_billing_address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_add_new_billing_address'));
        $data['app_text_msg_please_enter'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_please_enter'));
        $data['app_text_msg_please_enter_atleast'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_please_enter_atleast'));
        $data['app_text_msg_characters_for'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_characters_for'));
        $data['app_text_msg_no_product_found'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_no_product_found'));
        $data['app_text_msg_user_detail_updated'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_user_detail_updated'));
        $data['app_text_msg_update_user_details'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_update_user_details'));
        $data['app_text_msg_empty_wishlist'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_empty_wishlist'));
        $data['app_text_msg_enter_order_comment'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_enter_order_comment'));

        $data['app_text_msg_view_selected_filter'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_view_selected_filter'));
        $data['app_text_msg_clear_filter'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_clear_filter'));
        $data['app_text_choose_app_theme'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_choose_app_theme'));
        $data['app_text_view_demo'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_view_demo'));
        $data['app_text_view_your_store'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_view_your_store'));
        $data['app_text_view_store'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_view_store'));
        $data['app_text_billing_address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_billing_address'));
        $data['app_text_billing_details'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_billing_details'));
        $data['app_text_apply_coupon'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_apply_coupon'));
        $data['app_text_entry_coupon_code'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_entry_coupon_code'));

	$data['app_text_msg_confirm_password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_confirm_password'));
        $data['app_text_product_info_and_care'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_product_info_and_care'));
        $data['app_text_download'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_download'));
        $data['app_text_minimal_quantity'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_minimal_quantity'));
        $data['app_text_pack_content'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_pack_content'));
        $data['app_text_seller_details'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_seller_details'));
        $data['app_text_submit'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_submit'));
        $data['app_text_msg_forget_password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_forget_password'));
        $data['app_text_enter_your_email'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_enter_your_email'));
        $data['app_text_reset_password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_reset_password'));
        $data['app_text_view_selected_filter'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_view_selected_filter'));
        $data['app_text_clear_selected_filter'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_clear_selected_filter'));
        $data['app_text_no_filter'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_no_filter'));
        $data['app_text_phone_number_required'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_phone_number_required'));
        $data['app_text_refresh'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_refresh'));
        $data['app_text_internet_connection_failed'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_internet_connection_failed'));
        $data['app_text_internet_connection_problem'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_internet_connection_problem'));
        $data['app_text_cancel_transaction'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_cancel_transaction'));
        $data['app_text_middle_of_payment'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_middle_of_payment'));
        $data['app_text_yes'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_yes'));
        $data['app_text_no'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_no'));
        $data['app_text_enter_your_comment'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_enter_your_comment'));
        $data['app_text_msg_shipping_methods_unavailable'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_shipping_methods_unavailable'));
        $data['app_text_msg_select_shipping_methods'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_msg_select_shipping_methods'));
        $data['app_text_same_as_shipping_details'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_same_as_shipping_details'));
        $data['app_text_share_log'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_share_log'));
        $data['app_text_payment_failed_contact_support'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_payment_failed_contact_support'));
        $data['app_text_choose_payment_method'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_choose_payment_method'));
        $data['app_text_no_payment_method'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_no_payment_method'));
        $data['app_text_no_payment_currently'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_no_payment_currently'));
        $data['app_text_no_make_payment'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_no_make_payment'));
        $data['app_text_make_payment_after_select'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_make_payment_after_select'));
        $data['app_text_title_payment_paypal'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_title_payment_paypal'));
        $data['app_text_order_total_price'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_order_total_price'));
        $data['app_text_user_cancelled_the_payment'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_user_cancelled_the_payment'));
        $data['app_text_invalid_payment_try_again'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_invalid_payment_try_again'));
        $data['app_text_payment_failed_contact_support'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_payment_failed_contact_support'));
        $data['app_text_choose_payment_method'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_choose_payment_method'));
        $data['app_text_no_payment_method'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_no_payment_method'));
        $data['app_text_no_payment_currently'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_no_payment_currently'));
        $data['app_text_no_make_payment'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_no_make_payment'));
        $data['app_text_make_payment_after_select'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_make_payment_after_select'));
        $data['app_text_title_paypal_payment'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_title_paypal_payment'));
        $data['app_text_order_total_price'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_order_total_price'));
        $data['app_text_user_cancelled_the_payment'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_user_cancelled_the_payment'));
        $data['app_text_invalid_payment_try_again'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_invalid_payment_try_again'));
        $data['app_text_wishlist_item_removed'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_wishlist_item_removed'));
        $data['app_text_wishlist_item_added'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_wishlist_item_added'));
        $data['app_text_subject'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_subject'));
        $data['app_text_extra_share'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_extra_share'));
        $data['app_text_intent_title'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_intent_title'));
        $data['app_text_error_product_name_url'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_error_product_name_url'));
        $data['app_text_tag_error'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_tag_error'));
        $data['app_text_ok'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_ok'));
        $data['app_text_currency'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_currency'));
        $data['app_text_same_as_shipping_details'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_same_as_shipping_details'));
        $data['app_text_choose_your_app_theme'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_choose_your_app_theme'));
        $data['app_text_terms_and_conditions'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_terms_and_conditions'));
        $data['app_text_wishlist_only'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_wishlist_only'));
        $data['app_text_wishlist_remove'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_wishlist_remove'));
        $data['app_text_state_not_required'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_state_not_required'));
        $data['app_text_payment_failed'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_payment_failed'));
        $data['app_text_entered_wrong_url'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_entered_wrong_url'));
        $data['app_text_login_failed'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_login_failed'));
        $data['app_text_install_module'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_install_module'));
        $data['app_text_choose_your_app_theme'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_choose_your_app_theme'));
        $data['app_text_no_data_found'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_no_data_found'));
        $data['app_text_enter_store_url'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_enter_store_url'));
        $data['app_text_fill_all_required_fields'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_fill_all_required_fields'));
        $data['app_text_sellers'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_sellers'));
        $data['app_text_seller'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_seller'));
        $data['app_text_seller_shipping_policy'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_seller_shipping_policy'));
        $data['app_text_seller_return_policy'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_seller_return_policy'));
        $data['app_text_review_title'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_review_title'));
        $data['app_text_review_comment'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_review_comment'));
        $data['app_text_review_rating'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_review_rating'));
        $data['app_text_filter_products'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_filter_products'));
        $data['app_text_select_category'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_select_category'));
        $data['app_text_select_sort'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_select_sort'));
        $data['app_text_clear'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_clear'));
        
        $data['app_text_state'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_state'));
        $data['app_text_text_state'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_state'));
        $data['app_text_add_to_cart'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_add_to_cart'));
	$data['app_text_add_to_wishlist'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_add_to_wishlist'));    

        /* Banner countdown changes */

        $data["app_text_hour"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_hour'));
        $data["app_text_min"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_min'));
        $data["app_text_sec"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_sec'));
        $data["app_text_sale"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_sale'));
        $data["app_text_new"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_new'));



        /* Whatsapp Chat */

        $data["app_text_install_whatsapp"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_install_whatsapp'));



        /* Biometric Auth */

        $data["app_text_no_account_registered"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_no_account_registered'));
        $data["app_text_confirm_for_authentication"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_confirm_for_authentication'));
        $data["app_text_login_with_fingerprint"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_login_with_fingerprint'));
        $data["app_text_register_for_fingerprint"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_register_for_fingerprint'));
        $data["app_text_biometric_not_available"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_biometric_not_available'));
        $data["app_text_fingerprint_not_recognized"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_fingerprint_not_recognized'));
        $data["app_text_face_not_recognizes"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_face_not_recognizes'));
        $data["app_text_fingerprint_or_face_not_enrolled"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_fingerprint_or_face_not_enrolled'));
        $data["app_text_biometry_locked_out"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_biometry_locked_out'));
        $data["app_text_register_for_fingerpring"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_register_for_fingerpring'));
        $data["app_text_register_for_fingerprint_details"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_register_for_fingerprint_details'));
        $data["app_text_register"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_register'));
        $data["app_text_fingerprint_not_enrolled"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_fingerprint_not_enrolled'));
        $data["app_text_android_marshmallow_required"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_android_marshmallow_required'));
        $data["app_text_fingerprint_error_try_again"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_fingerprint_error_try_again'));
        $data["app_text_fingerprint_wipe_sensor_try_again"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_fingerprint_wipe_sensor_try_again'));
        $data["app_text_skip_and_continue"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_skip_and_continue'));
        $data["app_text_place_your_finger_on_sensor"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_place_your_finger_on_sensor'));



        /* OTP Changes */

        $data["app_text_verify"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_verify'));
        $data["app_text_get_otp"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_get_otp'));
        $data["app_text_mobile_number"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_mobile_number'));
        $data["app_text_mobile_number_optional"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_mobile_number_optional'));
        $data["app_text_error_number_verification"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_error_number_verification'));
        $data["app_text_phone_number_already_exists"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_phone_number_already_exists'));
        $data["app_text_number_verification"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_number_verification'));
        $data["app_text_number_verification_description"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_number_verification_description'));
        $data["app_text_login_with_phone_number"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_login_with_phone_number'));
        $data["app_text_forget_password_description"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_forget_password_description'));
        $data["app_text_enter_email_or_mobile_number"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_enter_email_or_mobile_number'));
        $data["app_text_phone_number_change_message"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_phone_number_change_message'));
        $data["app_text_enter_otp"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_enter_otp'));
        $data["app_text_recent_product"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_recent_product'));
        $data["app_text_invalid_phone_number"] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_invalid_phone_number'));
        $data['app_text_please_login'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_please_login'));
        $data['app_text_your_wishlist_is_empty'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_your_wishlist_is_empty')); 
        
        $translated_texts = array();
        foreach ($data as $key => $value) {
            $translated_texts[] = array(
                'unique_key' => $key,
                'iso_code' => $iso_code,
                'trans_text' => $value
            );
        }
        return $translated_texts;
    }

}
