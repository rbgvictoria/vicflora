<?php

class AutoComplete extends CI_Controller {

    var $data;

    function  __construct() {
        parent::__construct();
        $this->load->database();
        //$this->load->library('session');
        $this->load->helper('url');
        $this->output->enable_profiler(FALSE);
        $this->load->model('autocompletemodel');
    }
    
    public function autocompleteName() {
        if (empty($_GET['term'])) exit;
        $q = strtolower($_GET['term']);
        $items = $this->autocompletemodel->findNames($q);
        echo json_encode($items);
    }

    public function autocompleteAcceptedName() {
        if (empty($_GET['term'])) exit;
        $q = strtolower($_GET['term']);
        $items = $this->autocompletemodel->findAcceptedNames($q);
        echo json_encode($items);
    }
    
    public function autocomplete_parent($taxontreedefitemid) {
        if (empty($_GET['term'])) exit;
        $q = strtolower($_GET['term']);
        $items = $this->autocompletemodel->findParent($q, $taxontreedefitemid);
        echo json_encode($items);
    }
    
    public function autocomplete_glossary_term() {
        if (empty($_GET['term'])) exit;
        $q = strtolower($_GET['term']);
        $items = $this->autocompletemodel->findGlossaryTerm($q);
        echo json_encode($items);
    }
}

?>
