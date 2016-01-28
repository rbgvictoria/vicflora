<?php

class GlossaryModel extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getGlossaryTerms($firstletter) {
        $this->db->select('Name AS term');
        $this->db->from('keybase_glossary.term');
        $this->db->where('GlossaryID', 4);
        $this->db->like('Name', $firstletter, 'after');
        $this->db->order_by('term');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getGlossaryDefinition($term) {
        $this->db->select("t.TermID, t.Name AS term, t.Definition AS definition");
        $this->db->from('keybase_glossary.term t');
        $this->db->where('t.GlossaryID', 4);
        $this->db->where('t.Name', $term);
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->row_array();
    }
    
    public function getGlossaryImages($term) {
        $ret = array();
        $this->db->select('i.GUID, i.CumulusRecordID');
        $this->db->from('keybase_glossary.term t');
        $this->db->join('glossaryterm_image gi', 't.TermID=gi.TermID');
        $this->db->join('cumulus_image i', 'gi.ImageID=i.ImageID');
        $this->db->where('t.GlossaryID', 4);
        $this->db->where('t.Name', $term);
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $ret[] = array(
                    'imageUrl' => site_url() . 'flora/glossary_image/' . $row->GUID,
                    'thumbnailUrl' => 'http://images.rbg.vic.gov.au/sites/T/library/' . $row->CumulusRecordID . '?b=256'
                );
            }
        }
        return $ret;
    }
    
    public function getGlossaryImage($guid) {
        $this->db->select("i.CumulusRecordID, 
            i.PixelXDimension, i.PixelYDimension,
            i.Subtype, i.Caption, i.SubjectPart, i.Creator, i.RightsHolder, i.License", FALSE);
        $this->db->from('cumulus_image i');
        $this->db->where('i.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row;
        }
    }
    
    public function getGlossaryTermRelationships($termid) {
        $this->db->select('rt.Name AS relationshipType, t.Name AS relatedTerm');
        $this->db->from('keybase_glossary.relationship r');
        $this->db->join('keybase_glossary.relationshiptype rt', 'r.RelationshipTypeID=rt.RelationshipTypeID');
        $this->db->join('keybase_glossary.term t', 'r.RelatedTermID=t.TermID');
        $this->db->where('r.TermID', $termid);
        $query = $this->db->get();
        return $query->result();
    }
    
}

/* End of file glossarymodel.php */
/* Location: ./models/glossarymodel.php */
