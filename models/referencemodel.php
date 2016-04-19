<?php
require_once 'third_party/uuid/uuid.php';

class ReferenceModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function referenceLookupAutocomplete($term) {
        $ret = array();
        $this->basicQuery();
        $this->inPublicationSelect();
        $this->db->where('r.Subject !=', 'namePublishedIn');
        $this->db->like('r.Author', $term, 'after');
        $this->db->order_by('r.Author, r.PublicationYear');
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $ret[] = (object) array(
                    'value' => $row->ReferenceID,
                    'label' => $row->Author . ' (' . $row->PublicationYear . ')',
                    'description' => $this->compileDescription($row)
                );
            }
        }
        return $ret;
    }
    
    public function getReferenceData($id) {
        $this->basicQuery();
        $this->db->where('r.ReferenceID', $id);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        }
        else {
            return FALSE;
        }
    }
    
    public function getFullReference($id) {
        $this->basicQuery();
        $this->inPublicationSelect();
        $this->db->where('r.ReferenceID', $id);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return (object) array(
                'value' => $row->ReferenceID,
                'label' => $row->Author . ' (' . $row->PublicationYear . ')',
                'description' => $this->compileDescription($row)
            );
        }
        else {
            return FALSE;
        }
    }
    
    public function getInPublications($id) {
        $this->basicQuery();
        $this->db->where('r.InPublicationID', $id);
        $this->db->order_by('r.Page, r.Author');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $ret = array();
            foreach ($query->result() as $row) {
                $row->InPublicationID = NULL;
                $ret[] = (object) array(
                    'value' => $row->ReferenceID,
                    'label' => $row->Author . ' (' . $row->PublicationYear . ')',
                    'description' => $this->compileDescription($row)
                );
            }
            return $ret;
        }
        else return FALSE;
    }
    
    public function updateReference($data, $id=FALSE) {
        $updArray = array();
        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) == 'ref_') {
                $keybits = explode('_', $key);
                $field = '';
                for ($i = 1; $i < count($keybits); $i++) {
                    $field .= ($keybits[$i] == 'id') ? strtoupper($keybits[$i]) : ucfirst($keybits[$i]);
                }
                $updArray[$field] = ($value) ? $value : NULL;
            }
        }
        $updArray['TimestampModified'] = date('Y-m-d H:i:s');
        $updArray['ModifiedByID'] = $this->session->userdata('id');
        
        if ($id) {
            $updArray['Version']++;
            $this->db->where('ReferenceID', $id);
            $this->db->update('vicflora_reference', $updArray);
        }
        else {
            $newID = $this->getNewReferenceID();
            $updArray['ReferenceID'] = $newID;
            $updArray['Author'] = str_replace('|', '; ', $updArray['Author']);
            $updArray['TimestampCreated'] = $updArray['TimestampModified'];
            $updArray['CreatedByID'] = $updArray['ModifiedByID'];
            $updArray['Version'] = 1;
            $updArray['GUID'] = UUID::v4();
            $updArray['Subject'] = 'reference';
            $this->db->insert('vicflora_reference', $updArray);
            return $newID;
        }
    }
    
    public function getTaxonReferences($guid) {
        $ret = array();
        $this->basicQuery();
        $this->inPublicationSelect();
        $this->db->join('vicflora_taxon_reference tr', 'r.ReferenceID=tr.ReferenceID');
        $this->db->join('vicflora_taxon t', 'tr.TaxonID=t.TaxonID');
        $this->db->where('t.GUID', $guid);
        $this->db->order_by('r.Author, r.PublicationYear');
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $ret[] = (object) array(
                    'value' => $row->ReferenceID,
                    'label' => $row->Author . ' (' . $row->PublicationYear . ')',
                    'description' => $this->compileDescription($row)
                );
            }
        }
        return $ret;
    }
    
    public function createTaxonReference($taxonID, $referenceID) {
        $tid = $this->getTaxonID($taxonID);
        if (!$tid) {
            return FALSE;
        }
        $id = $this->findTaxonReference($tid, $referenceID);
        if ($id) {
            return FALSE;
        }
        $ins = array(
            'TimestampCreated' => date('Y-m-d H:i:s'),
            'TimestampModified' => date('Y-m-d H:i:s'),
            'Version' => 1,
            'TaxonID' => $tid,
            'ReferenceID' => $referenceID,
            'CreatedByID' => $this->session->userdata('id'),
            'ModifiedByID' => $this->session->userdata('id')
        );
        $this->db->insert('vicflora_taxon_reference', $ins);
        return $this->getFullReference($referenceID);
    }
    
    public function deleteTaxonReference($taxonID, $referenceID) {
        $tid = $this->getTaxonID($taxonID);
        if (!$tid) {
            return FALSE;
        }
        $id = $this->findTaxonReference($tid, $referenceID);
        if ($id) {
            $this->db->where('TaxonReferenceID', $id);
            $this->db->delete('vicflora_taxon_reference');
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    private function getTaxonID($guid) {
        $this->db->select('TaxonID');
        $this->db->from('vicflora_taxon');
        $this->db->where('GUID', $guid);
        $query = $this->db->get();
        if (!$query->num_rows()) {
            return FALSE;
        }
        else {
            $row = $query->row();
            return $row->TaxonID;
        }
    }
    
    private function findTaxonReference($taxonID, $referenceID) {
        $this->db->select('TaxonReferenceID');
        $this->db->from('vicflora_taxon_reference');
        $this->db->where('TaxonID', $taxonID);
        $this->db->where('ReferenceID', $referenceID);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->TaxonReferenceID;
        }
        else {
            return FALSE;
        }
    }
    
    private function basicQuery() {
        $this->db->select('r.ReferenceID, r.Version, r.Author, r.PublicationYear, 
            r.Title, r.JournalOrBook, r.Series, r.Edition, r.Volume, r.Part,
            r.Page, r.Publisher, r.PlaceOfPublication, r.InPublicationID');
        $this->db->from('vicflora_reference r');
        $this->db->join('vicflora_reference ir', 'r.InPublicationID=ir.ReferenceID', 'left');
    }
    
    private function inPublicationSelect() {
        $this->db->select('ir.Author as InAuthor, ir.PublicationYear AS InPublicationYear, 
            ir.Title AS InTitle, ir.JournalOrBook AS InJournalOrBook, ir.Series AS InSeries,
            ir.Edition AS InEdition, ir.Volume AS InVolume, ir.Part AS InPart,
            ir.Page AS InPage, ir.Publisher AS InPublisher, 
            ir.PlaceOfPublication AS InPlaceOfPublication');
    }
    
    private function compileDescription($data) {
        $desc = preg_replace('/~([^~]*)~/', '<i>$1</i>' , $data->Title) . ', ';
        if ($data->JournalOrBook) {
            $desc .= '<i>' . $data->JournalOrBook . '</i>';
            if ($data->Volume) {
                $desc .= ' <b>' . $data->Volume . '</b>';
                if ($data->Part) {
                    $desc .= '(' . $data->Part . ')';
                }
                if ($data->Page) {
                    $desc .= ': ' . $data->Page;
                }
            }
            elseif ($data->Page) {
                $desc .= ' ' . $data->Page;
            }
        }
        elseif ($data->Publisher || $data->PlaceOfPublication) {
            if ($data->Publisher) {
                $desc .= $data->Publisher;
                if ($data->PlaceOfPublication) {
                    $desc .= ', ' . $data->PlaceOfPublication;
                }
            }
            else {
                    $desc .= $data->PlaceOfPublication;
            }
        }
        if ($data->InPublicationID) {
            if ($data->Page) {
                $desc .= $data->Page . ', ';
            }
            $desc .= $this->compileInDescription($data);
        }
        $desc .= '.';
        return $desc;
    }
    
    private function compileInDescription($data) {
        $desc = 'in: ';
        if ($data->InAuthor) {
            $desc .= $data->InAuthor . ', ';
        }
        $desc .= $data->InTitle . ', ';
        if ($data->InPublisher || $data->InPlaceOfPublication) {
            if ($data->InPublisher) {
                $desc .= $data->InPublisher;
                if ($data->InPlaceOfPublication) {
                    $desc .= ', ' . $data->InPlaceOfPublication;
                }
            }
            else {
                    $desc .= $data->InPlaceOfPublication;
            }
        }
        return $desc;
    }
    
    private function getNewReferenceID() {
        $this->db->select('max(ReferenceID)+1 AS refid', FALSE);
        $this->db->from('vicflora_reference');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->refid;
        }
        else return FALSE;
    }
    
    
    
    
}

/* End of file referencemodel.php */
/* Location: ./models/referencemodel.php */
