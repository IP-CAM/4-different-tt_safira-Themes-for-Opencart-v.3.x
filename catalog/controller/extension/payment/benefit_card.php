<?php
class ControllerExtensionPaymentBenefitCard extends Controller 
{
    public function index() {
        $this->load->language('extension/payment/benefit_card');

        $this->load->model('account/customer');

        $card_amount = $this->model_account_customer->getCardAmount($this->customer->getId());

        $data['card_amount'] = $card_amount;

        $data['continue'] = $this->url->link('checkout/success');

		return $this->load->view('extension/payment/benefit_card', $data);
    }

    public function confirm() {
        if ($this->session->data['payment_method']['code'] == 'benefit_card') {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_benefit_card_sort_order'));
		}
    }
}