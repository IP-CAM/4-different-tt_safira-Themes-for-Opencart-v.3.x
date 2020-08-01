<?php
namespace Vexsoluciones\Module\Glovo;

use Vexsoluciones\Module\Glovo\Model\Address;
use Vexsoluciones\Module\Glovo\Model\Order;
use Vexsoluciones\Module\ShippingGlovoConstants;

class Client
{
    const API_URL_PRODUCTION = 'https://api.glovoapp.com/';
    const API_URL_DEVELOPMENT = 'https://testapi.glovoapp.com/';

    private $apiKey;
    private $apiSecret;
    private $headers;

    public function __construct($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;

        $this->headers = [
            'Authorization: Basic ' . base64_encode("{$apiKey}:{$apiSecret}"),
            'Content-Type: application/json'
        ];
    }

    private function performGetRequest($resource)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getActionUrl($resource));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 500);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $content = json_decode(trim(curl_exec($ch)));
        $error = '';

        if (curl_error($ch))
        {
            $error = curl_error($ch);
        }
        curl_close($ch);

        if ($error !== '')
            throw new GlovoException($error);

        if (isset($content->error))
            throw new GlovoException($content->error->message);

        return (object) $content;
    }

    private function performPostRequest($resource)
    {

    }

    public function estimateOrder(Order $order)
    {
        ShippingGlovoConstants::logger()->write('[Estimate Order] ' . json_encode($order->toArray()));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getActionUrl('b2b/orders/estimate'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order->toArray()));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $content = curl_exec($ch);

        if (curl_error($ch))
        {
            $error = curl_error($ch);
            curl_close($ch);

            throw new GlovoException($error);
        }
        curl_close($ch);

        $content = json_decode($content);

        if (isset($content->error))
        {
            ShippingGlovoConstants::logger()->write('[Error] ' . json_encode($content));
            throw new GlovoException($content->error);
        }

        return $content->total;
    }

    public function createOrder(Address $source, Address $destination)
    {

    }

    private function getActionUrl($url)
    {
        return "https://api.glovoapp.com/{$url}";
    }

    public function workingAreas()
    {
        return $this->performGetRequest('b2b/working-areas');
    }

    public function trackByOrderId($orderId)
    {
        return $this->performGetRequest("/b2b/orders/{$orderId}/tracking");
    }

    public function getCourierContact($orderId)
    {
        return $this->performGetRequest("/b2b/orders/{$orderId}/courier-contact");
    }
}