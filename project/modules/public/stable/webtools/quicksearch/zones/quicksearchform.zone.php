<?php
/**
 * @package		webtools
 * @subpackage	quicksearch
* @author	Croës Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package		webtools
 * @subpackage	quicksearch
* 
* ZoneSearchEngineForm
*/
class ZoneQuickSearchForm extends CopixZone {
	/**
	 * Affichage de l'écran de recherche
	 * @param string $toReturn
	 * @return boolean
	 */
    function _createContent (& $toReturn){
        $tpl = new CopixTpl ();
        $tpl->assign ('criteria', $this->getParam ('criteria', CopixRequest::get ('criteria')));
        $toReturn = $tpl->fetch ('search.form.tpl');
        return true;
    }
}
?>