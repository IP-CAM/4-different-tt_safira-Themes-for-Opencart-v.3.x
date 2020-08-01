<?php

class ControllerWebServicesOneSixProduct extends Controller {

    //Function definition to get product details
    public function appGetProductDetails() {
        $this->load->language('product/product');
        //Response Array
        $json = array(
            'product' => '',
            'fproducts' => ''
        );

        $setting_data = $this->config->get('webservice');
        $marketplace_setting = $this->config->get('kbmp_marketplace_setting');
       
       
        $this->load->model('webservices/home');

        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            $this->model_webservices_home->logKnowbandAppRequest("getProductDetails", json_encode($json));
            die;
        } else {
            $json['install_module'] = '';
        }

        //Load models
        $this->load->model('catalog/product');
        $this->load->model('webservices/category');
        $this->load->model('webservices/image');

        $this->load->language('webservices/config');

        $module_info = $this->config->get('webservice');
        if (!empty($module_info)) {
            $getModulesSettingArray = $module_info;
            $width = $getModulesSettingArray['product_image_width'];
            $height = $getModulesSettingArray['product_image_height'];
        } else {
            $width = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_width'));
            $height = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_height'));
        }

        //$this->request->post['product_id'] = '42';
        if (!empty($this->request->post['product_id'])) {
            $productID = $this->request->post['product_id'];
            
            
            $json['product']= array();
            
            //Get product Information
            $productInfo = $this->model_catalog_product->getProduct($productID);
            $json['product']['id_product'] = $productID;
            $json['product']['quantity'] = $productInfo['quantity'];
            $json['product']['allow_out_of_stock'] = 0;
            $json['product']['name'] = html_entity_decode(str_replace('&quot;', '"', $productInfo['name']));
            $json['product']['price'] = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            if (isset($productInfo['special'])) {
                if($productInfo['price']>0) {
                    $json['product']['discount_price'] = $this->currency->format($this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $act_price = $this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                    $disc_price = $this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax'));
                    $discount_perc = (($act_price - $disc_price) / $act_price) * 100;
                    $json['product']['discount_percentage'] = number_format($discount_perc, 2);
                } else{
                    $json['product']['discount_price'] = $this->currency->format($this->tax->calculate($productInfo['special'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $json['product']['discount_price'] = '';
                    $json['product']['discount_percentage'] = '';
                }
            } else {
                $json['product']['discount_price'] = '';
                $json['product']['discount_percentage'] = '';
            }
            $json['product']['minimal_quantity'] = $productInfo['minimum'];

            $image = $this->model_webservices_image->resize($productInfo['image'], $width, $height);
            if (!file_exists(DIR_IMAGE . $productInfo['image'])) {
                $image = $this->model_webservices_image->resize('no_image.gif', $width, $height);
            }
            //Check if webp image exists
            $info = pathinfo($image);
            if (isset($info['extension'])) {
                $directory = str_replace(HTTP_SERVER . 'image/cache/', DIR_IMAGE . 'cache/', $info['dirname']);
                if (file_exists($directory . '/' . $info['filename'] . '.webp')) {
                    $image = $info['dirname'] . '/' . $info['filename'] . '.webp';
                }
            }
            
            $json['product']['images'][0]['src'] = str_replace(" ","%20",$image);

            //Additional Images
            $ProductImages = $this->model_catalog_product->getProductImages($productID);
            if (!empty($ProductImages)) {
                $i = 1;
                foreach ($ProductImages as $Images) {
                    $image = $this->model_webservices_image->resize($Images['image'], $width, $height);
                    $json['product']['images'][$i]['src'] = str_replace(" ","%20",$image);
                    $i++;
                }
            }
            //Get Product Options
            $json['product']['options'] = array();
            $productOption = $this->model_catalog_product->getProductOptions($productID);
            if (!empty($productOption)&& is_array($productOption)) {
                foreach ($productOption as $productOption) {
                    $items = array();
                    if (!empty($productOption['product_option_value']) && is_array($productOption['product_option_value'])) {
                        foreach ($productOption['product_option_value'] as $productOptionValue) {
			    if($productOptionValue['quantity'] <= 0){
                                continue;
                            }
                            if ($productOptionValue['price'] > 0) {
                                $items[] = array(
                                    'id' => $productOptionValue['product_option_value_id'],
                                    'value' => html_entity_decode($productOptionValue['name'] . " (" . $productOptionValue['price_prefix'] . $this->currency->format($this->tax->calculate($productOptionValue['price'], $productInfo['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']) . ")")
                                );
                            } else {
                                $items[] = array(
                                    'id' => $productOptionValue['product_option_value_id'],
                                    'value' => html_entity_decode($productOptionValue['name'])
                                );
                            }
                        }
                        $json['product']['options'][] = array(
                            'title' => html_entity_decode($productOption['name']),
                            'id' => $productOption['product_option_id'],
                            'items' => $items
                        );
                    }
                }
            }
	    $json['product']['is_in_wishlist'] = $this->isInWishlist($productID);
            $json['product']['product_url'] = $this->url->link('product/product', 'product_id=' . $productID,'','true');
            //Get Product Description
//            $json['product']['description'] = htmlspecialchars($productInfo['description']);
            //$json['product']['description'] = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($productInfo['description']))))));
            $clear = strip_tags($productInfo['description']);
            // Clean up things like &amp;
            $clear = html_entity_decode($clear);
            // Strip out any url-encoded stuff
            $clear = urldecode($clear);
            // Replace non-AlNum characters with space
            //$clear = preg_replace('/[^A-Za-z0-9]/', ' ', $clear);
            // Replace Multiple spaces with single space
            $clear = preg_replace('/ +/', ' ', $clear);
            // Trim the string of leading/trailing space
            $clear = trim($clear);
            $json['product']['description'] = $clear;
            //Get Product Attributes
            $json['product']['product_info'] = array();
            
            //Product Model
            $json['product']['product_info'][0] = array(
                'name' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_model')),
                'value' => $productInfo['model']
            );
            
            //Minimum Quantity
            $json['product']['product_info'][1] = array(
                'name' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_minimal_quantity')),
                'value' => $productInfo['minimum']
            );
            //Brand
            $json['product']['product_info'][2] = array(
                'name' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_brand')),
                'value' => $productInfo['manufacturer']
            );
            
            // PRODUCT ATTRIBUTES
            $productAttribute = $this->model_catalog_product->getProductAttributes($productID);
            if (!empty($productAttribute)) {
                $counter = 3;
                foreach ($productAttribute as $productAttribute) {
                    if (!empty($productAttribute['attribute'])) {
                        foreach ($productAttribute['attribute'] as $attribuiteValue) {
                            $json['product']['product_info'][$counter] = array(
                                'name' => html_entity_decode($attribuiteValue['name']),
                                'value' => $attribuiteValue['text']
                            );
                            $counter++;
                        }
                    }
                }
            }
            
            if($this->config->get('kbmp_marketplace_setting')) {

                $json['product']['seller_info'] = array();
                $this->load->model('webservices/home');
                $seller_info = $this->model_webservices_home->getSellerInfoByProduct($productID);

                if(!empty($seller_info)) {
                    $json['product']['seller_info'][0]['seller_id'] = $seller_info['seller_id'];
                    $json['product']['seller_info'][0]['name'] = $seller_info['name'];
                    $json['product']['seller_info'][0]['rating'] = round($seller_info['rating'],1);
                    $json['product']['seller_info'][0]['is_write_review_enabled'] = $marketplace_setting['kbmp_enable_seller_review'];
                }
            }

            //Similar Products Response
            $json['fproducts'] = $this->getSimilarProducts($productID);
        }

        //Complete response
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("getProductDetails", json_encode($json));
        $this->response->output();
        die;
    }

    public function appGetProductReviews() {
        
        $json = array();
        
        $this->load->model('catalog/product');
        
        $productInfo = $this->model_catalog_product->getProduct($this->request->post['product_id']);
        
        $setting_data = $this->config->get('webservice');
        
        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            die;
        } else {
            $json['install_module'] = '';
        }

        $json['status'] = 'success';
        
        if(!isset($this->request->post['page_number'])) {
            $this->request->post['page_number'] = 1;
        }
        if(!isset($this->request->post['item_count'])) {
            $this->request->post['item_count'] = 10000;
        }
        $n = $this->request->post['page_number'];
        $lower_limit = ($n-1)*$this->request->post['item_count'];
        $upper_limit = $n*$this->request->post['item_count'];
        
        $query = $this->db->query('SELECT * FROM `'.DB_PREFIX.'review` WHERE product_id = "'.$this->request->post['product_id'].'" LIMIT '.$lower_limit.','.$upper_limit.'');
        
        if($query->num_rows > 0) {
            $reviews= $query->rows; 
            foreach($reviews as $review) {
                $json['reviews'][] = array(
                    'id' => $review['review_id'],
                    'comment_date' => date('Y-m-d H:i:s',  strtotime($review['date_added'])),
                    'commented_by' => $review['author'],
                    'text' => $review['text'],
                    'rating' => $review['rating'],
                );
            }
        } else {
            $json['reviews'] = array();
        }
        
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: PHPSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("getHomePageData", json_encode($json));
        $this->response->output();

        die;
    }
    
    public function appSaveProductReview() {
        
        
        $json = array();
        
        $this->load->language('product/product');

        $setting_data = $this->config->get('webservice');
        
        $json['install_module'] = '';
        $json['status'] = 'failure';
        
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
                    $json['message'] = $this->language->get('error_name');
            }

            if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
                    $json['message'] = $this->language->get('error_text');
            }

            if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
                    $json['message'] = $this->language->get('error_rating');
            }

            if (!isset($json['error'])) {
                    $this->load->model('catalog/review');

                    $this->model_catalog_review->addReview($this->request->post['product_id'], $this->request->post);

                    $json['status'] = 'success';
                    $json['message'] = $this->language->get('text_success');
            }
        }
        
        
       
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: PHPSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("getHomePageData", json_encode($json));
        $this->response->output();

