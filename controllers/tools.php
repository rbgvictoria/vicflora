<?php
class Tools extends CI_Controller {

    public function get_map_data($from, $pageSize=1000, $start=0) {
        if ($this->input->is_cli_request()) {
            set_time_limit(0);
        }
        $this->load->library('VicFloraMap');
        $startTime = date('Y-m-d H:i:s');
        //$startTime = '2016-01-21 00:00:00';
        $numLoaded = $this->vicfloramap->updateOccurrences($from, $pageSize, $start);
        $endTime = date('Y-m-d H:i:s');
        $this->vicfloramap->updateDistribution($startTime, $endTime);
    }
    
    public function get_vba_data($from, $pageSize=1000, $start=0) {
        $this->load->helper('map');
        $numLoaded = getVbaRecords($from, $pageSize, $start);
        echo $numLoaded . PHP_EOL;
    }
    
    public function state_distribution() {
        $this->load->library('VicFloraMap');
        $this->vicfloramap->stateDistribution();
    }
    
}
?>
