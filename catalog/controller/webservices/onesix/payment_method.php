<?php

class ControllerWebservicesOneSixPaymentMethod extends Controller {

    public function getBillingAddress($id) {
        //$this->request->post['email'] = 'xxxx@gmail.com';
        $this->load->model('webservices/home');


        $data['billing_address'] = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            die;
        } else {
            $data['install_module'] = '';
        }

        if (!empty($this->request->post['email'])) {
            $this->load->model('account/customer');
            $customer_email = $this->request->post['email'];
            $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
            if (!empty($check_customer)) {
                $customer_id = $check_customer['customer_id'];
                $this->session->data['customer_id'] = $customer_id;
                $this->load->model('account/address');
                $this->load->model('webservices/checkout');
                if ($id == 0) {
                    $id = $check_customer['address_id'];
                }
                $addresses = $this->model_webservices_checkout->getAddress($id);
                if (!$addresses) {
                    $addresses = $this->model_webservices_checkout->getAddress(0);
                }

                //$this->session->data['billing_address'] = $addresses;
                foreach ($addresses as $address) {
                    $this->session->data['payment_address'] = $address;
                    $this->session->data['payment_address']['country_id'] = $address['country_id'];
                    $this->session->data['payment_address']['zone_id'] = $address['zone_id'];
                    //$this->session->data['billing_address']['country_id'] = $address['country_id'];
                    //$this->session->data['billing_address']['zone_id'] = $address['zone_id'];
                    $data['billing_address'] = array(
                        'id_shipping_address' => $address['address_id'],
                        'firstname' => $address['firstname'],
                        'lastname' => $address['lastname'],
                        'company' => $address['company'],
                        'mobile_no' => $address['custom_field'] ? $address['custom_field'][1] : '',
                        'address_1' => $address['address_1'],
                        'address_2' => $address['address_2'],
                        'city' => $address['city'],
                        'state' => $address['zone'],
                        'country_id' => $address['country_id'],
                        'zone_id' => $address['zone_id'],
                        'country' => $address['country'],
                        'postcode' => $address['postcode'],
                        'alias' => ''
                    );
                }
            }
        }
        return $data;
        die();
    }

    public function appGetPaymentMethods() {
        
        if (isset($this->request->post['session_data'])) {
            $ses = str_replace(";", "", str_replace("OCSESSID=", "", $this->request->post['session_data']));
                if($this->session->getId() != $ses) {
                    $this->session_id = $ses;
                    setcookie('OCSESSID', $ses, ini_get('session.cookie_lifetime'), ini_get('session.cookie_path'), ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
                    setcookie('OCSESSID', $ses, ini_get('session.cookie_lifetime'), ini_get('session.cookie_path'), ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
                $this->response->redirect($this->url->link('webservices/onesix/payment_method/appGetPaymentMethods','','true')."&version=".$this->request->get['version']."&order_message=" . $this->request->post['order_message']."&s_id=".$this->request->post['session_data']);
                die();
            }
        }
if(isset($this->request->get['s_id'])){
	$data['s_id'] = $this->request->get['s_id'];
}elseif(isset($this->request->post['session_data'])){
	$data['s_id'] = $this->request->post['session_data'];
}

        if(isset($this->request->get['order_message'])){
            $this->request->post['order_message'] = $this->request->get['order_message'];
        }
        
        $this->load->model('webservices/numberstring');
        $app_version = $this->request->get['version'];

        $app_version_explodes = explode(".", $app_version);
        $app_version_path = '';
        foreach ($app_version_explodes as $app_version_explode) {
            $app_version_path .= $this->model_webservices_numberstring->N2L($app_version_explode);
        }

        $this->load->language('checkout/checkout');
        /*if (isset($this->request->post['id_shipping_address'])) {
            $this->getBillingAddress($this->request->post['id_shipping_address']);
        }*/

        if (isset($this->session->data['payment_address'])) {
            // Selected payment methods should be from cart sub total not total!
            $total = $this->cart->getSubTotal();

            // Payment Methods
            $method_data = array();

            $this->load->model('setting/extension');

            $results = $this->model_setting_extension->getExtensions('payment');

            $recurring = $this->cart->hasRecurringProducts();

            foreach ($results as $result) {
                if ($this->config->get('payment_'.$result['code'] . '_status')) {
                    $this->load->model('extension/payment/' . $result['code']);

                    $method = $this->{'model_extension_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);

                    if ($method) {
                        if ($recurring) {
                            if (method_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_payment_' . $result['code']}->recurringPayments()) {
                                $method_data[$result['code']] = $method;
                            }
                        } else {
                            $method_data[$result['code']] = $method;
                        }
                    }
                }
            }

            $sort_order = array();

            foreach ($method_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $method_data);

            $this->session->data['payment_methods'] = $method_data;
        }

        $data['text_payment_method'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_payment_method'));
        $data['text_comments'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_comments'));
        $data['text_loading'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_loading'));

        $data['button_continue'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('button_continue'));

        if (empty($this->session->data['payment_methods'])) {
            $data['error_warning'] = sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_no_payment')), $this->url->link('information/contact','','true'));
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['payment_methods'])) {
            $data['payment_methods'] = $this->session->data['payment_methods'];
        } else {
            $data['payment_methods'] = array();
        }

        if (isset($this->session->data['payment_method']['code'])) {
            $data['code'] = $this->session->data['payment_method']['code'];
        } else {
            $data['code'] = '';
        }

        if (isset($this->session->data['comment'])) {
            $data['comment'] = $this->session->data['comment'];
        } else {
            $data['comment'] = '';
        }

        $data['scripts'] = $this->document->getScripts();

        if ($this->config->get('config_checkout_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

            if ($information_info) {
                $data['text_agree'] = sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_agree')), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), 'true')."&status=tnc", $information_info['title'], $information_info['title']);
            } else {
                $data['text_agree'] = '';
            }
        } else {
            $data['text_agree'] = '';
        }

        if (isset($this->session->data['agree'])) {
            $data['agree'] = $this->session->data['agree'];
        } else {
            $data['agree'] = '';
        }
	 if(!isset($this->request->post['order_message'])){
		$this->request->post['order_message'] = '';	
	 }
        $data['paymentSaveURL'] = str_replace("amp;", "", $this->url->link('webservices/' . $app_version_path . '/payment_method/paymentSave', 'order_message=' . $this->request->post['order_message']."&s_id=".$data['s_id'],'true'));

        if (version_compare(VERSION, '2.2.0.0', '<')) {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/webservices/payment_method.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/webservices/payment_method.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/webservices/payment_method.tpl', $data));
            }
        } else {
            echo $this->load->view('webservices/payment_method', $data);
        }
    }
    private function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    public function confirm() {

        $this->load->model('webservices/numberstring');
        $app_version = $this->request->get['version'];

        $app_version_explodes = explode(".", $app_version);
        $app_version_path = '';
        foreach ($app_version_explodes as $app_version_explode) {
            $app_version_path .= $this->model_webservices_numberstring->N2L($app_version_explode);
        }
        if (function_exists('getallheaders')) {
            $request_headers = getallheaders();
        } else {
            $request_headers = $this->getallheaders();
        }
        $this->load->model('webservices/home');
        foreach ($request_headers as $name => $value) {
            if ($name == "confirm_order") {
                $this->request->post['confirm_order'] = $value;
            }
        }

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            $this->model_webservices_home->logKnowbandAppRequest("paymentMethod", json_encode($data));
            echo json_encode($data);
            die;
        }

        $this->load->model('account/customer');

        if (true) {
            $this->load->model('account/address');
            $this->load->model('webservices/checkout');
            $this->load->model('webservices/wishlist');

            if (isset($this->request->post['order_message'])) {
                $this->session->data['comment'] = strip_tags($this->request->post['order_message']);
            }


            if (isset($this->request->post['coupon']) && version_compare(VERSION, '2.1.0.1', '>=')) {
                $coupon = $this->request->post['coupon'];
                $this->load->model('extension/total/coupon');
                $this->load->language('extension/total/coupon');
                $coupon_info = $this->model_extension_total_coupon->getCoupon($coupon);
                if ($coupon_info) {
                    $this->session->data['coupon'] = $this->request->post['coupon'];
                    $data['coupon'] = array('status' => 'success', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_success')));
                } else {
                    $data['coupon'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_coupon')));
                }
            } else if (isset($this->request->post['coupon'])) {
                $this->session->data['coupon'] = $this->request->post['coupon'];
                $this->load->language('extension/total/coupon');
                $this->load->model('extension/total/coupon');
                $coupon_info = $this->model_extension_total_coupon->getCoupon($this->session->data['coupon']);
                if ($coupon_info) {
                    $data['coupon'] = array('status' => 'success', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_success')));
                } else {
                    $data['coupon'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_coupon')));
                }
            }
            if (isset($this->request->post['voucher']) && version_compare(VERSION, '2.1.0.1', '>=')) {
                $this->load->model('extension/total/voucher');
                $this->load->language('extension/total/voucher');
                $voucher = $this->request->post['voucher'];
                $voucher_info = $this->model_extension_total_voucher->getVoucher($voucher);
                if ($voucher_info) {
                    $this->session->data['voucher'] = $this->request->post['voucher'];
                    $data['voucher'] = array('status' => 'success', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_success')));
                } else {
                    $data['voucher'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_voucher')));
                }
            } else if (isset($this->request->post['voucher'])) {
                $this->session->data['voucher'] = $this->request->post['voucher'];
                $this->load->language('extension/total/voucher');
                $this->load->model('extension/total/voucher');
                $coupon_info = $this->model_extension_total_voucher->getVoucher($this->session->data['voucher']);
                if ($coupon_info) {
                    $data['voucher'] = array('status' => 'success', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_success')));
                } else {
                    $data['voucher'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_voucher')));
                }
            }

            if (isset($this->request->post['shipping_method_code'])) {
                $quote_data = array();
                $this->load->model('setting/extension');
                $this->session->data['shipping_method'] = $this->request->post['shipping_method_code'];
                $results = $this->model_setting_extension->getExtensions('shipping');
                foreach ($results as $result) {
                    if ($this->config->get('shipping_'.$result['code'] . '_status')) {
                        $this->load->model('extension/shipping/' . $result['code']);

                        $quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

                        if ($quote) {
                            $quote_data[$result['code']] = array(
                                'title' => $quote['title'],
                                'quote' => $quote['quote'],
                                'sort_order' => $quote['sort_order'],
                                'error' => $quote['error']
                            );
                        }
                    }
                }
                $sort_order = array();

                foreach ($quote_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $quote_data);

                $this->session->data['shipping_methods'] = $quote_data;
                if ($this->session->data['shipping_methods']) {
                    $shipping = explode('.', $this->request->post['shipping_method_code']);
                    $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                }
            }

            if (true) {

                $totals = array();
                $total = 0;
                $taxes = $this->cart->getTaxes();

                $total_data = array(
                        'totals' => &$total_data,
                        'taxes'  => &$taxes,
                        'total'  => &$total
                );
                
                $this->load->model('setting/extension');

                $sort_order = array();

                $results = $this->model_setting_extension->getExtensions('total');

                foreach ($results as $key => $value) {
                    $sort_order[$key] = $this->config->get('total_'.$value['code'] . '_sort_order');
                }

                array_multisort($sort_order, SORT_ASC, $results);

                foreach ($results as $result) {
                    if ($this->config->get('total_'.$result['code'] . '_status')) {
                        $this->load->model('extension/total/' . $result['code']);

                        $this->{'model_extension_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                    }
                }

                // Payment Methods
                $method_data = array();

                $this->load->model('setting/extension');

                $results = $this->model_setting_extension->getExtensions('payment');

                $recurring = $this->cart->hasRecurringProducts();



                foreach ($results as $result) {
                    if ($this->config->get('payment_'.$result['code'] . '_status')) {
                        $this->load->model('extension/payment/' . $result['code']);

                        $method = $this->{'model_extension_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);

                        if ($method) {
                            if ($recurring) {
                                if (method_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_payment_' . $result['code']}->recurringPayments()) {
                                    $method_data[$result['code']] = $method;
                                }
                            } else {
                                $method_data[$result['code']] = $method;
                            }
                        }
                    }
                }

                $sort_order = array();

                foreach ($method_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $method_data);
                $this->session->data['payment_methods'] = $method_data;
                //$this->request->post['payment_method'] = $this->request->post['payment_method_code'];
            }
        }
        ?>
        <script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
        <link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
        <script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <style> .table-responsive {padding:5% 5% 2% 5%;} .buttons, #button-payment-method { width: 60%; text-align: center;}#button-confirm { font-size: 18px; padding: 7px; width:300px;} </style>
        <?php

        $json = array();

        // Validate if payment address has been set.
        if (!isset($this->session->data['payment_address'])) {
            $json['error'] = 'Problem with Payment Address';
        }

        // Validate minimum quantity requirements.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $json['redirect'] = $this->url->link('checkout/cart', "status=kbmobileappredirecterror",'true');

                break;
            }
        }

        if (!isset($this->request->post['payment_method'])) {
            $json['error']['warning'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_payment'));
        } elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
            $json['error']['warning'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_payment'));
        }
        if (!$json) {
            $this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];

            //$this->session->data['comment'] = strip_tags($this->request->post['comment']);
        }
        $redirect = '';

        if ($this->cart->hasShipping()) {
            // Validate if shipping address has been set.
            if (!isset($this->session->data['shipping_address'])) {
                $redirect = $this->url->link('checkout/checkout', 'status=kbmobileappredirecterror', 'true');
            }

            // Validate if shipping method has been set.
            if (!isset($this->session->data['shipping_method'])) {
                $redirect = $this->url->link('checkout/checkout', 'status=kbmobileappredirecterror', 'true');
            }
        } else {
            unset($this->session->data['shipping_address']);
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
        }

        // Validate if payment address has been set.
        if (!isset($this->session->data['payment_address'])) {
            $redirect = $this->url->link('checkout/checkout', 'status=kbmobileappredirecterror', 'true');
        }

        // Validate if payment method has been set.
        if (!isset($this->session->data['payment_method'])) {
            $redirect = $this->url->link('checkout/checkout', 'status=kbmobileappredirecterror', 'true');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $redirect = $this->url->link('checkout/cart', 'status=kbmobileappredirecterror','true');
        }


        // Validate minimum quantity requirements.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $redirect = $this->url->link('checkout/cart', 'status=kbmobileappredirecterror','true');

                break;
            }
        }

        if (!$redirect) {
            //$this->response->redirect($this->url->link('webservices/' . $app_version_path . '/payment_method/confirmOrder'));
            $this->confirmOrder();
            die;
            $order_data = array();

            $order_data['totals'] = array();
            $total = 0;
            $taxes = $this->cart->getTaxes();

            $order_data['totals'] = array(
                    'totals' => &$order_data,
                    'taxes'  => &$taxes,
                    'total'  => &$total
            );
            
            $this->load->model('setting/extension');

            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get('total_'.$value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get('total_'.$result['code'] . '_status')) {
                    $this->load->model('extension/total/' . $result['code']);

                    $this->{'model_extension_total_' . $result['code']}->getTotal($order_data['totals'], $total, $taxes);
                }
            }

            $sort_order = array();

            foreach ($order_data['totals'] as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $order_data['totals']);

            $this->load->language('checkout/checkout');

            $order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
            $order_data['store_id'] = $this->config->get('config_store_id');
            $order_data['store_name'] = $this->config->get('config_name');
            $order_data['customer_id'] = $customer_id;
            $order_data['customer_group_id'] = $customer_id;
            $order_data['firstname'] = $check_customer['firstname'];
            $order_data['lastname'] = $check_customer['lastname'];
            $order_data['email'] = $check_customer['email'];
            $order_data['telephone'] = $check_customer['telephone'];
            $order_data['fax'] = $check_customer['fax'];

            if ($order_data['store_id']) {
                $order_data['store_url'] = $this->config->get('config_url');
            } else {
                $order_data['store_url'] = HTTP_SERVER;
            }

            $order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
            $order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
            $order_data['payment_company'] = $this->session->data['payment_address']['company'];
            $order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
            $order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
            $order_data['payment_city'] = $this->session->data['payment_address']['city'];
            $order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
            $order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
            $order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
            $order_data['payment_country'] = $this->session->data['payment_address']['country'];
            $order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
            $order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
            $order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());

            if (isset($this->session->data['payment_method']['title'])) {
                $order_data['payment_method'] = $this->session->data['payment_method']['title'];
            } else {
                $order_data['payment_method'] = '';
            }

            if (isset($this->session->data['payment_method']['code'])) {
                $order_data['payment_code'] = $this->session->data['payment_method']['code'];
            } else {
                $order_data['payment_code'] = '';
            }

            if ($this->cart->hasShipping()) {
                $order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
                $order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
                $order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
                $order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
                $order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
                $order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
                $order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
                $order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
                $order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
                $order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
                $order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
                $order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
                $order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : array());

                if (isset($this->session->data['shipping_method']['title'])) {
                    $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
                } else {
                    $order_data['shipping_method'] = '';
                }

                if (isset($this->session->data['shipping_method']['code'])) {
                    $order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
                } else {
                    $order_data['shipping_code'] = '';
                }
            } else {
                $order_data['shipping_firstname'] = '';
                $order_data['shipping_lastname'] = '';
                $order_data['shipping_company'] = '';
                $order_data['shipping_address_1'] = '';
                $order_data['shipping_address_2'] = '';
                $order_data['shipping_city'] = '';
                $order_data['shipping_postcode'] = '';
                $order_data['shipping_zone'] = '';
                $order_data['shipping_zone_id'] = '';
                $order_data['shipping_country'] = '';
                $order_data['shipping_country_id'] = '';
                $order_data['shipping_address_format'] = '';
                $order_data['shipping_custom_field'] = array();
                $order_data['shipping_method'] = '';
                $order_data['shipping_code'] = '';
            }

            $order_data['products'] = array();

            foreach ($this->cart->getProducts() as $product) {
                $option_data = array();

                foreach ($product['option'] as $option) {
                    $option_data[] = array(
                        'product_option_id' => $option['product_option_id'],
                        'product_option_value_id' => $option['product_option_value_id'],
                        'option_id' => $option['option_id'],
                        'option_value_id' => $option['option_value_id'],
                        'name' => $option['name'],
                        'value' => $option['value'],
                        'type' => $option['type']
                    );
                }

                $order_data['products'][] = array(
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                    'download' => $product['download'],
                    'quantity' => $product['quantity'],
                    'subtract' => $product['subtract'],
                    'price' => $product['price'],
                    'total' => $product['total'],
                    'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
                    'reward' => $product['reward']
                );
            }

            // Gift Voucher
            $order_data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $voucher) {
                    $order_data['vouchers'][] = array(
                        'description' => $voucher['description'],
                        'code' => substr(md5(mt_rand()), 0, 10),
                        'to_name' => $voucher['to_name'],
                        'to_email' => $voucher['to_email'],
                        'from_name' => $voucher['from_name'],
                        'from_email' => $voucher['from_email'],
                        'voucher_theme_id' => $voucher['voucher_theme_id'],
                        'message' => $voucher['message'],
                        'amount' => $voucher['amount']
                    );
                }
            }

            $order_data['comment'] = $this->session->data['comment'];
            $order_data['total'] = $total;

            if (isset($this->request->cookie['tracking'])) {
                $order_data['tracking'] = $this->request->cookie['tracking'];

                $subtotal = $this->cart->getSubTotal();

                // Affiliate
                $this->load->model('affiliate/affiliate');

                $affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);

                if ($affiliate_info) {
                    $order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
                    $order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
                } else {
                    $order_data['affiliate_id'] = 0;
                    $order_data['commission'] = 0;
                }

                // Marketing
                $this->load->model('checkout/marketing');

                $marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

                if ($marketing_info) {
                    $order_data['marketing_id'] = $marketing_info['marketing_id'];
                } else {
                    $order_data['marketing_id'] = 0;
                }
            } else {
                $order_data['affiliate_id'] = 0;
                $order_data['commission'] = 0;
                $order_data['marketing_id'] = 0;
                $order_data['tracking'] = '';
            }

            $order_data['language_id'] = $this->config->get('config_language_id');
            $order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
            $order_data['currency_code'] = $this->session->data['currency'];
            $order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
            $order_data['ip'] = $this->request->server['REMOTE_ADDR'];

            if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
                $order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
            } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
                $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
            } else {
                $order_data['forwarded_ip'] = '';
            }

            if (isset($this->request->server['HTTP_USER_AGENT'])) {
                $order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
            } else {
                $order_data['user_agent'] = '';
            }

            if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
            } else {
                $order_data['accept_language'] = '';
            }

            $this->load->model('checkout/order');
            $this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);
            $this->load->model('webservices/fcm');
            $this->model_webservices_fcm->updateFcmOrderIdBySessionId();
            $data['text_recurring_item'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_recurring_item'));
            $data['text_payment_recurring'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_payment_recurring'));

            $data['column_name'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('column_name'));
            $data['column_model'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('column_model'));
            $data['column_quantity'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('column_quantity'));
            $data['column_price'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('column_price'));
            $data['column_total'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('column_total'));

            $this->load->model('tool/upload');

            $data['products'] = array();

            foreach ($this->cart->getProducts() as $product) {
                $option_data = array();

                foreach ($product['option'] as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['value'];
                    } else {
                        $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                        if ($upload_info) {
                            $value = $upload_info['name'];
                        } else {
                            $value = '';
                        }
                    }

                    $option_data[] = array(
                        'name' => $option['name'],
                        'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                    );
                }

                $recurring = '';

                if ($product['recurring']) {
                    $frequencies = array(
                        'day' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_day')),
                        'week' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_week')),
                        'semi_month' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_semi_month')),
                        'month' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_month')),
                        'year' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_year')),
                    );

                    if ($product['recurring']['trial']) {
                        $recurring = sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_trial_description')), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
                    }

                    if ($product['recurring']['duration']) {
                        $recurring .= sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_payment_description')), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
                    } else {
                        $recurring .= sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_payment_cancel')), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
                    }
                }

                $data['products'][] = array(
                    'key' => $product['key'],
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                    'recurring' => $recurring,
                    'quantity' => $product['quantity'],
                    'subtract' => $product['subtract'],
                    'price' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                    'total' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']),
                    'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'],'true'),
                );
            }

            // Gift Voucher
            $data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $voucher) {
                    $data['vouchers'][] = array(
                        'description' => $voucher['description'],
                        'amount' => $this->currency->format($voucher['amount'], $this->session->data['currency'])
                    );
                }
            }

            $data['totals'] = array();

            foreach ($order_data['totals'] as $total) {
                $data['totals'][] = array(
                    'title' => $total['title'],
                    'text' => $this->currency->format($total['value'], $this->session->data['currency']),
                );
            }

            $data['payment'] = $this->load->controller('extension/payment/' . $this->session->data['payment_method']['code']);
        } else {
            $data['redirect'] = $redirect;
        }

        if (version_compare(VERSION, '2.2.0.0', '<')) {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/confirm.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/confirm.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/checkout/confirm.tpl', $data));
            }
        } else {
            echo $this->load->view('checkout/confirm', $data);
        }
    }

    function confirmOrder() {
        ?>

        <script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
        <link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
        <script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <style>  .buttons { width: 100%; padding-left:10%;padding-right:10%; text-align: center;}#button-confirm { font-size: 18px; padding: 7px; width:300px;}</style>
        <?php


        $this->load->model('account/customer');
        $customer_id = $this->session->data['customer_id'];
        $check_customer = $this->model_account_customer->getCustomer($customer_id);
        $customer_group_id = $check_customer['customer_group_id'];


        $order_data = array();

        $order_data['totals'] = array();

        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;
        // Because __call can not keep var references so we put them into an array. 			
        $total_data = array(
            'totals' => &$totals,
            'taxes' => &$taxes,
            'total' => &$total
        );

        $this->load->model('setting/extension');

        $sort_order = array();

        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get('total_'.$value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get('total_'.$result['code'] . '_status')) {
                $this->load->model('extension/total/' . $result['code']);

                // We have to put the totals in an array so that they pass by reference.

                if (version_compare(VERSION, '2.2.0.0', '>=')) {
                    $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                } else {
                    $this->{'model_extension_total_' . $result['code']}->getTotal($totals, $total, $taxes);
                }
            }
        }

        $order_data['totals'] = $totals;

        $sort_order = array();

        foreach ($order_data['totals'] as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $order_data['totals']);

        $this->load->language('checkout/checkout');

        $order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
        $order_data['store_id'] = $this->config->get('config_store_id');
        $order_data['store_name'] = $this->config->get('config_name');
        $order_data['customer_id'] = $customer_id;
        $order_data['customer_group_id'] = $customer_id;
        $order_data['firstname'] = $check_customer['firstname'];
        $order_data['lastname'] = $check_customer['lastname'];
        $order_data['email'] = $check_customer['email'];
        $order_data['telephone'] = $check_customer['telephone'];
        $order_data['fax'] = $check_customer['fax'];

        if ($order_data['store_id']) {
            $order_data['store_url'] = $this->config->get('config_url');
        } else {
            $order_data['store_url'] = HTTP_SERVER;
        }

        $order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
        $order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
        $order_data['payment_company'] = $this->session->data['payment_address']['company'];
        $order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
        $order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
        $order_data['payment_city'] = $this->session->data['payment_address']['city'];
        $order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
        $order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
        $order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
        $order_data['payment_country'] = $this->session->data['payment_address']['country'];
        $order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
        $order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
        $order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());

        if (isset($this->session->data['payment_method']['title'])) {
            $order_data['payment_method'] = $this->session->data['payment_method']['title'];
        } else {
            $order_data['payment_method'] = '';
        }

        if (isset($this->session->data['payment_method']['code'])) {
            $order_data['payment_code'] = $this->session->data['payment_method']['code'];
        } else {
            $order_data['payment_code'] = '';
        }

        if ($this->cart->hasShipping()) {
            $order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
            $order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
            $order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
            $order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
            $order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
            $order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
            $order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
            $order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
            $order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
            $order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
            $order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
            $order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
            $order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : array());

            if (isset($this->session->data['shipping_method']['title'])) {
                $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
            } else {
                $order_data['shipping_method'] = '';
            }

            if (isset($this->session->data['shipping_method']['code'])) {
                $order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
            } else {
                $order_data['shipping_code'] = '';
            }
        } else {
            $order_data['shipping_firstname'] = '';
            $order_data['shipping_lastname'] = '';
            $order_data['shipping_company'] = '';
            $order_data['shipping_address_1'] = '';
            $order_data['shipping_address_2'] = '';
            $order_data['shipping_city'] = '';
            $order_data['shipping_postcode'] = '';
            $order_data['shipping_zone'] = '';
            $order_data['shipping_zone_id'] = '';
            $order_data['shipping_country'] = '';
            $order_data['shipping_country_id'] = '';
            $order_data['shipping_address_format'] = '';
            $order_data['shipping_custom_field'] = array();
            $order_data['shipping_method'] = '';
            $order_data['shipping_code'] = '';
        }

        $order_data['products'] = array();

        foreach ($this->cart->getProducts() as $product) {
            $option_data = array();

            foreach ($product['option'] as $option) {
                $option_data[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'product_option_value_id' => $option['product_option_value_id'],
                    'option_id' => $option['option_id'],
                    'option_value_id' => $option['option_value_id'],
                    'name' => $option['name'],
                    'value' => $option['value'],
                    'type' => $option['type']
                );
            }

            $order_data['products'][] = array(
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'model' => $product['model'],
                'option' => $option_data,
                'download' => $product['download'],
                'quantity' => $product['quantity'],
                'subtract' => $product['subtract'],
                'price' => $product['price'],
                'total' => $product['total'],
                'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
                'reward' => $product['reward']
            );
        }

        // Gift Voucher
        $order_data['vouchers'] = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {
                $order_data['vouchers'][] = array(
                    'description' => $voucher['description'],
                    'code' => substr(md5(mt_rand()), 0, 10),
                    'to_name' => $voucher['to_name'],
                    'to_email' => $voucher['to_email'],
                    'from_name' => $voucher['from_name'],
                    'from_email' => $voucher['from_email'],
                    'voucher_theme_id' => $voucher['voucher_theme_id'],
                    'message' => $voucher['message'],
                    'amount' => $voucher['amount']
                );
            }
        }

        $order_data['comment'] = $this->session->data['comment'];
        $order_data['total'] = $total;

        if (isset($this->request->cookie['tracking'])) {
            $order_data['tracking'] = $this->request->cookie['tracking'];

            $subtotal = $this->cart->getSubTotal();

            // Affiliate
            $this->load->model('affiliate/affiliate');

            $affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);

            if ($affiliate_info) {
                $order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
                $order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
            } else {
                $order_data['affiliate_id'] = 0;
                $order_data['commission'] = 0;
            }

            // Marketing
            $this->load->model('checkout/marketing');

            $marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

            if ($marketing_info) {
                $order_data['marketing_id'] = $marketing_info['marketing_id'];
            } else {
                $order_data['marketing_id'] = 0;
            }
        } else {
            $order_data['affiliate_id'] = 0;
            $order_data['commission'] = 0;
            $order_data['marketing_id'] = 0;
            $order_data['tracking'] = '';
        }

        $order_data['language_id'] = $this->config->get('config_language_id');
        $order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
        $order_data['currency_code'] = $this->session->data['currency'];
        $order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
        $order_data['ip'] = $this->request->server['REMOTE_ADDR'];

        if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
            $order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
            $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
        } else {
            $order_data['forwarded_ip'] = '';
        }

        if (isset($this->request->server['HTTP_USER_AGENT'])) {
            $order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
        } else {
            $order_data['user_agent'] = '';
        }

        if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
            $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
        } else {
            $order_data['accept_language'] = '';
        }

        $this->load->model('checkout/order');
        $this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);
        $this->load->model('webservices/fcm');
        $this->model_webservices_fcm->updateFcmOrderIdBySessionId();
        $data['text_recurring_item'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_recurring_item'));
        $data['text_payment_recurring'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_payment_recurring'));

        $data['column_name'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('column_name'));
        $data['column_model'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('column_model'));
        $data['column_quantity'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('column_quantity'));
        $data['column_price'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('column_price'));
        $data['column_total'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('column_total'));

        $this->load->model('tool/upload');

        $data['products'] = array();

        foreach ($this->cart->getProducts() as $product) {
            $option_data = array();

            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['value'];
                } else {
                    $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                    if ($upload_info) {
                        $value = $upload_info['name'];
                    } else {
                        $value = '';
                    }
                }

                $option_data[] = array(
                    'name' => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                );
            }

            $recurring = '';

            if ($product['recurring']) {
                $frequencies = array(
                    'day' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_day')),
                    'week' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_week')),
                    'semi_month' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_semi_month')),
                    'month' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_month')),
                    'year' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_year')),
                );

                if ($product['recurring']['trial']) {
                    $recurring = sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_trial_description')), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
                }

                if ($product['recurring']['duration']) {
                    $recurring .= sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_payment_description')), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
                } else {
                    $recurring .= sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_payment_cancel')), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
                }
            }
            $this->load->model('webservices/image');
            $this->load->model('tool/upload');
            if ($product['image']) {
                $image = $this->model_webservices_image->resize($product['image'], 100, 100);
            } else {
                $image = '';
            }

            if (version_compare(VERSION, '2.1.0.1', '<')) {
                $cart_key = $product['key'];
            } else {
                $cart_key = $product['cart_id'];
            }
            
            $data['products'][] = array(
                'key' => $cart_key,
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'model' => $product['model'],
                'image' => $image,
                'option' => $option_data,
                'recurring' => $recurring,
                'quantity' => $product['quantity'],
                'subtract' => $product['subtract'],
                'price' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                'total' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']),
                'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'],'true'),
            );
        }

        // Gift Voucher
        $data['vouchers'] = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {
                $data['vouchers'][] = array(
                    'description' => $voucher['description'],
                    'amount' => $this->currency->format($voucher['amount'], $this->session->data['currency'])
                );
            }
        }

        $data['totals'] = array();

        foreach ($order_data['totals'] as $total) {
            $data['totals'][] = array(
                'title' => $total['title'],
                'text' => $this->currency->format($total['value'], $this->session->data['currency']),
            );
        }

        $data['payment'] = $this->load->controller('extension/payment/' . $this->session->data['payment_method']['code']);

        if (version_compare(VERSION, '2.2.0.0', '<')) {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/webservices/confirm_new.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/webservices/confirm_new.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/webservices/confirm_new.tpl', $data));
            }
        } else {
            echo($this->load->view('webservices/confirm_new', $data)); die;
        }
    }

    public function paymentSave() {
        $ses = str_replace(";", "", str_replace("OCSESSID=", "", $this->request->get['s_id']));
        if(!isset($_COOKIE['OCSESSID']) || (isset($_COOKIE['OCSESSID']) && $_COOKIE['OCSESSID'] =! $ses)) {
            setcookie("OCSESSID", $ses);
            $json['error']['warning'] = "Please try again!";
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            die;
        }
        
        $this->load->language('checkout/checkout');
        $json = array();
        // Validate if payment address has been set.
        if (!isset($this->session->data['payment_address'])) {
            $json['redirect'] = $this->url->link('checkout/checkout', 'status=kbmobileappredirecterror', 'true');
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
           $json['redirect'] = $this->url->link('checkout/cart', 'status=kbmobileappredirecterror','true');
        }
        // Validate minimum quantity requirements.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $json['redirect'] = $this->url->link('checkout/cart', 'status=kbmobileappredirecterror','true');

                break;
            }
        }

        if (!isset($this->request->post['payment_method'])) {
            $json['error']['warning'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_payment'));
        } elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
            $json['error']['warning'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_payment'));
        }

        if ($this->config->get('config_checkout_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

            if ($information_info && !isset($this->request->post['agree'])) {
                $json['error']['warning'] = sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_agree')), $information_info['title']);
            }
        }

        if (!$json) {
            $this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];

            $this->session->data['comment'] = strip_tags($this->request->get['order_message']);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function imageUrlEncoding($url){
        $setting_data = $this->config->get('webservice');
        if($setting_data['url_encoding_status']){
            return urlencode($url); 
        } else {
            return str_replace(" ","%20",$url);
        }
    }

}
?>
