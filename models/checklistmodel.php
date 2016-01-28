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
    
    public function getCheckListTaxa($park) {
        $sql = <<<EOT
SELECT o."taxon_id", o."scientific_name"
FROM vicflora.vicflora_capad c
JOIN vicflora.vicflora_occurrence o ON ST_Intersects(o.geom, c.geom)
WHERE c.res_number='$park'
GROUP BY o."taxon_id", o."scientific_name"
ORDER BY o."scientific_name"
EOT;
        $query = $this->pgdb->query($sql);
        if ($query->num_rows()) {
            $checklist = array();
            foreach ($query->result() as $row) {
                $checklist = array_merge($checklist, $this->getClassification($row->taxon_id));
            }
            
            $checklist = array_map("unserialize", array_unique(array_map("serialize", $checklist)));
            
            $classification = array();
            foreach ($checklist as $item) {
                $classification[] = $item['higherClassification'];
            }
            
            array_multisort($classification, SORT_ASC, $checklist);
            
            return $checklist;
            
        }
        else {
            return FALSE;
        }
    }
    
    private function getClassification($taxonid) {
        $ret = array();
        $this->db->select('NodeNumber');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('t.GUID', $taxonid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $nodenumber = $row->NodeNumber;
            
            $this->db->select('t.GUID, n.Name, n.FullName, n.Author, td.Name AS TaxonRank');
            $this->db->from('vicflora_taxon t');
            $this->db->join('vicflora_taxontreedefitem td', 't.TaxonTreeDefItemID=td.TaxonTreeDefItemID');
            $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
            $this->db->join('vicflora_name n', 't.NameID=n.NameID');
            $this->db->where('tt.NodeNumber <=', $nodenumber);
            $this->db->where('tt.HighestDescendantNodeNumber >=', $nodenumber);
            $this->db->where('td.RankID >=', 140);
            $this->db->order_by('tt.NodeNumber');
            $query = $this->db->get();
            if ($query->num_rows()) {
                $name = array();
                foreach ($query->result() as $row) {
                    $name[] = $row->Name;
                    $ret[] = array(
                        'taxonID' => $row->GUID,
                        'higherClassification' => implode('|', $name),
                        'scientificName' => $row->FullName,
                        'scientificNameAuthor' => $row->Author,
                        'taxonRank' => $row->TaxonRank
                    );
                }
            }
            
        }
        return $ret;
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
              CASE establishment_means_source WHEN 'AVH' THEN establishment_means  WHEN 'VicFlora' THEN establishment_means ELSE NULL END AS establishment_means,
              occurrence_status,
              sub_name_7
            FROM vicflora.vicflora_occurrence
            WHERE \"taxon_id\"='$taxonid'
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
