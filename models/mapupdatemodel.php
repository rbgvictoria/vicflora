<?php

require_once 'floramodel.php';

class MapUpdateModel extends FloraModel {
    public function __construct() {
        parent::__construct();
        $this->pgdb = $this->load->database('postgis', TRUE);
    }
    
    public function updateTaxa($from=FALSE) {
        $this->db->select('guid');
        $this->db->from('vicflora_taxon');
        $this->db->where_in('TaxonomicStatus', array('accepted', 'homotypic synonym', 'heterotypic synonym', 'synonym'));
        if ($from) {
            $this->db->where('TimestampModified >', $from);
        }
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $this->updateTaxon($row->guid);
            }
        }
    }
    
    public function updateTaxon($guid) {
        $row = $this->getTaxonData($guid);
        $data = array();
        switch ($row->establishmentMeans) {
            case 'native (naturalised in part(s) of state)':
                $data['establishment_means'] = 'native';
                break;
            case 'sparingly established':
                $data['establishment_means'] = 'introduced';
                break;
            default:
                $data['establishment_means'] = $row->establishmentMeans;
                break;
        }
        $data['occurrence_status'] = $row->occurrenceStatus;
        $data['accepted_name_usage_taxon_rank'] = $row->acceptedNameUsageTaxonRank;
        $data['accepted_name_usage'] = $row->acceptedNameUsage;
        $data['taxonomic_status'] = $row->taxonomicStatus;
        $data['scientific_name_authorship'] = $row->scientificNameAuthorship;
        $data['scientific_name'] = $row->scientificName;
        $data['parent_name_usage_id'] = $row->parentNameUsageID;
        $data['accepted_name_usage_id'] = $row->acceptedNameUsageID;
        $data['scientific_name_id'] = $row->scientificNameID;
        
        if ($row->taxonomicStatus == 'accepted') {
            $classification = $this->higherClassification($row->NodeNumber);
            $data = array_merge($data, (array) $classification);
            if ($row->AcceptedRankID == 220) {
                $data['species_id'] = $guid;
            }
            elseif ($row->AcceptedRankID > 220) {
                $data['species_id'] = $this->getSpeciesID($guid);
            }
        }

        $this->pgdb->select('id');
        $this->pgdb->from('vicflora.vicflora_taxon');
        $this->pgdb->where('id', $guid);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $this->updateVicFloraTaxon($guid, $data);
        }
        else {
            $data['id'] = $guid;
            $this->insertVicFloraTaxon($data);
            if ($row->AcceptedRankID >= 220) {
                $this->nameMatch($row->scientificNameID, $row->scientificName, $row->scientificNameAuthorship);
            }
        }
    }
    
    private function nameMatch($scientificNameID, $scientificName, $scientificNameAuthorship) {
        if (!$this->findScientificNameID($scientificNameID)) {
            $scientificNameWithAuthor = $scientificName . ' ' . $scientificNameAuthorship;
            $exactMatch = $this->exactNameMatch($scientificNameWithAuthor);
            /*if ($exactMatch) {
                foreach ($exactMatch as $match) {
                    $this->updateNameMatchOccurrence($match->id, $scientificNameID, 'exactMatch');
                }
            }
            else {*/
                $canonicalNameMatch = $this->canonicalNameMatch($scientificName);
                if ($canonicalNameMatch) {
                    foreach ($canonicalNameMatch as $match) {
                        $this->updateNameMatchOccurrence($match->id, $scientificNameID, 'canonicalNameMatch');
                    }
                }
            //}
        }
    }
    
    private function updateNameMatchOccurrence($alaParsedNameID, $vicfloraScientificNameID, $matchType) {
        $this->pgdb->where('id', $alaParsedNameID);
        $this->pgdb->update('vicflora.ala_parsed_name', array(
            'name_match_type' => $matchType,
            'vicflora_scientific_name_id' => $vicfloraScientificNameID
        ));
        $this->pgdb->where('ala_parsed_name_id', $alaParsedNameID);
        $this->pgdb->update('vicflora.avh_occurrence', array('vicflora_scientific_name_id' => $vicfloraScientificNameID));
    }
    
    private function exactNameMatch($scientificName) {
        $this->pgdb->select('id');
        $this->pgdb->from('vicflora.ala_parsed_name');
        $this->pgdb->where('scientific_name', $scientificName);
        $this->pgdb->or_where('canonical_name_complete', $scientificName);
        $query = $this->pgdb->get();
        return $query->result();
    }
    
    private function canonicalNameMatch($scientificName) {
        $this->pgdb->select('id');
        $this->pgdb->from('vicflora.ala_parsed_name');
        $this->pgdb->where('canonical_name_with_marker', $scientificName);
        $this->pgdb->or_where('canonical_name', $scientificName);
        $this->pgdb->or_where('canonical_name', str_replace('×', '', $scientificName));
        $query = $this->pgdb->get();
        return $query->result();
    }
    
    private function findScientificNameID($scientificNameID) {
        $this->pgdb->select('id');
        $this->pgdb->from('vicflora.ala_parsed_name');
        $this->pgdb->where('vicflora_scientific_name_id', $scientificNameID);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    private function updateVicFloraTaxon($guid, $data) {
        $this->pgdb->where('id', $guid);
        $this->pgdb->update('vicflora.vicflora_taxon', $data);
    }
    
    private function insertVicFloraTaxon($data) {
        $this->pgdb->insert('vicflora.vicflora_taxon', $data);
    }
}


/* End of file mapupdatemodel.php */
/* Location: ./models/mapupdatemodel.php */
