<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of webservicemodel
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
class WebServiceModel extends CI_Model {
    
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getSpecimenImageCount($nodeNumber, $highestDescendantNodeNumber)
    {
        $this->db->from('vicflora_specimen_images i');
        $this->db->join('vicflora_taxon t', 'i.accepted_id=t.TaxonID');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('tt.NodeNumber >=', $nodeNumber);
        $this->db->where('tt.NodeNumber <=', $highestDescendantNodeNumber);
        $this->db->where_in('i.subject_category', array('Herbarium specimen', 'Herbarium specimen detail'));
        return $this->db->count_all_results();
    }
    
    public function getSpecimenImages($nodeNumber, $highestDescendantNodeNumber, $limit=12, $offset=0)
    {
        $this->db->select('i.cumulus_record_id as id, i.ala_image_uuid as alaImageUuid, i.scientific_name as scientificName,'
                . 'i.title, i.caption, i.pixel_x_dimension as pixelXDimension, i.pixel_y_dimension as pixelYDimension');
        $this->db->from('vicflora_specimen_images i');
        $this->db->join('vicflora_taxon t', 'i.accepted_id=t.TaxonID');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('tt.NodeNumber >=', $nodeNumber);
        $this->db->where('tt.NodeNumber <=', $highestDescendantNodeNumber);
        $this->db->where_in('i.subject_category', array('Herbarium specimen', 'Herbarium specimen detail'));
        $this->db->order_by('i.scientific_name');
        $this->db->order_by('i.cumulus_record_id');
        $this->db->limit($limit);
        $this->db->offset($offset);
        $query = $this->db->get();
            
        return $query->result();
    }
    
    public function getNode($taxonId)
    {
        $this->db->select('tt.nodeNumber, tt.highestDescendantNodeNumber');
        $this->db->from('vicflora_taxontree tt');
        $this->db->join('vicflora_taxon t', 'tt.TaxonID=t.TaxonID');
        $this->db->where('t.GUID', $taxonId);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        }
        return false;
    }
    
    public function getOtherImageCount($nodeNumber, $highestDescendantNodeNumber)
    {
        $this->db->from('cumulus_image i');
        $this->db->join('vicflora_taxon t', 'i.AcceptedID=t.TaxonID');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('tt.NodeNumber >=', $nodeNumber);
        $this->db->where('tt.NodeNumber <=', $highestDescendantNodeNumber);
        return $this->db->count_all_results();
    }
    
}
