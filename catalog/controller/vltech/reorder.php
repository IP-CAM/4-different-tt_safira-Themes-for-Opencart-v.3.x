<?php
class ControllerVltechReorder extends Controller
{
    public function last() {
        $this->load->language('checkout/cart');
        $this->load->language('vltech/reorder');

        $this->load->model('vltech/reorder');
        $this->load->model('account/order');
        $this->load->model('catalog/product');

        $json = array();

        $error_products = array();

        $last_order = $this->model_vltech_reorder->getLastOrder();

        $order_id = $last_order['order_id'];

        $order_info = $this->model_account_order->getOrder($order_id);

        $products = $this->model_account_order->getOrderProducts($order_id);

        if($order_info) {
            if($products) {
                foreach ($products as $product) {
                    $order_product_id = $product['order_product_id'];

                    $order_product_info = $this->model_account_order->getOrderProduct($order_id, $order_product_id);

                    if ($order_product_info) {
                        $product_info = $this->model_catalog_product->getProduct($order_product_info['product_id']);

                        if ($product_info) {
                            $can_add = true;

                            $quantity = (int) $order_product_info['quantity'];

                            $option_data = array();

                            $order_options = $this->model_account_order->getOrderOptions($order_product_info['order_id'], $order_product_id);

                            foreach ($order_options as $order_option) {
                                if ($order_option['type'] == 'select' || $order_option['type'] == 'radio' || $order_option['type'] == 'image') {
                                    $option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
                                } elseif ($order_option['type'] == 'checkbox') {
                                    $option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
                                } elseif ($order_option['type'] == 'text' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
                                    $option_data[$order_option['product_option_id']] = $order_option['value'];
                                } elseif ($order_option['type'] == 'file') {
                                    $option_data[$order_option['product_option_id']] = $this->encryption->encrypt($this->config->get('config_encryption'), $order_option['value']);
                                }

                                $option_quantity = (int) $this->model_vltech_reorder->getProductOptionQuantity($order_option['product_option_value_id']);

                                if($quantity > $option_quantity) $can_add = false;
                            }

                            if($can_add) {
                                $this->cart->add($order_product_info['product_id'], $order_product_info['quantity'], $option_data);
                            } else {
                                array_push($error_products, $order_product_info['name']);
                            }
                        } else {
                            array_push($error_products, $order_product_info['name']);
                        }
                    }
                }

                if(!empty($error_products)) {
                    $json['error'] = $this->language->get('error_add_product');
                }
            } else {
                $json['error'] = $this->language->get('error_add_product');
            }

            if(empty($json)) {
                $json['success'] = $this->language->get('text_success');

                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);

                // Totals
                $this->load->model('setting/extension');

                $totals = array();
                $taxes = $this->cart->getTaxes();
                $total = 0;

                // Because __call can not keep var references so we put them into an array.
                $total_data = array(
                    'totals' => &$totals,
                    'taxes'  => &$taxes,
                    'total'  => &$total
                );

                $sort_order = array();

                $results = $this->model_setting_extension->getExtensions('total');

                foreach ($results as $key => $value) {
                    $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
                }

                array_multisort($sort_order, SORT_ASC, $results);

                foreach ($results as $result) {
                    if ($this->config->get('total_' . $result['code'] . '_status')) {
                        $this->load->model('extension/total/' . $result['code']);

                        // We have to put the totals in an array so that they pass by reference.
                        $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                    }
                }

                $sort_order = array();

                foreach ($totals as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $totals);

                $json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));
                $json['cart_qty'] = sprintf($this->language->get('cart_qty'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0));
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function current() {
        $this->load->language('checkout/cart');
        $this->load->language('vltech/reorder');

        $this->load->model('vltech/reorder');
        $this->load->model('account/order');
        $this->load->model('catalog/product');

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }

        $order_info = $this->model_account_order->getOrder($order_id);

        if($order_info) {
            $products = $this->model_account_order->getOrderProducts($order_id);

            if($products) {
                foreach ($products as $product) {
                    $order_product_id = $product['order_product_id'];

                    $order_product_info = $this->model_account_order->getOrderProduct($order_id, $order_product_id);

                    if ($order_product_info) {
                        $product_info = $this->model_catalog_product->getProduct($order_product_info['product_id']);

                        if ($product_info) {
                            $can_add = true;

                            $quantity = (int) $order_product_info['quantity'];

                            $option_data = array();

                            $order_options = $this->model_account_order->getOrderOptions($order_product_info['order_id'], $order_product_id);

                            foreach ($order_options as $order_option) {
                                if ($order_option['type'] == 'select' || $order_option['type'] == 'radio' || $order_option['type'] == 'image') {
                                    $option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
                                } elseif ($order_option['type'] == 'checkbox') {
                                    $option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
                                } elseif ($order_option['type'] == 'text' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
                                    $option_data[$order_option['product_option_id']] = $order_option['value'];
                                } elseif ($order_option['type'] == 'file') {
                                    $option_data[$order_option['product_option_id']] = $this->encryption->encrypt($this->config->get('config_encryption'), $order_option['value']);
                                }

                                $option_quantity = (int) $this->model_vltech_reorder->getProductOptionQuantity($order_option['product_option_value_id']);

                                if($quantity > $option_quantity) $can_add = false;
                            }

                            if($can_add) {
                                $this->cart->add($order_product_info['product_id'], $order_product_info['quantity'], $option_data);

                                $this->session->data['success'] = $this->language->get('text_success');

                                unset($this->session->data['shipping_method']);
                                unset($this->session->data['shipping_methods']);
                                unset($this->session->data['payment_method']);
                                unset($this->session->data['payment_methods']);
                            } else {
                                $this->session->data['error'] = $this->language->get('error_add_product');
                            }
                        } else {
                            $this->session->data['error'] = $this->language->get('error_add_product');
                        }
                    }
                }

                if(!empty($error_products)) {
                    $this->session->data['error'] = $this->language->get('error_add_product');
                }
            } else {
                $this->session->data['error'] = $this->language->get('error_add_product');
            }
        } else {
            $this->session->data['error'] = $this->language->get('error_add_product');
        }

        $this->response->redirect($this->url->link('account/order', '', true));
    }
}
