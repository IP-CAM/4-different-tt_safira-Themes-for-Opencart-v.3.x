<?php

class ControllerWebservicesFirebase extends Controller {

    public function appCheckAbandonedCart() {
        $this->load->model('webservices/fcm');
        $this->load->model('checkout/order');
        $fcm_data = $this->model_webservices_fcm->getallCarts();

        if ($fcm_data) {
            foreach ($fcm_data as $cart) {
                $order_info = $this->model_checkout_order->getOrder($cart['order_id']);
                if ($order_info['order_status_id'] == 0) {
                    $setting_data = $this->config->get('webservice');
                    $firebase_server_key = $setting_data['firebase_server_key'];
                    $push_type = '';
                    $title = '';
                    $message = '';
                    if ($setting_data['abandoned_cart_notification_status'] == 0) {
                        return false;
                    } else {
                        $title = $setting_data['abandoned_cart_notification_title'];
                        $message = $setting_data['abandoned_cart_notification_message'];
                        $push_type = 'order_abandoned';


                        $user_id = ""; // user_id

                        $firebase_data = array();
                        $firebase_data['data']['title'] = $title;
                        $firebase_data['data']['is_background'] = false;
                        $firebase_data['data']['message'] = $message;
                        $firebase_data['data']['image'] = '';
                        $firebase_data['data']['payload'] = '';
                        $firebase_data['data']['user_id'] = $user_id;
                        $firebase_data['data']['push_type'] = $push_type;
                        $firebase_data['data']['cart_id'] = $cart['session_id'];
                        $firebase_data['data']['order_id'] = $cart['order_id'];
                        $firebase_data['data']['email_id'] = $cart['email_id'];

                        if ($cart['fcm_id']) {
                            $this->sendMultiple($cart['fcm_id'], $firebase_data, $firebase_server_key);
                        }
                    }
                    $this->model_webservices_fcm->updateNotificationStatus($cart['fcm_details_id']);
                }
            }
        }
    }

    public function on_order_add($order_id) {
        $this->load->model('webservices/fcm');
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);
        $fcm_data = $this->model_webservices_fcm->getFcmDataByID($order_info['email'], 0);
        if ($fcm_data) {
            $this->model_webservices_fcm->updateOrderId($fcm_data['fcm_details_id'], $order_id);
        }
    }

    public function on_order_update($order_id) {
        $this->load->model('checkout/order');
        $this->load->model('webservices/fcm');
        $order_status = '';
        $order_info = $this->model_checkout_order->getOrder($order_id);
        if ($order_info['order_status_id'] > 0) {
            $fcm_data = $this->model_webservices_fcm->getFcmDataByID($order_info['email'], $order_id);
            if ($fcm_data) {
                $order_status = $fcm_data['last_order_status'];

                if ($order_status == 0) {
                    $type = 'order_create';
                } else {
                    $type = 'order_status_change';
                }
                $cart_id = $order_id;
                $order_id = $order_id;
                $order_status = $order_info['order_status'];
                $email = $order_info['email'];
                if ($order_status) {
                    $this->notify($type, $cart_id, $order_id, $order_status, $email, $fcm_data['fcm_id']);
                    $this->model_webservices_fcm->updateFCMOrderStatus($order_info['email'], $order_id, $order_info['order_status_id']);
                }
            }
        }
    }

    public function notify($type, $cart_id = null, $order_id = null, $order_status = null, $email = '', $fcm_id) {

        $setting_data = $this->config->get('webservice');
        $firebase_server_key = $setting_data['firebase_server_key'];

        $push_type = '';
        $title = '';
        $message = '';
        if ($type == 'order_create') {
            if ($setting_data['order_success_status'] == 0) {
                return false;
            } else {
                $title = $setting_data['order_success_title'];
                $message = $setting_data['order_success_message'];
                $push_type = 'order_placed';
            }
        } elseif ($type == 'order_status_change') {
            if ($setting_data['status_change_status'] == 0) {
                return false;
            } else {
                $title = $setting_data['status_change_title'];
                $message = str_replace(
                        '{{STATUS}}', $order_status, $setting_data['status_change_title']
                );
                $push_type = 'order_status_changed';
            }
        }

        $user_id = ""; // user_id

        $firebase_data = array();
        $firebase_data['data']['title'] = $title;
        $firebase_data['data']['is_background'] = false;
        $firebase_data['data']['message'] = $message;
        $firebase_data['data']['image'] = '';
        $firebase_data['data']['payload'] = '';
        $firebase_data['data']['user_id'] = $user_id;
        $firebase_data['data']['push_type'] = $push_type;
        $firebase_data['data']['cart_id'] = $cart_id;
        $firebase_data['data']['order_id'] = $order_id;
        $firebase_data['data']['email_id'] = $email;

        if ($fcm_id) {
            $this->sendMultiple($fcm_id, $firebase_data, $firebase_server_key);
        }
    }

    // sending push message to single user by firebase reg id
    public function send($to, $message, $server_key) {
        $fields = array(
            'to' => $to,
            'data' => $message,
        );
        return $this->sendPushNotification($fields, $server_key);
    }

    // Sending message to a topic by topic name
    public function sendToTopic($to, $message, $server_key) {
        $fields = array(
            'to' => '/topics/' . $to,
            'data' => $message,
            'priority'=> "high",
            'mutable_content' => true,
            'content_available' => true,
            'notification'=> array(
                'title'=> $message['data']['title'],
                'body'=> $message['data']['message']
            ),
        );
        return $this->sendPushNotification($fields, $server_key);
    }

    public function sendMultiple($registration_ids, $message, $server_key) {
        $fields = array(
            'to' => $registration_ids,
            'data' => $message,
            'priority'=> "high",
            'mutable_content' => true,
            'content_available' => true,
            'notification'=> array(
                'title'=> $message['data']['title'],
                'body'=> $message['data']['message']
            ),
        );

        return $this->sendPushNotification($fields, $server_key);
    }

    // function makes curl request to firebase servers
    private function sendPushNotification($fields, $server_key = '') {

        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=' . $server_key,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));


        // Execute post
        $result = curl_exec($ch);


        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        return $result;
    }

}

?>
