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
        $this->db->select('i.uid as GUID, i.cumulus_record_id as CumulusRecordID, '
                . 'i.pixel_x_dimension as PixelXDimension, '
                . 'i.pixel_y_dimension as PixelYDimension, '
                . 'i.caption as Caption,'
                . "i.creator as Creator, 'Royal Botanic Gardens Board' as RightsHolder, i.license AS License", false);
        $this->db->from('keybase_glossary.term t');
        $this->db->join('glossaryterm_image gi', 't.TermID=gi.TermID');
        $this->db->join('cumulus_images_cip i', 'gi.uid=i.uid');
        $this->db->where('t.GlossaryID', 4);
        $this->db->where('t.Name', $term);
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $sizeObj = $this->imageSize($row->PixelXDimension, $row->PixelYDimension);
                $caption = $this->imageCaption($row);
                $ret[] = array(
                    'id' => $row->CumulusRecordID,
                    'width' => $sizeObj->width,
                    'height' => $sizeObj->height,
                    'alt' => $caption->alt,
                    'caption' => $caption->caption
                );
            }
        }
        return $ret;
    }
    
    private function imageCaption($data) {
        if (substr($data->License, 0, 5) === 'CC BY') {
            $bits = explode(' ', $data->License);
            $url = 'https://creativecommons.org/licenses/';
            $url .= strtolower($bits[1]);
            $url .= (isset($bits[2])) ? '/' . $bits[2] : '/4.0';
            if (isset($bits[3])) {$url .= '/' .strtolower ($bits[3]);}
            $license = "<a href='$url'>$data->License</a>";
        }
        elseif ($data->License == 'All rights reserved') {
            $license = 'All rights reserved';
        }
        else {
            $license = "<a href='https://creativecommons.org/licenses/by/4.0'>CC BY 4.0</a>";
        }
        
        $alt = $data->Caption;
        
        $caption = $data->Caption;
        $caption .= '<br/>';
        $caption .= 'Illustration: ';
        $caption .= $data->Creator . ', &copy ' . date('Y') . ' ';
        $caption .= ($data->RightsHolder) ? $data->RightsHolder : 'Royal Botanic Gardens Victoria';
        $caption .= ', ' . $license . '.';
        
        return (object) array(
            'alt' => $alt,
            'caption' => $caption
        );
    }
    
    private function imageSize($width, $height) {
        $width = $width / 2;
        $height = $height / 2;
        $sizeObj = new stdClass();
        $sizeObj->width = $width;
        $sizeObj->height = $height;
        if ($width > $height) {
            if ($width > 1024) {
                $sizeObj->height = ceil($height * (1024 / $width));
                $sizeObj->width = 1024;
            }
            $sizeObj->size = $width;
        }
        else {
            if ($height > 1024) {
                $sizeObj->width = ceil($width * (1024 / $height));
                $sizeObj->height = 1024;
            }
            $sizeObj->size = $height;
        }
        return $sizeObj;
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
