<?php

class ControllerWebservicesOneSixShoppingCart extends Controller {

    public function appAddToCart() {

        $json = array();
        $setting_data = $this->config->get('webservice');
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            $this->model_webservices_home->logKnowbandAppRequest("appAddToCart", json_encode($json));

            die;
        } else {
            $json['install_module'] = '';
        }
        //load Models
        $this->load->model('account/customer');
        $this->load->model('catalog/product');
        $this->load->language('webservices/config');
        
        if (isset($this->request->post['cart_products'])) {
            $this->request->post = json_decode(str_replace('&quot;', '"', (trim($this->request->post['cart_products']))), true);
        }
        if (!empty($this->request->post['cart_products'])) {
            foreach ($this->request->post['cart_products'] as $products) {
                $product_id = (int) $products['product_id'];
                $product_detail = $this->model_catalog_product->getProduct($product_id);
                
                $quantity = (int)$products['quantity'];
                
                 if($product_detail['quantity']<$quantity) {
                    $json['status'] = 'failure';
                    $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_out_of_stock'));
                    $json['session_data'] = $this->session->getId();
                } else {
                $recurring_id = 0;
                $options = array();
                $product_options = $this->model_catalog_product->getProductOptions($product_id);

                foreach ($products['option'] as $option) {
                    foreach ($product_options as $product_option) {
                        if ($option['id'] == $product_option['product_option_id'] && $product_option['type'] == 'checkbox') {
                            $options[$option['id']][] = $option['selected_value_id'];
                            break;
                        } else if ($option['id'] == $product_option['product_option_id']) {
                            $options[$option['id']] = $option['selected_value_id'];
                            break;
                        }
                    }
                }

                //                    foreach ($products['option'] as $option) {
                //                        $options[$option['id']] = $option['selected_value_id'];
                //                    }

                $option = $options;

                $customer_email = isset($this->request->post['email']) ? $this->request->post['email'] : '';
                $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
                if (!empty($check_customer)) {
                    $customer_id = $check_customer['customer_id'];
                } else {
                    $customer_id = 0;
                }
                
                $this->session->data['customer_id'] = $customer_id;
                $this->customer_id = $customer_id;
                //$this->customer->customer_id = $customer_id;

                $this->cart->add($product_id, $quantity, $option, $recurring_id);
               
                if ($customer_id > 0) {
                    if (version_compare(VERSION, '2.1.0.1', '<')) {
                        if (isset($this->session->data['cart'])) {
                            $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(serialize($this->session->data['cart'])) . "' WHERE customer_id = '" . (int) $customer_id . "'");
                        }
                    } else {
                
                        $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int) $this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
                        $req_data = array();
                        if ($cart_query->num_rows > 0) {
                            foreach ($cart_query->rows as $cart_query_row) {
                                $req_data[$cart_query_row['cart_id']] = (int) $cart_query_row['quantity'];
                            }
                        }
                        if (isset($req_data) && count($req_data) > 0) {
                            $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(serialize($req_data)) . "' WHERE customer_id = '" . (int) $customer_id . "'");
                        }

                    }

                    if (isset($this->session->data['wishlist'])) {
                        $this->db->query("UPDATE " . DB_PREFIX . "customer SET  wishlist = '" . $this->db->escape(serialize($this->session->data['wishlist'])) . "' WHERE customer_id = '" . (int) $customer_id . "'");
                    }
                }
                $json['status'] = 'success';
                $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get("text_product_added"));
                $json['session_data'] = $this->session->getId();
            }
            }
        } else {
            $json['status'] = 'failure';
            $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_product_not_added'));
            $json['session_data'] = $this->session->getId();
        }
        
        $json['total_cart_items'] = $this->cart->countProducts();
        
        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("appAddToCart", json_encode($json));

        $this->response->output();

        //echo json_encode($json);
        die;
    }

    public function appUpdateCartQuantity() {

        $json = array();

        $setting_data = $this->config->get('webservice');
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            $this->model_webservices_home->logKnowbandAppRequest("appUpdateCartQuantity", json_encode($json));

            die;
        } else {
            $json['install_module'] = '';
        }
        //load Models
        $this->load->model('account/customer');
        $this->load->model('catalog/product');
        $this->load->language('webservices/config');

        $session_data = $this->request->post['session_data'];


        if (isset($session_data) && $session_data != '') {
            $customer_email = isset($this->request->post['email']) ? $this->request->post['email'] : '';
            ;
            if (isset($this->request->post['cart_products'])) {
                $this->request->post['cart_products'] = json_decode(str_replace('&quot;', '"', (trim($this->request->post['cart_products']))), true);
            }
            $this->request->post['cart_products'] = $this->request->post['cart_products']['cart_products'];
            if (!empty($this->request->post['cart_products'])) {
                foreach ($this->request->post['cart_products'] as $products) {
                    $product_id = (int) $products['product_id'];
                    $quantity = (int) $products['quantity'];
                    $recurring_id = 0;



                    $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
                    if (!empty($check_customer)) {
                        $customer_id = $check_customer['customer_id'];
                    } else {
                        $customer_id = 0;
                    }
                    $this->session->data['customer_id'] = $customer_id;
                    $this->customer_id = $customer_id;


                    $this->cart->update($products['id_product_attribute'], $products['quantity']);


                    if ($customer_id > 0) {
                        if (version_compare(VERSION, '2.1.0.1', '<')) {
                            if (isset($this->session->data['cart'])) {
                                $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(serialize($this->session->data['cart'])) . "' WHERE customer_id = '" . (int) $customer_id . "'");
                            }
                        } else {
                            $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int) $this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
                            $req_data = array();
                            if ($cart_query->num_rows > 0) {
                                foreach ($cart_query->rows as $cart_query_row) {
                                    $req_data[$cart_query_row['cart_id']] = (int) $cart_query_row['quantity'];
                                }
                            }
                            if (isset($req_data) && count($req_data) > 0) {
                                $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(serialize($req_data)) . "' WHERE customer_id = '" . (int) $customer_id . "'");
                            }
                        }

                        if (isset($this->session->data['wishlist'])) {
                            $this->db->query("UPDATE " . DB_PREFIX . "customer SET  wishlist = '" . $this->db->escape(serialize($this->session->data['wishlist'])) . "' WHERE customer_id = '" . (int) $customer_id . "'");
                        }
                    }
                }
                $json = $this->cartDetails();
                $json['status'] = 'success';
                $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_product_added'));
                $json['session_data'] = $this->session->getId();
            } else {
                $json['error'] = 'error';
                $json['session_data'] = '';
            }
        } else {
            $json['error'] = 'error';
            $json['session_data'] = '';
        }

        $json['total_cart_items'] = $this->cart->countProducts();
        
        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("appUpdateCartQuantity", json_encode($json));

        $this->response->output();

        //echo json_encode($json);
        die;
    }

    public function appRemoveProduct() {

        $json = array();
        $this->load->language('webservices/config');
        $setting_data = $this->config->get('webservice');
        $this->load->model('webservices/home');
        
        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            $this->model_webservices_home->logKnowbandAppRequest("appRemoveProduct", json_encode($json));

            die;
        } else {
            $json['install_module'] = '';
        }
        //load Models
        $this->load->model('account/customer');
        $this->load->model('catalog/product');
        $this->load->language('webservices/config');

        $session_data = $this->request->post['session_data'];


        if (isset($session_data) && $session_data != '') {
            $customer_email = isset($this->request->post['email']) ? $this->request->post['email'] : '';
            ;
            if (isset($this->request->post['cart_products'])) {
                $this->request->post = json_decode(str_replace('&quot;', '"', (trim($this->request->post['cart_products']))), true);
            }
            $this->request->post['email'] = $customer_email;
            $this->request->post['email'] = $customer_email;
            if (!empty($this->request->post['cart_products'])) {
                foreach ($this->request->post['cart_products'] as $products) {
                    $product_id = (int) $products['product_id'];
                    $quantity = (int) $products['quantity'];
                    $recurring_id = 0;



                    $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
                    if (!empty($check_customer)) {
                        $customer_id = $check_customer['customer_id'];
                    } else {
                        $customer_id = 0;
                    }
                    $this->session->data['customer_id'] = $customer_id;
                    $this->customer_id = $customer_id;


                    $this->cart->remove($products['id_product_attribute']);


                    if ($customer_id > 0) {
                        if (version_compare(VERSION, '2.1.0.1', '<')) {
                            if (isset($this->session->data['cart'])) {
                                $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(serialize($this->session->data['cart'])) . "' WHERE customer_id = '" . (int) $customer_id . "'");
                            }
                        } else {
                            $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int) $this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
                            $req_data = array();
                            if ($cart_query->num_rows > 0) {
                                foreach ($cart_query->rows as $cart_query_row) {
                                    $req_data[$cart_query_row['cart_id']] = (int) $cart_query_row['quantity'];
                                }
                            }
                            if (isset($req_data) && count($req_data) > 0) {
                                $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(serialize($req_data)) . "' WHERE customer_id = '" . (int) $customer_id . "'");
                            }
                        }

                        if (isset($this->session->data['wishlist'])) {
                            $this->db->query("UPDATE " . DB_PREFIX . "customer SET  wishlist = '" . $this->db->escape(serialize($this->session->data['wishlist'])) . "' WHERE customer_id = '" . (int) $customer_id . "'");
                        }
                    }
                }
                $json = $this->cartDetails();
                $json['status'] = 'success';
                $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_product_deleted'));
                $json['session_data'] = $this->session->getId();
            } else {
                $json['error'] = 'error';
                $json['session_data'] = '';
            }
        } else {
            $json['error'] = 'error';
            $json['session_data'] = '';
        }

        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("appRemoveProduct", json_encode($json));

        $this->response->output();

        //echo json_encode($json);
        die;
    }

    public function appGetCartDetails() {
        
        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);

        $data = $this->cartDetails();
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("appGetCartDetails", json_encode($data));
        $this->response->output();
        die;
    }

    public function cartDetails() {
        
        
        
        $file = DIR_LOGS . 'test_api_log.txt';
        $handle = fopen($file, 'a+');
        fwrite($handle, date('Y-m-d G:i:s'). ' - ' . print_r($this->request->post,TRUE)  . "\r\n");
        fclose($handle);
        
        $this->load->language('webservices/config');
        $this->load->language('checkout/cart');
        $this->load->model('account/customer');
        $this->load->model('webservices/home');

        $data['products'] = array();
        $data['cart'] = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfCarts", json_encode($data));

            die;
        } else {
            $data['install_module'] = '';
        }
        $customer_email = isset($this->request->post['email']) ? $this->request->post['email'] : '';
        $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
        if (!empty($check_customer)) {
            $customer_id = $check_customer['customer_id'];
        } else {
            $customer_id = 0;
        }
        
        $this->session->data['customer_id'] = $customer_id;
        $this->customer_id = $customer_id;

        if (version_compare(VERSION, '2.1.0.1', '<')) {
            if (isset($this->session->data['cart'])) {
                $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(serialize($this->session->data['cart'])) . "' WHERE customer_id = '" . (int) $customer_id . "'");
            }
        } else {
            $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int) $this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
            $req_data = array();
            if ($cart_query->num_rows > 0) {
                foreach ($cart_query->rows as $cart_query_row) {
                    $req_data[$cart_query_row['cart_id']] = (int) $cart_query_row['quantity'];
                }
            }

            if (isset($req_data) && count($req_data) > 0) {
                $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(serialize($req_data)) . "' WHERE customer_id = '" . (int) $customer_id . "'");
            }
        }

        if (isset($this->session->data['wishlist'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "customer SET  wishlist = '" . $this->db->escape(serialize($this->session->data['wishlist'])) . "' WHERE customer_id = '" . (int) $customer_id . "'");
        }

        $data['delay_shipping'] = array(
            "applied" => "0",
            "available" => "0"
        );
        $data['cart_id'] = $this->session->getId();

        $data['voucher_allowed'] = "1";
        $data['minimum_purchase_message'] = "";
        $data['voucher_html'] = "";


        
        
        if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {

            $this->load->model('webservices/image');
            $this->load->model('tool/upload');
            $this->load->language('webservices/config');

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
                $this->load->language('checkout/coupon');
                $this->load->model('checkout/coupon');
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
                $this->load->language('checkout/voucher');
                $this->load->model('checkout/voucher');
                $coupon_info = $this->model_checkout_voucher->getVoucher($this->session->data['voucher']);
                if ($coupon_info) {
                    $data['voucher'] = array('status' => 'success', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_success')));
                } else {
                    $data['voucher'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_voucher')));
                }
            }
            
            if(isset($this->request->post['shipping_method']) && $this->request->post['shipping_method'] != '') {
                $this->load->language('extension/total/shipping');
                $json = array();
                if ($this->request->post['country_id'] == '') {
                    $json['country'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_country'));
                }
                if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
                    $json['zone'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_zone'));
                }
                $this->load->model('localisation/country');
                $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
                if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
                    $json['postcode'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_postcode'));
                }
                if (!$json) {
                    $this->tax->setShippingAddress($this->request->post['country_id'], $this->request->post['zone_id']);

                    if ($country_info) {
                        $country = $country_info['name'];
                        $iso_code_2 = $country_info['iso_code_2'];
                        $iso_code_3 = $country_info['iso_code_3'];
                        $address_format = $country_info['address_format'];
                    } else {
                        $country = '';
                        $iso_code_2 = '';
                        $iso_code_3 = '';
                        $address_format = '';
                    }

                    $this->load->model('localisation/zone');

                    $zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

                    if ($zone_info) {
                        $zone = $zone_info['name'];
                        $zone_code = $zone_info['code'];
                    } else {
                        $zone = '';
                        $zone_code = '';
                    }

                    $this->session->data['shipping_address'] = array(
                        'firstname' => '',
                        'lastname' => '',
                        'company' => '',
                        'address_1' => '',
                        'address_2' => '',
                        'postcode' => $this->request->post['postcode'],
                        'city' => '',
                        'zone_id' => $this->request->post['zone_id'],
                        'zone' => $zone,
                        'zone_code' => $zone_code,
                        'country_id' => $this->request->post['country_id'],
                        'country' => $country,
                        'iso_code_2' => $iso_code_2,
                        'iso_code_3' => $iso_code_3,
                        'address_format' => $address_format
                    );

                    $quote_data = array();

                    $this->load->model('setting/extension');

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
                        $shipping = explode('.', $this->request->post['shipping_method']);
                        $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                    } else {
                        $data['shipping_warning'] = sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_no_shipping')), $this->url->link('information/contact','','true'));
                    }
                } else {
                    $data['shipping_error'] = $json;
                    unset($this->session->data['shipping_method']);
                    unset($this->session->data['shipping_methods']);
                }
            }
            $width = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_width'));
            $height = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_height'));

            $products = $this->cart->getProducts();

            $error_warning = array();

            foreach ($products as $product) {
                $product_total = 0;
                $stock_error = "";
                $not_in_stock_options_quantity = array();

                foreach ($products as $product_2) {
                    if ($product_2['product_id'] == $product['product_id']) {
                        $product_total += $product_2['quantity'];
                    }
                }

                if ($product['minimum'] > $product_total) {
                    $error_warning[] = "<b>*" . $product['name'] . ":</b>". iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_minimum'))." <b>" . $product['minimum'] . "</b> ". iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_quantity_required'))."";
                    //$data['error_warning'] = sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_minimum'), str_replace('&quot;', '"', $product['name']), $product['minimum']);
                }

                if ($product['image']) {
                    $image = $this->model_webservices_image->resize($product['image'], $width, $height);
                } else {
                    $image = '';
                }

                $option_data = array();
                $selected_options = array();
                $attribute_string = '';
                $attribute_data = array();
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

                    $selected_options[] = array("key" => $option['product_option_id'], "value" => $option['product_option_value_id']);

                    $attribute_data[] = array(
                        'name' => $option['name'],
                        'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                    );


                    if ($product['stock'] == false) {
                        if ($option['quantity'] < $product['quantity']) {
                            $not_in_stock_options_quantity[] = $option['quantity'];
                        }
                    }
                }
                foreach ($attribute_data as $attr) {
                    if ($attribute_string == '') {
                        $attribute_string .= $attr['name'] . " : " . $attr['value'];
                    } else {
                        $attribute_string .= ", " . $attr['name'] . " : " . $attr['value'];
                    }
                }
                $attribute_string = trim($attribute_string, ',');
                if ($attribute_string != '') {
                    $option_data[] = array(
                        'name' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_attributes')),
                        'value' => $attribute_string
                    );
                }
                if (count($not_in_stock_options_quantity) > 0) {
                    $stock_error = "Only " . min($not_in_stock_options_quantity) . "quantity left in stock. Kindly order accordingly.";
                }

                if ($product['stock'] == false && $stock_error == "") {

                    $product_query = $this->db->query("SELECT quantity FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int) $this->config->get('config_store_id') . "' AND p2s.product_id = '" . (int) $product['product_id'] . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");

                    if ($product_query->num_rows) {
                        $stock_error = "Only " . $product_query->row['quantity'] . "quantity left in stock. Kindly order accordingly.";
                        ;
                    }
                }

                $option_data[] = array(
                    'name' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_quantity')),
                    'value' => $product['quantity']
                );

                // Display prices
                if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                    $total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']);
                } else {
                    $total = false;
                }

                if (isset($product['special'])) {
                    if($productInfo['price']>0) {
                            $discount_price = $this->currency->format($this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                            $act_price = $this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                            $disc_price = $this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                            $discount_perc = (($act_price - $disc_price) / $act_price) * 100;
                            $discount_percentage = number_format($discount_perc, 2);
                        } else{
                            $productInfo['price']=$productInfo['special'];
                            $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                            $discount_percentage = 0;
                            
                        }
                } else {
                    $discount_price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $discount_percentage = 0;
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

                if (version_compare(VERSION, '2.1.0.1', '<')) {
                    $cart_key = $product['key'];
                } else {
                    $cart_key = $product['cart_id'];
                }

                $data['products'][] = array(
                    'product_id' => $product['product_id'],
                    'title' => str_replace('&quot;', '"', $product['name']),
                    'is_gift_product' => '0',
                    'id_product_attribute' => $cart_key,
                    'id_address_delivery' => 0,
                    'stock' => $product['stock'],
                    'discount_price' => $discount_price,
                    'discount_percentage' => $discount_percentage,
                    'images' => $image,
                    'price' => $total,
                    'quantity' => $product['quantity'],
                    'product_items' => $option_data,
                    'customizable_items' => array()
                );
            }

            /* if (count($error_warning) > 0) {
              $data['error_warning'] = "";
              foreach ($error_warning as $error_warnings) {
              $data['error_warning'] .= $error_warnings . "\n";
              }
              } */

            $data['cart']['total_cart_items'] = $this->cart->countProducts();
            // Totals
            $this->load->model('setting/extension');

            $totals = array();
            $taxes = $this->cart->getTaxes();
            $total = 0;
            // Because __call can not keep var references so we put them into an array. 			
            $total_data = array(
                'totals' => &$totals,
                'taxes' => &$taxes,
                'total' => &$total
            );
            // Display prices
            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
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

                        if (VERSION >= '2.2.0.0') {
                            $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                        } else {
                            $this->{'model_extension_total_' . $result['code']}->getTotal($totals, $total, $taxes);
                        }
                    }
                }

                $sort_order = array();

                foreach ($totals as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $totals);
            }
            
            $data['gift_wrapping'] = array(
                "applied" => "0",
                "available" => "0",
                "cost_text" => "",
                "message" => ""
            );
            
            $data['guest_checkout_enabled'] = $this->config->get('config_checkout_guest');
            $vouchers = array();
            $data['totals'] = array();

            foreach ($totals as $total) {
                if ($total['code'] == 'coupon') {
                    $code = '';
                    $start = strpos($total['title'], '(') + 1;
                    $end = strrpos($total['title'], ')');

                    if ($start && $end) {
                        $code = substr($total['title'], $start, $end - $start);
                    }
                    if (version_compare(VERSION, '2.1.0.1', '>=')) {
                        $coupon_info = $this->model_extension_total_coupon->getCoupon($code);
                    } else {
                        $coupon_info = $this->model_extension_total_coupon->getCoupon($code);
                    }
                    if ($coupon_info) {
                        $vouchers = array('id' => $coupon_info['coupon_id'], 'name' => $code, 'value' => $this->currency->format($total['value'], $this->session->data['currency']));
                    } else {
                        $data['vouchers'] = array();
                    }
                } else {
                    $data['vouchers'] = array();
                }
                if ($total['code'] == 'voucher') {
                    $code = '';
                    $start = strpos($total['title'], '(') + 1;
                    $end = strrpos($total['title'], ')');

                    if ($start && $end) {
                        $code = substr($total['title'], $start, $end - $start);
                    }
                    if (version_compare(VERSION, '2.1.0.1', '>=')) {
                        $voucher_info = $this->model_extension_total_voucher->getVoucher($code);
                    } else {
                        $voucher_info = $this->model_checkout_voucher->getVoucher($code);
                    }
                    if ($voucher_info) {
                        $data['vouchers'][] = array('id' => $voucher_info['voucher_id'], 'name' => $code, 'value' => $this->currency->format($voucher_info['value'], $this->session->data['currency']));
                    } else {
                        $data['vouchers'] = array();
                    }
                }
                if ($total['code'] == 'coupon') {
                
                }else{
                    $data['totals'][] = array(
                        'name' => strip_tags(html_entity_decode($total['title'])),
                        'value' => $this->currency->format($total['value'], $this->session->data['currency'])
                    );
                }
            }
            if($vouchers){
                $data['vouchers'][] = $vouchers;
            }else{
                $data['vouchers'] = array();
            }
