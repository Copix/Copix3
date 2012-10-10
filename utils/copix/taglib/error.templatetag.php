<?php
/**
 * @package copix
 * @subpackage taglib
 * @author Gérald Croës, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Affiche un bloc contenant les messages d'erreur, si message est vide, n'affiche rien
 * Permet de toujours appeler le tag sans faire de if dans le template
 * Paramètres :
 * 		- message : chaine ou tableau de chaine, contenant le ou les erreurs
 * 		- class : classe du div qui contient les erreurs, par défaut errorMessage
 * 		- title : titre du bloc
 * 		- titlei18n : titre i18n du bloc
 *
 * @package copix
 * @subpackage taglib
 */
class TemplateTagError extends CopixTemplateTag {
	/**
	 * Retourne le contenu HTML du tag
	 *
	 * @param array $pParams Paramètres passés au tag
	 * @return string
	 */
	public function process ($pParams = null) {
		extract ($this->getParams ());
		if (!isset ($message) || (is_array ($message) && count ($message) == 0) || (is_string ($message) && trim ($message) == '')) {
			return null;
		}		
		$tpl = new CopixTPL ();
		
		if (!isset ($class)) {
			$class = 'errorMessage';
		}
		$tpl->assign ('class', $class);
		
		if (!is_array ($message)) {
			$message = array ($message);
		}
		$tpl->assign ('errors', $message);
		
		if (isset ($titlei18n)) {
			$title = _i18n ($titlei18n);
		}
		if (!isset ($title)) {
			$title = (count ($message) > 1) ? _i18n ('copix:taglib.errorsTitle') : _i18n ('copix:taglib.errorTitle');
		}
		$tpl->assign ('title', $title);
		
		// on met le 1er message dans une autre variable, au cas ou il y ait une seul message, il est dur de le récupérer dans un TPL à cause des tableaux associatifs
		$tpl->assign ('error', array_shift ($message));
		
		return $tpl->fetch ('default|taglib/error.php');
	}
}