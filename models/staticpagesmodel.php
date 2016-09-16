<?php
class StaticPagesModel extends CI_Model {
	function __construct() {
		parent::__construct();
	}
	
    public function getStaticPages() {
        $this->db->select('Uri, PageTitle');
        $this->db->from('vicflora_static');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getStaticContent($uri) {
        $this->db->select('StaticID, PageTitle, Uri, PageContent');
        $this->db->from('vicflora_static');
        $this->db->where('Uri', $uri);
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->row_array();
    }
    
    public function updateStaticContent($data) {
        $update = array(
            'PageTitle' => $data['title'],
            'PageContent' => $data['pagecontent'],
            'TimestampModified' => date('Y-m-d H:i:s'),
            'ModifiedByAgentID' => 1,
        );
        $this->db->where('StaticID', $data['id']);
        $this->db->update('vicflora_static', $update);
    }
    
    public function addStaticPage($data) {
        $insert = array(
            'Uri' => $data['uri'],
            'PageTitle' => $data['title'],
            'TimestampCreated' => date('Y-m-d H:i:s'),
            'CreatedByAgentID' => 1
        );
        $this->db->insert('vicflora_static', $insert);
    }
    
    public function getHomepageImages() {
        $this->db->select('CumulusRecordID, Caption, Creator, RightsHolder, License');
        $this->db->from('cumulus_image');
        $this->db->where('SubjectCategory','Botanical art, home page');
        $this->db->order_by('RAND()');
        $this->db->limit(6);
        $query = $this->db->get();
        return $query->result();
    }
    
    
    
    
}
?>