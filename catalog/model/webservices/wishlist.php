<?php
class ModelWebservicesWishlist extends Model {
    
    public function AddWishlist($customer_id, $id){
        $wishlist = array();
        $this->load->model('catalog/product');
        $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . $this->db->escape($customer_id) . "' ");
        if ($customer_query->num_rows) {
            if ($customer_query->row['wishlist'] && is_string($customer_query->row['wishlist'])) {
                    $wishlist = unserialize($customer_query->row['wishlist']);
                     if (!in_array($id, $wishlist)) {
                        array_push($wishlist, $id);
                    }
                    foreach ($wishlist as $key=>$product_id) {
                            $product_info = $this->model_catalog_product->getProduct($product_id);
                            if (!in_array($product_id, $this->session->data['wishlist'])) {
                                if(!empty($product_info)) {
                                    $this->session->data['wishlist'][] = $product_id;
                                } else {
                                    unset($wishlist[$key]);
                                }
                            }
                    }
                    $this->db->query("UPDATE " . DB_PREFIX . "customer SET wishlist = '" . serialize($wishlist) . "'  WHERE customer_id = '" . (int)$customer_id. "'"); 
            }else{
                $wishlist[] = $id;
                $this->session->data['wishlist'][] = $id;
                $this->db->query("UPDATE " . DB_PREFIX . "customer SET wishlist = '" . serialize($wishlist) . "'  WHERE customer_id = '" . (int)$customer_id. "'"); 
            }
        }
        return $wishlist;
    }
    public function UpdateWishlist($customer_id, $id){
        $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . $this->db->escape($customer_id) . "' ");
        $this->load->model('catalog/product');
        if ($customer_query->num_rows) {
            if ($customer_query->row['wishlist'] && is_string($customer_query->row['wishlist'])) {
                    $wishlist = unserialize($customer_query->row['wishlist']);
                    $key = array_search($id, $wishlist);
                    if ($key !== false) {
                        unset($wishlist[$key]);
                        $this->session->data['wishlist'] = array();
                        foreach ($wishlist as $product_id) {
                            $product_info = $this->model_catalog_product->getProduct($product_id);
                            if(!empty($product_info)) {
                                $this->session->data['wishlist'][] = $product_id;
                            } else {
                                unset($wishlist[$key]);
                            }
                        }
                        $this->db->query("UPDATE " . DB_PREFIX . "customer SET wishlist = '" . serialize($wishlist) . "'  WHERE customer_id = '" . (int)$customer_id. "'");
                    }else{
                        $wishlist = array();
                        $this->db->query("UPDATE " . DB_PREFIX . "customer SET wishlist = '" . serialize($wishlist) . "'  WHERE customer_id = '" . (int)$customer_id. "'");
                    }
            }
        }
        
    }
    public function getWishlist($customer_id){
        $wishlist = array();
        $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . $this->db->escape($customer_id) . "' ");        
        if ($customer_query->num_rows) {
            $this->load->model('catalog/product');
            if ($customer_query->row['wishlist'] && is_string($customer_query->row['wishlist'])) {
                    if (!isset($this->session->data['wishlist'])) {
                            $this->session->data['wishlist'] = array();
                    }
                    $wishlist = unserialize($customer_query->row['wishlist']);
                    foreach ($wishlist as $key=>$product_id) {
                            $product_info = $this->model_catalog_product->getProduct($product_id);
                            if (!in_array($product_id, $this->session->data['wishlist'])) {
                                if(!empty($product_info)) {
                                    $this->session->data['wishlist'][] = $product_id;
                                } else {
                                    unset($wishlist[$key]);
                                }
                            } 
                    }
            }
        }
        return $wishlist;
    }
}

?>
