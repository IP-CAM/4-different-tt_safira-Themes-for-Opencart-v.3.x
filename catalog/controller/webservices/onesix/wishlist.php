<?php

class ControllerWebservicesOneSixWishlist extends Controller {

    public function appAddToWishlist() {

        $json = array();
        $setting_data = $this->config->get('webservice');
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        
        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            $this->model_webservices_home->logKnowbandAppRequest("addProductToWishlist", json_encode($json));
            die;
        } else {
            $json['install_module'] = '';
        }

        if (!empty($this->request->post['email']) && $this->request->post['product_id']) {
            $this->load->model('account/customer');
            $this->load->model('webservices/wishlist');
            $customer_email = $this->request->post['email'];
            $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
            if (!empty($check_customer)) {
                $customer_id = $check_customer['customer_id'];
                $this->session->data['customer_id'] = $customer_id;
                $this->customer_id = $customer_id;
                $results = $this->model_webservices_wishlist->AddWishlist($customer_id, $this->request->post['product_id']);
                if (!empty($results)) {
                    $json['status'] = 'success';
                    $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_wishlist_added'));
                    $count_results = $this->model_webservices_wishlist->getWishlist($this->session->data['customer_id']);
                    $json['wishlist_count'] = count($count_results);
                } else {
                    $json['status'] = 'failure';
                    $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_wishlist_not_added'));
                    $count_results = $this->model_webservices_wishlist->getWishlist($this->session->data['customer_id']);
                    $json['wishlist_count'] = count($count_results);
                }
            }
        } else {
            $json['status'] = 'failure';
            $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_wishlist_not_added'));
            $json['wishlist_count'] = 0;
        }
        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("addProductToWishlist", json_encode($json));
        $this->response->output();
        die;
    }

    public function appRemoveWishlist() {

        $json = array();
        $this->load->model('webservices/home');
        $setting_data = $this->config->get('webservice');
        $this->load->language('webservices/config');
        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            $this->model_webservices_home->logKnowbandAppRequest("deleteProductToWishlist", json_encode($json));
            die;
        } else {
            $json['install_module'] = '';
        }

        if (!empty($this->request->post['email']) && $this->request->post['product_id']) {
            $this->load->model('account/customer');
            $this->load->model('webservices/wishlist');
            $customer_email = $this->request->post['email'];
            $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
            if (!empty($check_customer)) {
                $customer_id = $check_customer['customer_id'];
                $this->session->data['customer_id'] = $customer_id;
                $this->customer_id = $customer_id;
                $results = $this->model_webservices_wishlist->UpdateWishlist($customer_id, $this->request->post['product_id']);
                $json['status'] = 'success';
                $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_wishlist_removed'));
                $count_results = $this->model_webservices_wishlist->getWishlist($this->session->data['customer_id']);
                $json['wishlist_count'] = count($count_results);
            }
        } else {
            $json['status'] = 'failure';
            $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_wishlist_not_added'));
            $json['wishlist_count'] = 0;
        }

        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("deleteProductToWishlist", json_encode($json));
        $this->response->output();
        die;
    }

    public function appGetWishlist() {

        $data['wishlist_products'] = array();
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $data['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($data);
            $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfWishlist", json_encode($data));

            die;
        } else {
            $data['install_module'] = '';
        }
        $data['status'] = 'failure';
        $data['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_no_email'));
        if (!empty($this->request->post['email'])) {
            $this->load->model('account/customer');
            $this->load->model('webservices/wishlist');
            $this->load->model('catalog/product');
            $this->load->model('webservices/image');
            $this->load->language('webservices/config');
            $this->load->language('product/product');
            $customer_email = $this->request->post['email'];
            $check_customer = $this->model_account_customer->getCustomerByEmail($customer_email);
            if (!empty($check_customer)) {
                $customer_id = $check_customer['customer_id'];
                $this->session->data['customer_id'] = $customer_id;
                $this->customer_id = $customer_id;
                $results = $this->model_webservices_wishlist->getWishlist($customer_id);
                $width = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_width'));
                $height = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_height'));
                if (!empty($results)) {
                    foreach ($results as $result) {
                        $product_info = $this->model_catalog_product->getProduct($result);

                        if ($product_info) {
                            if ($product_info['image']) {
                                $image = $this->model_webservices_image->resize($product_info['image'], $width, $height);
                            } else {
                                $image = false;
                            }
                            
                            if ($product_info['quantity'] <= 0) {
                                $stock = $product_info['stock_status'];
                            } elseif ($this->config->get('config_stock_display')) {
                                $stock = $product_info['quantity'];
                            } else {
                                $stock = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_instock'));
                            }

                            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                                $total = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                            } else {
                                $total = false;
                            }

                            if(isset($product_info['special'])){
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
                            
                            $data['wishlist_products'][] = array(
                                'product_id' => $product_info['product_id'],
                                'title' => str_replace('&quot;', '"', $product_info['name']),
                                'is_gift_product' => '0',
                                'id_product_attribute' => 0,
                                'id_address_delivery' => 0,
                                'stock' => $product_info['quantity'] ? true : false,
                                'discount_price' => $discount_price,
                                'discount_percentage' => $discount_percentage,
                                'images' => $image,
                                'price' => $total,
                                'allow_out_of_stock' => 0,
                                'available_for_order' => 1,
                                'minimal_quantity' => $product_info['minimum'],
                                'quantity' => $product_info['quantity'],
                                'product_items' => array(),
                                'customizable_items' => array()
                            );
                            $data['status'] = 'success';
                            $data['message'] = '';
                        } else {

                            $this->model_account_wishlist->deleteWishlist($result);

                        }
                    }
                } else {
                    $data['status'] = 'success';
                    $data['message'] = '';
                }
            }
        }
        
        $data['status'] = 'success';
        $data['message'] = '';

        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($data));
        $this->model_webservices_home->logKnowbandAppRequest("getDetailsOfWishlist", json_encode($data));
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
