<?php
class ModelVltechReorder extends Model
{
    public function getLastOrder() {
        $query = $this->db->query("SELECT o.order_id, o.firstname, o.lastname, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.customer_id = '" . (int)$this->customer->getId() . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC");

        if($query->rows) {
            return $query->rows[0];
        } else return false;
    }

    public function getProductOptionQuantity($product_option_value_id) {
        $sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = '" . (int) $product_option_value_id . "'";

        $query = $this->db->query($sql);

        if($query->num_rows) {
            return $query->row['quantity'];
        } else return false;
    }
}
