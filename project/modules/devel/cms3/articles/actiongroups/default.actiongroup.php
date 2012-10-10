<?php
/**
 * @package     cms
 * @subpackage  articles
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Gestion de l'upload des images  
 * 
 * @package cms
 * @subpackage images
 */
class ActionGroupDefault extends CopixActionGroup {

	/**
	 * 
	 * Retour de page aprés ajout dynamique d'article en édition de page
	 */
	public function processConfirmArticleChooser(){
		$ppo = new CopixPPO();
		CopixConfig::instance()->mainTemplate = "default|popup.php";
		return _arPPO($ppo, "confirmarticlechooser.php");
	}
}