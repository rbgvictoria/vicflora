<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* 
 * Copyright 2016 Royal Botanic Gardens Victoria.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */



class WS extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function specimen_image_thumbnails($taxonId)
    {
        $this->load->model('webservicemodel');
        $this->load->helper('json');
        $node = $this->webservicemodel->getNode($taxonId);
        if (!$node) {
            exit('Taxon not found');
        }
                
        $limit = $this->input->get('limit') ?: 12;
        $offset = $this->input->get('offset') ?: 0;
        
        $count = $this->webservicemodel->getSpecimenImageCount($node->nodeNumber, $node->highestDescendantNodeNumber);
        $data = $this->webservicemodel->getSpecimenImages($node->nodeNumber, $node->highestDescendantNodeNumber, $limit, $offset);
        
        $json = array();
        $json['meta'] = array();
        $json['meta']['totalCount'] = $count;
        $json['meta']['taxonId'] = $taxonId;
        $json['meta']['limit'] = (int) $limit;
        $json['meta']['offset'] = (int) $offset;
        $json['data'] = $data;
        
        
        
        echo json_output($json);
    }
    
    public function other_image_thumbnails($taxonId)
    {
        $this->load->model('webservicemodel');
        $this->load->helper('json');
        $node = $this->webservicemodel->getNode($taxonId);
        if (!$node) {
            exit('Taxon not found');
        }
                
        $limit = $this->input->get('limit') ?: 12;
        $offset = $this->input->get('offset') ?: 0;
        
        $count = $this->webservicemodel->getSpecimenImageCount($node->nodeNumber, $node->highestDescendantNodeNumber);
        $data = $this->webservicemodel->getSpecimenImages($node->nodeNumber, $node->highestDescendantNodeNumber, $limit, $offset);
        
        $json = array();
        $json['meta'] = array();
        $json['meta']['totalCount'] = $count;
        $json['meta']['taxonId'] = $taxonId;
        $json['meta']['limit'] = (int) $limit;
        $json['meta']['offset'] = (int) $offset;
        $json['data'] = $data;
        
        
        
        echo json_output($json);
    }
    
}


