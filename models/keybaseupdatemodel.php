<?php

require_once 'floramodel.php';

class KeyBaseUpdateModel extends FloraModel {
    private $projectId;
    private $baseUrl;
    
    public function __construct() {
        parent::__construct();
        $this->projectId = 10;
        $this->baseUrl = 'http://data.rbg.vic.gov.au/vicflora/flora/taxon/';
    }
    
    /**
     * Update items in KeyBase project
     * 
     * Finds the accepted taxon records in VicFlora that have been modified since
     * the given date (or all accepted taxon records if no date is provided) and 
     * calls the updateProjectItem() function for each to update the information 
     * in KeyBase. 
     * 
     * @param datetime $from
     */
    public function updateKeyBaseProject($from=FALSE) {
        $this->db->select('guid');
        $this->db->from('vicflora_taxon');
        $this->db->where('TaxonomicStatus', 'accepted');
        $this->db->where('DoNotIndex IS NULL', FALSE, FALSE);
        if ($from) {
            $this->db->where('TimestampModified >', $from);
        }
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $this->updateProjectItem($row->guid);
            }
        }
    }
    
    /**
     * Update single project item
     * 
     * Collects the data for a single taxon from VicFlora. It then checks whether
     * there is already a record in KeyBase. If so, the existing record is updated, 
     * otherwise a new Project Item is created; if necessary, a new Item will be 
     * created as well.
     * 
     * @param text $guid
     */
    public function updateProjectItem($guid) {
        $row = $this->getTaxonData($guid);
        $data = new KeyBaseRecord();
        $data->projectsID = $this->projectId;
        $data->taxonID = $guid;
        $data->url = $this->baseUrl . $guid;
        $data->taxonRank = $row->acceptedNameUsageTaxonRank;
        $data->scientificName = $row->scientificName;
        $data->scientificNameAuthorship = $row->scientificNameAuthorship;
        $data->timestampModified = date('Y-m-d H:i:s');
        
        if ($row->NodeNumber) {
            $data = (object) array_merge((array) $data, (array) $this->higherClassification($row->NodeNumber));
        }
        
        $projectItemId = $this->findKeyBaseProjectItem($guid);
        if ($projectItemId) {
            unset($data->itemsID);
            $this->updateKeyBaseProjectItem($projectItemId, $data);
        }
        else {
            $data->itemsID = $this->findKeyBaseItem($data->scientificName);
            if (!$data->itemsID) {
                $data->itemsID = $this->insertKeyBaseItem($data->scientificName);
            }
            if ($data->itemsID) {
                $data->timestampCreated = date('Y-m-d H:i:s');
                $this->insertKeyBaseProjectItem($data);
            }
        }
        
    }
    
    /**
     * Get higher classification
     * 
     * Overrides the function in the parent model, because KeyBase needs a 
     * differently formated object.
     * 
     * @param integer $nodenumber
     * @return object
     */
    protected function higherClassification($nodenumber) {
        $classification = new stdClass();
        $result = $this->getHigherClassification($nodenumber);
        if ($result) {
            foreach ($result as $row) {
                switch ($row->Rank) {
                    case 'kingdom':
                        $classification->kingdom = $row->Name;
                        break;
                    case 'phylum':
                        $classification->phylum = $row->Name;
                        break;
                    case 'class':
                        $classification->class = $row->Name;
                        break;
                    case 'subclass':
                        $classification->subclass = $row->Name;
                        break;
                    case 'superorder':
                        $classification->superorder = $row->Name;
                        break;
                    case 'order':
                        $classification->order = $row->Name;
                        break;
                    case 'family':
                        $classification->family = $row->Name;
                        break;
                    case 'genus':
                        $classification->genus = $row->Name;
                        break;
                    case 'species':
                        $classification->specificEpithet = $row->Name;
                        break;
                    case 'subspecies':
                    case 'variety':
                    case 'subvariety':
                    case 'forma':
                    case 'subforma':
                        $classification->infraspecificEpithet = $row->Name;
                        break;

                    default:
                        break;
                }
            }
        }
        return $classification;
    }
    
    /**
     * Find project item in KeyBase
     * 
     * Tries to find a project item in KeyBase based on the provided taxonID; 
     * returns the project item's ID, or FALSE if the item cannot be found.
     * 
     * @param type $guid
     * @return integer|boolean
     */
    private function findKeyBaseProjectItem($guid) {
        $this->db->select('ProjectItemsID');
        $this->db->from('keybase.projectitems');
        $this->db->where('ProjectsID', $this->projectId);
        $this->db->where('TaxonID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->ProjectItemsID;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Find item in KeyBase
     * 
     * Tries to find an item in KeyBase based on the provided scientific name; 
     * returns the item's ID, or FALSE if the item cannot be found
     * 
     * @param text $scientificName
     * @return integer|boolean
     */
    private function findKeyBaseItem($scientificName) {
        $this->db->select('ItemsID');
        $this->db->from('keybase.items');
        $this->db->where('Name', $scientificName);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->ItemsID;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Insert item record into KeyBase
     * 
     * Inserts an item record into KeyBase for the supplied scientific name and
     * returns the ID of the new item. 
     * 
     * @param text $scientificName
     * @return integer
     */
    private function insertKeyBaseItem($scientificName) {
        $this->db->select('max(ItemsID)+1 as newID', FALSE);
        $this->db->from('keybase.items');
        $query = $this->db->get();
        $row = $query->row();
        $data = array(
            'ItemsID' => $row->newID,
            'Name' => $scientificName,
            'TimestampCreated' => date('Y-m-d H:i:s'),
            'TimestampModified' => date('Y-m-d H:i:s')
        );
        $this->db->insert('keybase.items', $data);
        return $row->newID;
    }
    
    /**
     * Update project item in KeyBase
     * 
     * @param integer $projectItemId
     * @param object|array $data
     */
    private function updateKeyBaseProjectItem($projectItemId, $data) {
        $this->db->where('ProjectItemsID', $projectItemId);
        $this->db->update('keybase.projectitems', $data);
    }
    
    /**
     * Insert a new project item into KeyBase
     * 
     * @param object|array $data
     */
    private function insertKeyBaseProjectItem($data) {
        $this->db->insert('keybase.projectitems', $data);
    }
}

class KeyBaseRecord {
    var $timestampCreated = NULL;
    var $timestampModified = NULL;
    var $projectsID = NULL;
    var $itemsID = NULL;
    var $taxonID = NULL;
    var $taxonRank = NULL;
    var $scientificName = NULL;
    var $scientificNameAuthorship = NULL;
    var $kingdom = NULL;
    var $phylum = NULL;
    var $class = NULL;
    var $subclass = NULL;
    var $superorder = NULL;
    var $order = NULL;
    var $family = NULL;
    var $specificEpithet = NULL;
    var $infraspecificEpithet = NULL;
    var $url = NULL;
}

/* End of file mapupdatemodel.php */
/* Location: ./models/mapupdatemodel.php */
