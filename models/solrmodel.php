<?php


class SolrModel extends CI_Model {
    private $client;
    private $query;
    private $facet_config;
    private $pgdb;
    
    public function __construct() {
        require_once($this->config->item('solr_solarium')); 
        require_once('third_party/Encoding/Encoding.php');
        parent::__construct();
        $solrconfig = array(
            "endpoint" => array(
                "localhost" => array(
                    "host"=>"10.15.15.107",
                    //"host"=>'localhost',
                    "port"=>"65002",
                    //"port"=>'1024',
                    "path"=>"/solr",
                    "core"=>"vicflora",
                    'timeout' => 30
                ),
            )
        );
        
        $this->client = new Solarium\Client($solrconfig);
        $this->pgdb = $this->load->database('postgis', TRUE);
    }
    
    public function solrSearch($download=FALSE) {
        if ($download) {
            ini_set('memory_limit', '1024M');
        }

        $facets = array(
            'name_type',
            'taxonomic_status',
            'taxon_rank',
            'occurrence_status',
            'establishment_means',
            'threat_status',
            'subclass',
            'superorder',
            'order',
            'family',
            'ibra_7_subregion',
            'nrm_region',
            'media'
        );
        
        if (isset($this->session->userdata['id'])) {
            $facets[] = 'profile';
            $facets[] = 'apni_match_type';
            $facets[] = 'apni_match_verification_status';
        }
        // parse query string
        $in = $this->parseQueryString();
        
        if ($download) {
            $in->rows = 11000;
            $in->start = 0;
        }
        
        $this->query = $this->client->createSelect();
        $this->query->setQueryDefaultField('text');
        $this->query->setQuery($in->q);

        // parse fq
        if ($in->fq) {
            foreach ($in->fq as $index => $value) {
                if (substr($value, 0, 1) === '(') {
                    $fquery = $this->query->createFilterQuery(substr($value, 1, strpos($value, ':')-1));
                    $fquery->setQuery($in->fq[$index]);
                }
                else {
                    $arr = explode(':', $value);
                    if (count($arr) == 2 && $arr[0] && $arr[1]) {
                        $fquery = $this->query->createFilterQuery($arr[0]);
                        $fquery->setQuery($in->fq[$index]);
                    }
                }
            }
        }
        
        $this->query->setStart($in->start);
        $this->query->setRows($in->rows);
        
        $fields = array();
        if ($in->fieldlist) {
            foreach ($in->fieldlist as $field) {
                if ($field == 'taxon_id')
                    $fields[] = 'id';
                else
                    $fields[] = $field;
            }
        }
        else {
            $fields = array(
                'id',
                'taxon_rank',
                'scientific_name',
                'scientific_name_authorship',
                'taxonomic_status',
                'family',
                'occurrence_status',
                'establishment_means',
                'accepted_name_usage_id',
                'accepted_name_usage',
                'accepted_name_usage_authorship',
                'accepted_name_usage_taxon_rank',
                'name_according_to',
                'sensu',
                'threat_status',
                'profile',
                'vernacular_name'
            );
        }
        
        $this->query->setFields($fields);
        
        $this->query->addSorts(array('scientific_name' => 'asc'));
        
        if(!$download);
            $this->setFacets($facets);
        
        $resultSet = $this->client->select($this->query);
        $result = array();
        
        $result['numFound'] = $resultSet->getNumFound();
        
        $result['docs'] = array();
        foreach ($resultSet as $doc) {
            $fl = array();
            foreach ($fields as $field) {
                $fl[$field] = $doc->$field;
            }
            $result['docs'][] = (object) $fl;
        }
        
        if (!$download) {
            $result['facets'] = array();
            foreach ($facets as $facetfield) {
                $facetData = $resultSet->getFacetSet()->getFacet($facetfield);
                $facet = array();
                $facet['name'] = $facetfield;
                $facet['label'] = $this->facet_config[$facetfield]['label'];
                $items = array();
                $facet['items'] = array();
                foreach ($facetData as $item => $count) {
                    $items[$item] = $count;
                }
                if (isset($this->facet_config[$facetfield]['customsort'])) {
                    foreach ($this->facet_config[$facetfield]['customsort'] as $key) {
                        if (isset($items[$key])) {
                            $facet['items'][] = array(
                                'name' => $key,
                                'label' => ucfirst($key),
                                'count' => $items[$key],
                                'fq' => ($key) ? $facetfield . ':' . $key : '-' . $facetfield . ':*'
                            );
                        }
                    }
                }
                elseif (isset($this->facet_config[$facetfield]['itemlabels'])) {
                    foreach ($this->facet_config[$facetfield]['itemlabels'] as $key => $label) {
                        if (isset($items[$key]))
                            $facet['items'][] = array(
                                'name' => $key,
                                'label' => $label,
                                'count' => $items[$key],
                                'fq' => ($key) ? $facetfield . ':' . $key : '-' . $facetfield . ':*'
                            );
                    }
                }
                else {
                    foreach ($items as $key => $value)
                        $facet['items'][] = array(
                            'name' => $key,
                            'label' => ucfirst($key),
                            'count' => $value,
                            'fq' => ($key) ? $facetfield . ':' . $key : '-' . $facetfield . ':*'
                        );
                }
                $result['facets'][] = $facet;
            }
        }
        $request = $this->client->createRequest($this->query);
        $result['params'] = (object) $request->getParams();
        
        return (object) $result;
    }
    
