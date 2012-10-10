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
 * Diverses réparations sur le CMS
 *
 * @package cms
 * @subpackage heading
 */
class HeadingRepair {
	/**
	 * Retourne la liste des éléments fantomes (qui n'existent que dans les tables spécifiques à chaque élément, ou que dans la table principale)
	 *
	 * @return array
	 */
	public static function findGhosts () {
		$t = new HeadingElementType ();
		$toReturn = array ();
		foreach ($t->getList () as $type => $element) {
			$typeGhosts = _ioClass ($element['classid'])->findGhosts ();
			if (count ($typeGhosts['specific']) > 0 || count ($typeGhosts['general']) > 0) {
				$toReturn[$type]['ghosts'] = $typeGhosts;
				if (count ($typeGhosts['specific']) > 0) {
					foreach ($typeGhosts['specific'] as $ghost) {
						foreach ($ghost as $key => $test) {
							if (substr ($key, 0, 3) == 'id_') {
								$toReturn[$type]['id'] = $key;
								break;
							}
						}
					}
				}
			}
		}
		return $toReturn;
	}

	public static function findDeadLinks () {
		$t = new HeadingElementType ();
		$toReturn = array ();
		foreach ($t->getList () as $type => $element) {
			$deadLinks = _ioClass ($element['classid'])->findDeadLinks ();
			if (count ($deadLinks) > 0) {
				$toReturn[$type] = $deadLinks;
			}
		}
		return $toReturn;
	}
}