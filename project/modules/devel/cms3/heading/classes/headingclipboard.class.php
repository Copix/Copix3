<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 */

/**
 * Gestion du presse-papier
 *
 * @package cms
 * @subpackage heading
 */
class HeadingClipboard {
	/**
	 * Mode copier
	 */
	const MODE_COPY = 'copy';

	/**
	 * Mode couper
	 */
	const MODE_CUT = 'cut';

	/**
	 * Cache du chemin des éléments du presse-papier
	 *
	 * @var string
	 */
	private static $_path = null;

	/**
	 * Retourne le contenu du presse-papier, à savoir les identifiants publiques
	 *
	 * @return array
	 */
	public static function getContent () {
		$content = array ();
		$services = _ioClass ('HeadingElementInformationServices');
		$update = false;
		foreach (CopixSession::get ('clipboard|content', 'heading', array ()) as $id){
			try {
				$services->get ($id);//on valide que l'élément existe.
				$content[] = $id;
			}catch (HeadingElementInformationNotFoundException $e){
				$update = true;				
			}
		}
		
		if ($update){
			CopixSession::set ('clipboard|content', $content, 'heading');
		}

		return $content;
	}

	/**
	 * Retourne les records des éléments du presse-papier
	 *
	 * @return array
	 */
	public static function getElements () {
		$toReturn = array ();
		$services = _ioClass ('HeadingElementInformationServices');
		foreach (self::getContent () as $publicId) {
			$toReturn[] = $services->get ($publicId);
		}
		return $toReturn;
	}

	/**
	 * Ajoute un élément dans le presse-papier
	 *
	 * @param int $pPublicId Identifiant publique
	 */
	public static function add ($pPublicId) {
		$elements = self::getContent ();
		if (!in_array ($pPublicId, $elements)) {
			$elements[] = $pPublicId;
			self::set ($elements);
		}
	}

	/**
	 * Définit les éléments du presse-papier
	 *
	 * @param mixed $pPublicId Identifiant(s) publique(s)
	 */
	public static function set ($pPublicId) {
		if (!is_array ($pPublicId)) {
			$pPublicId = array ($pPublicId);
		}
		CopixSession::set ('clipboard|content', $pPublicId, 'heading');
	}

	/**
	 * Vide le contenu
	 */
	public static function clear () {
		CopixSession::delete ('clipboard|content', 'heading');
		CopixSession::delete ('clipboard|mode', 'heading');
	}

	/**
	 * Retourne le mode, self::MODE_XX
	 *
	 * @return string
	 */
	public static function getMode () {
		return CopixSession::get ('clipboard|mode', 'heading', self::MODE_COPY);
	}

	/**
	 * Définit le mode
	 *
	 * @param string $pMode Mode, utiliser self::MODE_
	 */
	public static function setMode ($pMode) {
		if (!in_array ($pMode, array (self::MODE_COPY, self::MODE_CUT))) {
			throw new CopixException ('Le mode "' . $pMode . '" ne correspond à aucun mode connu pour le presse-papier.');
		}
		CopixSession::set ('clipboard|mode', $pMode, 'heading');
	}

	/**
	 * Retourne le chemin vers les éléments du presse-papier
	 *
	 * @return array
	 */
	public static function getPath () {
		if (self::$_path == null && count (self::getContent ()) > 0) {
			$services = _ioClass ('HeadingElementInformationServices');
			$elements = self::getElements ();
			$ids = explode ('-', $services->get ($elements[0]->parent_heading_public_id_hei)->hierarchy_hei);
			// suppression de la racine du site
			array_shift ($ids);
			self::$_path = array ();
			foreach ($ids as $breadcumbPublicId) {
				self::$_path[] = $services->get ($breadcumbPublicId)->caption_hei;
			}
		}
		return self::$_path;
	}

	/**
	 * Indique si on peut copier dans le répertoire demandé
	 *
	 * @param int $pPublicId Identifiant publique du répertoire
	 * @return boolean
	 */
	public static function canPaste ($pPublicId) {
		$content = self::getContent ();
        if (count ($content) == 0) {
			return false;
		} else {
			$path = _ioClass ('headingservices')->getPath ($pPublicId);
			foreach ($content as $publicId) {
				if (in_array ($publicId, $path)) {
					return false;
				}
            }
        }
		return true;
	}
}