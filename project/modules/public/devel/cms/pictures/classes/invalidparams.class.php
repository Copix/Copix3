<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package	cms
 * @subpackage pictures
 * Paramètres invalides lors de la modification d'une categorie
 */
class InvalidParams  {
	var $maxX;
	var $maxY;
	var $maxWeight;
	var $invalidFormat = array();

	function InvalidParams ($maxX, $maxY, $maxWeight,$invalidFormat) {
		$this->maxX         = $maxX;
		$this->maxY         = $maxY;
		$this->maxWeight    = $maxWeight;
		$this->invalidFormat= $invalidFormat;
	}
}
?>