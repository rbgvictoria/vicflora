<?php

class TaxonModel extends CI_Model {
    var $pgdb;
    
    public function __construct() {
        parent::__construct();
        $this->pgdb = $this->load->database('postgis', TRUE);
    }
    
    public function getTaxonData($guid) {
        $this->db->select('t.TaxonID, t.GUID, td.Name AS Rank, n.FullName, n.Author, r.Author AS InAuthor, 
            r.JournalOrBook, r.Series, r.Edition, r.Volume, r.Part, r.Page, r.PublicationYear,
            t.Sensu, n.NomenclaturalNote, t.TaxonomicStatus, t.OccurrenceStatus, t.EstablishmentMeans, 
            t.Remarks, t.RankID, IF(p.ProfileID IS NOT NULL AND (p.AcceptedID IS NULL OR p.TaxonID=p.AcceptedID), p.ProfileID, NULL) AS UnmatchedProfile', FALSE);
        $this->db->select("distv.DistributionText AS DistV, dista.DistributionText AS DistA, distw.DistributionText AS DistW");

        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxontreedefitem td', 't.TaxonTreeDefitemID=td.TaxonTreeDefItemID');
        $this->db->join('vicflora_reference r', 'n.ProtologueID=r.ReferenceID', 'left');
        $this->db->join('vicflora_profile p', 't.TaxonID=p.TaxonID AND p.IsCurrent=1', 'left', false);
        $this->db->join('vicflora_distributionextra distv', "t.TaxonID=distv.AcceptedID AND distv.Scope='Victoria'", 'left', FALSE);
        $this->db->join('vicflora_distributionextra dista', "t.TaxonID=dista.AcceptedID AND dista.Scope='Australia'", 'left', FALSE);
        $this->db->join('vicflora_distributionextra distw', "t.TaxonID=distw.AcceptedID AND distw.Scope='World'", 'left', FALSE);
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) 
            return $query->row_array();
        else
            return FALSE;
    }
    
