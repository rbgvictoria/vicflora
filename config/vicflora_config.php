<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| VicFlora configuration items
| -------------------------------------------------------------------
*/

/*
 * GeoServer base url
 */

$config['geoserver_url'] = 'http://data.rbg.vic.gov.au/geoserver';


/*
 * SOLR
 */

$config['solr_solarium'] = '../../lib/vendor/autoload.php';
$config['solr_host'] = 'localhost';
$config['solr_port'] = '65002';


/*
 * cUrl option
 */

$config['curl_opts']['proxy'] = '10.15.14.4';
$config['curl_opts']['proxy_port'] = '8080';
$config['curl_opts']['proxy_userpwd'] = 'helpdesk:glass3d';




/* End of file vicflora_config.php */
/* Location: ./application/config/vicflora_config.php */

