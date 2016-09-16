<?php
require_once 'floramodel.php';

class MapModel extends FloraModel {
    var $pgdb;
    
    public function __construct() {
        parent::__construct();
        $this->pgdb = $this->load->database('postgis', TRUE);
    }
    
    public function checkOccurrences($guid, $rank) {
        $this->pgdb->select('count(*) AS num', FALSE);
        $this->pgdb->from('vicflora.vicflora_occurrence');
        if ($rank == 220)
            $this->pgdb->where('speciesGUID', $guid);
        else
            $this->pgdb->where('taxonID', $guid);
        $query = $this->db->get();
        $row = $query->row();
        return $row->num;
    }
    
    public function getOccurrence($uuid) {
        $sql = "SELECT uuid, taxon_id, scientific_name, catalog_number, decimal_longitude, decimal_latitude, 
                establishment_means, occurrence_status, sub_name_7
            FROM vicflora.occurrence_view
            WHERE uuid='$uuid'";
        $query = $this->pgdb->query($sql);
        return $query->result();
    }
    
    public function getBioregionForOccurrence($uuid) {
        $sql = "SELECT taxon_id, sub_code_7
            FROM vicflora.occurrence_view
            WHERE uuid='$uuid'";
        $query = $this->pgdb->query($sql);
        if ($query->num_rows()) {
            $row = $query->row();
            $sql = "SELECT taxon_id, sub_code_7, occurrence_status, establishment_means
                FROM vicflora.distribution_bioregion_view
                WHERE taxon_id='$row->taxon_id' AND sub_code_7='$row->sub_code_7'";
            $qry = $this->pgdb->query($sql);
            return $qry->row();
        }
    }
    
    public function getBioregions() {
        $this->pgdb->select('b.sub_code_7, b.sub_name_7 AS vic_bioregion, i.sub_name_7 AS ibra_subregion');
        $this->pgdb->from('vicflora.vicflora_bioregion b');
        $this->pgdb->join('vicflora.ibra7_subregions i', 'b.sub_code_7=i.sub_code_7');
        $this->pgdb->order_by('b.sub_code_7');
        $query = $this->pgdb->get();
        return $query->result_array();
    }
    
