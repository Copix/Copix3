<?php
/**
 * @package webtools
 * @subpackage index_search
 * @author Duboeuf Damien
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * @package webtools
 * @subpackage index_search
 * 
 * ZoneIndexSearchForm
 */
class ZoneIndexSearchForm extends CopixZone {
	/**
	 * Affichage de l'écran de recherche
	 * @param string $toReturn
	 * @return boolean
	 */
	protected function _createContent (& $toReturn){
		$tpl = new CopixTpl ();
		$tpl->assign ('criteria', $this->getParam ('criteria', CopixRequest::get ('criteria')));
		$tpl->assign ('standalone', $this->getParam ('standalone'));
		$tpl->assign ('form_action', $this->getParam ('form_action', _url ('index_search||', $this->getParam ('params', array()))));
		$toReturn = $tpl->fetch ('index_search|search.form.tpl');
		return true;
	}
}