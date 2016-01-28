<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WSCurl extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->config->load('vicflora_config');
    }
    
    public function search_nsl_name_id ($nameid) {
        $url = "http://biodiversity.org.au/apni.name/$nameid.json";
        $result = $this->doCurl($url);
        if ($this->isJson($result))
            echo $result;
        else
            echo '[]';
    }
    
    public function search_nsl_name($name) {
        $url = "http://biodiversity.org.au/name/$name.json";
        $result = $this->doCurl($url);
        header('Content-type: application/json');
        if ($this->isJson($result))
            echo $result;
        else
            echo '[{}]';
    }
    
    private function doCurl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $curl_opts = $this->config->item('curl_opts');
        if ($curl_opts['proxy']) {
            curl_setopt($ch, CURLOPT_PROXY, $curl_opts['proxy']); 
            curl_setopt($ch, CURLOPT_PROXYPORT, $curl_opts['proxy_port']); 
            curl_setopt ($ch, CURLOPT_PROXYUSERPWD, $curl_opts['proxy_userpwd']);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    private function isJson($string) {
        json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

    
}


/* End of file wscurl.php */
/* Location: ./controllers/wscurl.php */
