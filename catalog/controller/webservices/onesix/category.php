<?php

class ControllerWebServicesOneSixCategory extends Controller {

    public function appGetCategoryDetails() {

        //Response Array
        $json = array();
        $this->load->model('webservices/home');

        $setting_data = $this->config->get('webservice');
        //$this->request->post = json_decode('{"item_count":"10","filter":"{&quot;filter_result&quot;:[{&quot;title&quot;:&quot;Brands|Brands&quot;,&quot;choice_type&quot;:&quot;multiple&quot;,&quot;items&quot;:[{&quot;id&quot;:&quot;8&quot;,&quot;name&quot;:&quot;Apple&quot;}]},{&quot;title&quot;:&quot;Prices|Prices&quot;,&quot;choice_type&quot;:&quot;multiple&quot;,&quot;items&quot;:[]},{&quot;title&quot;:&quot;1|1&quot;,&quot;choice_type&quot;:&quot;multiple&quot;,&quot;items&quot;:[]},{&quot;title&quot;:&quot;2|2&quot;,&quot;choice_type&quot;:&quot;multiple&quot;,&quot;items&quot;:[]},{&quot;title&quot;:&quot;5|5&quot;,&quot;choice_type&quot;:&quot;multiple&quot;,&quot;items&quot;:[]},{&quot;title&quot;:&quot;11|11&quot;,&quot;choice_type&quot;:&quot;multiple&quot;,&quot;items&quot;:[]}]}","iso_code":"fr","email":"alath@velsof.com","page_number":"1","search_term":"","category_id":"20","sort_by":"low","id_currency":"GBP","session_data":"h8q37fsru940r3rlvqms3asv34"}', true);

        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            $this->model_webservices_home->logKnowbandAppRequest("appGetCategoryDetails", json_encode($json));

            die;
        } else {
            $json['install_module'] = '';
        }

