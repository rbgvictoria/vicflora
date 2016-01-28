<?php

require_once('taxonmodel.php');
require_once('third_party/uuid/uuid.php');

class EditTaxonModel extends TaxonModel {
    public function __construct() {
        parent::__construct();
    }
    
    public function getTaxonData($guid) {
        $this->db->select('t.GUID,
            t.TaxonID,
            t.TaxonTreeDefItemID,
            t.RankID,
            td.Name AS Rank,
            t.ParentID,
            pt.GUID AS ParentGUID,
            pt.RankID AS ParentRankID,
            pn.FullName AS ParentName,
            pn.Author AS ParentNameAuthor,
            t.AcceptedID,
            an.FullName AS AcceptedName,
            an.Author AS AcceptedNameAuthor,
            c.Source AS AcceptedNameSource,
            n.NameID,
            n.Name,
            n.FullName,
            n.Author,
            r.ReferenceID,
            r.Author AS InAuthor,
            r.ReferenceID,
            r.JournalOrBook,
            r.Series,
            r.Edition,
            r.Volume,
            r.Part,
            r.Page,
            r.PublicationYear,
            t.Sensu,
            n.NomenclaturalNote,
            t.TaxonomicStatus,
            t.OccurrenceStatus,
            t.EstablishmentMeans,
            t.Remarks,
            t.EditorNotes,
            t.DoNotIndex AS DoNotIndex');
        $this->db->select("concat_ws(' ', cb.FirstName, cb.LastName) AS CreatedBy, t.TimestampCreated,
                concat_ws(' ', mb.FirstName, mb.LastName) AS ModifiedBy, t.TimestampModified", FALSE);
        $this->db->select("distv.DistributionText AS DistV, dista.DistributionText AS DistA, distw.DistributionText AS DistW");
        
        $this->db->select('ety.EtymologyText AS Etymology');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxontreedefitem td', 't.TaxonTreeDefitemID=td.TaxonTreeDefItemID', 'left');
        $this->db->join('vicflora_taxon pt', 't.ParentID=pt.TaxonID', 'left');
        $this->db->join('vicflora_name pn', 'pt.NameID=pn.NameID', 'left');
        $this->db->join('vicflora_taxon at', 't.AcceptedID=at.TaxonID', 'left');
        $this->db->join('vicflora_name an', 'at.NameID=an.NameID', 'left');
        $this->db->join('vicflora_reference r', 'n.ProtologueID=r.ReferenceID', 'left');
        $this->db->join('vicflora_change c', 't.TaxonID=c.NameID AND t.AcceptedID=c.NewNameID 
            AND c.IsCurrent=1', 'left');
        $this->db->join('users cb', 't.CreatedByID=cb.UsersID', 'left');
        $this->db->join('users mb', 't.CreatedByID=mb.UsersID', 'left');
        
        $this->db->join('vicflora_distributionextra distv', "t.TaxonID=distv.AcceptedID AND distv.Scope='Victoria'", 'left', FALSE);
        $this->db->join('vicflora_distributionextra dista', "t.TaxonID=dista.AcceptedID AND dista.Scope='Australia'", 'left', FALSE);
        $this->db->join('vicflora_distributionextra distw', "t.TaxonID=distw.AcceptedID AND distw.Scope='World'", 'left', FALSE);
        
        $this->db->join('vicflora_etymology ety', 'n.NameID=ety.NameID', 'left');
        
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) 
            return $query->row_array();
        else
            return FALSE;
    }
    
    public function getParentData($guid) {
        $this->db->select('t.TaxonID AS ParentID,
            t.GUID AS ParentGUID,
            t.RankID AS ParentRankID,
            n.FullName AS ParentName,
            n.Author AS ParentNameAuthor');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->where('t.GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows())
            return $query->row_array();
        else
            return FALSE;
    }
    
    public function getTaxonAttributes($guid) {
        $this->db->select('a.TaxonAttributeID, a.Attribute, a.StrValue');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_taxonattribute a', 't.TaxonID=a.TaxonID');
        $this->db->where('t.GUID', $guid);
        $this->db->where_in('a.Attribute', array('Naturalised status', 'VROT', 'FFG'));
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->result_array();
        }
        else {
            return FALSE;
        }
    }
    
    function updateTaxon($guid) {
        $guids = array();
        $taxonid = $this->input->post('taxon_id');
        $nameid = $this->input->post('name_id');
        $protologueid = $this->input->post('protologue_id');
        
        $agentid = $this->session->userdata['id'];
        $date = date('Y-m-d H:i:s');

        if ($this->recordHasChanged('taxon') || $this->recordHasChanged('name') || $this->recordHasChanged('reference')
                || $this->recordHasChanged('vernacular_name') || $this->recordHasChanged('vernacular_name_del')
                || $this->recordHasChanged('distribution')) {
            // protologue
            if ($this->recordHasChanged('reference')) {
                $refArray = array(
                    'Author' => $this->input->post('in_author'),
                    'JournalOrBook' => $this->input->post('journal_or_book'),
                    'Series' => $this->input->post('series'),
                    'Edition' => $this->input->post('edition'),
                    'Volume' => $this->input->post('volume'),
                    'Part' => $this->input->post('part'),
                    'Page' => $this->input->post('page'),
                    'PublicationYear' => $this->input->post('publication_year'),
                    'Subject' => 'namePublishedIn',
                    'TimestampModified' => $date
                );
                foreach ($refArray as $key => $value) {
                    $refArray[$key] = ($value) ? $value : NULL;
                }

                if ($protologueid) {
                    $refArray['ModifiedByID'] = $agentid;
                    $this->db->select('Version');
                    $this->db->from('vicflora_reference');
                    $this->db->where('ReferenceID', $protologueid);
                    $query = $this->db->get();
                    $row = $query->row();
                    $refArray['Version'] = $row->Version + 1;
                    $refArray['ModifiedByID'] = $agentid;
                    $this->db->where('ReferenceID', $protologueid);
                    $this->db->update('vicflora_reference', $refArray);
                }
                else {
                    $this->db->select('MAX(ReferenceID)+1 AS NewProtologueID', FALSE);
                    $this->db->from('vicflora_reference');
                    $query = $this->db->get();
                    $row = $query->row();
                    $protologueid = $row->NewProtologueID;
                    $refArray['ReferenceID'] = $protologueid;
                    $refArray['GUID'] = UUID::v4();
                    $refArray['Version'] = 0;
                    $refArray['TimestampCreated'] = $date;
                    $refArray['CreatedByID'] = $agentid;
                    $this->db->insert('vicflora_reference', $refArray);
                    
                }
            }

            // name
            if ($this->recordHasChanged('name') || $this->recordHasChanged('reference')) {
                $nameArr = array(
                    'Name' => $this->input->post('name'),
                    'FullName' => $this->input->post('full_name'),
                    'Author' => $this->input->post('author'),
                    'NomenclaturalNote' => $this->input->post('nomenclatural_status'),
                    'TimestampModified' => date('Y-m-d h:i:s'),
                    'ProtologueID' => $protologueid
                );
                foreach ($nameArr as $key => $value) {
                    $nameArr[$key] = ($value) ? $value : NULL;
                }


                if ($nameid) {
                    $nameArr['ModifiedByID'] = $this->session->userdata['id'];

                    $this->db->select('Version');
                    $this->db->from('vicflora_name');
                    $this->db->where('NameID', $nameid);
                    $query = $this->db->get();
                    $row = $query->row();
                    $nameArr['Version'] = $row->Version + 1;
                    $nameArr['ModifiedByID'] = $agentid;
                    $this->db->where('NameID', $nameid);
                    $this->db->update('vicflora_name', $nameArr);
                }
                else {
                    $this->db->select('MAX(NameID)+1 AS NewNameID', FALSE);
                    $this->db->from('vicflora_name');
                    $query = $this->db->get();
                    $row = $query->row();
                    $nameid = $row->NewNameID;
                    $nameArr['NameID'] = $nameid;
                    $nameArr['GUID'] = UUID::v4();
                    $nameArr['Version'] = 0;
                    $nameArr['TimestampCreated'] = $date;
                    $nameArr['CreatedByID'] = $agentid;
                    $this->db->insert('vicflora_name', $nameArr);
                }
            }
            // taxon
            $taxstatus = $this->input->post('taxonomic_status');

            $taxonArr = array(
                'ParentID' => $this->input->post('parent_id'),
                'TaxonTreeDefItemID' => $this->input->post('taxon_tree_def_item_id'),
                'RankID' => $this->getRankID($this->input->post('taxon_tree_def_item_id')),
                'NameID' => $nameid,
                'Sensu' => $this->input->post('sensu'),
                'AcceptedID' => $this->input->post('accepted_name_id'),
                'TaxonomicStatus' => $taxstatus,
                'OccurrenceStatus' => ($taxstatus == 'accepted' || $this->input->post('occurrence_status') == 'excluded') 
                    ? $this->input->post('occurrence_status') : NULL,
                'EstablishmentMeans' => ($taxstatus == 'accepted') ? $this->input->post('establishment_means') : NULL,
                'Remarks' => $this->input->post('taxon_remarks'),
                'EditorNotes' => $this->input->post('editor_notes'),
                'TimestampModified' => $date,
            );
            foreach ($taxonArr as $key => $value) {
                $taxonArr[$key] = ($value) ? $value : NULL;
            }

            if ($taxonid) {
                $this->db->select('Version');
                $this->db->from('vicflora_taxon');
                $this->db->where('TaxonID', $taxonid);
                $query = $this->db->get();
                $row = $query->row();
                $taxonArr['Version'] = $row->Version + 1;
                $taxonArr['ModifiedByID'] = $agentid;
                $this->db->where('TaxonID', $taxonid);
                $this->db->update('vicflora_taxon', $taxonArr);
                
                // If a taxon wasn't accepted before and is accepted now,
                // we need to add it to the taxon tree
                if ($this->input->post('taxonomic_status') == 'accepted' && 
                        ($this->input->post('taxonomic_status_old') != 'accepted' || 
                        !$this->input->post('taxonomic_status_old'))) {
                    $this->addToTaxonTree($taxonid, $this->input->post('parent_id'));
                }
                
                // Likewise, if a taxon is no longer accepted, it needs to
                // be removed from the taxon tree
                elseif (($this->input->post('taxonomic_status') != 'accepted' || 
                        !$this->input->post('taxonomic_status')) && 
                        $this->input->post('taxonomic_status_old') == 'accepted') {
                    $this->deleteFromTaxonTree($taxonid);
                }
                
                // If the parent_id has changed, we need to move this taxon and all 
                // its children in the taxon tree
                if ($this->input->post('parent_id') != $this->input->post('parent_id_old')) {
                    $guids = $this->moveInTaxonTree($taxonid, $this->input->post('parent_id'));
                }

            }
            else {
                $this->db->select('MAX(TaxonID)+1 AS NewTaxonID', FALSE);
                $this->db->from('vicflora_taxon');
                $query = $this->db->get();
                $row = $query->row();
                $taxonid = $row->NewTaxonID;
                $taxonArr['TaxonID'] = $taxonid;
                if ($this->input->post('taxonomic_status') == 'accepted')
                    $taxonArr['AcceptedID'] = $taxonid;
                $guid = UUID::v4();
                $taxonArr['GUID'] = $guid;
                $taxonArr['Version'] = 0;
                $taxonArr['TimestampCreated'] = $date;
                $taxonArr['CreatedByID'] = $agentid;
                $this->db->insert('vicflora_taxon', $taxonArr);
                
                // New accepted taxa need to be added to the taxon tree
                if ($this->input->post('taxonomic_status') == 'accepted') {
                    $this->addToTaxonTree($taxonid, $this->input->post('parent_id'));
                }
            }
            
            // insert change record if accepted name has changed
            if ($this->input->post('accepted_name_id') != $this->input->post('accepted_name_id_old')) {
                $this->db->where('NameID', $this->input->post('taxon_id'));
                $this->db->update('vicflora_change', array('IsCurrent' => 0));
                
                $ins = array(
                    'NameID' => $this->input->post('taxon_id'),
                    'TimestampCreated' => $date,
                    'NewNameID' => $this->input->post('accepted_name_id'),
                    'Source' => ($this->input->post('accepted_name_source')) 
                        ? $this->input->post('accepted_name_source') : NULL,
                    'CreatedByID' => $agentid,
                    'ChangeType' => $this->input->post('taxonomic_status')
                );
                $this->db->insert('vicflora_change', $ins);
            }
                


            // attributes
            if ($taxstatus != 'accepted' && ($this->input->post('naturalised_status_old') 
                    || $this->input->post('vrot_old'))) { // delete attribute records if taxon is no longer accepted
                $this->db->where('TaxonID', $taxonid);
                $this->db->where_in('Attribute', array('establishmentMeans', 'VROT'));
                $this->db->delete('vicflora_taxonattribute');
            }

            if ($this->input->post('naturalised_status') 
                    && $this->input->post('naturalised_status') != $this->input->post('naturalised_status_old')) {
                $attrArr = array(
                    'TaxonID' => $taxonid,
                    'Attribute' => 'Naturalised status',
                    'StrValue' => $this->input->post('naturalised_status'),
                    'TimestampModified' => $date
                );
                foreach ($attrArr as $key => $value) {
                    $attrArr[$key] = ($value) ? $value : NULL;
                }

                if ($this->input->post('naturalised_attribute_id')) {
                    $this->db->select('Version');
                    $this->db->from('vicflora_taxonattribute');
                    $this->db->where('TaxonAttributeID', $this->input->post('naturalised_attribute_id'));
                    $query = $this->db->get();
                    $row = $query->row();
                    $attrArr['Version'] = $row->Version + 1;
                    $attrArr['ModifiedByID'] = $agentid;
                    $this->db->where('TaxonAttributeID', $this->input->post('naturalised_attribute_id'));
                    $this->db->update('vicflora_taxonattribute', $attrArr);
                }
                else {
                    $this->db->select('MAX(TaxonAttributeID)+1 AS NewTaxonAttributeID', FALSE);
                    $this->db->from('vicflora_taxonattribute');
                    $query = $this->db->get();
                    $row = $query->row();
                    $attrArr['TaxonAttributeID'] = $row->NewTaxonAttributeID;
                    $attrArr['TimestampCreated'] = $date;
                    $attrArr['Version'] = 0;
                    $attrArr['CreatedByID'] = $agentid;
                    $this->db->insert('vicflora_taxonattribute', $attrArr);
                }
            }
            elseif ($this->input->post('naturalised_attribute_id') && !$this->input->post('naturalised')) {
                $this->db->where('TaxonAttributeID', $this->input->post('naturalised_attribute_id'));
                $this->db->delete('vicflora_taxonattribute');
            }

            if ($this->input->post('vrot') 
                    && $this->input->post('vrot') != $this->input->post('vrot_old')) {
                $attrArr = array(
                    'TaxonID' => $taxonid,
                    'Attribute' => 'VROT',
                    'StrValue' => $this->input->post('vrot'),
                    'TimestampModified' => $date
                );
                foreach ($attrArr as $key => $value) {
                    $attrArr[$key] = ($value) ? $value : NULL;
                }

                if ($this->input->post('vrot_attribute_id')) {
                    $this->db->select('Version');
                    $this->db->from('vicflora_taxonattribute');
                    $this->db->where('TaxonAttributeID', $this->input->post('vrot_attribute_id'));
                    $query = $this->db->get();
                    $row = $query->row();
                    $attrArr['Version'] = $row->Version + 1;
                    $attrArr['ModifiedByID'] = $agentid;
                    $this->db->where('TaxonAttributeID', $this->input->post('vrot_attribute_id'));
                    $this->db->update('vicflora_taxonattribute', $attrArr);
                }
                else {
                    $this->db->select('MAX(TaxonAttributeID)+1 AS NewTaxonAttributeID', FALSE);
                    $this->db->from('vicflora_taxonattribute');
                    $query = $this->db->get();
                    $row = $query->row();
                    $attrArr['TaxonAttributeID'] = $row->NewTaxonAttributeID;
                    $attrArr['TimestampCreated'] = $date;
                    $attrArr['Version'] = 0;
                    $attrArr['CreatedByID'] = $agentid;
                    $this->db->insert('vicflora_taxonattribute', $attrArr);
                }
            }
            elseif ($this->input->post('vrot_attribute_id') && !$this->input->post('vrot')) {
                $this->db->where('TaxonAttributeID', $this->input->post('vrot_attribute_id'));
                $this->db->delete('vicflora_taxonattribute');
            }

            if ($this->input->post('ffg') 
                    && $this->input->post('ffg') != $this->input->post('ffg_old')) {
                $attrArr = array(
                    'TaxonID' => $taxonid,
                    'Attribute' => 'FFG',
                    'StrValue' => $this->input->post('ffg'),
                    'TimestampModified' => $date
                );
                foreach ($attrArr as $key => $value) {
                    $attrArr[$key] = ($value) ? $value : NULL;
                }

                if ($this->input->post('ffg_attribute_id')) {
                    $this->db->select('Version');
                    $this->db->from('vicflora_taxonattribute');
                    $this->db->where('TaxonAttributeID', $this->input->post('ffg_attribute_id'));
                    $query = $this->db->get();
                    $row = $query->row();
                    $attrArr['Version'] = $row->Version + 1;
                    $attrArr['ModifiedByID'] = $agentid;
                    $this->db->where('TaxonAttributeID', $this->input->post('ffg_attribute_id'));
                    $this->db->update('vicflora_taxonattribute', $attrArr);
                }
                else {
                    $this->db->select('MAX(TaxonAttributeID)+1 AS NewTaxonAttributeID', FALSE);
                    $this->db->from('vicflora_taxonattribute');
                    $query = $this->db->get();
                    $row = $query->row();
                    $attrArr['TaxonAttributeID'] = $row->NewTaxonAttributeID;
                    $attrArr['TimestampCreated'] = $date;
                    $attrArr['Version'] = 0;
                    $attrArr['CreatedByID'] = $agentid;
                    $this->db->insert('vicflora_taxonattribute', $attrArr);
                }
            }
            elseif ($this->input->post('ffg_attribute_id') && !$this->input->post('ffg')) {
                $this->db->where('TaxonAttributeID', $this->input->post('ffg_attribute_id'));
                $this->db->delete('vicflora_taxonattribute');
            }
            
            
            // Common names
            if ($this->input->post('common_name')) {
                $cnames = $this->input->post('common_name');
                $cids = $this->input->post('common_name_id');
                $pref = $this->input->post('preferred');
                $usages = $this->input->post('usage');
                $del = $this->input->post('delete');
                
                foreach ($cnames as $key => $cname) {
                    // delete
                    if (isset($del[$key]) && $del[$key] && isset($cids[$key])) {
                        $this->db->where('CommonNameID', $cids[$key]);
                        $this->db->delete('vicflora_commonname');
                    }
                    
                    // update
                    if ($this->recordHasChanged('vernacular_name') && isset($cids[$key]) && !isset($del[$key])) {
                        $this->db->select('Version');
                        $this->db->from('vicflora_commonname');
                        $this->db->where('CommonNameID', $cids[$key]);
                        $query = $this->db->get();
                        $row = $query->row();
                        $version = $row->Version;
                        
                        $upd = array(
                            'TimestampModified' => $date,
                            'Version' => $version + 1,
                            'CommonName' => $cname,
                            'IsPreferred' => (isset($pref[$key])) ? 1 : 0,
                            'NameUsage' => ($usages[$key]) ? $usages[$key] : NULL,
                            'ModifiedByID' => $agentid
                        );
                        
                        $this->db->where('CommonNameID', $cids[$key]);
                        $this->db->update('vicflora_commonname', $upd);
                    }
                    
                    // insert
                    if (!isset($cids[$key]) && !isset($del[$key])) {
                        $ins = array(
                            'TimestampCreated' => $date,
                            'TimestampModified' => $date,
                            'Version' => 1,
                            'CommonName' => $cname,
                            'IsPreferred' => (isset($pref[$key])) ? 1 : 0,
                            'NameUsage' => ($usages[$key]) ? $usages[$key] : NULL,
                            'TaxonID' => $taxonid,
                            'CreatedByID' => $agentid
                        );
                        
                        $this->db->insert('vicflora_commonname', $ins);
                    }
                    
                }
            }
            
            if ($this->recordHasChanged('distribution')) {
                if ($this->input->post('distv') != $this->input->post('distv_old')) {
                    $this->db->select('DistributionExtraID, Version');
                    $this->db->from('vicflora_distributionextra');
                    $this->db->where('AcceptedID', $taxonid);
                    $this->db->where('Scope', 'Victoria');
                    $query = $this->db->get();
                    
                    if ($query->num_rows()) {
                        $row = $query->row();
                        $updArray = array(
                            'TimestampModified' => date('Y-m-d H:i:s'),
                            'Version' => $row->Version + 1,
                            'Scope' => 'Victoria',
                            'DistributionText' => $this->input->post('distv'),
                            'TaxonID' => $taxonid,
                            'ModifiedByID' => $agentid
                        );
                        $this->db->where('DistributionExtraID', $row->DistributionExtraID);
                        $this->db->update('vicflora_distributionextra', $updArray);
                    }
                    else {
                        $insArray = array(
                            'TimestampCreated' => date('Y-m-d H:i:s'),
                            'TimestampModified' => date('Y-m-d H:i:s'),
                            'Version' => 1,
                            'Scope' => 'Victoria',
                            'DistributionText' => $this->input->post('distv'),
                            'TaxonID' => $taxonid,
                            'AcceptedID' => $taxonid,
                            'CreatedByID' => $agentid,
                            'ModifiedByID' => $agentid
                        );
                        $this->db->insert('vicflora_distributionextra', $insArray);
                    }
                }

                if ($this->input->post('dista') != $this->input->post('dista_old')) {
                    $this->db->select('DistributionExtraID, Version');
                    $this->db->from('vicflora_distributionextra');
                    $this->db->where('AcceptedID', $taxonid);
                    $this->db->where('Scope', 'Australia');
                    $query = $this->db->get();
                    
                    if ($query->num_rows()) {
                        $row = $query->row();
                        $updArray = array(
                            'TimestampModified' => date('Y-m-d H:i:s'),
                            'Version' => $row->Version + 1,
                            'Scope' => 'Australia',
                            'DistributionText' => $this->input->post('dista'),
                            'TaxonID' => $taxonid,
                            'ModifiedByID' => $agentid
                        );
                        $this->db->where('DistributionExtraID', $row->DistributionExtraID);
                        $this->db->update('vicflora_distributionextra', $updArray);
                    }
                    else {
                        $insArray = array(
                            'TimestampCreated' => date('Y-m-d H:i:s'),
                            'TimestampModified' => date('Y-m-d H:i:s'),
                            'Version' => 1,
                            'Scope' => 'Australia',
                            'DistributionText' => $this->input->post('dista'),
                            'TaxonID' => $taxonid,
                            'AcceptedID' => $taxonid,
                            'CreatedByID' => $agentid,
                            'ModifiedByID' => $agentid
                        );
                        $this->db->insert('vicflora_distributionextra', $insArray);
                    }
                }

                if ($this->input->post('distw') != $this->input->post('distw_old')) {
                    $this->db->select('DistributionExtraID, Version');
                    $this->db->from('vicflora_distributionextra');
                    $this->db->where('AcceptedID', $taxonid);
                    $this->db->where('Scope', 'World');
                    $query = $this->db->get();
                    
                    if ($query->num_rows()) {
                        $row = $query->row();
                        $updArray = array(
                            'TimestampModified' => date('Y-m-d H:i:s'),
                            'Version' => $row->Version + 1,
                            'Scope' => 'World',
                            'DistributionText' => $this->input->post('distw'),
                            'TaxonID' => $taxonid,
                            'ModifiedByID' => $agentid
                        );
                        $this->db->where('DistributionExtraID', $row->DistributionExtraID);
                        $this->db->update('vicflora_distributionextra', $updArray);
                    }
                    else {
                        $insArray = array(
                            'TimestampCreated' => date('Y-m-d H:i:s'),
                            'TimestampModified' => date('Y-m-d H:i:s'),
                            'Version' => 1,
                            'Scope' => 'World',
                            'DistributionText' => $this->input->post('distw'),
                            'TaxonID' => $taxonid,
                            'AcceptedID' => $taxonid,
                            'CreatedByID' => $agentid,
                            'ModifiedByID' => $agentid
                        );
                        $this->db->insert('vicflora_distributionextra', $insArray);
                    }
                }
            }
            
            
            
            
            if (!$guids)
                $guids[] = $guid;
            return $guids;
        }
        else 
            return FALSE;
    }
    
    function recordHasChanged($which) {
        $ret = FALSE;

        switch ($which) {
            case 'reference':
                if ($this->input->post('in_author') != $this->input->post('in_author_old'))
                    $ret = TRUE;
                if ($this->input->post('journal_or_book') != $this->input->post('journal_or_book_old'))
                    $ret = TRUE;
                if ($this->input->post('series') != $this->input->post('series_old'))
                    $ret = TRUE;
                if ($this->input->post('edition') != $this->input->post('edition_old'))
                    $ret = TRUE;
                if ($this->input->post('volume') != $this->input->post('volume_old'))
                    $ret = TRUE;
                if ($this->input->post('part') != $this->input->post('part_old'))
                    $ret = TRUE;
                if ($this->input->post('page') != $this->input->post('page_old'))
                    $ret = TRUE;
                if ($this->input->post('publication_year') != $this->input->post('publication_year_old'))
                    $ret = TRUE;
                break;
                
            case 'name':
                if ($this->input->post('name') != $this->input->post('name_old'))
                    $ret = TRUE;
                if ($this->input->post('full_name') != $this->input->post('full_name_old'))
                    $ret = TRUE;
                if ($this->input->post('author') != $this->input->post('author_old'))
                    $ret = TRUE;
                if ($this->input->post('nomenclatural_status') != $this->input->post('nomenclatural_status_old'))
                    $ret = TRUE;
                break;
                
            case 'taxon':
                if ($this->input->post('taxon_tree_def_item_id') != $this->input->post('taxon_tree_def_item_id-old'))
                    $ret = TRUE;
                if ($this->input->post('sensu') != $this->input->post('sensu_old'))
                    $ret = TRUE;
                if ($this->input->post('accepted_name_id') != $this->input->post('accepted_name_id_old'))
                    $ret = TRUE;
                if ($this->input->post('taxonomic_status') != $this->input->post('taxonomic_status_old'))
                    $ret = TRUE;
                if ($this->input->post('occurrence_status') != $this->input->post('occurrence_status_old'))
                    $ret = TRUE;
                if ($this->input->post('establishment_means') != $this->input->post('establishment_means_old'))
                    $ret = TRUE;
                if ($this->input->post('vrot') != $this->input->post('vrot_old'))
                    $ret = TRUE;
                if ($this->input->post('ffg') != $this->input->post('ffg_old'))
                    $ret = TRUE;
                if ($this->input->post('naturalised_status') != $this->input->post('naturalised_status_old'))
                    $ret = TRUE;
                if ($this->input->post('taxon_remarks') != $this->input->post('taxon_remarks_old'))
                    $ret = TRUE;
                if ($this->input->post('editor_notes') != $this->input->post('editor_notes_old'))
                    $ret = TRUE;
                break;
                
            case 'vernacular_name':
                foreach ($this->input->post('common_name') as $key=>$value) {
                    if (!isset($_POST['common_name_old'][$key]))
                        $ret = TRUE;
                    else {
                        if ($value != $_POST['common_name_old'][$key])
                            $ret = TRUE;
                        if ($_POST['usage'][$key] != $_POST['common_name_old'][$key])
                            $ret = TRUE;
                        if (($_POST['preferred_old'] && !isset($_POST['preferred'][$key]))
                                || (!$_POST['preferred_old'] && isset($_POST['preferred'][$key])))
                            $ret = TRUE;
                    }
                }
                break;
                
            case 'vernacular_name_del':
                if ($this->input->post('delete'))
                    $ret = TRUE;
                break;
                
            case 'distribution':
                if ($this->input->post('distv') != $this->input->post('distv_old'))
                    $ret = TRUE;
                if ($this->input->post('dista') != $this->input->post('dista_old'))
                    $ret = TRUE;
                if ($this->input->post('distw') != $this->input->post('distw_old'))
                    $ret = TRUE;
                break;
                
            default:
                break;
        }
        return $ret;
        
    }
    
    /**
     *  
     * 
     */
    private function addToTaxonTree($taxonid, $parentid, $children=FALSE) {
        // Find the node number and highest descendant node number of the parent
        $this->db->select('NodeNumber, HighestDescendantNodeNumber, Depth');
        $this->db->from('vicflora_taxontree');
        $this->db->where('TaxonID', $parentid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $node= $row->NodeNumber;
            $highestnode = $row->HighestDescendantNodeNumber;
            $n = ($children) ? count($children) : 1;
            
            // Raise the node number and highest descendant node numbers of records 
            // for which the node number is higher than that of the parent of the new 
            // taxon by the number of taxa to be inserted
            $update = "UPDATE vicflora_taxontree
                SET NodeNumber=NodeNumber+$n, HighestDescendantNodeNumber=HighestDescendantNodeNumber+$n
                WHERE NodeNumber>$highestnode";
            $this->db->query($update);
            
            // raise the highest descendant node number of the ancestors by the number
            // of taxa to be inserted
            $update = "UPDATE vicflora_taxontree
                SET HighestDescendantNodeNumber=HighestDescendantNodeNumber+$n
                WHERE NodeNumber<=$node AND HighestDescendantNodeNumber>=$highestnode";
            $this->db->query($update);
            
            // insert the new taxa
            
            $taxonids = ($children) ? $children : array($taxonid);
            
            // The elements in the $children array are already in the right order
            // and include the parent/ancestor. If this hadn't been the case we
            // would need to take care of that here.
            
            foreach ($taxonids as $i => $id) {
                $j = $i+1;
                $ins = array(
                    'TaxonID' => $id,
                    'NodeNumber' => $highestnode+$j,
                    'HighestDescendantNodeNumber' => $highestnode+$n,
                    'Depth' => $row->Depth+1,
                    'TimestampCreated' => date('Y-m-d H:i:s')
                );
                $this->db->insert('vicflora_taxontree', $ins);
            }
        }
    }
    
    public function deleteTaxon($guid) {
        $this->db->select('TaxonID, NameID');
        $this->db->from('vicflora_taxon');
        $this->db->where('GUID', $guid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $taxonid = $row->TaxonID;
            $nameid = $row->NameID;
            
            $this->db->select('TaxonID');
            $this->db->from('vicflora_taxon');
            $this->db->where("(ParentID=$taxonid OR AcceptedID=$taxonid)", FALSE, FALSE);
            $this->db->where('TaxonID !=', $taxonid);
            $query = $this->db->get();
            if ($query->num_rows() == 0) {
                $this->db->where('TaxonID', $taxonid);
                $this->db->delete('vicflora_taxonattribute');

                $this->deleteFromTaxonTree($taxonid);
                
                $this->db->where('TaxonID', $taxonid);
                $this->db->update('vicflora_taxon', array('AcceptedID' => NULL));

                $this->db->where('TaxonID', $taxonid);
                $this->db->delete('vicflora_taxon');

                // name
                $this->db->select('TaxonID');
                $this->db->from('vicflora_taxon');
                $this->db->where('NameID', $nameid);
                $query = $this->db->get();

                if ($query->num_rows() == 0) {
                    $this->db->select('ProtologueID');
                    $this->db->from('vicflora_name');
                    $this->db->where('NameID', $nameid);
                    $query = $this->db->get();
                    $row = $query->row();
                    $refid = $row->ProtologueID;

                    $this->db->where('NameID', $nameid);
                    $this->db->delete('vicflora_name');

                    if ($refid) {
                        $this->db->where('ReferenceID', $refid);
                        $this->db->delete('vicflora_reference');
                    }
                }
            }
        }
    }
    
    private function deleteFromTaxonTree($taxonid, $children=FALSE) {
        // find node number and highest descendant node number
        $this->db->select('NodeNumber, HighestDescendantNodeNumber');
        $this->db->from('vicflora_taxontree');
        $this->db->where('TaxonID', $taxonid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            $node = $row->NodeNumber;
            $highestnode = $row->HighestDescendantNodeNumber;
            $n = ($children) ? count($children) : 1;
            $taxonids = ($children) ? implode(',', $children) : $taxonid;
            
            // delete taxon
            $delete = "DELETE FROM vicflora_taxontree WHERE TaxonID IN ($taxonids)";
            $this->db->query($delete);

            // decrement higher node numbers
            $update = "UPDATE vicflora_taxontree
                SET NodeNumber=NodeNumber-$n
                WHERE NodeNumber>$node";
            $this->db->query($update);

            // decrement highest descendant node numbers
            $update = "UPDATE vicflora_taxontree
                SET HighestDescendantNodeNumber=HighestDescendantNodeNumber-$n
                WHERE HighestDescendantNodeNumber>$highestnode";
            $this->db->query($update);
        }
    }
    
    private function moveInTaxonTree($taxonid, $newparentid) {
        // Get this taxon and its children
        $this->db->select('NodeNumber, HighestDescendantNodeNumber');
        $this->db->from('vicflora_taxontree');
        $this->db->where('TaxonID', $taxonid);
        $query = $this->db->get();
        $row = $query->row();
        $node = $row->NodeNumber;
        $highestnode = $row->HighestDescendantNodeNumber;
        
        $this->db->select('t.TaxonID, t.GUID');
        $this->db->from('vicflora_taxontree tt');
        $this->db->join('vicflora_taxon t', 'tt.TaxonID=t.TaxonID');
        $this->db->where('tt.NodeNumber >=', $node);
        $this->db->where('tt.HighestDescendantNodeNumber <=', $highestnode);
        $this->db->order_by('tt.NodeNumber');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $guids = array();
            $taxonids = array();
            foreach ($query->result() as $row) {
                $guids[] = $row->GUID;
                $taxonids[] = $row->TaxonID;
            }
            
            // Delete taxa
            $this->deleteFromTaxonTree($taxonid, $taxonids);
            
            // Insert taxa in new position
            $this->addToTaxonTree($taxonid, $newparentid, $taxonids);
            
            // Return the array with GUIDs, as the SOLR index needs to
            // be updated for all child taxa as well.
            return $guids;
        }
    }
    
