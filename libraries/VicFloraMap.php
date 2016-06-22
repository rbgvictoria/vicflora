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
    
    private $outlierCount;
    
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
                    $records = $this->processOccurrences($result);
                }
                $startIndex += $pageSize;
            }
            $totalRecords += $this->recordCount;
        }
        return $totalRecords;
    }
    
    public function outliers() {
        $this->truncateOutlierTable();
        $startIndex = 0;
        $pageSize = 1000;
        $this->outlierCount = $startIndex + 1;
        while ($startIndex < $this->outlierCount) {
            $result = $this->getOutliers($startIndex, $pageSize);
            if ($result) {
                $data = json_decode($result);
                $this->outlierCount = $data->totalRecords;
                $outliers = $data->occurrences;
                foreach ($outliers as $outlier) {
                    $this->insertOutlierRecord($outlier->uuid);
                }
                $startIndex += $pageSize;
            }
        }
    }
    
    private function getOutliers($startIndex, $pageSize) {
        $params = array();
        $params['fq'] = array(
            'data_hub_uid' => 'dh2',
            'state' => 'Victoria',
            'class' => 'Equisetopsida',
            'outlier_layer' => '[* TO *]'
        );
        $params['fl'] = array('id');
        $params['facet'] = 'off';
        $params['pageSize'] = $pageSize;
        $params['startIndex'] = $startIndex;
        $query = createQueryString('biocache_search', $params);
        $result = doCurl($query, FALSE, TRUE);
        return $result;
    }
    
    private function insertOutlierRecord($uuid) {
        $data = array('uuid' => $uuid);
        $this->pgdb->insert('vicflora.vicflora_outlier', $data);
    }
    
    private function truncateOutlierTable() {
        $this->pgdb->empty_table('vicflora.vicflora_outlier');
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
    
    private function processOccurrences($json) {
        $data = json_decode($json);
        $occurrences = $data->occurrences;
        $records = array();
        if ($occurrences) {
            foreach ($occurrences as $occurrence) {
                $latitude = (isset($occurrence->decimalLatitude) && $occurrence->decimalLatitude) ? $occurrence->decimalLatitude : NULL;
                $longitude = (isset($occurrence->decimalLongitude) && $occurrence->decimalLongitude) ? $occurrence->decimalLongitude : NULL;
                if ($latitude && $longitude) {
                    $vfOccurrence = $this->findOccurrence($occurrence->uuid);
                    if ($vfOccurrence) {
                        $this->updateOccurrenceRecord($occurrence, $vfOccurrence);
                    }
                    else {
                        $this->insertOccurrenceRecord($occurrence);
                        
                    }
                }
            }
        }
        $this->recordCount = $data->totalRecords;
        return $records;
    }
    
    private function updateOccurrenceRecord($occurrence, $vfOccurrence) {
        $rec = $this->createOccurrenceRecord($occurrence);
        if ($occurrence->raw_scientificName != $vfOccurrence->unprocessed_scientific_name) { 
            $sciName = $this->processScientificName($occurrence->raw_scientificName);
            $rec['ala_parsed_name_id'] = $sciName->id;
            $rec['vicflora_scientific_name_id'] = $sciName->vicflora_scientific_name_id;

        }
        $this->pgdb->where('uuid', $occurrence->uuid);
        $this->pgdb->update('vicflora.avh_occurrence', $rec);
        
        if ($occurrence->decimalLatitude != $vfOccurrence->decimal_latitude ||
                $occurrence->decimalLongitude != $vfOccurrence->decimal_longitude) {
            $this->updateOccurrenceAttributeRecord($occurrence->uuid, $rec['geom']);
        }
        
    }
    
    private function insertOccurrenceRecord($occurrence) {
        $rec = $this->createOccurrenceRecord($occurrence);
        $sciName = $this->processScientificName($occurrence->raw_scientificName);
        $rec['ala_parsed_name_id'] = $sciName->id;
        $rec['vicflora_scientific_name_id'] = $sciName->vicflora_scientific_name_id;
        $this->pgdb->insert('vicflora.avh_occurrence', $rec);
        $this->updateOccurrenceAttributeRecord($occurrence->uuid, $rec['geom']);
    }
    
    private function updateOccurrenceAttributeRecord($uuid, $geom) {
        $bio = $this->getBioregion($geom);
        if ($bio) {
            $data = $bio;
            $nrm = $this->getNrm($geom);
            if ($nrm) {
                $data->nrm_region = $nrm->nrm_region;
            }
            $data->timestamp_modified = date('Y-m-d H:i:s');
            
            $fid = $this->findOccurrenceAttributeRecord($uuid);
            if ($fid) {
                $this->pgdb->where('uuid', $fid);
                $this->pgdb->update('vicflora.occurrence_attribute', $data);
            }
            else {
                $data->uuid = $uuid;
                $this->pgdb->insert('vicflora.occurrence_attribute', $data);
            }
        }
    }
    
    private function findOccurrenceAttributeRecord($uuid) {
        $this->pgdb->select('uuid');
        $this->pgdb->from('vicflora.occurrence_attribute');
        $this->pgdb->where('uuid', $uuid);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->uuid;
        }
        else {
            return FALSE;
        }
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
    
    private function getBioregion($geom) {
        $this->pgdb->select('reg_code_7, reg_name_7, sub_code_7, sub_name_7');
        $this->pgdb->from('vicflora.vicflora_bioregion');
        $this->pgdb->where("ST_Intersects('$geom'::geometry(Point,4326), geom)", FALSE, FALSE);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            return $query->row();
        }
        else {
            return FALSE;
        }
    }
    
    public function getNrm($geom) {
        $this->pgdb->select('nrm_region');
        $this->pgdb->from('vicflora.vicflora_nrm2014');
        $this->pgdb->where("ST_Intersects('$geom'::geometry(Point,4326), geom)", FALSE, FALSE);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            return $query->row();
        }
        else {
            return FALSE;
        }
    }

    private function findOccurrence($uuid) {
        $this->pgdb->select('uuid, decimal_latitude, decimal_longitude, unprocessed_scientific_name, geom, establishment_means');
        $this->pgdb->from('vicflora.avh_occurrence');
        $this->pgdb->where('uuid', $uuid);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            return $query->row();
        }
        else {
            return FALSE;
        }
    }
    
    private function processScientificName($scientificName) {
        $parsedName = $this->findParsedName($scientificName);
        if ($parsedName) {
            return $parsedName;
        }
        else {
            $parsedName = $this->parseName($scientificName);
            $scientificNameID = NULL;
            $match = $this->matchName($parsedName->scientificName, $parsedName->canonicalNameComplete, 
                    $parsedName->canonicalNameWithMarker, $parsedName->canonicalName);
            if ($match) {
                $scientificNameID = $match->scientific_name_id;
                $this->pgdb->where('id', $parsedName->id);
                $this->pgdb->update('vicflora.ala_name_match', array('vicflora_scientific_name_id', $match->scientific_name_id));
            }
            return (object) array(
                'id' => $parsedName->id,
                'vicflora_scientific_name_id' => $scientificNameID
            );
        }
    }
    
    private function findParsedName($scientificName) {
        $this->pgdb->select('id, vicflora_scientific_name_id');
        $this->pgdb->from('vicflora.ala_parsed_name');
        $this->pgdb->where('scientific_name', $scientificName);
        $query = $this->pgdb->get();
        if ($query->num_rows()) {
            return $query->row();
        }
        else {
            return FALSE;
        }
    }
    
    private function parseName($scientificName) {
        $result = doCurl('http://api.gbif.org/v1/parser/name', 'name=' . urlencode($scientificName), TRUE);
        $data = json_decode($result);
        $rec = $data[0];
        $newID = $this->getNewParsedNameID();
        $rec->id = $newID;
        $this->insertParsedName($rec);
        return $rec;
    }
    
    private function getNewParsedNameID() {
        $this->pgdb->select('max(id)+1 as new_id', FALSE);
        $this->pgdb->from('vicflora.ala_parsed_name');
        $query = $this->pgdb->get();
        $row = $query->row();
        return $row->new_id;
    }
    
    private function insertParsedName($data) {
        $data = $this->convertKeys($data);
        if (isset($data->infra_specific_epithet)) {
            $data->infraspecific_epithet = $data->infra_specific_epithet;
            unset($data->infra_specific_epithet);
        }
        unset($data->authors_parsed);
        $this->pgdb->insert('vicflora.ala_parsed_name', $data);
    }
    
    private function convertKeys($data) {
        $keys = array_keys((array) $data);
        $convert = array();
        foreach ($keys as $key) {
            $convert[$key] = strtolower(preg_replace('/[A-Z]/', '_$0', $key));
        }
        $ret = array();
        foreach ((array) $data as $key => $value) {
            $ret[$convert[$key]] = $value;
        }
        return (object) $ret;
    }
    
    private function matchName($scientificName, $canonicalNameComplete, 
            $canonicalNameWithMarker, $canonicalName) {
        $match = $this->exactMatch($scientificName, $canonicalNameComplete);
        if ($match) {
            return $match;
        }
        else {
            $match = $this->canonicalNameMatch($canonicalNameWithMarker, $canonicalName);
            return $match;
        }
        
    }
    
    private function exactMatch($scientificName, $canonicalNameComplete) {
        $this->db->select('n.GUID as scientific_name_id');
        $this->db->from('vicflora_name n');
        $this->db->join('vicflora_taxon t', 'n.NameID=t.NameID');
        $this->db->join('vicflora_taxon a', 't.AcceptedID=a.TaxonID');
        $this->db->where("(n.FullNameWithAuthor='$scientificName' OR 
            n.FullNameWithAuthor='$canonicalNameComplete')", FALSE, FALSE);
        $this->db->where('a.RankID >=', 220);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->scientific_name_id;
        }
        else {
            return FALSE;
        }
    }
    
    private function canonicalNameMatch($canonicalNameWithMarker, $canonicalName) {
        $this->db->select('n.GUID as scientific_name_id');
        $this->db->from('vicflora_name n');
        $this->db->join('vicflora_taxon t', 'n.NameID=t.NameID');
        $this->db->join('vicflora_taxon a', 't.AcceptedID=a.TaxonID');
        $this->db->where("(n.FullName='$canonicalNameWithMarker' OR n.FullName='$canonicalName' 
            OR REPLACE(FullName, '×', '')='$canonicalName')", FALSE, FALSE);
        $this->db->where('a.RankID >=', 220);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->scientific_name_id;
        }
        else {
            return FALSE;
        }
    }
}


/* End of file VicFloraMap.php */
/* Location: ./models/VicFloraMap.php */