    public function getTaxonName($guid) {
        $this->db->select('n.FullName');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->FullName;
        }
    }
    
    public function getTaxonGuids() {
        $ret = array();
        $this->db->select('GUID');
        $this->db->from('vicflora_taxon');
        $this->db->where('DoNotIndex !=', 1);
        $this->db->or_where('DoNotIndex IS NULL', FALSE, FALSE);
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $ret[] = $row->GUID;
            }
        }
        return $ret;
    }
    
    public function getDoNotIndexGuids() {
        $ret = array();
        $this->db->select('GUID');
        $this->db->from('vicflora_taxon');
        $this->db->where('DoNotIndex', 1);
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $ret[] = $row->GUID;
            }
        }
        return $ret;
    }
    
    public function getSynonyms($guid) {
        $this->db->select('t.GUID, n.FullName, n.Author');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxon a', 't.AcceptedID=a.AcceptedID');
        $this->db->where_in('t.TaxonomicStatus', array('synonym', 'homotypic synonym', 'heterotypic synonym'));
        $this->db->where('a.GUID', $guid);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getMisapplications($guid) {
        $this->db->select('t.GUID, n.FullName, n.Author, t.Sensu');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxon a', 't.AcceptedID=a.AcceptedID');
        $this->db->where_in('t.TaxonomicStatus', array('misapplication'));
        $this->db->where('a.GUID', $guid);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getTaxonAttributes($guid) {
        $this->db->select('a.Attribute, a.StrValue');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_taxonattribute a', 't.TaxonID=a.TaxonID');
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $ret = array();
            foreach ($query->result() as $row) {
                $ret[$row->Attribute] = $row->StrValue;
            }
            return $ret;
        }
        else {
            return FALSE;
        }
    }
    
    function getChanges($guid) {
        $this->db->select('c.ChangeID, DATE(c.TimestampCreated) AS ChangeDate, c.NewNameID,
            nn.FullName AS AcceptedName, nn.Author AS AcceptedNameAuthor, c.Source, c.ChangeType');
        $this->db->select("concat_ws(' ', u.FirstName, u.LastName) AS ChangedBy", false);
        $this->db->from('vicflora_change c');
        $this->db->join('vicflora_taxon t', 'c.NameID=t.TaxonID');
        $this->db->join('vicflora_taxon nt', 'c.NewNameID=nt.TaxonID');
        $this->db->join('vicflora_name nn', 'nt.NameID=nn.NameID');
        $this->db->join('users u', 'c.CreatedByID=u.UsersID','left');
        $this->db->where('t.GUID', $guid);
        $this->db->order_by('c.TimestampCreated');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function getAcceptedName($fullname) {
        $this->db->select('t.TaxonID, n.FullName, n.Author');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->where('n.FullName', $fullname);
        $this->db->where('t.TaxonomicStatus', 'accepted');
        $query = $this->db->get();
        if ($query->num_rows()) 
            return $query->row_array();
        else
            return FALSE;
    }
    
    function getNewName($fullname) {
        $this->db->select('t.TaxonID, n.FullName, n.Author');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->where('n.FullName', $fullname);
        $query = $this->db->get();
        if ($query->num_rows()) 
            return $query->row_array();
        else
            return FALSE;
    }

    
    function getAcceptedNameByGUID($guid) {
        $this->db->select('t.TaxonID, n.FullName, n.Author, t.GUID, t.RankID');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxon s', 't.TaxonID=s.AcceptedID');
        $this->db->where('s.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) 
            return $query->row_array();
        else
            return FALSE;
    }
    
    function getAcceptedNameByID($id) {
        $this->db->select('t.TaxonID, n.FullName, n.Author');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->where('t.TaxonID', $id);
        $query = $this->db->get();
        if ($query->num_rows()) 
            return $query->row_array();
        else
            return FALSE;
    }
    
    function getTaxonByName($name) {
        $this->db->select('t.TaxonID, n.FullName AS Name');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->where('n.FullName', $name);
        $this->db->where('t.TaxonomicStatus', 'accepted');
        $query = $this->db->get();
        if ($query->num_rows()) 
            return $query->row_array();
        else
            return FALSE;
    }
    
    function getParentByName($name) {
        $this->db->select('t.TaxonID, n.Name');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->where('n.Name', $name);
        $this->db->where('t.TaxonomicStatus', 'accepted');
        $query = $this->db->get();
        if ($query->num_rows()) 
            return $query->row_array();
        else
            return FALSE;
    }
    
    public function getProfiles($guid) {
        $ret = array();
        $this->db->select("p.ProfileID, p.GUID, p.Profile, if(p.AcceptedID!=p.TaxonID, sn.FullName, NULL) AS AsFullName, 
            if(p.AcceptedID!=p.TaxonID, sn.Author, NULL) AS AsAuthor, 
            if(p.AcceptedID!=p.TaxonID, st.GUID, NULL) AS AsGUID, p.TaxonomicStatus, p.IsUpdated=1 AS IsUpdated,
            IF(p.SourceID IS NULL AND p.IsUpdated IS NULL, DATE(p.TimestampCreated), NULL) AS DateCreated,
            IF(p.SourceID IS NULL AND p.isUpdated IS NULL, CONCAT(u.FirstName, ' ', u.LastName), NULL) AS CreatedBy,
            DATE(p.TimestampCreated) AS DateUpdated, CONCAT(u.FirstName, ' ', u.LastName) AS UpdatedBy,
            r.Author, ir.PublicationYear, r.Title, ir.Author as InAuthor, ir.Title AS InTitle, 
            ir.Publisher, ir.PlaceOfPublication", FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_profile p', 't.TaxonID=p.AcceptedID');
        $this->db->join('vicflora_taxon st', 'p.TaxonID=st.TaxonID');
        $this->db->join('vicflora_name sn', 'st.NameID=sn.NameID');
        $this->db->join('vicflora_reference r', 'p.SourceID=r.ReferenceID', 'left');
        $this->db->join('vicflora_reference ir', 'r.InPublicationID=ir.ReferenceID', 'left');
        $this->db->join('users u', 'p.CreatedByID=u.UsersID', 'left');
        $this->db->where('t.GUID', $guid);
        $this->db->where('p.IsCurrent', 1);
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if ($row['IsUpdated'] && !$row['Author']) {
                    $this->db->select("CONCAT(u.FirstName, ' ', u.LastName) AS CreatedBy,
                        DATE(p.TimestampCreated) AS DateCreated", FALSE);
                    $this->db->from('vicflora_profile p');
                    $this->db->join('users u', 'p.CreatedByID=u.UsersID', 'left');
                    $this->db->where('p.GUID', $row['GUID']);
                    $this->db->where('p.IsUpdated IS NULL', FALSE, FALSE);
                    $q = $this->db->get();
                    $r = $q->row();
                    $row['CreatedBy'] = $r->CreatedBy;
                    $row['DateCreated'] = $r->DateCreated;
                }
                $ret[] = $row;
            }
        }
        return $ret;
    }
    
    function getUnmatchedProfiles($guid) {
        $ret = array();
        $this->db->select("p.ProfileID, p.GUID, p.Profile, if(p.AcceptedID!=p.TaxonID, sn.FullName, NULL) AS AsFullName, 
            if(p.AcceptedID!=p.TaxonID, sn.Author, NULL) AS AsAuthor, 
            if(p.AcceptedID!=p.TaxonID, st.GUID, NULL) AS AsGUID, p.TaxonomicStatus, p.IsUpdated=1 AS IsUpdated,
            IF(p.SourceID IS NULL AND p.IsUpdated IS NULL, DATE(p.TimestampCreated), NULL) AS DateCreated,
            IF(p.SourceID IS NULL AND p.isUpdated IS NULL, CONCAT(u.FirstName, ' ', u.LastName), NULL) AS CreatedBy,
            DATE(p.TimestampCreated) AS DateUpdated, CONCAT(u.FirstName, ' ', u.LastName) AS UpdatedBy,
            r.Author, ir.PublicationYear, r.Title, ir.Author as InAuthor, ir.Title AS InTitle, 
            ir.Publisher, ir.PlaceOfPublication", FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_profile p', 't.TaxonID=p.TaxonID');
        $this->db->join('vicflora_taxon st', 'p.TaxonID=st.TaxonID');
        $this->db->join('vicflora_name sn', 'st.NameID=sn.NameID');
        $this->db->join('vicflora_reference r', 'p.SourceID=r.ReferenceID', 'left');
        $this->db->join('vicflora_reference ir', 'r.InPublicationID=ir.ReferenceID', 'left');
        $this->db->join('users u', 'p.CreatedByID=u.UsersID', 'left');
        $this->db->where('t.GUID', $guid);
        $this->db->where('(p.AcceptedID IS NULL OR p.AcceptedID=p.TaxonID)', FALSE, FALSE);
        $this->db->where('p.IsCurrent', 1);
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if ($row['IsUpdated'] && !$row['Author']) {
                    $this->db->select("CONCAT(u.FirstName, ' ', u.LastName) AS CreatedBy,
                        DATE(p.TimestampCreated) AS DateCreated", FALSE);
                    $this->db->from('vicflora_profile p');
                    $this->db->join('users u', 'p.CreatedByID=u.UsersID');
                    $this->db->where('p.GUID', $row['GUID']);
                    $this->db->where('p.IsUpdated IS NULL', FALSE, FALSE);
                    $q = $this->db->get();
                    $r = $q->row();
                    $row['CreatedBy'] = $r->CreatedBy;
                    $row['DateCreated'] = $r->DateCreated;
                }
                $ret[] = $row;
            }
        }
        return $ret;
    }
    
    public function getCommonNames($guid) {
        $this->db->select('c.CommonNameID, c.CommonName, c.IsPreferred, c.NameUsage');
        $this->db->from('vicflora_commonname c');
        $this->db->join('vicflora_taxon t', 'c.TaxonID=t.TaxonID');
        $this->db->where('t.GUID', $guid);
        $this->db->order_by('IsPreferred', 'desc');
        $this->db->order_by('CommonName');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getApniNames($guid) {
        $this->db->select('a.ApniID, t.GUID, a.MatchType, a.ApniNo, a.APNIFullNameWithAuthor, a.IsVerified');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_apni a', 'n.NameID=a.NameID');
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getTaxonImages($guid) {
        $this->db->select('f.GUID, f.Filename');
        $this->db->from('vicflora_figure f');
        $this->db->join('vicflora_taxon t', 'coalesce(f.AcceptedID, f.TaxonID)=t.TaxonID', FALSE, FALSE);
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getThumbnails($guid) {
        $this->db->select('i.CumulusRecordID,i.GUID');
        $this->db->from('cumulus_image i');
        $this->db->join('vicflora_taxon t', 'i.AcceptedID=t.AcceptedID');
        $this->db->join('vicflora_taxon p', 't.ParentID=p.TaxonID');
        $this->db->where("(t.GUID='$guid' OR (p.GUID='$guid' AND p.RankID=220))", FALSE, FALSE);
        $this->db->where('PixelXDimension >', 0);
        $this->db->where('i.ThumbnailUrlEnabled', true);
        $this->db->where('i.PreviewUrlEnabled', true);
        $this->db->group_by('i.ImageID');
        $this->db->order_by('i.Subtype', 'ASC');
        $this->db->order_by('i.HeroImage', 'DESC');
        $this->db->order_by('i.Rating', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function getImageMetadata($guid) {
        $sql = "SELECT f.Filename, f.Volume, f.FigureNumber, f.FigureSub, n.FullName AS TaxonName, an.FullName AS AsTaxonName, f.Caption, v.Editor, v.`Year`,
            v.Title, v.Volume, v.Subtitle, v.Publisher, v.PlaceOfPublication
            FROM vicflora_figure f
            JOIN vicflora.volume v ON concat('Vol. ', f.Volume)=v.Volume
            JOIN vicflora_taxon t ON coalesce(f.AcceptedID, f.TaxonID)=t.TaxonID
            JOIN vicflora_name n ON t.NameID=n.NameID
            LEFT JOIN vicflora_taxon at ON f.TaxonID=at.TaxonID AND f.AcceptedID IS NOT NULL
            LEFT JOIN vicflora_name an ON at.NameID=an.NameID
            WHERE f.GUID='$guid'";
        $query = $this->db->query($sql);
        if ($query->num_rows()) {
            return $query->row_array();
        }
        else
            return FALSE;
    }
    
    public function getImage($guid) {
        $this->db->select("i.CumulusRecordID, 
            i.PixelXDimension, i.PixelYDimension,
            n.FullName AS ScientificName, if(t.TaxonID!=st.TaxonID, sn.FullName, NULL) AS AsName, i.Subtype, i.Caption, i.SubjectPart, i.Creator, i.RightsHolder, i.License", FALSE);
        $this->db->from('cumulus_image i');
        $this->db->join('vicflora_taxon t', 'i.AcceptedID=t.TaxonID');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxon st', 'i.TaxonID=st.TaxonID AND t.TaxonID!=st.TaxonID', 'left');
        $this->db->join('vicflora_name sn', 'st.NameID=sn.NameID', 'left');
        $this->db->where('i.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row;
        }
    }
    
    public function getHeroImage($guid) {
        $this->db->select('i.GUID, i.CumulusRecordID, i.Subtype, i.PixelXDimension, i.PixelYDimension');
        $this->db->from('cumulus_image i');
        $this->db->join('vicflora_taxon t', 'i.AcceptedID=t.TaxonID');
        $this->db->join('vicflora_taxon p', 't.ParentID=p.TaxonID');
        $this->db->where("(t.GUID='$guid' OR (p.GUID='$guid' AND p.RankID=220))");
        $this->db->where('i.PixelXDimension >', 0);
        $this->db->where('i.ThumbnailUrlEnabled', true);
        $this->db->where('i.PreviewUrlEnabled', true);
        $this->db->group_by('i.ImageID');
        $this->db->order_by('i.Subtype', 'DESC');
        $this->db->order_by('i.HeroImage', 'DESC');
        $this->db->order_by('i.Rating', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        }
        else {
            return FALSE;
        }
    }
    
    public function getKey($guid) {
        $this->db->select('n.FullName, td.Name AS Rank, k.Name, k.KeysID');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_taxon c', 't.TaxonID=c.ParentID');
        $this->db->join('vicflora_taxontreedefitem td', 'c.TaxonTreeDefItemID=td.TaxonTreeDefItemID');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('keybase.items i', 'n.FullName=i.Name');
        $this->db->join('keybase.keys k', 'i.ItemsID=k.TaxonomicScopeID');
        $this->db->where('t.TaxonomicStatus', 'Accepted');
        $this->db->where('k.ProjectsID', 10);
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row_array();
        }
        else
            return FALSE;
    }
    
    public function getTaxonDataForMap($guid) {
        $this->db->select('n.FullName, t.RankID');
        $this->db->from('vicflora_name n');
        $this->db->join('vicflora_taxon t', 't.NameID=n.NameID');
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row_array();
        }
        else {
            return FALSE;
        }
    }
    
    public function getClassificationBreadCrumbs($guid) {
        $this->db->select('tt.NodeNumber');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        $row = $query->row();
        $nodeNumber = $row->NodeNumber;
        
        $this->db->select('t.GUID, n.FullName');
        $this->db->from('vicflora_name n');
        $this->db->join('vicflora_taxon t', 'n.NameID=t.NameID');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('tt.NodeNumber <', $nodeNumber);
        $this->db->where('tt.HighestDescendantNodeNumber >=', $nodeNumber);
        $this->db->where_in('t.RankID', array(140, 180, 220));
        $this->db->order_by('t.RankID');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function getSiblingsDropdown($guid) {
        $ret = array();
        $this->db->select('ParentID');
        $this->db->from('vicflora_taxon');
        $this->db->where('GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $this->db->select('t.GUID, n.FullName');
            $this->db->from('vicflora_taxon t');
            $this->db->join('vicflora_name n', 't.NameID=n.NameID');
            $this->db->where('t.ParentID', $row->ParentID);
            $this->db->where('t.TaxonomicStatus', 'accepted');
            $this->db->where('(t.DoNotIndex IS NULL OR t.DoNotIndex != 1)', false, false);
            $this->db->order_by('n.FullName');
            $query = $this->db->get();
            if ($query->num_rows() > 1) {
                $ret[''] = 'Select sibling...';
                foreach ($query->result() as $row) {
                    $ret[$row->GUID] = $row->FullName;
                }
            }
        }
        return $ret;
    }
    
    public function getChildrenDropdown($guid) {
        $ret = array();
        $sql = "SELECT t.GUID, n.FullName
            FROM vicflora_taxon t
            JOIN vicflora_taxon p ON t.ParentID=p.TaxonID
            JOIN vicflora_name n ON t.NameID=n.NameID
            WHERE p.GUID='$guid' AND t.TaxonomicStatus='accepted'
                AND (t.DoNotIndex IS NULL OR t.DoNotIndex != 1)
            ORDER BY n.FullName";
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            $ret[''] = 'Select child...';
            foreach ($query->result() as $row) {
                $ret[$row->GUID] = $row->FullName;
            }
        }
        return $ret;
    }
}


/* End of file taxonmodel.php */
/* Location: ./models/taxonmodel.php */