    public function editprofile($profileid, $profile, $minoredit=FALSE) {
        if ($minoredit) {
            $this->db->select('Version');
            $this->db->from('vicflora_profile');
            $this->db->where('ProfileID', $profileid);
            $query = $this->db->get();
            $row = $query->row();
            $version = $row->Version + 1;

            $upd = array(
                'Profile' => $profile,
                'TimestampModified' => date('Y-m-d H:i:s'),
                'Version' => $version,
                'ModifiedByID' => $this->session->userdata['id']
            );
            $this->db->where('ProfileID', $profileid);
            $this->db->update('vicflora_profile', $upd);
        }
        else {
            $this->db->select('AcceptedID, GUID, TaxonID, TaxonomicStatus, SourceID');
            $this->db->from('vicflora_profile');
            $this->db->where('ProfileID', $profileid);
            $query = $this->db->get();
            $row = $query->row();
            
            // Set IsCurrent for current profile record to 0
            if ($row->AcceptedID) {
                $this->db->where('AcceptedID', $row->AcceptedID);
            }
            else 
                $this->db->where('ProfileID', $profileid);
            $this->db->update('vicflora_profile', array('IsCurrent' => 0));
            
            $acceptedid = ($this->input->post('new_accepted_id')) ? $this->input->post('new_accepted_id') : $row->AcceptedID;
            
            // Create new record for edited profile
            $ins = array(
                'TimestampCreated' => date('Y-m-d H:i:s'),
                'TimestampModified' => date('Y-m-d H:i:s'),
                'Version' => 0,
                'GUID' => $row->GUID,
                'TaxonID' => $row->TaxonID,
                'AcceptedID' => $acceptedid,
                'Profile' => $profile,
                'TaxonomicStatus' => $row->TaxonomicStatus,
                'SourceID' => $row->SourceID,
                'IsCurrent' => 1,
                'IsUpdated' => 1,
                'CreatedByID' => $this->session->userdata['id']
            );
            $this->db->insert('vicflora_profile', $ins);
        }
    }
    