//            if(count($data['vouchers']) == 0 && isset($this->session->data['coupon'])){
//                if (version_compare(VERSION, '2.1.0.1', '>=')) {
//                    $coupon_info = $this->model_extension_total_coupon->getCoupon($code);
//                } else {
//                    $coupon_info = $this->model_checkout_coupon->getCoupon($code);
//                }
//                if ($coupon_info) {
//                    $data['vouchers'][] = array('id' => $coupon_info['coupon_id'], 'name' => $code, 'value' => $this->currency->format($coupon_info['value'], $this->session->data['currency']));
//                }
//            }
        } else {
            $data['cart']['total_cart_items'] = 0;
            $data['cart']['empty'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_empty'));
        }


        $data['checkout_page'] = array('per_products_shipping' => 0);

        return $data;
    }

    public function appApplyVoucher() {
	$data = array();
        $setting_data = $this->config->get('webservice');
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            $this->model_webservices_home->logKnowbandAppRequest("appApplyVoucher", json_encode($json));

            die;
        } else {
            $data['install_module'] = '';
        }
        $status = false;
        if(isset($this->session->data['coupon'])){
		$data = $this->cartDetails();
            $data['status'] = 'success';
            $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_voucher'));
		
        }else{
            if (isset($this->request->post['voucher']) && version_compare(VERSION, '2.1.0.1', '>=')) {
                $this->load->model('extension/total/coupon');
                $this->load->language('extension/total/coupon');
                $voucher = $this->request->post['voucher'];
                $voucher_info = $this->model_extension_total_coupon->getCoupon($voucher);
                if ($voucher_info) {
                    $this->session->data['coupon'] = $this->request->post['voucher'];
                    $status = true;
                } else {
                    $status = false;
                }
            } else if (isset($this->request->post['voucher'])) {
                $this->session->data['coupon'] = $this->request->post['voucher'];
                $this->load->language('checkout/coupon');
                $this->load->model('checkout/coupon');
                $coupon_info = $this->model_extension_total_coupon->getCoupon($this->session->data['coupon']);
                if ($coupon_info) {
                    $status = true;
                } else {
                    $status = false;
                }
            }
            $data = $this->cartDetails();
            if ($status) {
                $data['status'] = 'success';
                $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('voucher_success'));
            } else {
                $data['status'] = 'failure';
                $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_voucher'));
            }
        }
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("appGetCartDetails", json_encode($data));
        $this->response->output();
        die;
    }

    public function appRemoveVoucher() {
        $status = false;
        $this->load->language('webservices/config');
        if (isset($this->request->post['voucher'])) {
            unset($this->session->data['coupon']);
            $status = true;
        }
        $data = $this->cartDetails();
        if ($status) {
            $data['status'] = 'success';
            $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('voucher_removed'));
        } else {
            $data['status'] = 'failure';
            $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_coupon'));
        }
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("appGetCartDetails", json_encode($data));
        $this->response->output();
        die;
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