    public function getBioregionInfo($name) {
        $this->db->select('DepiCode, Name, Description, FovNaturalRegion');
        $this->db->from('vicflora_bioregion');
        $this->db->where('Name', $name);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        }
        else {
            return array();
        }
    }
    
    
    public function getOccurrenceRecords($guid) {
        $ret = array();
        
        $namedata = $this->getNameData($guid);
        
        $this->pgdb->select('uuid, scientific_name, taxon_id, catalog_number, 
                decimal_longitude, decimal_latitude, geom, genus, species, establishment_means, 
                data_source, sensitive, data_generalised AS generalised, scientific_name, unprocessed_scientific_name');
        $this->pgdb->from('vicflora.avh_occurrence');
        if ($this->input->get_post('ala_scientific_name')) {
            $this->pgdb->where('scientific_name', urldecode($this->input->get_post('ala_scientific_name')));
        }
        if ($this->input->get_post('ala_unprocessed_scientific_name')) {
            $providedNames = preg_split('/\n|\r/', urldecode($this->input->get_post('ala_unprocessed_scientific_name')), -1, PREG_SPLIT_NO_EMPTY);
            $this->pgdb->where_in('unprocessed_scientific_name', $providedNames);
        }
        else {
            $this->pgdb->where('scientific_name', $namedata->scientificName);
        }
        $this->pgdb->where('decimal_latitude IS NOT NULL');
        $this->pgdb->where('decimal_longitude IS NOT NULL');
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $item = $row;
                unset($item['scientific_name']);
                unset($item['unprocessed_scientific_name']);
                $item['scientific_name'] = $namedata->scientificName;
                $item['ala_scientific_name'] = $row['scientific_name'];
                $item['ala_unprocessed_scientific_name'] = $row['unprocessed_scientific_name'];
                $item['taxon_id'] = $guid;
                $item['ala_taxon_id'] = $row['taxon_id'];
                $item['species'] = $namedata->species;
                $item['species_guid'] = $namedata->speciesGUID;
                $item['genus'] = $namedata->genus;
                $item['genus_guid'] = $namedata->genusGUID;
                
                $ibra = $this->getBioregion($row['geom']);
                $item = array_merge($item, (array) $ibra);
                $item['nrm_region'] = $this->getNrmRegion($row['geom']);
                
                if ($item['establishment_means']) {
                    $item['establishment_means_source'] = 'AVH';
                }
                else {
                    if ($item['data_source'] === 'AVH') {
                        $item['establishment_means_source'] = 'taxon';
                        switch ($namedata->establishmentMeans) {
                            case 'native (naturalised in part(s) of state)':
                                $item['establishment_means'] = 'native';
                                break;
                            case 'sparingly established':
                                $item['establishment_means'] = 'introduced';
                                break;
                            default:
                                $item['establishment_means'] = $namedata->establishmentMeans;
                                break;
                        };
                    }
                    else {
                        $item['establishment_means_source'] = null;
                    }
                }
                $item['occurrence_status'] = 'present';
                
                //array_multisort(array_keys($item), SORT_ASC, $item);
                $ret[] = (object) $item;
            }
            
            if ($this->input->get_post('ala_scientific_name')) {
                $this->insertNameMatch($guid, urldecode($this->input->get_post('ala_scientific_name')));
            }
            elseif ($this->input->get_post('ala_unprocessed_scientific_name')) {
                foreach ($providedNames as $name) {
                    $this->insertNameMatch($guid, FALSE, $name);
                }
            }
            
        }
        return $ret;
    }
    
    private function insertNameMatch($guid, $alaName=FALSE, $alaUnprocessedName=FALSE) {
        $ins = array(
            'TaxonID' => $guid,
            'AlaScientificName' => ($alaName) ? $alaName : NULL,
            'AlaProvidedName' => ($alaUnprocessedName) ? $alaUnprocessedName : NULL,
        );
        $this->db->insert('vicflora_ala_name_match', $ins);
    }
    
    public function getVicFloraOccurrence($uuid) {
        $this->pgdb->select('fid');
        $this->pgdb->from('vicflora.vicflora_occurrence');
        $this->pgdb->where('uuid', $uuid);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->fid;
        }
        else {
            return FALSE;
        }
    }
    
    public function updateVicFloraOccurrence($rec, $fid) {
        $upd = (array) $rec;
        $upd['timestamp_modified'] = date('Y-m-d H:i:s');
        $upd['user_id'] = $this->session->userdata('id');
        $this->pgdb->where('fid', $fid);
        $this->pgdb->update('vicflora.vicflora_occurrence', $upd);
    }
    
    public function createVicFloraOccurrence($rec) {
        $insert = (array) $rec;
        $insert['timestamp_modified'] = date('Y-m-d H:i:s');
        $insert['user_id'] = $this->session->userdata('id');
        $this->pgdb->insert('vicflora.vicflora_occurrence', $insert);
    }
    
    public function createVicFloraDistribution($guid) {
        $this->pgdb->select('sub_code_7');
        $this->pgdb->from('vicflora.vicflora_occurrence');
        $this->pgdb->where('taxon_id', $guid);
        $this->pgdb->group_by('sub_code_7');
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $distributionID = $this->getVicFloraDistributionRecord($guid, $row->sub_code_7);
                if ($distributionID) {
                    $this->updateDistributionRecord($distributionID, $guid, $row->sub_code_7);
                }
                else {
                    $this->createVicFloraDistributionRecord($guid, $row->sub_code_7);
                }
            }
        }
    }
    
    public function updateVicFloraDistribution($guid) {
        $distribution = $this->getDistribution($guid);
        $subregions = array();
        $newDistribution = $this->getDistributionFromOccurrences($guid);
        if ($newDistribution) {
            foreach ($newDistribution as $row) {
                $subregions[] = $row->sub_code_7;
                $distributionID = $this->getVicFloraDistributionRecord($guid, $row->sub_code_7);
                if ($distributionID) {
                    $this->updateDistributionRecord($distributionID, $guid, $row->sub_code_7);
                }
                else {
                    $this->createVicFloraDistributionRecord($guid, $row->sub_code_7);
                }
            }
        }
        if ($distribution) {
            foreach ($distribution as $row) {
                if (!in_array($row->locality_id, $subregions)) {
                    $this->deleteDistributionRecord($row->distribution_id);
                }
            }
        }
    }
    
    private function getDistributionFromOccurrences($guid) {
        $this->pgdb->select('sub_code_7');
        $this->pgdb->from('vicflora.vicflora_occurrence');
        $this->pgdb->where('taxon_id', $guid);
        $this->pgdb->group_by('sub_code_7');
        $query = $this->pgdb->get();
        return $query->result();
    }
    
    public function getDistribution($guid) {
        $this->pgdb->select('distribution_id, locality_id');
        $this->pgdb->from('vicflora.vicflora_distribution');
        $this->pgdb->where('taxon_id', $guid);
        $this->pgdb->where('locality_type', 'IBRA7');
        $query = $this->pgdb->get();
        return $query->result();
    }
    
    public function getDistributionDetail($guid) {
        $this->pgdb->select('sub_code_7, sub_name_7, depi_code, occurrence_status, establishment_means');
        $this->pgdb->from('vicflora.distribution_bioregion_view');
        $this->pgdb->where('taxon_id', $guid);
        $this->pgdb->where_not_in('occurrence_status', array('absent', 'doubtful'));
        $this->pgdb->order_by('depi_order');
        $query = $this->pgdb->get();
        return $query->result_array();
    }
    
    private function deleteDistributionRecord($distributionID) {
        $this->pgdb->where('distribution_id', $distributionID);
        $this->pgdb->delete('vicflora.vicflora_distribution');
    }
    
    private function getVicFloraDistributionRecord($guid, $region) {
        $this->pgdb->select('distribution_id');
        $this->pgdb->from('vicflora.vicflora_distribution');
        $this->pgdb->where('taxon_id', $guid);
        $this->pgdb->where('locality_type', 'IBRA7');
        $this->pgdb->where('locality_id', $region);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->distribution_id;
        }
        else {
            return FALSE;
        }
    }
    
    public function updateDistributionStatus($taxonid, $sub, $term, $value) {
        $sql = "SELECT $term as tval FROM vicflora.vicflora_taxon WHERE id='$taxonid'";
        $qry = $this->pgdb->query($sql);
        $taxonValue = FALSE;
        if ($qry->num_rows()) {
            $rec = $qry->row();
            $taxonValue = $rec->tval;
        }
        
        $assertionType = ($term == 'establishment_means') ? 'establishmentMeans' : 'occurrenceStatus';
        $this->pgdb->select("uuid, $term AS term_value, {$term}_source AS term_source");
        $this->pgdb->from('vicflora.occurrence_view');
        $this->pgdb->where("(taxon_id='$taxonid' OR species_id='$taxonid')", FALSE, FALSE);
        $this->pgdb->where('sub_code_7', $sub);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                if ($row->term_source != 'avh') {
                    if ($value == $taxonValue) {
                        $this->deleteVicFloraAssertion($row->uuid, $assertionType);
                    }
                    else {
                        $this->insertVicFloraAssertion($row->uuid, $assertionType, $value, 'region');
                    }
                }
            }
        }
        
    }
    
    /*public function updateDistributionStatus($taxonid, $sub, $occ, $est) {
        $updArr = array(
            'occurrence_status' => $occ,
            'establishment_means' => $est,
            'timestamp_modified' => date('Y-m-d H:i:s'),
            'modified_by_id' => $this->session->userdata('id')
        );
        
        $this->pgdb->where('taxon_id', $taxonid);
        $this->pgdb->where('locality_id', $sub);
        $this->pgdb->update('vicflora.vicflora_distribution', $updArr);
        
        $occurrences = $this->getOccurrencesForRegion($taxonid, $sub);
        if ($occurrences) {
            foreach ($occurrences as $occurrence) {
                if (!in_array($occurrence->establishment_means_source, array('AVH', 'VicFlora'))) {
                    $user = array_pop($updArr);
                    $updArr['user_id'] = $user;
                    $this->pgdb->where('uuid', $occurrence->uuid);
                    $this->pgdb->update('vicflora.vicflora_occurrence', $updArr);
                    
                    if ($occ != $occurrence->occurrence_status) {
                        $this->insertVicFloraAssertion($occurrence->uuid, 'occurrenceStatus', $occ);
                    }
                    if ($est != $occurrence->establishment_means) {
                        $this->insertVicFloraAssertion($occurrence->uuid, 'establishmentMeans', $est);
                    }
                }
            }
        }
    }*/
    
    private function getOccurrencesForRegion($taxonid, $region) {
        $this->pgdb->select("uuid, CASE WHEN occurrence_status IS NUll THEN 'present'
            WHEN occurrence_status='' THEN 'present'
            ELSE occurrence_status END AS occurrence_status,
            establishment_means, establishment_means_source", FALSE);
        $this->pgdb->from('vicflora.vicflora_occurrence');
        $this->pgdb->where('taxon_id', $taxonid);
        $this->pgdb->where('sub_code_7', $region);
        $query = $this->pgdb->get();
        return $query->result();
    }

    private function updateDistributionRecord($distributionID, $guid, $region) {
        $update = array(
            'occurrence_status' => $this->getRegionOccurrenceStatus($guid, $region),
            'establishment_means' => $this->getRegionEstablishmentMeans($guid, $region),
            'timestamp_modified' => date('Y-m-d H:i:s'),
            'modified_by_id' => (!$this->input->is_cli_request()) ? $this->session->userdata('id') : NULL
        );
        $this->pgdb->where('distribution_id', $distributionID);
        $this->pgdb->update('vicflora.vicflora_distribution', $update);
    }
    
    private function createVicFloraDistributionRecord($guid, $region) {
        $insert = array(
            'taxon_id' => $guid,
            'locality_type' => 'IBRA7',
            'locality_id' => $region,
            'occurrence_status' => $this->getRegionOccurrenceStatus($guid, $region),
            'establishment_means' => $this->getRegionEstablishmentMeans($guid, $region),
            'timestamp_created' => date('Y-m-d H:i:s'),
            'timestamp_modified' => date('Y-m-d H:i:s'),
            'modified_by_id' => (!$this->input->is_cli_request()) ? $this->session->userdata('id') : NULL
        );
        $this->pgdb->insert('vicflora.vicflora_distribution', $insert);
    }
    
    private function getRegionOccurrenceStatus($guid, $region) {
        $occ = NULL;
        $this->pgdb->select("CASE WHEN occurrence_status IS NULL THEN 'present' 
            WHEN occurrence_status='' THEN 'present' ELSE occurrence_status END
            AS occurrence_status", FALSE);
        $this->pgdb->from('vicflora.vicflora_occurrence');
        $this->pgdb->where('taxon_id', $guid);
        $this->pgdb->where('sub_code_7', $region);
        $this->pgdb->group_by('occurrence_status');
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $values = array();
            foreach ($query->result() as $row) {
                $values[] = $row->occurrence_status;
            }
            if (in_array('present', $values)) {
                $occ = 'present';
            }
            elseif (in_array('extinct', $values)) {
                $occ = 'extinct';
            }
            elseif (in_array('doubtful', $values)) {
                $occ = 'doubtful';
            }
        }
        return $occ;
    }
    
    private function getRegionEstablishmentMeans($guid, $region) {
        $est = null;
        $this->pgdb->select('establishment_means');
        $this->pgdb->from('vicflora.vicflora_occurrence');
        $this->pgdb->where('taxon_id', $guid);
        $this->pgdb->where('sub_code_7', $region);
        $this->pgdb->group_by('establishment_means');
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $values = array();
            foreach ($query->result() as $row) {
                $values[] = $row->establishment_means;
            }
            if (in_array('native', $values)) {
                $est = 'native';
            }
            elseif (in_array('naturalised', $values)) {
                $est = 'naturalised';
            }
            elseif (in_array('introduced', $values)) {
                $est = 'introduced';
            }
            elseif (in_array('cultivated', $values)) {
                $est = 'cultivated';
            }
            elseif (in_array('uncertain', $values)) {
                $est = 'uncertain';
            }
        }
        return $est;
    }
    
    public function insertVicFloraAssertion($uuid, $term, $value, $reason=NULL) {
        $this->deleteVicFloraAssertion($uuid, $term);
        
        $ins = array(
            'uuid' => $uuid,
            'term' => $term,
            'asserted_value' => $value,
            'timestamp_modified' => date('Y-m-d H:i:s'),
            'user_id' => $this->session->userdata('id'),
            'reason' => $reason
        );
        $this->pgdb->insert('vicflora.vicflora_assertion', $ins);
    }
    
    public function deleteVicFloraAssertion($uuid, $term) {
        $this->pgdb->where('uuid', $uuid);
        $this->pgdb->where('term', $term);
        $this->pgdb->delete('vicflora.vicflora_assertion');
    }
    
    private function getNameData($guid) {
        $namedata = array();
        $namedata['taxonID'] = $guid;
        $this->db->select('n.FullName, t.RankID, t.EstablishmentMeans, tt.NodeNumber');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        
        if ($query->num_rows()) {
            $row = $query->row();
            $namedata['scientificName'] = $row->FullName;
            $namedata['establishmentMeans'] = $row->EstablishmentMeans;
            
            $this->db->select('t.RankID, t.GUID, n.FullName');
            $this->db->from('vicflora_taxon t');
            $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
            $this->db->join('vicflora_name n', 't.NameID=n.NameID');
            $this->db->where('tt.NodeNumber <=', $row->NodeNumber);
            $this->db->where('tt.HighestDescendantNodeNumber >=', $row->NodeNumber);
            $this->db->where_in('t.RankID', array(180, 220));
            $query = $this->db->get();
            
            foreach ($query->result() as $row) {
                switch ($row->RankID) {
                    case 220:
                        $namedata['speciesGUID'] = $row->GUID;
                        $namedata['species'] = $row->FullName;
                        break;

                    case 180:
                        $namedata['genusGUID'] = $row->GUID;
                        $namedata['genus'] = $row->FullName;
                        break;

                    default:
                        break;
                }
            }
        }
        return (object) $namedata;
    }
    
    public function getBioregion($geom) {
        $ibra = (object) array(
            'reg_code_7' => null,
            'reg_name_7' => null,
            'sub_code_7' => null,
            'sub_name_7' => null
        );
        $this->pgdb->select('reg_code_7, reg_name_7, sub_code_7, sub_name_7');
        $this->pgdb->from('vicflora.ibra7_subregions');
        $this->pgdb->where("ST_Intersects(wkb_geometry, '$geom'::geometry)", FALSE, FALSE);
        $query = $this->pgdb->get();
        
        if ($query->num_rows()) {
            $ibra = $query->row();
        }
        return $ibra;
    }
    
    public function getNrmRegion($geom) {
        $nrm = NULL;
        $this->pgdb->select('nrm_region');
        $this->pgdb->from('vicflora.vicflora_nrm2014');
        $this->pgdb->where("ST_Intersects(geom, '$geom'::geometry)", FALSE, FALSE);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $nrm = $row->nrm_region;
        }
        return $nrm;
    }
    
    public function getLastUpdatedDate() {
        $this->pgdb->select('date(max(timestamp_modified)) as date_last_modified', FALSE);
        $this->pgdb->from('vicflora.avh_occurrence');
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->date_last_modified;
        }
    }
    
    public function loadAvhData($json, $intro=false, $dataSource='AVH') {
        $data = json_decode($json);
        $occurrences = $data->occurrences;
        if ($occurrences) {
            foreach ($occurrences as $occurrence) {
                $latitude = (isset($occurrence->decimalLatitude) && $occurrence->decimalLatitude) ? $occurrence->decimalLatitude : NULL;
                $longitude = (isset($occurrence->decimalLongitude) && $occurrence->decimalLongitude) ? $occurrence->decimalLongitude : NULL;
                if ($longitude && $latitude) {
                    $scientificName = (isset($occurrence->scientificName) && $occurrence->scientificName) ? $this->pgdb->escape($occurrence->scientificName) : 'NULL';
                    $rawscientificName = (isset($occurrence->raw_scientificName) && $occurrence->raw_scientificName) ? $this->pgdb->escape($occurrence->raw_scientificName) : 'NULL';
                    $taxonID = (isset($occurrence->taxonConceptID) && $occurrence->taxonConceptID) ? $this->pgdb->escape($occurrence->taxonConceptID) : 'NULL';
                    $speciesGUID = (isset($occurrence->speciesGuid) && $occurrence->speciesGuid) ? $this->pgdb->escape($occurrence->speciesGuid) : 'NULL';
                    $genusGUID = (isset($occurrence->genusGuid) && $occurrence->genusGuid) ? $this->pgdb->escape($occurrence->genusGuid) : 'NULL';
                    $taxonRank = (isset($occurrence->taxonRank)) ? $this->pgdb->escape($occurrence->taxonRank) : 'NULL';
                    $kingdom = (isset($occurrence->kingdom)) ? $this->pgdb->escape($occurrence->kingdom) : 'NULL';
                    $phylum = (isset($occurrence->phylum)) ? $this->pgdb->escape($occurrence->phylum) : 'NULL';
                    $class = (isset($occurrence->classs)) ? $this->pgdb->escape($occurrence->classs) : 'NULL';
                    $order = (isset($occurrence->order)) ? $this->pgdb->escape($occurrence->order) : 'NULL';
                    $family = (isset($occurrence->family)) ? $this->pgdb->escape($occurrence->family) : 'NULL';
                    $genus = (isset($occurrence->genus)) ? $this->pgdb->escape($occurrence->genus) : 'NULL';
                    $species = (isset($occurrence->species)) ? $this->pgdb->escape($occurrence->species) : 'NULL';
                    $geom = ($longitude && $latitude && $latitude != 'NULL' && $longitude != 'NULL') ? "ST_GeomFromText('POINT($longitude $latitude)', 4326)" : 'NULL';
                    $establishment_means = ($intro) ? $this->pgdb->escape($intro) : 'NULL';
                    $sensitive = (isset($occurrence->sensitive) && $occurrence->sensitive) ? $this->pgdb->escape($occurrence->sensitive) : 'NULL';
                    $generalised = (isset($occurrence->assertions) && in_array('dataAreGeneralised', $occurrence->assertions)) ? 'TRUE' : 'NULL';
                    
                    $occurrenceID = $this->findAvhRecord($occurrence->uuid);
                    if ($occurrenceID) {
                        $update = "UPDATE vicflora.avh_occurrence
                            SET scientific_name=$scientificName, 
                              taxon_id=$taxonID, 
                              species_guid=$speciesGUID, 
                              genus_guid=$genusGUID, 
                              catalog_number='$occurrence->raw_catalogNumber', 
                              decimal_latitude=$latitude, 
                              decimal_longitude=$longitude, 
                              geom=$geom, 
                              unprocessed_scientific_name=$rawscientificName,
                              taxon_rank=$taxonRank, 
                              kingdom=$kingdom, 
                              phylum=$phylum, class=$class, 
                              \"order\"=$order, 
                              family=$family, 
                              genus=$genus, 
                              species=$species, 
                              data_source='$dataSource', 
                              sensitive=$sensitive, 
                              data_generalised=$generalised, 
                              timestamp_modified=NOW()
                            WHERE occurrence_id=$occurrenceID";
                        $this->pgdb->query($update);
                    }
                    else {
                        $insert = "INSERT INTO vicflora.avh_occurrence (uuid, scientific_name, taxon_id, species_guid, 
                            genus_guid, catalog_number, decimal_latitude, decimal_longitude, geom, unprocessed scientific_name,
                            taxon_rank, kingdom, phylum, class, \"order\", family, genus, species, establishment_means, data_source, 
                            sensitive, data_generalised, timestamp_modified)
                            VALUES ('$occurrence->uuid', $scientificName, $taxonID, $speciesGUID, $genusGUID, '$occurrence->raw_catalogNumber', 
                                $latitude, $longitude, $geom, $rawscientificName, $taxonRank, $kingdom, $phylum, $class, $order,
                                $family, $genus, $species, $establishment_means, 'AVH', $sensitive, $generalised, NOW())";
                        $this->pgdb->query($insert);
                    }
                    
                    $occurrence->establishment_means = $establishment_means;
                }
            }
        }
        return $data->totalRecords;
    }
    
    private function compareVicFloraOccurrence($occurrence) {
        $this->pgdb->select('fid, uuid, scientific_name, decimal_latitude, decimal_longitude, establishment_means, establishment_means_source');
        $this->pgdb->from('vicflora.vicflora_occurrence');
        $this->pgdb->where('uuid', $occurrence->uuid);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $row = $query->row();
            if ($occurrence->scientific_name != $row->scientific_name
                || $occurrence->decimal_longitude != $row->decimal_longitude
                || $occurrence->decimal_latitude != $row->decimal_latitude
                || ($occurrence->establishment_means && $occurrence->establishment_means != $row->establishment_means)) {
                
                
                
            }
        }
        else {
            $this->updateVicFloraOccurrence($occurrence);
        }
    }
    
    private function findAvhRecord($uuid) {
        $this->pgdb->select('occurrence_id');
        $this->pgdb->from('vicflora.avh_occurrence');
        $this->pgdb->where('uuid', $uuid);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->occurrence_id;
        }
        else {
            return FALSE;
        }
    }
    
    public function getMapUpdates($taxonID=FALSE) {
        $this->pgdb->select('date(timestamp_modified) as date_updated, taxon_id, 
            scientific_name, fid, uuid, decimal_latitude, catalog_number, 
            decimal_longitude, sub_name_7, occurrence_status, establishment_means,
            is_updated_record, is_new_record', FALSE);
        $this->pgdb->from('vicflora.vicflora_occurrence');
        $this->pgdb->where('(is_updated_record=1 OR is_new_record=1)', FALSE, FALSE);
        if ($taxonID) {
            $this->pgdb->where('taxon_id', $taxonID);
        }
        else {
            $this->pgdb->where('species_guid IS NOT NULL', FALSE, FALSE);
        }
        $this->pgdb->order_by('date_updated');
        $this->pgdb->order_by('scientific_name');
        $query = $this->pgdb->get();
        return $query->result_array();
    }
    
    public function getAlaNameOverview($taxonID) {
        $this->pgdb->select("t.accepted_name_usage AS scientific_name, o.scientific_name AS ala_scientific_name, 
            o.taxon_id AS ala_taxon_id, o.unprocessed_scientific_name AS ala_unprocessed_scientific_name, 
            count(CASE WHEN data_source='AVH' THEN 1 ELSE NULL END) as count_avh,
            count(CASE WHEN data_source='VBA' THEN 1 ELSE NULL END) as count_vba", FALSE);
        $this->pgdb->from('vicflora.avh_occurrence o');
        $this->pgdb->join('vicflora.vicflora_taxon t', 'o.vicflora_scientific_name_id=t.scientific_name_id');
        $this->pgdb->where('t.accepted_name_usage_id', $taxonID);
        $this->pgdb->group_by('t.accepted_name_usage');
        $this->pgdb->group_by('o.scientific_name');
        $this->pgdb->group_by('o.taxon_id');
        $this->pgdb->group_by('o.unprocessed_scientific_name');
        $query = $this->pgdb->get();
        return $query->result_array();
    }
    
    public function getAlaMatchedNames() {
        $this->pgdb->select("scientific_name, taxon_id, ala_scientific_name, ala_taxon_id, ala_unprocessed_scientific_name, 
            count(CASE WHEN data_source='AVH' THEN 1 ELSE NULL END) as count_avh,
            count(CASE WHEN data_source='VBA' THEN 1 ELSE NULL END) as count_vba", FALSE);
        $this->pgdb->from('vicflora.vicflora_occurrence');
        $this->pgdb->where('scientific_name!=ala_scientific_name', FALSE, FALSE);
        $this->pgdb->group_by('scientific_name');
        $this->pgdb->group_by('taxon_id');
        $this->pgdb->group_by('ala_scientific_name');
        $this->pgdb->group_by('ala_taxon_id');
        $this->pgdb->group_by('ala_unprocessed_scientific_name');
        $query = $this->pgdb->get();
        return $query->result_array();
    }
    
    public function getUpdatedTaxa($startTime=FALSE, $endTime=FALSE) {
        $this->pgdb->select('taxon_id');
        $this->pgdb->from('vicflora.vicflora_occurrence');
        $this->pgdb->where('species IS NOT NULL', FALSE, FALSE);
        if ($startTime) {
            $this->pgdb->where('timestamp_modified >=', $startTime);
            if ($endTime) {
                $this->pgdb->where('timestamp_modified <=', $endTime);
            }
        }
        else {
            $this->pgdb->where('(is_new_record=1 OR is_updated_record=1)', FALSE, FALSE);
        }
        $this->pgdb->group_by('taxon_id');
        $query = $this->pgdb->get();
        return $query->result_array();
    }
    
    public function getAssertions($guid) {
        $this->pgdb->select("a.uuid, v.data_source, a.term, a.asserted_value, a.timestamp_modified, 
            CASE WHEN a.term='scientificName' THEN v.ala_scientific_name ELSE NULL END AS ala_scientific_name,
            CASE WHEN a.term='scientificName' THEN v.ala_unprocessed_scientific_name ELSE NULL END AS ala_unprocessed_scientific_name",
            FALSE);
        $this->pgdb->from('vicflora.vicflora_assertion a');
        $this->pgdb->join('vicflora.vicflora_occurrence v', 'a.uuid=v.uuid');
        $this->pgdb->where('v.taxon_id', $guid);
        $this->pgdb->where("(a.term!='scientificName' OR v.scientific_name!=v.ala_scientific_name)", FALSE, FALSE);
        $query = $this->pgdb->get();
        return $query->result_array();
    }
    
    public function acceptMapUpdates($taxonID=FALSE) {
        if ($taxonID) {
            $this->pgdb->where('taxon_id', $taxonID);
        }
        $this->pgdb->where('(is_new_record=1 OR is_updated_record=1)');
        $update = array(
            'is_new_record' => NULL,
            'is_updated_record' => NULL
        );
        $this->pgdb->update('vicflora.vicflora_occurrence', $update);
    }
    
    public function getStateDistribution($guid) {
        $this->pgdb->select('d.state_province, s.state_abbr');
        $this->pgdb->from('vicflora.vicflora_statedistribution d');
        $this->pgdb->join('vicflora.australia_states s', 'd.state_province=s.state');
        $this->pgdb->where('d.taxon_id', $guid);
        $this->pgdb->order_by('s.state_order');
        $query = $this->pgdb->get();
        return $query->result_array();
    }
    
    public function findStateDistributionRecord($taxonId, $state) {
        $where = array(
            'taxon_id' => $taxonId,
            'state_province' => $state
        );
        $this->pgdb->select('statedistribution_id');
        $this->pgdb->from('vicflora.vicflora_statedistribution');
        $this->pgdb->where($where);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->statedistribution_id;
        }
        else {
            return FALSE;
        }
    }
    
    public function updateStateDistributionRecord($data, $id) {
        $this->pgdb->where('statedistribution_id', $id);
        $this->pgdb->update('vicflora.vicflora_statedistribution', $data);
    }
    
    public function insertStateDistributionRecord($data) {
        $this->pgdb->insert('vicflora.vicflora_statedistribution', $data);
    }
}

/* End of file mapmodel.php */
/* Location: ./models/mapmodel.php */
