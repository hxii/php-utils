<?php

namespace hxii;

class Curium {
    
    function makeRequest(string $type = 'GET', string $url = 'https', $payload = null, $extraOpts = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        // if ('POST' === $type && $payload) {
        if (isset($payload) && !empty($payload)) {
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
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if ($curl_errno > 0) {
            echo "cURL Error ($curl_errno): $curl_error\n";
            return false;
        } else {
            return $result;
        }
    }

    function simplePath($json, string $path) {
        if (is_string($json) || is_object($json)) {
            $json = json_decode($json, true);
        }
        $parts = explode('.', $path);
        
    }

}