<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Flora extends CI_Controller {
    var $data;
    
    public function __construct() {
        parent::__construct();
        
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        
        $this->config->load('vicflora_config');
        $this->output->enable_profiler(true);
    }
    
    public function index() {
        $this->session->unset_userdata('last_search');
        $this->load->model('staticpagesmodel');
        $this->data['staticcontent'] = $this->staticpagesmodel->getStaticContent('home');
        $this->load->view('home_view', $this->data);
    }
    
    public function about() {
        $this->session->unset_userdata('last_search');
        $this->load->model('staticpagesmodel');
        $this->data['staticcontent'] = $this->staticpagesmodel->getStaticContent('about');
        $this->load->view('staticview', $this->data);
    }
    
    public function help() {
        $this->session->unset_userdata('last_search');
        $this->load->model('staticpagesmodel');
        $this->data['staticcontent'] = $this->staticpagesmodel->getStaticContent('help');
        $this->load->view('staticview', $this->data);
    }
    
    public function search() {
        $this->load->model('solrmodel');
        $this->data['solrresult'] = $this->solrmodel->solrSearch();
        $this->load->view('solr_search_view', $this->data);
    }
    
    public function download() {
        $this->load->model('solrmodel');
        
        $qstring = urldecode($this->input->server('QUERY_STRING'));
        $qarray = explode('&', $qstring);
        
        $filename = FALSE;
        $fieldlist = FALSE;
        $fieldtype = FALSE;
        foreach ($qarray as $item) {
            $bits = explode('=', $item);
            if ($bits[0] == 'fl')
                $fieldlist = $bits[1];
            elseif ($bits[0] == 'filename')
                $filename = $bits[1];
            elseif ($bits[0] == 'filetype')
                $filetype = $bits[1];
        }
        
        if ($fieldlist && $filename) {
            $data = $this->solrmodel->solrSearch(TRUE);
            
            switch ($filetype) {
                case 'csv':
                    $delimiter = ',';
                    $contenttype = 'text/csv';
                    $extension = 'csv';
                    break;

                case 'txt':
                    $delimiter = "\t";
                    $contenttype = 'text/plain';
                    $extension = 'txt';
                    break;

                default:
                    break;
            }
            
            $csv = array();
            $firstrow = array();
            $fieldlist = explode(',', $fieldlist);
            foreach ($fieldlist as $item) {
                if (in_array($item, array('epbc', 'vrot', 'ffg')))
                    $firstrow[] = strtoupper($item);
                else {
                    $itemarr = explode('_', $item);
                    foreach($itemarr as $index => $value) {
                        if ($index)
                        $itemarr[$index] = ucfirst($value);
                    }
                    $firstrow[] = implode('', $itemarr);
                }
                    
            }
            
            foreach ($firstrow as $index=>$value) {
                $firstrow[$index] = '"' . $value . '"';
            }
            $csv[] = implode($delimiter, $firstrow);

            foreach ($data->docs as $row) {
                $row = (array) $row;
                foreach ($row as $key => $value) {
                    if ($value) {
                        if (is_array($value)) {
                            $row[$key] = '"' . implode('|', $value) . '"';
                        }
                        else {
                            $row[$key] = '"' . $value . '"';
                        }
                    }
                }
                $csv[] = implode($delimiter, $row);
            }

            $csv = implode("\r\n", $csv);

            header("Content-Disposition: attachment; filename=\"$filename.$extension\"");
            header("Content-type: $contenttype");
            echo $csv;
        }
        else {
            $this->data['qstring'] = $qstring;
            $this->load->view('download_view', $this->data);
        }
    }
    
    public function classification($guid=FALSE) {
        if (!$guid) 
            $guid = '947325be-f912-44dc-8a2d-c5f6e345b1bd';
        
        $this->session->unset_userdata('last_search');
        
        $this->load->model('classificationmodel');
        $this->data['taxon'] = $this->classificationmodel->getCurrentTaxon($guid);
        $this->data['ancestors'] = $this->classificationmodel->getAncestors($guid);
        $this->data['children'] = $this->classificationmodel->getChildren($guid);
        
        $this->load->view('classification_view', $this->data);
    }
    
    public function taxon($guid=FALSE) {
        if (!$guid)
            redirect(base_url());
        
        if (isset($_SERVER['HTTP_REFERER']) && preg_match('/\/search\??/', $_SERVER['HTTP_REFERER'])) {
            $this->session->unset_userdata('last_search');
            $this->session->set_userdata('last_search', $_SERVER['HTTP_REFERER']); 
        }
        
        $this->load->model('viewtaxonmodel','taxonmodel');
        $this->data['namedata'] = $this->taxonmodel->getTaxonData($guid);
        
        /*if (isset($this->session->userdata['last_search']) && 
                (isset($_SERVER['HTTP_REFERER']) && (preg_match('/\/search\??/', $_SERVER['HTTP_REFERER']) || 
                preg_match("/$guid/", $_SERVER['HTTP_REFERER']) || preg_match('/\/taxon\??/', $_SERVER['HTTP_REFERER'])))) {
            $this->load->model('solrmodel');
            $this->data['browse'] = $this->solrmodel->browse($this->data['namedata']['FullName'], $guid);
        }*/
        
        if ($this->data['namedata']['TaxonomicStatus'] == 'accepted' &&
                $this->data['namedata']['RankID'] > 0) {
            $this->data['breadcrumbs'] = $this->taxonmodel->getClassificationBreadCrumbs($guid);
            $this->data['siblings'] = $this->taxonmodel->getSiblingsDropdown($guid);
            $this->data['children'] = $this->taxonmodel->getChildrenDropdown($guid);
        }
        
        $this->data['commonname'] = $this->taxonmodel->getCommonNames($guid);
        $this->data['apni'] = $this->taxonmodel->getApniNames($guid);
        $this->data['attributes'] = $this->taxonmodel->getTaxonAttributes($guid);
        $this->load->model('classificationmodel');
        $this->data['classification'] = $this->classificationmodel->getAncestors($guid);
        $this->data['subordinates'] = $this->classificationmodel->getChildren($guid);
        $this->data['profile'] = $this->taxonmodel->getProfiles($guid);
        $this->data['key'] = $this->taxonmodel->getKey($guid);
        //$this->data['images'] = $this->taxonmodel->getTaxonImages($guid);
        $this->data['images'] = $this->taxonmodel->getThumbnails($guid);
        $this->data['heroImage'] = $this->taxonmodel->getHeroImage($guid);
        $this->data['acceptedname'] = $this->taxonmodel->getAcceptedNameByGUID($guid);
        $this->data['synonyms'] = $this->taxonmodel->getSynonyms($guid);
        $this->data['misapplications'] = $this->taxonmodel->getMisapplications($guid);
        $this->data['links'] = $this->taxonmodel->getLinks($guid);
        
        $this->data['distribution'] = FALSE;
        $this->data['stateDistribution'] = FALSE;
        $this->data['stateDistributionMap'] = FALSE;
        
        $this->load->model('referencemodel');
        $this->data['taxonReferences'] = $this->referencemodel->getTaxonReferences($guid);

        if ($this->data['namedata']['RankID'] >= 220) {
            $this->load->model('mapmodel');
            $this->data['distribution'] = $this->mapmodel->getDistributionDetail($guid);
            $this->data['stateDistribution'] = $this->mapmodel->getStateDistribution($guid);
            $this->data['stateDistributionMap'] = $this->state_dist_map($guid, 'png');
            /*$this->data['boundingPolygonMap100'] = $this->bounding_polygon_map100($guid, $this->data['namedata']['RankID'], 'png');
            $this->data['boundingPolygonMap99'] = $this->bounding_polygon_map99($guid, $this->data['namedata']['RankID'], 'png');
            $this->data['boundingPolygonMap99C'] = $this->bounding_polygon_map99C($guid, $this->data['namedata']['RankID'], 'png');
            $this->data['boundingPolygonMap80'] = $this->bounding_polygon_map80($guid, $this->data['namedata']['RankID'], 'png');*/
            $this->data['profileMap'] = $this->profile_map($guid, $this->data['namedata']['RankID'], 'png');
            $this->data['svg_map'] = $this->map($guid, $this->data['namedata']['RankID']);
            //$this->data['imageMap'] = $this->imageMap(480);
            //$this->data['ibra_map'] = $this->ibra_map($guid, $this->data['namedata']['RankID']);
            $this->data['bioregion_legend'] = $this->legendBioregion();
            //$this->data['vicgrid_map'] = $this->vicgrid_map($guid, $this->data['namedata']['RankID']);
            //$this->data['maplink'] = $this->maplink($guid, $this->data['namedata']['RankID']);
        }
        
        $this->load->view('taxon_view', $this->data);
    }
    
    public function key($keyID=FALSE) {
        if (!$keyID) {
            exit();
        }
        
        $this->data['keyID'] = $keyID;
        $this->load->view('key_view', $this->data);
    }
    
    public function image($guid) {
        if (!$guid)
            redirect(site_url());
        $this->output->enable_profiler(false);
        $this->load->model('viewtaxonmodel','taxonmodel');
        //$this->data['image'] = $this->taxonmodel->getImageMetadata($guid);
        $this->data['image'] = $this->taxonmodel->getImage($guid);
        $this->load->view('image_only_view', $this->data);
    }
    
    public function glossary_image($guid) {
        if (!$guid)
            redirect(site_url());
        $this->output->enable_profiler(false);
        $this->load->model('glossarymodel');
        $this->data['image'] = $this->glossarymodel->getGlossaryImage($guid);
        $this->load->view('image_only_view', $this->data);
    }
    
    public function svg_map ($guid, $map, $format, $width=400, $fill=FALSE) {
        $this->output->enable_profiler(FALSE);
        $this->load->model('taxonmodel');
        $taxondata = $this->taxonmodel->getTaxonDataForMap($guid);
        
        $ret = FALSE;
        switch ($map) {
            case 'avh_distribution':
                if ($format == 'svg') {
                    echo $this->map($guid, $taxondata['RankID'], $format, $width);
                }
                else {
                    $data = array(
                        'alt' => 'Distribution of ' . $taxondata['FullName'] . ' in Victoria',
                        'src' => $this->map($guid, $taxondata['RankID'], $format, $width)
                    );
                    echo json_encode((object) $data);
                }
                break;

            case 'grid':
                if ($format == 'svg') {
                    echo $this->vicgrid_map($guid, $format);
                }
                else {
                    $data = array(
                        'alt' => $taxondata['FullName'] . ' 10&apos; grid',
                        'src' => $this->vicgrid_map($guid, $format)
                    );
                    echo json_encode((object) $data);
                }
                break;

            case 'bioregions':
                if ($format == 'svg') {
                    echo $this->ibra_map($guid, $format, $fill);
                }
                else {
                    $data = array(
                        'alt' => $taxondata['FullName'] . ' bioregions',
                        'src' => $this->ibra_map($guid, $format, $fill)
                    );
                    echo json_encode((object) $data);
                }
                break;

            default:
                break;
        }
        return $ret;
    }
    
    public function imageMap ($width=400, $return=false) {
        $this->output->enable_profiler(FALSE);
        $url = "http://10.15.15.107:65002/geoserver/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:vicflora_bioregion';
        $query['styles'] = 'vicflora_bioregions_imagemap';
        $query['bbox'] = '140.8,-39.3,150.2,-33.8';
        $query['width'] = $width;
        $query['height'] = ceil($width * (242/400));
        $query['srs'] = 'EPSG:4326';
        $query['format'] = 'text/html';
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        if ($return) {
            return $this->doCurl($url . '?' . $qstring);
        }
        else {
            echo $this->doCurl($url . '?' . $qstring);
        }
    }
    
    public function map($guid, $rankid, $format=FALSE, $width=400) {
        $url = $this->config->item('geoserver_url') . "/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:cst_vic,vicflora:ibra_taxon_view,vicflora:vicflora_bioregion,vicflora:cst_vic,vicflora:vicflora_occurrence';
        $query['styles'] = ',polygon_establishment_means,polygon_no-fill_grey-outline,polygon_no-fill_black-outline,';
        $query['bbox'] = '140.8,-39.3,150.2,-33.8';
        $query['width'] = $width;
        $query['height'] = ceil($width * (242/400));
        $query['srs'] = 'EPSG:4326';
        if ($format == 'svg') {
            $query['format'] = 'image/svg';
        }
        else {
            $query['format'] = 'image/png';
        }
        $term = ($rankid == 220) ? 'species_guid' : 'taxon_id';
        $query['cql_filter'] = urlencode("FEAT_CODE IN ('mainland','island');taxon_id='$guid';INCLUDE;FEAT_CODE IN ('mainland','island');$term='$guid'");
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        
        if ($format == 'svg') {
            $svg = $this->doCurl($url, $qstring);
            return $svg;
        }
        else {
            return $url . '?' . $qstring;
        }
    }
        
    public function profile_map($guid, $rankid, $format=FALSE) {
        $url = $this->config->item('geoserver_url') . "/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:cst_vic,vicflora:vicflora_occurrence';
        $query['styles'] = 'polygon_no-fill_black-outline,';
        $query['bbox'] = '140.8,-39.3,150.2,-33.8';
        $query['width'] = 512;
        $query['height'] = 310;
        $query['srs'] = 'EPSG:4326';
        $query['format'] = 'image/png';
        $term = ($rankid == 220) ? 'species_guid' : 'taxon_id';
        $query['cql_filter'] = urlencode("FEAT_CODE IN ('mainland','island');$term='$guid' AND establishment_means NOT IN ('cultivated') AND occurrence_status NOT IN ('doubtful','absent')");
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        
        if ($format == 'svg') {
            $svg = $this->doCurl($url, $qstring);
            return $svg;
        }
        else {
            return $url . '?' . $qstring;
        }
    }
        
    public function ibra_map($guid, $format='img', $fill=FALSE) {
        $url = $this->config->item('geoserver_url') . "/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:cst_vic,vicflora:ibra_taxon_view,vicflora:vicflora_bioregion,vicflora:cst_vic';
        if ($fill == 'establishment_means') {
            $query['styles'] = 'polygon_no-fill_grey-outline,polygon_establishment_means,polygon_no-fill_grey-outline,polygon_no-fill_black-outline';
        }
        else {
            $query['styles'] = ',vic_bioregions,polygon_no-fill_grey-outline,polygon_no-fill_black-outline';
        }
        $query['bbox'] = '140.8,-39.3,150.2,-33.8';
        $query['width'] = 400;
        $query['height'] = 242;
        $query['srs'] = 'EPSG:4326';
        $query['format'] = 'image/svg';
        $query['cql_filter'] = urlencode("FEAT_CODE IN ('mainland','island');taxon_id='$guid';INCLUDE;INCLUDE");
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        
        
        if ($format == 'svg') {
            $svg = $this->doCurl($url, $qstring);
            return $svg;
        }
        else {
            return $url . '?' . $qstring;
        }
    }
        
    public function vicgrid_map($guid, $format) {
        $url = $this->config->item('geoserver_url') . "/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:cst_vic,vicflora:vicgrid_taxon_view,vicflora:vicgrid';
        $query['styles'] = '';
        $query['bbox'] = '140.8,-39.3,150.2,-33.8';
        $query['width'] = 400;
        $query['height'] = 242;
        $query['srs'] = 'EPSG:4326';
        $query['format'] = 'image/svg';
        $query['cql_filter'] = urlencode("FEAT_CODE IN ('mainland','island');taxon_id='$guid';INCLUDE");
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        
        if ($format == 'svg') {
            $svg = $this->doCurl($url, $qstring);
            return $svg;
        }
        else {
            return $url . '?' . $qstring;
        }
    }
        
    public function state_dist_map($guid, $format) {
        $url = $this->config->item('geoserver_url') . "/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:australia_states,vicflora:state_taxon_view';
        $query['styles'] = '';
        $query['bbox'] = '112.9215,-43.8604,153.63834,-9.14129';
        $query['width'] = 242;
        $query['height'] = 242;
        $query['srs'] = 'EPSG:4326';
        $query['format'] = 'image/svg';
        $query['cql_filter'] = urlencode("INCLUDE;taxon_id='$guid'");
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        
        if ($format == 'svg') {
            $svg = $this->doCurl($url, $qstring);
            return $svg;
        }
        else {
            return $url . '?' . $qstring;
        }
    }
        
    public function bounding_polygon_map99($guid, $rankid, $format) {
        $url = $this->config->item('geoserver_url') . "/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:cst_vic,vicflora:concave_hull_99,vicflora:vicflora_occurrence';
        $query['styles'] = ',red_polygon,small-black-dot';
        $query['bbox'] = '140.8,-39.3,150.2,-33.8';
        $query['width'] = 400;
        $query['height'] = 242;
        $query['srs'] = 'EPSG:4326';
        $query['format'] = 'image/svg';
        //$term = ($rankid == 220) ? 'species_guid' : 'taxon_id';
        $query['cql_filter'] = urlencode("FEAT_CODE IN ('mainland','island');taxon_id='$guid';taxon_id='$guid'");
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        
        if ($format == 'svg') {
            $svg = $this->doCurl($url, $qstring);
            return $svg;
        }
        else {
            return $url . '?' . $qstring;
        }
    }
        
    public function bounding_polygon_map99C($guid, $rankid, $format) {
        $url = $this->config->item('geoserver_url') . "/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:cst_vic,vicflora:concave_hull_99_curved,vicflora:vicflora_occurrence';
        $query['styles'] = ',red_polygon,small-black-dot';
        $query['bbox'] = '140.8,-39.3,150.2,-33.8';
        $query['width'] = 400;
        $query['height'] = 242;
        $query['srs'] = 'EPSG:4326';
        $query['format'] = 'image/svg';
        //$term = ($rankid == 220) ? 'species_guid' : 'taxon_id';
        $query['cql_filter'] = urlencode("FEAT_CODE IN ('mainland','island');taxon_id='$guid';taxon_id='$guid'");
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        
        if ($format == 'svg') {
            $svg = $this->doCurl($url, $qstring);
            return $svg;
        }
        else {
            return $url . '?' . $qstring;
        }
    }
        
    public function bounding_polygon_map80($guid, $rankid, $format) {
        $url = $this->config->item('geoserver_url') . "/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:cst_vic,vicflora:concave_hull_80,vicflora:vicflora_occurrence';
        $query['styles'] = ',red_polygon,small-black-dot';
        $query['bbox'] = '140.8,-39.3,150.2,-33.8';
        $query['width'] = 400;
        $query['height'] = 242;
        $query['srs'] = 'EPSG:4326';
        $query['format'] = 'image/svg';
        //$term = ($rankid == 220) ? 'species_guid' : 'taxon_id';
        $query['cql_filter'] = urlencode("FEAT_CODE IN ('mainland','island');taxon_id='$guid';taxon_id='$guid'");
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        
        if ($format == 'svg') {
            $svg = $this->doCurl($url, $qstring);
            return $svg;
        }
        else {
            return $url . '?' . $qstring;
        }
    }
        
    public function bounding_polygon_map100($guid, $rankid, $format) {
        $url = $this->config->item('geoserver_url') . "/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:cst_vic,vicflora:convex_hull,vicflora:vicflora_occurrence';
        $query['styles'] = ',red_polygon,small-black-dot';
        $query['bbox'] = '140.8,-39.3,150.2,-33.8';
        $query['width'] = 400;
        $query['height'] = 242;
        $query['srs'] = 'EPSG:4326';
        $query['format'] = 'image/svg';
        //$term = ($rankid == 220) ? 'species_guid' : 'taxon_id';
        $query['cql_filter'] = urlencode("FEAT_CODE IN ('mainland','island');taxon_id='$guid';taxon_id='$guid'");
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        
        if ($format == 'svg') {
            $svg = $this->doCurl($url, $qstring);
            return $svg;
        }
        else {
            return $url . '?' . $qstring;
        }
    }
        
    public function capad_park_map($resno, $symbol) {
        $url = $this->config->item('geoserver_url') . "/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:vic_boundaries,vicflora:vicflora_capad,vicflora:vic_boundaries';
        if ($symbol == 'point')
            $query['styles'] = 'polygon,green_point,';
        else
            $query['styles'] = 'polygon,,';
        $query['bbox'] = '140.8,-39.3,150.2,-33.8';
        $query['width'] = 400;
        $query['height'] = 242;
        $query['srs'] = 'EPSG:4326';
        $query['format'] = 'image/svg';
        $query['cql_filter'] = urlencode("INCLUDE;res_number='$resno';INCLUDE");
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        
        //$svg = $this->doCurl($url, $qstring);
        //return $svg;
        return $url . '?' . $qstring;
    }
        
    public function maplink($guid, $rankid) {
        $url = "http://data.rbg.vic.gov.au/geoserver/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:cst_vic,vicflora:vicflora_occurrence';
        $query['styles'] = '';
        $query['bbox'] = '140.8,-39.3,150.2,-33.8';
        $query['width'] = 600;
        $query['height'] = 400;
        $query['srs'] = 'EPSG:4326';
        $query['format'] = 'application/openlayers';
        $term = ($rankid == 220) ? 'species_guid' : 'taxon_id';
        $query['cql_filter'] = urlencode("FEAT_CODE IN ('mainland','island');$term='$guid'");
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        
        //$svg = $this->doCurl($url, $qstring);
        //return $svg;
        return $url . '?' . $qstring;
    }
    
    public function bioregions() {
        $this->load->model('staticpagesmodel');
        $uri = substr($this->uri->uri_string(), strlen('flora/bioregions/'));
        if ($uri) {
            //$this->session->unset_userdata('last_search');
            $this->data['staticcontent'] = $this->staticpagesmodel->getStaticContent('bioregions/' . $uri);
            $this->load->view('staticview', $this->data);
        }
        else {
            $this->data['map'] = $this->bioregion_map();
            $this->load->model('mapmodel');
            $this->data['bioregions'] = $this->mapmodel->getBioregions();
            $this->data['legend'] = $this->legendBioregion('bioregion');
            $this->data['staticcontent'] = $this->staticpagesmodel->getStaticContent('bioregions');
            $this->load->view('bioregions_view', $this->data);
        }
    }
    
    public function bioregion_map() {
        $url = $this->config->item('geoserver_url') . "/vicflora/wms";
        $query = array();
        $query['service'] = 'WMS';
        $query['version'] = '1.1.0';
        $query['request'] = 'GetMap';
        $query['layers'] = 'vicflora:vicflora_bioregion,vicflora:cst_vic';
        $query['styles'] = ',polygon_no-fill_black-outline';
        $query['bbox'] = '140.8,-39.3,150.2,-33.8';
        $query['width'] = 600;
        $query['height'] = 361;
        $query['srs'] = 'EPSG:4326';
        $query['format'] = 'image/svg';
        $query['cql_filter'] = urlencode("INCLUDE;FEAT_CODE IN ('mainland','island')");
        
        $qstring = array();
        foreach ($query as $key => $value) {
            $qstring[] = "$key=$value";
        }
        $qstring = implode('&', $qstring);
        
        //$svg = $this->doCurl($url, $qstring);
        //return $svg;
        return $url . '?' . $qstring;
    }
        

    private function legendBioregion($fill=FALSE) {
        $geoserverurl = 'http://10.15.15.107:65002/geoserver';
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
        
    private function doCurl($url, $query=FALSE, $proxy=FALSE) {   
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
            curl_setopt($ch, CURLOPT_PROXY, "http://proxy.rbg.vic.gov.au:8080"); 
            curl_setopt($ch, CURLOPT_PROXYPORT, 8080); 
            curl_setopt ($ch, CURLOPT_PROXYUSERPWD, "helpdesk:glass3d");
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function glossary() {
        $alpha = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
            'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
            'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
            'y', 'z', '-');
        $this->data['alpha_dropdown'] = array();
        foreach ($alpha as $letter)
            $this->data['alph_dropdown'][$letter] = strtoupper($letter);
        
        $this->load->view('glossary_view', $this->data);
    }
    
    public function checklist($park = FALSE) {
        $this->load->model('checklistmodel');
        
        $this->data['parkno'] = FALSE;
        if (($this->input->post('submit') && $this->input->post('park')) || $park) {
            if ($park) {
                $parkno = $park;
            }
            else {
                $parkno = $this->input->post('park');
            }
            $this->data['parkno'] = $parkno;
            $info = $this->checklistmodel->getParkInfo($parkno);
            $this->data['park_name'] = $info['name'];
            $symbol = ($info['area'] < 0.03) ? 'point' : 'polygon';
            $this->data['park_map'] = $this->capad_park_map($parkno, $symbol);
            $this->data['checklist'] = $this->checklistmodel->getCheckListTaxa($parkno);
        }
        
        $this->data['parks'] = $this->checklistmodel->getParks();
        $this->load->view('checklist_view', $this->data);
    }
}


/* End of file flora.php */
/* Location: ./controllers/flora.php */
