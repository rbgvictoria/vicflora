<?php

class SearchModel extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function parseFacetQuery($fq, $getRecords = FALSE) {
        $accepted = FALSE;
        $lowest = FALSE;
        $present = FALSE;
        
        $ret = array();
        $ret['join'] = array();
        $ret['where'] = array();
        
        foreach ($fq as $key => $value) {
            if ($key == 'taxonomicStatus') {
                //$lowest = TRUE;
                $st = explode(',', $value);
                if (in_array('accepted', $st) && !in_array('notCurrent', $st)) {
                    $accepted = TRUE;
                }
                elseif (in_array('notCurrent', $st) && !in_array('accepted', $st)) {
                    $this->db->where("(t.TaxonomicStatus IS NULL OR t.TaxonomicStatus!='Accepted')", FALSE, FALSE);
                    $ret['where'][] = 'TaxonomicStatus:!Accepted';
                }
            }
            
            if ($key == 'taxonType') {
                $st = explode(',', $value);
                if (!in_array('c', $ret['join'])) {
                    $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left', FALSE);
                    $ret['join'][] = 'c';
                }
                if (in_array('endTaxa', $st) && !in_array('parentTaxa', $st)) {
                    $this->db->where('c.TaxonID IS NULL', FALSE, FALSE);
                    $ret['where'][] = 'endTaxa';
                }
                elseif (in_array('parentTaxa', $st) && !in_array('endTaxa', $st)) {
                    $this->db->where('c.TaxonID IS NOT NULL', FALSE, FALSE);
                    $ret['where'][] = 'parentTaxa';
                }
            }
            
            if ($key == 'occurrenceStatus') {
                $lowest = TRUE;
                $accepted = TRUE;
                $st = explode(',', $value);
                if ((in_array('present', $st)) && !in_array('extinct', $st)) {
                    $this->db->where_in('t.OccurrenceStatus', array('present', 'endemic'));
                    $ret['where'][] = 'present';
                }
                elseif (in_array('endemic', $st)) {
                    $this->db->where('t.OccurrenceStatus', 'endemic');
                    $ret['where'][] = 'endemic';
                }
                elseif (in_array('extinct', $st)) {
                    $this->db->where('t.OccurrenceStatus', 'extinct');
                    $ret['where'][] = 'extinct';
                }
            }
            
            if ($key == 'establishmentMeans') {
                $accepted = TRUE;
                $lowest = TRUE;
                $est = explode(',', $value);
                if (in_array('native', $est) && !in_array('introduced', $est)) {
                    $this->db->where('t.EstablishmentMeans', 'native');
                    $ret['where'][] = 'native';
                }
                elseif(in_array('introduced', $est) && !in_array('native', $est)) {
                    $this->db->where('t.EstablishmentMeans', 'introduced');
                    $ret['where'][] = 'introduced';
                }
            }
            
            if ($key == 'epbc') {
                $accepted = TRUE;
                $lowest = TRUE;
                if (!$getRecords) {
                    $this->db->join('vicflora_taxonattribute a2', "t.TaxonID=a2.TaxonID AND a2.Attribute='EPBC (Jan. 2014)'", 'left', FALSE);
                    $ret['join'][] = 'a2';
                }
                $st = explode(',', $value);
                foreach ($st as $val)
                    $st[] = '(' . $val . ')';
                $this->db->where_in('a2.StrValue', $st);
            }
            
            if ($key == 'vrot') {
                $accepted = TRUE;
                $lowest = TRUE;
                if (!$getRecords) {
                    $this->db->join('vicflora_taxonattribute a3', "t.TaxonID=a3.TaxonID AND a3.Attribute='VROT'", 'left', FALSE);
                    $ret['join'][] = 'a3';
                }
                $st = explode(',', $value);
                foreach ($st as $val)
                    $st[] = '(' . $val . ')';
                $this->db->where_in('a3.StrValue', $st);
            }
            
            if ($key == 'ffg') {
                $accepted = TRUE;
                $lowest = TRUE;
                if (!$getRecords) {
                    $this->db->join('vicflora_taxonattribute a4', "t.TaxonID=a4.TaxonID AND a4.Attribute='FFG (2013)'", 'left', FALSE);
                    $ret['join'][] = 'a4';
                }
                $st = explode(',', $value);
                foreach ($st as $val)
                    $st[] = '(' . $val . ')';
                $this->db->where_in('a4.StrValue', $st);
            }
            
            if ($key == 'ibra') {
                $accepted = TRUE;
                $lowest = TRUE;
                $this->db->join('vicflora_distribution d1', 
                        "t.TaxonID=d1.TaxonID AND d1.AreaClass='IBRA 6.1 region'", 'left', FALSE);
                $ret['join'][] = 'd1';
                $st = explode(',', $value);
                $this->db->where_in('d1.AreaCode', $st);
            }
            
            if ($key == 'ibra_sub') {
                $accepted = TRUE;
                $lowest = TRUE;
                $this->db->join('vicflora_distribution d2', 
                        "t.TaxonID=d2.TaxonID AND d2.AreaClass='IBRA 6.1 subregion'", 'left', FALSE);
                $ret['join'][] = 'd2';
                $st = explode(',', $value);
                $this->db->where_in('d2.AreaCode', $st);
            }
            
            if ($key == 'subclass') {
                $values = explode(',', $value);
                if (!in_array('cl', $ret['join'])) {
                    $this->db->join('vicflora_classification cl', 't.TaxonID=cl.TaxonID', 'left');
                    $ret['join'][] = 'cl';
                }
                $this->db->where_in('cl.Subclass', $values);
            }
            
            if ($key == 'superorder') {
                $values = explode(',', $value);
                if (!in_array('cl', $ret['join'])) {
                    $this->db->join('vicflora_classification cl', 't.TaxonID=cl.TaxonID', 'left');
                    $ret['join'][] = 'cl';
                }
                $this->db->where_in('cl.Superorder', $values);
            }
            
            if ($key == 'order') {
                $values = explode(',', $value);
                if (!in_array('cl', $ret['join'])) {
                    $this->db->join('vicflora_classification cl', 't.TaxonID=cl.TaxonID', 'left');
                    $ret['join'][] = 'cl';
                }
                $this->db->where_in('cl.Order', $values);
            }
            
            if ($key == 'family') {
                $values = explode(',', $value);
                if (!in_array('cl', $ret['join'])) {
                    $this->db->join('vicflora_classification cl', 't.TaxonID=cl.TaxonID', 'left');
                    $ret['join'][] = 'cl';
                }
                $this->db->where_in('cl.Family', $values);
            }
            
            if ($key == 'genus') {
                $values = explode(',', $value);
                if (!in_array('cl', $ret['join'])) {
                    $this->db->join('vicflora_classification cl', 't.TaxonID=cl.TaxonID', 'left');
                    $ret['join'][] = 'cl';
                }
                $this->db->where_in('cl.Genus', $values);
            }
            
            if ($key == 'rank') {
                $accepted = TRUE;
                $values = explode(',', $value);
                $this->db->join('vicflora_taxontreedefitem td', 't.TaxonTreeDefItemID=td.TaxonTreeDefItemID');
                $ret['join'][] = 'td';
                $this->db->where_in('td.Name', $values);
                $ret['where'][] = 'rank';
            }
            
        }
        
