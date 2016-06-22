<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'VicFloraMap.php';

class StateDistribution extends VicFloraMap {

    public function __construct() {
        parent::__construct();
    }
    
    public function stateDistribution() {
        $states = $this->getStates();
        foreach ($states as $state) {
            $taxa = $this->getTaxaForState($state);
            foreach ($taxa as $name) {
                $taxon = $this->getTaxonId($name);
                if (!$taxon) {
                    $taxon = $this->getTaxonIdMatched($name);
                    if (!$taxon) {
                        continue;
                    }
                }
                if (is_object($taxon)) {
                    $nameData = $this->getNameData($taxon->GUID);
                    if ($nameData) {
                        $update = array(
                            'taxon_id' => $nameData['taxon_id'],
                            'genus_guid' => $nameData['genus_guid'],
                            'species_guid' => $nameData['species_guid'],
                            'scientific_name' => $nameData['scientific_name'],
                            'genus' => $nameData['genus'],
                            'species' => $nameData['species'],
                            'state_province' => $state
                        );
                        $stateDistributionId = $this->ci->mapmodel->findStateDistributionRecord($nameData['taxon_id'], $state);
                        if ($stateDistributionId) {
                            $update['timestamp_modified'] = date('Y-m-d H:i:s');
                            $this->ci->mapmodel->updateStateDistributionRecord($update, $stateDistributionId);
                        }
                        else {
                            $update['timestamp_created'] = date('Y-m-d H:i:s');
                            $this->ci->mapmodel->insertStateDistributionRecord($update);
                        }
                    }
                    else {
                        print_r($taxon);
                    }
                }
            }
        }
    }
    
    private function getStates() {
        $params = array();
        $params['fq'] = array(
            'data_hub_uid' => 'dh2'
        );
        $params['facets'] = 'state';
        $params['pageSize'] = 1;
        $query = createQueryString('biocache_search', $params);
        $result = doCurl($query, FALSE, TRUE);
        $data = json_decode($result);
        $facets = $data->facetResults;
        $states = array();
        foreach ($facets[0]->fieldResult as $facet) {
            $states[] = $facet->label;
        }
        return $states;
    }
    
    private function getTaxaForState($state) {
        $params = array();
        $params['fq'] = array();
        $params['fq']['data_hub_uid'] = 'dh2';
        $params['fq']['class'] = 'Equisetopsida';
        $params['fq']['state'] = '"' . $state . '"';
        $params['fq'][] = '(rank:species OR rank:subspecies OR rank:variety OR rank:form)';
        $params['pageSize'] = 1;
        $params['facets'] = 'taxon_name';
        $params['flimit'] = 100000;
        $query = createQueryString('biocache_search', $params);
        $result = doCurl($query, FALSE, TRUE);
        $data = json_decode($result);
        $facets = $data->facetResults;
        $taxa = array();
        foreach ($facets[0]->fieldResult as $facet) {
            $taxa[] = $facet->label;
        }
        return $taxa;
    }
}


/* End of file StateDistribution.php */
/* Location: ./models/StateDistribution.php */

