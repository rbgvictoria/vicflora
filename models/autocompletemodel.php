<?php

class AutoCompleteModel extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function findNames($searchstring) {
        $ret = array();
        if (strpos($searchstring, ' ')) {
            $this->db->select('n.FullName AS Name');
            $this->db->from('vicflora_taxon t');
            $this->db->join('vicflora_name n', 't.NameID=n.NameID');
            $this->db->where('t.RankID >= 220', FALSE, FALSE);
            $this->db->like('n.FullName', $searchstring, 'after');
            $this->db->where('t.DoNotIndex IS NULL', FALSE, FALSE);
            $this->db->group_by('n.FullName');
        }
        else {
            $this->db->select('n.Name');
            $this->db->from('vicflora_taxon t');
            $this->db->join('vicflora_name n', 't.NameID=n.NameID');
            $this->db->where('t.RankID <= 180', FALSE, FALSE);
            $this->db->like('n.Name', $searchstring, 'after');
            $this->db->where('t.DoNotIndex IS NULL', FALSE, FALSE);
            $this->db->group_by('n.FullName');
        }
        $this->db->order_by('Name');
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row)
                $ret[] = $row->Name;
        }
        return $ret;
    }
    
    public function findAcceptedNames($searchstring) {
        $ret = array();
        $this->db->select('n.FullName AS Name');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->where('t.TaxonomicStatus', 'accepted');
        $this->db->like('n.FullName', $searchstring, 'after');
        $query = $this->db->get();
        if($query->num_rows()) {
            foreach ($query->result() as $row)
                $ret[] = $row->Name;
        }
        return $ret;
    }
    
    public function findParent($searchstring, $taxontreedefitemid) {
        $ret = array();
        $this->db->select('n.Name');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->where('t.TaxonomicStatus', 'accepted');
        switch ($taxontreedefitemid) {
            case 12:
                $this->db->where('t.taxonTreeDefItemID', 11);
                break;
            case 11:
                $this->db->where_in('t.taxonTreeDefItemID', array(9,10));
                break;
            case 10:
                $this->db->where('t.taxonTreeDefItemID', 9);
                break;
            case 9:
                $this->db->where_in('t.taxonTreeDefItemID', array(6,7,8));
                break;
            case 8:
                $this->db->where_in('t.taxonTreeDefItemID', array(6,7));
                break;
            case 7:
                $this->db->where_in('t.taxonTreeDefItemID', array(6));
                break;
            case 6:
                $this->db->where_in('t.taxonTreeDefItemID', array(4,5));
                break;
            case 5:
                $this->db->where_in('t.taxonTreeDefItemID', array(4));
                break;
            case 4:
                $this->db->where_in('t.taxonTreeDefItemID', array(2,3));
                break;
            case 3:
                $this->db->where_in('t.taxonTreeDefItemID', array(2));
                break;
            default:
                break;
        }
        $this->db->like('n.Name', $searchstring, 'after');
        $query = $this->db->get();
        if($query->num_rows()) {
            foreach ($query->result() as $row)
                $ret[] = $row->Name;
        }
        return $ret;
    }
    
    public function findGlossaryTerm($searchstring) {
        $ret = array();
        $this->db->select('Name');
        $this->db->from('keybase_glossary.term');
        $this->db->where('GlossaryID', 4);
        $this->db->like('Name', $searchstring, 'after');
        $this->db->order_by('Name');
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $ret[] = $row->Name;
            }
        }
        return $ret;
    }
}
?>
