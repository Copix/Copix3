<?php
/**
* @package   copix
* @subpackage core
* @author    Salleyron Julien
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license   http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* SoapClient 
* @package copix
* @subpackage core
*/
class CopixSoapClient {
    /**
     * renvoi un client soap 
     * @param string Le serveur soap
     * @return object SoapObject
     */
    public static function get($pSoapServer) {
        
        if (CopixConfig::get('default|proxyEnabled')) {
            //proxy_host, proxy_port, proxy_login  et proxy_password
            $params = array();
            if (CopixConfig::get('default|proxyHost') != null) {
                $params['proxy_host'] = str_replace('http://','',CopixConfig::get('default|proxyHost'));
            }
            if (CopixConfig::get('default|proxyPort') != null) {
                $params['proxy_port'] = intval(CopixConfig::get('default|proxyPort'));
            }
            if (CopixConfig::get('default|proxyUser') != null) {
                $params['proxy_login'] = CopixConfig::get('default|proxyUser');
            }
            if (CopixConfig::get('default|proxyPass') != null) {
                $params['proxy_password'] = CopixConfig::get('default|proxyPass');
            }
        }
        $params['compression'] = SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP;
        
        $soap = new SoapClient($pSoapServer,$params);
        return $soap;
    }
}
?>