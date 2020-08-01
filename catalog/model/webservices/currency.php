<?php 
class ModelWebservicesCurrency extends Model {
        public function checkCurrencyCode($currency_code){
        $query= $this->db->query("SELECT * FROM " . DB_PREFIX . "currency where code='".$currency_code."'");
        if($query->num_rows>0){
            return $query->row['code'];
        }
    }
}
?>
