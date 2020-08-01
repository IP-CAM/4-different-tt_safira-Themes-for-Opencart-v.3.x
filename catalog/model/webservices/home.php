<?php 
class ModelWebservicesHome extends Model {

    public function getHomeLayoutModules($layout_id, $code) {

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_module WHERE layout_id = '" . (int) $layout_id . "' and code like '%" . $code . "%' ORDER BY sort_order");
        return $query->rows;
    }

    public function getAppInformation($information_id,$language_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$language_id . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");

        return $query->row;
    }
    
    public function getSellerInfoByProduct($product_id) {
        $query = $this->db->query("SELECT c.customer_id, c.firstname, c.lastname, c.email, ksd.title as shop, ks.seller_id, ks.active, ks.date_added FROM " . DB_PREFIX . "kb_mp_seller ks LEFT JOIN " . DB_PREFIX . "kb_mp_seller_details ksd ON (ks.seller_id = ksd.seller_id) INNER JOIN " . DB_PREFIX . "customer c ON (c.customer_id = ks.customer_id) INNER JOIN " . DB_PREFIX . "kb_mp_seller_product ksp ON (ksp.seller_id = ks.seller_id) WHERE ks.approved = '1' and ksp.product_id ='" . $product_id . "'");
        if ($query->num_rows > 0) {
            return array(
                'seller_id' => $query->row['seller_id'],
                'name' => $query->row['firstname'] . ' ' . $query->row['lastname'],
                'rating' => $this->getSellerRating($query->row['seller_id'])
            );
        } else {
            return array();
        }
    }

    public function getSellers($data) {
        $sql = "SELECT ks.*,ksd.*,kc.*  FROM " . DB_PREFIX . "kb_mp_seller ks LEFT JOIN " . DB_PREFIX . "kb_mp_seller_details ksd ON (ks.seller_id = ksd.seller_id) INNER JOIN " . DB_PREFIX . "customer kc ON (kc.customer_id = ks.customer_id) WHERE ksd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND ks.approved = '1' AND ks.active = '1'";

        if (isset($data['seller_id'])) {
            $sql .= " AND ks.seller_id = " . $data['seller_id'] . "";
        }

        if (isset($data['lower_limit'])) {
            $sql .= " LIMIT " . $data['lower_limit'] . "," . $data['upper_limit'] . "";
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getSellerRating($seller_id) {
        $query = $this->db->query("SELECT AVG(rating) as rating FROM " . DB_PREFIX . "kb_mp_seller_review WHERE seller_id = '" . (int) $seller_id . "'");
        return $query->row['rating'];
    }

    public function getLayoutModulesSetting($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "module WHERE code = '" . $code . "'");
		
		return $query->rows;
	}
    
    public function checkBannerStatus($id){
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner WHERE banner_id = '" . $id . "'");
		
        return $query->rows;
    }
    
    public function checkSlideshowBannerStatus($id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "webservice_banner WHERE webservice_banner_id = '" . $id . "'");
        $slideshow_data= array(
          "webservice_banner_id" => $query->row['webservice_banner_id'],
          "name" => $query->row['name'],
          "status" => $query->row['status'],
          "banner_limit" => $query->row['banner_limit'] 
        );
        return $slideshow_data;
    }
    
    public function getSlideshowBannerImageData($id) {
        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "webservice_banner_image wbi inner join " . DB_PREFIX . "webservice_banner_image_description wbid on wbi.webservice_banner_image_id=wbid.webservice_banner_image_id WHERE wbi.webservice_banner_id = '" . $id . "' and wbid.language_id = '".(int)$this->config->get('config_language_id')."' order by sort_order");
    
