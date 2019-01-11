<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('doCurl')) {
    function doCurl($url, $query=FALSE, $proxy=FALSE) {
        $ch = curl_init();
        if ($query) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $query);
        }
        else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        //curl_setopt($ch,CURLOPT_POST, TRUE);
        //curl_setopt($ch,CURLOPT_POSTFIELDS, $postfields);    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, "http://10.15.14.4:8080"); 
            curl_setopt($ch, CURLOPT_PROXYPORT, 8080); 
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}