<?php
/**
 * @package     copix
 * @subpackage  taglib
 * @author      Steevan BARBOYON
 * @copyright   CopixTeam
 * @link        http://www.copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Balise capable d'afficher une liste d'onglets
 * @package    copix
 * @subpackage taglib
 */
class TemplateTagTabs extends CopixTemplateTag {

	public function process ($pContent = null) {
		$pParams = $this->getParams ();
		if (is_null ($pContent)) {
			return;
		}

		// ---------------------------
		// verification des parametres
		// ---------------------------

		if (!isset ($pParams['ul_class'])){
			$pParams['ul_class'] = '';
		} else if (trim($pParams['ul_class']) != '') {
			$pParams['ul_class'] = 'class="' . $pParams['ul_class']  . '"';
		}

		if (!isset ($pParams['li_class'])){
			$pParams['li_class'] = '';
		} else if (trim($pParams['li_class']) != '') {
			$pParams['li_class'] = 'class="' . $pParams['li_class']  . '"';
		}

		if (!isset ($pParams['li_class_selected'])){
			$pParams['li_class_selected'] = '';
		} else if (trim($pParams['li_class_selected']) != '') {
			$pParams['li_class_selected'] = 'class="' . $pParams['li_class_selected']  . '"';
		}

		if (!isset ($pParams['content_class'])){
			$pParams['content_class'] = '';
		} else if (trim($pParams['content_class']) != '') {
			$pParams['content_class'] = 'class="' . $pParams['content_class']  . '"';
		}
	  
		if (!isset ($pParams['values'])){
			$pParams['values'][''] = 'Défaut';
		}

		if (!isset ($pParams['selected'])){
			$pParams['selected'] = '';
		}

		// ---------------------
		// creation du code HTML
		// ---------------------
			
		$tabs = explode('|', $pParams['values']);
		$toReturn = '<ul ' . $pParams['ul_class'] . '>';
		 
		foreach ($pParams['values'] as $tab) {
			$li_class = ($tab->url == $pParams['selected']) ? $pParams['li_class_selected'] : $pParams['li_class'];
			$toReturn .= '<li ' . $li_class . '>';
			if ($tab->enable == 1) {
				$toReturn .= '<a href="' . $tab->url . '">';
				$toReturn .= $tab->caption;
				$toReturn .= '</a>';
			} else {
				$toReturn .= $tab->caption;
			}
			$toReturn .= '</li>';
		}
		 
		$toReturn .= '</ul>';
		$toReturn .= '<div ' . $pParams['content_class'] . '>';
		$toReturn .= $pContent;
		$toReturn .= '</div>';
		 
		return $toReturn;
	}
}