        die;
    }
    
    //Function definition to get Similar Products
    public function getSimilarProducts($productID) {
        $data = array(
            'title' => 'Similar Products',
            'products' => array()
        );

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            die;
        } else {
            $json['install_module'] = '';
        }

        //Load Models
        $this->load->model('setting/setting');
        $this->load->model('catalog/product');
        $this->load->model('webservices/category');
        $this->load->model('webservices/image');
        $this->load->language('webservices/config');

        $module_info = $this->config->get('webservice');
        if (!empty($module_info)) {
            $getModulesSettingArray = $module_info;
            $width = $getModulesSettingArray['product_image_width'];
            $height = $getModulesSettingArray['product_image_height'];
        } else {
            $width = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_width'));
            $height = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_height'));
        }

        $setting = $this->config->get('similar_module');

        $limit = 4; //Default Limit
        if (!empty($setting)) {
            foreach ($setting as $setting) {
                $limit = $setting['limit'];
            }
        }

        $products = array();

        if (!empty($productID)) {
            $currentProductID = $productID;
            $productToCategory = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = " . (int) $currentProductID);
            
            if($productToCategory->num_rows > 0) {

                $productToCategoryID = $productToCategory->row['category_id'];

                //Get Similar products
                $similarProducts = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_to_category WHERE category_id = " . (int) $productToCategoryID . " ORDER BY rand() limit " . $limit);

                foreach ($similarProducts->rows as $key => $value) {
                    $products[] = $similarProducts->rows[$key]['product_id'];
                }
            }
        }

        if (!empty($products)) {
            foreach ($products as $product) {
                $productInfo = $this->model_catalog_product->getProduct($product);
                if ($productInfo) {
                    $image = $this->model_webservices_image->resize($productInfo['image'], $width, $height);
                    if (!file_exists(DIR_IMAGE . $productInfo['image'])) {
                        $image = $this->model_webservices_image->resize('no_image.gif', $width, $height);
                    }
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
                        $discount_price = '';
                        $discount_percentage = '';
                    }

                    $data['products'][] = array(
                        'id' => $productInfo['product_id'],
                        'name' => html_entity_decode(str_replace('&quot;', '"', $productInfo['name'])),
                        'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                        'discount_price' => $discount_price,
                        'discount_percentage' => $discount_percentage,
                        'src' => str_replace(" ","%20",$image),
                        'ClickActivityName' => 'ProductActivity'
                    );
                }
            }
        }
        return $data;
    }
    
    public function isInWishlist($product_id) {
        if (isset($this->session->data['wishlist']) && in_array($product_id, $this->session->data['wishlist'])) {
            return true;
        } else {
            return false;
        }
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
