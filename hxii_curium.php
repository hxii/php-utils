<?php

namespace hxii;

class Curium {
    
    function makeRequest(string $type = 'GET', string $url, $payload = null, $extraOpts = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ('POST' === $type && $payload) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_POST, 1);
            if (is_array($payload)) {
                $payload = json_encode($payload);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
        if (is_array($extraOpts)) {
            curl_setopt_array($ch, $extraOpts);
        }
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if (200 !== $info['http_code']) {
            return false;
        } else {
            return $result;
        }
    }

}