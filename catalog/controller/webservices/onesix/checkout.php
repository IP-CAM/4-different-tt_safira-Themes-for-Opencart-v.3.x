<?php

class ControllerWebservicesOneSixCheckout extends Controller {

    public function getAllShippingAddress() {
        
        //$this->request->post['email'] = 'ashwani.srits@gmail.com';
        $data['shipping_address'] = array();
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        if (!empty($this->request->post['email'])) {
            $this->load->model('account/customer');
            $customer_email = $this->request->post['email'];
            $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
            if (!empty($check_customer)) {
                $customer_id = $check_customer['customer_id'];
                $this->session->data['customer_id'] = $customer_id;
                $this->load->model('account/address');
                $this->load->model('webservices/checkout');
                $addresses = $this->model_webservices_checkout->getAddresses($customer_id);

                foreach ($addresses as $address) {
                    $data['shipping_address'][] = array(
                        'id_shipping_address' => $address['address_id'],
                        'firstname' => $address['firstname'],
                        'lastname' => $address['lastname'],
                        'company' => $address['company'],
                        'mobile_no' => $address['custom_field'] ? $address['custom_field'][1] : '',
                        'address_1' => $address['address_1'],
                        'address_2' => $address['address_2'],
                        'city' => $address['city'],
                        'state' => $address['zone'],
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

    public function getShippingAddress($id = 0) {
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        
        $data['shipping_address'] = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("getShippingAddress", json_encode($data));
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

                foreach ($addresses as $address) {
                    $this->session->data['shipping_address'] = $address;
                    $this->session->data['shipping_address']['country_id'] = $address['country_id'];
                    $this->session->data['shipping_address']['zone_id'] = $address['zone_id'];
                    $data['shipping_address'] = array(
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

        $this->model_webservices_home->logKnowbandAppRequest("getShippingAddress", json_encode($data));
        return $data;
        die();
    }

    public function getBillingAddress($id) {
        //$this->request->post['email'] = 'xxxx@gmail.com';
        $this->load->model('webservices/home');

        $this->load->language('webservices/config');
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

    public function getDetailsOfShippingAddress() {
        $this->load->model('webservices/home');
        $data = array();
        $setting_data = $this->config->get('webservice');
        $this->load->language('webservices/config');
        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfShippingAddress", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }

        if (!empty($this->request->post['email'])) {
            $data = $this->getAllShippingAddress();
            $this->load->model('localisation/country');
            $countries = $this->model_localisation_country->getCountries();
            //Model
            $this->load->model('localisation/zone');
            foreach ($countries as $country) {
                $data['countries'][] = array(
                    'id' => $country['country_id'],
                    'name' => $country['name'],
                );
            }
            $zones = $this->model_localisation_zone->getZonesByCountryId('99');
            foreach ($zones as $zone) {
                $data['states'][] = array(
                    'country_id' => $zone['country_id'],
                    'state_id' => $zone['zone_id'],
                    'name' => $zone['name'],
                );
            }
        }
        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfShippingAddress", json_encode($data));
        $this->response->output();
        die;
    }

    public function getAllCountries() {
        $this->load->model('webservices/home');

        $data = array();
        $setting_data = $this->config->get('webservice');
        $this->load->language('webservices/config');
        
        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("getAllCountries", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }

        $this->load->model('localisation/country');
        $countries = $this->model_localisation_country->getCountries();
        //Model
        $this->load->model('localisation/zone');
        foreach ($countries as $country) {
            $data['countries'][] = array(
                'id' => $country['country_id'],
                'name' => $country['name'],
            );
        }
        $zones = $this->model_localisation_zone->getZonesByCountryId('99');
        foreach ($zones as $zone) {
            $data['states'][] = array(
                'country_id' => $zone['country_id'],
                'state_id' => $zone['zone_id'],
                'name' => $zone['name'],
            );
        }
        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("getAllCountries", json_encode($data));
        $this->response->output();
        die;
    }

    public function getCountryZone() {
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $data['states'] = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("getCountryZone", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }

        if (isset($this->request->post['country_id'])) {
            $country_id = $this->request->post['country_id'];
            $this->load->model('localisation/zone');
            $zones = $this->model_localisation_zone->getZonesByCountryId($country_id);
            foreach ($zones as $zone) {
                $data['states'][] = array(
                    'country_id' => $zone['country_id'],
                    'state_id' => $zone['zone_id'],
                    'name' => $zone['name'],
                );
            }
        }
        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("getCountryZone", json_encode($data));
        $this->response->output();
        die;
    }

    public function addDetailsOfShippingAddress() {
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $data['shipping_address_reponse'] = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            $this->model_webservices_home->logKnowbandAppRequest("addDetailsOfShippingAddress", json_encode($data));
            echo json_encode($data);
            die;
        } else {
            $data['install_module'] = '';
        }

        if (isset($this->request->post['shipping_address'])) {
            $this->request->post = json_decode(str_replace('&quot;', '"', (trim($this->request->post['shipping_address']))), true);
        }
        if (!empty($this->request->post['email'])) {
            $this->load->model('account/customer');
            $customer_email = $this->request->post['email'];
            $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
            if (!empty($check_customer)) {
                $customer_id = $check_customer['customer_id'];
                $this->session->data['customer_id'] = $customer_id;
                $this->load->model('webservices/checkout');
                $address_id = $this->model_webservices_checkout->addAddress($customer_id, $this->request->post['shipping_address']);
                if (isset($address_id)) {
                    $data['shipping_address_reponse'] = array(
                        "status" => "success",
                        "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_shipping_address_success'))
                    );
                } else {
                    $data['shipping_address_reponse'] = array(
                        "status" => "failure",
                        "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_shipping_address_issue'))
                    );
                }
            } else {
                $data['shipping_address_reponse'] = array(
                    "status" => "failure",
                    "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_shipping_address_issue'))
                );
            }
        } else {
            $data['shipping_address_reponse'] = array(
                "status" => "failure",
                "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_shipping_address_issue'))
            );
        }
        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("addDetailsOfShippingAddress", json_encode($data));
        $this->response->output();
        die;
    }

    public function updateDetailsOfShippingAddress() {
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $this->load->language('webservices/config');
        
        $data['shipping_address_reponse'] = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("updateDetailsOfShippingAddress", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }

        if (isset($this->request->post['shipping_address'])) {
            $this->request->post = json_decode(str_replace('&quot;', '"', (trim($this->request->post['shipping_address']))), true);
        }
        if (!empty($this->request->post['id_shipping_address'])) {
            $this->load->model('account/customer');
            $customer_email = $this->request->post['email'];
            $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
            if (!empty($check_customer)) {
                $customer_id = $check_customer['customer_id'];
                $this->session->data['customer_id'] = $customer_id;
                $this->customer_id = $customer_id;
                $this->load->model('webservices/checkout');
                $address_id = $this->model_webservices_checkout->editAddress($this->request->post['id_shipping_address'], $customer_id, $this->request->post['shipping_address']);
                $data['shipping_address_reponse'] = array(
                    "status" => "success",
                    "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_shipping_address_update'))
                );
            } else {
                $data['shipping_address_reponse'] = array(
                    "status" => "failure",
                    "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_shipping_address_issue'))
                );
            }
        } else {
            $data['shipping_address_reponse'] = array(
                "status" => "failure",
                "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_shipping_address_issue'))
            );
        }
        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("updateDetailsOfShippingAddress", json_encode($data));
        $this->response->output();
        die;
    }

    public function getAllPaymentMethod() {
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("getAllPaymentMethod", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }
        if (isset($this->request->post['billing_address_id'])) {
            $payment_address = $this->getBillingAddress($this->request->post['billing_address_id']);
            $this->session->data['payment_address'] = $payment_address['billing_address'];
        }
        if (isset($this->session->data['payment_address'])) {
            $total = $this->request->post['order_total'];
            // Payment Methods
            $method_data = array();

            $this->load->model('setting/extension');

            $results = $this->model_setting_extension->getExtensions('payment');

            $recurring = $this->cart->hasRecurringProducts();

            foreach ($results as $result) {
                if ($this->config->get('payment_'.$result['code'] . '_status')) {
                    $this->load->model('payment/' . $result['code']);

                    $method = $this->{'model_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);

                    if ($method) {
                        if ($recurring) {
                            if (method_exists($this->{'model_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_payment_' . $result['code']}->recurringPayments()) {
                                $method_data[] = $method;
                            }
                        } else {
                            $method_data[] = $method;
                        }
                    }
                }
            }

            $sort_order = array();

            foreach ($method_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $method_data);

            $data['payment_methods'] = $method_data;
        }
        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("getAllPaymentMethod", json_encode($data));
        $this->response->output();
        die;
    }

    public function ClearCart() {
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $data = array();
        if (isset($this->request->post['email'])) {
            $this->session->data['cart'] = array();
            $this->load->model('account/customer');
            $customer_email = $this->request->post['email'];
            $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
            if (!empty($check_customer)) {
                $customer_id = $check_customer['customer_id'];
            } else {
                $customer_id = 0;
            }

            $this->db->query("UPDATE " . DB_PREFIX . "customer SET cart = '" . $this->db->escape(serialize($this->session->data['cart'])) . "' WHERE customer_id = '" . (int) $customer_id . "'");
            $data['clear_cart'] = array('status' => 'success', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_empty_cart')), 'session_data' => $this->session->data['cart']);
        } else {
            $data['clear_cart'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_error_cart')), 'session_data' => $this->session->data['cart']);
        }

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("ClearCart", json_encode($data));
        $this->response->output();
        die;
    }

    public function appCheckout() {
        $this->load->model('webservices/home');
        $this->load->model('account/customer');
        $this->load->language('webservices/config');
        if (isset($this->request->post['checkout'])) {
            $this->request->post = json_decode(str_replace('&quot;', '"', (trim($this->request->post['checkout']))), true);
        }
        $data['checkout_page'] = array();
        $data['status'] = "failure";
        $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_session_expired'));

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfCheckout", json_encode($data));
            echo json_encode($data);
            die;
        } else {
            $data['install_module'] = '';
        }


        if (!empty($this->request->post['email'])) {
            $this->load->model('webservices/fcm');
            $this->model_webservices_fcm->updateFcmEmailSessionId($this->request->post['email']);
            $check_customer = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
            if (!empty($check_customer)) {
                $customer_id = $check_customer['customer_id'];
            } else {
                $customer_id = 0;
            }

            $this->session->data['customer_id'] = $customer_id;
            $this->customer_id = $customer_id;
            if (isset($this->request->post['id_shipping_address'])) {
                $shipping_add = $this->getShippingAddress($this->request->post['id_shipping_address']);
                if ($shipping_add) {
                    $data['checkout_page']['shipping_address'] = $shipping_add['shipping_address'];
                }
            } else {
                $data['checkout_page']['shipping_address'] = array();
            }
            if (isset($this->request->post['id_billing_address'])) {
                $billing_add = $this->getBillingAddress($this->request->post['id_billing_address']);
                if ($billing_add) {
                    $data['checkout_page']['billing_address'] = $billing_add['billing_address'];
                }
            } else {
                $data['checkout_page']['billing_address'] = array();
            }

            if ($this->customer->getId() > 0) {
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
            }
            
            $products = $this->cart->getProducts();
            

            
            $this->load->model('webservices/image');
            foreach ($products as $product) {
                $attribute_string = '';
                $product_total = 0;

                foreach ($products as $product_2) {
                    if ($product_2['product_id'] == $product['product_id']) {
                        $product_total += $product_2['quantity'];
                    }
                }
                $this->load->language('webservices/config');

                $width = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_width'));
                $height = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_height'));

                if ($product['minimum'] > $product_total) {
                    $data['error_warning'] = sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_minimum')), str_replace('&quot;', '"', $product['name']), $product['minimum']);
                }

                if ($product['image']) {
                    $image = $this->model_webservices_image->resize($product['image'], $width, $height);
                } else {
                    $image = '';
                }
                
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
                    $attribute_data[] = array(
                        'name' => $option['name'],
                        'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                    );
//                    $option_data[] = array(
//                        'name' => $option['name'],
//                        'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
//                    );
                }
                if (isset($attribute_data)) {
                    foreach ($attribute_data as $attr) {
                        if (isset($attribute_string) && $attribute_string == '') {
                            $attribute_string .= $attr['name'] . " : " . $attr['value'];
                        } else {
                            $attribute_string .= ", " . $attr['name'] . " : " . $attr['value'];
                        }
                    }
                    $attribute_string = trim($attribute_string, ',');
                    if ($attribute_string != '') {
                        $option_data[] = array(
                            'name' => 'Attributes',
                            'value' => $attribute_string
                        );
                    }
                }
                $option_data[] = array(
                    'name' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_quantity')),
                    'value' => $product['quantity']
                );

                // Display prices
                if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                } else {
                    $price = false;
                }

                // Display prices
                if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                    $total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']);
                } else {
                    $total = false;
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

                $data['checkout_page']['products'][] = array(
                    'product_id' => $product['product_id'],
                    'title' => str_replace('&quot;', '"', $product['name']),
                    'is_gift_product' => '0',
                    'id_product_attribute' => $cart_key,
                    'id_address_delivery' => 0,
                    'stock' => $product['stock'],
                    'discount_price' => '',
                    'discount_percentage' => '',
                    'images' => $image,
                    'price' => $total,
                    'quantity' => $product['quantity'],
                    'product_items' => $option_data,
                    'customizable_items' => array()
                );
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
                $coupon_info = $this->model_extension_total_voucher->getVoucher($this->session->data['voucher']);
                if ($coupon_info) {
                    $data['voucher'] = array('status' => 'success', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_success')));
                } else {
                    $data['voucher'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_voucher')));
                }
            }
            $this->load->model('webservices/checkout');
            if ($this->config->get('config_tax_customer') == 'payment') {
                if ($this->request->post['id_billing_address'] == 0) {
                    $this->request->post['id_billing_address'] = $check_customer['address_id'];
                }

                $addresses = $this->model_webservices_checkout->getAddress($this->request->post['id_billing_address']);
                //$addresses = $this->model_webservices_checkout->getAddress($this->request->post['shipping_address_id']);
                if (isset($addresses) && count($addresses) > 0) {
                    $this->session->data['payment_address'] = $addresses;
                    foreach ($addresses as $address) {
                        $this->session->data['payment_address']['country_id'] = $address['country_id'];
                        $this->session->data['payment_address']['zone_id'] = $address['zone_id'];
                    }
                }
            }
//            if ($this->config->get('config_tax_customer') == 'shipping') {
//                if ($this->request->post['id_shipping_address'] == 0) {
//                    $this->request->post['id_shipping_address'] = $check_customer['address_id'];
//                }
//                $addresses = $this->model_webservices_checkout->getAddress($this->request->post['id_shipping_address']);
//                if(isset($addresses) && count($addresses) > 0){
//                    $this->session->data['shipping_address'] = $addresses;
//                    foreach ($addresses as $address) {
//                        $this->session->data['shipping_address']['country_id'] = $address['country_id'];
//                        $this->session->data['shipping_address']['zone_id'] = $address['zone_id'];
//                    }
//                }
//            }
            
            $data['checkout_page']['shipping_methods'] = array();
            
            if (isset($this->session->data['shipping_address'])) {
                // Shipping Methods
                $method_data = array();
                $this->tax->setShippingAddress($this->session->data['shipping_address']['country_id'], $this->session->data['shipping_address']['zone_id']);
                if (isset($this->session->data['payment_address'])) {
                    $this->tax->setPaymentAddress($this->session->data['payment_address']['country_id'], $this->session->data['payment_address']['zone_id']);
                }

                $this->load->model('setting/extension');

                $results = $this->model_setting_extension->getExtensions('shipping');

                foreach ($results as $result) {
                    if ($this->config->get('shipping_'.$result['code'] . '_status')) {
                        $this->load->model('extension/shipping/' . $result['code']);

                        $quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

                        if ($quote) {
                            $method_data[$result['code']] = array(
                                'title' => $quote['title'],
                                'quote' => $quote['quote'],
                                'sort_order' => $quote['sort_order'],
                                'error' => $quote['error']
                            );
                        }
                    }
                }

                $sort_order = array();

                foreach ($method_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $method_data);
                foreach ($method_data as $Shippingdata) {
                    foreach ($Shippingdata['quote'] as $Shipping) {
                        $data['checkout_page']['shipping_methods'][] = array(
                            'name' => strip_tags(html_entity_decode($Shipping['title'])),
                            'code' => $Shipping['code'],
                            'price' => $Shipping['text'],
                            'delay_text' => ''
                        );
                    }
                }
            }
            if (isset($this->request->post['shipping_method']) && $this->request->post['shipping_method'] != '') {
                $this->session->data['shipping_methods'] = $method_data;
                if ($this->session->data['shipping_methods']) {
                    $shipping = explode('.', $this->request->post['shipping_method']);
                    $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                }
            }

            $data['gift_wrapping'] = array(
                "applied" => "0",
                "available" => "0",
                "cost_text" => "",
                "message" => ""
            );
            $data['checkout_page']['guest_checkout_enabled'] = $this->config->get('config_checkout_guest');
            $data['checkout_page']['cart']['total_cart_items'] = $this->cart->countProducts();
            $data['delay_shipping'] = array(
                "applied" => "0",
                "available" => "0"
            );
            $data['checkout_page']['cart_id'] = $this->session->getId();
            $data['checkout_page']['voucher_allowed'] = "1";
            $data['checkout_page']['minimum_purchase_message'] = "";
            $data['checkout_page']['voucher_html'] = "";
            $data['checkout_page']['per_products_shipping'] = "0";
            $data['checkout_page']['per_products_shipping_methods'] = array();
            $data['checkout_page']['default_shipping'] = isset($this->session->data['shipping_method']['code'])?$this->session->data['shipping_method']['code']:"0";
            if(empty($data['checkout_page']['shipping_methods'])) {
                $data['checkout_page']['shipping_available'] = "0";
            } else {
                $data['checkout_page']['shipping_available'] = "1";
            }
            $data['checkout_page']['shipping_message'] = "";
            $data['status'] = "success";
            $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_cart_loaded'));
            $data['checkout_page']['shipping_message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_no_shipping_address'));

            //if (isset($this->session->data['payment_address'])) {
            // Totals
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
                    if (VERSION >= '2.2.0.0') {
                        // We have to put the totals in an array so that they pass by reference.
                        $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                    } else {
                        $this->{'model_extension_total_' . $result['code']}->getTotal($totals, $total, $taxes);
                    }
                }
            }
            foreach ($totals as $total) {
                if ($total['code'] == 'coupon') {
                    $code = '';
                    $start = strpos($total['title'], '(') + 1;
                    $end = strrpos($total['title'], ')');

                    if ($start && $end) {
                        $code = substr($total['title'], $start, $end - $start);
                    }
                    
                    $coupon_info = $this->model_extension_total_coupon->getCoupon($code);
                     
                    if ($coupon_info) {
                        $data['checkout_page']['coupons'][] = array('id' => $coupon_info['coupon_id'], 'name' => $code, 'value' => $this->currency->format($total['value'], $this->session->data['currency']));
                    } else {
                        $data['checkout_page']['coupons'] = array();
                    }
                } else {
                    $data['checkout_page']['coupons'] = array();
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
                        $voucher_info = $this->model_extension_total_voucher->getVoucher($code);
                    }
                    if ($voucher_info) {
                        $data['checkout_page']['vouchers'][] = array('id' => $voucher_info['voucher_id'], 'name' => $code, 'value' => $this->currency->format($total['value'], $this->session->data['currency']));
                    } else {
                        $data['checkout_page']['vouchers'] = array();
                    }
                } else {
                    $data['checkout_page']['vouchers'] = array();
                }
                $data['checkout_page']['totals'][] = array(
                    'name' => strip_tags(html_entity_decode($total['title'])),
                    'value' => $this->currency->format($total['value'], $this->session->data['currency'])
                );
            }
            $data['checkout_page']['order_total'] = $total['value'];
            $data['total_cost'] = $total['value'];
            if (isset($this->session->data['currency']) && !empty($this->session->data['currency'])) {
                $this->load->model('localisation/currency');
                $currency = $this->model_localisation_currency->getCurrencyByCode($this->session->data['currency']);
                $data['currency_code'] = $this->session->data['currency'];
                $data['currency_symbol'] = $currency['symbol_left'] ? $currency['symbol_left'] : $currency['symbol_right'];
            } else {
                $data['currency_code'] = 'USD';
                $data['currency_symbol'] = '2';
            }
        }

        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfCheckout", json_encode($data));
        $this->response->output();
        die;
    }

    public function appCreateOrder() {
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $data = array();
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("updateCustomerDetails", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }

        if (!empty($this->request->post['email']) && !empty($this->request->post['payment_info'])) {
            $redirect = '';
            if (isset($this->request->post['payment_info'])) {
                $this->request->post['payment_info'] = json_decode(str_replace('&quot;', '"', (trim($this->request->post['payment_info']))), true);
            }
            if ($this->cart->hasShipping()) {
                // Validate if shipping address has been set.
                if (!isset($this->session->data['shipping_address'])) {
                    $redirect = $this->url->link('checkout/checkout', '', 'true');
                }

                // Validate if shipping method has been set.
                if (!isset($this->session->data['shipping_method'])) {
                    $redirect = $this->url->link('checkout/checkout', '', 'true');
                }
            } else {
                unset($this->session->data['shipping_address']);
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
            }

            // Validate if payment address has been set.
            if (!isset($this->session->data['payment_address'])) {
                $redirect = $this->url->link('checkout/checkout', '', 'true');
            }

            // Validate if payment method has been set.
            //if (!isset($this->session->data['payment_method'])) {
            //	$redirect = $this->url->link('checkout/checkout', '', 'SSL');
            //}
            // Validate cart has products and has stock.
            if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
                $redirect = $this->url->link('checkout/cart','','true');
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
                    $redirect = $this->url->link('checkout/cart','','true');

                    break;
                }
            }

            if (!$redirect) {
                $order_data = array();

                $order_data['totals'] = array();
                $total = 0;
                $taxes = $this->cart->getTaxes();
                
                $total_data = array(
                        'totals' => &$order_data['totals'],
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

                        $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
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

                if ($order_data['store_id']) {
                    $order_data['store_url'] = $this->config->get('config_url');
                } else {
                    $order_data['store_url'] = HTTP_SERVER;
                }

                if ($this->customer->isLogged()) {
                    $this->load->model('account/customer');

                    $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

                    $order_data['customer_id'] = $this->customer->getId();
                    $order_data['customer_group_id'] = $customer_info['customer_group_id'];
                    $order_data['firstname'] = $customer_info['firstname'];
                    $order_data['lastname'] = $customer_info['lastname'];
                    $order_data['email'] = $customer_info['email'];
                    $order_data['telephone'] = $customer_info['telephone'];
                    $order_data['fax'] = $customer_info['fax'];
                    $order_data['custom_field'] = @unserialize($customer_info['custom_field']);
                } elseif (isset($this->session->data['guest'])) {
                    $order_data['customer_id'] = 0;
                    $order_data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
                    $order_data['firstname'] = $this->session->data['guest']['firstname'];
                    $order_data['lastname'] = $this->session->data['guest']['lastname'];
                    $order_data['email'] = $this->session->data['guest']['email'];
                    $order_data['telephone'] = $this->session->data['guest']['telephone'];
                    $order_data['fax'] = $this->session->data['guest']['fax'];
                    $order_data['custom_field'] = $this->session->data['guest']['custom_field'];
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
                $order_data['payment_custom_field'] = $this->session->data['payment_address']['custom_field'];

                if (isset($this->request->post['payment_info']['payment_method_name'])) {
                    $order_data['payment_method'] = $this->request->post['payment_info']['payment_method_name'];
                    $order_data['payment_code'] = $this->request->post['payment_info']['payment_method_code'];
                } else {
                    $order_data['payment_method'] = '';
                    $order_data['payment_code'] = '';
                }

//print_R($this->session->data['shipping_address']);die;
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
                    $order_data['shipping_custom_field'] = $this->session->data['shipping_address']['custom_field'];

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

                $order_data['comment'] = isset($this->session->data['comment']) ? $this->session->data['comment'] : '';
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
                $order_data['currency_value'] = $this->currency->getValue( $this->session->data['currency']);
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
                if ($this->request->post['payment_info']['status'] == 'approved') {
                    $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('config_order_status_id'));
                }
                $data['status'] = 'success';
                $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_order_success'));
                $data['order_id'] = $this->session->data['order_id'];
                
                $this->cart->clear();
                
            } else {
                $data['redirect'] = $redirect;
            }
        }

        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("appUpdateProfile", json_encode($data));
        $this->response->output();
        die;
    }

    public function appSetShippingMethod() {
        $this->appCheckout();
    }

    public function appReorder() {
        $this->load->language('webservices/config');
        $this->load->model('webservices/home');
        $this->load->model('account/customer');
        $data['shipping_address_reponse'] = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            $this->model_webservices_home->logKnowbandAppRequest("addDetailsOfShippingAddress", json_encode($data));
            echo json_encode($data);
            die;
        } else {
            $data['install_module'] = '';
        }
        
        if (!empty($this->request->post['order_id'])) {
            if (isset($this->request->post['order_id'])) {
                $order_id = $this->request->post['order_id'];
            } else {
                $order_id = 0;
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

            $this->load->model('account/order');
            $this->load->model('tool/upload');

            $order_info = $this->model_account_order->getOrder($order_id);

            if ($order_info) {
                $order_products = $this->model_account_order->getOrderProducts($order_id);
                foreach ($order_products as $order_product) {
                    $order_product_info = $this->model_account_order->getOrderProduct($order_id, $order_product['order_product_id']);
                    if ($order_product_info) {
                        $this->load->model('catalog/product');

                        $product_info = $this->model_catalog_product->getProduct($order_product_info['product_id']);

                        if ($product_info) {
                            $option_data = array();

                            $order_options = $this->model_account_order->getOrderOptions($order_product_info['order_id'], $order_product['order_product_id']);

                            foreach ($order_options as $order_option) {
                                if ($order_option['type'] == 'select' || $order_option['type'] == 'radio' || $order_option['type'] == 'image') {
                                    $option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
                                } elseif ($order_option['type'] == 'checkbox') {
                                    $option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
                                } elseif ($order_option['type'] == 'text' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
                                    $option_data[$order_option['product_option_id']] = $order_option['value'];
                                } elseif ($order_option['type'] == 'file') {
                                    $option_data[$order_option['product_option_id']] = $this->encryption->encrypt($order_option['value']);
                                }
                            }

                            $this->cart->add($order_product_info['product_id'], $order_product_info['quantity'], $option_data);
                        } else {
                            
                        }
                    }
                }
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);
                if (!empty($this->request->post['email'])) {

                    $check_customer = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
                    if (!empty($check_customer)) {
                        $customer_id = $check_customer['customer_id'];
                    } else {
                        $customer_id = 0;
                    }

                    $this->session->data['customer_id'] = $customer_id;
                    $this->customer_id = $customer_id;
                    if (isset($this->request->post['id_shipping_address'])) {
                        $shipping_add = $this->getShippingAddress($this->request->post['id_shipping_address']);
                        if ($shipping_add) {
                            $data['shipping_address'] = $shipping_add['shipping_address'];
                        }
                    } else {
                        $data['shipping_address'] = array();
                    }
                    if (isset($this->request->post['id_billing_address'])) {
                        $billing_add = $this->getBillingAddress($this->request->post['id_billing_address']);
                        if ($billing_add) {
                            $data['billing_address'] = $billing_add['billing_address'];
                        }
                    } else {
                        $data['billing_address'] = array();
                    }

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
                    }

                    $products = $this->cart->getProducts();

                    $this->load->model('webservices/image');
                    foreach ($products as $product) {
                        $attribute_string = '';
                        $product_total = 0;

                        foreach ($products as $product_2) {
                            if ($product_2['product_id'] == $product['product_id']) {
                                $product_total += $product_2['quantity'];
                            }
                        }
                        $this->load->language('webservices/config');

                        $width = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_width'));
                        $height = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_height'));

                        if ($product['minimum'] > $product_total) {
                            $data['error_warning'] = sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_minimum')), str_replace('&quot;', '"', $product['name']), $product['minimum']);
                        }

                        if ($product['image']) {
                            $image = $this->model_webservices_image->resize($product['image'], $width, $height);
                        } else {
                            $image = '';
                        }
                        
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
                            $attribute_data[] = array(
                                'name' => $option['name'],
                                'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                            );
                            //                    $option_data[] = array(
                            //                        'name' => $option['name'],
                            //                        'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                            //                    );
                        }
                        if (isset($attribute_data)) {
                            foreach ($attribute_data as $attr) {
                                if (isset($attribute_string) && $attribute_string == '') {
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
                        }
                        $option_data[] = array(
                            'name' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_quantity')),
                            'value' => $product['quantity']
                        );

                        // Display prices
                        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                            $price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        } else {
                            $price = false;
                        }

                        // Display prices
                        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                            $total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']);
                        } else {
                            $total = false;
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
                            'discount_price' => '',
                            'discount_percentage' => '',
                            'images' => $image,
                            'price' => $total,
                            'quantity' => $product['quantity'],
                            'product_items' => $option_data,
                            'customizable_items' => array()
                        );
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
                    $this->load->model('webservices/checkout');
                    if ($this->config->get('config_tax_customer') == 'payment') {
                        if ($this->request->post['id_billing_address'] == 0) {
                            $this->request->post['id_billing_address'] = $check_customer['address_id'];
                        }

                        $addresses = $this->model_webservices_checkout->getAddress($this->request->post['id_billing_address']);
                        //$addresses = $this->model_webservices_checkout->getAddress($this->request->post['shipping_address_id']);
                        if (isset($addresses) && count($addresses) > 0) {
                            $this->session->data['payment_address'] = $addresses;
                            foreach ($addresses as $address) {
                                $this->session->data['payment_address']['country_id'] = $address['country_id'];
                                $this->session->data['payment_address']['zone_id'] = $address['zone_id'];
                            }
                        }
                    }
                    //            if ($this->config->get('config_tax_customer') == 'shipping') {
                    //                if ($this->request->post['id_shipping_address'] == 0) {
                    //                    $this->request->post['id_shipping_address'] = $check_customer['address_id'];
                    //                }
                    //                $addresses = $this->model_webservices_checkout->getAddress($this->request->post['id_shipping_address']);
                    //                if(isset($addresses) && count($addresses) > 0){
                    //                    $this->session->data['shipping_address'] = $addresses;
                    //                    foreach ($addresses as $address) {
                    //                        $this->session->data['shipping_address']['country_id'] = $address['country_id'];
                    //                        $this->session->data['shipping_address']['zone_id'] = $address['zone_id'];
                    //                    }
                    //                }
                    //            }


                    if (isset($this->session->data['shipping_address'])) {
                        // Shipping Methods
                        $method_data = array();
                        $this->tax->setShippingAddress($this->session->data['shipping_address']['country_id'], $this->session->data['shipping_address']['zone_id']);
                        if (isset($this->session->data['payment_address'])) {
                            $this->tax->setPaymentAddress($this->session->data['payment_address']['country_id'], $this->session->data['payment_address']['zone_id']);
                        }

                        $this->load->model('setting/extension');

                        $results = $this->model_setting_extension->getExtensions('shipping');
                        

                        
                        foreach ($results as $result) {
                            if ($this->config->get('shipping_'.$result['code'] . '_status')) {
                                $this->load->model('extension/shipping/' . $result['code']);

                                $quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

                                if ($quote) {
                                    $method_data[$result['code']] = array(
                                        'title' => $quote['title'],
                                        'quote' => $quote['quote'],
                                        'sort_order' => $quote['sort_order'],
                                        'error' => $quote['error']
                                    );
                                }
                            }
                        }
                        
                        
                        $sort_order = array();

                        foreach ($method_data as $key => $value) {
                            $sort_order[$key] = $value['sort_order'];
                        }

                        array_multisort($sort_order, SORT_ASC, $method_data);
                        foreach ($method_data as $Shippingdata) {
                            foreach ($Shippingdata['quote'] as $Shipping) {
                                $data['shipping_methods'][] = array(
                                    'name' => $Shipping['title'],
                                    'code' => $Shipping['code'],
                                    'price' => $Shipping['text'],
                                    'delay_text' => ''
                                );
                            }
                        }
                    }
                    if (isset($this->request->post['shipping_method']) && $this->request->post['shipping_method'] != '') {
                        $this->session->data['shipping_methods'] = $method_data;
                        if ($this->session->data['shipping_methods']) {
                            $shipping = explode('.', $this->request->post['shipping_method']);
                            $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                        }
                    }

                    $data['gift_wrapping'] = array(
                        "applied" => "0",
                        "available" => "0",
                        "cost_text" => "",
                        "message" => ""
                    );
                    $data['guest_checkout_enabled'] = $this->config->get('config_checkout_guest');
                    $data['cart']['total_cart_items'] = $this->cart->countProducts();
                    $data['delay_shipping'] = array(
                        "applied" => "0",
                        "available" => "0"
                    );
                    $data['cart_id'] = $this->session->getId();
                    $data['voucher_allowed'] = "1";
                    $data['minimum_purchase_message'] = "";
                    $data['voucher_html'] = "";
                    $data['per_products_shipping'] = "0";
                    $data['per_products_shipping_methods'] = array();
                    $data['checkout_page']['per_products_shipping'] = "0";
                    $data['default_shipping'] = "0";
                    $data['shipping_available'] = "1";
                    $data['shipping_message'] = "";
                    $data['status'] = "success";
                    $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_cart_loaded'));
                    $data['shipping_message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_cart_loaded'));


                    //if (isset($this->session->data['payment_address'])) {
                    // Totals
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
                            if (VERSION >= '2.2.0.0') {
                                // We have to put the totals in an array so that they pass by reference.
                                $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                            } else {
                                $this->{'model_extension_total_' . $result['code']}->getTotal($totals, $total, $taxes);
                            }
                        }
                    }
                    foreach ($totals as $total) {
                        if ($total['code'] == 'coupon') {
                            $code = '';
                            $start = strpos($total['title'], '(') + 1;
                            $end = strrpos($total['title'], ')');

                            if ($start && $end) {
                                $code = substr($total['title'], $start, $end - $start);
                            }
                            $coupon_info = $this->model_extension_total_coupon->getCoupon($code);
                            
                            if ($coupon_info) {
                                $data['coupons'][] = array('id' => $coupon_info['coupon_id'], 'name' => $code, 'value' => $this->currency->format($total['value'], $this->session->data['currency']));
                            } else {
                                $data['coupons'] = array();
                            }
                        } else {
                            $data['coupons'] = array();
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
                                $voucher_info = $this->model_extension_total_voucher->getVoucher($code);
                            }
                            if ($voucher_info) {
                                $data['vouchers'][] = array('id' => $voucher_info['voucher_id'], 'name' => $code, 'value' => $this->currency->format($total['value'], $this->session->data['currency']));
                            } else {
                                $data['vouchers'] = array();
                            }
                        } else {
                            $data['vouchers'] = array();
                        }
                        $data['totals'][] = array(
                            'name' => $total['title'],
                            'value' => $this->currency->format($total['value'], $this->session->data['currency'])
                        );
                    }
                    $data['order_total'] = $total['value'];
                    $data['total_cost'] = $total['value'];
                    if (isset($this->session->data['currency']) && !empty($this->session->data['currency'])) {
                        $this->load->model('localisation/currency');
                        $currency = $this->model_localisation_currency->getCurrencyByCode($this->session->data['currency']);
                        $data['currency_code'] = $this->session->data['currency'];
                        $data['currency_symbol'] = $currency['symbol_left'] ? $currency['symbol_left'] : $currency['symbol_right'];
                    } else {
                        $data['currency_code'] = 'USD';
                        $data['currency_symbol'] = '2';
                    }
                }
                $data['status'] = 'success';
                $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_order_success'));
            } else {
                
            }
        } else {
            $data['status'] = 'failure';
            $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_reorder_fail'));
        }
        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("addDetailsOfShippingAddress", json_encode($data));
        $this->response->output();
        die;
    }

    public function appCheckOrderStatus() {
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $data['shipping_address_reponse'] = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            $this->model_webservices_home->logKnowbandAppRequest("addDetailsOfShippingAddress", json_encode($data));
            echo json_encode($data);
            die;
        } else {
            $data['install_module'] = '';
        }

        if (!empty($this->request->post['session_data'])) {
            //$this->load->model('account/order');
            //$order_info = $this->model_account_order->getOrder($order_id);
            //if ($order_info) {
            //      $order_products = $this->model_account_order->getOrderProducts($order_id);
            //	foreach ($order_products as $order_product) {
            //   }
            //       
            $data['status'] = 'failure';
            $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_reorder_fail'));
            $data['cart_id'] = $this->session->getId();
            //}else{
            //    
            //}
        } else {
            $data['status'] = 'failure';
            $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_reorder_fail'));
        }
        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("addDetailsOfShippingAddress", json_encode($data));
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

?>
