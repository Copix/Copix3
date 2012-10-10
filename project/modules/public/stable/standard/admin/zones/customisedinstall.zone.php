<?php
/**
 * @package standard
 * @subpackage admin 
* 
* @author		Bertrand Yan, Croes Gérald
* @copyright	2001-2005 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Affichage de la liste des modules disponibles pour installation
 * @package standard
 * @subpackage admin 
 * 
 */
class ZoneCustomisedInstall extends CopixZone {
    /**
     * Création du contenu de la page
     */
    function _createContent (&$toReturn) {
      $tpl = new CopixTpl ();
      $tpl->assign ('arModulesPath', CopixConfig::instance ()->arModulesPath);
      $tpl->assign ('arModules', $this->_getModuleOrderByDescription());
      $toReturn = $tpl->fetch ('modules.list.tpl');
    }
    
    /**
    * Récupération des modules dans l'ordre de leur description
    * @todo a trier comme avant
    */
    function _getModuleOrderByDescription (){
    	return _class ('InstallService')->getModules ();
    }
}
?>