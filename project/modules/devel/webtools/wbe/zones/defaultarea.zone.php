<?php
/**
 * @package		webtools
 * @subpackage	wbe
 * @author		Favre Brice
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Affichage d'une zone éditable
 * @package		webtools
 * @subpackage	wbe
 */
class ZoneDefaultArea extends CopixZone {
	function _createContent (& $toReturn){		
		$params = array ();
		$params['name'] = $this->getParam ('name', 'content');
		$params['cols'] = $this->getParam ('cols', '50');
		$params['rows'] = $this->getParam ('rows', '15');
		$params['content'] = $this->getParam ('content', '');

		switch (CopixConfig::get ('wbe|defaultwbe')) {
			case 'tinymce':
				$toReturn = CopixZone::process('tinymcearea', $params);
				break;
			case 'fck':
				$toReturn = CopixZone::process('fckeditorarea', $params);
				break;
			default:
				return false;
		}
		return true;
	}
}
?>