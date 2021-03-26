<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('json_output')) {
    /**
     * function json_output
     * 
     * Adds headers for cross-server access and JSON, includes the callback,
     * if one is given (for JSONP requests and converts PHP object or array
     * to JSON string.
     * 
     * @param type $data
     * @return type string
     */
    function json_output($data) {
        $json = json_encode($data);
        header('Access-Control-Allow-Origin: *');  
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
        header('Content-type: application/json');
        if (isset($_GET['callback']) && $_GET['callback'])
            return $_GET['callback'] . '(' . $json . ')';
        else
            return $json;
    }
}