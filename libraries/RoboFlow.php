<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'third_party/Encoding/Encoding.php';

class RoboFlow {
    private $ci;
    private $db;
    private $csvArray;
    
    public function __construct() {
        $this->ci =& get_instance();
        $this->db = $this->ci->load->database('default', TRUE);
    }
    
    public function update($file) {
        $this->getDataFromFile($file);
        $this->updateVicFloraImageMetadata();
    }
    
    private function getDataFromFile($file) {
        $infile = fopen($file, 'r');
        $firstLine = fgetcsv($infile);
        $this->csvArray = array();
        while (!feof($infile)) {
            $assoc = array();
            $line = fgetcsv($infile);
            if ($line[0]) {
                foreach ($line as $index => $value) {
                    $assoc[$firstLine[$index]] = ($value) ? Encoding::toUTF8($value) : NULL;
                }
                $this->csvArray[] = $assoc;
            }
        }
        fclose($infile);
    }
    
    private function updateVicFloraImageMetadata() {
        $handle = fopen('logs/image_upload_' . date('Ymd_Hi') . '.csv', 'w');
        fputcsv($handle, array_keys((array) new ImageRecord()));
        foreach ($this->csvArray as $row) {
            $rec = $this->createImageRecord($row);
            if ((!$rec->Latitude || ($rec->Latitude > -90 && $rec->Latitude < 90)) 
                    && (!$rec->Longitude || ($rec->Longitude > -180 && $rec->Longitude < 180))) {
                $flrec = $this->findRecord($row['CumulusCatalog'], $row['CumulusRecordID']);
                if ($flrec) {
                    $imageID = $flrec->ImageID;
                    $rec->TimestampCreated = $flrec->TimestampCreated;
                    $rec->TimestampModified = date('Y-m-d H:i:s');
                    $rec->GUID = $flrec->GUID;
                    $rec->Version = $flrec->Version + 1;
                    $this->updateImage($rec, $imageID);
                }
                else {
                    $rec->TimestampCreated = date('Y-m-d H:i:s');
                    $rec->TimestampModified = date('Y-m-d H:i:s');
                    $rec->GUID = UUID::v4();
                    $rec->Version = 1;
                    $this->insertImage($rec);
                }
                fputcsv($handle, array_values((array) $rec));
            }
        }
        fclose($handle);
        //$this->deleteOldRecords();
    }
    
    private function createImageRecord($row) {
        $rec = new ImageRecord();
        $rec->CumulusRecordID = $row['CumulusRecordID'];
        $rec->CumulusRecordName = $row['CumulusRecordName'];
        $rec->CumulusCatalogue = $row['CumulusCatalog'];
        $rec->Title = $row['dcterms:title'];
        $rec->Source = $row['dc:source'];
        $rec->Modified = $row['dcterms:modified'];
        $rec->DCType = $row['dcterms:type'];
        $rec->Subtype = ($row['ac:subtype'] == 'Illustration') ? 'Illustration' : 'Photograph';
        if ($row['ac:caption']) {
            $rec->Caption = (substr($row['ac:caption'], -1) == '.') ? $row['ac:caption'] : $row['ac:caption'] . '.';
        }
        else {
            $rec->Caption = null;
        }
        $rec->SubjectCategory = $row['iptc:CVTerm'];
        $rec->SubjectPart = $row['ac:subjectPart'];
        $rec->SubjectOrientation = $row['ac:subjectOrientation'];
        $rec->CreateDate = $row['xmp:CreateDate'];
        $rec->DigitizationDate = $row['ac:digitizationDate'];
        $rec->Creator = $row['dcterms:creator'];
        $rec->RightsHolder = $row['dcterms:rightsHolder'];
        $rec->License = $row['dcterms:license'];
        if (in_array($rec->RightsHolder, array('Royal Botanic Gardens Victoria', 'Royal Botanic Gardens Board'))) {
            $rec->License = 'CC BY-NC-SA 4.0';
        }
        $rec->Rights = $row['dc:rights'];
        $rec->ScientificName = Encoding::toUTF8($row['dwc:scientificName']);
        $rec->CatalogNumber = $row['dwc:catalogNumber'];
        $rec->RecordedBy = $row['dwc:recordedBy'];
        $rec->RecordNumber = $row['dwc:recordNumber'];
        $rec->Country = $row['dwc:country'];
        $rec->CountryCode = $row['dwc:countryCode'];
        $rec->StateProvince = $row['dwc:stateProvince'];
        $rec->Locality = $row['dwc:locality'];
        $rec->Latitude = (float) $row['dwc:decimalLatitude'];
        $rec->Longitude = (float) $row['dwc:decimalLongitude'];
        $rec->PixelXDimension = $row['exif:PixelXDimension'];
        $rec->PixelYDimension = $row['exif:PixelYDimension'];
        $rec->HeroImage = ($row['HeroImage'] == 'true') ? true : false;
        $rec->Rating = $row['xmp:Rating'];
        $rec->ThumbnailUrlEnabled = (isset($row['ThumbnailUrlEnabled']) && strtolower($row['ThumbnailUrlEnabled'])=='true') ? TRUE : FALSE;
        $rec->PreviewUrlEnabled = (isset($row['PreviewUrlEnabled']) && strtolower($row['PreviewUrlEnabled'])=='true') ? TRUE : FALSE;
        $rec->FileFormat = (isset($row['FileFormat'])) ? $row['FileFormat'] : NULL;
        $tax = $this->findTaxon(Encoding::toUTF8($row['dwc:scientificName']));
        if ($tax) {
            $rec->TaxonID = $tax->TaxonID;
            $rec->AcceptedID = $tax->AcceptedID;
        }
        return $rec;
    }
    
