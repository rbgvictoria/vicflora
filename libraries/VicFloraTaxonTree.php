<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class VicfloraTaxonTree {
    private $db;
    private $nodenumber;
    private $selectStmt;
    private $tree;
    private $parentids;
    private $taxonids;
    
    public function __construct() {
        $this->ci =& get_instance();
        $this->db = $this->ci->load->database('default', TRUE);
        $this->nodenumber = 0;
        $this->tree = array();
        $this->parentids = array();
        $this->taxonids = array();

        $this->selectStmt = "SELECT TaxonID
            FROM vicflora_taxon
            WHERE ParentID=?
                AND TaxonomicStatus='Accepted'";
    }
    
    public function updateTaxonTree() {
        $this->db->truncate("vicflora_taxontree");
        $this->init();
        $this->getHighestDescendantNodeNumbers();
        $this->saveTree();
    }
    
    public function init() {
        $select = "SELECT TaxonID
            FROM vicflora_taxon
            WHERE TaxonTreeDefItemID=1";
        $initStmt = $this->db->query($select);
        
        $this->nodenumber++;
        $depth = 0;
        $row = $initStmt->row();
        
        $node = new Node();
        $node->TimestampCreated = date('Y-m-d H:i:s');
        $node->TimestampModified = date('Y-m-d H:i:s');
        $node->TaxonID = $row->TaxonID;
        $node->NodeNumber = $this->nodenumber;
        $node->Depth = 0;
        
        $this->tree[] = (array) $node;
        $this->parentids[] = NULL;
        $this->taxonids[] = $row->TaxonID;
        
        $this->addNode($row->TaxonID, $depth);
    }
    
    private function addNode($parentid, $depth) {
        $this->selectStmt->execute(array($parentid));
        $result = $this->selectStmt->fetchAll(5);
        if ($result) {
            $depth++;
            foreach ($result as $row) {
                $this->nodenumber++;
                
                $node = new Node();
                $node->TimestampCreated = date('Y-m-d H:i:s');
                $node->TimestampModified = date('Y-m-d H:i:s');
                $node->Version = 0;
                $node->TaxonID = $row->TaxonID;
                $node->ParentID = $parentid;
                $node->NodeNumber = $this->nodenumber;
                $node->Depth = $depth;
                $node->CreatedByID = 1;
                $node->ModifiedByID = 1;
                
                $this->tree[] = (array) $node;
                $this->taxonids[] = $row->TaxonID;
                $this->parentids[] = $parentid;
        
                $this->addNode($row->TaxonID, $depth);
            }
        }
        else { 
            return FALSE;
        }
    }
    
    private function getHighestDescendantNodeNumbers() {
        //print_r($this->tree);
        foreach ($this->tree as $key=>$node) {
            //echo $taxon . "\n";
            
            $this->getHighestDescendant($key, $node['TaxonID']);
        }
        return TRUE;
    }

    private function getHighestDescendant($key, $taxonid) {
        $parentids = array_keys($this->parentids, $taxonid);
        if ($parentids) {
            foreach ($parentids as $parentid) {
                $node = $this->tree[$parentid];
                $this->getHighestDescendant($key, $node['TaxonID']);
            }
        }
        else {
            $skey = array_search($taxonid, $this->taxonids);
            if ($skey !== FALSE) {
                $node = $this->tree[$skey];
                $this->tree[$key]['HighestDescendantNodeNumber'] = $node['NodeNumber'];
            }
        }
        return TRUE;
    }
    
    private function saveTree() {
        $fields = array_keys($this->tree[0]);
        $values = array();
        foreach ($fields as $field)
            $values[] = '?';
        $fields = implode(',', $fields);
        $values = implode(',', $values);
        $insert = "INSERT INTO vicflora_taxontree ($fields)
                VALUES ($values)";
        $insertStmt = $this->db->prepare($insert);
        
        foreach ($this->tree as $node) {
            $insertStmt->execute(array_values($node));
        }
    }

}

class Node {
    var $TimestampCreated = NULL;
    var $TimestampModified = NULL;
    var $Version = NULL;
    var $TaxonID = NULL;
    var $ParentID = NULL;
    var $NodeNumber = NULL;
    var $HighestDescendantNodeNumber = NULL;
    var $Depth = NULL;
    var $CreatedByID = NULL;
    var $ModifiedByID = NULL;
}


/* End of file VicFloraTaxonTree.php */
/* Location: ./libraries/VicFloraTaxonTree.php */
