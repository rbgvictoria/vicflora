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
            t.Remarks, t.RankID, tt.NodeNumber, tt.HighestDescendantNodeNumber, tt.Depth,
            IF(p.ProfileID IS NOT NULL AND (p.AcceptedID IS NULL OR p.TaxonID=p.AcceptedID), p.ProfileID, NULL) AS UnmatchedProfile', FALSE);
        $this->db->select("distv.DistributionText AS DistV, dista.DistributionText AS DistA, distw.DistributionText AS DistW");

        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxontreedefitem td', 't.TaxonTreeDefitemID=td.TaxonTreeDefItemID');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID', 'left');
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
    
    public function getTaxonID($guid) {
        $this->db->select('TaxonID');
        $this->db->from('vicflora_taxon');
        $this->db->where('GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->TaxonID;
        }
        else {
            return FALSE;
        }
    }
    
    public function getRankID($guid) {
        $this->db->select('RankID');
        $this->db->from('vicflora_taxon');
        $this->db->where('GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->RankID;
        }
        else {
            return FALSE;
        }
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
    
    public function getGenus($guid) {
        $this->db->select("SUBSTRING(n.FullName, 1, LOCATE(' ', n.FullName)-1) AS genus", FALSE);
        $this->db->from('vicflora_name n');
        $this->db->join('vicflora_taxon t', 'n.NameID=t.NameID');
        $this->db->where('t.RankID >=', 180);
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->genus;
        }
        else {
            return FALSE;
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
            /* IF(p.SourceID IS NULL AND p.IsUpdated IS NULL, DATE(p.TimestampCreated), NULL) AS DateCreated, */
            IF(p.SourceID IS NULL OR p.SourceID=0, DATE(p.TimestampCreated), NULL) AS DateCreated,
            /* IF(p.SourceID IS NULL AND p.isUpdated IS NULL, CONCAT(u.FirstName, ' ', u.LastName), NULL) AS CreatedBy, */
            IF(p.SourceID IS NULL OR p.SourceID=0, CONCAT(u.FirstName, ' ', u.LastName), NULL) AS CreatedBy,
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
        $this->db->select('a.ApniID, t.GUID, a.GUID as ApniScientificNameID, a.MatchType, a.ApniNo, a.APNIFullNameWithAuthor, a.IsVerified');
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
    
    public function getThumbnails($nodeNumber, $highestDescendantNodeNumber, $rankID) {
        $this->db->select('i.CumulusRecordID,i.GUID');
        $this->db->from('cumulus_image i');
        $this->db->join('vicflora_taxon t', 'i.AcceptedID=t.TaxonID');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('tt.NodeNumber >=', $nodeNumber);
        $this->db->where('tt.NodeNumber <=', $highestDescendantNodeNumber);
        $this->db->where('PixelXDimension >', 0);
        $this->db->where('i.ThumbnailUrlEnabled', true);
        $this->db->where('i.PreviewUrlEnabled', true);
        $this->db->where('i.Creator IS NOT NULL', false, false);
        $this->db->group_by('i.ImageID');
        if ($rankID >= 220) {
            $this->db->order_by('i.Subtype', 'ASC');
            $this->db->order_by('i.HeroImage', 'DESC');
            $this->db->order_by('i.Rating', 'DESC');
        }
        else {
            $this->db->order_by('i.Subtype', 'DESC');
            $this->db->order_by('i.HeroImage', 'DESC');
            $this->db->order_by('i.Rating', 'DESC');
            $this->db->order_by('rand()');
        }
        $this->db->limit(12);
        $query = $this->db->get();
        $ret = array();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $ret[] = $this->getImage($row->GUID);
            }
        }
        return $ret;
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
        $this->db->select("lower(replace(i.CumulusCatalogue, ' ', '-')) as CumulusCatalogue, i.CumulusRecordID, 
            i.PixelXDimension, i.PixelYDimension,
            n.FullName AS ScientificName, if(t.TaxonID!=st.TaxonID, sn.FullName, NULL) AS AsName, 
            i.Subtype, i.Caption, i.SubjectPart, i.Creator, i.RightsHolder, i.License, i.rights,
            i.Source, i.SubjectCategory", FALSE);
        $this->db->from('cumulus_image i');
        $this->db->join('vicflora_taxon t', 'i.AcceptedID=t.TaxonID');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxon st', 'i.TaxonID=st.TaxonID AND t.TaxonID!=st.TaxonID', 'left');
        $this->db->join('vicflora_name sn', 'st.NameID=sn.NameID', 'left');
        $this->db->where('i.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $imgSize = $this->imageSize($row->Subtype, $row->PixelXDimension, $row->PixelYDimension);
            $imgCaption = $this->imageCaption($row);
            $image = (object) array(
                'id' => $row->CumulusRecordID,
                'catalog' => $row->CumulusCatalogue,
                'guid' => $guid,
                'alt' => $imgCaption->alt,
                'caption' => $imgCaption->caption,
                'width' => $imgSize->width,
                'height' => $imgSize->height,
                'maxsize' => ($row->Subtype === 'Illustration') ? $imgSize->size : 1024
            );
            return $image;
        }
        else {
            return FALSE;
        }
    }
    
    private function imageSize($subtype, $width, $height) {
        $sizeObj = new stdClass();
        $sizeObj->width = $width;
        $sizeObj->height = $height;
        if ($subtype == 'Illustration') {
            $sizeObj->width = $width / 2;
            $sizeObj->height = $height / 2;
        }    

        if ($width > $height) {
            if ($width > 1024) {
                $sizeObj->height = $height * (1024 / $width);
                $sizeObj->width = 1024;
            }
            $sizeObj->size = $width;
        }
        else {
            if ($height > 1024) {
                $sizeObj->width = $width * (1024 / $height);
                $sizeObj->height = 1024;
            }
            $sizeObj->size = $height;
        }
        return $sizeObj;
    }
    
    private function imageCaption($data) {
        $scientificName = FALSE;
        if (isset($data->ScientificName)) {
            $scientificName = '<i>' . $data->ScientificName . '</i>';
            if ($data->AsName) {$scientificName .= ' (as <i>' . $data->AsName . '</i>)';}
            $search = array(' subsp. ', ' var. ', ' f. ');
            $replace = array(
                '</i> subsp. <i>',
                '</i> var. <i>',
                '</i> f. <i>'
            );
            $scientificName = str_replace($search, $replace, $scientificName);
        }
        
        if (substr($data->License, 0, 5) === 'CC BY') {
            $bits = explode(' ', $data->License);
            $url = 'https://creativecommons.org/licenses/';
            $url .= strtolower($bits[1]);
            $url .= (isset($bits[2])) ? '/' . $bits[2] : '/4.0';
            if (isset($bits[3])) {$url .= '/' .strtolower ($bits[3]);}
            $license = "<a href='$url'>$data->License</a>";
        }
        elseif ($data->License == 'All rights reserved') {
            $license = 'all rights reserved';
        }
        elseif ($data->SubjectCategory == 'Flora of the Otway Plain and Ranges Plate') {
            $license = 'not to be reproduced without prior permission from CSIRO Publishing.';
        }
        else {
            $license = "<a href='https://creativecommons.org/licenses/by/4.0'>CC BY 4.0</a>";
        }
        
        $alt = ($scientificName) ? $scientificName : $data->Caption;
        
        $caption = ($scientificName) ? $scientificName : '. ';
        $caption .= (trim($data->Caption) && $scientificName) ? '. ' : '';
        $caption .= (trim($data->Caption)) ? trim($data->Caption) . ' ' : '';
        $caption .= '<br/>';
        if ($data->Source) {
            $caption .= '<b>Source:</b> ' . $data->Source . '<br/>';
        }
        $caption .= ($data->Subtype === 'Illustration') ? 'Illustration: ' : 'Photo: ';
        $caption .= $data->Creator . ', &copy ' . date('Y') . ' ';
        if ($data->RightsHolder == 'Royal Botanic Gardens Victoria') {
            $data->RightsHolder = 'Royal Botanic Gardens Board';
        }
        $caption .= ($data->RightsHolder) ? $data->RightsHolder : 'Royal Botanic Gardens Board';
        $caption .= ', ' . $license . '.';
        if ($data->SubjectCategory === 'Flora of the Otway Plain and Ranges plate') {
            $caption .= '<br/>' . $data->rights;
        }
        
        return (object) array(
            'alt' => $alt,
            'caption' => $caption
        );
    }
    
    public function getHeroImage($nodeNumber, $highestDescendantNodeNumber, $rankID) {
        $this->db->select("i.GUID, lower(replace(i.CumulusCatalogue, ' ', '-')) as CumulusCatalogue, i.CumulusRecordID, i.Subtype, i.PixelXDimension, i.PixelYDimension", false);
        $this->db->from('cumulus_image i');
        $this->db->join('vicflora_taxon t', 'i.AcceptedID=t.TaxonID');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('tt.NodeNumber >=', $nodeNumber);
        $this->db->where('tt.NodeNumber <=', $highestDescendantNodeNumber);
        $this->db->where('i.PixelXDimension >', 0);
        $this->db->where('i.ThumbnailUrlEnabled', true);
        $this->db->where('i.PreviewUrlEnabled', true);
        $this->db->where('i.Creator IS NOT NULL', false, false);
        $this->db->group_by('i.ImageID');
        $this->db->order_by('i.Subtype', 'DESC');
        $this->db->order_by('i.HeroImage', 'DESC');
        $this->db->order_by('i.Rating', 'DESC');
        $this->db->order_by('rand()');
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
        $this->db->select('n.FullName, td.Name AS Rank, k.Title, k.KeysID');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_taxon c', 't.TaxonID=c.ParentID');
        $this->db->join('vicflora_taxontreedefitem td', 'c.TaxonTreeDefItemID=td.TaxonTreeDefItemID');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('keybase.items i', 'n.FullName=i.Name');
        $this->db->join('keybase.keys k', 'i.ItemsID=k.TaxonomicScopeID');
        $this->db->where('c.TaxonomicStatus', 'Accepted');
        $this->db->where('(c.DoNotIndex IS NULL OR c.DoNotIndex!=1)', FALSE, FALSE);
        $this->db->where('k.ProjectsID', 10);
        $this->db->where('t.GUID', $guid);
        $this->db->order_by('td.RankID');
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row_array();
        }
        else {
            return FALSE;
        }
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
    
    public function getClassificationBreadCrumbs($nodeNumber) {
        $this->db->select('t.GUID, n.FullName');
        $this->db->from('vicflora_name n');
        $this->db->join('vicflora_taxon t', 'n.NameID=t.NameID');
        $this->db->join('vicflora_taxontree tt', 't.TaxonID=tt.TaxonID');
        $this->db->where('tt.NodeNumber <', $nodeNumber);
        $this->db->where('tt.HighestDescendantNodeNumber >=', $nodeNumber);
        $this->db->where_in('t.RankID', array(60, 100, 140, 180, 220));
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
    
    public function getScientificNameLink($sciName) {
        $this->db->select('t.GUID');
        $this->db->from('vicflora_name n');
        $this->db->join('vicflora_taxon t', 'n.NameID=t.NameID');
        $this->db->where("n.FullName='$sciName' OR CONCAT(SUBSTRING(n.FullName, 1, 1), '. ', SUBSTRING(n.FullName, LOCATE(' ', n.FullName)+1))='$sciName'", FALSE, FALSE);
        $this->db->where('(t.DoNotIndex IS NULL OR t.DoNotIndex=0)', FALSE, FALSE);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->GUID;
        }
        else {
            return FALSE;
        }
    }
}


/* End of file taxonmodel.php */
/* Location: ./models/taxonmodel.php */