    private function parseQueryString() {
        $ret = new stdClass();
        $qstring = urldecode($this->input->server('QUERY_STRING'));
        $qarray = explode('&', $qstring);
        
        $q = '*:*';
        $fq = array();
        $rows = 50;
        $start = 0;
        $fieldlist = FALSE;
        foreach ($qarray as $item) {
            $item = explode('=', $item);
            if (isset($item[1])) {
                if ($item[0] == 'q') {
                    if ($item[1]) {
                        $q = $item[1];
                    }
                    if ($q != '*:*' && strpos($q, ':')===FALSE && !(substr($q, 0, 1) === '"' && substr($q, -1) === '"') && strpos($q, '*') === FALSE && strpos($q, ' ') !== FALSE) {
                        $q = '"' . $q . '"';
                    }
                    elseif (!$q) {
                        $q = '*:*';
                    }
                    elseif ($q != '*:*' && strpos($q, ' ') === FALSE && strpos($q, '*') === FALSE) {
                        $q .= '*';
                    }
                    
                }
                if ($item[0] == 'fq')
                    $fq[] = $item[1];
                if ($item[0] == 'rows')
                    $rows = $item[1];
                if ($item[0] == 'start' && $item[1] > 0)
                    $start = $item[1];
                if ($item[0] == 'fl')
                    $fieldlist = explode(',', $item[1]);
                    
            }
        }
        $ret->q = $q;
        $ret->fq = $fq;
        $ret->rows = $rows;
        $ret->start = $start;
        $ret->fieldlist = $fieldlist;
        return $ret;
    }
    
