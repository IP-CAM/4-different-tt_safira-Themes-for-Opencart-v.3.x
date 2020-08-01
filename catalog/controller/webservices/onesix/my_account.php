<?php

class ControllerWebservicesOneSixMyAccount extends Controller {

    private $error = array();

    public function appRegisterUser() {

        $data['signup_user'] = array();
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("Signup", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }

        //load model
        $this->load->language('account/register');
        $this->load->model('account/customer');

        if (isset($this->request->post['signup'])) {

            $this->request->post = json_decode(str_replace('&quot;', '"', (trim($this->request->post['signup']))), true);
            $this->request->post['firstname'] = $this->request->post['first_name'];
            $this->request->post['lastname'] = $this->request->post['last_name'];
            if (isset($this->request->post) && $this->validate()) {
                if (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
                    $customer_data = array(
                        'firstname' => $this->request->post['firstname'],
                        'lastname' => $this->request->post['lastname'],
                        'company' => '',
                        'telephone' => $this->request->post['mobile_number'],
                        'fax' => '',
                        'address_1' => '',
                        'address_2' => '',
                        'postcode' => '',
                        'city' => '',
                        'zone_id' => 0,
                        'country_id' => 0,
                        'email' => $this->request->post['email'],
                        'password' => $this->request->post['password'],
                    );
                    
                    $customer_data['custom_field'] = array();
                    if(isset($setting_data['gender_field'])){
                        $customer_data['custom_field']['account'][$setting_data['gender_field']] = $this->request->post['title'];
                    } else {
                        $setting_data['gender_field']='';
                    }
                    if(isset($setting_data['dob_field'])){
                        $customer_data['custom_field']['account'][$setting_data['dob_field']] = $this->request->post['dob'];
                    } else {
                        $setting_data['dob_field']='';
                    }
                    $customer_id = $this->model_webservices_home->addCustomer($customer_data);
                    $this->db->query('INSERT INTO `'.DB_PREFIX.'kbmobileapp_unique_verification` (`id_customer`, `mobile_number`,`country_code`) VALUES ("'.$customer_id.'","'.$this->request->post['mobile_number'].'","'.$this->request->post['country_code'].'");');
                }
                if ($this->request->post['social_login'] == 'yes') {
                    $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
                    $this->request->post['login'] = '{"email":"' . $this->request->post['email'] . '","password":"" }';
                    $this->session->data['customer_id'] = $customer_info['customer_id'];
                    $this->CheckLogin('yes');
                }
                // Clear any previous login attempts for unregistered accounts.
                if (version_compare(VERSION, '2.0.1.0', '>=')) {
                    $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
                }

                $this->customer->login($this->request->post['email'], $this->request->post['password']);

                unset($this->session->data['guest']);

                // Add to activity log
                $this->load->model('account/activity');

                $activity_data = array(
                    'customer_id' => $this->customer->getId(),
                    'name' => $this->request->post['firstname'] . ' ' . $this->request->post['lastname']
                );

                $this->model_account_activity->addActivity('register', $activity_data);

                $this->load->model('webservices/wishlist');
                $results = $this->model_webservices_wishlist->getWishlist($this->session->data['customer_id']);
                $cart_data = array();
                $cart_data_count = 0;

                if (version_compare(VERSION, '2.1.0.1', '>=')) {
                    // Remove all the expired carts with no customer ID
                    $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND date_added < DATE_SUB(NOW(), INTERVAL 1 HOUR)");

                    if ($this->customer->getId()) {
                        // We want to change the session ID on all the old items in the customers cart
                        $this->db->query("UPDATE " . DB_PREFIX . "cart SET session_id = '" . $this->db->escape($this->session->getId()) . "' WHERE customer_id = '" . (int) $this->customer->getId() . "'");

                        // Once the customer is logged in we want to update the customer ID on all items he has
                        $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

                        foreach ($cart_query->rows as $cart) {
                            $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int) $cart['cart_id'] . "'");

                            // The advantage of using $this->add is that it will check if the products already exist and increaser the quantity if necessary.
                            $this->cart->add($cart['product_id'], $cart['quantity'], json_decode($cart['option']), $cart['recurring_id']);
                        }
                    }

                    $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int) $this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

                    if ($cart_query->num_rows > 0) {
                        foreach ($cart_query->rows as $cart_query_row) {
                            $cart_data[$cart_query_row['cart_id']] = (int) $cart_query_row['quantity'];
                            $cart_data_count += (int) $cart_query_row['quantity'];
                        }
                    }
                } else {
                    if (isset($this->session->data['cart'])) {
                        $cart_data = $this->session->data['cart'];
                        foreach ($cart_data as $key => $value) {
                            $cart_data_count += $value;
                        }
                    }
                }
                $data['signup_user'] = array('status' => 'success', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_customer_created')), 'customer_id' => $this->customer->getId(), 'wishlist_count' => count($results), 'session_data' => $this->session->getId(), 'cart_count' => $cart_data_count);
            } else {
                if(isset($this->error['warning'])) {
                    $data['signup_user'] = array('status' => 'failure', 'message' => $this->error['warning']);
                } else {
                   $data['signup_user'] = array('status' => 'failure', 'message' => $this->error); 
                }    
            }
        } else {
            $data['signup_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get("text_wrong")));
        }

        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("Signup", json_encode($data));
        $this->response->output();
        die;
    }

    public function validate() {
        $this->load->model('account/customer');
        $this->load->language('webservices/config');
        
        if (isset($this->request->post['user_type']) && $this->request->post['user_type'] == 'guest') {
            if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
                $this->error['email'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_email'));
            }
            
        } else {
            if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
                $this->error['firstname'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_firstname'));
            }

            if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
                $this->error['lastname'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_lastname'));
            }

            if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
                $this->error['email'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_email'));
            }

            if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
                $this->error['warning'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_exists'));
            }

            if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
                $this->error['password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_password'));
            }
        }
        return !$this->error;
    }

    public function appForgotPassword() {
        $data['forgot'] = array();
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("forgotten", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }

        $this->load->language('account/forgotten');
        $this->load->model('account/customer');
        if (isset($this->request->post['email']) && $this->forgot_validate()) {
            $this->load->language('mail/forgotten');

            $password = substr(sha1(uniqid(mt_rand(), true)), 0, 10);

            $this->model_account_customer->editPassword($this->request->post['email'], $password);

            $subject = sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_subject')), $this->config->get('config_name'));

            $message = sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_greeting')), $this->config->get('config_name')) . "\n\n";
            $message .= iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_password')) . "\n\n";
            $message .= $password;
            try {
                if (version_compare(VERSION, '2.0.2.0', '<')) {
                    $mail = new Mail($this->config->get('config_mail'));

                    $mail->setTo($this->request->post['email']);
                    $mail->setFrom($this->config->get('config_email'));
                    $mail->setSender($this->config->get('config_name'));
                    $mail->setSubject($subject);
                    $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                    $mail->send();
                } else {
                    $mail = new Mail();
                    $mail->protocol = $this->config->get('config_mail_protocol');
                    $mail->parameter = $this->config->get('config_mail_parameter');
                    $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                    $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                    $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                    $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                    $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                    $mail->setTo($this->request->post['email']);
                    $mail->setFrom($this->config->get('config_email'));
                    $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                    $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                    $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                    $mail->send();
                }
            } catch (Exception $e) {
                $data['status'] = 'failure';
                $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_error_email'));
                $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
                $this->response->addHeader('Access-Control-Max-Age: 1000');
                $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
                $this->response->addHeader('Content-Type: application/json');
                $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
                $this->response->setOutput(json_encode($data));
                $this->model_webservices_home->logKnowbandAppRequest("forgotten", json_encode($data));
                $this->response->output();
                die;
            }

            $this->session->data['success'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_success'));

            // Add to activity log
            $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

            if ($customer_info) {
                $this->load->model('account/activity');

                $activity_data = array(
                    'customer_id' => $customer_info['customer_id'],
                    'name' => $customer_info['firstname'] . ' ' . $customer_info['lastname']
                );

                $this->model_account_activity->addActivity('forgotten', $activity_data);
            }
            $data['status'] = 'success';
            $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_confirm_email')) . $this->request->post['email'];
        } else {
            $data['status'] = 'failure';
            $data['message'] = $this->error;
        }

        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("forgotten", json_encode($data));
        $this->response->output();
        die;
    }

    public function forgot_validate() {
        if (!isset($this->request->post['email'])) {
            $this->error['warning'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_email'));
        } elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $this->error['warning'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_email'));
        }
        return !$this->error;
    }

    public function appLogin() {
        
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $data['login_user'] = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("CheckLogin", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }

        //load model
        $this->load->language('account/login');
        $this->load->model('account/customer');
        if ((isset($this->request->post['email']) && ($this->request->post['password']))) {

            if ((isset($this->request->post['email']) && ($this->request->post['password']))) {

                if (version_compare(VERSION, '2.0.1.0', '>=')) {

                    $login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

                    if ($login_info && ($login_info['total'] > $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
                        $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_attempts')), 'customer_id' => '0');
                    }
                }

                // Check if customer has been approved.
                $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
//                if ($customer_info && !$customer_info['approved']) {
//                    $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_approved'), 'customer_id' => '0');
//                }
                if (!$data['login_user']) {
                    if (!($this->customer->login($this->request->post['email'], $this->request->post['password']))) {
                        $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_login')), 'customer_id' => '0');
                        if (version_compare(VERSION, '2.0.1.0', '>=')) {
                            $this->model_account_customer->addLoginAttempt($this->request->post['email']);
                        }
                    } else {
                        
                        $this->load->model('webservices/wishlist');
                        $results = $this->model_webservices_wishlist->getWishlist($this->session->data['customer_id']);
                        $cart_data = array();
                        $cart_data_count = 0;

                        // Default Shipping Address
                        $this->load->model('account/address');

                        if ($this->config->get('config_tax_customer') == 'payment') {
                            $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                        }

                        if ($this->config->get('config_tax_customer') == 'shipping') {
                            $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                        }

                        if (version_compare(VERSION, '2.1.0.1', '>=')) {
                            // Remove all the expired carts with no customer ID
                            $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND date_added < DATE_SUB(NOW(), INTERVAL 1 HOUR)");

                            if ($this->customer->getId()) {
                                // We want to change the session ID on all the old items in the customers cart
                                $this->db->query("UPDATE " . DB_PREFIX . "cart SET session_id = '" . $this->db->escape($this->session->getId()) . "' WHERE customer_id = '" . (int) $this->customer->getId() . "'");

                                // Once the customer is logged in we want to update the customer ID on all items he has
                                $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

                                foreach ($cart_query->rows as $cart) {
                                    $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int) $cart['cart_id'] . "'");

                                    // The advantage of using $this->add is that it will check if the products already exist and increaser the quantity if necessary.
                                    $this->cart->add($cart['product_id'], $cart['quantity'], json_decode($cart['option']), $cart['recurring_id']);
                                }
                            }

                            $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int) $this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

                            if ($cart_query->num_rows > 0) {
                                foreach ($cart_query->rows as $cart_query_row) {
                                    $cart_data[$cart_query_row['cart_id']] = (int) $cart_query_row['quantity'];
                                    $cart_data_count += (int) $cart_query_row['quantity'];
                                }
                            }
                        } else {
                            if (isset($this->session->data['cart'])) {
                                $cart_data = $this->session->data['cart'];
                                foreach ($cart_data as $key => $value) {
                                    $cart_data_count += $value;
                                }
                            }
                        }

                        $data['login_user'] = array('status' => 'success', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_login_success')), 'customer_id' => $this->session->data['customer_id'], 'wishlist_count' => count($results), 'session_data' => $this->session->getId(), 'cart_count' => $cart_data_count);
                        if (version_compare(VERSION, '2.0.1.0', '>=')) {
                            $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
                        }
                    }
                    
                }
            }
        } else {
            $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_login')), 'customer_id' => '0');
        }

        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("CheckLogin", json_encode($data));
        $this->response->output();
        die;
    }

    public function appSocialLogin() {
        
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $data['login_user'] = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("CheckLogin", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }

        //load model
        $this->load->language('account/login');
        $this->load->model('account/customer');
        if (isset($this->request->post['login'])) {

            $this->request->post = json_decode(str_replace('&quot;', '"', (trim($this->request->post['login']))), true);
            if (isset($this->request->post['email'])) {

                if (version_compare(VERSION, '2.0.1.0', '>=')) {

                    $login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

                    if ($login_info && ($login_info['total'] > $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
                        $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_attempts')), 'customer_id' => '0');
                    }
                }

                // Check if customer has been approved.
                $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

                if (!$customer_info) {
                    $customer_data = array(
                        'firstname' => $this->request->post['first_name'],
                        'lastname' => $this->request->post['last_name'],
                        'company' => '',
                        'telephone' => '',
                        'fax' => '',
                        'email' => $this->request->post['email'],
                        'password' => '',
                    );
                    $customer_id = $this->model_webservices_home->addCustomer($customer_data);
                    $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
                }
                
                if ($customer_info && !$customer_info['status']) {
                    $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_approved')), 'customer_id' => '0');
                }
                if (!$data['login_user']) {
//                    if (!($this->customer->login($this->request->post['email']))) {
                    if (false) {
                        $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_login')), 'customer_id' => '0');
                        if (version_compare(VERSION, '2.0.1.0', '>=')) {
                            $this->model_account_customer->addLoginAttempt($this->request->post['email']);
                        }
                    } else {

                        $this->load->model('webservices/wishlist');
                        $results = $this->model_webservices_wishlist->getWishlist($customer_info['customer_id']);
                        $cart_data = array();
                        $cart_data_count = 0;

                        // Default Shipping Address
                        $this->load->model('account/address');

                        if ($this->config->get('config_tax_customer') == 'payment') {
                            $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                        }

                        if ($this->config->get('config_tax_customer') == 'shipping') {
                            $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                        }

                        if (version_compare(VERSION, '2.1.0.1', '>=')) {
                            // Remove all the expired carts with no customer ID
                            $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND date_added < DATE_SUB(NOW(), INTERVAL 1 HOUR)");

                            if ($this->customer->getId()) {
                                // We want to change the session ID on all the old items in the customers cart
                                $this->db->query("UPDATE " . DB_PREFIX . "cart SET session_id = '" . $this->db->escape($this->session->getId()) . "' WHERE customer_id = '" . (int) $this->customer->getId() . "'");

                                // Once the customer is logged in we want to update the customer ID on all items he has
                                $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

                                foreach ($cart_query->rows as $cart) {
                                    $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int) $cart['cart_id'] . "'");

                                    // The advantage of using $this->add is that it will check if the products already exist and increaser the quantity if necessary.
                                    $this->cart->add($cart['product_id'], $cart['quantity'], json_decode($cart['option']), $cart['recurring_id']);
                                }
                            }

                            $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int) $this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

                            if ($cart_query->num_rows > 0) {
                                foreach ($cart_query->rows as $cart_query_row) {
                                    $cart_data[$cart_query_row['cart_id']] = (int) $cart_query_row['quantity'];
                                    $cart_data_count += (int) $cart_query_row['quantity'];
                                }
                            }
                        } else {
                            if (isset($this->session->data['cart'])) {
                                $cart_data = $this->session->data['cart'];
                                foreach ($cart_data as $key => $value) {
                                    $cart_data_count += $value;
                                }
                            }
                        }

                        $data['login_user'] = array('status' => 'success', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_login_success')), 'customer_id' => $customer_info['customer_id'], 'wishlist_count' => count($results), 'session_data' => $this->session->getId(), 'cart_count' => $cart_data_count);
                        if (version_compare(VERSION, '2.0.1.0', '>=')) {
                            $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
                        }
                    }
                }
            }
        } else {
            $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_login')), 'customer_id' => '0');
        }

        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("CheckLogin", json_encode($data));
        $this->response->output();
        die;
    }
    public function appMapEmailWithUUID() {
        $this->load->model('account/customer');
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        
        $setting_data = $this->config->get('webservice');
        
        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("CheckLogin", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }
        if(isset($this->request->post['email_id'])) {
            $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email_id']);
            $this->db->query('DELETE FROM `'.DB_PREFIX.'kbmobileapp_unique_verification` WHERE id_customer = '.$customer_info['customer_id'].' AND fid!=""');
            $this->db->query('INSERT INTO `'.DB_PREFIX.'kbmobileapp_unique_verification` (`id_customer`, `fid`) VALUES ("'.$customer_info['customer_id'].'","'.$this->request->post['unique_id'].'");');
            $data['status'] = 'success';
            $data['message'] = 'The account has been mapped for the fingerprint login in this device';
        } else {
            $data['status'] = 'failure';
            $data['message'] = 'Email ID does not exist';
        } 
        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("CheckLogin", json_encode($data));
        $this->response->output();
        die;
    }
    public function appLoginViaPhone() {
        
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $data['login_user'] = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("CheckLogin", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }

        //load model
        $this->load->language('account/login');
        $this->load->model('account/customer');
            if (isset($this->request->post['mobile_number'])) {
                
                $customer_info = $this->model_webservices_home->getCustomerByPhone($this->request->post['mobile_number']);
                
                $this->request->post['email_id'] = $customer_info['email'];
                
                if (version_compare(VERSION, '2.0.1.0', '>=')) {
                   
                    $login_info = $this->model_account_customer->getLoginAttempts($customer_info['email']);

                    if ($login_info && ($login_info['total'] > $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
                        $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_attempts')), 'customer_id' => '0');
                    }
                }
                if ($customer_info && !$customer_info['status']) {
                    $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_approved')), 'customer_id' => '0');
                }
                if (!$data['login_user']) {
//                    if (!($this->customer->login($this->request->post['email']))) {
                    if (false) {
                        $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_login')), 'customer_id' => '0');
                        if (version_compare(VERSION, '2.0.1.0', '>=')) {
                            $this->model_account_customer->addLoginAttempt($customer_info['email']);
                        }
                    } else {

                        $this->load->model('webservices/wishlist');
                        $results = $this->model_webservices_wishlist->getWishlist($customer_info['customer_id']);
                        $cart_data = array();
                        $cart_data_count = 0;

                        // Default Shipping Address
                        $this->load->model('account/address');

                        if ($this->config->get('config_tax_customer') == 'payment') {
                            $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                        }

                        if ($this->config->get('config_tax_customer') == 'shipping') {
                            $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                        }

                        if (version_compare(VERSION, '2.1.0.1', '>=')) {
                            // Remove all the expired carts with no customer ID
                            $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND date_added < DATE_SUB(NOW(), INTERVAL 1 HOUR)");

                            if ($this->customer->getId()) {
                                // We want to change the session ID on all the old items in the customers cart
                                $this->db->query("UPDATE " . DB_PREFIX . "cart SET session_id = '" . $this->db->escape($this->session->getId()) . "' WHERE customer_id = '" . (int) $this->customer->getId() . "'");

                                // Once the customer is logged in we want to update the customer ID on all items he has
                                $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

                                foreach ($cart_query->rows as $cart) {
                                    $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int) $cart['cart_id'] . "'");

                                    // The advantage of using $this->add is that it will check if the products already exist and increaser the quantity if necessary.
                                    $this->cart->add($cart['product_id'], $cart['quantity'], json_decode($cart['option']), $cart['recurring_id']);
                                }
                            }

                            $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int) $this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

                            if ($cart_query->num_rows > 0) {
                                foreach ($cart_query->rows as $cart_query_row) {
                                    $cart_data[$cart_query_row['cart_id']] = (int) $cart_query_row['quantity'];
                                    $cart_data_count += (int) $cart_query_row['quantity'];
                                }
                            }
                        } else {
                            if (isset($this->session->data['cart'])) {
                                $cart_data = $this->session->data['cart'];
                                foreach ($cart_data as $key => $value) {
                                    $cart_data_count += $value;
                                }
                            }
                        }

                        $data['login_user'] = array('status' => 'success', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_login_success')), 'customer_id' => $customer_info['customer_id'], 'wishlist_count' => count($results),'email' => $this->request->post['email_id'] ,'session_data' => $this->session->getId(), 'cart_count' => $cart_data_count);
                        if (version_compare(VERSION, '2.0.1.0', '>=')) {
                            $this->model_account_customer->deleteLoginAttempts($this->request->post['email_id']);
                        }
                    }
                }
        } else {
            $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_login')), 'customer_id' => '0');
        }
            

        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("CheckLogin", json_encode($data));
        $this->response->output();
        die;
    }
    
    
    public function appUpdatePassword() {
        $json = array();

        $this->load->language('webservices/config');
        
        $setting_data = $this->config->get('webservice');
        
        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            die;
        } else {
            $json['install_module'] = '';
        }
        $json['status'] = 'success';
        
        $query = $this->db->query("SELECT id_customer FROM `".DB_PREFIX."kbmobileapp_unique_verification` where mobile_number = '".$this->request->post['mobile_number']."' and country_code = '".$this->request->post['country_code']."'"); 
        if($query->num_rows > 0){
            $customer_id = $query->row['id_customer'];
            $query = $this->db->query("SELECT id_customer FROM `".DB_PREFIX."kbmobileapp_unique_verification` where mobile_number = '".$this->request->post['mobile_number']."' and country_code = '".$this->request->post['country_code']."'");
            $this->db->query("UPDATE " . DB_PREFIX . "customer SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($this->request->post['new_password'])))) . "', code = '' WHERE customer_id = '" . (int)($customer_id) . "'");
            $json['status'] = 'success';
            $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('password_update_message'));
            $json['session_data'] = $this->session->getId();
        } else {
            $json['status'] = 'failure';
            $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('phone_number_not_exist_message'));
            $json['session_data'] = $this->session->getId();
        }
        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("CheckLogin", json_encode($json));
        $this->response->output();
        die;
    }
    
    public function appCheckIfContactNumberExists() {
        $json = array();

        $this->load->language('webservices/config');
        
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            die;
        } else {
            $json['install_module'] = '';
        }
        $json['status'] = 'success';
        
        $query = $this->db->query("SELECT * FROM `".DB_PREFIX."kbmobileapp_unique_verification` where mobile_number = '".$this->request->post['mobile_number']."' and country_code = '".$this->request->post['country_code']."'"); 
        if($query->num_rows > 0){
            $json['status'] = 'success';
            $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('phone_number_exist_message'));
            $json['does_mobile_number_exists'] = true;
            $json['session_data'] = $this->session->getId();
        } else {
            $json['status'] = 'failure';
            $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('phone_number_not_exist_message'));
            $json['does_mobile_number_exists'] = false;
            $json['session_data'] = $this->session->getId();
        }
        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("CheckLogin", json_encode($json));
        $this->response->output();
        die;
    }
    
    public function appLoginViaEmail() {
        
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $data['login_user'] = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("CheckLogin", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }

        //load model
        $this->load->language('account/login');
        $this->load->model('account/customer');
            if (isset($this->request->post['email_id'])) {

                if (version_compare(VERSION, '2.0.1.0', '>=')) {

                    $login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email_id']);

                    if ($login_info && ($login_info['total'] > $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
                        $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_attempts')), 'customer_id' => '0');
                    }
                }

                // Check if customer has been approved.
                $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email_id']);
                
                if ($customer_info && !$customer_info['status']) {
                    $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_approved')), 'customer_id' => '0');
                }
                if (!$data['login_user']) {
//                    if (!($this->customer->login($this->request->post['email']))) {
                    if (false) {
                        $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_login')), 'customer_id' => '0');
                        if (version_compare(VERSION, '2.0.1.0', '>=')) {
                            $this->model_account_customer->addLoginAttempt($this->request->post['email_id']);
                        }
                    } else {

                        $this->load->model('webservices/wishlist');
                        $results = $this->model_webservices_wishlist->getWishlist($customer_info['customer_id']);
                        $cart_data = array();
                        $cart_data_count = 0;

                        // Default Shipping Address
                        $this->load->model('account/address');

                        if ($this->config->get('config_tax_customer') == 'payment') {
                            $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                        }

                        if ($this->config->get('config_tax_customer') == 'shipping') {
                            $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                        }

                        if (version_compare(VERSION, '2.1.0.1', '>=')) {
                            // Remove all the expired carts with no customer ID
                            $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND date_added < DATE_SUB(NOW(), INTERVAL 1 HOUR)");

                            if ($this->customer->getId()) {
                                // We want to change the session ID on all the old items in the customers cart
                                $this->db->query("UPDATE " . DB_PREFIX . "cart SET session_id = '" . $this->db->escape($this->session->getId()) . "' WHERE customer_id = '" . (int) $this->customer->getId() . "'");

                                // Once the customer is logged in we want to update the customer ID on all items he has
                                $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

                                foreach ($cart_query->rows as $cart) {
                                    $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int) $cart['cart_id'] . "'");

                                    // The advantage of using $this->add is that it will check if the products already exist and increaser the quantity if necessary.
                                    $this->cart->add($cart['product_id'], $cart['quantity'], json_decode($cart['option']), $cart['recurring_id']);
                                }
                            }

                            $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int) $this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

                            if ($cart_query->num_rows > 0) {
                                foreach ($cart_query->rows as $cart_query_row) {
                                    $cart_data[$cart_query_row['cart_id']] = (int) $cart_query_row['quantity'];
                                    $cart_data_count += (int) $cart_query_row['quantity'];
                                }
                            }
                        } else {
                            if (isset($this->session->data['cart'])) {
                                $cart_data = $this->session->data['cart'];
                                foreach ($cart_data as $key => $value) {
                                    $cart_data_count += $value;
                                }
                            }
                        }

                        $data['login_user'] = array('status' => 'success', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_login_success')), 'customer_id' => $customer_info['customer_id'], 'wishlist_count' => count($results),'email' => $this->request->post['email_id'] ,'session_data' => $this->session->getId(), 'cart_count' => $cart_data_count);
                        if (version_compare(VERSION, '2.0.1.0', '>=')) {
                            $this->model_account_customer->deleteLoginAttempts($this->request->post['email_id']);
                        }
                    }
                }
        } else {
            $data['login_user'] = array('status' => 'failure', 'message' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_login')), 'customer_id' => '0');
        }
            

        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("CheckLogin", json_encode($data));
        $this->response->output();
        die;
    }
    

    public function appGetOrders() {
        
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $data['order_history'] = array();

        $setting_data = $this->config->get('webservice');
            
        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfUserOrder", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }
        $this->load->model('webservices/checkout');
        if (!empty($this->request->post['email'])) {
            
            $this->load->model('account/customer');
            $customer_email = $this->request->post['email'];
            $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
            if (!empty($check_customer)) {
                $check_customer['custom_field'] = @unserialize($check_customer['custom_field']);
                $customer_id = $check_customer['customer_id'];
                $this->load->model('account/custom_field');
                if(!isset($setting_data['gender_field'])){
                    $setting_data['gender_field'] = '';
                }
                $custom_field = $this->model_account_custom_field->getCustomField($setting_data['gender_field']);
                if(!isset($custom_field['custom_field_id'])){
                      $custom_field['custom_field_id'] = '';
                }
                $custom_field_value = $this->model_webservices_checkout->getCustomFieldDetails($custom_field['custom_field_id']);
                $gender = array();
                foreach ($custom_field_value as $cfv){
                    $gender[] = array(
                            "id" => $cfv['custom_field_value_id'],
                            "name" => 'gender',
                            "label" => $cfv['name']
                        );
                }
                $query = $this->db->query("SELECT * FROM `".DB_PREFIX."kbmobileapp_unique_verification` where id_customer = '".$check_customer['customer_id']."'");
                if($query->num_rows > 0) {
                    $mobile_number = $query->row['mobile_number'];
                    $country_code = $query->row['country_code'];
                } else {
                    $mobile_number = "";
                    $country_code = "";
                }
                $data['personal_info'] = array(
                    'titles' => $gender,
                    'firstname' => $check_customer['firstname'],
                    'lastname' => $check_customer['lastname'],
                    'email' => $check_customer['email'],
                    'mobile_number' => $mobile_number,
                    'country_code' => $country_code,
                    'dob' => '',
                    'gender' => isset($check_customer['custom_field'][$setting_data['gender_field']]) ? $check_customer['custom_field'][$setting_data['gender_field']]:''
                );
                
            } else {
                $customer_id = 0;
            }
            $this->session->data['customer_id'] = $customer_id;
            $this->customer_id = $customer_id;
            $this->load->model('account/order');
            $this->load->model('webservices/image');
            $order_total = $this->model_webservices_checkout->getTotalOrders($customer_id);
            if (isset($this->request->post['page'])) {
                $page = $this->request->post['page'];
            } else {
                $page = 1;
            }
            $results = $this->model_webservices_checkout->getOrders($customer_id, ($page - 1) * 10, 10);
            foreach ($results as $result) {
                $json['products'] = array();
                $this->load->model('catalog/product');
                $this->load->model('tool/upload');
                $products = $this->model_account_order->getOrderProducts($result['order_id']);
                foreach ($products as $product) {
                    $option_data = array();

                    $options = $this->model_account_order->getOrderOptions($result['order_id'], $product['order_product_id']);

                    foreach ($options as $option) {
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

                    $product_info = $this->model_catalog_product->getProduct($product['product_id']);
                    if (isset($product_info['special'])) {
                        if($product_info['price']>0) {
                            $discount_price = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                            $act_price = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
                            $disc_price = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));
                            $discount_perc = (($act_price - $disc_price) / $act_price) * 100;
                            $discount_percentage = number_format($discount_perc, 2);
                        } else{
                            $product_info['price']=$product_info['special'];
                            $discount_price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                            $discount_percentage = 0;
                            
                        }
                    } else {
                        $discount_price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        $discount_percentage = 0;
                    }
                    if ($product_info['date_added'] >= $setting_data['new_product_start_date']) {
                        $is_new = 1;
                    } else {
                        $is_new = 0;
                    }
                    
                    $image = $this->model_webservices_image->resize($product_info['image'], 200, 200);
                    $json['products'][] = array(
                        'id' => $product['product_id'],
                        'title' => $product['name'],
                        'is_gift_product' => 0,
                        'id_product_attribute' => $product['order_product_id'],
                        'stock' => $product['quantity'] ? true : false,
                        'images' => $image,
                        'model' => $product['model'],
                        'customizable_items' => array(),
                        'discount_price' => $discount_price,
                        'discount_percentage' => $discount_percentage,
                        'product_items' => $option_data,
                        'quantity' => $product['quantity'],
                        'price' => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $result['currency_code'], $result['currency_value']),
                        'total' => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $result['currency_code'], $result['currency_value'])
                    );
                }

                $data['order_history'][] = array(
                    'order_id' => $result['order_id'],
                    'cart_id' => $result['order_id'],
                    'order_number' => $result['order_id'] ? $result['order_id'] : '',
                    'status_color' => '#FF69B4',
                    'reorder_allowed' => 1,
                    'firstname' => $check_customer['firstname'],
                    'lastname' => $check_customer['lastname'],
                    'email' => $check_customer['email'],
                    'telephone' => $result['telephone'],
                    'status' => $result['status'],
                    'date_added' => date(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('date_format_short')), strtotime($result['date_added'])),
                    'products' => $json['products'],
                    'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                );
            }
        }
        $data['status'] = 'success';
        $data['message'] = "";
        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfUserOrder", json_encode($data));
        $this->response->output();
        die;
    }

    public function appUpdateProfile() {
        
        $this->load->model('webservices/home');
        $this->load->language('account/password');
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
        
        $email = isset($this->request->post['email']) ? $this->request->post['email'] : "";
        $this->request->post = json_decode(str_replace('&quot;', '"', (trim($this->request->post['personal_info']))), true);
        if (!empty($email) && $this->validatePassword()) {
            if($this->customer->login($email, $this->request->post['password'])){
                $this->load->model('account/customer');
                $this->load->model('webservices/checkout');
                $customer_email = $email;
                $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
                if (!empty($check_customer)) {
                    $customer_id = $check_customer['customer_id'];
                    $this->session->data['customer_id'] = $customer_id;
                    $customer_data = $this->request->post;
                    if(isset($setting_data['gender_field'])){
                        $customer_data['custom_field'][$setting_data['gender_field']] = $customer_data['title'];
                    }else{
                        $customer_data['custom_field'] = array();
                    }
                    $this->model_webservices_checkout->updateCustomerData($customer_data, $customer_id);
                    $data['status'] = 'success';
                    $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get("text_customer_update"));
                } else {
                    $data['status'] = 'failure';
                    $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get("error_email_empty"));
                }
            }else{
                $data['status'] = 'failure';
                $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get("error_password"));
            }
        } else {
            if (isset($this->error['password'])) {
                $data['message'] = $this->error['password'];
            } else
            if (isset($this->error['confirm'])) {
                $data['message'] = $this->error['confirm'];
            } else {
                $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get("error_email_empty"));
            }
            $data['status'] = 'failure';
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

    public function appGetOrderDetails() {
        
        $this->load->model('webservices/home');
        $this->load->model('webservices/checkout');
        $this->load->language('webservices/config');
        $data['order_details'] = array();
        $data['order_details']['gift_wrapping'] = array(
            "applied" => "0",
            "available" => "0",
            "cost_text" => "",
            "message" => ""
        );
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfOrder", json_encode($data));
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

            $this->load->model('checkout/order');
            $this->load->model('account/order');
            $this->load->model('webservices/checkout');
            $this->load->model('webservices/image');
            $this->load->model('catalog/product');
            $this->load->model('tool/upload');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            if ($order_info) {
                $data['order_details']['order_history'] = array(
                    'order_id' => $order_info['order_id'],
                    'cart_id' => $order_info['order_id'],
                    'status_color' => '#FF69B4',
                    'order_number' => $order_info['order_id'],
                    'status' => $order_info['order_status'],
                    'date_added' => $order_info['date_added'],
                    'reorder_allowed' => 1,
                    'total' => $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']),
                );
                $data['order_details']['shipping_address'] = array(
                    'firstname' => $order_info['shipping_firstname'],
                    'lastname' => $order_info['shipping_lastname'],
                    'company' => $order_info['shipping_company'],
                    'mobile_no' => $order_info['telephone'],
                    'address_1' => $order_info['shipping_address_1'],
                    'address_2' => $order_info['shipping_address_2'],
                    'city' => $order_info['shipping_city'],
                    'state' => $order_info['shipping_zone'],
                    'country' => $order_info['shipping_country'],
                    'postcode' => $order_info['shipping_postcode'],
                    'alias' => ''
                );
                $data['order_details']['billing_address'] = array(
                    'firstname' => $order_info['payment_firstname'],
                    'lastname' => $order_info['payment_lastname'],
                    'company' => $order_info['payment_company'],
                    'mobile_no' => $order_info['telephone'],
                    'address_1' => $order_info['payment_address_1'],
                    'address_2' => $order_info['payment_address_2'],
                    'city' => $order_info['payment_city'],
                    'state' => $order_info['payment_zone'],
                    'country' => $order_info['payment_country'],
                    'postcode' => $order_info['payment_postcode'],
                    'alias' => ''
                );

                if (isset($order_info['comment']) && $order_info['comment'] != "") {
                    $data['order_details']['order_comment'] = $order_info['comment'];
                } else {
                    $data['order_details']['order_comment'] = "";
                }

                $products = $this->model_account_order->getOrderProducts($order_info['order_id']);
                foreach ($products as $product) {
                    $option_data = array();

                    $options = $this->model_account_order->getOrderOptions($order_info['order_id'], $product['order_product_id']);

                    foreach ($options as $option) {
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
                    $option_data[] = array(
                        'name' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get("app_text_quantity")),
                        'value' => $product['quantity']
                    );
                    $product_info = $this->model_catalog_product->getProduct($product['product_id']);
                    if (isset($product_info['special'])) {
                        if($product_info['price']>0) {
                            $discount_price = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                            $act_price = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
                            $disc_price = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));
                            $discount_perc = (($act_price - $disc_price) / $act_price) * 100;
                            $discount_percentage = number_format($discount_perc, 2);
                        } else{
                            $product_info['price']=$product_info['special'];
                            $discount_price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                            $discount_percentage = 0;
                            
                        }
                    } else {
                        $discount_price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        $discount_percentage = 0;
                    }
                    $image  = $this->model_webservices_image->resize($product_info['image'], 200, 200);
                    
                    $data['order_details']['products'][] = array(
                        'id' => $product['product_id'],
                        'quantity' => $product['quantity'],
                        'id_product_attribute' => $product['order_product_id'],
                        'title' => $product['name'],
                        'is_gift_product' => 0,
                        'discount_price' => $discount_price,
                        'discount_percentage' => $discount_percentage,
                        'stock' => $product['quantity'] ? true : false,
                        'images' => $image,
                        'model' => $product['model'],
                        'customizable_items' => array(),
                        'product_items' => $option_data,
                        'price' => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                        'total' => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
                    );
                }
                $data['order_details']['shipping_method'] = array(
                    'name' => $order_info['shipping_method']);
                $data['order_details']['payment_method'] = array(
                    'name' => $order_info['payment_code']);

                $histories = $this->model_webservices_checkout->getOrderHistory($this->request->post['order_id']);
                foreach ($histories as $history) {
                    $data['order_details']['status_history'][] = array(
                        'id' => $history['order_history_id'],
                        'comment' => $history['comment'],
                        'history_date' => $history['date_added'],
                        'notify' => $history['notify'],
                        'order_status' => $history['status'],
                        'status_color' => "#FF69B4"
                    );
                }

                $totals = $this->model_account_order->getOrderTotals($this->request->post['order_id']);

                foreach ($totals as $total) {
                    $data['order_details']['total'][] = array(
                        'name' => strip_tags(html_entity_decode($total['title'])),
                        'value' => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
                    );
                }
                $data['order_details']['vouchers'] = array();
                $data['status'] = 'success';
                $data['message'] = '';
            }
        }

        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfOrder", json_encode($data));
        $this->response->output();
        die;
    }

    public function appGetCustomerAddress() {

        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("appGetCustomerAddress", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }
        $default_id = 0;
        $id = 0;
        $data['shipping_address'] = array();
        if (!empty($this->request->post['email'])) {
            $this->load->model('account/customer');
            $customer_email = $this->request->post['email'];
            $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
            if (!empty($check_customer)) {
                $customer_id = $check_customer['customer_id'];
                $this->session->data['customer_id'] = $customer_id;
                $this->load->model('account/address');
                $this->load->model('webservices/checkout');

                $default_id = $check_customer['address_id'];
                $addresses = $this->model_webservices_checkout->getAddresses($customer_id);
                if(!isset($setting_data['phone_field'])){
                    $setting_data['phone_field'] = '';
                }
                foreach ($addresses as $address) {
                    if(!empty($address['firstname'])) {
                        $data['shipping_address'][] = array(
                            'id_shipping_address' => $address['address_id'],
                            'firstname' => $address['firstname'],
                            'lastname' => $address['lastname'],
                            'mobile_no' => isset($address['custom_field'][$setting_data['phone_field']]) ? $address['custom_field'][$setting_data['phone_field']]:'',
                            'company' => $address['company'],
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
        }

        $data['default_address'] = $default_id;

        $this->model_webservices_home->logKnowbandAppRequest("appGetCustomerAddress", json_encode($data));
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfCarts", json_encode($data));
        $this->response->output();
        die;
    }

    public function appGetRegistrationForm() {
        
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("getShippingAddress", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }

        $data['signup_details'] = array(
                    'titles' => array(
                        array(
                            "id" => '1',
                            "name" => 'gender',
                            "label" => 'Mr.'
                        ),
                        array(
                            "id" => '2',
                            "name" => 'gender',
                            "label" => 'Mrs.'
                        )
                    ),
                    'firstname' => '',
                    'lastname' => '',
                    'email' => '',
                    'password' => '',
                    'dob' => '',
        );

        $this->model_webservices_home->logKnowbandAppRequest("getShippingAddress", json_encode($data));
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfCarts", json_encode($data));
        $this->response->output();
        die;
    }

    public function appGetAddressForm() {
        
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $this->load->language('account/register');
        $setting_data = $this->config->get('webservice');
        //$abc = '{"email":"alath@velsof.com","customer_email":"alath@velsof.com","iso_code":"en","id_currency":"GBP","id_shipping_address":"13","user_type":""}';
        //$this->request->post = json_decode($abc, TRUE);print_r($this->request->post);
        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("getShippingAddress", json_encode($data));
            die;
        } else {
            $data['install_module'] = '';
        }
        $default_id = 0;
        $id = 0;
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
                    $this->session->data['payment_address'] = $address;
                    $this->session->data['payment_address']['country_id'] = $address['country_id'];
                    $this->session->data['payment_address']['zone_id'] = $address['zone_id'];
                }
            }
        }
        if(isset($this->request->post['id_shipping_address']) && $this->request->post['id_shipping_address'] > 0){
            $addresses = $this->model_webservices_checkout->getAddress($this->request->post['id_shipping_address']);
            $add = $addresses[$this->request->post['id_shipping_address']];
        }
        $data['default_country_id'] = isset($this->session->data['payment_address']['country_id']) ? $this->session->data['payment_address']['country_id'] : $default_id;
        $data['countries'] = $this->getAllCountries();
        if(!isset($setting_data['phone_field'])){
            $data['shipping_address_items'] = array(
                array(
                    'label' => $this->language->get('entry_firstname'),
                    'type' => 'textfield',
                    'name' => 'firstname',
                    'value' => isset($add['firstname']) ? $add['firstname'] : '',
                    'required' => '1',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_lastname'),
                    'type' => 'textfield',
                    'name' => 'lastname',
                    'value' => isset($add['lastname']) ? $add['lastname'] : '',
                    'required' => '1',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_company'),
                    'type' => 'textfield',
                    'name' => 'company',
                    'value' => isset($add['company']) ? $add['company'] : '',
                    'required' => '0',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_address_1'),
                    'type' => 'textfield',
                    'name' => 'address_1',
                    'value' => isset($add['address_1']) ? $add['address_1'] : '',
                    'required' => '1',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_address_2'),
                    'type' => 'textfield',
                    'name' => 'address_2',
                    'value' => isset($add['address_2']) ? $add['address_2'] : '',
                    'required' => '0',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_city'),
                    'type' => 'textfield',
                    'name' => 'city',
                    'value' => isset($add['city']) ? $add['city'] : '',
                    'required' => '1',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_postcode'),
                    'type' => 'textfield',
                    'name' => 'postcode',
                    'value' => isset($add['postcode']) ? $add['postcode'] : '',
                    'required' => '1',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_country'),
                    'type' => 'dropdownfield',
                    'name' => 'country',
                    'value' => isset($add['country_id']) ? $add['country_id'] : '',
                    'required' => '1',
                    'validation' => '',
                    'group_items' => array(),
                ),
            );

        } else {
            $data['shipping_address_items'] = array(
                array(
                    'label' => $this->language->get('entry_firstname'),
                    'type' => 'textfield',
                    'name' => 'firstname',
                    'value' => isset($add['firstname']) ? $add['firstname'] : '',
                    'required' => '1',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_lastname'),
                    'type' => 'textfield',
                    'name' => 'lastname',
                    'value' => isset($add['lastname']) ? $add['lastname'] : '',
                    'required' => '1',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_company'),
                    'type' => 'textfield',
                    'name' => 'company',
                    'value' => isset($add['company']) ? $add['company'] : '',
                    'required' => '0',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_address_1'),
                    'type' => 'textfield',
                    'name' => 'address_1',
                    'value' => isset($add['address_1']) ? $add['address_1'] : '',
                    'required' => '1',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_address_2'),
                    'type' => 'textfield',
                    'name' => 'address_2',
                    'value' => isset($add['address_2']) ? $add['address_2'] : '',
                    'required' => '0',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_city'),
                    'type' => 'textfield',
                    'name' => 'city',
                    'value' => isset($add['city']) ? $add['city'] : '',
                    'required' => '1',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_postcode'),
                    'type' => 'textfield',
                    'name' => 'postcode',
                    'value' => isset($add['postcode']) ? $add['postcode'] : '',
                    'required' => '1',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_country'),
                    'type' => 'dropdownfield',
                    'name' => 'country',
                    'value' => isset($add['country_id']) ? $add['country_id'] : '',
                    'required' => '1',
                    'validation' => '',
                    'group_items' => array(),
                ),
                array(
                    'label' => $this->language->get('entry_telephone'),
                    'type' => 'textfield',
                    'name' => 'phone',
                    'value' => isset($add['custom_field'][$setting_data['phone_field']]) ? $add['custom_field'][$setting_data['phone_field']]:'',
                    'required' => '1',
                    'validation' => '',
                    'group_items' => array(),
                ),
            );

        }

        $this->model_webservices_home->logKnowbandAppRequest("getShippingAddress", json_encode($data));
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfCarts", json_encode($data));
        $this->response->output();
        die;
    }

    public function getAllCountries() {

        $this->load->language('webservices/config');
        $this->load->model('localisation/country');
        $countries = $this->model_localisation_country->getCountries();
        //Model
        $this->load->model('localisation/zone');
        foreach ($countries as $country) {
            $data[] = array(
                'id' => $country['country_id'],
                'name' => $country['name'],
            );
        }
        return $data;
    }

    public function appGetRegions() {

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
            $this->load->model('localisation/country');
            $country = $this->model_localisation_country->getCountry($country_id);
            $data['zipcode_required'] = $country['postcode_required'];
            $data['dni_required'] = 0;
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
        $data['zipcode_required'] = '1';
        $data['dni_required'] = '0';
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

    public function appAddAddress() {
        $this->load->language('webservices/config');
        $this->load->model('webservices/home');

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
            $this->request->post['shipping_address'] = json_decode(str_replace("\\", "", str_replace('&quot;', '"', (trim($this->request->post['shipping_address'])))), true);
        }
        if (!empty($this->request->post['user_email']) && $this->validateAddress()) {
            $this->load->model('account/customer');
            $customer_email = $this->request->post['user_email'];
            $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
            if (!empty($check_customer)) {
                $customer_id = $check_customer['customer_id'];
                $this->session->data['customer_id'] = $customer_id;
                $this->load->model('webservices/checkout');
                $shipping_addr = $this->request->post['shipping_address'];
                if(isset($setting_data['phone_field'])){
                    $shipping_addr['custom_field'][$setting_data['phone_field']] = $shipping_addr['phone'];
                }else{
                    $shipping_addr['custom_field'] = array();
                }
                $address_id = $this->model_webservices_checkout->addAddress($customer_id, $shipping_addr);
                if (isset($address_id)) {
                    $data['shipping_address_reponse'] = array(
                        "status" => "success",
                        "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get("text_shipping_address_success"))
                    );
                    $data['cart_id'] = $this->session->getId();
                    $addresses = $this->model_webservices_checkout->getAddresses($customer_id);
                    $data['shipping_address_count'] = isset($addresses) ? count($addresses) : 0;
                    $data['id_shipping_address'] = $address_id;
                } else {
                    $data['shipping_address_reponse'] = array(
                        "status" => "failure",
                        "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get("text_shipping_address_issue"))
                    );
                }
            } else {
                $data['shipping_address_reponse'] = array(
                    "status" => "failure",
                    "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get("text_shipping_address_issue"))
                );
            }
        } else {
            if (isset($this->error['address'])) {
                $data['shipping_address_reponse'] = array(
                    "status" => "failure",
                    "message" => iconv("UTF-8","UTF-8//IGNORE",$this->error['address'])
                );
            } else {
                $data['shipping_address_reponse'] = array(
                    "status" => "failure",
                    "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get("text_shipping_address_issue"))
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
        $this->model_webservices_home->logKnowbandAppRequest("addDetailsOfShippingAddress", json_encode($data));
        $this->response->output();
        die;
    }

    public function appUpdateAddress() {
        
        $this->load->language('webservices/config');
        $this->load->model('webservices/home');

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
        //$abc = '{"shipping_address":"{&quot;firstname&quot;:&quot;Sachin 2&quot;,&quot;lastname&quot;:&quot;Rana&quot;,&quot;company&quot;:&quot;jsjs&quot;,&quot;address_1&quot;:&quot;hdjdj&quot;,&quot;address_2&quot;:&quot;hsj&quot;,&quot;city&quot;:&quot;jsjdj&quot;,&quot;postcode&quot;:&quot;12345&quot;,&quot;country&quot;:&quot;239&quot;,&quot;phone&quot;:&quot;121212&quot;,&quot;dni&quot;:&quot;&quot;,&quot;state&quot;:&quot;3832&quot;}","email":"alath@velsof.com","iso_code":"en","user_email":"alath@velsof.com","id_currency":"GBP","id_shipping_address":"13","session_data":"1f17mgs0ld6hie0hp66u9mpui1"}';
        //$this->request->post = json_decode($abc, true);
        
        if (!empty($this->request->post['id_shipping_address'])) {
            $this->load->model('account/customer');
            $customer_email = $this->request->post['user_email'];
            $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
            if (!empty($check_customer)) {
                $customer_id = $check_customer['customer_id'];
                $this->session->data['customer_id'] = $customer_id;
                $this->customer_id = $customer_id;
                $this->load->model('webservices/checkout');
                $shipping_addr = json_decode(str_replace('&quot;', '"', (trim($this->request->post['shipping_address']))), true);
                if(isset($setting_data['phone_field'])){
                    $shipping_addr['custom_field'][$setting_data['phone_field']] = $shipping_addr['phone'];
                }else{
                    $shipping_addr['custom_field'] = array();
                }
                $this->request->post['shipping_address'] = json_decode(str_replace('&quot;', '"', (trim($this->request->post['shipping_address']))), true);
                if($this->validateAddress()){
                    $this->model_webservices_checkout->editAddress($this->request->post['id_shipping_address'], $customer_id, $shipping_addr);
                    $data['shipping_address_reponse'] = array(
                        "status" => "success",
                        "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get("text_shipping_address_success"))
                    );
                    $data['cart_id'] = $this->session->getId();
                    $addresses = $this->model_webservices_checkout->getAddresses($customer_id);
                    $data['shipping_address_count'] = isset($addresses) ? count($addresses) : 0;
                    $data['id_shipping_address'] = $this->request->post['id_shipping_address'];
                }else{
                    if (isset($this->error['address'])) {
                        $data['shipping_address_reponse'] = array(
                            "status" => "failure",
                            "message" => $this->error['address']
                        );
                    }else{
                        $data['shipping_address_reponse'] = array(
                            "status" => "failure",
                            "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get("text_shipping_address_issue"))
                        );
                    }
                }
            } else {
                $data['shipping_address_reponse'] = array(
                    "status" => "failure",
                    "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get("text_shipping_address_issue"))
                );
            }
        } else {
            $data['shipping_address_reponse'] = array(
                "status" => "failure",
                "message" => iconv("UTF-8","UTF-8//IGNORE",$this->language->get("text_shipping_address_issue"))
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

    public function validateSocialLogin() {
        $this->load->model('account/customer');
        $this->load->language('webservices/config');
        if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
            $this->error['firstname'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_firstname'));
        }

        if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
            $this->error['lastname'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_lastname'));
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
            $this->error['email'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_email'));
        }

        if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $this->error['warning'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_exists'));
        }

        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
            $this->error['telephone'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_telephone'));
        }

        if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
            $this->error['address_1'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_address_1'));
        }

        if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
            $this->error['city'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_city'));
        }

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

        if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
            $this->error['postcode'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_postcode'));
        }

        if ($this->request->post['country_id'] == '') {
            $this->error['country'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_country'));
        }

        if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
            $this->error['zone'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_zone'));
        }

        // Customer Group
        if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
            $customer_group_id = $this->request->post['customer_group_id'];
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        // Custom field validation
        $this->load->model('account/custom_field');

        $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

        foreach ($custom_fields as $custom_field) {
            if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
                $this->error['custom_field'][$custom_field['custom_field_id']] = sprintf(iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_custom_field')), $custom_field['name']);
            }
        }

        if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
            $this->error['password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_password'));
        }

        if ($this->request->post['confirm'] != $this->request->post['password']) {
            $this->error['confirm'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_confirm'));
        }
        return !$this->error;
    }

    public function appGuestRegistration() {
        
        $this->load->model('webservices/home');
        $this->load->model('account/customer');
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


        if (isset($this->request->post['email'])) {
            $this->request->post['user_type'] = 'guest';
            if (isset($this->request->post) && $this->validate()) {
                if (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
                    $customer_data = array(
                        'firstname' => '',
                        'lastname' => '',
                        'company' => '',
                        'telephone' => '',
                        'fax' => '',
                        'address_1' => '',
                        'address_2' => '',
                        'postcode' => '',
                        'city' => '',
                        'zone_id' => 0,
                        'country_id' => 0,
                        'email' => $this->request->post['email'],
                        'password' => "",
                    );
                    $this->model_account_customer->addCustomer($customer_data);
                }
                // Clear any previous login attempts for unregistered accounts.
                if (version_compare(VERSION, '2.0.1.0', '>=')) {
                    $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
                }                			
                $this->customer->login($this->request->post['email'], "", true);

                unset($this->session->data['guest']);

                // Add to activity log
                $this->load->model('account/activity');

                $activity_data = array(
                    'customer_id' => $this->customer->getId(),
                    'name' => "" . ' ' . ""
                );
                
                $this->model_account_activity->addActivity('register', $activity_data);
                
                $this->load->model('webservices/wishlist');
                $results = $this->model_webservices_wishlist->getWishlist($this->session->data['customer_id']);
                $cart_data = array();
                $cart_data_count = 0;

                if (version_compare(VERSION, '2.1.0.1', '>=')) {
                    
                    // Remove all the expired carts with no customer ID
                    $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND date_added < DATE_SUB(NOW(), INTERVAL 1 HOUR)");

                    if ($this->customer->getId()) {
                        // We want to change the session ID on all the old items in the customers cart
                        
                        
                        $this->db->query("UPDATE " . DB_PREFIX . "cart SET session_id = '" . $this->db->escape($this->session->getId()) . "' WHERE customer_id = '" . (int) $this->customer->getId() . "'");

                        // Once the customer is logged in we want to update the customer ID on all items he has
                        $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '0' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

                        foreach ($cart_query->rows as $cart) {
                            $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int) $cart['cart_id'] . "'");

                            // The advantage of using $this->add is that it will check if the products already exist and increaser the quantity if necessary.
                            $this->cart->add($cart['product_id'], $cart['quantity'], json_decode($cart['option']), $cart['recurring_id']);
                        }
                    }

                    $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int) $this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
                    
                    if ($cart_query->num_rows > 0) {
                        foreach ($cart_query->rows as $cart_query_row) {
                            $cart_data[$cart_query_row['cart_id']] = (int) $cart_query_row['quantity'];
                            $cart_data_count += (int) $cart_query_row['quantity'];
                        }
                    }
                } else {
                    if (isset($this->session->data['cart'])) {
                        $cart_data = $this->session->data['cart'];
                        foreach ($cart_data as $key => $value) {
                            $cart_data_count += $value;
                        }
                    }
                }
                $data['status'] = 'success';
                $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get("text_customer_created"));
            } else {
                $data['status'] = 'failure';
                $data['message'] = $this->error;
            }
        } else {
            $data['status'] = 'failure';
            $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get("text_wrong"));
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

    protected function validatePassword() {
        $this->load->language('webservices/config');
        if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
            $this->error['password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_password'));
        }
        
        if (utf8_strlen($this->request->post['new_password']) > 0 && ((utf8_strlen($this->request->post['new_password']) < 4) || (utf8_strlen($this->request->post['new_password']) > 20))) {
            $this->error['password'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_password'));
        }

        if (utf8_strlen($this->request->post['new_password']) > 0 && ($this->request->post['cnfrm_password'] != $this->request->post['cnfrm_password'])) {
            $this->error['confirm'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_confirm'));
        }

        return !$this->error;
    }
    
    
    protected function validateAddress() {
        $this->load->model('localisation/country');
        $this->load->language('account/register');
        $this->load->language('webservices/config');
        $country_info = $this->model_localisation_country->getCountry($this->request->post['shipping_address']['country']);
        if ((utf8_strlen($this->request->post['shipping_address']['firstname']) < 1) || (utf8_strlen($this->request->post['shipping_address']['firstname']) > 32)) {
                $this->error['address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_firstname'));
        }

        elseif ((utf8_strlen($this->request->post['shipping_address']['lastname']) < 1) || (utf8_strlen($this->request->post['shipping_address']['lastname']) > 32)) {
                $this->error['address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_lastname'));
        }

        elseif ((utf8_strlen($this->request->post['shipping_address']['address_1']) < 3) || (utf8_strlen($this->request->post['shipping_address']['address_1']) > 128)) {
                $this->error['address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_address_1'));
        }

        elseif ((utf8_strlen($this->request->post['shipping_address']['city']) < 2) || (utf8_strlen($this->request->post['shipping_address']['city']) > 128)) {
                $this->error['address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_city'));
        }

        elseif ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['shipping_address']['postcode']) < 2 || utf8_strlen($this->request->post['shipping_address']['postcode']) > 10)) {
                $this->error['address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_postcode'));
        }

        elseif ($this->request->post['shipping_address']['country'] == '') {
                $this->error['address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_country'));
        }
//
//        elseif (!isset($this->request->post['shipping_address']['state']) || $this->request->post['shipping_address']['state'] == '') {
//                $this->error['address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_zone'));
//        }
        
//        elseif (isset($this->request->post['shipping_address']['phone']) && (utf8_strlen($this->request->post['shipping_address']['phone']) < 3 || utf8_strlen($this->request->post['shipping_address']['phone'])) > 15) {
//                $this->error['address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_phone1'));
//        }
//        
//        elseif(isset($this->request->post['shipping_address']['phone']) && (!preg_match("/^[0-9-]+$/", $this->request->post['shipping_address']['phone']))){
//                $this->error['address'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('error_phone2'));
//        }
    
        return !$this->error;
        
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
