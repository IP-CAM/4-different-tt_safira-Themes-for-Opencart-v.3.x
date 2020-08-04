<?php
class ModelExtensionPaymentBenefitCard extends Model
{
    public function getMethod($address, $total) {
        $this->load->language('extension/payment/benefit_card');

        $status = 0;

        if ($this->customer->isLogged()) {
            $this->load->model('account/customer');

            $customer_id = $this->customer->getId();
            $status = $this->model_account_customer->getCardStatus($customer_id);
        }

        if($status) {
            $method_data = array(
                'code'       => 'free_checkout',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('payment_benefit_card_sort_order')
            );
        }

        return $method_data;
    }
}
