<?php

class ModelWebservicesCategory extends Model {

//Function definition to get manufacturers by category ID
    public function getManufacturersByCategory($categoryID) {
        if (!empty($categoryID)) {

            $query = $this->db->query("SELECT m.manufacturer_id, m.name FROM " . DB_PREFIX . "product p, " . DB_PREFIX . "product_to_category pc, " . DB_PREFIX . "category_path cp, " . DB_PREFIX . "manufacturer m WHERE pc.category_id = cp.category_id and cp.path_id = " . (int) $categoryID . " AND p.product_id = pc.product_id AND m.manufacturer_id = p.manufacturer_id AND p.status = '1'  GROUP BY m.name");
            $manufacturerList = $query->rows;

            return $manufacturerList;
        }
    }

//Function definition to get Product Price Ranges by Category ID
    public function getProductPricesByCategory($categoryID) {
        if (!empty($categoryID)) {

            $query = $this->db->query("SELECT min(p.price) as minPrice, max(p.price) as maxPrice FROM " . DB_PREFIX . "product p, " . DB_PREFIX . "product_to_category p2c, " . DB_PREFIX . "category_path cp WHERE p.product_id = p2c.product_id and p2c.category_id = cp.category_id AND p.status = '1' AND cp.path_id = " . (int) $categoryID);
            $minPrice = $query->row['minPrice'];
            $maxPrice = $query->row['maxPrice'];

            $priceRanges = array();
            $rangeDiff = ceil($maxPrice / 4);

            if ($rangeDiff > 0) {
                $totalRanges = ceil($maxPrice / $rangeDiff);

                for ($i = 0; $i < $totalRanges; $i++) {
                    $start = $i * $rangeDiff;
                    $end = $start + $rangeDiff;
                    $priceRanges[$i] = $start . '|' . $end;
                }

                return $priceRanges;
            }
        }
    }
    //Function definition to get Product Price Ranges by Search Term
    public function getProductPricesBySearchTerm($searchTerm)
    {
        if (!empty($searchTerm))
        {
            $implode = array();
            $words = explode(' ', trim(preg_replace('/\s+/', ' ', $searchTerm)));

            foreach ($words as $word)
            {
                $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
            }

            $sql = '';
            if ($implode)
            {
                $sql .= " AND " . implode(" AND ", $implode) . "";
            }
            $query = $this->db->query("SELECT min(p.price) as minPrice, max(p.price) as maxPrice FROM " . DB_PREFIX . "product p, " . DB_PREFIX . "product_description pd WHERE p.product_id = pd.product_id AND p.status = '1' ". $sql );
            $minPrice = $query->row['minPrice'];
            $maxPrice = $query->row['maxPrice'];
            
            $priceRanges = array();
            $rangeDiff = ceil($maxPrice/4);
            
            if($rangeDiff > 0)
            {
                $totalRanges = ceil($maxPrice/$rangeDiff);

                for($i=0;$i<$totalRanges;$i++)
                {
                    $start = $i * $rangeDiff;
                    $end = $start + $rangeDiff;
                    $priceRanges[$i] = $start.'|'.$end;
                }

                return $priceRanges;
            }
        }
    }
    //Function definition to get manufacturers by search term
    public function getManufacturersBySearchTerm($searchTerm)
    {
        if(!empty($searchTerm))
        {
            $implode = array();
            $words = explode(' ', trim(preg_replace('/\s+/', ' ', $searchTerm)));

            foreach ($words as $word)
            {
                $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
            }

            $sql = '';
            if ($implode)
            {
                $sql .= " AND " . implode(" AND ", $implode) . "";
            }
            $query = $this->db->query("SELECT m.manufacturer_id, m.name FROM " . DB_PREFIX . "product p, " . DB_PREFIX . "product_description pd, " . DB_PREFIX . "manufacturer m WHERE m.manufacturer_id = p.manufacturer_id AND p.product_id = pd.product_id AND p.status = '1' " . $sql  . " GROUP BY m.name");
            $manufacturerList = $query->rows;
            
            return $manufacturerList;
        }
    }
    
