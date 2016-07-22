<?php

class ChecklistModel extends CI_Model {
    var $pgdb;
    
    public function __construct() {
        parent::__construct();
        $this->pgdb = $this->load->database('postgis', TRUE);
    }
    
    public function getParks() {
        $sql = <<<EOT
SELECT "name", "type", "res_number"
FROM vicflora.vicflora_capad
WHERE type_abbr IN ('NP', 'SP')
ORDER BY name
EOT;
        $query = $this->pgdb->query($sql);
        if ($query->num_rows()) {
            $ret = array();
            foreach ($query->result() as $row) {
                $ret[$row->res_number] = $row->name . ' ' . $row->type;
            }
            return $ret;
        }
        else {
            return FALSE;
        }
    }
    
    public function getParkInfo($park) {
        $this->pgdb->select('name, type, shape_area');
        $this->pgdb->from('vicflora.vicflora_capad');
        $this->pgdb->where('res_number', $park);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return array(
                        'name' => $row->name . ' ' . $row->type,
                        'area' => $row->shape_area
                    );
        }
        else {
            return FALSE;
        }
    }
    
    //public function getCheckListTaxa($park) {
    public function getCheckList($park) {
        $ret = new stdClass();
        $geom = $this->getParkGeometry($park);
        if ($geom) {
            $names = $this->getParkScientificNames($geom);
            if ($names) {
                $ids = $this->getParkTaxonIDs($names);
                if ($ids) {
                    $ret->parkNumber = $park;
                    $ret->numFound = count($ids);
                    $ret->taxa = $this->getParkTaxonDetail($ids);
                    return $ret;
                }
            }
        }
        return FALSE;
    }
    
    private function getParkGeometry($park) {
        $this->pgdb->select('geom');
        $this->pgdb->from('vicflora.vicflora_capad');
        $this->pgdb->where('res_number', $park);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->geom;
        }
    }
    
    private function getParkScientificNames($geom) {
        $this->pgdb->select('vicflora_scientific_name_id');
        $this->pgdb->from('vicflora.avh_occurrence');
        $this->pgdb->where('vicflora_scientific_name_id IS NOT NULL', FALSE, FALSE);
        $this->pgdb->where("ST_Intersects(geom, '$geom'::geometry(MultiPolygon, 4326))", FALSE, FALSE);
        $this->pgdb->group_by('vicflora_scientific_name_id');
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $ids = array();
            foreach ($query->result() as $row) {
                $ids[] = $row->vicflora_scientific_name_id;
            }
            return $ids;
        }
        else {
            return FALSE;
        }
    }
    
    private function getParkTaxa($names) {
        $this->pgdb->select("id as taxon_id, family, genus, genus||' '||specific_epithet as species, scientific_name", FALSE);
        $this->pgdb->from('vicflora.vicflora_taxon');
        $this->pgdb->where_in('scientific_name_id', $names);
        $this->pgdb->group_by('family, genus, specific_epithet, scientific_name, id');
        $query = $this->pgdb->get();
        return $query->result();
    }
    
    private function getParkTaxonIDs($names) {
        $this->pgdb->select('accepted_name_usage_id');
        $this->pgdb->from('vicflora.vicflora_taxon');
        $this->pgdb->where_in('scientific_name_id', $names);
        $this->pgdb->group_by('accepted_name_usage_id');
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $ids = array();
            foreach ($query->result() as $row) {
                $ids[] = $row->accepted_name_usage_id;
            }
            return $ids;
        }
        else {
            return FALSE;
        }
    }
    
    private function getParkTaxonDetail($ids) {
        $this->pgdb->select('id, scientific_name as "scientificName", 
            scientific_name_authorship as "scientificNameAuthorship", 
            accepted_name_usage_taxon_rank as taxonRank, phylum, class, subclass, 
            superorder, order, family, genus, 
            specific_epithet as "specificEpithet", 
            infraspecific_epithet as "infraspecificEpithet",
            occurrence_status as "occurrenceStatus",
            establishment_means as "establishmentMeans"');
        $this->pgdb->from('vicflora.vicflora_taxon');
        $this->pgdb->where_in('id', $ids);
        $query = $this->pgdb->get();
        return $query->result();
    }
    
    public function getCapadFromMap($long, $lat) {
        $sql = "SELECT res_number, name, type
            FROM vicflora.vicflora_capad
            WHERE ST_Dwithin(geom, ST_Transform(ST_GeomFromText('POINT($long $lat)', 3857), 4326), 0.08)
            ORDER BY name";
        $query = $this->pgdb->query($sql);
        return $query->result();
    }
    
    public function getOccurrencesFromPoint($taxonid,$long, $lat) {
        $sql = "SELECT uuid, taxon_id, scientific_name, catalog_number, decimal_longitude, decimal_latitude, 
                establishment_means, occurrence_status, sub_name_7
            FROM vicflora.occurrence_view
            WHERE accepted_name_usage_id='$taxonid'
              AND ST_Dwithin(geom, ST_GeomFromText('POINT($long $lat)', 4326), 0.08)";
        $query = $this->pgdb->query($sql);
        return $query->result();
    }
    
    public function updateOccurrence($uuid, $data) {
        $this->pgdb->where('uuid', $uuid);
        $upd = $data;
        if (in_array('establishment_means', array_keys($data))) {
            $upd['establishment_means_source'] = 'VicFlora';
        }
        $upd['timestamp_modified'] = date('Y-m-d H:i:s');
        $upd['user_id'] = $this->session->userdata('id');
        $this->pgdb->update('vicflora.vicflora_occurrence', $upd);
        
        $this->pgdb->select('taxon_id, species_guid, sub_code_7');
        $this->pgdb->from('vicflora.vicflora_occurrence');
        $this->pgdb->where('uuid', $uuid);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $taxonUpd = $this->updateDistribution($row->taxon_id, $row->sub_code_7);
            if ($row->species_guid != $row->taxon_id) {
                $this->updateDistribution($row->species_guid, $row->sub_code_7);
            }
            return $taxonUpd;
        }
    }
    
    private function updateDistribution($taxon, $region) {
        $this->pgdb->select('occurrence_status, establishment_means');
        $this->pgdb->from('vicflora.vicflora_occurrence');
        $this->pgdb->where('taxon_id', $taxon);
        $this->pgdb->where('sub_code_7', $region);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $occ = array();
            $est = array();
            foreach ($query->result() as $row) {
                if ($row->occurrence_status && !in_array($row->occurrence_status, $occ)) {
                    $occ[] = $row->occurrence_status;
                }
                if ($row->establishment_means && !in_array($row->establishment_means, $est)) {
                    $est[] = $row->establishment_means;
                }
            }

            $occReg = null;
            if (in_array('present', $occ)) {
                $occReg = 'present';
            }
            elseif (in_array('extinct', $occ)) {
                $occReg = 'extinct';
            }
            elseif (in_array('doubtful', $occ)) {
                $occReg = 'doubtful';
            }

            $estReg = null;
            if (in_array('native', $est)) {
                $estReg = 'native';
            }
            elseif (in_array('naturalised', $est)) {
                $estReg = 'naturalised';
            }
            elseif (in_array('introduced', $est)) {
                $estReg = 'introduced';
            }
            elseif (in_array('cultivated', $est)) {
                $estReg = 'cultivated';
            }
            elseif (in_array('uncertain', $est)) {
                $estReg = 'uncertain';
            }

            $upd = array(
                'occurrence_status' => $occReg,
                'establishment_means' => $estReg,
                'timestamp_modified' => date('Y-m-d H:i:s'),
                'modified_by_id' => $this->session->userdata('id')
            );
            
            $this->pgdb->where('taxon_id', $taxon);
            $this->pgdb->where('locality_id', $region);
            $this->pgdb->where('locality_type', 'IBRA7');
            $this->pgdb->update('vicflora.vicflora_distribution', $upd);
            
            return (object) array(
                'region' => $region,
                'occurrenceStatus' => $occReg,
                'establishmentMeans' => $estReg
            );
        }
    }
    
}

/* End of file mapmodel.php */
/* Location: ./models/mapmodel.php */
