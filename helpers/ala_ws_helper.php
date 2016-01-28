<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('createQueryString')) {
    function createQueryString($service, $params) {
        $services = array(
            'biocache_search' => 'http://biocache.ala.org.au/ws/occurrences/search'
        );
        
        $url = $services[$service];

        $query = array();
        
        $query['q'] = '*:*';
        if (isset($params['q']) && $params['q']) {
            $query['q'] = urlencode($params['q']);
        }
        
        if (isset($params['fq']) && $params['fq']) {
            $fq = array();
            foreach ($params['fq'] as $key => $value) {
                if ($value) {
                    if ($key) {
                        $fq[] = $key . ':' . urlencode($value);
                    }
                    else {
                        $fq[] = urlencode($value);
                    }
                }
            }
            $query['fq'] = $fq;
        }
        
        if (isset($params['fl']) && $params['fl']) {
            $query['fl'] = implode(',', $params['fl']);
        }
        
        if (isset($params['facet']) && $params['facet']) {
            $query['facet'] = $params['facet'];
        }
        
        if (isset($params['facets']) && $params['facets']) {
            $query['facets'] = $params['facets'];
        }
        
        if (isset($params['flimit']) && $params['flimit']) {
            $query['flimit'] = $params['flimit'];
        }
        
        if (isset($params['pageSize']) && $params['pageSize']) {
            $query['pageSize'] = $params['pageSize'];
        }
        
        if (isset($params['startIndex']) && $params['startIndex']) {
            $query['startIndex'] = $params['startIndex'];
        }
        
        $qstring = array();
        foreach ($query as $key => $item) {
            if (is_array($item)) {
                foreach ($item as $value) {
                    $qstring[] = $key . '=' . $value;
                }
            }
            else {
                $qstring[] = $key . '=' . $item;
            }
        }
        return $url . '?' . implode('&', $qstring);
    }
    
    
}