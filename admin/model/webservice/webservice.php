<?php
class ModelWebserviceWebservice extends Model {
	public function addSlideshow($data) {		

		$this->db->query("INSERT INTO " . DB_PREFIX . "webservice_banner SET name = '" . $this->db->escape($data['slideshow_name']) . "', status = '" . (int)$data['slideshow_status'] . "', banner_limit = '" . (int)$data['slideshow_limit'] . "'");

		$slideshow_id = $this->db->getLastId();

		if (isset($data['slideshow_image'])) {
			foreach ($data['slideshow_image'] as $slideshow_image) {                                
				$this->db->query("INSERT INTO " . DB_PREFIX . "webservice_banner_image SET webservice_banner_id = '" . (int)$slideshow_id . "', link_type = '" .  $this->db->escape($slideshow_image['link_type']) . "', link_to = '" . $this->db->escape($slideshow_image['link_to']) . "', image = '" .  $this->db->escape($slideshow_image['image']) . "', sort_order = '" . (int)$slideshow_image['sort_order'] . "'");

				$slideshow_image_id = $this->db->getLastId();

				foreach ($slideshow_image['slideshow_image_description'] as $language_id => $slideshow_image_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "webservice_banner_image_description SET webservice_banner_image_id = '" . (int)$slideshow_image_id . "', language_id = '" . (int)$language_id . "', webservice_banner_id = '" . (int)$slideshow_id . "', title = '" .  $this->db->escape($slideshow_image_description['title']) . "'");
				}
			}
		}		

