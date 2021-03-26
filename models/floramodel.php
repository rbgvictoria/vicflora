<?php

class FloraModel extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get taxon data
     * 
     * Gets the taxon data that is needed to update the taxon information for external
     * services such as the SOLR index, KeyBase and the mapper. 
     * 
     * @param type $guid
     * @return object
     */
    protected function getTaxonData($guid) {
        $select = "SELECT n.GUID AS scientificNameID,
                n.NameID,
                n.FullName AS scientificName,
                n.Author AS scientificNameAuthorship,
                n.name_type,
                r.GUID AS namePublishedInID,
                r.Title AS namePublishedIn,
                r.PublicationYear AS namePublishedInYear,
                t.Sensu,
                pub.Publication AS nameAccordingTo,
                LOWER(td.Name) AS taxonRank,
                LOWER(t.TaxonomicStatus) AS taxonomicStatus,
                at.OccurrenceStatus AS occurrenceStatus,
                at.EstablishmentMeans AS establishmentMeans,
                t.Remarks AS taxonRemarks,
                pt.GUID AS parentNameUsageID,
                pn.FullName AS parentNameUsage,
                at.GUID AS acceptedNameUsageID,
                an.FullName AS acceptedNameUsage,
                an.Author AS acceptedNameUsageAuthorship,
                LOWER(atd.Name) AS acceptedNameUsageTaxonRank,
                tt.NodeNumber,
                att.NodeNumber AS AcceptedNodeNumber,
                t.RankID,
                at.RankID AS AcceptedRankID,
                c.CommonName AS vernacularName
            FROM vicflora_taxon t
            JOIN vicflora_name n ON t.NameID=n.NameID
            LEFT JOIN vicflora_taxontreedefitem td ON t.TaxonTreeDefItemID=td.TaxonTreeDefItemID
            LEFT JOIN vicflora_reference r ON n.ProtologueID=r.ReferenceID
            LEFT JOIN vicflora_taxon pt ON t.ParentID=pt.TaxonID
            LEFT JOIN vicflora_name pn ON pt.NameID=pn.NameID
            LEFT JOIN vicflora_taxon at ON t.AcceptedID=at.TaxonID
            LEFT JOIN vicflora_name an ON at.NameID=an.NameID
            LEFT JOIN vicflora_taxontreedefitem atd ON at.TaxonTreeDefItemID=atd.TaxonTreeDefItemID
            LEFT JOIN vicflora_publication pub ON t.SourceID=pub.PublicationID
            LEFT JOIN vicflora_taxontree tt ON t.TaxonID=tt.TaxonID AND t.TaxonomicStatus='accepted'
            LEFT JOIN vicflora_taxontree att ON t.AcceptedID=att.TaxonID AND !(t.TaxonomicStatus='accepted')
            LEFT JOIN vicflora_commonname c ON t.TaxonID=c.TaxonID AND t.TaxonomicStatus='accepted' AND c.IsPreferred=1
            WHERE t.GUID='$guid'";
        $query = $this->db->query($select);
        if ($query->num_rows()) {
            return $query->row();
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Get the higher classification
     * 
     * Gets the higher classification for a taxon using the getHigherClassification
     * function and converts the result to a Classification object
     * 
     * @param type $nodenumber
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
                        $classification->specific_epithet = $row->Name;
                        break;
                    case 'subspecies':
                    case 'variety':
                    case 'subvariety':
                    case 'forma':
                    case 'subforma':
                        $classification->infraspecific_epithet = $row->Name;
                        break;

                    default:
                        break;
                }
            }
        }
        return $classification;
    }
    
    /**
     * Get the higher classification
     * 
     * Gets the higher classification for a taxon based on the node number
     * that is provided (so the taxon needs to be in the tree)
     * 
     * @param integer $nodenumber
     * @return array
     */
    protected function getHigherClassification($nodenumber) {
        $select = "SELECT lower(td.Name) AS `Rank`, n.Name
            FROM vicflora_taxon t
            JOIN vicflora_taxontreedefitem td ON t.TaxonTreeDefItemID=td.TaxonTreeDefItemID
            JOIN vicflora_taxontree tt ON t.TaxonID=tt.TaxonID
            JOIN vicflora_name n ON t.NameID=n.NameID
            WHERE tt.NodeNumber<=$nodenumber AND tt.HighestDescendantNodeNumber>=$nodenumber AND t.RankID>0
            ORDER BY t.RankID";
        $query = $this->db->query($select);
        return $query->result();
    }
    
    protected function getSpeciesID($id) {
        $select = "SELECT p.GUID
            FROM vicflora_taxon t
            JOIN vicflora_taxon p ON t.ParentID=p.TaxonID AND p.RankID=220
            WHERE t.GUID='$id'";
        $query = $this->db->query($select);
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->GUID;
        }
        else {
            return FALSE;
        }
    }
}

/* End of file floramodel.php */
/* Location: ./models/floramodel.php */
