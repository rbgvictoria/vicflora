<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('cumulus_update')) {
    function cumulus_update($file) {
        $infile = fopen($file, 'r');
        $firstLine = fgetcsv($infile);
        $csvArray = array();
        while (!feof($infile)) {
            $assoc = array();
            $line = fgetcsv($infile);
            if ($line[0]) {
                foreach ($line as $index => $value) {
                    $assoc[$firstLine[$index]] = ($value) ? Encoding::toUTF8($value) : NULL;
                }
                $csvArray[] = $assoc;
            }
        }
        fclose($infile);
        print_r($csvArray);
    }
}