        return $query->rows;
    }
    
    public function getInformation(){
        
        $query= $this->db->query("SELECT * FROM " . DB_PREFIX . "webservice_cms_page_data wcpd inner join " . DB_PREFIX . "webservice_cms_page_title wcpt on wcpd.cms_row_id= wcpt.cms_row_id where wcpt.language_id='".(int)$this->config->get('config_language_id')."' order by sort_order");                    
        
        return $query->rows;
    }

    public function getCurrencies() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency where status=1 ORDER BY title ASC");
            foreach ($query->rows as $result) {
                    $currency_data['currency_list'][] = array(
                            'id_currency'   => $result['code'],
                            'name'         => $result['title'],
                            'currency_code'          => $result['code'],
                            'currency_symbol'   => $result['symbol_left'] ? $result['symbol_left']:$result['symbol_right']
                    );
            }		
        return $currency_data;    
    }

    public function getCustomerByPhone($phone) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer ct," . DB_PREFIX . "kbmobileapp_unique_verification uv WHERE uv.id_customer = ct.customer_id and  uv.mobile_number = '" . $phone . "'");
        return $query->row;
    }

    public function getLanguages() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language ORDER BY sort_order, name");

            foreach ($query->rows as $result) {
                    $language_data['lang_list'][] = array(
                            'id_lang'     => $result['language_id'],
                            'name'        => $result['name'],
                            'active'	  => $result['status'] ? "1":"0",
                            'iso_code'    => $result['code'],
                            'language_code'    => $result['locale'],
                            'directory'    => $result['directory'],
                            "date_format_lite" =>  "m/d/Y",
                            "date_format_full" => "m/d/Y H:i:s",
                            "is_rtl" => "0",
                            "id_shop" => $this->config->get('config_store_id'),
                            "shops" => array(),
                    );
            }
            return $language_data;
    }
    

    public function getLanguageCode($language_id){
        $query= $this->db->query("SELECT * FROM " . DB_PREFIX . "language where language_id='".(int)$language_id."'");
        if($query->num_rows>0){
            return $query->row['code'];
        }
    }
    
	 public function logKnowbandAppRequest($api_name, $response){
//		if (defined('KB_APP_Webservices_Log') && KB_APP_Webservices_Log == "1") {
		if (true) {
			$logfile = fopen(DIR_LOGS . "KB_App.log", "a+");
			$corelationId = $this->getCorelationId();
			$postParams = array();
                        if(isset($this->request->post)){
			foreach($this->request->post as $key => $value) {
				$postParams[$key] = $value;
                        } }
			$getParams = array();
			foreach($this->request->get as $key => $value) {
				$getParams[$key] = $value;
			}
			$message = date("Y-m-d H:i:s")."	".$corelationId."	".$api_name."	".$_SERVER["REMOTE_ADDR"]."	".$_SERVER["REQUEST_URI"]."	".json_encode($getParams)."	".json_encode($postParams)."	".$response."\n";
			fwrite($logfile, $message);
			fclose($logfile);
		}
	 }
	 
	 
	 public function getCorelationId() {
		$headers = $_SERVER;
		foreach ($headers as $header => $value) {
			if($header == "CorelationId") {
				return $value;
			}
		}
		return ""; 
	 }
         
        public function getParentCatName($product_id) {
            $query = $this->db->query("SELECT cd.category_id, cd.name FROM " . DB_PREFIX . "product_to_category p2c "
                    . "INNER JOIN " . DB_PREFIX . "category c On c.category_id= p2c.category_id "
                    . "INNER JOIN " . DB_PREFIX . "category_description cd ON cd.category_id=c.category_id "
                    . "WHERE p2c.product_id = '" . (int) $product_id . "' AND c.parent_id= 0");
            return $query->row;
        }
        
        public function addCustomer($data) {

                 if (isset($data['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($data['customer_group_id'], $this->config->get('config_customer_group_display'))) {
                         $customer_group_id = $data['customer_group_id'];
                 } else {
                         $customer_group_id = $this->config->get('config_customer_group_id');
                 }

                 $this->load->model('account/customer_group');

                 $customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

                 $this->db->query("INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$customer_group_id . "', store_id = '" . (int)$this->config->get('config_store_id') . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['account']) ? serialize($data['custom_field']['account']) : '') . "', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', newsletter = '" . (isset($data['newsletter']) ? (int)$data['newsletter'] : 0) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', status = '1', safe = '1',date_added = NOW()");

                 $customer_id = $this->db->getLastId();

                 $this->load->language('mail/register');

                 $subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));

                 $message = sprintf($this->language->get('text_welcome'), $this->config->get('config_name')) . "\n\n";

                 if (!$customer_group_info['approval']) {
                         $message .= $this->language->get('text_login') . "\n";
                 } else {
                         $message .= $this->language->get('text_approval') . "\n";
                         $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_approval` SET customer_id = '" . (int)$customer_id . "', type = 'customer', date_added = NOW()");
                 }

                 $message .= $this->url->link('account/login', '', 'SSL') . "\n\n";
                 $message .= $this->language->get('text_services') . "\n\n";
                 $message .= $this->language->get('text_thanks') . "\n";
                 $message .= $this->config->get('config_name');
                 
                 if($this->config->get('config_mail_engine')) {
                 
                     
                    $mail = new Mail($this->config->get('config_mail_engine'));
                    $mail->parameter = $this->config->get('config_mail_parameter');
                    $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                    $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                    $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                    $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                    $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                    $mail->setTo($data['email']);
                    $mail->setFrom($this->config->get('config_email'));
                    $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                    $mail->setSubject($subject);
                    $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                    $mail->send();
                 }

                 // Send to main admin email if new account email is enabled
                 if ($this->config->get('config_account_mail')) {
                         $message  = $this->language->get('text_signup') . "\n\n";
                         $message .= $this->language->get('text_website') . ' ' . $this->config->get('config_name') . "\n";
                         $message .= $this->language->get('text_firstname') . ' ' . $data['firstname'] . "\n";
                         $message .= $this->language->get('text_lastname') . ' ' . $data['lastname'] . "\n";
                         $message .= $this->language->get('text_customer_group') . ' ' . $customer_group_info['name'] . "\n";
                         $message .= $this->language->get('text_email') . ' '  .  $data['email'] . "\n";
                         $message .= $this->language->get('text_telephone') . ' ' . $data['telephone'] . "\n";

                         $mail->setTo($this->config->get('config_email'));
                         $mail->setSubject(html_entity_decode($this->language->get('text_new_customer'), ENT_QUOTES, 'UTF-8'));
                         $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                         $mail->send();

                         // Send to additional alert emails if new account email is enabled
                         $emails = explode(',', $this->config->get('config_mail_alert'));

                         foreach ($emails as $email) {
                                 if (utf8_strlen($email) > 0 && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
                                         $mail->setTo($email);
                                         $mail->send();
                                 }
                         }
                 }

        return $customer_id;
    }

    public function getSellerProducts($data, $is_seller = 0) {
        $sql = "SELECT p.product_id";

        $sql .= " FROM " . DB_PREFIX . "product p";

        $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

        if (!empty($data['seller_id'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "kb_mp_seller_product ksp ON (p.product_id = ksp.product_id)";
        }

        if (!empty($data['filter_category']) && $data['filter_category']!=0) {
            $sql .= " INNER JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = ksp.product_id and p2c.category_id = '".$data['filter_category']."')";
        }

        $sql .= " WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p2s.store_id = '" . (int) $this->config->get('config_store_id') . "'";

        if (!empty($data['seller_id'])) {
            $sql .= " AND ksp.seller_id = '" . (int) $data['seller_id'] . "' AND ksp.deleted = '0' AND ksp.store_id = '" . (int) $this->config->get('config_store_id') . "'";
            if (!$is_seller) {
                $sql .= " AND ksp.approved = '1'";
            }
        }


        $sql .= " GROUP BY p.product_id";

        $sort_data = array(
            'pl.name',
            'p.price',
        );
        
        if (isset($data['order_by']) && in_array($data['order_by'], $sort_data)) {
            if ($data['order_by'] == 'pl.name' || $data['order_by'] == 'p.model') {
                $sql .= " ORDER BY pd.name";
            } elseif ($data['order_by'] == 'p.price') {
                $sql .= " ORDER BY p.price";
            } else {
                $sql .= " ORDER BY " . $data['order_by'];
            }
        } else {
            $sql .= " ORDER BY p.sort_order";
        }

        if (isset($data['sort']) && ($data['sort'] == 'desc')) {
            $sql .= " DESC, LCASE(pd.name) DESC";
        } else {
            $sql .= " ASC, LCASE(pd.name) ASC";
        }

        if (isset($data['page_number']) && isset($data['item_count'])) {
            $data['start'] = ($data['page_number'] - 1) * $data['item_count'];
            $data['limit'] = ($data['page_number']) * $data['item_count'];
            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $product_data = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $product_data[$result['product_id']] = $this->getProduct($result['product_id']);
        }

        return $product_data;
    }

    public function getProduct($product_id) {
        $query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int) $this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int) $this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int) $this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int) $this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int) $this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int) $this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, (SELECT SUM(qty) AS qty_sold FROM " . DB_PREFIX . "kb_mp_seller_order_detail ksod WHERE ksod.product_id = p.product_id AND ksod.is_consider = '1' AND ksod.is_canceled = '0' GROUP BY ksod.product_id) AS qty_sold, (SELECT SUM(seller_earning) AS seller_earning FROM " . DB_PREFIX . "kb_mp_seller_order_detail ksod WHERE ksod.product_id = p.product_id AND ksod.is_consider = '1' AND ksod.is_canceled = '0' GROUP BY ksod.product_id) AS seller_earning, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int) $product_id . "' AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p2s.store_id = '" . (int) $this->config->get('config_store_id') . "'");

        if ($query->num_rows) {
            return array(
                'product_id' => $query->row['product_id'],
                'name' => $query->row['name'],
                'description' => $query->row['description'],
                'meta_title' => $query->row['meta_title'],
                'meta_description' => $query->row['meta_description'],
                'meta_keyword' => $query->row['meta_keyword'],
                'tag' => $query->row['tag'],
                'model' => $query->row['model'],
                'sku' => $query->row['sku'],
                'upc' => $query->row['upc'],
                'ean' => $query->row['ean'],
                'jan' => $query->row['jan'],
                'isbn' => $query->row['isbn'],
                'mpn' => $query->row['mpn'],
                'location' => $query->row['location'],
                'quantity' => $query->row['quantity'],
                'stock_status' => $query->row['stock_status'],
                'image' => $query->row['image'],
                'manufacturer_id' => $query->row['manufacturer_id'],
                'manufacturer' => $query->row['manufacturer'],
                'price' => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
                'special' => $query->row['special'],
                'reward' => $query->row['reward'],
                'points' => $query->row['points'],
                'tax_class_id' => $query->row['tax_class_id'],
                'date_available' => $query->row['date_available'],
                'weight' => $query->row['weight'],
                'weight_class_id' => $query->row['weight_class_id'],
                'length' => $query->row['length'],
                'width' => $query->row['width'],
                'height' => $query->row['height'],
                'length_class_id' => $query->row['length_class_id'],
                'subtract' => $query->row['subtract'],
                'rating' => round($query->row['rating']),
                'reviews' => $query->row['reviews'] ? $query->row['reviews'] : 0,
                'minimum' => $query->row['minimum'],
                'sort_order' => $query->row['sort_order'],
                'status' => $query->row['status'],
                'date_added' => $query->row['date_added'],
                'date_modified' => $query->row['date_modified'],
                'viewed' => $query->row['viewed'],
                'qty_sold' => $query->row['qty_sold'],
                'seller_earning' => $query->row['seller_earning']
            );
        } else {
            return false;
        }
    }

}
