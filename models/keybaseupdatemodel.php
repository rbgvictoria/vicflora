<?php

require_once 'floramodel.php';

class KeyBaseUpdateModel extends FloraModel {
    private $projectId;
    private $baseUrl;
    
    public function __construct() {
        parent::__construct();
        $this->projectId = 10;
        $this->baseUrl = 'https://vicflora.rbg.vic.gov.au/flora/taxon/';
    }
    
    /**
     * Update items in KeyBase project
     * 
     */
    public function updateKeyBaseProject() {
        $this->db->where('ProjectsID', $this->projectId);
        $this->db->delete('keybase.projectitems');

        $sql = "REPLACE INTO keybase.projectitems(ProjectsID, ItemsID, ScientificName, Url)
                SELECT {$this->projectId}, i.ItemsID, n.FullName, concat('{$this->baseUrl}', t.guid)
                FROM vicflora.vicflora_taxon t
                JOIN vicflora.vicflora_name n ON t.NameID=n.NameID
                JOIN keybase.items i ON n.FullName=i.Name
                WHERE t.TaxonomicStatus='accepted'";
        $this->db->query($sql);
    }
    
}

/* End of file mapupdatemodel.php */
/* Location: ./models/mapupdatemodel.php */
