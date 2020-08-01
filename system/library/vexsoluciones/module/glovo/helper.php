<?php
namespace Vexsoluciones\Module\Glovo;

class Helper
{
    public function __construct()
    {
    }

    public static function instance()
    {
        return new static();
    }

    /**
     * Used in Admin
     *
     * @param $lat
     * @param $lng
     * @return object
     * @throws \Exception
     */
    public function obtainAirportCodeFromLatLng($lat, $lng)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://iatageo.com/getCode/{$lat}/{$lng}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 500);
        $content = json_decode(trim(curl_exec($ch)));
        $error = '';

        if (curl_error($ch))
        {
            $error = curl_error($ch);
        }
        curl_close($ch);

        if ($error !== '') throw new \Exception($error);
        if (isset($content->error)) throw new \Exception($content->error->message);

        return ((object) $content)->IATA;
    }
}