    public function solrChecklistSearch($ids) {
        $facets = array(
            'subclass',
            'superorder',
            'order',
            'family',
            'establishment_means',
            'threat_status',
            'media'
        );

        $fields = array(
            'id',
            'taxon_rank',
            'scientific_name',
            'scientific_name_authorship',
            'taxonomic_status',
            'family',
            'occurrence_status',
            'establishment_means',
            'accepted_name_usage_id',
            'accepted_name_usage',
            'accepted_name_usage_authorship',
            'accepted_name_usage_taxon_rank',
            'name_according_to',
            'sensu',
            'threat_status',
            'profile',
            'vernacular_name'
        );
        
        $limit = (count($ids) < 1000) ? count($ids) : 1000;
        $i = 0;
        $q = array();
        while ($i < $limit) {
            $q[] = 'id:' . $ids[$i];
            $i++;
        }
        
        $q = implode(' OR ', $q);
        
        $this->client->getPlugin('postbigrequest');
        $this->query = $this->client->createSelect();
        
        $this->query->setQueryDefaultField('scientific_name');
        $this->query->setQuery($q);
        $this->query->setStart(0);
        $this->query->setRows($limit);
        $this->query->setFields($fields);
        
        $this->query->addSorts(array('scientific_name' => 'asc'));
        $this->setFacets($facets);
        
        $resultSet = $this->client->select($this->query);
        $result = array();
        
        $result['numFound'] = $resultSet->getNumFound();
        
        $result['docs'] = array();
        foreach ($resultSet as $doc) {
            $fl = array();
            foreach ($fields as $field) {
                $fl[$field] = $doc->$field;
            }
            $result['docs'][] = (object) $fl;
        }
        
        $result['facets'] = array();
        foreach ($facets as $facetfield) {
            $facetData = $resultSet->getFacetSet()->getFacet($facetfield);
            $facet = array();
            $facet['name'] = $facetfield;
            $facet['label'] = $this->facet_config[$facetfield]['label'];
            $items = array();
            $facet['items'] = array();
            foreach ($facetData as $item => $count) {
                $items[$item] = $count;
            }
            if (isset($this->facet_config[$facetfield]['customsort'])) {
                foreach ($this->facet_config[$facetfield]['customsort'] as $key) {
                    if (isset($items[$key]))
                        $facet['items'][] = array(
                            'name' => $key,
                            'label' => ucfirst($key),
                            'count' => $items[$key]
                        );

                }
            }
            elseif (isset($this->facet_config[$facetfield]['itemlabels'])) {
                foreach ($this->facet_config[$facetfield]['itemlabels'] as $key => $label) {
                    if (isset($items[$key]))
                        $facet['items'][] = array(
                            'name' => $key,
                            'label' => $label,
                            'count' => $items[$key]
                        );
                }
            }
            else {
                foreach ($items as $key => $value)
                    $facet['items'][] = array(
                        'name' => $key,
                        'label' => ucfirst($key),
                        'count' => $value
                    );
            }
            $result['facets'][] = $facet;
        }
        //$request = $this->client->createRequest($this->query);
        //$result['request_uri'] = $request->getUri();
        //$result['params'] = (object) $request->getParams();
        $result['params'] = (object) array(
            'q' => 'id:' . $ids[0],
            'start' => 0,
            'rows' => count($ids)
        );
        
        return (object) $result;
    }
    
    public function browse($scientificName, $guid) {
        $ret = array();
        $qstring = urldecode(substr($this->session->userdata['last_search'], strpos($this->session->userdata['last_search'], '?')+1));
        $qarray = explode('&', $qstring);
        
        $q = '*:*';
        $fq = array();
        $rows = 50;
        $start = 0;
        $fieldlist = FALSE;
        foreach ($qarray as $item) {
            $item = explode('=', $item);
            if (isset($item[1])) {
                if ($item[0] == 'q') {
                    $q = $item[1];
                    if (strpos($q, ':')===FALSE && strpos($q, '*')===FALSE && strpos($q, '+')===FALSE && strpos($q, '\\')===FALSE && strpos($q, ' AND ')===FALSE && 
                            strpos($q, ' OR ')===FALSE && strpos($q, '[')===FALSE && strpos($q, '{')===FALSE) {
                        $q = str_replace(' ', '\\ ', $q);
                        $q = str_replace('(', '\\(', $q);
                        $q = str_replace(')', '\\)', $q);
                        $q .= '*';
                    }
                }
                if ($item[0] == 'fq')
                    $fq[] = $item[1];
                    
            }
        }

        $this->query = $this->client->createSelect();
        $this->query->setFields(array('scientific_name'));
        
        $this->query->addSorts(array('scientific_name' => 'asc'));
        $this->query->setQueryDefaultField('scientific_name');
        $this->query->setQuery($q);

        // parse fq
        if ($fq) {
            foreach ($fq as $index => $value) {
                $arr = explode(':', $value);
                if (count($arr) == 2 && $arr[0] && $arr[1]) {
                    $fquery = $this->query->createFilterQuery($arr[0]);
                    $fquery->setQuery($fq[$index]);
                }
            }
        }
        $fquery = $this->query->createFilterQuery('scientific_name');
        $fquery->setQuery('[* TO "'. $scientificName .'"]');
        

        
        $resultSet = $this->client->select($this->query);
        $numFound = $resultSet->getNumFound();

        // Now find the IDs of the previous and next records
        if ($numFound) {
            $this->query = $this->client->createSelect();
            $this->query->setFields(array('id', 'scientific_name'));

            $this->query->addSorts(array('scientific_name' => 'asc'));
            $this->query->setQueryDefaultField('scientific_name');
            $this->query->setQuery($q);

            // parse fq
            if ($fq) {
                foreach ($fq as $index => $value) {
                    $arr = explode(':', $value);
                    if (count($arr) == 2 && $arr[0] && $arr[1]) {
                        $fquery = $this->query->createFilterQuery($arr[0]);
                        $fquery->setQuery($fq[$index]);
                    }
                }
            }

            $this->query->setRows(3);
            if ($numFound > 1) {
                $this->query->setStart($numFound-2);
            }
            else {
                $this->query->setStart(0);
            }
            $resultSet = $this->client->select($this->query);
            $docs = array();
            foreach ($resultSet as $document) {
                $docs[] = $document->id;
            }
            
            if ($numFound == 1) {
                $docs[] = array_unshift($docs, FALSE);
            }
            elseif (count($docs) < 3) {
                $docs[] = FALSE;
            }
            
            if ($docs[1] == $guid) {
                $ret['previous'] = $docs[0];
                $ret['current'] = $docs[1];
                $ret['next'] = $docs[2];
            }
            
        }
        return $ret;
    }
    
