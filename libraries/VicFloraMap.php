<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class VicFloraMap {
    private $ci;
    
    private $db;
    private $pgdb;
    
    private $dataSource;
    private $pageSize;
    private $startIndex;
    private $from;
    private $recordCount;
    private $intro;
    
    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->helper('curl');
        $this->ci->load->helper('ala_ws');
        $this->ci->load->model('mapmodel');
        
        $this->db = $this->ci->load->database('default', TRUE);
        $this->pgdb = $this->ci->load->database('postgis', TRUE);
    }
    
    public function updateOccurrences($from, $pageSize=1000, $start=0, $dataSource='AVH') {
        $this->dataSource = $dataSource;
        if ($from == 'all' || $from == '*') {
            $startDate = '*';
        }
        else {
            $startDate = $from . 'T00:00:00Z';
        }
        
        $intro = array(
            NULL => '-(establishment_means:"not native" OR establishment_means:cultivated OR establishment_means:"presumably cultivated" OR establishment_means:"possibly cultivated" OR establishment_means:"doubtfully native")',
            'introduced' => '(establishment_means:"not native" OR establishment_means:"doubtfully native")',
            'cultivated' => '(establishment_means:cultivated OR establishment_means:"presumably cultivated" OR establishment_means:"possibly cultivated")'
        );
        $totalRecords = 0;
        foreach ($intro as $key=>$value) {
            $this->intro = $key;
            $startIndex = $start;
            $this->recordCount = $start + 1;
        
            while ($startIndex < $this->recordCount) {
                $result = $this->queryBiocache($startDate, $pageSize, $startIndex, $value);
                if ($result) {
                    $records = $this->loadOccurrences($result);
                    $this->loadVicFloraOccurrences($records);
                }
                $startIndex += $pageSize;
                if ($this->ci->input->is_cli_request()) {
                    if ($startIndex < $this->recordCount) {
                        echo $startIndex . ' of ' . $this->recordCount . PHP_EOL;
                    }
                    else {
                        echo $this->recordCount . ' of ' . $this->recordCount . PHP_EOL;
                    }
                }
            }
            $totalRecords += $this->recordCount;
        }
        return $totalRecords;
    }
    
    public function updateDistribution ($startTime=FALSE, $endTime=FALSE) {
        $taxa = $this->ci->mapmodel->getUpdatedTaxa($startTime, $endTime);
        if ($taxa) {
            foreach ($taxa as $taxon) {
                echo $taxon['taxon_id'] . PHP_EOL;
                $this->ci->mapmodel->updateVicFloraDistribution($taxon['taxon_id']);
            }
        }
    }
    
    private function queryBiocache($startDate, $pageSize, $startIndex, $establishmentMeans) {
        $params = array();
        $params['fq'] = array();
        $params['fq']['data_hub_uid'] = 'dh2';
        $params['fq']['state'] = 'Victoria';
        $params['fq']['latitude'] = '[* TO *]';
        $params['fq']['longitude'] = '[* TO *]';
        $params['fq']['-raw_identification_qualifier'] = '[* TO *]';
        $params['fq'][] = $establishmentMeans;
        $params['fq']['last_load_date'] = '[' . $startDate . ' TO *]';
        $params['fl'] = array(
            'id',
            'taxon_concept_lsid',
            'genus_guid',
            'species_guid',
            'rank',
            'genus',
            'species',
            'raw_taxon_name',
            'catalogue_number',
            'taxon_name',
            'latitude',
            'longitude',
            'kingdom',
            'phylum',
            'class',
            'order',
            'family'
        );
        $params['facet'] = 'off';
        $params['pageSize'] = $pageSize;
        $params['startIndex'] = $startIndex;

        $query = createQueryString('biocache_search', $params);
        $result = doCurl($query, FALSE, TRUE);
        return $result;
    }
    
    private function loadOccurrences($json) {
        $data = json_decode($json);
        $occurrences = $data->occurrences;
        $records = array();
        if ($occurrences) {
            foreach ($occurrences as $occurrence) {
                $latitude = (isset($occurrence->decimalLatitude) && $occurrence->decimalLatitude) ? $occurrence->decimalLatitude : NULL;
                $longitude = (isset($occurrence->decimalLongitude) && $occurrence->decimalLongitude) ? $occurrence->decimalLongitude : NULL;
                if (isset($occurrence->decimalLatitude) && $occurrence->decimalLatitude &&
                        isset($occurrence->decimalLongitude) && $occurrence->decimalLongitude) {
                    $rec = $this->createOccurrenceRecord($occurrence);
                    $occurrenceID = $this->findOccurrence($occurrence->uuid);
                    if ($occurrenceID) {
                        $this->pgdb->where('occurrence_id', $occurrenceID);
                        $this->pgdb->update('vicflora.avh_occurrence', $rec);
                        $rec['is_updated_record'] = TRUE;
                    }
                    else {
                        $this->pgdb->insert('vicflora.avh_occurrence', $rec);
                        $rec['is_new_record'] = TRUE;
                    }
                }
                $records[] = $rec;
            }
        }
        $this->recordCount = $data->totalRecords;
        return $records;
    }
    
    private function createOccurrenceRecord($occurrence) {
        $rec = array(
            'uuid' => $occurrence->uuid,
            'data_source' => $this->dataSource,
            'catalog_number' => $occurrence->raw_catalogNumber,
            'decimal_latitude' => $occurrence->decimalLatitude,
            'decimal_longitude' => $occurrence->decimalLongitude,
            'scientific_name' => (isset($occurrence->scientificName) && $occurrence->scientificName) ? $occurrence->scientificName : NULL,
            'unprocessed_scientific_name' => (isset($occurrence->raw_scientificName) && $occurrence->raw_scientificName) ? $occurrence->raw_scientificName : NULL,
            'taxon_id' => (isset($occurrence->taxonConceptID) && $occurrence->taxonConceptID) ? $occurrence->taxonConceptID : NULL,
            'species_guid' => (isset($occurrence->speciesGuid) && $occurrence->speciesGuid) ? $occurrence->speciesGuid : NULL,
            'genus_guid' => (isset($occurrence->genusGuid) && $occurrence->genusGuid) ? $occurrence->genusGuid : NULL,
            'taxon_rank' => (isset($occurrence->taxonRank)) ? $occurrence->taxonRank : NULL,
            'kingdom' => (isset($occurrence->kingdom)) ? $occurrence->kingdom : NULL,
            'phylum' => (isset($occurrence->phylum)) ? $occurrence->phylum : NULL,
            'class' => (isset($occurrence->classs)) ? $occurrence->classs : NULL,
            'order' => (isset($occurrence->order)) ? $occurrence->order : NULL,
            'family' => (isset($occurrence->family)) ? $occurrence->family : NULL,
            'genus' => (isset($occurrence->genus)) ? $occurrence->genus : NULL,
            'species' => (isset($occurrence->species)) ? $occurrence->species : NULL,
            'geom' => $this->createGeometry($occurrence->decimalLongitude, $occurrence->decimalLatitude),
            'establishment_means' => (isset($this->intro)) ? $this->intro : NULL,
            'sensitive' => (isset($occurrence->sensitive) && $occurrence->sensitive) ? $occurrence->sensitive : NULL,
            'data_generalised' => (isset($occurrence->assertions) && in_array('dataAreGeneralised', $occurrence->assertions)) ? TRUE : NULL,
            'timestamp_modified' => date('Y-m-d H:i:s')
        );
        return $rec;
    }
    
    private function createGeometry($longitude, $latitude, $srid=4326) {
        $query = $this->pgdb->query("SELECT ST_GeomFromText('POINT($longitude $latitude)', $srid) AS geom");
        $row = $query->row();
        return $row->geom;
    }

    private function findOccurrence($uuid) {
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
    
    private function loadVicFloraOccurrences($records) {
        foreach ($records as $rec) {
            $vrec = $this->createVicFloraOccurrenceRecord($rec);
            if (!$vrec) {
                continue;
            }
            
            if (isset($rec['is_new_record'])) {
                $this->insertVicFloraOccurrence($vrec);
            }
            elseif (isset($rec['is_updated_record'])) {
                $vic = $this->findVicFloraOccurrence($rec['uuid']);
                if ($vic) { // this occurrence is in VicFlora already
                    if ($vrec['scientific_name'] != $vic->scientific_name
                            || (isset($rec['ala_scientific_name']) && 
                                    (!$vic->ala_scientific_name || $rec['ala_scientific_name'] != $vic->ala_scientific_name))
                            || (isset($rec['ala_unprocessed_scientific_name']) && 
                                    (!$vic->ala_unprocessed_scientific_name || $rec['ala_unprocessed_scientific_name'] != $vic->ala_unprocessed_scientific_name))
                            || $rec['decimal_latitude'] != $rec['decimal_latitude']
                            || $rec['decimal_longitude'] != $rec['decimal_longitude']
                            || ($rec['establishment_means'] && $rec['establishment_means'] != $vic->establishment_means)
                            ) {
                        $this->updateVicFloraOccurrence($vrec, $vic->fid);
                    }
                }
                else { 
                    // this is a new occurrence for VicFlora
                    // (meaning it couldn't be matched before)
                    $vrec['is_updated_record'] = NULL;
                    $vrec['is_new_record'] = TRUE;
                    $this->insertVicFloraOccurrence($vrec);
                }
            }
        }
    }
    
    private function updateVicFloraOccurrence($rec, $fid) {
        $update = $rec;
        $update['timestamp_modified'] = date('Y-m-d H:i:s');
        $update['is_updated_record'] = TRUE;
        $this->pgdb->where('fid', $fid);
        $this->pgdb->update('vicflora.vicflora_occurrence', $update);
    }
    
    private function insertVicFloraOccurrence($rec) {
        $insert = $rec;
        $insert['timestamp_modified'] = date('Y-m-d H:i:s');
        $insert['is_new_record'] = TRUE;
        $this->pgdb->insert('vicflora.vicflora_occurrence', $insert);
    }
    
    private function createVicFloraOccurrenceRecord($rec) {
        $vrec = array();
        $vrec['uuid'] = $rec['uuid'];
        $vrec['catalog_number'] = $rec['catalog_number'];
        $vrec['data_source'] = $rec['data_source'];
        $name = $this->matchName($rec);
        if (!$name) {
            return FALSE;
        }
        $vrec = array_merge($vrec, $name);
        $vrec['decimal_latitude'] = $rec['decimal_latitude'];
        $vrec['decimal_longitude'] = $rec['decimal_longitude'];
        $vrec['geom'] = $rec['geom'];
        $vrec = array_merge($vrec, (array) $this->ci->mapmodel->getBioregion($rec['geom']));
        $vrec['nrm_region'] = $this->ci->mapmodel->getNrmRegion($rec['geom']);
        if ($this->dataSource = 'AVH') {
            if ($rec['establishment_means']) {
                $vrec['establishment_means'] = $rec['establishment_means'];
                $vrec['establishment_means_source'] = 'AVH';
            }
        }
        else {
            unset($vrec['establishment_means']);
            unset($vrec['occurrence_status']);
        }
        if (isset($rec['is_new_record'])) {
            $vrec['is_new_record'] = TRUE;
        }
        elseif (isset($rec['is_updated_record'])) {
            $vrec['is_updated_record'] = TRUE;
        }
        return $vrec;
    }
    
    private function matchName($rec) {
        $name = array();
        $taxonID = FALSE;
        $trec = $this->getTaxonId($rec['scientific_name']);
        if ($trec) {
            $taxonID = $trec->GUID;
        }
        if (!$taxonID) {
            $taxonID = $this->getTaxonIdMatched($rec['scientific_name']);
            if (!$taxonID) {
                $taxonID = $this->getTaxonIdMatched($rec['unprocessed_scientific_name'], FALSE);
                if (!$taxonID) { // name cannot be matched to name in VicFlora
                    return FALSE;
                }
            }
        }
        $name['ala_scientific_name'] = $rec['scientific_name'];
        $name['ala_unprocessed_scientific_name'] = $rec['unprocessed_scientific_name'];
        $nameData = $this->getNameData($taxonID);
        if (!$nameData) {
            return FALSE;
        }
        return array_merge($name, $nameData);
    }
    
    private function getNameData($taxonID) {
        $this->db->select('t.GUID, n.FullName, td.Name AS Rank, t.RankID, tt.NodeNumber, t.OccurrenceStatus, t.EstablishmentMeans');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxontreedefitem td', 't.TaxonTreeDefItemID=td.TaxonTreeDefItemID');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('t.GUID', $taxonID);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $name = array(
                'taxon_id' => $taxonID,
                'species_guid' => NULL,
                'genus_guid' => NULL,
                'scientific_name' => $row->FullName,
                'species' => NULL,
                'genus' => NULL,
                'occurrence_status' => $row->OccurrenceStatus,
            );
            switch ($row->EstablishmentMeans) {
                case 'native (naturalised in part(s) of state)':
                    $name['establishment_means'] = 'native';
                    break;
                case 'naturalised':
                case 'sparingly established':
                    $name['establishment_means'] = 'introduced';
                    break;
                default:
                    $name['establishment_means'] = $row->EstablishmentMeans;
                    break;
            }
            if ($row->RankID == 180) {
                $name['genus_guid'] = $taxonID;
                $name['genus'] = $row->FullName;
            }
            elseif ($row->RankID > 180) {
                $this->db->select('t.RankID, t.GUID, n.FullName');
                $this->db->from('vicflora_taxon t');
                $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
                $this->db->join('vicflora_name n', 't.NameID=n.NameID');
                $this->db->where('tt.NodeNumber <=', $row->NodeNumber);
                $this->db->where('tt.HighestDescendantNodeNumber >=', $row->NodeNumber);
                $query = $this->db->get();
                if ($query->num_rows()) {
                    foreach ($query->result() as $row) {
                        if ($row->RankID == 180) {
                            $name['genus_guid'] = $row->GUID;
                            $name['genus'] = $row->FullName;
                        }
                        elseif ($row->RankID == 220) {
                            $name['species_guid'] = $row->GUID;
                            $name['species'] = $row->FullName;
                        }
                    }
                }
            }
            return $name;
        }
        else {
            return array();
        }
    }
    
    
    private function getTaxonId($sciName) {
        $this->db->select('coalesce(at.GUID, t.GUID) AS GUID, n.FullName, an.FullName, t.TaxonomicStatus', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxon at', 't.AcceptedID=at.TaxonID', 'left');
        $this->db->join('vicflora_name an', 'at.NameID=an.NameID', 'left');
        $this->db->where('n.FullName', $sciName);
        $this->db->where("coalesce(at.TaxonomicStatus, t.TaxonomicStatus)='accepted'", FALSE, FALSE);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        }
        else {
            return FALSE;
        }
    }
    
    private function getTaxonIdMatched($name, $processed=TRUE) {
        $this->db->select('TaxonID');
        $this->db->from('vicflora_ala_name_match');
        if ($processed) {
            $this->db->where('AlaScientificName', $name);
        }
        else {
            $this->db->where('AlaProvidedName', $name);
        }
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->TaxonID;
        }
        else {
            return FALSE;
        }
    }
    
    private function findVicFloraOccurrence($uuid) {
        $this->pgdb->select('fid, scientific_name, ala_scientific_name, ala_unprocessed_scientific_name,
            decimal_longitude, decimal_latitude,
            occurrence_status, establishment_means, establishment_means_source');
        $this->pgdb->from('vicflora.vicflora_occurrence');
        $this->pgdb->where('uuid', $uuid);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            return $query->row();
        }
        else {
            return FALSE;
        }
    }
    
    public function stateDistribution() {
        $states = $this->getStates();
        foreach ($states as $state) {
            $taxa = $this->getTaxaForState($state);
            foreach ($taxa as $name) {
                $taxon = $this->getTaxonId($name);
                if (!$taxon) {
                    $taxon = $this->getTaxonIdMatched($name);
                    if (!$taxon) {
                        continue;
                    }
                }
                if (is_object($taxon)) {
                    $nameData = $this->getNameData($taxon->GUID);
                    if ($nameData) {
                        $update = array(
                            'taxon_id' => $nameData['taxon_id'],
                            'genus_guid' => $nameData['genus_guid'],
                            'species_guid' => $nameData['species_guid'],
                            'scientific_name' => $nameData['scientific_name'],
                            'genus' => $nameData['genus'],
                            'species' => $nameData['species'],
                            'state_province' => $state
                        );
                        $stateDistributionId = $this->ci->mapmodel->findStateDistributionRecord($nameData['taxon_id'], $state);
                        if ($stateDistributionId) {
                            $update['timestamp_modified'] = date('Y-m-d H:i:s');
                            $this->ci->mapmodel->updateStateDistributionRecord($update, $stateDistributionId);
                        }
                        else {
                            $update['timestamp_created'] = date('Y-m-d H:i:s');
                            $this->ci->mapmodel->insertStateDistributionRecord($update);
                        }
                    }
                    else {
                        print_r($taxon);
                    }
                }
            }
        }
    }
    
    private function getStates() {
        $params = array();
        $params['fq'] = array(
            'data_hub_uid' => 'dh2'
        );
        $params['facets'] = 'state';
        $params['pageSize'] = 1;
        $query = createQueryString('biocache_search', $params);
        $result = doCurl($query, FALSE, TRUE);
        $data = json_decode($result);
        $facets = $data->facetResults;
        $states = array();
        foreach ($facets[0]->fieldResult as $facet) {
            $states[] = $facet->label;
        }
        return $states;
    }
    
    private function getTaxaForState($state) {
        $params = array();
        $params['fq'] = array();
        $params['fq']['data_hub_uid'] = 'dh2';
        $params['fq']['class'] = 'Equisetopsida';
        $params['fq']['state'] = '"' . $state . '"';
        $params['fq'][] = '(rank:species OR rank:subspecies OR rank:variety OR rank:form)';
        $params['pageSize'] = 1;
        $params['facets'] = 'taxon_name';
        $params['flimit'] = 100000;
        $query = createQueryString('biocache_search', $params);
        $result = doCurl($query, FALSE, TRUE);
        $data = json_decode($result);
        $facets = $data->facetResults;
        $taxa = array();
        foreach ($facets[0]->fieldResult as $facet) {
            $taxa[] = $facet->label;
        }
        return $taxa;
    }
    
}

/* End of file VicFloraMap.php */