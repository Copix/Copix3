<?php
/**
 * @package standard
 * @subpackage generictools
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion d'images
 *
 * @package standard
 * @subpackage generictools
 */
class ActionGroupImage extends CopixActionGroup {
	/**
	 * Retourne l'image demandée avec les dimensions voulues
	 *
	 * @return CopixActionReturn
	 */
	public function processGet () {
		$image = CopixImage::load (CopixURL::getResourcePath (_request ('image')));
		$image->resize (_request ('width'), _request ('height'), true);
		return _arString ($image->getContent ());
	}
}