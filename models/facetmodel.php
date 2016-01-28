<?php

require_once('searchmodel.php');

class FacetModel extends SearchModel {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 
     * function facetTaxonomicStatus
     * 
     * @param string $term
     * @param array $fq
     * @return array
     */
    public function facetTaxonomicStatus($term, $fq) {
        $facet = array();
        $facet['name'] = 'taxonomicStatus';
        $facet['label'] = 'Taxonomic status';
        $facet['items'] = array();
        
        // Accepted
        $this->db->select('count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        if ($term)
            $this->db->like('n.FullName', $term, 'after');
        if ($fq)
            $q = $this->parseFacetQuery ($fq);
        if (!$fq || !in_array('present', $q['where']))
            $this->db->where('t.TaxonomicStatus', 'Accepted');
        $query = $this->db->get();
        $row = $query->row();
        if ($row->num) {
            $facet['items'][] = array(
                'name' => 'accepted',
                'label' => 'Accepted',
                'count' => $row->num
            );
        }
        
        // Not current
        $this->db->select('count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        if ($term)
            $this->db->like('n.FullName', $term, 'after');
        if ($fq)
            $q = $this->parseFacetQuery ($fq);
        if (!$fq || !in_array('TaxonomicStatus:!Accepted', $q['where']))
            $this->db->where("(t.TaxonomicStatus IS NULL OR t.TaxonomicStatus!='Accepted')", FALSE, FALSE);
        $query = $this->db->get();
        $row = $query->row();
        if ($row->num)
            $facet['items'][] = array(
                'name' => 'notCurrent',
                'label' => 'Not current',
                'count' => $row->num
            );
        
        return $facet;
    }
    
    public function facetTaxonType($term, $fq) {
        $facet = array();
        $facet['name'] = 'taxonType';
        $facet['label'] = 'Taxon type';
        $facet['items'] = array();
        
        // End taxa
        $this->db->select('count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        if ($term)
            $this->db->like('n.FullName', $term, 'after');
        if ($fq)
            $q = $this->parseFacetQuery ($fq);
        if (!$fq || !in_array('c', $q['join']))
            $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left', FALSE);
        if (!$fq || !in_array('endTaxa', $q['where']))
            $this->db->where('c.TaxonID IS NULL', FALSE, FALSE);
        $query = $this->db->get();
        $row = $query->row();
        if ($row->num)
            $facet['items'][] = array(
                'name' => 'endTaxa',
                'label' => 'End taxa',
                'count' => $row->num
            );
        
        // Parent taxa
        $this->db->select('count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        if ($term)
            $this->db->like('n.FullName', $term, 'after');
        if ($fq)
            $q = $this->parseFacetQuery ($fq);
        if (!$fq || !in_array('c', $q['join']))
            $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left', FALSE);
        if (!$fq || !in_array('parentTaxa', $q['where']))
            $this->db->where('c.TaxonID IS NOT NULL', FALSE, FALSE);
        $query = $this->db->get();
        $row = $query->row();
        if ($row->num)
            $facet['items'][] = array(
                'name' => 'parentTaxa',
                'label' => 'Higher taxa',
                'count' => $row->num
            );

        return $facet;
    }
    
    public function facetOccurrenceStatus($term, $fq) {
        $facet = array();
        $facet['name'] = 'occurrenceStatus';
        $facet['label'] = 'Occurrence status';
        $facet['collapsible'] = 1;
        $facet['items'] = array();
        
        // Present
        $this->db->select('count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        if ($term)
            $this->db->like('n.FullName', $term, 'after');
        if ($fq)
            $q = $this->parseFacetQuery ($fq);
        if (!$fq || !in_array('TaxonomicStatus:Accepted', $q['where']))
            $this->db->where('t.TaxonomicStatus', 'Accepted');
        if (!$fq || !in_array('c', $q['join']))
            $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left');
        if (!$fq || !in_array('endTaxa', $q['where']))
            $this->db->where('c.TaxonID IS NULL', FALSE, FALSE);
        if (!$fq || !in_array('present', $q['where']))
            $this->db->where_in('t.OccurrenceStatus', array('present', 'endemic'));
        $query = $this->db->get();
        $row = $query->row();
        if ($row->num)
            $facet['items'][] = array(
                'name' => 'present',
                'label' => 'Present',
                'count' => $row->num
            );
        
        // Endemic
        $this->db->select('count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        if ($term)
            $this->db->like('n.FullName', $term, 'after');
        if ($fq)
            $q = $this->parseFacetQuery ($fq);
        if (!$fq || !in_array('TaxonomicStatus:Accepted', $q['where']))
            $this->db->where('t.TaxonomicStatus', 'Accepted');
        if (!$fq || !in_array('c', $q['join']))
            $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left');
        if (!$fq || !in_array('endTaxa', $q['where']))
            $this->db->where('c.TaxonID IS NULL', FALSE, FALSE);
        if (!$fq || !in_array('endemic', $q['where']))
            $this->db->where('t.OccurrenceStatus', 'endemic');
        $query = $this->db->get();
        $row = $query->row();
        if ($row->num)
            $facet['items'][] = array(
                'name' => 'endemic',
                'label' => 'Endemic',
                'count' => $row->num
            );
        
        // Extinct
        $this->db->select('count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        if ($term)
            $this->db->like('n.FullName', $term, 'after');
        if ($fq)
            $q = $this->parseFacetQuery ($fq);
        if (!$fq || !in_array('TaxonomicStatus:Accepted', $q['where']))
            $this->db->where('t.TaxonomicStatus', 'Accepted');
        if (!$fq || !in_array('c', $q['join']))
            $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left');
        if (!$fq || !in_array('endTaxa', $q['where']))
            $this->db->where('c.TaxonID IS NULL', FALSE, FALSE);
        if (!$fq || !in_array('extinct', $q['where']))
            $this->db->where('t.OccurrenceStatus', 'extinct');
        $query = $this->db->get();
        $row = $query->row();
        if ($row->num)
            $facet['items'][] = array(
                'name' => 'extinct',
                'label' => 'Extinct',
                'count' => $row->num
            );
        
        return $facet;
    }
    
    public function facetEstablishmentMeans($term, $fq) {
        $facet = array();
        $facet['name'] = 'establishmentMeans';
        $facet['label'] = 'Establishment means';
        $facet['collapsible'] = 1;
        $facet['items'] = array();
        
        // Native
        $this->db->select('count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        
        if ($fq) {
            $q = $this->parseFacetQuery($fq);
        }
        
        if ($term)
            $this->db->like('n.FullName', $term, 'after');
        
        if (!$fq || !in_array('TaxonomicStatus:Accepted', $q['where']))
            $this->db->where('t.TaxonomicStatus', 'Accepted');
        
        if (!$fq || !in_array('c', $q['join']))
            $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left');
        
        if (!$fq || !in_array('endTaxa', $q['where']))
            $this->db->where('c.TaxonID IS NULL', FALSE, FALSE);
        
        if (!$fq || !in_array('native', $q['where']))
            $this->db->where('t.EstablishmentMeans', 'native');
        $query = $this->db->get();
        $row = $query->row();
        if ($row->num)
            $facet['items'][] = array(
                'name' => 'native',
                'label' => 'Native',
                'count' => $row->num
            );

        // introduced
        $this->db->select('count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        
        if ($fq) {
            $q = $this->parseFacetQuery($fq);
        }
        
        if ($term)
            $this->db->like('n.FullName', $term, 'after');
        
        if (!$fq || !in_array('present', $q['where']))
            $this->db->where('t.TaxonomicStatus', 'Accepted');
        
        if (!$fq || !in_array('c', $q['join']))
            $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left');
        
        if (!$fq || !in_array('TaxonID:NULL', $q['where']))
            $this->db->where('c.TaxonID IS NULL', FALSE, FALSE);
        
        if (!$fq || !in_array('introduced', $q['where']))
            $this->db->where('t.EstablishmentMeans', 'introduced');
        $query = $this->db->get();
        $row = $query->row();
        if ($row->num)
            $facet['items'][] = array(
                'name' => 'introduced',
                'label' => 'Introduced',
                'count' => $row->num
            );
        
        return $facet;
    }
    
    public function facetEPBC($term, $fq) {
        $facet = array();
        $facet['name'] = 'epbc';
        $facet['label'] = 'EPBC';
        $facet['collapsible'] = 1;
        $facet['items'] = array();
        
        $items = array('EX', 'CR', 'EN', 'VU');
        foreach ($items as $item) {
            $this->db->select('count(DISTINCT t.TaxonID) AS num', FALSE);
            $this->db->from('vicflora_taxon t');
            $this->db->join('vicflora_name n', 't.NameID=n.NameID');

            if ($term)
                $this->db->like('n.FullName', $term, 'after');
            
            if ($fq)
                $q = $this->parseFacetQuery($fq);
            
            if (!$fq || !in_array('c', $q['join']))
                $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left');

            if (!$fq || !in_array('TaxonID:NULL', $q['where']))
                $this->db->where('c.TaxonID IS NULL', FALSE, FALSE);
            
            if (!$fq || !in_array('a2', $q['join']))
                $this->db->join('vicflora_taxonattribute a2', 
                        "t.TaxonID=a2.TaxonID AND a2.Attribute='EPBC (Jan. 2014)'", 'left', FALSE);
            
            $this->db->like('a2.StrValue', $item, 'both');

            $query = $this->db->get();
            $row = $query->row();
            if ($row->num)
                $facet['items'][] = array(
                    'name' => $item,
                    'label' => $item,
                    'count' => $row->num
                );
        }
        
        return $facet;
    }
    
    public function facetVROT($term, $fq) {
        $facet = array();
        $facet['name'] = 'vrot';
        $facet['label'] = 'VROT';
        $facet['collapsible'] = 1;
        $facet['items'] = array();
        
        $items = array('x' => 'Extinct', 'e' => 'Endangered', 'v' => 'Vulnerable', 
            'r' => 'Rare', 'k' => 'Data deficient');
        foreach ($items as $item => $label) {
            $this->db->select('count(DISTINCT t.TaxonID) AS num', FALSE);
            $this->db->from('vicflora_taxon t');
            $this->db->join('vicflora_name n', 't.NameID=n.NameID');

            if ($term)
                $this->db->like('n.FullName', $term, 'after');
            
            if ($fq)
                $q = $this->parseFacetQuery($fq);
            
            if (!$fq || !in_array('c', $q['join']))
                $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left');

            if (!$fq || !in_array('TaxonID:NULL', $q['where']))
                $this->db->where('c.TaxonID IS NULL', FALSE, FALSE);
            
            if (!$fq || !in_array('a3', $q['join']))
                $this->db->join('vicflora_taxonattribute a3', 
                        "t.TaxonID=a3.TaxonID AND a3.Attribute='VROT'", 'left', FALSE);
            
            $this->db->like('a3.StrValue', $item, 'both');

            $query = $this->db->get();
            $row = $query->row();
            if ($row->num)
                $facet['items'][] = array(
                    'name' => $item,
                    'label' => $label,
                    'count' => $row->num
                );
        }
        
        return $facet;
    }
    
    public function facetFFG($term, $fq) {
        $facet = array();
        $facet['name'] = 'ffg';
        $facet['label'] = 'FFG';
        $facet['collapsible'] = 1;
        $facet['items'] = array();
        
        $items = array('L' => 'Listed');
        foreach ($items as $item => $label) {
            $this->db->select('count(DISTINCT t.TaxonID) AS num', FALSE);
            $this->db->from('vicflora_taxon t');
            $this->db->join('vicflora_name n', 't.NameID=n.NameID');

            if ($term)
                $this->db->like('n.FullName', $term, 'after');
            
            if ($fq)
                $q = $this->parseFacetQuery($fq);
            
            if (!$fq || !in_array('c', $q['join']))
                $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left');

            if (!$fq || !in_array('TaxonID:NULL', $q['where']))
                $this->db->where('c.TaxonID IS NULL', FALSE, FALSE);
            
            if (!$fq || !in_array('a4', $q['join']))
                $this->db->join('vicflora_taxonattribute a4', 
                        "t.TaxonID=a4.TaxonID AND a4.Attribute='FFG (2013)'", 'left', FALSE);
            
            $this->db->like('a4.StrValue', $item, 'both');

            $query = $this->db->get();
            $row = $query->row();
            if ($row->num)
                $facet['items'][] = array(
                    'name' => $item,
                    'label' => $label,
                    'count' => $row->num
                );
        }
        
        return $facet;
    }
    
    public function facetIBRA($term, $fq) {
        $facet = array();
        $facet['name'] = 'ibra';
        $facet['label'] = 'IBRA 6.1 region';
        $facet['collapsible'] = 1;
        $facet['items'] = array();
        
        $this->db->select('d1.AreaCode, d1.AreaName, count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');

        if ($term)
            $this->db->like('n.FullName', $term, 'after');

        if ($fq)
            $q = $this->parseFacetQuery($fq);

        if (!$fq || !in_array('c', $q['join']))
            $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left');

        if (!$fq || !in_array('TaxonID:NULL', $q['where']))
            $this->db->where('c.TaxonID IS NULL', FALSE, FALSE);

        if (!$fq || !in_array('d1', $q['join']))
            $this->db->join('vicflora_distribution d1', 
                    "t.TaxonID=d1.TaxonID AND d1.AreaClass='IBRA 6.1 region'", 'left', FALSE);
        $this->db->where('d1.DistributionID IS NOT NULL', FALSE, FALSE);
        $this->db->group_by('d1.AreaCode');
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $facet['items'][] = array(
                    'name' => $row->AreaCode,
                    'label' => $row->AreaName,
                    'count' => $row->num
                );
            }
        }
            
        return $facet;
    }
    
    public function facetIBRASub($term, $fq) {
        $facet = array();
        $facet['name'] = 'ibra_sub';
        $facet['label'] = 'IBRA 6.1 subregion';
        $facet['collapsible'] = 1;
        $facet['items'] = array();
        
        $this->db->select('d2.AreaCode, d2.AreaName, count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');

        if ($term)
            $this->db->like('n.FullName', $term, 'after');

        if ($fq)
            $q = $this->parseFacetQuery($fq);

        if (!$fq || !in_array('c', $q['join']))
            $this->db->join('vicflora_taxon c', "t.TaxonID=c.ParentID AND c.TaxonomicStatus='Accepted'", 'left');

        if (!$fq || !in_array('TaxonID:NULL', $q['where']))
            $this->db->where('c.TaxonID IS NULL', FALSE, FALSE);

        if (!$fq || !in_array('d2', $q['join']))
            $this->db->join('vicflora_distribution d2', 
                    "t.TaxonID=d2.TaxonID AND d2.AreaClass='IBRA 6.1 subregion'", 'left', FALSE);
        $this->db->where('d2.DistributionID IS NOT NULL', FALSE, FALSE);
        $this->db->group_by('d2.AreaCode');
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $facet['items'][] = array(
                    'name' => $row->AreaCode,
                    'label' => $row->AreaName,
                    'count' => $row->num
                );
            }
        }
            
        return $facet;
    }
    
    public function facetSubclass($term, $fq) {
        $facet = array();
        $facet['name'] = 'subclass';
        $facet['label'] = 'Subclass';
        $facet['collapsible'] = 1;
        $facet['items'] = array();
        
        $this->db->select('cl.Subclass, count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');

        if ($term)
            $this->db->like('n.FullName', $term, 'after');

        if ($fq)
            $q = $this->parseFacetQuery($fq);

        if (!$fq || !in_array('cl', $q['join']))
            $this->db->join('vicflora_classification cl', 't.TaxonID=cl.TaxonID');
        
        $this->db->group_by('cl.Subclass');
        
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $facet['items'][] = array(
                    'name' => $row->Subclass,
                    'label' => $row->Subclass,
                    'count' => $row->num
                );
            }
        }
            
        return $facet;
    }
    
    public function facetSuperorder($term, $fq) {
        $facet = array();
        $facet['name'] = 'superorder';
        $facet['label'] = 'Superorder';
        $facet['collapsible'] = 1;
        $facet['items'] = array();
        
        $this->db->select('cl.Superorder, count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');

        if ($term)
            $this->db->like('n.FullName', $term, 'after');

        if ($fq)
            $q = $this->parseFacetQuery($fq);

        if (!$fq || !in_array('cl', $q['join']))
            $this->db->join('vicflora_classification cl', 't.TaxonID=cl.TaxonID');
        
        $this->db->where('cl.Superorder IS NOT NULL', FALSE, FALSE);
        $this->db->group_by('cl.Superorder');
        
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $facet['items'][] = array(
                    'name' => $row->Superorder,
                    'label' => $row->Superorder,
                    'count' => $row->num
                );
            }
        }
            
        return $facet;
    }
    
    public function facetOrder($term, $fq) {
        $facet = array();
        $facet['name'] = 'order';
        $facet['label'] = 'Order';
        $facet['collapsible'] = 1;
        $facet['items'] = array();
        
        $this->db->select('cl.Order, count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');

        if ($term)
            $this->db->like('n.FullName', $term, 'after');

        if ($fq)
            $q = $this->parseFacetQuery($fq);

        if (!$fq || !in_array('cl', $q['join']))
            $this->db->join('vicflora_classification cl', 't.TaxonID=cl.TaxonID');
        
        $this->db->group_by('cl.Order');
        
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $facet['items'][] = array(
                    'name' => $row->Order,
                    'label' => $row->Order,
                    'count' => $row->num
                );
            }
        }
            
