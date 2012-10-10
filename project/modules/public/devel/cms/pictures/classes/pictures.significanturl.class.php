<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package cms
 * @subpackage pictures
 * Gestion des URL pour le module phototèque
 */
class PicturesSignificantUrl {
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
		if ($mode!='prepend'){
			return false;
		}

		if (!(count($path) == 3 && $path[0]=='pictures')) {
			return false;
		}

		if (!isset ($path[1])){
			return false;
		}

		if (!is_numeric ($path[1])){
			return false;
		}

		$id_pict = $path[1];
		$toReturn = array();
		$toReturn['module']  = 'pictures';
		$toReturn['desc']    = 'default';
		$toReturn['action']  = 'get';
		$toReturn['id_pict'] = $id_pict;
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
		if ($mode=='none'){
			return false;
		}else{
			if ($dest['module'] == 'pictures' && ($dest['desc'] == 'default' || $dest['desc'] == '') && $dest['action'] == 'get') {

				if (!isset($parameters['id_pict'])) {
					return false;
				}
				$daoPicture = CopixDAOFactory::getInstanceOf ('pictures|pictures');
				if (!($picture = $daoPicture->get($parameters['id_pict']))) {
					return false;
				}
				$pictureName  =  CopixUrl::escapeSpecialChars ($picture->name_pict).'.'.$picture->format_pict;
				unset($parameters['id_pict']);
                $toReturn = new StdClass ();
				$toReturn->path = array('pictures', $picture->id_pict, $pictureName);
				$toReturn->vars = $parameters;

				$headingServices = CopixClassesFactory::getInstanceOf ('copixheadings|CopixHeadingsServices');
                $toReturn->basePath  = $headingServices->getDomainFor ($picture->id_head);
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
}
?>