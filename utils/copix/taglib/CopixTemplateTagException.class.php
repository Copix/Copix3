<?php
/**
 * @package copix
 * @subpackage taglib
 * @author Gérald Croës
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Exception pour les templatetags
 *
 * @package copix
 * @subpackage taglib
 */
class CopixTemplateTagException extends CopixException {
	/**
	 * Fichier .templatetag.php non trouvé
	 */
	const FILE_NOT_FOUND = 1;

	/**
	 * Classe du templatetag non trouvée
	 */
	const CLASS_NOT_FOUND = 2;
}