    private function facetConfig() {
        $this->facet_config = array();
        
        $this->facet_config['taxonomic_status'] = array(
            'label' => 'Taxonomic status',
            'customsort' => array('accepted', 'homotypic synonym', 'heterotypic synonym', 'synonym', 'misapplication', null)
        );
        
        $this->facet_config['name_type'] = array(
            'label' => 'Type of name',
            'customsort' => array('scientific', 'hybrid name', 'hybrid formula', 'cultivar', 'informal', null)
        );
        
        $this->facet_config['end_or_higher_taxon'] = array(
            'label' => 'End or higher taxa',
            'itemlabels' => array(
                'end' => 'End taxa',
                'higher' => 'Higher taxa',
                null => '(blanks)'
            )
        );
        
        $this->facet_config['taxon_rank'] = array(
            'label' => 'Taxon rank',
            'customsort' => array('kingdom', 'class', 'subclass', 'superorder', 'order', 'family',
                'genus', 'species', 'subspecies', 'variety', 'forma', 'cultivar', null)
        );
        
        $this->facet_config['occurrence_status'] = array(
            'label' => 'Occurrence status',
            'customsort' => array('present', 'endemic', 'extinct', 'excluded', null)
        );
        
        $this->facet_config['establishment_means'] = array(
            'label' => 'Establishment means',
            'itemlabels' => array(
                'native' => 'Native',
                'also naturalised' => 'Naturalised in part(s) of state',
                'introduced' => 'Introduced',
                'naturalised' => 'Naturalised',
                'sparingly established' => 'Sparingly established',
                'adventive' => 'Sparingly established',
                'uncertain' => 'Uncertain',
                null => '(blanks)'
            )
        );
        
        $this->facet_config['threat_status'] = array(
            'label' => 'Threat status',
            'itemlabels' => array(
                'EPBC_EX' => 'EPBC: Extinct (EX)',
                'EPBC_CR' => 'EPBC: Critically endangered (CR)',
                'EPBC_EN' => 'EPBC: Endangered (EN)',
                'EPBC_VU' => 'EPBC: Vulnerable (VU)',
                'VROT_x' => 'Vic.: Extinct (x)',
                'VROT_e' => 'Vic.: Endangered (e)',
                'VROT_v' => 'Vic.: Vulnerable (v)',
                'VROT_r' => 'Vic.: Rare (r)',
                'VROT_k' => 'Vic.: Data deficient (k)',
                'FFG listed' => 'Vic.: FFG listed',
                null => '(blanks)'
            )
        );
        
        /*$this->facet_config['naturalised_status'] = array(
            'label' => 'Naturalised status',
            'customsort' => array('naturalised', 'incipiently naturalised', 'indigenous and naturalised', 'uncertain')
        );*/
        
        $this->facet_config['epbc'] = array(
            'label' => 'EPBC',
            'customsort' => array('EX', 'CR', 'EN', 'VU', null)
        );
        
        $this->facet_config['vrot'] = array(
            'label' => 'VROT',
            'itemlabels' => array(
                'x' => 'Extinct (x)',
                'e' => 'Endangered (e)',
                'v' => 'Vulnerable (v)',
                'r' => 'Rare (r)',
                'k' => 'Data deficient (k)',
                null => '(blanks)'
            )
        );
        
        $this->facet_config['ffg'] = array(
            'label' => 'FFG',
            'itemlabels' => array('L' => 'Listed'),
        );
        
        $this->facet_config['subclass'] = array(
            'label' => 'Subclass',
        );
        
        $this->facet_config['superorder'] = array(
            'label' => 'Superorder',
        );
        
        $this->facet_config['order'] = array(
            'label' => 'Order',
        );
        
        $this->facet_config['family'] = array(
            'label' => 'Family',
        );
        
        $this->facet_config['genus'] = array(
            'label' => 'Genus',
        );
        
        $this->facet_config['ibra_6_region'] = array(
            'label' => 'IBRA 6 region',
        );
        
        $this->facet_config['ibra_6_subregion'] = array(
            'label' => 'IBRA 6 subregion',
        );
        
        $this->facet_config['ibra_7_region'] = array(
            'label' => 'IBRA region',
        );
        
        $this->facet_config['ibra_7_subregion'] = array(
            'label' => 'Bioregion',
        );
        
        $this->facet_config['nrm_region'] = array(
            'label' => 'CMA',
        );
        
        $this->facet_config['profile'] = array(
            'label' => 'Profile available',
            'customsort' => array('accepted', 'accepted plus', 'homotypic synonym',
                'heterotypic synonym', 'misapplication', null)
        );

        $this->facet_config['apni_match_type'] = array(
            'label' => 'APNI name match type',
            'itemlabels' => array(
                'FullNameWithAuthors' => 'Full name with authors',
                'FullName' => 'Full name without authors',
                'MultipleMatches' => 'Multiple matches',
                'NotMatched' => 'Not matched',
                null => '(blanks)'
            )
        );
        
        $this->facet_config['apni_match_verification_status'] = array(
            'label' => 'APNI match verification status',
            'itemlabels' => array(
                'Verified' => 'Verified',
                'Not verified' => 'Not verified',
                null => '(blanks)'
            )
        );
        
        $this->facet_config['media'] = array(
            'label' => 'Media',
            'itemlabels' => array(
                'illustration' => 'Illustration',
                'photograph' => 'Photograph',
                null => '(blanks)'
            )
        );
        
    }
    
