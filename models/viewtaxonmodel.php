<?php

require_once('taxonmodel.php');

class ViewTaxonModel extends TaxonModel {
    public function __construct() {
        parent::__construct();
    }
    
    function getChanges($guid) {
        $this->db->select('c.ChangeID, DATE(c.TimestampCreated) AS ChangeDate, c.NewNameID,
            nn.FullName AS AcceptedName, nn.Author AS AcceptedNameAuthor, c.Source, c.ChangeType');
        $this->db->from('vicflora_change c');
        $this->db->join('vicflora_taxon t', 'c.NameID=t.TaxonID');
        $this->db->join('vicflora_taxon nt', 'c.NewNameID=nt.TaxonID');
        $this->db->join('vicflora_name nn', 'nt.NameID=nn.NameID');
        $this->db->where('t.GUID', $guid);
        $this->db->order_by('c.TimestampCreated');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function getAcceptedName($fullname) {
        $this->db->select('t.TaxonID, n.FullName, n.Author');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->where('n.FullName', $fullname);
        $this->db->where('t.TaxonomicStatus', 'accepted');
        $query = $this->db->get();
        if ($query->num_rows()) 
            return $query->row_array();
        else
            return FALSE;
    }
    
    function getAcceptedNameByID($id) {
        $this->db->select('t.TaxonID, n.FullName, n.Author');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->where('t.TaxonID', $id);
        $query = $this->db->get();
        if ($query->num_rows()) 
            return $query->row_array();
        else
            return FALSE;
    }
    
    public function getLinks($guid) {
        $this->db->select('l.Flora, l.Url');
        $this->db->from('vicflora_link l');
        $this->db->join('vicflora_taxon t', 'l.TaxonID=t.TaxonID');
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        return $query->result_array();
    }
}
?>
