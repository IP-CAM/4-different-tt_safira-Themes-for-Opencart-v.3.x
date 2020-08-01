<?php

/*
 * File: currency.php
 * Description: It contains currency class and functions for web-services
 * Created Date: 29-December-2016
 */

class ControllerWebServicesOneSixCurrency extends Controller {

    //Function definition to prepare JSON response of currency data and send to requested application
    public function getCurrency() {

        //Response Array
        $json = array();

        $this->load->model('webservices/home');
        $this->load->model('webservices/currency');
        $this->load->language('webservices/config');
        $setting_data = $this->config->get('webservice');

        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            $this->model_webservices_home->logKnowbandAppRequest("getCurrency", json_encode($json));
            die;
        } else {
            $json['install_module'] = '';
        }
        if (isset($this->request->post['currency_code']) && !empty($this->request->post['currency_code'])) {
            $currency_code = $this->request->post['currency_code'];
        } else {
            $currency_code = '';
        }

        if ($currency_code != "") {
            $code = $this->model_webservices_currency->checkCurrencyCode($currency_code);
            if (isset($code) && $code != "") {
                $this->session->data['currency'] = $code;
                $json['status'] = "success";
                $json['message'] = "";
            } else {
                $json['status'] = "failure";
                $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_currecy_error'));
            }
        } else {
            $json['status'] = "failure";
            $json['message'] = iconv("UTF-8","UTF-8//IGNORE",$this->language->get('text_currecy_error'));
        }

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("getCurrency", json_encode($json));
        $this->response->output();
        die;
    }

}
