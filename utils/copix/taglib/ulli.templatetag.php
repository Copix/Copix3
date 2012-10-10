<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagUlLi extends CopixTemplateTag {
	public function process ($pContent = null) {
		$values = $this->requireParam ('values');
		if (!is_array ($values)) {
			if (strlen ($values) == 0) {
				return false;
			}
			$values =  (array)$values;
		}
		$extras = ($this->getParam ('extras')) ? ' '.$this->getParam ('extras') : '';
		$showKeys = ($this->getParam ('showKeys', false));
		return $this->ulli_internal_li ($values, $extras, $showKeys);
	}
	
	/**
	 * Génération des listes
	 * @param	mixed 	$values	éléments que l'on souhaite mettre dans la liste.
	 * @return string	le code HTML correspondant au UL / LI	
	 */
	private function ulli_internal_li ($values, $extras = '', $showKeys = false) {
		$toReturn = '';
		if (is_array ($values)) {
			// Evite les tableaux vides
			if (count ($values)) {
				$toReturn .= '<ul'.$extras.'>';
				foreach ($values as $key => $item) {
					// Evite les lignes vides
					if ((is_array ($item) && count ($item) === 0) || (is_string ($item) && strlen($item) === 0)) {
						continue;
					}
					$toReturn .= '<li>';
					if ($showKeys && is_string ($key)) {
						$toReturn .= $key;
						$item = is_array ($item) ? $item : (array)$item;
					}
					$toReturn .= $this->ulli_internal_li ($item, '', $showKeys);
					$toReturn .= '</li>';
				}
				$toReturn .= '</ul>';
			}
		} else {
			$toReturn .= $values;
		}
		return $toReturn;
	}
}