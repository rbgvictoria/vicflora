<?php
/**
 * @property CI_Loader $load
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Email $email
 * @property CI_DB_active_record $db
 * @property CI_DB_forge $dbforge
 */

require_once('third_party/class.Diff.php');
require_once 'third_party/Encoding/Encoding.php';
require_once 'third_party/uuid/uuid.php';

class Admin extends CI_Controller {
    var $data;
    
    function __construct() {
        parent::__construct();

        if (!$this->input->is_cli_request()) {
            $this->load->library('session');
        }
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('versioning');
        $this->load->helper('captcha');
        $this->config->load('vicflora_config');
        $this->load->model('authenticationmodel');
    }

    function index($message="") {
        $this->login($message);
    }
    
    function login($message="") {
        if (isset($_SERVER['HTTP_REFERER']) && substr($_SERVER['HTTP_REFERER'], 0, strlen(base_url())) == base_url())
            $this->data['referer'] = $_SERVER['HTTP_REFERER'];
        else
            $this->data['referer'] = FALSE;
        $this->load->view('login', $this->data);
    }

    function authenticate(){
        // do the authenticate stuff here:
        if($this->input->post('username') && $this->input->post('passwd')){
                        if($this->authenticationmodel->checkLogin())
                if ($this->input->post('referer')){
                    redirect($this->input->post('referer'));
                }
                else 
                    redirect('flora');
            else $message = 'Authentication failed';
            $this->load->view('message', array("message" => $message));
        }
        else 
            $this->load->view('message', array('message' => "Username or password not filled in"));
    }

    function logout(){
        // unset the session variables, then destroy the session
        $unset = array('id'=>'', 'name'=>'', 'firstname'=>'', 'surname'=>'', 'email'=>'', 'role'=>'');
        $this->session->unset_userdata($unset);
        //$this->session->sess_destroy();
        if (isset($_SERVER['HTTP_REFERER']) && substr($_SERVER['HTTP_REFERER'], 0, strlen(base_url())) == base_url()) 
            redirect($_SERVER['HTTP_REFERER']);
        else 
            redirect('flora');
    }
    
    /*
     *  Admin functions for VicFlora
     *  For editing data in VicFlora
     */
    
    public function edittaxon($guid=FALSE) {
        if (!$guid)
            redirect(base_url());
        if (!isset($this->session->userdata['id'])) {
            redirect("/flora/taxon/$guid");
        }

        $this->load->model('edittaxonmodel', 'taxonmodel');
        $this->load->model('solrmodel');
        
        if ($this->input->post('submit')) {
            if ($this->input->post('do_not_index')) {
                $this->taxonmodel->doNotIndex($guid);
                $this->solrmodel->deleteDocument($guid);
            }
            else {
                $update = $this->taxonmodel->updateTaxon($guid);
                $this->load->model('mapupdatemodel');
                $this->mapupdatemodel->updateTaxon($guid);
                if ($update) {
                    $this->load->model('keybaseupdatemodel');
                    $this->keybaseupdatemodel->updateProjectItem($guid);
                    $this->load->model('mapupdatemodel');
                    $this->mapupdatemodel->updateTaxon($guid);
                    if (count($update) == 1) {
                        $this->solrmodel->updateSynonyms($guid);
                        $this->solrmodel->updateDocument($guid);
                    }
                    else {
                        foreach ($update as $item) {
                            $this->solrmodel->updateDocument($item);
                        }
                    }
                }
            }
        }
        
        $this->data['type'] = 'edit';
        $this->data['taxondata'] = $this->taxonmodel->getTaxonData($guid);
        $this->data['apni'] = $this->taxonmodel->getApniNames($guid);
        $this->data['commonnames'] = $this->taxonmodel->getCommonNames($guid);
        $this->data['attributes'] = $this->taxonmodel->getTaxonAttributes($guid);
        $this->data['changes'] = $this->taxonmodel->getChanges($guid);
        
        $this->load->view('edittaxon_view', $this->data);
        
    }
    
