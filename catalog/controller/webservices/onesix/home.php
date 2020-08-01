<?php

/*
 * File: service.php
 * Description: It contains Service class and functions for web-services
 * Author: Ashwani Gupta
 * Created Date: 10-March-2016
 */

class ControllerWebServicesOneSixHome extends Controller {

    //Function definition to prepare JSON response of Home Page data and send to requested application
    public function appGetHome() {
        //Response Array
        $json = array();

        $setting_data = $this->config->get('webservice');
       
        //print_R($setting_data);
        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            die;
        } else {
            $json['install_module'] = '';
        }

        //Load models
        $this->load->model('setting/setting');
        $this->load->model('catalog/category');
        $this->load->model('webservices/home');
        $this->load->model('design/banner');
        $this->load->model('catalog/product');
        $this->load->model('webservices/image');
        $this->load->model('design/layout');

        
        $this->load->model('setting/module');
        

        $this->load->language('webservices/config');
        
        if($setting_data['homepage_layout']>0){
            $json['elements'] = $this->getHomePageComponents($setting_data['homepage_layout']);
        }
        
        //Main Banner Response
        //$json['topslider'] = $this->getMainBanner();

        //Promo Banner Response
        //$json['topbanner'] = $this->getPromoBanner();

        //Featured Products Response
        //$json['fproducts'] = $this->getFeaturedProducts($setting_data);

        //Special Products Response
        //$json['sproducts'] = $this->getSpecialProducts($setting_data);

        //Latest Products Response
        //$json['newproducts'] = $this->getLatestProducts($setting_data);
        
        //Best Sellers Products
        //$json['bsproducts'] = $this->getBestSellerProducts($setting_data);

        $json['Menu_Categories'] = $this->getMenuCategories();

        //Menu Information
        $json['cms_links'] = $this->getMenuInformation();

        //Currency
        $json['currencies'] = $this->getCurrencies();

        //Languages
        $json['languages'] = $this->getLanguages();

        $json['languages_record'] = $this->getLanguageRecords();
        
        $spin_win_response = array();
        
        
        $this->load->model('setting/setting');
        $config = $this->model_setting_setting->getSetting('spinwin', $this->config->get('config_store_id'));
        $json['is_tab_bar_enabled'] = "0";
        if(isset($setting_data['tab_bar_enabled'])){
            $json['is_tab_bar_enabled'] = $setting_data['tab_bar_enabled'];
        }
        $spin_win_response['is_spin_and_win_enabled'] = false;
        if (isset($config['spinwin_enable']) && $config['spinwin_enable'] == '1') {
        
            if ($config['spinwin_enable'] == 1) {
                $spin_win_response['is_spin_and_win_enabled'] = true;
                $spin_win_response['wheel_display_interval'] = "30";
                $spin_win_response['maximum_display_frequency'] = "0";
                
                $current_client_time = strtotime(date('Y-m-d H:i:s'));
                
                if (($config["spinwin_fixtime"] == 0) || ($config["spinwin_fixtime"] == 1 && $current_client_time >= strtotime($config["spinwin_fixtimeactive"]) && $current_client_time <= strtotime($config["spinwin_fixtimeexpire"]))) {
                    $spin_win_response['is_spin_and_win_enabled'] = true;
                } else {
                    $spin_win_response['is_spin_and_win_enabled'] = false;
                }
            } else {
                $spin_win_response['is_spin_and_win_enabled'] = false;
            }
           
            if (isset($config['spinwin_freq'])) {
                if ($config['spinwin_freq'] == 2) {  //hour
                    $spin_win_response['maximum_display_frequency'] = "0.04";
                } elseif ($config['spinwin_freq'] == 3) {  //day
                    $spin_win_response['maximum_display_frequency'] = "1";
                } elseif ($config['spinwin_freq'] == 4) {  //week
                    $spin_win_response['maximum_display_frequency'] = "7";
                } elseif ($config['spinwin_freq'] == 5) {  //month
                    $spin_win_response['maximum_display_frequency'] = '30';
                } else {
                    $spin_win_response['maximum_display_frequency'] = "0";
                }
            }
//            $spin_win_response['wheel_display_interval'] = $config['display_interval'];
        } 
        if ($setting_data['mobile_app_spin_win'] == '0') {
            $spin_win_response['is_spin_and_win_enabled'] = false;
        }
        $json['spin_win_response'] = $spin_win_response;
        
        if($this->config->get('kbmp_marketplace_setting')) {
            $json["is_marketplace"] = "1"; // 1 if store is marketplace, 0 if not
        } else {
            $json["is_marketplace"] = "0";
        }
        
        $json['wishlist_active'] = 1;
        $app_theme_color = explode("#", $setting_data['app_theme_color']);
        $json['app_theme_color'] = $app_theme_color[1];
        $app_button_color = explode("#", $setting_data['app_button_color']);
        $json['app_button_color'] = $app_button_color[1];
        $app_button_text_color = explode("#", $setting_data['app_button_text_color']);
        $json['app_button_text_color'] = $app_button_text_color[1];
        $app_background_color = explode("#", $setting_data['app_background_color']);
        $json['app_background_color'] = $app_background_color[1];
        $json['add_to_cart_redirect_enabled'] = $setting_data['redirect_cart_status'];
        if ($setting_data['logo_status']) {
            $json['display_logo_on_title_bar'] = "1";
            $json['title_bar_logo_url']=HTTPS_SERVER.'image/'.$this->imageUrlEncoding($setting_data['image_for_logo']);
        } else {
            $json['display_logo_on_title_bar'] = "0";
            $json['title_bar_logo_url'] = "";
        }
        
        
        if(!isset($this->request->post['iso_code'])) {
            $this->request->post['iso_code'] = 'en-gb';
        }
        
        $json['contact_us_available'] = 1;
        $json['contact_us_link'] = str_replace("amp;", "", $this->url->link('information/contact', 'app=1&lang='.$this->request->post['iso_code'],'true'));
        $json['status'] = 'success';
        $json['message'] = '';


        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("getHomePageData", json_encode($json));
        $this->response->output();
        //echo json_encode($json);

