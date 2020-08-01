<?php

class ControllerWebServicesApi extends Controller {

    public function index() {

        error_reporting(0);
        
        $this->load->model('webservices/home');
        $this->model_webservices_home->logKnowbandAppRequest("app_get_data", json_encode($this->request->get));
        $this->model_webservices_home->logKnowbandAppRequest("app_post_data", json_encode($this->request->post));
        $this->model_webservices_home->logKnowbandAppRequest("app_cookie_data", json_encode($_COOKIE));
        if (!isset($this->request->get['method']) || !isset($this->request->get['version']) || $this->request->get['version'] < 1.5) {
            echo "Invalid request!!!";
            die;
        }
        $this->load->model('webservices/numberstring');
        $route = $this->request->get['method'];
        $app_version = $this->request->get['version'];
        if (isset($this->request->post['id_currency']) && $this->request->post['id_currency'] != '') {
            $currency_id = $this->request->post['id_currency'];
            if(VERSION >= '2.2.0.0'){
                $this->session->data['currency'] = $currency_id;
            } else{
                $this->currency->set($currency_id);
            }
        }
        if (isset($this->request->post['iso_code']) && $this->request->post['iso_code'] != '') {
            $language = $this->request->post['iso_code'];
            $this->session->data['language'] = $language;
        }

        $app_version_explodes = explode(".", $app_version);
        $app_version_path = '';
        foreach ($app_version_explodes as $app_version_explode) {
            $app_version_path .= $this->model_webservices_numberstring->N2L($app_version_explode);
        }
        
        $file = DIR_LOGS . 'test_api_log.txt';
        $handle = fopen($file, 'a+');
        fwrite($handle, date('Y-m-d G:i:s'). ' - ' . print_r($route,TRUE)  . "\r\n");
        fclose($handle);
        
        if ($route == 'appGetHome' || $route == 'appSpinWin' || $route == 'appGetSellers' || $route == 'appGetSellerReviews' || $route == 'appSaveSellerReview' || $route == 'appGetConfig' || $route == 'appCheckLogStatus' || $route == 'appGetMobilePaymentMethods' || $route == 'appCheckLiveChatSupportStatus') {
            $controller = 'home';
        } else if ($route == 'appGetCategoryDetails' || $route == 'appGetSellerProducts' || $route == 'appGetFilters') {
            $controller = 'category';
        } else if ($route == 'appAddToWishlist' || $route == 'appRemoveWishlist' || $route == 'appGetWishlist') {
            $controller = 'wishlist';
        } else if ($route == 'appCheckout' || $route == 'appCreateOrder' || $route == 'appSetShippingMethod' || $route == 'appCreateOrder' || $route == 'appCheckOrderStatus' || $route == 'appReorder') {
            $controller = 'checkout';
        } else if ($route == 'appGetTranslations') {
            $controller = 'language';
        } else if ($route == 'appGetPaymentMethods' || $route == 'confirm') {
            $controller = 'payment_method';
        } else if ($route == 'appGetProductDetails' || $route == 'appGetProductReviews' || $route == 'appSaveProductReview') {
            $controller = 'product';
        } else if ($route == 'appGetCartDetails' || $route == 'appAddToCart' || $route == 'appUpdateCartQuantity' || $route == 'appRemoveProduct' || $route == 'appApplyVoucher' || $route == 'appRemoveVoucher') {
            $controller = 'shopping_cart';
        } else if ($route == 'appUpdatePassword' || $route == 'appCheckIfContactNumberExists' || $route == 'appLoginViaPhone' || $route == 'appLoginViaEmail' || $route == 'appMapEmailWithUUID' || $route == 'appGetCustomerAddress' || $route == 'appGetAddressForm' || $route == 'appAddAddress' || $route == 'appLogin' || $route == 'appGetRegistrationForm' || $route == 'appRegisterUser' || $route == 'appGetRegions' || $route == 'appUpdateAddress' || $route == 'appGetOrders' || $route == 'appGetOrderDetails' || $route == 'appSocialLogin' || $route == 'appForgotPassword' || $route == 'appUpdateProfile' || $route == 'appGuestRegistration') {
            $controller = 'my_account';
        } else if ($route == 'appFCMregister') {
            $controller = 'fcm';
        } else {
            echo json_encode(array('status' => 'Method not found'));
            die;
        }
        $this->load->controller('webservices/' . $app_version_path . '/' . $controller . '/' . $route);
        die;
    }

    public function loggingStatus() {
        $response = array();
        if (defined('KB_APP_Webservices_Log') && KB_APP_Webservices_Log == "1") {
            $response["logging"] = true;
        } else {
            $response["logging"] = false;
        }
        echo json_encode($response);
        die();
    }
}
