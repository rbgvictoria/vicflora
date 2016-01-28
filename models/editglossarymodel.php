<?php
require_once 'glossarymodel.php';

class EditGlossaryModel extends GlossaryModel {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getGlossaryTerm($termid) {
        $this->db->select('TermID, Name, Definition');
        $this->db->from('keybase_glossary.term');
        $this->db->where('TermID', $termid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        }
        else {
            return FALSE;
        }
    }
    
    public function getGlossaryTermRelationships($termid) {
        $this->db->select('r.RelationshipID, rt.relationshipTypeID, rt.Name AS relationshipType, t.termID, t.Name AS relatedTerm');
        $this->db->from('keybase_glossary.relationship r');
        $this->db->join('keybase_glossary.relationshiptype rt', 'r.RelationshipTypeID=rt.RelationshipTypeID');
        $this->db->join('keybase_glossary.term t', 'r.RelatedTermID=t.TermID');
        $this->db->where('r.TermID', $termid);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function updateGlossaryTerm($termid, $termName, $termDefinition) {
        $this->db->select('Version');
        $this->db->from('keybase_glossary.term');
        $this->db->where('TermID', $termid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $version = $row->Version;
            
            $updArray = array(
                'TimestampModified' => date('Y-m-d H:i:s'),
                'Version' => $version + 1,
                'Name' => $termName,
                'Definition' => $termDefinition,
                'ModifiedByID' => $this->session->userdata['id']
            );
            $this->db->where('TermID', $termid);
            $this->db->update('keybase_glossary.term', $updArray);
        }
    }
    
    public function insertGlossaryTerm($termName, $termDefinition) {
        $this->db->select('max(TermID) as maxID', FALSE);
        $this->db->from('keybase_glossary.term');
        $query = $this->db->get();
        $row = $query->row();
        $termID = $row->maxID + 1;
        
        $insArray = array(
            'TermID' => $termID,
            'TimestampCreated' => date('Y-m-d H:i:s'),
            'TimestampModified' => date('Y-m-d H:i:s'),
            'Version' => 1,
            'Name' => $termName,
            'Definition' => $termDefinition,
            'GlossaryID' => 4,
            'CreatedByID' => $this->session->userdata['id'],
            'ModifiedByID' => $this->session->userdata['id']
        );
        
        $this->db->insert('keybase_glossary.term', $insArray);
        return $termID;
    }
    
    public function delGlossaryTermRelationship($relID) {
        $this->db->select('TermID, RelatedTermID');
        $this->db->from('keybase_glossary.relationship');
        $this->db->where('RelationshipID', $relID);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            
            $this->db->where('TermID', $row->TermID);
            $this->db->where('RelatedTermID', $row->RelatedTermID);
            $this->db->delete('keybase_glossary.relationship');
            
            $this->db->where('TermID', $row->RelatedTermID);
            $this->db->where('RelatedTermID', $row->TermID);
            $this->db->delete('keybase_glossary.relationship');
        }
    }
    
    public function insGlossaryTermRelationship($termID, $relatedTerm, $relationshipTypeID) {
        $this->db->select('TermID');
        $this->db->from('keybase_glossary.term');
        $this->db->where('GlossaryID', 4);
        $this->db->where('Name', $relatedTerm);
        $query = $this->db->get();
        $row = $query->row();
        $relTermID = $row->TermID;
        
        if ($relTermID && $relationshipTypeID) {
            $this->db->select('RelationshipID');
            $this->db->from('keybase_glossary.relationship');
            $this->db->where('TermID', $termID);
            $this->db->where('RelatedTermID', $relTermID);
            $this->db->where('RelationshipTypeID', $relationshipTypeID);
            $query = $this->db->get();
            if ($query->num_rows() == 0) {
            
                $insArray = array(
                    'TimestampCreated' => date('Y-m-d H:i:s'),
                    'TimestampModified' => date('Y-m-d H:i:s'),
                    'Version' => 1,
                    'TermID' => $termID,
                    'RelatedTermID' => $relTermID,
                    'RelationshipTypeID' => $relationshipTypeID
                );
                $this->db->insert('keybase_glossary.relationship', $insArray);

                $this->db->select('InverseRelationshipTypeID');
                $this->db->from('keybase_glossary.relationshiptype');
                $this->db->where('RelationshipTypeID', $relationshipTypeID);
                $query = $this->db->get();
                $invRelTypeID = FALSE;
                if ($query->num_rows()) {
                    $row = $query->row();
                    $invRelTypeID = $row->InverseRelationshipTypeID;
                }
        
                if ($invRelTypeID) {
                    $insArray = array(
                        'TimestampCreated' => date('Y-m-d H:i:s'),
                        'TimestampModified' => date('Y-m-d H:i:s'),
                        'Version' => 1,
                        'TermID' => $relTermID,
                        'RelatedTermID' => $termID,
                        'RelationshipTypeID' => $invRelTypeID
                    );
                    $this->db->insert('keybase_glossary.relationship', $insArray);
                }
            }
        }
    }
    
    public function getTermName($termID) {
        $this->db->select('Name');
        $this->db->from('keybase_glossary.term');
        $this->db->where('TermID', $termID);
        $query = $this->db->get();
        $row = $query->row();
        return $row->Name;
    }
    
    public function delGlossaryTerm($termID) {
        $this->db->where('TermID', $termID);
        $this->db->delete('keybase_glossary.term'); 
    }
    

}

/* End of file glossarymodel.php */
/* Location: ./models/glossarymodel.php */
