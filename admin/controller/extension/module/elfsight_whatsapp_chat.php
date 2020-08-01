<?php

const platform_alias = 'opencart';
const api_url = 'https://apps.elfsight.com/api/v1/public/portal/status';
const support_email = 'support@elfsight.com';

if (!empty($_POST)) {
    $deactivate = new Deactivate($_POST);

    $deactivate->SendDeactivateRequest();
    $deactivate->deactivateMessage();
}

class Deactivate {
    public $app_slug;
    public $app_name;
    public $plugin_text_domain;
    public $deactivate_reasons;
    public $admin_email;
    public $site_url;
    public $email;
    public $deactivate = false;
    public $reason_id;
    public $reason_text;
    public $reason_message;

    private $send_message = false;
    private $send_coupon = false;
    private $subject = 'OpenCart Portal Deactivate';
    private $message;
    private $headers;

    public $date;
    public $result;

    public function __construct($data) {
        $this->data = $data;

        $this->app_slug = 'whatsapp-chat';
        $this->app_name = 'Whatsapp Chat';
        $this->plugin_text_domain  = $_SERVER['SERVER_NAME'];
        $this->email = $data['email'];
        $this->send_coupon = $data['reason_id'] === 3 || $data['reason_id'] === 4 ? true : false;

        $this->site_url = $_SERVER['SERVER_NAME'];

        $this->reasons = $this->setDeactivateReasons();
    }

    public function setDeactivateReasons() {
        return require ( (__DIR__) . '/whatsapp-chat-assets/reasons.php' );
    }

