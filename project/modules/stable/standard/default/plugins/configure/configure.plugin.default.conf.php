<?php
/**
* @package		standard
* @subpackage	plugin_configure
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package		standard
* @subpackage 	plugin_configure
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class PluginDefaultConfigConfigure {
	/**
	 * Liste des répertoires qui doivent être en écriture
	 *
	 * @var array
	 */
   protected $_dir = array ();

   /**
    * Initialisation des paramètres requis
    */
   public function __construct (){
   	$this->_dir = array(
	   	COPIX_LOG_PATH,
	   	COPIX_VAR_PATH,
	   	COPIX_CACHE_PATH,
	   	COPIX_CACHE_PATH.'php',
	   	COPIX_CACHE_PATH.'php/templates',
	   	COPIX_CACHE_PATH.'html',
	   	COPIX_CACHE_PATH.'html/templates',
	   	COPIX_CACHE_PATH.'html/default',
	   	COPIX_CACHE_PATH.'html/zones',
	   	COPIX_VAR_PATH.'config/',
	   	COPIX_TEMP_PATH.'testreport'
    );
   }
   
   /**
    * Retourne la liste des répertoires qui doivent être en écriture
    *
    * @return array
    */
	public function getDirectories (){
		return $this->_dir;
	}
}
?>