    public function getFilterAttributes($product_id) {
        
        $filter_data = array();
        $product_attributes_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute pa," . DB_PREFIX . "attribute atb," . DB_PREFIX . "attribute_group_description agd," . DB_PREFIX . "attribute_group ag WHERE pa.product_id = '" . (int) $product_id . "' and pa.attribute_id = atb.attribute_id and ag.attribute_group_id = atb.attribute_group_id and agd.attribute_group_id = ag.attribute_group_id");
        $filter_description_data = array();
        foreach ($product_attributes_query->rows as $filter_group_id) {
            
            $filter_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '" . (int) $filter_group_id['attribute_id'] . "'");
            foreach ($filter_description_query->rows as $filter_description) {
                
                $filter_description_data[$filter_group_id['language_id']] = array('name' => $filter_description['name']);
            }
            $filter_data[] = array(
                'attribute_group_id' => $filter_group_id['attribute_group_id'],
                'name' => $filter_group_id['name'],
                'attribute_description' => $filter_description_data,
                'sort_order' => $filter_group_id['sort_order']
            );
        }
        return $filter_data;
    }
    
    //Function definition to get Product Filter by Product ID
    public function getFilterDescriptions($product_id) {
        $filter_data = array();
        $product_filter_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int) $product_id . "'");
        $filter_description_data = array();
        foreach ($product_filter_query->rows as $filter_group_id) {

            $filter_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "filter f, " . DB_PREFIX . "filter_group_description fgd WHERE f.filter_group_id = fgd.filter_group_id AND f.filter_group_id = '" . (int) $filter_group_id['filter_id'] . "'");

            foreach ($filter_query->rows as $filter) {

                $filter_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "filter_description WHERE filter_id = '" . (int) $filter['filter_id'] . "'");

                foreach ($filter_description_query->rows as $filter_description) {
                    $filter_description_data[$filter_description['language_id']] = array('name' => $filter_description['name']);
                }

                $filter_data[] = array(
                    'filter_id' => $filter['filter_id'],
                    'name' => $filter['name'],
                    'filter_description' => $filter_description_data,
                    'sort_order' => $filter['sort_order']
                );
            }
        }
        return $filter_data;
    }

    //Function definition to get Total Products Filter by Product ID    
    public function getTotalProducts($data = array()) {
        $sql = "SELECT COUNT(DISTINCT p.product_id) AS total";

        if (!empty($data['filter_category_id'])) {
            if (!empty($data['filter_category_id'])) {
                $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
            } else {
                $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
            }

            if (!empty($data['filter_filter'])) {
                $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
            } else {
                $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
            }
        } else {
            if (!empty($data['filter_filter'])) {
                $sql .= " FROM " . DB_PREFIX . "product_filter pf LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
            }else{
                $sql .= " FROM " . DB_PREFIX . "product p";
            }
        }

        if (!empty($data['filter_id']) && !empty($data['filter_title'])) {
            $option_ids = array();
            $product_ids = array();
            $product_option_query = $this->db->query("SELECT option_id FROM   " . DB_PREFIX . "option_description WHERE option_id in (" . $data['filter_title'] . ")");
            if($product_option_query->num_rows > 0) {
                foreach ($product_option_query->rows as $product_option) {
                    $option_ids[] = $product_option['option_id'];
                }
            }
            if(!empty($option_ids)) {
                $product_option_value_query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_option_value  WHERE option_id in (" . implode(',', $option_ids) . ") AND option_value_id in (" . $data['filter_id'] . ") group by product_id");
                foreach ($product_option_value_query->rows as $product_option_value) {
                    $product_ids[] = $product_option_value['product_id'];
                }
            }
            $attr_ids = array();
            $product_attr_query = $this->db->query("SELECT attribute_id FROM   " . DB_PREFIX . "attribute_description WHERE name in (" . $data['filter_title'] . ")");
            if($product_attr_query->num_rows > 0) {
                foreach ($product_attr_query->rows as $product_attr) {
                    $attr_ids[] = $product_attr['attribute_id'];
                }
            }
            
            if(!empty($attr_ids)) {
                $product_attr_value_query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_attribute  WHERE attribute_id in (" . implode(',', $attr_ids) . ") group by product_id");
                foreach ($product_attr_value_query->rows as $product_attr_value) {
                    $product_ids[] = $product_attr_value['product_id'];
                }
            }
            
        }

        $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int) $this->config->get('config_store_id') . "'";

        if (!empty($data['filter_category_id'])) {
            if (!empty($data['filter_sub_category'])) {
                $sql .= " AND cp.path_id = '" . (int) $data['filter_category_id'] . "'";
            } else {
                $sql .= " AND p2c.category_id = '" . (int) $data['filter_category_id'] . "'";
            }

            if (!empty($data['filter_filter'])) {
                $implode = array();

                $filters = explode(',', $data['filter_filter']);

                foreach ($filters as $filter_id) {
                    $implode[] = (int) $filter_id;
                }

                $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
            }
        }
        if (!empty($data['filter_filter'])) {
            $implode = array();

            $filters = explode(',', $data['filter_filter']);

            foreach ($filters as $filter_id) {
                $implode[] = (int) $filter_id;
            }

            $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
        }
        if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
                $sql .= " AND (";

                if (!empty($data['filter_name'])) {
                        $implode = array();

                        $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                        foreach ($words as $word) {
                                $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                        }

                        if ($implode) {
                                $sql .= " " . implode(" AND ", $implode) . "";
                        }

                        if (!empty($data['filter_description'])) {
                                $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                        }
                }

                if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                        $sql .= " OR ";
                }

                if (!empty($data['filter_tag'])) {
                        $sql .= "pd.tag LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tag'])) . "%'";
                }

                if (!empty($data['filter_name'])) {
                        $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                        $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                        $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                        $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                        $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                        $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                        $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                }

                $sql .= ")";
        }
        if (!empty($data['brand_filter'])) {
            $sql .= " AND p.manufacturer_id IN (" . $data['brand_filter'] . ")";
        }
        if (!empty($data['price_filter'])) {
            $priceList = explode(",", $data['price_filter']);
            $rangeCounter = 0;
            foreach ($priceList as $priceList) {
                if ($rangeCounter > 0) {
                    $sql .= " OR";
                } else {
                    $sql .= " AND (";
                }
                $priceRange = explode("|", $priceList);
                $sql .= " (p.price >= " . $priceRange[0] . " AND p.price <= " . $priceRange[1] . ")";
                $rangeCounter++;
            }
            $sql .= ") ";
        }
        if (!empty($data['filter_id']) && !empty($data['filter_title'])) {
            if (count($product_ids) > 0) {
                $sql .= " AND p.product_id IN (" . implode(',', $product_ids) . ")";
            }
        }
        $query = $this->db->query($sql);

        return $query->row['total'];
    }
    public function getProduct($product_id) {
            $query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

            if ($query->num_rows) {
                    return array(
                            'product_id'       => $query->row['product_id'],
                            'name'             => $query->row['name'],
                            'description'      => $query->row['description'],
                            'meta_title'       => $query->row['meta_title'],
                            'meta_description' => $query->row['meta_description'],
                            'meta_keyword'     => $query->row['meta_keyword'],
                            'tag'              => $query->row['tag'],
                            'model'            => $query->row['model'],
                            'sku'              => $query->row['sku'],
                            'upc'              => $query->row['upc'],
                            'ean'              => $query->row['ean'],
                            'jan'              => $query->row['jan'],
                            'isbn'             => $query->row['isbn'],
                            'mpn'              => $query->row['mpn'],
                            'location'         => $query->row['location'],
                            'quantity'         => $query->row['quantity'],
                            'stock_status'     => $query->row['stock_status'],
                            'image'            => $query->row['image'],
                            'manufacturer_id'  => $query->row['manufacturer_id'],
                            'manufacturer'     => $query->row['manufacturer'],
                            'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
                            'special'          => $query->row['special'],
                            'reward'           => $query->row['reward'],
                            'points'           => $query->row['points'],
                            'tax_class_id'     => $query->row['tax_class_id'],
                            'date_available'   => $query->row['date_available'],
                            'weight'           => $query->row['weight'],
                            'weight_class_id'  => $query->row['weight_class_id'],
                            'length'           => $query->row['length'],
                            'width'            => $query->row['width'],
                            'height'           => $query->row['height'],
                            'length_class_id'  => $query->row['length_class_id'],
                            'subtract'         => $query->row['subtract'],
                            'rating'           => round($query->row['rating']),
                            'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
                            'minimum'          => $query->row['minimum'],
                            'sort_order'       => $query->row['sort_order'],
                            'status'           => $query->row['status'],
                            'date_added'       => $query->row['date_added'],
                            'date_modified'    => $query->row['date_modified'],
                            'viewed'           => $query->row['viewed']
                    );
            } else {
                    return false;
            }
    }
    public function getProducts($data = array()) {
            $sql = "SELECT p.product_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";

            if (!empty($data['filter_category_id'])) {
                    if (!empty($data['filter_category_id'])) {
                            $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                    } else {
                            $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                    }

                    if (!empty($data['filter_filter'])) {
                            $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
                    } else {
                            $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
                    }
            } else {
                    $sql .= " FROM " . DB_PREFIX . "product p";
            }
            if (!empty($data['filter_id']) && !empty($data['filter_title'])) {
                $option_ids = array();
                $product_ids = array();
                $product_option_query = $this->db->query("SELECT option_id FROM   " . DB_PREFIX . "option_description WHERE option_id in (" . $data['filter_title'] . ")");
                if($product_option_query->num_rows > 0) {
                    foreach ($product_option_query->rows as $product_option) {
                        $option_ids[] = $product_option['option_id'];
                    }
                }
                if(!empty($option_ids)) {
                    $product_option_value_query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_option_value  WHERE option_id in (" . implode(',', $option_ids) . ") AND option_value_id in (" . $data['filter_id'] . ") group by product_id");
                    foreach ($product_option_value_query->rows as $product_option_value) {
                        $product_ids[] = $product_option_value['product_id'];
                    }
                }
                $attr_ids = array();
                $product_attr_query = $this->db->query("SELECT attribute_id FROM   " . DB_PREFIX . "attribute_description WHERE name in (" . $data['filter_title'] . ")");
                if($product_attr_query->num_rows > 0) {
                    foreach ($product_attr_query->rows as $product_attr) {
                        $attr_ids[] = $product_attr['attribute_id'];
                    }
                }

                if(!empty($attr_ids)) {
                    $product_attr_value_query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_attribute  WHERE attribute_id in (" . implode(',', $attr_ids) . ") group by product_id");
                    foreach ($product_attr_value_query->rows as $product_attr_value) {
                        $product_ids[] = $product_attr_value['product_id'];
                    }
                }
            }
            

            $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

            if (!empty($data['filter_category_id'])) {
                    if (!empty($data['filter_sub_category'])) {
                            $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                    } else {
                            $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                    }

                    if (!empty($data['filter_filter'])) {
                            $implode = array();

                            $filters = explode(',', $data['filter_filter']);

                            foreach ($filters as $filter_id) {
                                    $implode[] = (int)$filter_id;
                            }

                            $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
                    }
            }

            if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
                    $sql .= " AND (";

                    if (!empty($data['filter_name'])) {
                            $implode = array();

                            $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                            foreach ($words as $word) {
                                    $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                            }

                            if ($implode) {
                                    $sql .= " " . implode(" AND ", $implode) . "";
                            }

                            if (!empty($data['filter_description'])) {
                                    $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                            }
                    }

                    if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                            $sql .= " OR ";
                    }

                    if (!empty($data['filter_tag'])) {
                            $sql .= "pd.tag LIKE '%" . $this->db->escape($data['filter_tag']) . "%'";
                    }

                    if (!empty($data['filter_name'])) {
                            $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                            $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                            $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                            $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                            $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                            $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                            $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    }

                    $sql .= ")";
            }
            if (!empty($data['brand_filter'])) {
                $sql .= " AND p.manufacturer_id IN (" . $data['brand_filter'] . ")";
            }
            if (!empty($data['price_filter'])) {
                $priceList = explode(",", $data['price_filter']);
                $rangeCounter = 0;
                foreach ($priceList as $priceList) {
                    if ($rangeCounter > 0) {
                        $sql .= " OR";
                    } else {
                        $sql .= " AND (";
                    }
                    $priceRange = explode("|", $priceList);
                    $sql .= " (p.price >= " . $priceRange[0] . " AND p.price <= " . $priceRange[1] . ")";
                    $rangeCounter++;
                }
                $sql .= ") ";
            }
            if (!empty($data['filter_id']) && !empty($data['filter_title'])) {
                if (count($product_ids) > 0) {
                    $sql .= " AND p.product_id IN (" . implode(',', $product_ids) . ")";
                }
            }

            if (!empty($data['filter_manufacturer_id'])) {
                    $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
            }

            $sql .= " GROUP BY p.product_id";

            $sort_data = array(
                    'pd.name',
                    'p.model',
                    'p.quantity',
                    'p.price',
                    'rating',
                    'p.sort_order',
                    'p.date_added'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                    if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
                            $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
                    } elseif ($data['sort'] == 'p.price') {
                            $sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
                    } else {
                            $sql .= " ORDER BY " . $data['sort'];
                    }
            } else {
                    $sql .= " ORDER BY p.sort_order";
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                    $sql .= " DESC, LCASE(pd.name) DESC";
            } else {
                    $sql .= " ASC, LCASE(pd.name) ASC";
            }

            if (isset($data['start']) || isset($data['limit'])) {
                    if ($data['start'] < 0) {
                            $data['start'] = 0;
                    }

                    if ($data['limit'] < 1) {
                            $data['limit'] = 20;
                    }

                    $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }

            $product_data = array();
            $query = $this->db->query($sql);
            foreach ($query->rows as $result) {
                    $product_data[$result['product_id']] = $this->getProduct($result['product_id']);
            }

            return $product_data;
    }
    public function getModulesByCode($code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `name`");

		return $query->rows;
	}

}
