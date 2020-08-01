<?php 
class ModelWebservicesLanguage extends Model {
        public function checkLanguageCode($language_code){
        $query= $this->db->query("SELECT * FROM " . DB_PREFIX . "language where code='".$language_code."'");
        if($query->num_rows>0){
            return $query->row['code'];
        }
    }
}
?>