    private function findTaxon($scientificName) {
        $scientificName = str_replace('×', '', $scientificName);
        $this->db->select('t.TaxonID, at.TaxonID AS AcceptedID');
        $this->db->from('vicflora_taxon t');
        $this->db->join('vicflora_name n', 't.NameID=n.NameID');
        $this->db->join('vicflora_taxon at', 't.AcceptedID=at.TaxonID', 'left');
        $this->db->join('vicflora_name an', 'at.NameID=an.NameID', 'left');
        $this->db->where("REPLACE(n.FullName, '×', '')=" . $this->db->escape($scientificName), FALSE, FALSE);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        }
    }
    
    private function findRecord($cumulusCatalog, $cumulusRecordID) {
        $this->db->select('ImageID, TimestampCreated, Version, GUID, Modified');
        $this->db->from('cumulus_image');
        $this->db->where('CumulusCatalogue', $cumulusCatalog);
        $this->db->where('CumulusRecordID', $cumulusRecordID);
        $query = $this->db->get();
        if ($query->num_rows()) {
            return $query->row();
        }
    }
    
    private function updateImage($data, $imageID) {
        try {
            $this->db->where('ImageID', $imageID);
            $this->db->update('cumulus_image', $data);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    
    private function insertImage($data) {
        $this->db->insert('cumulus_image', $data);
    }
    
    public function deleteOldRecords($startTime) {
        $this->db->where('TimestampModified <', $startTime);
        $this->db->delete('cumulus_image');
    }
    
}

class ImageRecord {
    var $TimestampCreated = NULL;
    var $TimestampModified = NULL;
    var $Version = NULL;
    var $GUID = NULL;
    var $CumulusRecordID = NULL;
    var $CumulusRecordName = NULL;
    var $CumulusCatalogue = NULL;
    var $Title = NULL;
    var $Source = NULL;
    var $Modified = NULL;
    var $DCType = NULL;
    var $Subtype = NULL;
    var $Caption = NULL;
    var $SubjectCategory = NULL;
    var $SubjectPart = NULL;
    var $SubjectOrientation = NULL;
    var $CreateDate = NULL;
    var $DigitizationDate = NULL;
    var $Creator = NULL;
    var $RightsHolder = NULL;
    var $License = NULL;
    var $Rights = NULL;
    var $ScientificName = NULL;
    var $TaxonID = NULL;
    var $AcceptedID = NULL;
    var $CatalogNumber = NULL;
    var $RecordedBy = NULL;
    var $RecordNumber = NULL;
    var $Country = NULL;
    var $CountryCode = NULL;
    var $StateProvince = NULL;
    var $Locality = NULL;
    var $Latitude = NULL;
    var $Longitude = NULL;
    var $PixelXDimension = NULL;
    var $PixelYDimension = NULL;
    var $HeroImage = NULL;
    var $Rating = NULL;
    var $ThumbnailUrlEnabled = NULL;
    var $PreviewUrlEnabled = NULL;
    var $FileFormat = NULL;
}

/* End of file RoboFlow.php */
/* Location: ./libraries/RoboFlow.php */
