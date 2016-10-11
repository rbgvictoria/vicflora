<?php

class Reference extends CI_Controller {
    var $data;
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('versioning');
        $this->load->helper('curl');
        $this->output->enable_profiler(false);
        $this->config->load('vicflora_config');
        $this->load->model('referencemodel');
        
        $this->data['js'][] = base_url() . 'js/jquery.vicflora.reference.js';
    }
    
    public function index() {
        $this->load->view('reference/index', $this->data);
    }
    
    public function show($id=false) {
        if (!$id) {
            redirect('reference');
        }
        $this->data['referenceData'] = $this->referencemodel->getReferenceData($id);
        $this->data['fullReference'] = $this->referencemodel->getFullReference($id);
        $this->data['referenceData']->in = FALSE;
        if ($this->data['referenceData']->InPublicationID) {
            $this->data['referenceData']->in = $this->referencemodel->getFullReference($this->data['referenceData']->InPublicationID);
        }
        $this->data['inPublications'] = $this->referencemodel->getInPublications($id);
        $this->load->view('reference/show', $this->data);
    }
    
    public function edit($id=false) {
        if (!$id) {
            redirect('reference');
        }
        if (!$this->session->userdata('id')) {
            redirect('reference/show/' . $id);
        }
        
        if ($this->input->post('save')) {
            $data = $this->input->post();
            unset($data['save']);
            $this->referencemodel->updateReference($data, $id);
        }
        
        
        $this->data['referenceData'] = $this->referencemodel->getReferenceData($id);
        $this->data['fullReference'] = $this->referencemodel->getFullReference($id);
        $this->data['referenceData']->in = FALSE;
        if ($this->data['referenceData']->InPublicationID) {
            $this->data['referenceData']->in = $this->referencemodel->getFullReference($this->data['referenceData']->InPublicationID);
        }
        $this->data['inPublications'] = $this->referencemodel->getInPublications($id);
        $this->load->view('reference/edit', $this->data);
    }
    
    public function create() {
        if (!$this->session->userdata('id')) {
            redirect('reference');
        }
        
        if ($this->input->post('save')) {
            $data = $this->input->post();
            unset($data['save']);
            $id = $this->referencemodel->updateReference($data);
            
            if (strpos($this->input->post('http_referer'), 'vicflora/admin/editprofile') !== FALSE) {
                $guid = substr($this->input->post('http_referer'), strrpos($this->input->post('http_referer'), '/')+1);
                $this->referencemodel->createTaxonReference($guid, $id);
                redirect($this->input->post('http_referer'));
            }
            else {
                redirect('reference/show/' . $id);
            }
        }
        
        $this->load->view('reference/create', $this->data);
    }
    
    public function reference_lookup_autocomplete() {
        if (empty($_GET['term'])) exit;
        $q = strtolower($_GET['term']);
        $data = $this->referencemodel->referenceLookupAutocomplete($q);
        $this->jsonHeader();
        echo json_encode($data);
    }
    
    public function get_reference($id) {
        $data = $this->referencemodel->getFullReference($id);
        $this->jsonHeader();
        echo json_encode($data);
    }
    
    public function create_taxon_reference_ajax() {
        if (!$this->input->get_post('reference_id') || !$this->input->get_post('taxon_id')) {
            return FALSE;
        }
        $data = $this->referencemodel->createTaxonReference($this->input->get_post('taxon_id'), $this->input->get_post('reference_id'));
        $this->jsonHeader();
        echo json_encode($data);
    }
    
    public function delete_taxon_reference_ajax() {
        if (!$this->input->get_post('reference_id') || !$this->input->get_post('taxon_id')) {
            return FALSE;
        }
        $success = $this->referencemodel->deleteTaxonReference($this->input->get_post('taxon_id'), $this->input->get_post('reference_id'));
        $this->jsonHeader();
        echo json_encode($success);
    }
    
    private function jsonHeader() {
        $this->output->enable_profiler(false);
        header('Content-type: application/json');
    }
    

}


/* End of file reference.php */
/* Location: ./controllers/reference.php */