    private function compareProfiles($from, $to, $granularity=2) {
        require_once('third_party/FineDiff.php');
	$granularityStacks = array(
            FineDiff::$paragraphGranularity,
            FineDiff::$sentenceGranularity,
            FineDiff::$wordGranularity,
            FineDiff::$characterGranularity
        );
	$diff_opcodes = FineDiff::getDiffOpcodes($from, $to, $granularityStacks[$granularity]);
        return FineDiff::renderDiffToHTMLFromOpcodes($from, $diff_opcodes);
    }
    
    public function addchild($guid=FALSE) {
        if (!$guid)
            redirect(base_url());

        $this->load->model('edittaxonmodel', 'taxonmodel');
        
        $this->data['type'] = 'add child';
        if ($this->input->post('submit')) {
            $newguid = $this->taxonmodel->updateTaxon($guid);
            if ($newguid) {
                $this->load->model('keybaseupdatemodel');
                $this->keybaseupdatemodel->updateProjectItem($newguid[0]);
                $this->load->model('mapupdatemodel');
                $this->mapupdatemodel->updateTaxa($newguid[0]);
                $this->load->model('solrmodel');
                $this->solrmodel->updateDocument($newguid[0]);
            }
            //redirect('flora/taxon/' . $newguid[0]);
        }
        
        $this->data['taxondata'] = $this->taxonmodel->getParentData($guid);
        
        $this->load->view('edittaxon_view', $this->data);
    }
    
    public function deletetaxon($guid=FALSE) {
        if (!$guid)
            redirect(base_url());
        if (!isset($this->session->userdata['id']) || $this->session->userdata['id'] != 1 )
            redirect($_SERVER['HTTP_REFERER']);
        
        $this->load->model('edittaxonmodel', 'taxonmodel');
        $this->taxonmodel->deleteTaxon($guid);
        
        $this->load->model('solrmodel');
        $this->solrmodel->deleteDocument($guid);
        redirect(base_url());
    }
    
    public function editprofile($guid) {
        $this->load->helper('versioning');
        $this->data['js'][] = site_url() . autoVersion('js/jquery.vicflora.reference.js');
        $this->load->model('edittaxonmodel', 'taxonmodel');
        $this->load->model('solrmodel');
        
        if ($this->input->post('compare')) {
            $this->data['diff'] = html_entity_decode($this->compareProfiles($this->input->post('stored_profile'), 
                    $this->input->post('profile')));
        }
        
        if ($this->input->post('save')) {
            $this->taxonmodel->editprofile($this->input->post('profile_id'), 
                    $this->input->post('profile'),
                    $this->input->post('minor_edit'));
            $this->solrmodel->updateDocument($guid);
        }
        
        $this->data['taxondata'] = $this->taxonmodel->getTaxonData($guid);
        if ($this->data['taxondata']['TaxonomicStatus'] == 'accepted')
            $this->data['profiles'] = $this->taxonmodel->getProfiles($guid);
        else 
            $this->data['profiles'] = $this->taxonmodel->getUnmatchedProfiles($guid);
        $this->load->model('referencemodel');
        $this->data['taxonReferences'] = $this->referencemodel->getTaxonReferences($guid);
        $this->load->view('editprofile_view.php', $this->data);
    }
    
    public function newprofile($guid) {
        $this->data['js'][] = site_url() . 'js/jquery.vicflora.reference.js';
        if ($this->input->post('save')) {
            $this->load->model('solrmodel');
            $this->load->model('edittaxonmodel');
            $this->edittaxonmodel->newProfile($this->input->post('taxon_id'), $this->input->post('profile'), $this->input->post('source-id'));
            $this->solrmodel->updateDocument($guid);
            redirect('admin/editprofile/' . $guid);
        }
        
        $this->load->model('viewtaxonmodel');
        $this->data['taxondata'] = $this->viewtaxonmodel->getTaxonData($guid);
        $this->load->view('newprofile_view', $this->data);
    }
    
