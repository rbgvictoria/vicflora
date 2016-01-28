<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('getRecords')) {
    function getRecords($from, $pageSize=1000, $start=0, $dataSource='AVH') {
        $ci =& get_instance();
        $ci->load->helper('curl');
        $ci->load->helper('ala_ws');
        $ci->load->model('mapmodel');
        $url = "http://biocache.ala.org.au/ws/occurrences/search";
        
        if ($from == 'all' || $from == '*') {
            $startDate = '*';
        }
        else {
            $startDate = $from . 'T00:00:00Z';
        }
        
        $intro = array(
            NULL => '-(establishment_means:"not native" OR establishment_means:cultivated OR establishment_means:"presumably cultivated" OR establishment_means:"possibly cultivated" OR establishment_means:"doubtfully native")',
            'introduced' => '(establishment_means:"not native" OR establishment_means:"doubtfully native")',
            'cultivated' => '(establishment_means:cultivated OR establishment_means:"presumably cultivated" OR establishment_means:"possibly cultivated")'
        );
        $totalRecords = 0;
        foreach ($intro as $key=>$value) {
            $startIndex = $start;
            $recordCount = $start + 1;
        
            while ($startIndex < $recordCount) {
                $params = array();
                $params['fq'] = array();
                $params['fq']['data_hub_uid'] = 'dh2';
                $params['fq']['state'] = 'Victoria';
                $params['fq']['latitude'] = '[* TO *]';
                $params['fq']['longitude'] = '[* TO *]';
                $params['fq'][] = $value;
                $params['fq']['last_load_date'] = '[' . $startDate . ' TO *]';
                $params['fl'] = array(
                    'id',
                    'taxon_concept_lsid',
                    'genus_guid',
                    'species_guid',
                    'rank',
                    'genus',
                    'species',
                    'raw_taxon_name',
                    'catalogue_number',
                    'taxon_name',
                    'latitude',
                    'longitude',
                    'kingdom',
                    'phylum',
                    'class',
                    'order',
                    'family'
                );
                $params['facet'] = 'off';
                $params['pageSize'] = $pageSize;
                $params['startIndex'] = $startIndex;
        
                $query = createQueryString('biocache_search', $params);
                $result = doCurl($query, FALSE, TRUE);
                if ($result) {

                    $recordCount = $ci->mapmodel->loadAvhData($result, $key);
                }
                $startIndex += $pageSize;
                if ($ci->input->is_cli_request()) {
                    if ($startIndex < $recordCount) {
                        echo $startIndex . ' of ' . $recordCount . PHP_EOL;
                    }
                    else {
                        echo $recordCount . ' of ' . $recordCount . PHP_EOL;
                    }
                }
            }
            $totalRecords += $recordCount;
        }
        return $totalRecords;
    }

    function getVbaRecords($from, $pageSize=1000, $start=0) {
        $ci =& get_instance();
        $ci->load->helper('curl');
        $ci->load->model('mapmodel');
        $url = "http://biocache.ala.org.au/ws/occurrences/search";
        
        if ($from == 'all' || $from == '*') {
            $startDate = '%2A';
        }
        else {
            $startDate = $from . 'T00:00:00Z';
        }
        
        $startIndex = $start;
        $recordCount = $start + 1;

        while ($startIndex < $recordCount) {
            $query = "q=%2A%3A%2A&fq=data_resource_uid%3Adr1097&fq=state:Victoria&fq=class%3AEquisetopsida&fq=last_load_date%3A%5B{$startDate}%20TO%20%2A%5D&fl=id,taxon_concept_lsid,genus_guid,species_guid,rank,genus,species,raw_taxon_name,catalogue_number,taxon_name,latitude,longitude,kingdom,phylum,class,order,family&facet=off&pageSize=$pageSize&startIndex=$startIndex";;

            $result = doCurl($url, $query, TRUE);
            if ($result) {

                $recordCount = $ci->mapmodel->loadAvhData($result, FALSE, 'VBA');
            }
            $startIndex += $pageSize;
            echo $startIndex . ' of ' . $recordCount . PHP_EOL;
        }
        return $recordCount;
    }

    
}