    private function setFacets($facets) {
        $this->facetConfig();
        $facetset = $this->query->getFacetSet();
        $facetset->setMinCount(1);
        $facetset->setMissing(TRUE);
        $facetset->setLimit(-1);
        $facetset->setSort('index');
        foreach ($facets as $facetfield) {
            if (in_array($facetfield, array_keys($this->facet_config))) {
                $facetset->createFacetField($facetfield)->setField($facetfield);
            }
        }
    }
    
    public function updateSynonyms($guid) {
        $this->db->select('t.GUID');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_taxon a', 't.AcceptedID=a.TaxonID');
        $this->db->where('a.GUID', $guid);
        $this->db->where('t.TaxonID!=t.AcceptedID', FALSE, FALSE);
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row)
                $this->updateDocument($row->GUID);
        }
    }
    
    public function updateAll() {
        $this->db->select('t.GUID');
        $this->db->from('vicflora_taxon t');
        $this->db->where('t.DoNotIndex!=true OR t.DoNotIndex IS NULL', FALSE, FALSE);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $this->updateDocument($row->GUID);
        }
    }
    
    public function updateDocument($id) {
        $updateQuery = $this->client->createUpdate();
        $this->doc = $updateQuery->createDocument();
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
                t.OccurrenceStatus AS occurrenceStatus,
                t.EstablishmentMeans AS establishmentMeans,
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
              WHERE t.GUID='$id'";
        $query = $this->db->query($select);
        $row = $query->row();
        
        $this->doc->project = array('VicFlora');
        $this->doc->id = $id;
        $this->doc->scientific_name_id = $row->scientificNameID;
        $this->doc->scientific_name = Encoding::toUTF8($row->scientificName);
        $this->doc->scientific_name_authorship = $row->scientificNameAuthorship;
        $this->doc->name_type = $row->name_type;
        $this->doc->name_published_in_id = $row->namePublishedInID;
        $this->doc->name_published_in = $row->namePublishedIn;
        $this->doc->name_published_in_year = $row->namePublishedInYear;
        $this->doc->sensu = $row->Sensu;
        $this->doc->name_according_to = $row->nameAccordingTo;
        $this->doc->taxon_rank = $row->taxonRank;
        $this->doc->taxonomic_status = (in_array($row->taxonomicStatus, array('homotypic synonym', 
            'heterotypic synonym'))) ? 'synonym' : $row->taxonomicStatus;
        $occurrenceStatus = array();
        if ($row->occurrenceStatus == 'endemic')
            $occurrenceStatus[] = 'present';
        $occurrenceStatus[] = $row->occurrenceStatus;
        $this->doc->occurrence_status = $occurrenceStatus;
        $this->doc->establishment_means = $this->establishmentMeans($row->establishmentMeans);
        $this->doc->taxon_remarks = $row->taxonRemarks;
        
        $this->doc->parent_name_usage_id = $row->parentNameUsageID;
        $this->doc->parent_name_usage = $row->parentNameUsage;
        $this->doc->accepted_name_usage_id = $row->acceptedNameUsageID;
        $this->doc->accepted_name_usage = $row->acceptedNameUsage;
        $this->doc->accepted_name_usage_authorship = $row->acceptedNameUsageAuthorship;
        $this->doc->accepted_name_usage_taxon_rank = $row->acceptedNameUsageTaxonRank;
        
        if ($row->taxonomicStatus == 'accepted') {
            $description = $this->description($id);
            if ($description) {
                $this->doc->profile = $description;
            }
            $this->doc->media = $this->media($id);
            if ($row->AcceptedRankID == 220) {
                $this->doc->species_id = $row->acceptedNameUsageID;
            }
            elseif ($row->AcceptedRankID > 220) {
                $this->doc->species_id = $this->getSpeciesID($row->acceptedNameUsageID);
            }
        }
        
        $this->doc->vernacular_name = $row->vernacularName;
        
        if ($row->NodeNumber) {
            $this->doc->end_or_higher_taxon = $this->endTaxon($id);
            $this->higherClassification($row->NodeNumber, $row->RankID);
        }
        elseif ($row->AcceptedNodeNumber) {
            $this->doc->end_or_higher_taxon = $this->endTaxon($row->acceptedNameUsageID);
            $this->higherClassification($row->AcceptedNodeNumber, $row->AcceptedRankID);
        
        }
        $this->doc->ibra_7_subregion = $this->distribution($id, 'subregion', $row->RankID);
        $this->doc->ibra_7_subregion = $this->distribution($id, 'subregion', $row->RankID);
        if ($row->RankID >= 180) {
            $this->doc->nrm_region = $this->NrmRegions($id);
        }
        
        // Threat status
        $threatStatus = array();
        $epbc = $this->taxonAttribute($id, 'EPBC (Jan. 2014)');
        if ($epbc) 
            $threatStatus[] = 'EPBC_' . $epbc;
        $vrot = $this->taxonAttribute($id, 'VROT');
        if ($vrot)
            $threatStatus[] = 'VROT_'  . $vrot;
        $ffg = $this->taxonAttribute($id, 'FFG');
        if ($ffg)
            $threatStatus[] = 'FFG listed';
        if ($threatStatus)
            $this->doc->threat_status = $threatStatus;
        
        $apni = $this->apniMatch($row->NameID);
        if ($apni) {
            $this->doc->apni_match_type = $apni['apni_match_type'];
            $this->doc->apni_match_verification_status = $apni['apni_match_verification_status'];
        }
        
        $this->doc->last_modified = date('Y-m-d\TH:i:s\Z');
        
        $updateQuery->addDocuments(array($this->doc), $overwrite=true);
        $updateQuery->addCommit();
        $this->client->update($updateQuery);
    }
    
    public function getSpeciesID($id) {
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
    
    public function deleteDocument($guid) {
        $deleteQuery = $this->client->createUpdate();
        $deleteQuery->addDeleteById($guid);
        $deleteQuery->addCommit();
        $this->client->update($deleteQuery);
    }
    
    private function higherClassification($nodenumber) {
        $select = "SELECT lower(td.Name) AS Rank, n.Name
            FROM vicflora_taxon t
            JOIN vicflora_taxontreedefitem td ON t.TaxonTreeDefItemID=td.TaxonTreeDefItemID
            JOIN vicflora_taxontree tt ON t.TaxonID=tt.TaxonID
            JOIN vicflora_name n ON t.NameID=n.NameID
            WHERE tt.NodeNumber<=$nodenumber AND tt.HighestDescendantNodeNumber>=$nodenumber AND t.RankID>0
            ORDER BY t.RankID";
        $query = $this->db->query($select);
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                switch ($row->Rank) {
                    case 'kingdom':
                        $this->doc->kingdom = $row->Name;
                        break;
                    case 'phylum':
                        $this->doc->phylum = $row->Name;
                        break;
                    case 'class':
                        $this->doc->class = $row->Name;
                        break;
                    case 'subclass':
                        $this->doc->subclass = $row->Name;
                        break;
                    case 'superorder':
                        $this->doc->superorder = $row->Name;
                        break;
                    case 'order':
                        $this->doc->order = $row->Name;
                        break;
                    case 'family':
                        $this->doc->family = $row->Name;
                        break;
                    case 'genus':
                        $this->doc->genus = $row->Name;
                        break;
                    case 'species':
                        $this->doc->specific_epithet = $row->Name;
                        break;
                    case 'subspecies':
                    case 'variety':
                    case 'subvariety':
                    case 'forma':
                    case 'subforma':
                        $this->doc->infraspecific_epithet = $row->Name;
                        break;

                    default:
                        break;
                }
            }
        }
    }
    
    private function distribution($id, $areaclass, $rankid=FALSE) {
        $ret = array();
        if (!$rankid) {
            $select = "SELECT DISTINCT d.AreaName
                FROM vicflora_taxon t
                JOIN vicflora_distribution d ON t.TaxonID=d.TaxonID
                WHERE t.GUID='$id' AND AreaClass='$areaclass'
                ORDER BY AreaName";
            $query = $this->db->query($select);
            if ($query->num_rows()) {
                foreach ($query->result() as $row)
                    $ret[] = $row->AreaName;
            }
        }
        else {
            if ($rankid >= 180) {
                if ($rankid == 180)
                    $where = '"genus_guid"';
                elseif ($rankid == 220) 
                    $where = '"species_guid"';
                else
                    $where = '"taxon_id"';
                
                if ($areaclass == 'region')
                    $reg = 'reg_name_7';
                else
                    $reg = 'sub_name_7';
                
                $select = "SELECT $reg AS \"AreaName\"
                    FROM vicflora.vicflora_occurrence
                    WHERE $where='$id' AND $reg IS NOT NULL
                    GROUP BY $reg";
                $query = $this->pgdb->query($select);
                if ($query->num_rows()) {
                    foreach ($query->result() as $row)
                        $ret[] = $row->AreaName;
                }
            }
        }
        return $ret;
    }
    
    private function NrmRegions($id) {
        $ret = array();
        $select = "SELECT locality_id
            FROM vicflora.vicflora_distribution
            WHERE taxon_id='$id' AND locality_type='NRM region' AND locality_id IS NOT NULL
            GROUP BY locality_id";
        $query = $this->pgdb->query($select);
        if ($query->result()) {
            foreach ($query->result() as $row) {
                $ret[] = $row->locality_id;
            }
        }
        return $ret;
    }

    private function taxonAttribute($id, $attribute) {
        $ret = FALSE;
        $select = "SELECT REPLACE(REPLACE(a.StrValue, ')', ''), '(', '') AS StrValue
            FROM vicflora_taxon t
            JOIN vicflora_taxonattribute a ON t.TaxonID=a.TaxonID
            WHERE t.GUID='$id' AND a.Attribute='$attribute'";
        $query = $this->db->query($select);
        if ($query->num_rows()) {
            $row = $query->row();
            $ret = $row->StrValue;
        }
        return($ret);
    }
    
    private function endTaxon($id) {
        $select = "SELECT count(*) AS tCount
            FROM vicflora_taxon t
            JOIN vicflora_taxon c ON t.TaxonID=c.ParentID
            WHERE c.TaxonomicStatus='accepted' AND (c.DoNotIndex IS NULL OR c.DoNotIndex=0)
              AND t.GUID='$id'";
        $query = $this->db->query($select);
        $row = $query->row();
        if ($row->tCount)
            return 'higher';
        else
            return 'end';
    }

    private function description($id) {
        $desc = FALSE;
        $select = "SELECT p.TaxonomicStatus
            FROM vicflora_profile p
            JOIN vicflora_taxon t ON p.AcceptedID=t.TaxonID
            WHERE t.GUID='$id'";
        $query = $this->db->query($select);
        $result = $query->result();
        if ($result) {
            $stat = array();
            foreach ($result as $row) {
                $stat[] = $row->TaxonomicStatus;
            }
            
            $desc = FALSE;
            if (in_array('accepted', $stat) && count($result) > 1)
                $desc = 'accepted plus';
            elseif(in_array('accepted', $stat))
                $desc = 'accepted';
            elseif(in_array('homotypic synonym', $stat))
                $desc = 'homotypic synonym';
            elseif(in_array('heterotypic synonym', $stat))
                $desc = 'heterotypic synonym';
            elseif(in_array('misapplication', $stat))
                $desc = 'misapplication';
        }
        return $desc;
    }
    
    private function establishmentMeans($dbvalue) {
        $ret = array();
        if ($dbvalue == "native (naturalised in part(s) of state)") {
            $ret[] = 'native';
            $ret[] = 'also naturalised';
        }
        elseif (in_array ($dbvalue, array('naturalised', 'adventive'))) {
            $ret[] = 'introduced';
            $ret[] = $dbvalue;
        }
        else {
            $ret[] = $dbvalue;
        }
        return $ret;
    }
    
    private function media($id) {
        $ret = array();
        $select = "SELECT t.taxonID, count(IF(i.Subtype='Illustration', 1, NULL)) AS numIllustrations,
                  count(IF(i.Subtype='Photograph', 1, NULL)) AS numPhotographs
                FROM vicflora_taxon t
                JOIN vicflora_name n ON t.NameID=n.NameID
                LEFT JOIN vicflora_taxon ct ON t.TaxonID=ct.ParentID AND t.RankID=220
                LEFT JOIN vicflora_name cn ON ct.NameID=cn.NameID
                LEFT JOIN cumulus_image i ON coalesce(t.TaxonID, ct.TaxonID)=i.TaxonID AND PixelXDimension>0
                WHERE t.GUID='$id' OR ct.GUID='$id'
                GROUP BY t.TaxonID";
        $query = $this->db->query($select);
        if ($query->num_rows()) {
            $row = $query->row();
            if ($row->numIllustrations) {
                $ret[] = 'illustration';
            }
            if ($row->numPhotographs) {
                $ret[] = 'photograph';
            }
        }
        return $ret;
    }

    private function apniMatch($nameid) {
        $ret = array();
        $sql = "SELECT MatchType, IsVerified
            FROM vicflora_apni
            WHERE NameID=$nameid";
        $query = $this->db->query($sql);
        $result = $query->result();
        if (!$result) 
            $ret = array(
                'apni_match_type' => 'NotMatched',
                'apni_match_verification_status' => FALSE
            );
        elseif (count($result) > 1)
            $ret = array(
                'apni_match_type' => 'MultipleMatches',
                'apni_match_verification_status' => FALSE
            );
        else {
            $row = $result[0];
            $matchtype = (in_array($row->MatchType, array('Monomial', 'Autonym'))) ? 'FullName' : $row->MatchType;
            $ret = array(
                'apni_match_type' => $matchtype,
                'apni_match_verification_status' => ($row->IsVerified) ? 'Verified' : 'Not verified'
            );
        }
        return $ret;
    }
    
}

/* End of file solrmodel.php */
/* Location: ./models/solrmodel.php */
