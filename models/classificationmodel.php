<?php

class ClassificationModel extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getCurrentTaxon($guid) {
        $this->db->select('t.GUID, td.Name AS `Rank`, n.FullName, IF(t.RankID>=140, n.Author, NULL) AS Author, tt.Depth, t.RankID', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxontreedefitem td', 't.TaxonTreeDefItemID=td.TaxonTreeDefitemID');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->row_array();
        else
            return FALSE;
    }
    
    public function getAncestors($guid) {
        $this->db->select('tt.NodeNumber');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $this->db->select('t.GUID, td.Name AS `Rank`, n.FullName, 
                IF(t.RankID>=140, n.Author, NULL) AS Author, tt.Depth, t.RankID', FALSE);
            $this->db->from('vicflora_taxon t');
            $this->db->join('vicflora_name n', 't.NameID=n.NameID');
            $this->db->join('vicflora_taxontreedefitem td', 't.TaxonTreeDefItemID=td.TaxonTreeDefitemID');
            $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
            $this->db->where('tt.NodeNumber <', $row->NodeNumber);
            $this->db->where('tt.HighestDescendantNodeNumber >=', $row->NodeNumber);
            $this->db->where('t.DoNotIndex IS NULL', FALSE, FALSE);
            $this->db->order_by('t.RankID');
            $query = $this->db->get();
            if ($query->num_rows())
                return $query->result_array();
            else 
                return FALSE;
        }
        else
            return FALSE;
    }
    
    public function getChildren($guid) {
        $this->db->select('t.GUID, td.Name AS `Rank`, n.FullName, IF(t.RankID>=140, n.Author, NULL) AS Author, tt.Depth, t.RankID',
                FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxontreedefitem td', 't.TaxonTreeDefItemID=td.TaxonTreeDefitemID');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->join('vicflora_taxon p', 't.ParentID=p.TaxonID');
        $this->db->where('p.GUID', $guid);
        $this->db->where('t.DoNotIndex IS NULL', FALSE, FALSE);
        $this->db->order_by('n.FullName');
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->result_array();
        else
            return FALSE;
    }
    
}

?>
