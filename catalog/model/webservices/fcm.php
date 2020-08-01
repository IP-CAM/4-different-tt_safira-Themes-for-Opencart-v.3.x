<?php
class ModelWebservicesFcm extends Model {
    
    public function getallCarts(){
        $query = $this->db->query('SELECT * FROM ' . DB_PREFIX . 'fcm_details where order_id > 0 AND notification_sent_status = 0 AND last_order_status=0 AND date_upd < DATE_SUB(NOW(), INTERVAL 1 HOUR)');
        if ($query->num_rows) {
            return $query->rows;
        }else{
            return false;
        }
    }
    public function isFcmAndCartExist($fcm_id, $email_id){
        $query = $this->db->query('SELECT * FROM ' . DB_PREFIX . 'fcm_details where fcm_id ="'.$fcm_id.'" AND email_id ="'. $email_id.'" AND session_id="'.$this->session->getId().'" AND last_order_status =0');
        if ($query->num_rows) {
            return true;
        }else{
            return false;
        }
    }
    
    public function saveFcmData($fcm_id, $email_id){
        $this->db->query("INSERT INTO `".DB_PREFIX."fcm_details` ("
                    . "`fcm_details_id`,"
                    . " `order_id`,"
                    . " `cart`,"
                    . " `session_id`,"
                    . " `email_id`,"
                    . " `fcm_id`,"
                    . " `notification_sent_status`,"
                    . " `date_add`, `date_upd`) VALUES ("
                    . "NULL, '".$this->db->escape(isset($this->session->data['order_id']) ? $this->session->data['order_id'] : '')."', '" . $this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') . "', '".$this->session->getId()."', '".$this->db->escape($email_id)."', '".$this->db->escape($fcm_id)."',  '0', now(), now())");        
    }
        
    public function getFcmDataByID($email, $order_id){
        $query = $this->db->query('SELECT * FROM ' . DB_PREFIX . 'fcm_details where email_id = "'.$email.'" AND order_id ="'.$order_id.'" order by fcm_details_id limit 1');
        if ($query->num_rows) {
            return $query->row;
        }else{
            return false;
        }
    }
    
    public function updateFcmEmailSessionId($email){
        $this->db->query("UPDATE `".DB_PREFIX."fcm_details` set email_id = '".$email."', cart='".$this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') ."' WHERE session_id='".$this->session->getId()."'");        
    }
    public function updateFcmOrderIdBySessionId(){
        $this->db->query("UPDATE `".DB_PREFIX."fcm_details` set order_id = '".$this->session->data['order_id']."', cart='".$this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') ."' WHERE session_id='".$this->session->getId()."'");        
    }
    public function updateFCMOrderStatus($email, $order_id, $order_status){
        $this->db->query("UPDATE `".DB_PREFIX."fcm_details` set last_order_status = '".$order_status."' WHERE order_id = '".$order_id."' AND email_id='".$email."'");        
    }
    
    public function updateOrderId($fcm_details_id, $order_id){
        $this->db->query("UPDATE `".DB_PREFIX."fcm_details` set order_id = '".$order_id."', cart='".$this->db->escape(isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '') ."', session_id = '".$this->session->getId()."' WHERE fcm_details_id='".$fcm_details_id."'");        
    }
    
    public function updateNotificationStatus($fcm_details_id){
        $this->db->query("UPDATE `".DB_PREFIX."fcm_details` set notification_sent_status = '1' WHERE fcm_details_id='".$fcm_details_id."'");        
    }
}

?>