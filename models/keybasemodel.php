<?php

class KeyBaseModel extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getMissingTaxa() {
        $this->db->select('t.GUID, n.FullName');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('keybase.projectitems pi', 't.GUID=pi.TaxonID', 'left');
        $this->db->where('t.RankID >=', 140);
        $this->db->where('t.TaxonomicStatus', 'accepted');
        $this->db->where('pi.ProjectItemsID');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getVicFloraData($taxonID) {
        //-- TaxonID, TaxonRank, ScientificName, ScientificNameAuthorship, Kingdom, Phylum, Class, Subclass, Superorder, Order, Family, Genus, SpecificEpithet, InfraspecificEpithet
        $this->db->select('n.FullName AS scientificName, n.Author, td.Name AS taxonRank,
            tt.NodeNumber');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxontreedefitem td', 't.TaxonTreeDefItemID=td.TaxonTreeDefItemID');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('t.GUID', $taxonID);
        $query = $this->db->get();
        
        if ($query->num_rows()) {
            $row = $query->row();
            $this->db->select('td.Name AS Rank, n.FullName');
            $this->db->from('vicflora_taxon t');
            $this->db->join('vicflora_name n', 't.NameID=n.NameID');
            $this->db->join('vicflora_taxontreedefitem td', 't.TaxonTreeDefItemID=td.TaxonTreeDefItemID');
            $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
            $this->db->where('tt.NodeNumber <=', $row->NodeNumber);
            $this->db->where('tt.HighestDescendantNodeNumber >=', $row->NodeNumber);
            $this->db->where('td.RankID >', 0);
            $this->db->order_by('td.RankID');
            $query = $this->db->get();
            if ($query->num_rows()) {
                $ret = array();
                $ret['taxonID'] = $taxonID;
                $ret['taxonRank'] = $row->taxonRank;
                $ret['scientificName'] = $row->scientificName;
                $ret['scientificNameAuthorship'] = $row->Author;
                foreach ($query->result() as $r) {
                    switch ($r->Rank) {
                        case 'species':
                            $ret['specificEpithet'] = $r->FullName;
                            break;
                        case 'subspecies':
                        case 'variety':
                        case 'forma':
                        case 'nothosubspecies':
                        case 'nothovariety':
                        case 'nothoforma':
                            $ret['infraspecificEpithet'] = $r->FullName;
                            break;
                        case 'cultivar':
                            break;
                        default:
                            $ret[$r->Rank] = $r->FullName;
                            break;
                    }
                    
                }
                return $ret;
            }
        }
    }
    
    public function getKeyBaseItemsID($name) {
        $this->db->select('ItemsID');
        $this->db->from('keybase.items');
        $this->db->where('Name', $name);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $itemID = $row->ItemsID;
        }
        else {
            $q = $this->db->query("SELECT MAX(ItemsID)+1 AS NewID FROM keybase.items");
            $r = $q->row();
            $itemID = $r->NewID;
            $ins = array(
                'ItemsID' => $itemID,
                'Name' => $name
            );
            $this->db->insert('keybase.items', $ins);
        }
        return $itemID;
    }
    
    public function insertKeyBaseProjectItem($data) {
        $this->db->insert('keybase.projectitems', $data);
    }
    
    private function getKeyedOutItems() {
        $sql = "SELECT i.ItemsID
            FROM keybase.projects p
            JOIN keybase.`keys` k ON p.ProjectsID=k.ProjectsID
            JOIN keybase.leads l ON k.KeysID=l.KeysID
            JOIN keybase.items i ON l.ItemsID=i.ItemsID
            JOIN keybase.projectitems pi ON p.ProjectsID=pi.ProjectsID AND i.ItemsID=pi.ItemsID
            WHERE p.ProjectsID=10
            UNION
            SELECT i.ItemsID
            FROM keybase.projects p
            JOIN keybase.`keys` k ON p.ProjectsID=k.ProjectsID
            JOIN keybase.leads l ON k.KeysID=l.KeysID
            JOIN keybase.groupitem gi ON l.ItemsID=gi.GroupID
            JOIN keybase.items i ON gi.MemberID=i.ItemsID
            JOIN keybase.projectitems pi ON p.ProjectsID=pi.ProjectsID AND i.ItemsID=pi.ItemsID
            WHERE p.ProjectsID=10";
        $query = $this->db->query($sql);
        if ($query->num_rows()) {
            $items = array();
            foreach ($query->result() as $row) {
                $items[] = $row->ItemsID;
            }
            return $items;
        }
        else {
            return FALSE;
        }
    }
    
    public function getNotKeyedOutItems() {
        $items = $this->getKeyedOutItems();
        $this->db->select('t.GUID, n.FullName');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('keybase.projectitems pi', 't.GUID=pi.TaxonID');
        $this->db->where('t.TaxonomicStatus', 'accepted');
        $this->db->where_not_in('pi.ItemsID', $items);
        $this->db->order_by('FullName');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $ret = array();
            foreach ($query->result() as $row) {
                $item = (array) $row;
                if (!$this->isSubordinateOfMonotypic($row->GUID)) {
                    $ret[] = $item;
                }
            }
            return $ret;
        }
    }
    
    private function isSubordinateOfMonotypic($guid) {
        $this->db->select('t.GUID');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_taxon p', 't.ParentID=p.TaxonID');
        $this->db->join('vicflora_taxon c', "p.TaxonID=c.ParentID AND c.TaxonomicStatus='accepted'", FALSE, FALSE);
        $this->db->where('t.GUID', $guid);
        $this->db->group_by('t.TaxonID');
        $this->db->having('count(*)>1');
        $query = $this->db->get();
        if ($query->num_rows()) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
}

/* End of file keybasemodel.php */
/* Location: ./models/keybasemodel.php */