    public function newprofile($taxonid, $profile) {
        // Set IsCurrent=0 for all profile records for this taxon
        $this->db->where('AcceptedID', $taxonid);
        $this->db->update('vicflora_profile', array('IsCurrent' => 0));
        
        //
        $ins = array(
            'TimestampCreated' => date('Y-m-d H:i:s'),
            'TimestampModified' => date('Y-m-d H:i:s'),
            'Version' => 0,
            'GUID' => UUID::v4(),
            'TaxonID' => $taxonid,
            'AcceptedID' => $taxonid,
            'Profile' => $profile,
            'TaxonomicStatus' => 'accepted',
            'SourceID' => NULL,
            'IsCurrent' => 1,
            'IsUpdated' => NULL,
            'CreatedByID' => $this->session->userdata['id']
        );
        $this->db->insert('vicflora_profile', $ins);
    }
    
    private function getRankID($taxontreedefitemid) {
        $this->db->select('RankID');
        $this->db->from('vicflora_taxontreedefitem');
        $this->db->where('TaxonTreeDefItemID', $taxontreedefitemid);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row();
            return $row->RankID;
        }
        else
            return FALSE;
    }
    
    public function doNotIndex($guid) {
        $this->db->where('GUID', $guid);
        $this->db->update('vicflora_taxon', array('DoNotIndex' => 1));
    }
    
    public function getProfiles($guid) {
        $ret = array();
        $sql = <<<EOT
SELECT p.ProfileID, p.GUID, p.Profile,
  if(p.AcceptedID!=p.TaxonID, sn.FullName, NULL) AS AsFullName,
  if(p.AcceptedID!=p.TaxonID, sn.Author, NULL) AS AsAuthor,
  if(p.AcceptedID!=p.TaxonID, st.GUID, NULL) AS AsGUID,
  p.TaxonomicStatus, p.IsUpdated=1 AS IsUpdated,
  IF(p.SourceID IS NULL AND p.IsUpdated IS NULL,
  DATE(p.TimestampCreated), NULL) AS DateCreated,
  IF(p.SourceID IS NULL AND p.isUpdated IS NULL,
  CONCAT(u.FirstName, ' ', u.LastName), NULL) AS CreatedBy,
  DATE(p.TimestampCreated) AS DateUpdated,
  CONCAT(u.FirstName, ' ', u.LastName) AS UpdatedBy,
  r.Author, ir.PublicationYear, r.Title, ir.Author as InAuthor,
  ir.Title AS InTitle, ir.Publisher, ir.PlaceOfPublication
FROM (`vicflora_taxon` t)
JOIN `vicflora_profile` p ON `t`.`TaxonID`=`p`.`AcceptedID`
JOIN `vicflora_taxon` st ON `p`.`TaxonID`=`st`.`TaxonID`
JOIN `vicflora_name` sn ON `st`.`NameID`=`sn`.`NameID`
LEFT JOIN `vicflora_reference` r ON `p`.`SourceID`=`r`.`ReferenceID`
LEFT JOIN `vicflora_reference` ir ON `r`.`InPublicationID`=`ir`.`ReferenceID`
LEFT JOIN `users` u ON `p`.`CreatedByID`=`u`.`UsersID`
WHERE t.GUID='$guid'
  AND `p`.`IsCurrent` =  1

UNION

SELECT p.ProfileID, p.GUID, p.Profile,
  if(p.AcceptedID!=p.TaxonID, sn.FullName, NULL) AS AsFullName,
  if(p.AcceptedID!=p.TaxonID, sn.Author, NULL) AS AsAuthor,
  if(p.AcceptedID!=p.TaxonID, st.GUID, NULL) AS AsGUID,
  p.TaxonomicStatus, p.IsUpdated=1 AS IsUpdated,
  IF(p.SourceID IS NULL AND p.IsUpdated IS NULL,
  DATE(p.TimestampCreated), NULL) AS DateCreated,
  IF(p.SourceID IS NULL AND p.isUpdated IS NULL,
  CONCAT(u.FirstName, ' ', u.LastName), NULL) AS CreatedBy,
  DATE(p.TimestampCreated) AS DateUpdated,
  CONCAT(u.FirstName, ' ', u.LastName) AS UpdatedBy,
  r.Author, ir.PublicationYear, r.Title, ir.Author as InAuthor,
  ir.Title AS InTitle, ir.Publisher, ir.PlaceOfPublication
FROM (`vicflora_taxon` t)
JOIN `vicflora_profile` p ON `t`.`TaxonID`=`p`.`TaxonID`
JOIN `vicflora_taxon` st ON `p`.`TaxonID`=`st`.`TaxonID`
JOIN `vicflora_name` sn ON `st`.`NameID`=`sn`.`NameID`
LEFT JOIN `vicflora_reference` r ON `p`.`SourceID`=`r`.`ReferenceID`
LEFT JOIN `vicflora_reference` ir ON `r`.`InPublicationID`=`ir`.`ReferenceID`
LEFT JOIN `users` u ON `p`.`CreatedByID`=`u`.`UsersID`
WHERE t.GUID='$guid'
    AND `p`.`IsCurrent` =  1 
    AND ((p.TaxonomicStatus!='accepted' OR p.TaxonomicStatus IS NULL) AND (p.AcceptedID=p.TaxonID OR p.AcceptedID IS NULL))                
EOT;
        $query = $this->db->query($sql);
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
    
}
?>
