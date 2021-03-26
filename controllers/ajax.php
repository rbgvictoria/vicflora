<?php

class Ajax extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->output->enable_profiler(FALSE);
        $this->load->model('facetmodel');
        $this->config->load('vicflora_config');
    }
    
    public function facet($facet) {
        $json = array();
        
        switch ($facet) {
            case 'establishmentMeans':
                $numbers = $this->facetmodel->facetEstablishmentMeans($this->input->get('term'), $this->input->get());

                $est = array();
                if ($this->input->get('establishmentMeans'))
                    $est = explode(',', $this->input->get('establishmentMeans'));

                $json[] = '{';
                $json[] = '"name":"establishmentMeans",';
                $json[] = '"title":"Establishment means",';
                $json[] = '"items":[';
                $num = (isset($numbers['native'])) ? $numbers['native'] : 0;
                $checked = (in_array('native', $est)) ? 1 : 0;
                $json[] = '{"name":"native","label": "Native","count":' . $num . ', "checked":' . $checked . '},';
                $num = (isset($numbers['introduced'])) ? $numbers['introduced'] : 0;
                $checked = (in_array('introduced', $est)) ? 1 : 0;
                $json[] = '{"name":"introduced","label": "Introduced","count":' . $num . ', "checked":' . $checked . '}';
                $json[] = ']';
                $json[] = '}';
                break;

            case 'taxonomicStatus':
                $numbers = $this->facetmodel->facetTaxonomicStatus($this->input->get('term'), $this->input->get());

                $values = array();
                if ($this->input->get('taxonomicStatus'))
                    $values = explode(',', $this->input->get('taxonomicStatus'));

                $json[] = '{';
                $json[] = '"name":"taxonomicStatus",';
                $json[] = '"title":"Taxonomic status",';
                $json[] = '"items":[';
                $num = (isset($numbers['accepted'])) ? $numbers['accepted'] : 0;
                $checked = (in_array('accepted', $values)) ? 1 : 0;
                $json[] = '{"name":"accepted","label": "Accepted","count":' . $num . ', "checked":' . $checked . '},';
                $num = (isset($numbers['notCurrent'])) ? $numbers['notCurrent'] : 0;
                $checked = (in_array('notCurrent', $values)) ? 1 : 0;
                $json[] = '{"name":"notCurrent","label": "Not current","count":' . $num . ', "checked":' . $checked . '}';
                $json[] = ']';
                $json[] = '}';
                break;

            default:
                break;
        }
        
        
        $json = implode('', $json);


        header('Content-type: application/json');
        echo $json;
    }
    
    public function new_name($fullname) {
        if (!$fullname)
            exit();
        $fullname = html_entity_decode(urldecode($fullname));
        header('Content-type: application/json');
        $this->load->model('taxonmodel');
        $fullname = urldecode($fullname);
        $data = $this->taxonmodel->getNewName($fullname);
        $json = json_encode($data);
        echo $json;
    }
    
    public function accepted_name_by_id($id) {
        if (!$id)
            exit();
        header('Content-type: application/json');
        $this->load->model('taxonmodel');
        $data = $this->taxonmodel->getAcceptedNameByID($id);
        $json = json_encode($data);
        echo $json;
    }
    
    public function taxon_by_name($name) {
        if (!$name)
            exit();
        header('Content-type: application/json');
        $this->load->model('taxonmodel');
        $data = $this->taxonmodel->getTaxonByName(urldecode($name));
        $json = json_encode($data);
        echo $json;
    }
    
    public function parent_by_name($name) {
        if (!$name)
            exit();
        header('Content-type: application/json');
        $this->load->model('taxonmodel');
        $data = $this->taxonmodel->getParentByName(urldecode($name));
        $json = json_encode($data);
        echo $json;
    }
    
    public function glossary_terms($firstletter) {
        if (!$firstletter)
            exit();
        header('Content-type: application/json');
        $this->load->model('glossarymodel');
        $data = $this->glossarymodel->getGlossaryTerms($firstletter);
        $json = json_encode($data);
        echo $json;
    }
    
    public function glossary_definition($term) {
        if (!$term)
            exit();
        $ret = array();
        $term = html_entity_decode(urldecode($term));
        header('Content-type: application/json');
        $this->load->model('glossarymodel');
        $data = $this->glossarymodel->getGlossaryDefinition($term);
        if ($data) {
            $ret['term'] = $data['term'];
            $ret['termID'] = $data['TermID'];
            $ret['definition'] = $data['definition'];
            $ret['relationships'] = $this->glossarymodel->getGlossaryTermRelationships($data['TermID']);
            $ret['thumbnails'] = $this->glossarymodel->getGlossaryImages($term);
        }
        $json = json_encode((object) $ret);
        echo $json;
    }
    
    public function capad_from_map_point($long, $lat) {
        $this->load->model('checklistmodel');
        $data = $this->checklistmodel->getCapadFromMap($long, $lat);

        $json = json_encode($data);
        echo $json;
    }
    
    public function occurrences_from_point($taxonid, $long, $lat) {
        $this->load->model('checklistmodel');
        $data = $this->checklistmodel->getOccurrencesFromPoint($taxonid, $long, $lat);
        $json = json_encode($data);
        echo $json;
    }
    
    public function bioregion_legend($guid, $fill) {
        $this->load->model('viewtaxonmodel', 'taxonmodel');
        $this->load->model('mapmodel');
        $legend = $this->legendBioregion($fill);
        $rankID = $this->taxonmodel->getRankID($guid);
        $distribution = $this->mapmodel->getDistributionDetail($guid, $rankID);
        
        $codes = array();
        foreach ($legend as $item) {
            $codes[] = $item['code'];
        }
        
        $data = array();
        foreach ($distribution as $row) {
            $item = array();
            if ($fill == 'bioregion') {
                $key = array_search($row['sub_code_7'], $codes);
            }
            else {
                $key = array_search($row['occurrence_status'], $codes);
                
                if ($key === FALSE) {
                    $key = array_search($row['establishment_means'], $codes);
                }
            }
            
            if ($key !== FALSE) {
                $item['colour'] = $legend[$key]['colour'];
            }
            else {
                $item['colour'] = '#ffffff';
            }
            $item['sub_name_7'] = $row['sub_name_7'];
            $item['occurrence_status'] = $row['occurrence_status'];
            $item['establishment_means'] = $row['establishment_means'];
            $data[] = (object) $item;
        }
        $json = json_encode($data);
        echo $json;
    }
    
    private function legendBioregion($fill) {
        $geoserverurl = '10.15.15.107:65002/geoserver';
        if ($fill == 'bioregion') {
            $url = $geoserverurl . '/rest/workspaces/vicflora/styles/vic_bioregions.sld';
        }
        else {
            $url = $geoserverurl . '/rest/workspaces/vicflora/styles/polygon_establishment_means.sld';
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:dicranoloma');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $result = curl_exec($ch);
        curl_close($ch);
        
        $legend = array();

        $doc = new DOMDocument();
        $doc->loadXML($result);
        
        $rules = $doc->getElementsByTagName('Rule');
        if ($rules->length) {
            foreach ($rules as $rule) {
                $name = $rule->getElementsByTagName('Name')->item(0)->nodeValue;
                $sub = $rule->getElementsByTagName('Literal')->item(0)->nodeValue;
                $css = $rule->getElementsByTagName('CssParameter');
                if ($css->length) {
                    foreach ($css as $item) {
                        if ($item->getAttribute('name') == 'fill') {
                            $colour = $item->nodeValue;
                        }
                    }
                }
                $legend[] = array(
                    'name' => $name,
                    'code' => $sub,
                    'colour' => $colour
                );
            }
        }
        
        return $legend;
    }
    
    private function doCurl($url, $query) {   
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . $query);
        //curl_setopt($ch,CURLOPT_POST, TRUE);
        //curl_setopt($ch,CURLOPT_POSTFIELDS, $postfields);    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        //curl_setopt($ch, CURLOPT_PROXY, "http://proxy.rbg.vic.gov.au:8080"); 
        //curl_setopt($ch, CURLOPT_PROXYPORT, 8080); 
        //curl_setopt ($ch, CURLOPT_PROXYUSERPWD, "nklaze:32dicranol");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function ibra_map($guid, $format='img', $fill=FALSE) {
        $this->load->model('taxonmodel');
        $tdata = $this->taxonmodel->getTaxonDataForMap($guid);
        $url = $this->config->item('geoserver_url') . "/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:cst_vic,vicflora:ibra_taxon_view,vicflora:vicflora_bioregion,vicflora:cst_vic,vicflora:vicflora_occurrence';
        if ($fill == 'establishment_means') {
            $query['styles'] = ',polygon_establishment_means,polygon_no-fill_grey-outline,polygon_no-fill_black-outline,point_establishment_means';
        }
        else {
            $query['styles'] = ',vic_bioregions,polygon_no-fill_grey-outline,polygon_no-fill_black-outline,point_establishment_means';
        }
        $query['bbox'] = '140.8,-39.3,150.2,-33.8';
        $query['width'] = 600;
        $query['height'] = 363;
        $query['srs'] = 'EPSG:4326';
        $query['format'] = 'image/svg';
        $term = ($tdata['RankID'] == 220) ? 'species_guid' : 'taxon_id';
        $query['cql_filter'] = urlencode("FEAT_CODE IN ('mainland','island');taxon_id='$guid';INCLUDE;INCLUDE;$term='$guid'");
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        
        
        if ($format == 'svg') {
            $svg = $this->doCurl($url, $qstring);
            echo $svg;
        }
        else {
            echo $url . '?' . $qstring;
        }
    }
    
    public function bioregionInfo($title=false) {
        if (!$title) {
            exit();
        }
        $this->load->model('mapmodel');
        $data = $this->mapmodel->getBioregionInfo(urldecode($title));
        if ($data) {
            $json = json_encode($data);
            header('Content-type: application/json');
            echo $json;
        }
        
    }
    
    public function update_occurrence() {
        $this->load->model('checklistmodel');
        $this->load->model('mapmodel');
        $data = $_REQUEST;
        $uuid = array_shift($data);
        if ($this->input->get_post('occurrence_status')) {
            $this->mapmodel->insertVicFloraAssertion($_REQUEST['uuid'], 'occurrenceStatus', $data['occurrence_status']);
        }
        elseif ($this->input->get_post('establishment_means')) {
            $this->mapmodel->insertVicFloraAssertion($_REQUEST['uuid'], 'establishmentMeans', $data['establishment_means']);
        }
        $occ = $this->mapmodel->getBioregionForOccurrence($uuid);
        header('Content-type: application/json');
        echo json_encode($occ);
    }
    
    public function create_distribution_map($guid) {
        $this->load->model('mapmodel');
        $records = $this->mapmodel->getOccurrenceRecords($guid);
        if ($records) {
            foreach ($records as $rec) {
                if (isset($rec->ala_unprocessed_scientific_name) || isset($rec->ala_scientific_name)) {
                    $fid = $this->mapmodel->getVicFloraOccurrence($rec->uuid);
                    if ($fid) {
                        $this->mapmodel->updateVicFloraOccurrence($rec, $fid);
                    }
                    else {
                        $this->mapmodel->createVicFloraOccurrence($rec);
                    }
                }
                else {
                    $this->mapmodel->createVicFloraOccurrence($rec);
                }
                $this->mapmodel->insertVicFloraAssertion($rec->uuid, 'scientificName', $rec->scientific_name);
            }
            $this->mapmodel->createVicfloraDistribution($guid);
        }
    }
    
    public function accept_map_updates() {
        $this->load->model('mapmodel');
        $this->mapmodel->acceptMapUpdates($this->input->get_post('taxon_id'));
        return TRUE;
    }
    
    public function get_occurrence_records($guid) {
        $this->load->model('mapmodel');
        $records = $this->mapmodel->getOccurrenceRecords($guid);
        $json = json_encode($records);
        echo $json;
    }
    
    public function specimen_image_thumbnails($taxonId)
    {
        $this->load->model('taxonmodel');
        $data = $this->taxonmodel->getNode($taxonId);
        print_r($data);
    }


}


/* End of file ajax.php */
/* Location: ./controllers/ajax.php */