        die;
    }
    
    public function getHomePageComponents($layout_id) {
        $element_data = array();
        $sql = 'Select id_component from ' . DB_PREFIX . 'kb_mobileapp_layout_component where id_layout = '. (int) $layout_id .' order by position asc';
        $query = $this->db->query($sql); 
        $components = $query->rows;
        if (count($components) > 0 && !empty($components)) {
            $i = 0;
            foreach ($components as $key => $comp) {
                $query = $this->db->query('Select id_component_type from ' . DB_PREFIX . 'kb_mobileapp_layout_component where id_component = '.  $comp['id_component']) ;
                $component_type_id = $query->row['id_component_type'];
                $query = $this->db->query('Select component_name from ' . DB_PREFIX . 'kbmobileapp_component_types where id = '.  $component_type_id) ;
                $component_type = $query->row['component_name'];
                if ($component_type == 'top_category') {
                    $category_data = array();
                    $query = $this->db->query('SELECT *  FROM ' . DB_PREFIX . 'kbmobileapp_top_category
                            where id_component = '.(int)$comp['id_component']);
                    $categories = $query->rows; 
                    if (is_array($categories)) {
                        if (count($categories) > 0 && !empty($categories)) {
                            $element_data[$i]['element_type'] = 'categories_top';
                            $element_data[$i]['data'] = array();
                            foreach ($categories as $k => $value) {
                               $sql = 'SELECT name  FROM ' . DB_PREFIX . 'category_description
                                        where category_id = '.(int)$value['id_category'] .' And language_id = '.(int)1;
                                $query = $this->db->query($sql);
                                if(isset($query->row['name'])) {
                                    $category_name = $query->row['name'];
                                } else {
                                    $category_name = "";
                                }
                                $data = array();
                                $data['id'] = $value['id_category'];
                                if ($value['image_url'] != '') {
                                    $data['image_src'] = HTTPS_SERVER.'image/'.$this->imageUrlEncoding($value['image_url']);
                                }
                                $data['image_contentMode'] = $value['image_content_mode']?'scaleAspectFit':'scaleAspectFill';
                                $data['name'] = $category_name;
                                $category_data[] = $data;
                                
                            }
                            unset($data);
                            $element_data[$i]['data'] = $category_data;
                            $i++;
                        }
                    }
                } elseif ($component_type == 'banner_square') {
                    
                    $sql = 'Select heading from  ' . DB_PREFIX . 'kbb_components_heading where id_component =' . (int) $comp['id_component'].' and language_id = '.(int)$this->config->get('config_language_id');
                    $query = $this->db->query($sql);
                    $banner_heading = '';
                    if($query->num_rows) {
                        $banner_heading = $query->row['heading'];
                    }
                    $square_banner_data = array();
                    $sql = 'Select * from  ' . DB_PREFIX . 'kbmobileapp_banners where id_component =' . (int) $comp['id_component'];
                    $query = $this->db->query($sql);
                    $banner_data = $query->rows;
                    if (is_array($banner_data)) {
                        $number_of_entries = count($banner_data);
                        if ($number_of_entries > 0) {
                            $element_data[$i]['element_type'] = 'banners_square';
                            $element_data[$i]['heading'] =  $banner_heading;
                            $element_data[$i]['data'] = array();
                            foreach ($banner_data as $k => $bd) {
                                $data = array();
                                $data['click_target'] = $bd['redirect_activity'];
                                if ($bd['redirect_activity'] == 'category') {
                                    $data['target_id'] = $bd['category_id'];
                                } else {
                                    $data['target_id'] = $bd['product_id'];
                                }
                                $data['src'] = HTTPS_SERVER.'image/'.$this->imageUrlEncoding($bd['image_url']);
                                $data['title'] = '';
                                $data['image_contentMode'] = $bd['image_content_mode']?'scaleAspectFit':'scaleAspectFill';
                                $square_banner_data[] = $data;
                            }
                            unset($data);
                            $element_data[$i]['data'] = $square_banner_data;
                            $i++;
                        }
                    }
            } elseif ($component_type == 'banners_grid') {
                    $sql = 'Select heading from  ' . DB_PREFIX . 'kbb_components_heading where id_component =' . (int) $comp['id_component'].' and language_id = '.(int)$this->config->get('config_language_id');
                    $query = $this->db->query($sql);
                    $banner_heading = '';
                    if($query->num_rows) {
                        $banner_heading = $query->row['heading'];
                    }
                    
                    $square_banner_data = array();
                    $sql = 'Select * from  ' . DB_PREFIX . 'kbmobileapp_banners where id_component =' . (int) $comp['id_component'];
                    $query = $this->db->query($sql);
                    $banner_data = $query->rows;
                    if (is_array($banner_data)) {
                        $number_of_entries = count($banner_data);
                        if ($number_of_entries > 0) {
                            $element_data[$i]['element_type'] = 'banners_grid';
                            $element_data[$i]['heading'] =  $banner_heading;
                            $element_data[$i]['data'] = array();
                            foreach ($banner_data as $k => $bd) {
                                $data = array();
                                $data['click_target'] = $bd['redirect_activity'];
                                if ($bd['redirect_activity'] == 'category') {
                                    $data['target_id'] = $bd['category_id'];
                                } else {
                                    $data['target_id'] = $bd['product_id'];
                                }
                                $data['src'] = HTTPS_SERVER.'image/'.$this->imageUrlEncoding($bd['image_url']);
                                $data['title'] = '';
                                $data['image_contentMode'] = $bd['image_content_mode']?'scaleAspectFit':'scaleAspectFill';
                                $square_banner_data[] = $data;
                            }
                            unset($data);
                            $element_data[$i]['data'] = $square_banner_data;
                            $i++;
                        }
                    }
                } 
                elseif ($component_type == 'banners_countdown') {
                    $sql = 'Select heading from  ' . DB_PREFIX . 'kbb_components_heading where id_component =' . (int) $comp['id_component'].' and language_id = '.(int)$this->config->get('config_language_id');
                    $query = $this->db->query($sql);
                    $banner_heading = '';
                    if($query->num_rows) {
                        $banner_heading = $query->row['heading'];
                    }
                    $square_banner_data = array();
                    $sql = 'Select * from  ' . DB_PREFIX . 'kbmobileapp_banners where id_component =' . (int) $comp['id_component'];
                    $query = $this->db->query($sql);
                    $banner_data = $query->rows;
                    if (is_array($banner_data)) {
                        $number_of_entries = count($banner_data);
                        if ($number_of_entries > 0) {
                            $element_data[$i]['element_type'] = 'banners_countdown';
                            $element_data[$i]['heading'] =  $banner_heading;
                            foreach ($banner_data as $k => $bd) {
                                $data = array();
                                $data['click_target'] = $bd['redirect_activity'];
                                if ($bd['redirect_activity'] == 'category') {
                                    $data['target_id'] = $bd['category_id'];
                                } else {
                                    $data['target_id'] = $bd['product_id'];
                                }
                                $data['src'] = HTTPS_SERVER.'image/'.$this->imageUrlEncoding($bd['image_url']);
                                $data['title'] = '';
                                $data['image_contentMode'] = $bd['image_content_mode']?'scaleAspectFit':'scaleAspectFill';
                                $data['upto_time'] = "".strtotime($bd['countdown']) - time()."";
                                $text_color = array();
                                $text_color = explode("#", $bd['text_color']);
                                $data['timer_text_color'] = $text_color[1];
                                
                                if ($bd['is_enabled_background_color'] == 1) {
                                    $background_color = array();
                                    $background_color = explode("#", $bd['background_color']);
                                    $data['timer_background_color'] = $background_color[1];
                                } else {
                                    $data['timer_background_color'] = '';
                                }
                                if($data['upto_time']>0) {
                                    $square_banner_data[] = $data;
                                }
                            }
                            $element_data[$i]['data'] = $square_banner_data;
                            $i++;
                            unset($data);
                        }
                    }
                } elseif ($component_type == 'banner_horizontal_slider') {
                    $sql = 'Select heading from  ' . DB_PREFIX . 'kbb_components_heading where id_component =' . (int) $comp['id_component'].' and language_id = '.(int)$this->config->get('config_language_id');
                    $query = $this->db->query($sql);
                    $banner_heading = '';
                    if($query->num_rows) {
                        $banner_heading = $query->row['heading'];
                    }
                    $square_banner_data = array();
                    $sql = 'Select * from  ' . DB_PREFIX . 'kbmobileapp_banners where id_component =' . (int) $comp['id_component'];
                    $query = $this->db->query($sql);
                    $banner_data = $query->rows;
                    if (is_array($banner_data)) {
                        $number_of_entries = count($banner_data);
                        if ($number_of_entries > 0) {
                            $element_data[$i]['element_type'] = 'banners_horizontal_sliding';
                            $element_data[$i]['heading'] =  $banner_heading;
                            foreach ($banner_data as $k => $bd) {
                                $data = array();
                                $data['click_target'] = $bd['redirect_activity'];
                                if ($bd['redirect_activity'] == 'category') {
                                    $data['target_id'] = $bd['category_id'];
                                } else {
                                    $data['target_id'] = $bd['product_id'];
                                }
                                $data['src'] = HTTPS_SERVER.'image/'.$this->imageUrlEncoding($bd['image_url']);
                                $data['title'] = '';
                                $data['image_contentMode'] = $bd['image_content_mode']?'scaleAspectFit':'scaleAspectFill';
                                $square_banner_data[] = $data;
                            }
                            unset($data);
                            $element_data[$i]['data'] = $square_banner_data;
                            $i++;
                        }
                    } 
                }
                elseif ($component_type == 'products_square') {
                    $sql = 'Select heading from  ' . DB_PREFIX . 'kbb_components_heading where id_component =' . (int) $comp['id_component'].' and language_id = '.(int)$this->config->get('config_language_id');
                    $query = $this->db->query($sql);
                    $banner_heading = '';
                    if($query->num_rows) {
                        $banner_heading = $query->row['heading'];
                    }
                    $products = array();
                    $products = $this->getProductsComponentData($comp['id_component']);
                    if (!empty($products) && count($products) > 0) {
                        $element_data[$i]['element_type'] = $component_type;
                        $element_data[$i]['heading'] =  '';
                        $element_data[$i]['heading'] =  $banner_heading;
                        $element_data[$i]['data'] = $products;
                        unset($products);
                        $i++;
                } 
                } elseif ($component_type == 'products_recent') {
                        $sql = 'Select heading from  ' . DB_PREFIX . 'kbb_components_heading where id_component =' . (int) $comp['id_component'].' and language_id = '.(int)$this->config->get('config_language_id');
                        $query = $this->db->query($sql);
                        $banner_heading = '';
                        if($query->num_rows) {
                            $banner_heading = $query->row['heading'];
                        }
                        $element_data[$i]['element_type'] = 'products_recent';
                        $element_data[$i]['heading'] =  iconv("UTF-8","UTF-8//IGNORE",$this->language->get('app_text_recent_product'));
                        $element_data[$i]['data'] = array();
                        $i++;
                } elseif ($component_type == 'products_grid') {
                    $sql = 'Select heading from  ' . DB_PREFIX . 'kbb_components_heading where id_component =' . (int) $comp['id_component'].' and language_id = '.(int)$this->config->get('config_language_id');
                    $query = $this->db->query($sql);
                    $banner_heading = '';
                    if($query->num_rows) {
                        $banner_heading = $query->row['heading'];
                    }
                    $products = array();
                    $products = $this->getProductsComponentData($comp['id_component']);
                    if (!empty($products) && count($products) > 0) {
                        $element_data[$i]['element_type'] = $component_type;
                        $element_data[$i]['heading'] =  $banner_heading;
                        $element_data[$i]['data'] = $products;
                        unset($products);
                        $i++;
                    }
                } elseif ($component_type == 'products_horizontal') {
                    $sql = 'Select heading from  ' . DB_PREFIX . 'kbb_components_heading where id_component =' . (int) $comp['id_component'].' and language_id = '.(int)$this->config->get('config_language_id');
                    $query = $this->db->query($sql);
                    $banner_heading = '';
                    if($query->num_rows) {
                        $banner_heading = $query->row['heading'];
                    }
                    $products = array();
                    $products = $this->getProductsComponentData($comp['id_component']);
                    if (!empty($products) && count($products) > 0) {
                        $element_data[$i]['element_type'] = $component_type;
                        $element_data[$i]['heading'] =  '';
                        $element_data[$i]['heading'] =  $banner_heading;
                        $element_data[$i]['data'] = $products;
                        unset($products);
                        $i++;
                    }
            }
        }
    }
    return $element_data;
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
                     $image = HTTPS_SERVER.'image/'.$this->imageUrlEncoding($productInfo['image']);
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
                        $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        $discount_percentage = 0;
                    }

                    $parent_category = $this->model_webservices_home->getParentCatName((int) $productInfo['product_id']);

                    if ($productInfo['date_added'] >= $setting_data['new_product_start_date']) {
                        $is_new = 1;
                    } else {
                        $is_new = 0;
                    }
                
                    $products[$i] = array(
                        'id' => $productInfo['product_id'],
                        'is_in_wishlist' => $this->isInWishlist($productInfo['product_id']),
                        'name' => html_entity_decode(str_replace('&quot;', '"', $productInfo['name'])),
                        'available_for_order' => 1,
                        'new_products' => $is_new,
                        'on_sale_products' => $discount_percentage ? 1 : 0,
                        'category_name' => html_entity_decode(isset($parent_category['name']) ? $parent_category['name'] : ''),
                        'image_contentMode' => $image_content_mode,
                        'ClickActivityName' => 'ProductActivity',
                        'category_id' => isset($parent_category['category_id']) ? $parent_category['category_id'] : '',
                        'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
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
    
    
    
    /*     * ****************************************************************************
     * ***********Functions definition calling in above defined functions************
      /**************************************************************************** */

    //Function definition to get Menu Categories
    public function getMenuCategories() {
        $data = array();
        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
                // Level 2
                $children_data = array();

                $children = $this->model_catalog_category->getCategories($category['category_id']);

                foreach ($children as $child) {
                    $filter_data = array(
                        'filter_category_id' => $child['category_id'],
                        'filter_sub_category' => true
                    );
                    $third_children = array();
                    $results = $this->model_catalog_category->getCategories($child['category_id']);
                    if(!empty($results)) {
                        foreach($results as $result){
                            $sub_children = array();
                            $sub_children = $this->getSubChild($result['category_id']);
                            $third_children[] = array(
                                'id' => $result['category_id'],
                                'name' => html_entity_decode($result['name']),
                                'children' => $sub_children
                            );
                        } 
                    }
                    $children_data[] = array(
                        'id' => $child['category_id'],
                        'name' => html_entity_decode($child['name']) . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                        //  'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                        'children' => $third_children
                    );
                }

                // Level 1
                $data[] = array(
                    'id' => $category['category_id'],
                    'name' => html_entity_decode($category['name']),
                    // 'href'     => $this->url->link('product/category', 'path=' . $category['category_id']),
                    'second_children' => $children_data,
                );
        }
        return $data;
    }

    public function getSubChild($category_id) {
            $sub_child_data = $this->model_catalog_category->getCategories($category_id);
            if (!empty($sub_child_data)) {
                    foreach($sub_child_data as $sub_data) {
                            $grand_sub_children = array();
                            $grand_sub_children = $this->getSubChild($sub_data['category_id']);
                            $final_children_data[] = array(
                                    'id' => $sub_data['category_id'],
                                    'name' => html_entity_decode($sub_data['name']),
                                    'children' => $grand_sub_children
                            );
                    }                
            } else {
                    return array();
            }
            return $final_children_data;
    }
    
    //Function definition to get Menu Information.
    public function getMenuInformation() {
        $data = array();
        $informations = $this->model_webservices_home->getInformation();

        
        if(!isset($this->request->post['iso_code'])) {
            $this->request->post['iso_code'] = 'en-gb';
        }
        
        foreach ($informations as $information => $info_data) {
            if ($info_data['status'] == 1) {
                if ($info_data['cms_row_id'] == '0') {
                    $data[] = array(
                        'name' => html_entity_decode(str_replace("amp;", "", $info_data['title'])),
                        'link' => str_replace("amp;", "", $this->url->link('information/contact', 'app=1&lang='.$this->request->post['iso_code'],'true'))
                    );
                } else {
                    $data[] = array(
                        'name' => html_entity_decode(str_replace("amp;", "", $info_data['title'])),
                        'link' => str_replace("amp;", "", $this->url->link('information/information', 'information_id=' . $info_data['link_to'] . '&app=1&lang='.$this->request->post['iso_code'],'true'))
                    );
                }
            }
        }
        return $data;
    }

    //Function definition to get Main Banner data
    Public function getMainBanner() {

        $data = array(
            'title' => 'Top Sliders',
            'slides' => array()
        );
        $store_id = $this->config->get('config_store_id');
        $webservice_setting = $this->model_setting_setting->getSetting('webservice', $store_id);

        $slideshow_id = $webservice_setting['webservice']['slideshow_id'];
        $slideshow_data = $this->model_webservices_home->checkSlideshowBannerStatus($slideshow_id);

        if (isset($slideshow_data['status']) && $slideshow_data['status'] == 1) {
            $slideshow_image_data = $this->model_webservices_home->getSlideshowBannerImageData($slideshow_id);
            $results = array_slice($slideshow_image_data, 0, $slideshow_data['banner_limit'], true);

            $width = $webservice_setting['webservice']['slideshow_width'];
            $height = $webservice_setting['webservice']['slideshow_height'];
            foreach ($results as $result) {
                if ($result['link_type'] == '0') {
                    $link_type = 'Category';
                } else if ($result['link_type'] == '1') {
                    $link_type = 'Product';
                } else {
                    $link_type = "";
                }
                if (is_file(DIR_IMAGE . $result['image'])) {

                    $image = $this->model_webservices_image->resize($result['image'], $width, $height);
                    if (!file_exists(DIR_IMAGE . $result['image'])) {
                        $image = $this->model_webservices_image->resize('placeholder.png', $width, $height);
                    }
                    //Check if webp image exists
                    $info = pathinfo($image);
                    if (isset($info['extension'])) {
                        $directory = str_replace(HTTP_SERVER . 'image/cache/', DIR_IMAGE . 'cache/', $info['dirname']);
                        if (file_exists($directory . '/' . $info['filename'] . '.webp')) {
                            $image = $info['dirname'] . '/' . $info['filename'] . '.webp';
                        }
                    }
                    $data['slides'][] = array(
                        'click_target' => $link_type,
                        'target_id' => $result['link_to'],
                        'title' => $result['title'],
                        'src' => $image,
                    );
                }
            }
        } else {
            $layout_id = '1';
            $code = 'slideshow';

            $getHomeTopSliderLayout = $this->model_webservices_home->getHomeLayoutModules($layout_id, $code);

            if (count($getHomeTopSliderLayout) > 0) {
                if (version_compare(VERSION, '2.0.1.0', '<')) {

                    $modules = $this->model_design_layout->getLayoutModules($layout_id, 'content_top');

                    foreach ($modules as $module) {
                        $part = explode('.', $module['code']);
                        if (isset($part[0])) {
                            $code1 = $part[0];
                        }

                        if ($code1 && $code1 == $code && $this->config->get($code . '_status')) {

                            $setting = $this->config->get($code . '_module');
                            if (isset($part[1]) && isset($setting[$part[1]])) {
                                $getModulesSettingArray = $setting[$part[1]];

                                if (array_key_exists('banner_id', $getModulesSettingArray)) {
                                    $checkBannerStatus = $this->model_webservices_home->checkBannerStatus($getModulesSettingArray['banner_id']);
                                    if ($checkBannerStatus[0]['status'] == '1') {
                                        $results = $this->model_design_banner->getBanner($getModulesSettingArray['banner_id']);
                                        $width = $getModulesSettingArray['width'];
                                        $height = $getModulesSettingArray['height'];
                                        foreach ($results as $result) {
                                            if (is_file(DIR_IMAGE . $result['image'])) {
                                                $image = $this->model_webservices_image->resize($result['image'], $width, $height);
                                                if (!file_exists(DIR_IMAGE . $result['image'])) {
                                                    $image = $this->model_webservices_image->resize('placeholder.png', $width, $height);
                                                }
                                                //Check if webp image exists
                                                $info = pathinfo($image);
                                                if (isset($info['extension'])) {
                                                    $directory = str_replace(HTTP_SERVER . 'image/cache/', DIR_IMAGE . 'cache/', $info['dirname']);
                                                    if (file_exists($directory . '/' . $info['filename'] . '.webp')) {
                                                        $image = $info['dirname'] . '/' . $info['filename'] . '.webp';
                                                    }
                                                }

                                                $data['slides'][] = array(
                                                    'click_target' => 'Category',
                                                    'target_id' => '',
                                                    'title' => $result['title'],
                                                    'src' => $image
                                                );
                                            }
                                        }
                                    }
                                }

                                break;
                            }
                        }
                    }
                } else {

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

                                    if (array_key_exists('banner_id', $getModulesSettingArray)) {
                                        $checkBannerStatus = $this->model_webservices_home->checkBannerStatus($getModulesSettingArray['banner_id']);
                                        if ($checkBannerStatus[0]['status'] == '1') {
                                            $results = $this->model_design_banner->getBanner($getModulesSettingArray['banner_id']);
                                            $width = $getModulesSettingArray['width'];
                                            $height = $getModulesSettingArray['height'];
                                            foreach ($results as $result) {
                                                if (is_file(DIR_IMAGE . $result['image'])) {
                                                    $image = $this->model_webservices_image->resize($result['image'], $width, $height);
                                                    if (!file_exists(DIR_IMAGE . $result['image'])) {
                                                        $image = $this->model_webservices_image->resize('placeholder.png', $width, $height);
                                                    }
                                                    //Check if webp image exists
                                                    $info = pathinfo($image);
                                                    if (isset($info['extension'])) {
                                                        $directory = str_replace(HTTP_SERVER . 'image/cache/', DIR_IMAGE . 'cache/', $info['dirname']);
                                                        if (file_exists($directory . '/' . $info['filename'] . '.webp')) {
                                                            $image = $info['dirname'] . '/' . $info['filename'] . '.webp';
                                                        }
                                                    }
                                                    $data['slides'][] = array(
                                                        'click_target' => 'Category',
                                                        'target_id' => '',
                                                        'title' => $result['title'],
                                                        'src' => $image
                                                    );
                                                }
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
        }
        return $data;
    }

    //Function definition to get Promo Banner data
    public function getPromoBanner() {
        $data = array(
            'title' => 'Top Banners',
            'banners' => array()
        );

        $store_id = $this->config->get('config_store_id');
        $webservice_setting = $this->model_setting_setting->getSetting('webservice', $store_id);

        $banner_id = $webservice_setting['webservice']['banner_id'];
        $banner_data = $this->model_webservices_home->checkSlideshowBannerStatus($banner_id);
        if (isset($banner_data['status']) && $banner_data['status'] == 1) {
            $banner_image_data = $this->model_webservices_home->getSlideshowBannerImageData($banner_id);
            $results = array_slice($banner_image_data, 0, $banner_data['banner_limit'], true);
            $width = $webservice_setting['webservice']['banner_width'];
            $height = $webservice_setting['webservice']['banner_height'];
            foreach ($results as $result) {
                if ($result['link_type'] == '0') {
                    $link_type = 'Category';
                } else if ($result['link_type'] == '1') {
                    $link_type = 'Product';
                }
                if (is_file(DIR_IMAGE . $result['image'])) {
                    $image = $this->model_webservices_image->resize($result['image'], $width, $height);
                    if (!file_exists(DIR_IMAGE . $result['image'])) {
                        $image = $this->model_webservices_image->resize('placeholder.png', $width, $height);
                    }
                    //Check if webp image exists
                    $info = pathinfo($image);
                    if (isset($info['extension'])) {
                        $directory = str_replace(HTTP_SERVER . 'image/cache/', DIR_IMAGE . 'cache/', $info['dirname']);
                        if (file_exists($directory . '/' . $info['filename'] . '.webp')) {
                            $image = $info['dirname'] . '/' . $info['filename'] . '.webp';
                        }
                    }
                    $data['banners'][] = array(
                        'click_target' => $link_type,
                        'target_id' => $result['link_to'],
                        'title' => $result['title'],
                        'src' => $image,
                    );
                }
            }
        }
        return $data;
    }
    //Function definition to get Featured Products
    public function getFeaturedProducts($number_of_products=10,$image_content_mode=1,$setting_data) {
        $this->load->language('extension/module/featured');
        $data = array(
            'title' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('heading_title')),
            'products' => array()
        );
        $store_id = $this->config->get('config_store_id');
        $webservice_setting = $this->model_setting_setting->getSetting('webservice', $store_id);

        $layout_id = '1';
        $code = 'featured';

        $getHomeTopFeaturedProducts = $this->model_webservices_home->getHomeLayoutModules($layout_id, $code);
        
        if (count($getHomeTopFeaturedProducts) > 0) {

            if (version_compare(VERSION, '2.0.1.0', '<')) {

                    $modules = $this->model_design_layout->getLayoutModules($layout_id, 'content_top');
                    
                    
                    
                    foreach ($modules as $module) {
                        $part = explode('.', $module['code']);
                        if (isset($part[0])) {
                            $code1 = $part[0];
                        }
                        if ($code1 && $code1 == $code && $this->config->get($code . '_status')) {

                            $setting = $this->config->get($code . '_module');

                            if (isset($part[1]) && isset($setting[$part[1]])) {
                                $getModulesSetting = $setting[$part[1]];

                                $getModulesSettingArray = explode(",", $this->config->get($code . '_product'));
                                if (isset($getModulesSettingArray) && count($getModulesSettingArray) > 0) {
                                    $featuredProducts = array_slice($getModulesSettingArray, 0, $getModulesSetting['limit'], true);
                                    $width = $getModulesSetting['width'];
                                    $height = $getModulesSetting['height'];

                                    if (!empty($featuredProducts)) {
                                        foreach ($featuredProducts as $featuredProduct) {
                                            $productInfo = $this->model_catalog_product->getProduct($featuredProduct);
                                            if ($productInfo) {
                                                $image = HTTPS_SERVER.'image/'.$this->imageUrlEncoding($productInfo['image']);
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
                                                    $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                                                    $discount_percentage = 0;
                                                }

                                                $parent_category = $this->model_webservices_home->getParentCatName((int) $productInfo['product_id']);

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
                                                    'category_name' => html_entity_decode($parent_category['name']),
                                                    'ClickActivityName' => 'ProductActivity',
                                                    'category_id' => $parent_category['category_id'],
                                                    'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                                                    'src' => $image,
                                                    'discount_price' => $discount_price,
                                                    'discount_percentage' => $discount_percentage,
                                                    'is_in_wishlist' => $this->isInWishlist($productInfo['product_id']),
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
            } else {

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
                                                     $image = HTTPS_SERVER.'image/'.$this->imageUrlEncoding($productInfo['image']);
                                                    //Check if webp image exists
                                                    $info = pathinfo($image);
                                                    if (isset($info['extension'])) {
                                                        $directory = str_replace(HTTP_SERVER . 'image/cache/', DIR_IMAGE . 'cache/', $info['dirname']);
                                                        if (file_exists($directory . '/' . $info['filename'] . '.webp')) {
                                                            $image = $info['dirname'] . '/' . $info['filename'] . '.webp';
                                                        }
                                                    }
                                                    
                                                    $parent_category = $this->model_webservices_home->getParentCatName((int) $productInfo['product_id']);


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
                                                        $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
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
                                                        'category_name' => html_entity_decode(isset($parent_category['name']) ? $parent_category['name'] : ''),
                                                        'ClickActivityName' => 'ProductActivity',
                                                        'category_id' => isset($parent_category['category_id']) ? $parent_category['category_id'] : '',
                                                        'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                                                        'src' => $image,
                                                        'discount_price' => $discount_price,
                                                        'discount_percentage' => $discount_percentage,
                                                        'is_in_wishlist' => $this->isInWishlist($productInfo['product_id']),
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
        }
        return $data['products'];
    }

    //Function definition to get Special Products
    public function getSpecialProducts($number_of_products=10,$image_content_mode=1,$setting_data) {
        $this->load->language('extension/module/special');
        $data = array(
            'title' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('heading_title')),
            'products' => array()
        );

        $store_id = $this->config->get('config_store_id');
        $webservice_setting = $this->model_setting_setting->getSetting('webservice', $store_id);

            $filter_data = array(
                'sort' => 'pd.name',
                'order' => 'ASC',
                'start' => 0,
                'limit' => $number_of_products
            );

            $productInfoData = $this->model_catalog_product->getProductSpecials($filter_data);

            if (!empty($productInfoData)) {
                foreach ($productInfoData as $productInfo) {

                    $image = HTTPS_SERVER.'image/'.$this->imageUrlEncoding($productInfo['image']);
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
                        $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        $discount_percentage = 0;
                    }

                    $parent_category = $this->model_webservices_home->getParentCatName((int) $productInfo['product_id']);

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
                        'category_name' => html_entity_decode(isset($parent_category['name']) ? $parent_category['name'] : ''),
                        'ClickActivityName' => 'ProductActivity',
                        'category_id' => isset($parent_category['category_id']) ? $parent_category['category_id'] : '',
                        'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                        'src' => $image,
                        'discount_price' => $discount_price,
                        'discount_percentage' => $discount_percentage,
                        'is_in_wishlist' => $this->isInWishlist($productInfo['product_id']),
                        'image_contentMode' => $image_content_mode,
                    );
                }
            }
        return $data['products'];
    }

    //Function definition to get Latest Products
    public function getLatestProducts($number_of_products=10,$image_content_mode=1,$setting_data) {
        $this->load->language('extension/module/latest');
        $data = array(
            'title' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('heading_title')),
            'products' => array()
        );
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
                    $image = HTTPS_SERVER.'image/'.$productInfo['image'];
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
                        $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        $discount_percentage = 0;
                    }

                    $parent_category = $this->model_webservices_home->getParentCatName((int) $productInfo['product_id']);

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
                        'category_name' => html_entity_decode(isset($parent_category['name']) ? $parent_category['name'] : ''),
                        'ClickActivityName' => 'ProductActivity',
                        'category_id' => isset($parent_category['category_id']) ? $parent_category['category_id'] : '',
                        'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                        'src' => $image,
                        'discount_price' => $discount_price,
                        'discount_percentage' => $discount_percentage,
                        'is_in_wishlist' => $this->isInWishlist($productInfo['product_id']),
                        'image_contentMode' => $image_content_mode,
                    );
                } else {
                    break;
                }
            }
        }
        return $data['products'];
    }

    public function getBestSellerProducts($number_of_products=10,$image_content_mode=1,$setting_data) {
        $this->load->language('module/bestseller');
        $data = array(
            'title' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('heading_title')),
            'products' => array()
        );

        $store_id = $this->config->get('config_store_id');
        $webservice_setting = $this->model_setting_setting->getSetting('webservice', $store_id);

        $productInfoData = $this->model_catalog_product->getBestSellerProducts($number_of_products);

            if (!empty($productInfoData)) {
                foreach ($productInfoData as $productInfo) {

                     $image = HTTPS_SERVER.'image/'.$this->imageUrlEncoding($productInfo['image']);
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
                        $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        $discount_percentage = 0;
                    }

                    $parent_category = $this->model_webservices_home->getParentCatName((int) $productInfo['product_id']);

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
                        'category_name' => html_entity_decode(isset($parent_category['name']) ? $parent_category['name'] : ''),
                        'ClickActivityName' => 'ProductActivity',
                        'category_id' => isset($parent_category['category_id']) ? $parent_category['category_id'] : '',
                        'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                        'src' => $image,
                        'discount_price' => $discount_price,
                        'discount_percentage' => $discount_percentage,
                        'is_in_wishlist' => $this->isInWishlist($productInfo['product_id']),
                        'image_contentMode' => $image_content_mode,
                    );
                }
            }
        return $data['products'];
    }


    //Function to get currencies
    public function getCurrencies() {
        $data = array();
        $data = $this->model_webservices_home->getCurrencies();
        if (isset($this->session->data['currency']) && !empty($this->session->data['currency'])) {
            $this->load->model('localisation/currency');
            $currency = $this->model_localisation_currency->getCurrencyByCode($this->session->data['currency']);
            $data['default_currency_code'] = $this->session->data['currency'];
            $data['default_currency_id'] = $currency['code'];
        } else {
            $data['default_currency_code'] = 'USD';
            $data['default_currency_id'] = 'USD';
        }
        return $data;
    }

    public function getLanguageRecords() {
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
                    $langdata[] = array("iso_code" => $val["iso_code"], "timestamp" => filemtime(DIR_LANGUAGE . "en-gb/webservices/config.php"));
                }    
            }   
        }
        return $langdata;
    }

    //Function to get languages
    public function getLanguages() {
        $data = array(
            'lang_list' => array()
        );
        $data = $this->model_webservices_home->getLanguages();
        $data['default_lang'] = $this->session->data['language'];
        return $data;
    }

    public function isInWishlist($product_id) {
        if($this->customer->isLogged()){
            $this->load->model('account/customer');
            $this->load->model('webservices/wishlist');
            $check_customer = $this->model_account_customer->getCustomerByEmail($this->customer->getEmail());
            if (!empty($check_customer)) {
                $customer_id = $check_customer['customer_id'];
                $this->session->data['customer_id'] = $customer_id;
                $this->customer_id = $customer_id;
                $results = $this->model_webservices_wishlist->getWishlist($customer_id);
                if(in_array($product_id,$results)) {
                    return true;
                }
            }
            return false;
        } 
        
        return false;
    }
    
    public function appGetConfig() {

        $json = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            die;
        } else {
            $json['install_module'] = '';
        }
        $json['status'] = 'success';
        
        $delete_customer_query = "Select id_customer from `".DB_PREFIX."kbmobileapp_unique_verification`";
        $delete_array = $this->db->query($delete_customer_query);
        
        if($delete_array->num_rows > 0) {
            foreach($delete_array->rows as $delete_arr){
                $check_if_customer_exist = $this->db->query("Select * from `".DB_PREFIX."customer` where customer_id = '".$delete_arr['id_customer']."'");
                if($check_if_customer_exist->num_rows <= 0) {
                    $this->db->query("DELETE from `".DB_PREFIX."kbmobileapp_unique_verification` where id_customer = '".$delete_arr['id_customer']."'");
                }
            }
        }
        
        $json['session_data'] = '';
        $json['log_configurations']['status'] = $setting_data['webservice_log_status'];
        $json['fingerprint_configurations']['is_enabled'] = $setting_data['fingerprint_login_status'];
        $json['phone_number_registartion_configurations']['is_enabled'] = $setting_data['phone_registration_status'];
        $json['phone_number_registartion_configurations']['is_mandatory'] = $setting_data['mandatory_phone_registration_status'];
        if($setting_data['whatsapp_chat_status']) {
            $json['whatsapp_configurations']['is_enabled'] = true;
            $json['whatsapp_configurations']['chat_number'] = $setting_data['whatsapp_phone_number'];
        } else {
            $json['whatsapp_configurations']['is_enabled'] = false;
            $json['whatsapp_configurations']['chat_number'] = '';
        }
        if($setting_data['webservice_chat_status']) {
            $json['zopim_chat_configurations']['status'] = "1";
            $json['zopim_chat_configurations']['chat_api_key'] = $setting_data['chat_key'];
        } else {
            $json['zopim_chat_configurations']['status'] = "0";
            $json['zopim_chat_configurations']['chat_api_key'] = '';
        }
   
        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("getHomePageData", json_encode($json));
        $this->response->output();

        die;
    }
    
    public function appCheckLogStatus() {

        $json = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            die;
        } else {
            $json['install_module'] = '';
        }

        $json['log_status'] = $setting_data['webservice_log_status'];

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("getHomePageData", json_encode($json));
        $this->response->output();

        die;
    }

    public function appCheckLiveChatSupportStatus() {

        $json = array();

        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            die;
        } else {
            $json['install_module'] = '';
        }

        $json['status'] = $setting_data['webservice_chat_status'];
        $json['chat_api_key'] = "76Vb6BY89Y98UHB8y";

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("getHomePageData", json_encode($json));
        $this->response->output();

        die;
    }

    public function appGetMobilePaymentMethods() {

        $json = array();
        $this->load->model('webservices/home');
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            die;
        } else {
            $json['install_module'] = '';
        }
        
        if(isset($this->request->post['order_message'])) {
            $this->session->data['comment'] = $this->request->post['order_message'];
        } else if(isset($this->request->get['order_message'])) {
            $this->session->data['comment'] = $this->request->get['order_message'];
        }

        $json['status'] = 'success';
        $json['message'] = '';
        $json['payments'] = array();
        if ($setting_data['paypal_status']) {
            $json['payments'][] = array(
                'payment_method_name' => $setting_data['paypal_name'],
                'payment_method_code' => $setting_data['paypal_code'],
                'configuration' => array(
                    'payment_method_mode' => $setting_data['paypal_mode'] ? 'live' : 'sandbox',
                    'client_id' => $setting_data['paypal_clientid'],
                    'is_default' => 'yes',
                    'other_info' => ''
                ),
            );
        }
        if ($setting_data['cod_status']) {
            $json['payments'][] = array(
                'payment_method_name' => $setting_data['cod_name'],
                'payment_method_code' => $setting_data['cod_code'],
                'configuration' => array(
                    'payment_method_mode' => 'live',
                    'client_id' => '',
                    'is_default' => 'no',
                    'other_info' => ''
                ),
            );
        }

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("getHomePageData", json_encode($json));
        $this->response->output();

        die;
    }
    
    
    public function appGetSellers() {
        
        $json = array();
        $json['install_module'] = "";
        $json['sellers'] = array();
        $this->load->model('webservices/home');
        $this->load->model('kbmp_marketplace/kbmp_marketplace');
        
        $setting_data = $this->config->get('webservice');
        
        
        $n = $this->request->post['page_number'];
        
        if(isset($this->request->post['page_number']) && isset($this->request->post['item_count'])) {
            $filter_data=array(
                    'lower_limit' => ($n-1)*$this->request->post['item_count'], 
                    'upper_limit'  =>  $n*$this->request->post['item_count']
            );
        } else {
            $filter_data=array(
                    'lower_limit' => 0, 
                    'upper_limit'  =>  10000,
            );
        }
        
        $sellers_data = $this->model_webservices_home->getSellers($filter_data);
        $marketplace_setting = $this->config->get('kbmp_marketplace_setting');
        
        if(!empty($sellers_data)) {
            
            foreach($sellers_data as $seller_data) {
                
                if($seller_data['logo']=="") {
                    $seller_data['logo'] = "profile-pic.png";
                }
                if($seller_data['banner']=="") {
                    $seller_data['banner']="main_banner.jpg";
                }
                
                $rating = $this->model_webservices_home->getSellerRating($seller_data['seller_id'])?$this->model_webservices_home->getSellerRating($seller_data['seller_id']):"";
                $this->model_webservices_home->getSellerRating($seller_data['seller_id']);
                $json['sellers'][] = array(
                    'seller_id' => $seller_data['seller_id'],
                    'name' => $seller_data['firstname'].' '.$seller_data['lastname'],
                    'logo' => HTTPS_SERVER.'image/sellers_logo/'.$this->imageUrlEncoding($seller_data['logo']),
                    'banner' => HTTPS_SERVER.'image/sellers_banner/'.$this->imageUrlEncoding($seller_data['banner']),
                    'rating' =>  round($rating,1),
                    'is_write_review_enabled' =>  $marketplace_setting['kbmp_enable_seller_review'],
                );
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
    public function appGetSellerReviews() {
        
        
        $json = array();
        $this->load->model('webservices/home');
        $this->load->model('kbmp_marketplace/kbmp_marketplace');
        
        $setting_data = $this->config->get('webservice');
        $marketplace_setting = $this->config->get('kbmp_marketplace_setting');
       
        
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
        
        $filter_data=array(
                'lower_limit' => 0, 
                'upper_limit'  => 1,
                'seller_id' => $this->request->post['seller_id'],
        );
        
        $sellers_data = $this->model_webservices_home->getSellers($filter_data);
        $json['seller_info'] =array(); 
        
        if(!empty($sellers_data)) {
        
        $seller_data = $sellers_data[0];
        
        if(!isset($this->request->post['only_comments']) || $this->request->post['only_comments'] == 0) {
            
            if($seller_data['logo']=="") {
                $seller_data['logo'] = "profile-pic.png";
            }
            if($seller_data['banner']=="") {
                $seller_data['banner']="main_banner.jpg";
            }
            
            $json['seller_info'] =array(
                'seller_id' => $seller_data['seller_id'],
                'name' => $seller_data['firstname'].' '.$seller_data['lastname'],
                'rating' => $this->model_webservices_home->getSellerRating($seller_data['seller_id']),
                'Address' => $seller_data['address'],
                'is_write_review_enabled' => $marketplace_setting['kbmp_enable_seller_review'],
                'return_policy' => $seller_data['return_policy'],
                'shipping_policy' => $seller_data['shipping_policy'],
                'logo' => HTTPS_SERVER.'image/sellers_logo/'.$this->imageUrlEncoding($seller_data['logo']),
                'banner' => HTTPS_SERVER.'image/sellers_banner/'.$this->imageUrlEncoding($seller_data['banner']),
            );
        }
        
        
            $sql = "SELECT * FROM `oc_kb_mp_seller_review` WHERE seller_id = '" . (int) $seller_data['seller_id'] . "' and approved='1' LIMIT ".$lower_limit.",".$upper_limit."";
            $query = $this->db->query($sql);
        
            if($query->num_rows) {
                $comments= $query->rows; 
                foreach($comments as $comment) {
                    $json['seller_info']['comments'][] = array(
                        'id' => $comment['seller_review_id'],
                        'comment_date' => date('Y-m-d H:i:s',  strtotime($comment['date_added'])),
                        'commented_by' => $comment['author'],
                        'text' => $comment['text'],
                        'rating' => $comment['rating'],
                    );
                }
            } else {
                $json['seller_info']['comments'] = array();
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
    
    public function appSaveSellerReview() {
        
        
        $json = array();
        
        $this->load->model('webservices/home');
        $this->load->model('kbmp_marketplace/kbmp_marketplace');
        $this->load->model('account/customer');
        
        $setting_data = $this->config->get('webservice');
        $marketplace_setting = $this->config->get('kbmp_marketplace_setting');
       
        $json['install_module'] = '';
        $json['status'] = 'failure';
        $json['message'] = '';
        
        
        $customerInfo = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
        
        $data = array(
            'name' => $customerInfo['firstname'].' '.$customerInfo['lastname'],
            'rating' => $this->request->post['rating'],
            'text' => $this->request->post['text'],
        );
        
        $this->model_kbmp_marketplace_kbmp_marketplace->addSellerReview($data,$this->request->post['seller_id'],$marketplace_setting['kbmp_seller_review_approval_required']);
       
        $json['status'] = 'success';
        $json['message'] = 'Review has been saved successfully';
        
       
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

    
    public function imageUrlEncoding($url){
        $setting_data = $this->config->get('webservice');
        if($setting_data['url_encoding_status']){
            return urlencode($url); 
        } else {
            return str_replace(" ","%20",$url);
        }
    }
    public function appSpinWin() {

        $this->load->model('setting/setting');
        $this->load->model('extension/module/spin_win');
        if ($this->config->get('config_store_id') != "") {
            $storeId = $this->config->get('config_store_id');
        } else {
            $storeId = 0;
        }
        $data['setting'] = $this->model_setting_setting->getSetting('spinwin', $storeId);
        if (empty($data['setting'])) {
            return;
        }
        $data['select_pages'] = array();
        if (isset($data['setting']['spinwin_selectpages'])) {
            foreach ($data['setting']['spinwin_selectpages'] as $key => $value) {
                $data['select_pages'][] = $this->model_extension_module_spin_win->getLayoutRoutes($value);
            }
        }
        $data['spin_data'] = $this->model_extension_module_spin_win->getOffer('spinwin', $storeId);
        $this->load->language('extension/module/spin_win');
        $data['do_not'] = $this->language->get('do_not');
        $data['email_address'] = $this->language->get('email_address');
        $data['get_coupon'] = $this->language->get('get_coupon');
        $data['no_thanks'] = $this->language->get('no_thanks');
        $data['empty_field'] = $this->language->get('empty_field');
        $data['empty_email'] = $this->language->get('empty_email');
        $data['validate_email'] = $this->language->get('validate_email');
        $data['max_email'] = $this->language->get('max_email');
        $data['offer'] = $this->language->get('offer');
        $data['hurry'] = $this->language->get('hurry');
        $data['continue'] = $this->language->get('continue');
        $data['code_copied'] = $this->language->get('code_copied');
        $data['email_check'] = $this->language->get('email_check');
        $data['fat_discount'] = $this->language->get('fat_discount');
        $data['email_place'] = $this->language->get('email_place');
        $data['bonus_unlocked'] = $this->language->get('bonus_unlocked');
        $data['wheel_term'] = $this->language->get('wheel_term');
        $data['dnt_feel'] = $this->language->get('dnt_feel');
        $data['try_luck'] = $this->language->get('try_luck');
        $data['continue'] = $this->language->get('continue');
        $data['use_coupon'] = $this->language->get('use_coupon');
        $data['better_luck'] = $this->language->get('better_luck');
        $data['coupon_sent'] = $this->language->get('coupon_sent');
        $data['device_type'] = isset($this->request->get['device_type'])?$this->request->get['device_type']:"";;
        $data['route'] = isset($this->request->get['route']) ? $this->request->get['route'] : 'common/home';
        $this->config->get('config_store_id');
        //$data['header'] = $this->load->controller('common/header');
        $this->response->setoutput($this->load->view('webservices/spin_win', $data));
    }
}
