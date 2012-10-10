<?php
/**
* @package	cms
* @subpackage document
* @author	Bertrand Yan, Croes Gérald, see copix.org for other contributors.
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage document
* DocumentSignificantUrl
 */
class FlashSignificantUrl {
	/**
    * parse
    *
    * Handle url decryption
    *
    * @param path          array of path element
    * @param parameters    array of parameters (eq $this-vars)
    * @param mode          urlsignificantmode : prepend / none
    * @return array([module]=>, [desc]=>, [action]=>, [parametre]=>)
    */
	function parse ($path, $mode) {
		if ($mode!=='prepend'){
			return false;
		}
		if (!(count($path) == 3 && $path[0]=='document')) {
			return false;
		}
		$objInfos = $path[1];
		//nous recevons id_version dans path[1]
		$objInfos = explode ('_', $objInfos);
		if (count ($objInfos) == 2){
			$id_doc = $objInfos[0];
			$version_doc = $objInfos[1];
		}elseif (count ($objInfos == 1)){
			$id_doc = $objInfos[0];
			$version_doc = 0;
		}else{
			return false;
		}
		if (! (is_numeric($id_doc) && is_numeric($version_doc))){
			return false;
		}

		$toReturn = array();
		$toReturn['module']  = 'document';
		$toReturn['desc']    = 'default';
		$toReturn['action']  = 'download';
		$toReturn['id_doc']  = $id_doc;
		$toReturn['version'] = $version_doc;
		return $toReturn;
	}

	/**
    * get
    *
    * Handle url encryption
    *
    * @param dest          array([module]=>, [desc]=>, [action]=>)
    * @param parameters    array of parameters (eq $this-vars)
    * @param mode          urlsignificantmode : prepend / none
    * @return object([path]=>, [vars]=>)
    */
	function get ($dest, $parameters, $mode) {
		$toReturn=new StdClass();
		if ($mode=='none'){
			return false;
		}
		if ($dest['module'] == 'document' && ($dest['desc'] == 'default' || $dest['desc'] == '') && ($dest['action'] == 'download' || $dest['action'] == '')) {
			if (!isset($parameters['id_doc'])) {
				return false;
			}
			$dao = & CopixDAOFactory::getInstanceOf ('document|document');
			$version_doc = isset($parameters['version_doc']) ? $parameters['version_doc'] : $dao->getLastVersion ($parameters['id_doc']);
			if (! $Document = $dao->get ($parameters['id_doc'], $version_doc)) {
				return false;
			}

			$documentName  =  CopixUrl::escapeSpecialChars ($Document->title_doc).'.'.$Document->extension_doc;
			$toReturn->path = array('document', $parameters['id_doc'].'_'.$version_doc,$documentName);
			unset($parameters['id_doc']);
			$toReturn->vars = $parameters;
			$headingServices = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixHeadingsServices');
			$toReturn->basePath  = $headingServices->getDomainFor ($Document->id_head);
            if (($toReturn->basePath !== null) && (($scriptName = substr ($toReturn->basePath, strrpos ($toReturn->basePath, '/'))) != '/')){
				$toReturn->scriptName = $scriptName;
				$toReturn->basePath   = substr ($toReturn->basePath, 0, strrpos ($toReturn->basePath, '/'));
			}
			return $toReturn;
		}else{
			return false;
		}
	}
}
?>
