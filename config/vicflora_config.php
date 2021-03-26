<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| VicFlora configuration items
| -------------------------------------------------------------------
*/

/*
 * GeoServer base url
 */

$config['geoserver_url'] = 'https://data.rbg.vic.gov.au/geoserver';


/*
 * SOLR
 */

$config['solr_solarium'] = '/var/www/lib/vendor/autoload.php';
$config['solr_host'] = 'localhost';
$config['solr_port'] = '65002';

$cumulusBaseUrl = 'https://data.rbg.vic.gov.au/cip/';
//$config['asset_baseurl'] = $cumulusBaseUrl . 'A/';
$config['preview_baseurl'] = $cumulusBaseUrl . 'preview/image/';
$config['thumbnail_baseurl'] = $cumulusBaseUrl . 'preview/thumbnail/';

$config['ala_image_viewer_base_url'] = 'https://images.ala.org.au/image/viewer?imageId=';


/*
 * cUrl option
 */

$config['curl_opts']['proxy'] = '10.15.14.4';
$config['curl_opts']['proxy_port'] = '8080';
$config['curl_opts']['proxy_userpwd'] = 'helpdesk:glass3d';




/* End of file vicflora_config.php */
/* Location: ./application/config/vicflora_config.php */

