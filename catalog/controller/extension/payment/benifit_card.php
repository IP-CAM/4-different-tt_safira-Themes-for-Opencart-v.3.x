<?php
// <!-- [20-07-20 by Ming] -> Benift Card Payment -->
class ControllerExtensionPaymentBenifitCard extends Controller {
	public function index() {
		return $this->load->view('extension/payment/benifit_card');
	}

	public function confirm() {
		$json = array();
		$this->load->model('account/customer');
		$this->load->model('checkout/order');
		$this->load->model('catalog/product');

		$post_serial_num = $this->request->post['serial_num'];
		$post_security_num = $this->request->post['security_num'];

		if ($post_serial_num != $this->model_account_customer->getSerialNumber($this->customer->getId())) {
			$json['status'] = 403;
			// $json['warning'] = 'Your serial number is not correct';
			$json['warning'] = 'Su número de serie no es correcto';
		} else if ($post_security_num != $this->model_account_customer->getSecurityNumber($this->customer->getId())) {
			$json['status'] = 403;
			// $json['warninig'] = 'Your sercurity number is not correct';
			$json['warninig'] = 'Su número de seguridad no es correcto';
		} else {
			if (isset($this->session->data['benifit_card'])) {
				$card_amount = $this->model_account_customer->getCardAmount($this->customer->getId());
				$paying_amount = (doubleval)($card_amount - $this->session->data['total_amount']);

				// var_dump($paying_amount);
				// exit;
	
				if ($paying_amount >= 0) {	
					// customer card amount
					$this->model_account_customer->editCardAmount($this->customer->getId(), $paying_amount);

					// cart products
					$data = array();

					$this->language->load('extension/payment/benifit_card');

					$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

					$accepted_currencies = array('ARS' => 'ARS', 'ARG' => 'ARS', 'VEF' => 'VEF',
						'BRA' => 'BRL', 'BRL' => 'BRL', 'REA' => 'BRL', 'MXN' => 'MEX',
						'CLP' => 'CHI', 'COP' => 'COP', 'PEN' => 'PEN', 'US' => 'US', 'USD' => 'USD', 'UYU' => 'UYU');

					$currency = $accepted_currencies[$order_info['currency_code']];
					$currencies = array('ARS', 'BRL', 'MEX', 'CHI', 'PEN', 'VEF', 'COP', 'UYU');
					if (!in_array($currency, $currencies)) {
						$currency = '';
						$json['warning'] = $this->language->get('currency_no_support');
					}

					$totalprice = $order_info['total'] * $order_info['currency_value'];
					$products = '';
					$all_products = $this->cart->getProducts(); 
					$items = array();

					foreach ($all_products as $product) {
						$product_price = round($product['price'] * $order_info['currency_value'], 2);

						$products .= $product['quantity'] . ' x ' . $product['name'] . ', ';
						$items[] = array(
							"id" => $product['product_id'],
							"title" => $product['name'],
							"description" => $product['quantity'] . ' x ' . $product['name'],
							"quantity" => intval($product['quantity']),
							"unit_price" => $product_price,
							"currency_id" => $currency,
							"picture_url" => HTTP_SERVER . 'image/' . $product['image'],
						);

						// [20-07-21 by Ming] -> add benifit card payment
						$product_quantity = $this->model_catalog_product->getProductQuantity($product['product_id']);
						
						// $product_quantity = $this->model_catalog_product->updatePaidProducts($product_quantity - $product['quantity']);
					}

					$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_cod_order_status_id'), date('d/m/Y h:i'));
					
					$json['status'] = 200;
					$json['redirect'] = $this->url->link('checkout/success');
				} else {
					$json['status'] = 403;
					$json['warning'] = 'Your card amount is not enough';
				}
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}
}