        //Load models
        $this->load->model('catalog/product');
        $this->load->model('webservices/image');
        $this->load->model('webservices/category');
        $pageNumber = !empty($this->request->post['page_number']) ? $this->request->post['page_number'] : '1';
        $itemCount = !empty($this->request->post['item_count']) ? $this->request->post['item_count'] : '20';
        $sortBy = !empty($this->request->post['sort_by']) ? $this->request->post['sort_by'] : 'low';
        $filter = !empty($this->request->post['filter']) ? $this->request->post['filter'] : '';
        if (!empty($this->request->post['category_id'])) {
            $categoryID = $this->request->post['category_id'];
            $json['fproducts'] = $this->getProductsByCategoryId($categoryID, $pageNumber, $itemCount, $sortBy, $filter, $setting_data);
        } else if (!empty($this->request->post['search_term'])) {
            $searchTerm = $this->request->post['search_term'];
            $json['fproducts'] = $this->getProductsBySearchTerm($searchTerm, $pageNumber, $itemCount, $sortBy, $filter, $setting_data);
        }

        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("getProductsList", json_encode($json));
        $this->response->output();
        die;
    }

    //Function definition to get products by category ID
    public function getProductsByCategoryId($categoryID, $pageNumber, $itemCount, $sortBy, $filter, $setting_data) {

        $this->load->language('webservices/config');
        $data = array(
            'title' => 'Category Products',
            'products' => array()
        );
        $this->load->model('catalog/category');
        $category_info = $this->model_catalog_category->getCategory($categoryID);
        if ($category_info) {
            $data['title'] = html_entity_decode($category_info['name']);
        }

        $sort = 'p.price';
        $order = 'ASC';
        if ($sortBy == 'high') {
            $sort = 'p.price';
            $order = 'DESC';
        } else if ($sortBy == 'low') {
            $sort = 'p.price';
            $order = 'ASC';
        }
        $filter_id = array();
        $filter_title = array();
        $brand_filter = array();
        $price_filter = array();
        $filter_filter = array();
        //$filter = json_decode('{"filter_result":[{"title":"Brands","choice_type":"multiple","items":[{"id":"8","name":"Dupont"}]},{"title":"Size","choice_type":"multiple","items":[{"id":"1","name":"150 Ml "}]},{"title":"Color","choice_type":"multiple","items":[{"id":"2","name":"Red "}]},{"title":"Prices","choice_type":"multiple","items":[]}]}', true);
        if (!empty($filter)) {

            $filter = json_decode(str_replace('&quot;', '"', (trim($filter))), true);

            foreach ($filter['filter_result'] as $filter) {
                $temp = explode("|", $filter['title']);
                $filter['title'] = $temp[0];
                if (isset($filter['title']) && strtolower($filter['title']) == 'brands') {
                    if (!empty($filter['items'])) {
                        foreach ($filter['items'] as $items) {
                            $brand_filter[] = $items['id'];
                        }
                    }
                }
                if (isset($filter['title']) && strtolower($filter['title']) == 'prices') {
                    if (!empty($filter['items'])) {
                        foreach ($filter['items'] as $items) {
                            $price_filter[] = $items['id'];
                        }
                    }
                }

                if (isset($filter['title']) && strtolower($filter['title'])) {
                    if (!empty($filter['items'])) {
                        foreach ($filter['items'] as $items) {
                            if (isset($filter['title']) && (strtolower($filter['title']) != 'brands' && strtolower($filter['title']) != 'prices')) {
                                if (iconv("UTF-8","UTF-8//IGNORE",$this->language->get('use_option_filter')) == '4') {
                                    $filter_title[] = "'" . $filter['title'] . "'";
                                    $filter_id[] = $items['id'];
                                } else if(strpos($filter['title'], 'attr') !== false) {
                                    $filter_title[] = "'" . $items['name'] . "'";
                                    $filter_id[] = $items['id'];
                                } 
                                else {
                                    $filter_filter[] = $items['id'];
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($brand_filter)) {
                $brand_filter = implode(",", $brand_filter);
            }
            if (!empty($price_filter)) {
                $price_filter = implode(",", $price_filter);
            }
            if (!empty($filter_title)) {
                $filter_title = implode(",", $filter_title);
            }
            if (!empty($filter_id)) {
                $filter_id = implode(",", $filter_id);
            }
            if (!empty($filter_filter)) {
                $filter_filter = implode(",", $filter_filter);
            }
            $filterDataTemp = array(
                'filter_category_id' => $categoryID,
                'brand_filter' => $brand_filter,
                'price_filter' => $price_filter,
                'sort' => $sort,
                'filter_title' => $filter_title,
                //is_attribute' => $is_attr,
                'filter_filter' => $filter_filter,
                'filter_id' => $filter_id,
                'order' => $order
            );
            
            $totalProducts = $this->model_webservices_category->getTotalProducts($filterDataTemp);
            $filterData = array(
                'start' => ($pageNumber - 1) * $itemCount,
                'limit' => $itemCount
            );
            
            $filterData = array_merge($filterDataTemp, $filterData);
            $results = $this->model_webservices_category->getProducts($filterData);
        } else {
            $filterDataTemp = array(
                'filter_category_id' => $categoryID,
                'sort' => $sort,
                'order' => $order
            );

            $totalProducts = $this->model_catalog_product->getTotalProducts($filterDataTemp);

            $filterData = array(
                'start' => ($pageNumber - 1) * $itemCount,
                'limit' => $itemCount
            );

            $filterData = array_merge($filterDataTemp, $filterData);
            $results = $this->model_catalog_product->getProducts($filterData);
        }
        if (!empty($results)) {
            $module_info = $this->config->get('webservice');
            if (!empty($module_info)) {
                $getModulesSettingArray = $module_info;
                $width = $getModulesSettingArray['category_image_width'];
                $height = $getModulesSettingArray['category_image_height'];
            } else {
                $width = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_width'));
                $height = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_height'));
            }

            foreach ($results as $result) {
                $productInfo = $this->model_catalog_product->getProduct($result['product_id']);
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
                        'category_name' => isset($parent_category['name']) ? html_entity_decode($parent_category['name']) : '',
                        'ClickActivityName' => 'CategoryActivity',
                        'category_id' => isset($parent_category['category_id']) ? $parent_category['category_id'] : '',
                        'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                        'src' => $image?str_replace(" ","%20",$image):"",
                        'discount_price' => $discount_price,
                        'discount_percentage' => $discount_percentage,
                        'is_in_wishlist' => $this->isInWishlist($productInfo['product_id']),
                    );
                }
            }
        }
        return $data;
    }

    //Function definition to get Filters for Category Page
    public function appGetFilters() {
        //Response Array
        $json = array();
        $this->load->model('webservices/home');
        $this->load->language('webservices/config');
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            $this->model_webservices_home->logKnowbandAppRequest("getFiltersForCategory", json_encode($json));
            die;
        } else {
            $json['install_module'] = '';
        }

        //Load models

        $this->load->model('catalog/product');
        $this->load->model('webservices/image');
        $this->load->model('webservices/category');
        $this->load->language('webservices/config');

        $json['filter_result'] = array();

        if (!empty($this->request->post['category_id'])) {

            $filterDataTemp = array(
                'filter_category_id' => $this->request->post['category_id']
            );

            $arrIndex = 0;
            if (iconv("UTF-8","UTF-8//IGNORE",$this->language->get('use_brand_filter')) == '1') {
                $manufacturerFilters = $this->model_webservices_category->getManufacturersByCategory($this->request->post['category_id']);
                if (!empty($manufacturerFilters)) {
                    //Manufacturer Filters
                    $json['filter_result'][$arrIndex] = array(
                        'id' => 'Brands',
                        'name' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_brands')),
                        'title' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_brands')),
                        'is_color_group' => '0',
                        'choice_type' => 'multiple',
                        'items' => array()
                    );

                    foreach ($manufacturerFilters as $manufacturerFilter) {
                        $json['filter_result'][$arrIndex]['items'][] = array(
                            'id' => $manufacturerFilter['manufacturer_id'],
                            'name' => $manufacturerFilter['name'],
                            'color_value' => "",
                        );
                    }
                    $arrIndex++;
                }
            }

            if (iconv("UTF-8","UTF-8//IGNORE",$this->language->get('use_price_filter')) == '1') {
                $priceFilters = $this->model_webservices_category->getProductPricesByCategory($this->request->post['category_id']);
                if (!empty($priceFilters)) {
                    //Product Price Filter
                    $json['filter_result'][$arrIndex] = array(
                        'id' => 'Prices',
                        'name' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_prices')),
                        'title' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_prices')),
                        'is_color_group' => '0',
                        'choice_type' => 'multiple',
                        'items' => array()
                    );

                    foreach ($priceFilters as $priceFilter) {
                        $priceRange = explode("|", $priceFilter);

                        $json['filter_result'][$arrIndex]['items'][] = array(
                            'id' => $priceFilter,
                            'color_value' => "",
                            'name' => $this->currency->format($priceRange[0], $this->session->data['currency']) . ' - ' . $this->currency->format($priceRange[1], $this->session->data['currency'])
                        );
                    }
                }
            }

            if (iconv("UTF-8","UTF-8//IGNORE",$this->language->get('use_option_filter')) == '1') {
                $results = $this->model_catalog_product->getProducts($filterDataTemp);
                if (!empty($results)) {
                    $product_option_value_data = array();
                    foreach ($results as $result) {
                        foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $option) {
                            if (!$this->search_array($option['name'], $json['filter_result'])) {
                                $json['filter_result'][$option['name']] = array(
                                    'id' => $option['option_id'],
                                    'name' => $option['option_id'],
                                    'title' => html_entity_decode($option['name']),
                                    'is_color_group' => '0',
                                    'choice_type' => 'multiple',
                                    'items' => array()
                                );
                            }
                            foreach ($option['product_option_value'] as $option_value) {
                                if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                    if (!$this->search_array($option_value['name'], $json['filter_result'][$option['name']])) {
                                        $json['filter_result'][$option['name']]['items'][] = array(
                                            'id' => $option_value['option_value_id'],
                                            'name' => html_entity_decode($option_value['name']),
                                            'color_value' => "",
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            } else if (iconv("UTF-8","UTF-8//IGNORE",$this->language->get('use_default_filter')) == '1') {
                $results = $this->model_catalog_product->getProducts($filterDataTemp);
                if (!empty($results)) {
                    foreach ($results as $result) {
                        foreach ($this->model_webservices_category->getFilterDescriptions($result['product_id']) as $option) {
                            if (!$this->search_array($option['name'], $json['filter_result'])) {
                                $json['filter_result'][$option['name']] = array(
                                    'id' => $option['option_id'],
                                    'name' => $option['option_id'],
                                    'title' => html_entity_decode($option['name']),
                                    'is_color_group' => '0',
                                    'choice_type' => 'multiple',
                                    'items' => array()
                                );
                            }
                            foreach ($option['filter_description'] as $option_value) {
                                if (!$this->search_array($option_value['name'], $json['filter_result'][$option['name']])) {
                                    $json['filter_result'][$option['name']]['items'][] = array(
                                        'id' => $option['filter_id'],
                                        'name' => html_entity_decode($option_value['name']),
                                        'color_value' => "",
                                    );
                                }
                            }
                        }
                    }
                }
            }
            // filter product attributes
            
            $results = $this->model_catalog_product->getProducts($filterDataTemp);
            if (!empty($results)) {
                foreach ($results as $result) {
                    foreach ($this->model_webservices_category->getFilterAttributes($result['product_id']) as $option) {
                        if(!array_key_exists($option['name'],$json['filter_result'])) {
                            $json['filter_result'][$option['name']] = array(
                                'id' => $option['attribute_group_id'],
                                'name' => "attr",
                                'title' => html_entity_decode($option['name']),
                                'is_color_group' => '0',
                                'choice_type' => 'multiple',
                                'items' => array()
                            );
                        }
                        foreach ($option['attribute_description'] as $option_value) {
                            if(!$this->search_array($option_value['name'],$json['filter_result'][$option['name']]['items'])) {
                                $json['filter_result'][$option['name']]['items'][] = array(
                                    'id' => $option['attribute_group_id'],
                                    'name' => html_entity_decode($option_value['name']),
                                    'color_value' => "",
                                );
                            }
                        }
                    }
                }
            }
            
        } else if (!empty($this->request->post['search_term'])) {
            $filterDataTemp = array(
                'filter_name' => html_entity_decode($this->request->post['search_term'])
            );
            $arrIndex = 0;
            $json['filter_result'] = array();

            if (iconv("UTF-8","UTF-8//IGNORE",$this->language->get('use_brand_filter')) == '1') {
                $manufacturerFilters = $this->model_webservices_category->getManufacturersBySearchTerm($this->request->post['search_term']);
                if (!empty($manufacturerFilters)) {
                    //Manufacturer Filters
                    $json['filter_result'][$arrIndex] = array(
                        'id' => 'Brands',
                        'name' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('brands')),
                        'title' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('brands')),
                        'is_color_group' => '0',
                        'choice_type' => 'multiple',
                        'items' => array()
                    );

                    foreach ($manufacturerFilters as $manufacturerFilter) {
                        $json['filter_result'][$arrIndex]['items'][] = array(
                            'id' => $manufacturerFilter['manufacturer_id'],
                            'name' => html_entity_decode($manufacturerFilter['name']),
                            'color_value' => "",
                        );
                    }
                    $arrIndex++;
                }
            }
            if (iconv("UTF-8","UTF-8//IGNORE",$this->language->get('use_price_filter')) == '1') {
                $priceFilters = $this->model_webservices_category->getProductPricesBySearchTerm($this->request->post['search_term']);
                if (!empty($priceFilters)) {
                    //Product Price Filter
                    $json['filter_result'][$arrIndex] = array(
                        'id' => 'Prices',
                        'name' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('prices')),
                        'title' => iconv("UTF-8","UTF-8//IGNORE",$this->language->get('prices')),
                        'is_color_group' => '0',
                        'choice_type' => 'multiple',
                        'items' => array()
                    );

                    foreach ($priceFilters as $priceFilter) {
                        $priceRange = explode("|", $priceFilter);

                        $json['filter_result'][$arrIndex]['items'][] = array(
                            'id' => $priceFilter,
                            'color_value' => "",
                            'name' => $this->currency->format($priceRange[0], $this->session->data['currency']) . ' - ' . $this->currency->format($priceRange[1], $this->session->data['currency'])
                        );
                    }
                }
            }
            if (iconv("UTF-8","UTF-8//IGNORE",$this->language->get('use_option_filter')) == '1') {
                $results = $this->model_catalog_product->getProducts($filterDataTemp);
                if (!empty($results)) {

                    $product_option_value_data = array();
                    foreach ($results as $result) {
                        foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $option) {
                            if (!$this->search_array($option['name'], $json['filter_result'])) {
                                $json['filter_result'][$option['name']] = array(
                                    'id' => $option['option_id'],
                                    'name' => $option['option_id'],
                                    'title' => html_entity_decode($option['name']),
                                    'is_color_group' => '0',
                                    'choice_type' => 'multiple',
                                    'items' => array()
                                );
                            }
                            foreach ($option['product_option_value'] as $option_value) {
                                if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                    if (!$this->search_array($option_value['name'], $json['filter_result'][$option['name']])) {
                                        $json['filter_result'][$option['name']]['items'][] = array(
                                            'id' => $option_value['option_value_id'],
                                            'name' => html_entity_decode($option_value['name']),
                                            'color_value' => "",
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            } else if (iconv("UTF-8","UTF-8//IGNORE",$this->language->get('use_default_filter')) == '1') {

                $results = $this->model_catalog_product->getProducts($filterDataTemp);
                if (!empty($results)) {
                    foreach ($results as $result) {
                        foreach ($this->model_webservices_category->getFilterDescriptions($result['product_id']) as $option) {
                            if (!$this->search_array($option['name'], $json['filter_result'])) {
                                $json['filter_result'][$option['name']] = array(
                                    'id' => $option['option_id'],
                                    'name' => $option['option_id'],
                                    'is_color_group' => '0',
                                    'title' => html_entity_decode($option['name']),
                                    'choice_type' => 'multiple',
                                    'items' => array()
                                );
                            }
                            foreach ($option['filter_description'] as $option_value) {
                                if (!$this->search_array($option_value['name'], $json['filter_result'][$option['name']])) {
                                    $json['filter_result'][$option['name']]['items'][] = array(
                                        'id' => $option['filter_id'],
                                        'name' => html_entity_decode($option_value['name']),
                                        'color_value' => "",
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        $json['filter_result'] = array_values($json['filter_result']);
        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("getFiltersForCategory", json_encode($json));
        $this->response->output();
        die;
    }
    
public function appGetSellerProducts() {
        
        $json = array();
        $this->load->model('webservices/home');
        $this->load->model('kbmp_marketplace/kbmp_marketplace');
        $this->load->model('localisation/zone');
        $this->load->model('localisation/country');
        
        $marketplace_setting = $this->config->get('kbmp_marketplace_setting');
       
        $json = array();
        
        $json['install_module']="";
        $json['status']="success";
        
        $pageNumber = !empty($this->request->post['page_number']) ? $this->request->post['page_number'] : '1';
        $itemCount = !empty($this->request->post['item_count']) ? $this->request->post['item_count'] : '10000';
        $sortBy = !empty($this->request->post['sort_by']) ? $this->request->post['sort_by'] : 'low';
        $category_id = !empty($this->request->post['filter_category_id']) ? $this->request->post['filter_category_id'] : '';
        $seller_id =  !empty($this->request->post['seller_id']) ? $this->request->post['seller_id'] : '';
        $orderBy =  !empty($this->request->post['order_by']) ? $this->request->post['order_by'] : 'pl.name';   
        
        $filter = array(
            'page_number' => $pageNumber,
            'item_count' => $itemCount,
            'sort' => $sortBy,
            'filter_category' => $category_id,
            'seller_id' => $seller_id,
            'order_by' => $orderBy,
        );
        
        $limit = array(
            'lower_limit'=>0,
            'upper_limit'=>1,
            'seller_id' => $seller_id,
        );
        
        $sellers_info = $this->model_webservices_home->getSellers($limit);
        
        
        $sellers_info = $sellers_info[0];
        $state =$this->model_localisation_zone->getZone($sellers_info['state']);
        $country = $this->model_localisation_country->getCountry($sellers_info['country_id']);
        
        if(!isset($state['name'])) {
            $state['name'] = "";
        }
        if(!isset($country['name'])) {
            $country['name'] = "";
        }
        if($sellers_info['logo']=="") {
            $sellers_info['logo'] = "profile-pic.png";
        }
        if($sellers_info['banner']=="") {
            $sellers_info['banner']="main_banner.jpg";
        }
        if(!$this->request->post['only_products']) {
            $json['seller_info'] = array(
                'seller_id' => $sellers_info['seller_id'],
                'name' => $sellers_info['firstname'].' '.$sellers_info['lastname'],
                'rating' => $this->model_webservices_home->getSellerRating($sellers_info['seller_id']),
                'Address' => $sellers_info['address'],
                'state_name' => $state['name'],
                'is_write_review_enabled' => $marketplace_setting['kbmp_enable_seller_review'],
                'return_policy' => $sellers_info['return_policy'],
                'shipping_policy' => $sellers_info['shipping_policy'],
                'country_name' => $country['name'],
                'logo' => HTTPS_SERVER.'image/sellers_logo/'.$this->imageUrlEncoding($sellers_info['logo']),
                'banner' => HTTPS_SERVER.'image/sellers_banner/'.$this->imageUrlEncoding($sellers_info['banner']),
            );
        }
        
        $json['seller_info']['filters']['sort'] = array(
                
            0=>array(  
                    "order_by" => "pl.name",
                    "order_way" => "asc",
                    "label" => "Name",
                    "order_way_label" => "A-Z"
                ),
            1=>array(  
                    "order_by"=> "pl.name",
                    "order_way"=> "desc",
                    "label"=> "Name",
                    "order_way_label"=> "Z-A"
                ),
            2=>array(  
                    "order_by"  => "p.price",
                    "order_way" => "asc",
                    "label" => "Price",
                    "order_way_label" => "Low to High"
                ),
            3=>array(  
                    "order_by"=> "p.price",
                    "order_way"=> "desc",
                    "label"=> "Price",
                    "order_way_label"=> "High to Low"
                )
        );
        $sellers_product_data = $this->model_webservices_home->getSellerProducts($filter,1);

        $json['seller_info']['products'] = array();
        $json['seller_info']['filters']['category'][] = array(
                'id_category' => 0,
                'name' => "ALL",
        );
        
        foreach($sellers_product_data as $seller_product_data) {

            $parent_category = $this->model_webservices_home->getParentCatName((int)$seller_product_data['product_id']);

            if(isset($parent_category['category_id'])) {
                $json['seller_info']['filters']['category'][] = array(
                    'id_category' => $parent_category['category_id'],
                    'name' => $parent_category['name'],
                ); 
            }

            $setting_data = $this->config->get('webservice');


            if ($seller_product_data['date_added'] >= $setting_data['new_product_start_date']) {
                $is_new = 1;
            } else {
                $is_new = 0;
            }
             $image = HTTPS_SERVER.'image/'.$this->imageUrlEncoding($seller_product_data['image']);

            //Check if webp image exists
            $info = pathinfo($image);
            if (isset($info['extension'])) {
                $directory = str_replace(HTTP_SERVER . 'image/cache/', DIR_IMAGE . 'cache/', $info['dirname']);
                if (file_exists($directory . '/' . $info['filename'] . '.webp')) {
                    $image = $info['dirname'] . '/' . $info['filename'] . '.webp';
                }
            }
            if (isset($seller_product_data['special'])) {
                if($seller_product_data['price']>0) {
                    $discount_price = $this->currency->format($this->tax->calculate($seller_product_data['special'], $seller_product_data['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $act_price = $this->tax->calculate($seller_product_data['price'], $seller_product_data['tax_class_id'], $this->config->get('config_tax'));
                    $disc_price = $this->tax->calculate($seller_product_data['special'], $seller_product_data['tax_class_id'], $this->config->get('config_tax'));
                    $discount_perc = (($act_price - $disc_price) / $act_price) * 100;
                    $discount_percentage = number_format($discount_perc, 2);
                } else{
                    $seller_product_data['price']=$seller_product_data['special'];
                    $discount_price = $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $discount_percentage = 0;
                }
            } else {
                $discount_price = $this->currency->format($this->tax->calculate($seller_product_data['price'], $seller_product_data['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                $discount_percentage = 0;
            }
            $json['seller_info']['products'][] = array(
                    'id' => $seller_product_data['product_id'],
                    'is_in_wishlist' => $this->isInWishlist($seller_product_data['product_id']),
                    'name' => html_entity_decode(str_replace('&quot;', '"', $seller_product_data['name'])),
                    'available_for_order' => 1,
                    'new_products' => $is_new,
                    'on_sale_products' => $discount_percentage ? 1 : 0,
                    'category_name' => html_entity_decode(isset($parent_category['name']) ? $parent_category['name'] : ''),
                    'ClickActivityName' => 'ProductActivity',
                    'category_id' => isset($parent_category['category_id']) ? $parent_category['category_id'] : '',
                    'price' => $this->currency->format($this->tax->calculate($seller_product_data['price'], $seller_product_data['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                    'src' => $image?str_replace(" ","%20",$image):"",
                    'discount_price' => $discount_price,
                    'discount_percentage' => $discount_percentage,
            );
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

    
    //Function definition to get products by search term
    private function getProductsBySearchTerm($searchTerm, $pageNumber, $itemCount, $sortBy, $filter, $setting_data) {

        $this->load->language('webservices/config');

        $data = array(
            'title' => 'Search Products',
            'products' => array()
        );

        $start = ($pageNumber - 1) * $itemCount;

        $sort = 'p.price';
        $order = 'ASC';
        if ($sortBy == 'high') {
            $sort = 'p.price';
            $order = 'DESC';
        } else if ($sortBy == 'low') {
            $sort = 'p.price';
            $order = 'ASC';
        }

        //Filter section
        $filter_id = array();
        $filter_title = array();
        $brand_filter = array();
        $price_filter = array();
        $filter_filter = array();

        //$filter = json_decode('{"filter_result":[{"title":"Brands","choice_type":"multiple","items":[{"id":"8","name":"Dupont"}]},{"title":"Size","choice_type":"multiple","items":[{"id":"48","name":"150 Ml "}]},{"title":"Color","choice_type":"multiple","items":[{"id":"46","name":"Red "}]},{"title":"Prices","choice_type":"multiple","items":[]}]}', true);
        if (!empty($filter)) {

            $filter = json_decode(str_replace('&quot;', '"', (trim($filter))), true);

            foreach ($filter['filter_result'] as $filter) {
                $temp = explode("|", $filter['title']);
                $filter['title'] = $temp[0];
                if (isset($filter['title']) && strtolower($filter['title']) == 'brands') {
                    if (!empty($filter['items'])) {
                        foreach ($filter['items'] as $items) {
                            $brand_filter[] = $items['id'];
                        }
                    }
                }
                if (isset($filter['title']) && strtolower($filter['title']) == 'prices') {
                    if (!empty($filter['items'])) {
                        foreach ($filter['items'] as $items) {
                            $price_filter[] = $items['id'];
                        }
                    }
                }

                if (isset($filter['title']) && strtolower($filter['title'])) {
                    if (!empty($filter['items'])) {
                        foreach ($filter['items'] as $items) {
                            if (isset($filter['title']) && (strtolower($filter['title']) != 'brands' && strtolower($filter['title']) != 'prices')) {
                                if (iconv("UTF-8","UTF-8//IGNORE",$this->language->get('use_option_filter')) == '1') {
                                    $filter_title[] = "'" . $filter['title'] . "'";
                                    $filter_id[] = $items['id'];
                                } else {
                                    $filter_filter[] = $items['id'];
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($brand_filter)) {
                $brand_filter = implode(",", $brand_filter);
            }
            if (!empty($price_filter)) {
                $price_filter = implode(",", $price_filter);
            }
            if (!empty($filter_title)) {
                $filter_title = implode(",", $filter_title);
            }
            if (!empty($filter_id)) {
                $filter_id = implode(",", $filter_id);
            }
            if (!empty($filter_filter)) {
                $filter_filter = implode(",", $filter_filter);
            }
            $filterDataTemp = array(
                'filter_name' => html_entity_decode($searchTerm),
                'brand_filter' => $brand_filter,
                'price_filter' => $price_filter,
                'sort' => $sort,
                'filter_title' => $filter_title,
                'filter_filter' => $filter_filter,
                'filter_id' => $filter_id,
                'order' => $order
            );
            $totalProducts = $this->model_webservices_category->getTotalProducts($filterDataTemp);
            $filterData = array(
                'start' => ($pageNumber - 1) * $itemCount,
                'limit' => $itemCount
            );

            $filterData = array_merge($filterDataTemp, $filterData);
            $results = $this->model_webservices_category->getProducts($filterData);
        } else {

            $filterDataTemp = array(
                'filter_name' => html_entity_decode($searchTerm),
                'sort' => $sort,
                'order' => $order
            );

            $totalProducts = $this->model_catalog_product->getTotalProducts($filterDataTemp);

            $filterData = array(
                'start' => ($pageNumber - 1) * $itemCount,
                'limit' => $itemCount
            );

            $filterData = array_merge($filterDataTemp, $filterData);
            $results = $this->model_catalog_product->getProducts($filterData);
        }

        $module_info = $this->config->get('webservice');
        if (!empty($module_info)) {
            $getModulesSettingArray = $module_info;
            $width = $getModulesSettingArray['category_image_width'];
            $height = $getModulesSettingArray['category_image_height'];
        } else {
            $width = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_width'));
            $height = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('image_height'));
        }

        if (!empty($results)) {
            foreach ($results as $result) {
                $productInfo = $this->model_catalog_product->getProduct($result['product_id']);
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
                        'category_name' => isset($parent_category['name']) ? html_entity_decode($parent_category['name']) : '',
                        'ClickActivityName' => 'CategoryActivity',
                        'category_id' => isset($parent_category['category_id']) ? $parent_category['category_id'] : '',
                        'price' => $this->currency->format($this->tax->calculate($productInfo['price'], $productInfo['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                        'src' => $image?str_replace(" ","%20",$image):"",
                        'discount_price' => $discount_price,
                        'discount_percentage' => $discount_percentage,
                        'is_in_wishlist' => $this->isInWishlist($productInfo['product_id']),
                    );
                }
            }
        }
        return $data;
    }

    private function search_array($needle, $haystack) {
        if (in_array($needle, $haystack)) {
            return true;
        }
        foreach ($haystack as $element) {
            if (is_array($element) && $this->search_array($needle, $element)) {
                return true;
            }
        }
        return false;
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