    public function st() {
        if (!$this->session->userdata('id')) {
            redirect('admin/login');
        }
        $this->load->model('staticpagesmodel');
        
        $uri = str_replace('admin/st/', '', $this->uri->uri_string());
        $cleanuri = str_replace('/_edit', '', $uri);
        
        if ($uri == 'admin/st')
            $this->data['pages'] = $this->staticpagesmodel->getStaticPages();
        
        $this->data['staticcontent'] = $this->staticpagesmodel->getStaticContent($cleanuri);
        if (strpos($uri, '/_edit')) {
            if (isset($this->session->userdata['id'])) {
                if ($this->input->post('submit')) {
                    $this->staticpagesmodel->updateStaticContent($this->input->post());
                    redirect('/admin/st/' . $cleanuri);
                }
                $this->load->view('editstaticpagesview', $this->data);
                return TRUE;
            }
        }

        $this->load->view('staticview', $this->data);
        
    }
    
    public function add_static_page() {
        $this->load->model('staticpagesmodel');
        
        if ($this->input->post('submit') && $this->input->post('title')
                && $this->input->post('uri')) {
            $this->staticpagesmodel->addStaticPage($this->input->post());
            redirect(site_url() . 'admin/st/' . $this->input->post('uri') . '/_edit');
        }
        
        $this->load->view('addstaticpage_view', $this->data);
    }
    
    public function solr_reindex() {
        $this->load->model('edittaxonmodel');
        $this->load->model('solrmodel');
        
        $guids = $this->edittaxonmodel->getTaxonGuids();
        if ($guids) {
            foreach ($guids as $guid) {
                set_time_limit(30);
                $this->solrmodel->updateDocument($guid);
            }
        }
    }
    
    public function solr_unindex() {
        $this->load->model('edittaxonmodel');
        $this->load->model('solrmodel');
        
        $guids = $this->edittaxonmodel->getDoNotIndexGuids();
        if ($guids) {
            foreach ($guids as $guid) {
                set_time_limit(30);
                $this->solrmodel->deleteDocument($guid);
            }
        }
    }
    
    public function solr_delete($guid) {
        $this->load->model('solrmodel');
        $this->solrmodel->deleteDocument($guid);
    }
    
    private function getSmallerDimension($img_path, $width, $height) {
        list($wid, $hei, $type, $attr)= getimagesize($img_path);
        $ratio = $wid / $hei;

        if( $height < $width/$ratio ) {
            return 'width';
        }
        else if( $width < $height * $ratio ) {
            return 'height';
        }
        else {
            return 'auto';
        }
    }
    
