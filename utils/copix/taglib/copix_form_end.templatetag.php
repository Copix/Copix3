<?php
/**
 * @package    copix
 * @subpackage taglib
 * @author     Gérald Croës
 * @copyright  CopixTeam
 * @link       http://www.copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Fin d'une section formulaire
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagCopix_Form_End extends CopixTemplateTag {
	/**
	 * Demande d'exécution du tag
	 */
	public function process ($pContent=null) {
		//récupération du formulaire, création d'un nouveau formulaire si besoin
		$form = CopixFormFactory::get ($this->getParam ('id', null));
		return '</form>';
	}
}