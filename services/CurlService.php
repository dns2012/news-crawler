<?php

namespace Service;

class CurlService
{
    public static function request($url = null, $params = null)
    {
        $opts = [
            CURLOPT_URL             => $url,
            CURLOPT_USERAGENT       => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_TIMEOUT         => 30
        ];

        if (!empty($params)) {
            $opts[CURLOPT_URL]  = $opts[CURLOPT_URL] . '?' . http_build_query($params);
        }

        $curl = curl_init();
        curl_setopt_array($curl, $opts);

        $result = curl_exec($curl);
        // $info = curl_getinfo($curl);

        curl_close($curl);

        return $result;
    }
}