        if ($accepted) {
            $this->db->where('t.TaxonomicStatus', 'Accepted');
            $ret['where'][] = 'TaxonomicStatus:Accepted';
        }
        if ($lowest) {
            if (!in_array('c', $ret['join'])) {
                $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left', FALSE);
                $ret['join'][] = 'c';
            }
            $this->db->where('c.TaxonID IS NULL', FALSE, FALSE);
            $ret['where'][] = 'endTaxa';
        }
        
        return $ret;
    }
    
    public function countMatches($term, $fq = FALSE) {
        $term = $this->db->escape_str($term);
        $this->db->select('count(DISTINCT t.TaxonID) AS nummatches', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->like('n.FullName', $term, 'after');
        
        if ($fq)
            $this->parseFacetQuery($fq);
        
        $query = $this->db->get();
        $row = $query->row();
        return $row->nummatches;
    }
    
    public function findTaxa($term, $fq=FALSE, $limit=FALSE, $offset=0) {
        $term = $this->db->escape_str($term);
        $ret = array();
        $this->db->select("t.TaxonID, t.GUID, n.FullName, n.Author, t.TaxonomicStatus, t.OccurrenceStatus, td.Name AS TaxonRank,
              t.RankID, t.EstablishmentMeans, a2.StrValue AS EPBC,
              a3.StrValue AS VROT, a4.StrValue AS FFG");
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxonattribute a2', "t.TaxonID=a2.TaxonID AND a2.Attribute='EPBC (Jan. 2014)'", 'left', FALSE);
        $this->db->join('vicflora_taxonattribute a3', "t.TaxonID=a3.TaxonID AND a3.Attribute='VROT'", 'left', FALSE);
        $this->db->join('vicflora_taxonattribute a4', "t.TaxonID=a4.TaxonID AND a4.Attribute='FFG (2013)'", 'left', FALSE);
        $this->db->like('n.FullName', $term, 'after');
        $this->db->group_by('t.TaxonID');
        $this->db->order_by('n.FullName');
        
        if ($fq)
            $q = $this->parseFacetQuery($fq, TRUE);
        
        if (!$fq || !in_array('td', $q['join']))
            $this->db->join('vicflora_taxontreedefitem td', 't.TaxonTreeDefItemID=td.TaxonTreeDefItemID');
        
        if ($limit) 
            $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        if ($query->num_rows()) {
            $genus = FALSE;
            $family = FALSE;
            foreach ($query->result() as $row) {
                $taxon = array();
                $taxon['TaxonID'] = $row->TaxonID;
                $taxon['GUID'] = $row->GUID;
                $taxon['FullName'] = $row->FullName;
                $taxon['Author'] = $row->Author;
                $taxon['TaxonomicStatus'] = $row->TaxonomicStatus;
                $taxon['OccurrenceStatus'] = $row->OccurrenceStatus;
                $taxon['TaxonRank'] = $row->TaxonRank;
                $taxon['EstablishmentMeans'] = ($row->EstablishmentMeans == 'introduced');
                $taxon['EPBC'] = $row->EPBC;
                $taxon['VROT'] = $row->VROT;
                $taxon['FFG'] = $row->FFG;
                
                $taxon['Family'] = FALSE;
                if ($row->TaxonomicStatus == 'Accepted') {
                    if ($row->RankID >=180) {
                        if ($row->RankID == 180)
                            $gen = $row->FullName;
                        else
                            $gen = substr ($row->FullName, 0, strpos ($row->FullName, ' '));
                        
                        if ($gen == $genus) {
                            $taxon['Family'] = $family;
                        }
                        else {
                            $family = $this->getFamily($row->TaxonID);
                            $family = ($family) ? $family['Name'] : FALSE;
                            $taxon['Family'] = $family;
                            $genus = $gen;
                        }
                    }
                }
                $ret[] = $taxon;
            }
        }
        return $ret;
        
    }
    
    private function getFamily($taxonid) {
        $ret = array();
        
        // get node number first
        $this->db->select('NodeNumber');
        $this->db->from('vicflora_taxontree');
        $this->db->where('TaxonID', $taxonid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $nodenumber = $row->NodeNumber;
            
            $this->db->select('t.TaxonID, n.Name');
            $this->db->from('vicflora_taxon t');
            $this->db->join('vicflora_name n', 't.NameID=n.NameID');
            $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
            $this->db->where('tt.NodeNumber <=', $nodenumber);
            $this->db->where('tt.HighestDescendantNodeNumber >=', $nodenumber);
            $this->db->where('t.TaxonTreeDefItemID', 11);
            $query = $this->db->get();
            
            if ($query->num_rows()) {
                $ret = $query->row_array();
            }
            
        }
        return $ret;
    }

}

/* End of file searchmodel.php */
/* Location: ./models/searchmodel.php */