		return $slideshow_id;
	}
        public function getHomeLayoutModules($layout_id, $code) {
        
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_module WHERE layout_id = '" . (int) $layout_id . "' and code like '%" . $code . "%' ORDER BY sort_order");
            return $query->rows;
        }
        
        public function editSlideshow($slideshow_id, $data) {		

		$this->db->query("UPDATE " . DB_PREFIX . "webservice_banner SET name = '" . $this->db->escape($data['slideshow_name']) . "', status = '" . (int)$data['slideshow_status'] . "', banner_limit = '" . (int)$data['slideshow_limit'] . "' WHERE webservice_banner_id = '" . (int)$slideshow_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "webservice_banner_image WHERE webservice_banner_id = '" . (int)$slideshow_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "webservice_banner_image_description WHERE webservice_banner_id = '" . (int)$slideshow_id . "'");

		if (isset($data['slideshow_image'])) {
			foreach ($data['slideshow_image'] as $slideshow_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "webservice_banner_image SET webservice_banner_id = '" . (int)$slideshow_id . "', link_type = '" .  $this->db->escape($slideshow_image['link_type']) . "', link_to = '" . $this->db->escape($slideshow_image['link_to']) . "', image = '" .  $this->db->escape($slideshow_image['image']) . "', sort_order = '" . (int)$slideshow_image['sort_order'] . "'");

				$slideshow_image_id = $this->db->getLastId();

				foreach ($slideshow_image['slideshow_image_description'] as $language_id => $slideshow_image_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "webservice_banner_image_description SET webservice_banner_image_id = '" . (int)$slideshow_image_id . "', language_id = '" . (int)$language_id . "', webservice_banner_id = '" . (int)$slideshow_id . "', title = '" .  $this->db->escape($slideshow_image_description['title']) . "'");
				}
			}
		}
		
	}
        
        public function getSlideshow($slideshow_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "webservice_banner WHERE webservice_banner_id = '" . (int)$slideshow_id . "'");

		return $query->row;
	}
        
        public function getSlideshowImages($slideshow_id) {
		$slideshow_image_data = array();

		$slideshow_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "webservice_banner_image WHERE webservice_banner_id = '" . (int)$slideshow_id . "' ORDER BY sort_order ASC");

		foreach ($slideshow_image_query->rows as $slideshow_image) {
			$slideshow_image_description_data = array();

			$slideshow_image_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "webservice_banner_image_description WHERE webservice_banner_image_id = '" . (int)$slideshow_image['webservice_banner_image_id'] . "' AND webservice_banner_id = '" . (int)$slideshow_id . "'");

			foreach ($slideshow_image_description_query->rows as $slideshow_image_description) {
				$slideshow_image_description_data[$slideshow_image_description['language_id']] = array('title' => $slideshow_image_description['title']);
			}

			$slideshow_image_data[] = array(
				'slideshow_image_description' => $slideshow_image_description_data,
                                'link_type'                    => $slideshow_image['link_type'],
				'link_to'                      => $slideshow_image['link_to'],
				'image'                       => $slideshow_image['image'],
				'sort_order'                  => $slideshow_image['sort_order']
			);
		}

		return $slideshow_image_data;
	}                
        
        public function addBanner($data) {		

		$this->db->query("INSERT INTO " . DB_PREFIX . "webservice_banner SET name = '" . $this->db->escape($data['banner_name']) . "', status = '" . (int)$data['banner_status'] . "', banner_limit = '" . (int)$data['banner_limit'] . "'");

		$banner_id = $this->db->getLastId();

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $banner_image) {                                
				$this->db->query("INSERT INTO " . DB_PREFIX . "webservice_banner_image SET webservice_banner_id = '" . (int)$banner_id . "', link_type = '" .  $this->db->escape($banner_image['link_type']) . "', link_to = '" . $this->db->escape($banner_image['link_to']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', sort_order = '" . (int)$banner_image['sort_order'] . "'");

				$banner_image_id = $this->db->getLastId();

				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "webservice_banner_image_description SET webservice_banner_image_id = '" . (int)$banner_image_id . "', language_id = '" . (int)$language_id . "', webservice_banner_id = '" . (int)$banner_id . "', title = '" .  $this->db->escape($banner_image_description['title']) . "'");
				}
			}
		}		

		return $banner_id;
	}
        
        public function editBanner($banner_id, $data) {		

		$this->db->query("UPDATE " . DB_PREFIX . "webservice_banner SET name = '" . $this->db->escape($data['banner_name']) . "', status = '" . (int)$data['banner_status'] . "', banner_limit = '" . (int)$data['banner_limit'] . "' WHERE webservice_banner_id = '" . (int)$banner_id . "'");
                
		$this->db->query("DELETE FROM " . DB_PREFIX . "webservice_banner_image WHERE webservice_banner_id = '" . (int)$banner_id . "'");                
		$this->db->query("DELETE FROM " . DB_PREFIX . "webservice_banner_image_description WHERE webservice_banner_id = '" . (int)$banner_id . "'");

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $banner_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "webservice_banner_image SET webservice_banner_id = '" . (int)$banner_id . "', link_type = '" .  $this->db->escape($banner_image['link_type']) . "', link_to = '" . $this->db->escape($banner_image['link_to']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', sort_order = '" . (int)$banner_image['sort_order'] . "'");

				$banner_image_id = $this->db->getLastId();

				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "webservice_banner_image_description SET webservice_banner_image_id = '" . (int)$banner_image_id . "', language_id = '" . (int)$language_id . "', webservice_banner_id = '" . (int)$banner_id . "', title = '" .  $this->db->escape($banner_image_description['title']) . "'");
				}
			}
		}
		
	}
        
        public function getBanner($banner_id) {
		$query2 = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "webservice_banner WHERE webservice_banner_id = '" . (int)$banner_id . "'");

		return $query2->row;
	}
        
        public function getBannerImages($banner_id) {
		$banner_image_data = array();

		$banner_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "webservice_banner_image WHERE webservice_banner_id = '" . (int)$banner_id . "' ORDER BY sort_order ASC");

		foreach ($banner_image_query->rows as $banner_image) {
			$banner_image_description_data = array();

			$banner_image_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "webservice_banner_image_description WHERE webservice_banner_image_id = '" . (int)$banner_image['webservice_banner_image_id'] . "' AND webservice_banner_id = '" . (int)$banner_id . "'");

			foreach ($banner_image_description_query->rows as $banner_image_description) {
				$banner_image_description_data[$banner_image_description['language_id']] = array('title' => $banner_image_description['title']);
			}

			$banner_image_data[] = array(
				'banner_image_description' => $banner_image_description_data,
                                'link_type'                    => $banner_image['link_type'],
				'link_to'                      => $banner_image['link_to'],
				'image'                       => $banner_image['image'],
				'sort_order'                  => $banner_image['sort_order']
			);
		}

		return $banner_image_data;
	}   
        
        public function addCmsData($data){
            $received_data= $this->getCmsPages();
            if(empty($received_data)){                        
                foreach ($data as $key => $value) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "webservice_cms_page_data SET cms_row_id='".(int)$value['unique_info_id']."' , link_to = '" . (int)@$value['link_to'] . "' , status='".(int)$value['status']."', sort_order='".(int)$value['sort_order']."' , date_added=NOW()");                    
                    foreach ($value['cms_title'] as $key2 => $value2) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "webservice_cms_page_title SET cms_row_id='".(int)$value['unique_info_id']."' , language_id = '" . (int)($key2) . "', title = '" . $this->db->escape($value2['title']) . "', date_added=NOW()");
                    }                    
                }               
            } else {
                $delete_title_query= $this->db->query("delete from " . DB_PREFIX . "webservice_cms_page_title");
                $delete_data_query= $this->db->query("delete from " . DB_PREFIX . "webservice_cms_page_data");
                foreach ($data as $key => $value) {
                    if(!isset($value['link_to'])){
                        $value['link_to'] = 0;
                    }
                    $this->db->query("INSERT INTO " . DB_PREFIX . "webservice_cms_page_data SET cms_row_id='".(int)$value['unique_info_id']."' , link_to = '" . (int)$value['link_to'] . "' , status='".(int)$value['status']."', sort_order='".(int)$value['sort_order']."' , date_added=NOW()");                    
                    foreach ($value['cms_title'] as $key2 => $value2) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "webservice_cms_page_title SET cms_row_id='".(int)$value['unique_info_id']."' , language_id = '" . (int)($key2) . "', title = '" . $this->db->escape($value2['title']) . "', date_added=NOW()");
                    }                    
                }                
            }
        }

        
        public function getCmsPages(){            
            $cms_title_data = array();
            $cms_data = array();
                $cms_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "webservice_cms_page_data");    
                foreach ($cms_query->rows as $cms_row_data=> $cms_data_value) {
                            $cms_title_query= $this->db->query("SELECT * FROM " . DB_PREFIX . "webservice_cms_page_title where cms_row_id='".(int)$cms_data_value['cms_row_id']."' ");                                
                            foreach ($cms_title_query->rows as $cms_title_row=>$cms_title_value ) {                                                            
				$cms_title_data[$cms_title_value['language_id']] = array('title' => $cms_title_value['title']);
                            }
			$cms_data[$cms_data_value['cms_row_id']] = array(
                                'cms_row_id' => $cms_data_value['cms_row_id'],
				'cms_title' => $cms_title_data,                                
				'link_to'   => $cms_data_value['link_to'],
                                'status'    => $cms_data_value['status'],				
				'sort_order'=> $cms_data_value['sort_order']
			);
                }            
            return $cms_data;
        }
        
        public function getNotificationHistory($data) {
            $where = '';
            $sql = "SELECT * FROM " . DB_PREFIX . "push_notification_history WHERE notification_id > 0 ";
            if (isset($data['filter_title']) && $data['filter_title'] != '') {
                $sql .= " AND title like '%" . $data['filter_title'] . "%' ";
            }
            if (isset($data['filter_redirect_activity']) && $data['filter_redirect_activity'] != '') {
                $sql .= " AND redirect_activity = '" . $data['filter_redirect_activity'] . "'";
            }
            if (isset($data['filter_from_date']) && $data['filter_from_date'] != '') {
                $sql .= " AND date_added >= '" . $data['filter_from_date'] . "'";
            }
            if (isset($data['filter_to_date']) && $data['filter_to_date'] != '') {
                $sql .= " AND date_added <= '" . $data['filter_to_date'] . "'";
            }
            $sql .= ' ORDER BY date_added DESC';
            $query = $this->db->query($sql);
            return $query->rows;
        }
        
        public function saveNotification($data) {
            $sql = "INSERT INTO " . DB_PREFIX . "push_notification_history SET title ='" . $data['title'] . "', message = '" . $data['message'] . "', device = '" . $data['device'] . "', image_url = '" . $data['image'] . "', redirect_activity = '" . $data['redirect_activity'] . "', category_id = '" . $data['category'] . "', product_id = '" . $data['product'] . "', date_added = '" . $data['date_added'] . "'";
            $this->db->query($sql);
        }
        
        public function getFcmData(){
            $query = $this->db->query('SELECT distinct(fcm_id) FROM ' . DB_PREFIX . 'fcm_details');
            if ($query->num_rows) {
                return $query->rows;
            }else{
                return false;
            }
        }
        
        public function getHomePageLayouts(){
            $query = $this->db->query('SELECT * FROM ' . DB_PREFIX . 'kb_mobileapp_layouts');
            if ($query->num_rows) {
                return $query->rows;
            }else{
                return false;
            }
        }
        
        public function getComponentHeading($layout_id,$component_id){
            $query = $this->db->query('SELECT heading,language_id FROM ' . DB_PREFIX .'kbb_components_heading WHERE id_layout = '.$layout_id.' and id_component = '.$component_id.'');
            if ($query->num_rows) {
                return $query->rows;
            }else{
                return false;
            }
        }
        
        public function saveComponent($data){
            $position = $this->getLastPosition($data['id_layout']);
            $component_type_id = $this->getComponentIdByType($data['component_type']);
            $this->db->query('INSERT INTO '. DB_PREFIX . 'kb_mobileapp_layout_component (id_layout,id_component_type,position) VALUES ("'.$data['id_layout'].'","'.$component_type_id.'","'.$position.'")');
            return $this->db->getLastId();
            
        }
        public function saveBannerSquareData($data){
            $product_id = 0;
            $category_id = 0;
            if($data['redirect_activity']==2) {
                $product_id =  $data['link_to'];
            } else if($data['redirect_activity']==1) {
                $category_id = $data['link_to'];
            }
            $redirect_id = $data['redirect_activity'];
            $this->db->query('INSERT INTO '. DB_PREFIX . 'kbmobileapp_banners (id_component,id_banner_type,product_id,category_id,redirect_activity,image_url,image_content_mode) VALUES ("'.$data['id_component'].'",0,"'.$product_id.'","'.$category_id.'","'.$redirect_id.'","'.$data['image_url'].'","'.$data['image_content_mode'].'")');
            return $this->db->getLastId();
        }
        
        public function saveBannerCountdownData($data){
            $product_id = 0;
            $category_id = 0;
            if($data['redirect_activity']==2) {
                $product_id =  $data['link_to'];
            } else if($data['redirect_activity']==1) {
                $category_id = $data['link_to'];
            }
            $redirect_id = $data['redirect_activity'];
            $this->db->query('INSERT INTO '. DB_PREFIX . 'kbmobileapp_banners (id_component,id_banner_type,countdown,product_id,category_id,redirect_activity,image_url,image_content_mode,background_color,text_color,is_enabled_background_color) VALUES ("'.$data['id_component'].'",0,"'.$data['bc_countdown_validity'].'","'.$product_id.'","'.$category_id.'","'.$redirect_id.'","'.$data['image_url'].'","'.$data['image_content_mode'].'","'.$data['bc_background_color'].'","'.$data['bc_text_color'].'","'.$data['bc_back_color_status'].'")');
            return $this->db->getLastId();
        }
        
        
        public function saveProductData($data){
            if(!isset($data['category_id'])) {
                $data['category_id'] = 0;
            }
            if(!isset($data['product_list'])){
                $data['product_list']="";
            }
            $this->db->query('DELETE FROM '. DB_PREFIX . 'kbmobileapp_product_data WHERE id_component = "'.$data['id_component'].'" ');
            $this->db->query('INSERT INTO '. DB_PREFIX . 'kbmobileapp_product_data (id_component,product_type,category_products,custom_products,image_content_mode,number_of_products,id_category) VALUES ("'.$data['id_component'].'","'.$data['product_type'].'","'.$data['category_products'].'","'.$data['product_list'].'","'.$data['image_content_mode'].'","'.$data['number_of_product'].'","'.$data['category_id'].'")');
        }
        
        public function saveTopCategoryData($data){
            
            $this->db->query('DELETE FROM '. DB_PREFIX . 'kbmobileapp_top_category WHERE id_component = "'.$data['id_component'].'" ');
            
            $this->db->query('INSERT INTO '. DB_PREFIX . 'kbmobileapp_top_category (id_component,id_category,image_url,image_content_mode) VALUES ("'.$data['id_component'].'","'.$data['id_category_1'].'","'.$data['image_1'].'","'.$data['image_content_mode'].'")');
            $this->db->query('INSERT INTO '. DB_PREFIX . 'kbmobileapp_top_category (id_component,id_category,image_url,image_content_mode) VALUES ("'.$data['id_component'].'","'.$data['id_category_2'].'","'.$data['image_2'].'","'.$data['image_content_mode'].'")');
            $this->db->query('INSERT INTO '. DB_PREFIX . 'kbmobileapp_top_category (id_component,id_category,image_url,image_content_mode) VALUES ("'.$data['id_component'].'","'.$data['id_category_3'].'","'.$data['image_3'].'","'.$data['image_content_mode'].'")');
            $this->db->query('INSERT INTO '. DB_PREFIX . 'kbmobileapp_top_category (id_component,id_category,image_url,image_content_mode) VALUES ("'.$data['id_component'].'","'.$data['id_category_4'].'","'.$data['image_4'].'","'.$data['image_content_mode'].'")');
            if(isset($data['image_5'])) {
                $this->db->query('INSERT INTO '. DB_PREFIX . 'kbmobileapp_top_category (id_component,id_category,image_url,image_content_mode) VALUES ("'.$data['id_component'].'","'.$data['id_category_5'].'","'.$data['image_5'].'","'.$data['image_content_mode'].'")');
            }
            if(isset($data['image_6'])) {
                $this->db->query('INSERT INTO '. DB_PREFIX . 'kbmobileapp_top_category (id_component,id_category,image_url,image_content_mode) VALUES ("'.$data['id_component'].'","'.$data['id_category_6'].'","'.$data['image_6'].'","'.$data['image_content_mode'].'")');
            }
            if(isset($data['image_7'])) {
                $this->db->query('INSERT INTO '. DB_PREFIX . 'kbmobileapp_top_category (id_component,id_category,image_url,image_content_mode) VALUES ("'.$data['id_component'].'","'.$data['id_category_7'].'","'.$data['image_7'].'","'.$data['image_content_mode'].'")');
            }
            if(isset($data['image_8'])) {
                $this->db->query('INSERT INTO '. DB_PREFIX . 'kbmobileapp_top_category (id_component,id_category,image_url,image_content_mode) VALUES ("'.$data['id_component'].'","'.$data['id_category_8'].'","'.$data['image_8'].'","'.$data['image_content_mode'].'")');
            }
            return true;
        }
        
        public function getComponents($layout_id){
         
            $query = $this->db->query('SELECT * FROM '. DB_PREFIX . 'kb_mobileapp_layout_component where id_layout = '.$layout_id.' ORDER BY position ASC');
            return $query->rows;
            
        }
        
        public function getLastPosition($layout_id) {
            $query = $this->db->query('SELECT MAX(position) as max FROM ' . DB_PREFIX . 'kb_mobileapp_layout_component WHERE id_layout = '.$layout_id.'');
            if ($query->num_rows > 0) {
                return $query->row['max']+1;
            }else{
                return 0;
            }
        }
        
        public function updateLayoutPosition($pos,$id_layout,$id_component) {
            $this->db->query('UPDATE ' . DB_PREFIX . 'kb_mobileapp_layout_component SET position="'.$pos.'" WHERE id_layout = "'.$id_layout.'" AND id_component = "'.$id_component.'"');
        }
        
        public function getBannerByComponent($layout_id,$component_id) {
            
        $query = $this->db->query('SELECT kb.* FROM '.DB_PREFIX.'kb_mobileapp_layout_component klc,'.DB_PREFIX.'kbmobileapp_banners kb,'.DB_PREFIX.'kbmobileapp_component_types kct  WHERE kct.id = klc.id_component_type and klc.id_component = kb.id_component AND klc.id_layout ="'.$layout_id.'" and kb.id_component ="'.$component_id.'" AND (kct.component_name = "banner_square" OR kct.component_name = "banner_horizontal_slider" OR kct.component_name = "banners_grid" OR kct.component_name = "banners_countdown")');
            if ($query->num_rows > 0) {
                return $query->rows;
            }
        }
        public function getProductsByComponent($layout_id,$component_id) {
            $query = $this->db->query('SELECT * FROM ' . DB_PREFIX . 'kb_mobileapp_layout_component klc,' . DB_PREFIX . 'kbmobileapp_product_data kp,' . DB_PREFIX . 'kbmobileapp_component_types kct  WHERE kct.id = klc.id_component_type and klc.id_component = kp.id_component  and (kct.component_name = "products_square" OR kct.component_name = "products_horizontal" OR kct.component_name = "products_recent" OR kct.component_name = "products_grid") AND klc.id_layout ="'.$layout_id.'" and kp.id_component ="'.$component_id.'" ');
            if ($query->num_rows > 0) {
                return $query->rows;
            }
        }
        
        public function getComponentIdByType($type) {
            $query = $this->db->query('SELECT id  FROM ' . DB_PREFIX . 'kbmobileapp_component_types WHERE component_name="'.$type.'"');
            if ($query->num_rows > 0) {
                return $query->row['id'];
            }else{
                return 1;
            }
        }
        public function getTopCategoryData($component_id) {
            $query = $this->db->query('SELECT *  FROM ' . DB_PREFIX . 'kbmobileapp_top_category WHERE id_component="'.$component_id.'" ORDER BY id ASC');
            if ($query->num_rows > 0) {
                return $query->rows;
            }else{
                return 1;
            }
        }
        
        
        
        public function getComponentTypeByID($component_type_id) {
            $query = $this->db->query('SELECT component_name  FROM ' . DB_PREFIX . 'kbmobileapp_component_types WHERE id='.$component_type_id.'');
            if ($query->num_rows > 0) {
                return $query->row['component_name'];
            }else{
                return 1;
            }
        }
        
        public function getBestSellerProducts($limit) {
		$product_data = $this->cache->get('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);
        
		if (!$product_data) {
			$product_data = array();
        
			$query = $this->db->query("SELECT op.product_id, SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int)$limit);
                        $this->load->model('catalog/product');
			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
}

			$this->cache->set('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}
        
}
