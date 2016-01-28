<?php

class Map extends CI_Controller {
    var $data;
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('curl');
        $this->load->helper('map');
        $this->load->helper('ala_ws');
        $this->output->enable_profiler(FALSE);
        $this->config->load('vicflora_config');
        $this->load->model('mapmodel');
    }
    
    public function index() {
        $this->load->view('map/index', $this->data);
    }
    
    public function update($from=false) {
        $this->data['lastUpdated'] = $this->mapmodel->getLastUpdatedDate();
        
        if ($from) {
            $this->data['result'] = getRecords($from);
        }
        
        $this->load->view('map/update', $this->data);
    }
    
    public function message($to = 'World') {
            echo "Hello {$to}!".PHP_EOL;
    }

}


/* End of file map.php */
/* Location: ./controllers/map.php */
