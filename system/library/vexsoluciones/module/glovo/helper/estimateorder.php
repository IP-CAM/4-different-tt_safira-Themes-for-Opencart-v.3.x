<?php
namespace Vexsoluciones\Module\Glovo\Helper;

class EstimateOrder
{
    public $baseAmount;     //Amount returned from Glovo API
    public $currency;       //Currency returned from Glovo API
    public $finalAmount;    //Amount converted for shown in cart
    public $amountInUSD;    //Amount for saving session, used by OC last stage in checkout
    public $amountFormatted;    //Shown in checkout

    public function __construct($baseAmount, $currency)
    {
        $this->baseAmount = $baseAmount;
        $this->currency = $currency;
    }

    public function calculate(\Cart\Currency $currency, \Session $session, \Language $language)
    {
        $this->amountInUSD = $currency->convert($this->baseAmount / 100, $this->currency, 'USD');
        $this->amountFormatted = $currency->format($this->baseAmount / 100, $session->data['currency'], 1);

        if ($this->baseAmount == 0) $this->amountFormatted = $language->get('entry_free');

        return $this;



        // 1° PEN to USD
        // 2° USD to CLP

        if ($currency->getId($this->currency) === 0)
            throw new \Exception("Glovo API returns currency {$currency} and this is not configured yet, please go to admin and add this currency.");

        $amount = $this->baseAmount / 100;
        $this->amountInUSD = $currency->convert($amount, $this->currency, 'USD');

        $amount1 = $currency->convert($this->amountInUSD, 'USD', $session->data['currency']);
        $this->amountFormatted = $currency->format($amount1, $session->data['currency'], 1);

        if ($this->baseAmount == 0) $this->amountFormatted = $language->get('entry_free');

        return $this;
    }

    /**
     * @param $cart
     * @param $amount
     * @return void
     */
    public function calculateFreeShippingCost($cart, $amount)
    {
        if ((float)$cart->getSubTotal() >= (float)$amount)
        {
            $this->baseAmount = 0;
        }
    }
}