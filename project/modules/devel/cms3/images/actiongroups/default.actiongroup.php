<?php
/**
 * @package     cms
 * @subpackage  images
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * ActionGroup par défaut 
 * 
 * @package cms
 * @subpackage images
 */
class ActionGroupDefault extends CopixActionGroup {

	/**
	 * 
	 * Retour de page aprés edition dynamique d'image en édition de page
	 */
	public function processConfirmImageEdition(){
		$ppo = new CopixPPO();
		CopixConfig::instance()->mainTemplate = "default|popup.php";
		CopixHTMLHeader::addJSCode("parent.refreshEditionImage"._request('identifiantFormulaire')."();");
		return _arPPO($ppo, "generictools|blank.tpl");
	}
}