    public function create_thumbnails() {
        $path = getcwd() . '/images/fov/2';
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if (substr($entry, strrpos($entry, '.')+1) == 'jpg') {
                    echo "$entry<br/>";
                    $thumb = $this->resize_image("$path/$entry", 120, 120);
                    imagejpeg($thumb, "$path/thumbs/$entry");
                }
            }
            closedir($handle);
        }
    }
    
    private function resize_image($file, $w, $h, $crop=FALSE) {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }
    
    public function editdistribution($guid) {
        $this->load->model('mapmodel');
        if (!$guid)
            redirect(base_url());
        if (!isset($this->session->userdata['id'])) {
            redirect("/flora/taxon/$guid");
        }
        $this->load->model('edittaxonmodel', 'taxonmodel');
        
        if ($this->input->post('editBioregions')) {
            $subcodes = $this->input->post('sub_code_7');
            $occ_old = $this->input->post('occurrence_status_old');
            $occ = $this->input->post('occurrence_status');
            $est_old = $this->input->post('establishment_means_old');
            $est = $this->input->post('establishment_means');
            foreach ($subcodes as $index => $sub) {
                if ($occ[$index] != $occ_old[$index]) {
                    $this->mapmodel->updateDistributionStatus($this->input->post('taxon_id'), $sub, 'occurrence_status', $occ[$index]);
                }
                if ($est[$index] != $est_old[$index]) {
                    $this->mapmodel->updateDistributionStatus($this->input->post('taxon_id'), $sub, 'establishment_means', $est[$index]);
                }
            }
        }
        
        $this->data['taxondata'] = $this->taxonmodel->getTaxonData($guid);
        $legend = $this->legendBioregion('establishment_means');
        
        if ($this->data['taxondata']['RankID'] >= 220) {
            $distribution = $this->mapmodel->getDistributionDetail($guid, $this->data['taxondata']['RankID'], TRUE);
            $codes = array();
            foreach ($legend as $item) {
                $codes[] = $item['code'];
            }

            $table = array();
            foreach ($distribution as $row) {
                $item = array();
                $key = array_search($row['occurrence_status'], $codes);
                if ($key !== FALSE) {
                    $item['colour'] = $legend[$key]['colour'];
                }
                else {
                    $key = array_search($row['establishment_means'], $codes);
                    $item['colour'] = $legend[$key]['colour'];
                }
                $item['sub_code_7'] = $row['sub_code_7'];
                $item['sub_name_7'] = $row['sub_name_7'];
                $item['occurrence_status'] = $row['occurrence_status'];
                $item['establishment_means'] = $row['establishment_means'];
                $table[] = $item;
            }
            $this->data['bioregion_table'] = $table;
            $this->data['overview_by_name'] = $this->mapmodel->getAlaNameOverview($guid);
            $this->data['updates'] = $this->mapmodel->getMapUpdates($guid);
            $this->data['assertions'] = $this->mapmodel->getAssertions($guid);
        }
        $this->load->view('editdistribution_view', $this->data);
    }

    private function legendBioregion($fill) { 
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
    
    public function editglossaryterm($termID = FALSE) {
        $this->load->model('editglossarymodel');
        
        if ($this->input->post('submit') && $this->input->post('term_name')) {
            if ($this->input->post('term_name_old')) {
                if (($this->input->post('term_name') != $this->input->post('term_name_old')) || 
                        ($this->input->post('term_definition') != $this->input->post('term_definition_old'))) {
                    // update term
                    $this->editglossarymodel->updateGlossaryTerm($termID, $this->input->post('term_name'), $this->input->post('term_definition'));
                }
            }
            else {
                // insert term
                $termID = $this->editglossarymodel->insertGlossaryTerm($this->input->post('term_name'), $this->input->post('term_definition'));
            }
            
            if ($this->input->post('rel_delete')) {
                // delete relationship
                foreach ($this->input->post('rel_delete') as $rel) {
                    $this->editglossarymodel->delGlossaryTermRelationship($rel);
                }
            }
            
            if ($this->input->post('related_term')) {
                $relTypes = $this->input->post('rel_type');
                foreach ($this->input->post('related_term') as $index => $relTerm) {
                    if ($relTerm) {
                        // insert relationship
                        $this->editglossarymodel->insGlossaryTermRelationship($termID, $relTerm, $relTypes[$index]);
                    }
                }
            }
            
            $termName = $this->editglossarymodel->getTermName($termID);
            redirect('flora/glossary#' . $termName);

        }
        elseif ($this->input->post('cancel')) {
            $termName = $this->editglossarymodel->getTermName($termID);
            redirect('flora/glossary#' . $termName);
        }
        
        if ($termID) {
            $this->data['term'] = $this->editglossarymodel->getGlossaryTerm($termID);
            $this->data['relationships'] = $this->editglossarymodel->getGlossaryTermRelationships($termID);
        }
        $this->load->view('editglossary_view', $this->data);
    }
    
    public function newglossaryterm() {
        $this->load->model('editglossarymodel');
        if ($this->input->post('submit') && $this->input->post('term_name')) {
            $relTerms = $this->input->post('related_term');
            $relTypes = $this->input->post('rel_type');
            if ($this->input->post('term_definition') || $relTerms[0]) {
                // insert term
                $termID = $this->editglossarymodel->insertGlossaryTerm($this->input->post('term_name'), $this->input->post('term_definition'));
                
                // insert relationships
                foreach ($relTerms as $index => $relTerm) {
                    if ($relTerm && $relTypes[$index]) {
                        $this->editglossarymodel->insGlossaryTermRelationship($termID, $relTerm, $relTypes[$index]);
                    }
                }
                
                $termName = $this->editglossarymodel->getTermName($termID);
                redirect('flora/glossary#' . $termName);
            }
        }
        elseif ($this->input->post('cancel')) {
            redirect('flora/glossary');
        }
        
        $this->load->view('editglossary_view');
    }
    
    public function delete_glossary_term($termID) {
        $this->load->model('editglossarymodel');
        $this->editglossarymodel->delGlossaryTerm($termID);
        redirect('flora/glossary');
    }
    
    public function map_updates() {
        $this->load->model('mapmodel');
        
        $updates = $this->mapmodel->getMapUpdates();
        $this->data['updates'] = array();
        if ($updates) {
            $dates = array();
            $taxa = array();
            foreach ($updates as $item) {
                $dates[] = $item['date_updated'];
            }
            $dates_uniq = array_unique($dates);
            
            foreach ($dates_uniq as $date) {
                $day = array();
                $day['date'] = $date;
                $day['taxa'] = array();
                $updatesDay = array();
                $taxaDay = array();
                $sciNamesDay = array();
                foreach (array_keys($dates, $date) as $i) {
                    $updatesDay[] = $updates[$i];
                    $taxaDay[] = $updates[$i]['taxon_id'];
                    $sciNamesDay[] = $updates[$i]['scientific_name'];
                }
                $taxaDayUniq = array_unique($taxaDay);
                foreach ($taxaDayUniq as $index => $tax) {
                    $taxon = array();
                    $taxon['index'] = $index;
                    $taxon['taxon_id'] = $tax;
                    $taxon['scientific_name'] = $sciNamesDay[$index];
                    $taxon['occurrences'] = array();
                    foreach (array_keys($taxaDay, $tax) as $j) {
                        $taxon['occurrences'][] = array(
                            'fid' => $updatesDay[$j]['fid'],
                            'uuid' => $updatesDay[$j]['uuid'],
                            'catalog_number' => $updatesDay[$j]['catalog_number'],
                            'decimal_latitude' => $updatesDay[$j]['decimal_latitude'],
                            'decimal_longitude' => $updatesDay[$j]['decimal_longitude'],
                            'sub_name_7' => $updatesDay[$j]['sub_name_7'],
                            'occurrence_status' => $updatesDay[$j]['occurrence_status'],
                            'establishment_means' => $updatesDay[$j]['establishment_means'],
                            'is_new_record' => $updatesDay[$j]['is_new_record'],
                            'is_updated_record' => $updatesDay[$j]['is_updated_record']
                        );
                    }
                    $day['taxa'][] = $taxon;
                }
                $this->data['updates'][] = $day;
            }
        }
        
        $this->load->view('map_updates_view', $this->data);
    }
    
    public function ala_matched_names() {
        $this->load->model('mapmodel');
        $this->data['matched_names'] = $this->mapmodel->getAlaMatchedNames();
        $this->load->view('ala_matched_names_view', $this->data);
    }
    
    public function keybase_update($update=FALSE) {
        $this->load->model('keybasemodel');
        $missing = $this->keybasemodel->getMissingTaxa();
        if ($update) {
            foreach ($missing as $taxon) {
                $data = array_merge(
                    array(
                        'ProjectsID' => 10,
                        'ItemsID' => $this->keybasemodel->getKeyBaseItemsID($taxon['FullName']),
                        'Url' => 'https://vicflora.rbg.vic.gov.au/flora/taxon/' . $taxon['GUID']
                    ),
                    $this->keybasemodel->getVicFloraData($taxon['GUID'])
                );
                $this->keybasemodel->insertKeyBaseProjectItem($data);
            }
            $missing = $this->keybasemodel->getMissingTaxa();
        }
        $this->data['missing_taxa'] = $missing;
        $this->data['not_in_keys'] = $this->keybasemodel->getNotKeyedOutItems();
        if (!$this->input->is_cli_request()) {
            $this->load->view('keybase_update_view', $this->data);
        }
    }
    
    public function update_taxon_tree() {
        if (!$this->input->is_cli_request() ) {
            show_error('You don\'t have access to this page.', 403);
        }
        $this->load->library('VicFloraTaxonTree');
        $this->vicflorataxontree->updateTaxonTree();
    }
    
    public function update_solr_index($id=false) {
        if (!$this->input->is_cli_request() ) {
            show_error('You don\'t have access to this page.', 403);
        }
        $this->load->model('solrmodel');
        if ($id) {
            $this->solrmodel->updateDocument($id);
        }
        else {
            $this->solrmodel->updateAll();
        }
        $this->solr_unindex();
    }
    
    public function update_cumulus_images($date=FALSE) {
        if (!$this->input->is_cli_request() ) {
            show_error('You don\'t have access to this page.', 403);
        }
        $dir = getcwd() . '/roboflow';
        $library = array();
        $vcsb = array();
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (strpos($file, 'library') !== false) {
                        $library[] = array(
                            'filename' => $file,
                            'filemtime' => filemtime($dir . '/' . $file)
                        );
                    }
                    elseif (strpos($file, 'vcsb') !== false) {
                        $vcsb[] = array(
                            'filename' => $file,
                            'filemtime' => filemtime($dir . '/' . $file)
                        );
                    }
                }
                closedir($dh);
            }
        }
        
        $startTime = date('Y-m-d H:i:s');
        $this->load->library('RoboFlow');
        $library_dates = array();
        foreach ($library as $file) {
            $library_dates[] = $file['filemtime'];
        }
        array_multisort($library_dates, SORT_DESC, $library);
        echo "Processing: " . $library[0]['filename'] . "\r\n";
        $this->roboflow->update($dir . '/' . $library[0]['filename']);
        
        $vcsb_dates = array();
        foreach ($vcsb as $file) {
            $vcsb_dates[] = $file['filemtime'];
        }
        array_multisort($vcsb_dates, SORT_DESC, $vcsb);
        echo "Processing: " . $vcsb[0]['filename'] . "\r\n";
        $this->roboflow->update($dir . '/' . $vcsb[0]['filename']);
        
        $this->roboflow->deleteOldRecords($startTime);
    }
    
    public function update_maps($from=FALSE, $pageSize=1000, $start=0) {
        if (!$this->input->is_cli_request() ) {
            show_error('You don\'t have access to this page.', 403);
        }
        set_time_limit(0);
        if (!$from) {
            $date = new DateTime();
            $date->sub(new DateInterval('P14D'));
            $from = $date->format('Y-m-d');
            echo $from . "\n";
        }
        $this->load->library('VicFloraMap');
        $startTime = date('Y-m-d H:i:s');
        $numLoaded = $this->vicfloramap->updateOccurrences($from, $pageSize, $start);
        $endTime = date('Y-m-d H:i:s');
    }
    
    public function update_keybase($from=FALSE) {
        if (!$this->input->is_cli_request() ) {
            show_error('You don\'t have access to this page.', 403);
        }
        if ($from) {
            try {
                $dt = new DateTime($from);
                $from = date('Y-m-d H:i:s', $dt->getTimestamp());
            } catch (Exception $exc) {
                exit($exc->getTraceAsString());
            }
        }
        $this->load->model('keybaseupdatemodel');
        $this->keybaseupdatemodel->updateKeyBaseProject($from);
    }
    
    public function update_map_taxa($from=FALSE) {
        if ($from) {
            $from = $this->checkDate($from);
        }
        $this->load->model('mapupdatemodel');
        $this->mapupdatemodel->updateTaxa($from);
    }
    
    public function refresh_outliers() {
        $this->load->library('VicFloraMap');
        $this->vicfloramap->outliers();
    }
    
    private function checkDate($date) {
        try {
            $dt = new DateTime($date);
            return date('Y-m-d H:i:s', $dt->getTimestamp());
        } catch (Exception $exc) {
            exit($exc->getTraceAsString());
        }
    }
    
}

/* End of file admin.php */
/* Location: ./controllers/admin.php */
