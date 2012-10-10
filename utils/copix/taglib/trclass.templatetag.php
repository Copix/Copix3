<?php
/**
 * @package copix
 * @subpackage taglib
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Retourne la classe d'un tru (aucune, alternate ou highlight)
 *
 * @package copix
 * @subpackage taglib
 */
class TemplateTagTrClass extends CopixTemplateTag {
	/**
	 * Dernière classe de chaque id, pour pouvoir faire la rotation
	 *
	 * @var array
	 */
	private static $_classes = array ();

	/**
	 * Génération de l'HTML
	 *
	 * @param string $pContent Contenu passé en paramètre
	 * @return string
	 */
	public function process ($pContent) {
		$id = (string)$this->getParam ('id', 'default');
		if (!array_key_exists ($id, self::$_classes)) {
			self::$_classes[$id] = 'alternate';
		}

		$nameOnly = $this->getParam ('nameOnly', false);
		$highlight = $this->getParam ('highlight', false);
		self::$_classes[$id] = (self::$_classes[$id] == null) ? 'alternate' : null;
		if ($highlight !== false) {
			if ($highlight == _request ('highlight')) {
				return ($nameOnly) ? 'highlight' : 'class="highlight"';
			}
		}
		if (self::$_classes[$id] == 'alternate') {
			return ($nameOnly) ? 'alternate' : 'class="alternate"';
		}
	}
}