        return $facet;
    }
    
    public function facetFamily($term, $fq) {
        $facet = array();
        $facet['name'] = 'family';
        $facet['label'] = 'Family';
        $facet['collapsible'] = 1;
        $facet['items'] = array();
        
        $this->db->select('cl.Family, count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');

        if ($term)
            $this->db->like('n.FullName', $term, 'after');

        if ($fq)
            $q = $this->parseFacetQuery($fq);

        if (!$fq || !in_array('cl', $q['join']))
            $this->db->join('vicflora_classification cl', 't.TaxonID=cl.TaxonID');
        
        $this->db->group_by('cl.Family');
        
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $facet['items'][] = array(
                    'name' => $row->Family,
                    'label' => $row->Family,
                    'count' => $row->num
                );
            }
        }
            
        return $facet;
    }

    public function facetGenus($term, $fq) {
        $facet = array();
        $facet['name'] = 'genus';
        $facet['label'] = 'Genus';
        $facet['collapsible'] = 1;
        $facet['items'] = array();
        
        $this->db->select('cl.Genus, count(DISTINCT t.TaxonID) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');

        if ($term)
            $this->db->like('n.FullName', $term, 'after');

        if ($fq)
            $q = $this->parseFacetQuery($fq);

        if (!$fq || !in_array('cl', $q['join']))
            $this->db->join('vicflora_classification cl', 't.TaxonID=cl.TaxonID');
        
        $this->db->group_by('cl.Genus');
        
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $facet['items'][] = array(
                    'name' => $row->Genus,
                    'label' => $row->Genus,
                    'count' => $row->num
                );
            }
        }
            
        return $facet;
    }
    
    public function facetTaxonRank($term, $fq) {
        $facet = array();
        $facet['name'] = 'rank';
        $facet['label'] = 'Taxon rank';
        $facet['collapsible'] = 1;
        $facet['items'] = array();
        
        $this->db->select('td.Name AS TaxonRank, count(*) AS num', FALSE);
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->where('t.TaxonomicStatus', 'Accepted');
        $this->db->where('t.RankID >', 60);
        $this->db->group_by('t.RankID');
        
        if ($term)
            $this->db->like('n.FullName', $term, 'after');

        if ($fq)
            $q = $this->parseFacetQuery($fq);
        
        if (!$fq || !in_array('td', $q['join']))
            $this->db->join('vicflora_taxontreedefitem td', 't.TaxonTreeDefItemID=td.TaxonTreeDefItemID');

        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $facet['items'][] = array(
                    'name' => $row->TaxonRank,
                    'label' => $row->TaxonRank,
                    'count' => $row->num
                );
            }
        }
            
        return $facet;
    }
    
}

?>
