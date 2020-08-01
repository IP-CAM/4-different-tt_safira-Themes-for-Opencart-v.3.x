<?php

class ControllerWebservicesOneSixFcm extends Controller {

    public function appFCMregister() {

        $json = array();
        $setting_data = $this->config->get('webservice');
        $this->load->model('webservices/home');

        if (!$setting_data['webservice_status']) {
            $json['install_module'] = "Warning: You do not have permission to access the module, Kindly install module !!";
            echo json_encode($json);
            $this->model_webservices_home->logKnowbandAppRequest("addProductToWishlist", json_encode($json));
            die;
        } else {
            $json['install_module'] = '';
        }
        
        if (!empty($this->request->post['cart_id']) && !empty($this->request->post['fcm_id'])) {
            $this->load->model('webservices/fcm');
            $fcm_id = $this->request->post['fcm_id'];
            $email_id = $this->request->post['email'];
            $fcm_data = $this->model_webservices_fcm->isFcmAndCartExist($fcm_id, $email_id);
            if (!$fcm_data) {
                $this->model_webservices_fcm->saveFcmData($fcm_id, $email_id);
            }
            $json['status'] = 'success';
        } else {
            $json['status'] = 'failure';
            $json['message'] = 'Cart id or FCM id not found';
        }
        //Complete response

        $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $this->response->addHeader('Access-Control-Max-Age: 1000');
        $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cookie: OCSESSID=' . $this->session->getId() . ";");
        $this->response->setOutput(json_encode($json));
        $this->model_webservices_home->logKnowbandAppRequest("addProductToWishlist", json_encode($json));
        $this->response->output();
        die;
    }

}

?>