    public function SendDeactivateRequest() {
        $curl = curl_init();

        $response = array();

        $response['platform_alias'] = platform_alias;
        $response['email'] = $this->email;
        $response['admin_email'] = $this->email;
        $response['app_name'] = $this->app_name;
        $response['site_url'] = $this->site_url;
        $response['event'] = 'deactivated';
        $response['send_coupon'] = $_POST['reason_id'] === 3 || $_POST['reason_id'] === 4 ? true : false;

        if(isset($this->data['reason_message'])) {
            $response['deactivate_message'] = $this->data['reason_message'];
            $response['deactivate_reason'] = $this->data['reason_message'];
        } else {
            $response['deactivate_message'] = 'coupon request';
            $response['deactivate_reason'] = 'coupon request';
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($response)
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function deactivateMessage() {
        $this->email = strip_tags($this->data['email']);

        $this->deactivate = true;

        $this->reason_id = $this->data['reason_id'];
        $this->reason_text = $this->reasons[$this->reason_id]['text'];

        if (!empty($this->data['reason_message'])) {
            $this->reason_message = $this->data['reason_message'];
        }

        switch ($this->reason_id) {
            case '1':
                $this->subject = 'OpenCart Portal Deactivate - Ticket';
                $this->send_message = true;
                break;
            case '2':
                $this->send_message = true;
                break;
            case '3':
            case '4':
                $this->send_coupon = true;
                break;
            case '6':
                $this->send_message = $this->reason_message ? true : false;
                break;
            default:
                break;
        }

        $this->date = date("F j, Y, g:i a");

        $this->message = $this->setMessage();

        $this->headers = $this->setHeaders($this->email);

        $this->sendDeactivateMessage();
    }

    public function setMessage() {
        $message = '<html><body>';
        $message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
        $message .= '<tr><td style="background: #eee;"><strong>Portal:</strong></td><td>' . $this->app_name . '</td></tr>';
        $message .= '<tr><td style="background: #eee;"><strong>Reason:</strong></td><td>' . $this->reasons[$this->reason_id]['text'] . '</td></tr>';
        if ($this->data['reason_message']) {
            $message .= '<tr><td style="background: #eee;"><strong>Message:</strong></td><td style="white-space: pre-wrap;">' . $this->data['reason_message'] . '</td></tr>';
        }
        if ($this->email) {
            if ($this->send_coupon) {
                $message .= '<tr><td style="background: #eee;"><strong>Coupon sent to:</strong></td><td>' . $this->email . '</td></tr>';
            } else {
                $message .= '<tr><td style="background: #eee;"><strong>Contact at:</strong></td><td>' . $this->email . '</td></tr>';
            }
        } else {
            $message .= '<tr><td style="background: #eee;"><strong>From:</strong></td><td>' . $this->email . '</td></tr>';
        }
        $message .= '<tr><td style="background: #eee;"><strong>Site:</strong></td><td>' . $this->site_url . '</td></tr>';
        $message .= '<tr><td style="background: #eee;"><strong>Server date:</strong></td><td>' . $this->date . '</td></tr>';
        $message .= '</table>';
        $message .= '</body></html>';

        return $message;
    }

    public function setHeaders($from) {
        $headers = 'From: ' . $from . "\r\n";
        $headers .= 'Reply-To: ' . $from . "\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: text/html; charset="utf-8"';

        return $headers;
    }

    public function sendDeactivateMessage() {
        if ($this->send_message) {
            $mail_status = mail(support_email, $this->subject, $this->message, $this->headers);
            if ($mail_status) {
                $this->result['mail']['status'] = 'OK';
                $this->result['mail']['message'] = 'Mail successfully sent to ' . support_email;
            } else {
                $this->result['mail']['status'] = 'ERROR';
                $this->result['mail']['message'] = 'Mail error sent to ' . support_email;
            }
        } else {
            $this->result['mail']['status'] = 'OK';
            $this->result['mail']['message'] = 'Mail not sent for selected reason: "'. $this->reason_text .'"';
        }
    }
}

class ControllerExtensionModuleElfsightWhatsappChat extends Controller {
    private $error = array();

    public $params = NULL;

    const ELFSIGHT_EMBED_URL = 'https://apps.elfsight.com/embed/whatsapp-chat/?utm_source=portals&utm_medium=opencart&utm_campaign=whatsapp-chat&utm_content=sign-up';

    public function index() {
        $this->load->language('extension/module/elfsight_whatsapp_chat');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_elfsight_whatsapp_chat', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $params = json_encode([
            'user' => [
                'configEmail' => $this->config->get('config_email')
            ]
        ]);

        $data['url'] = $this::ELFSIGHT_EMBED_URL;

        if (!empty($params)) {
            $data['url'] .= (parse_url($data['url'], PHP_URL_QUERY) ? '&' : '?') . 'params=' . rawurlencode($params);
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['action'] = $this->url->link('extension/module/elfsight_whatsapp_chat', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_whatsapp_chat_status'])) {
            $data['module_whatsapp_chat_status'] = $this->request->post['module_whatsapp_chat_status'];
        } else {
            $data['module_whatsapp_chat_status'] = $this->config->get('module_whatsapp_chat_status');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');

        $this->response->setOutput($this->load->view('extension/module/elfsight_whatsapp_chat', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/elfsight_whatsapp_chat')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function setDeactivateReasons() {
        return require ( './controller/extension/module/whatsapp-chat-assets/reasons.php' );
    }

    public function install() {
        $this->sendRequest('activate');

        return;
    }

    public function uninstall() {
        $reasons = $this->setDeactivateReasons();

        $this->sendRequest('deactivate');

        include './controller/extension/module/whatsapp-chat-assets/popup.php';
    }

    public function sendRequest($type) {
        $curl = curl_init();

        $data = array();

        $data['platform_alias'] = platform_alias;
        $data['email'] = $this->config->get('config_email');
        $data['app_name'] = 'WhatsappChat';
        $data['admin_email'] = $this->config->get('config_email');
        $data['site_url'] = $_SERVER['SERVER_NAME'];

        if($type === 'deactivate') {
            $data['event'] = 'deactivated';
            $data['deactivate_reason'] = 'close popup without action';
            $data['deactivate_message'] = 'close popup without action';
        } else {
            $data['event'] = 'activated';
            $data['deactivate_reason'] = '';
            $data['deactivate_message'] = '';
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data)
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }
}