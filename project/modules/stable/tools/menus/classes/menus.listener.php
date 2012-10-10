<?php
/**
 * @package tools
 * @subpackage menus
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Ecoute l'événement menu
 * 
 * @package tools
 * @subpackage menus
 */
class ListenerMenus extends CopixListener {
	/**
	 * Ajoute les enfants trouvés
	 *
	 * @param MenusMenu $pMenu
	 * @param array $pItem
	 */
	private function _addChildren ($pMenu, $pItems) {
		foreach ($pItems as $item) {
			$id = (isset ($item['id'])) ? $item['id'] : null;
			$caption = (isset ($item['caption'])) ? $item['caption'] : null;
			$url = (isset ($item['url'])) ? $item['url'] : null;
			$selected = (isset ($item['selected'])) ? $item['selected'] : false;
			$icon = null;
			if (isset ($item['icon'])) {
				$icon = _resource ($item['icon']);
			} else if (isset ($item['icon_url'])) {
				$icon = $item['icon_url'];
			}
			$menu = $pMenu->addChild ($id, $caption, $url, $selected, $icon);
			if (isset ($item['children'])) {
				$this->_addChildren ($menu, $item['children']);
			}
		}
	}

	/**
	 * Sélectionne les menus demandés
	 *
	 * @param array $pSelected
	 * @param MenusMenu $pMenu
	 */
	private function _select ($pSelected, $pMenu) {
		foreach ($pMenu->getChildren () as $menu) {
			foreach ($pSelected as $selected) {
				if ($menu->getId () == $selected) {
					$menu->setSelected (true);
				}
			}
			$this->_select ($pSelected, $menu);
		}
	}

	/**
	 * Ecoute l'événement menu
	 *
	 * @param CopixEvent $pEvent Evénement
	 * @param CopixEventResponse $pEventResponse Réponse à l'événement
	 */
	public function processMenu ($pEvent, $pEventResponse) {
		$id = $pEvent->getParam ('id', 'main');

		// récupération du menu
		if (MenusService::exists ($id)) {
			$menu = MenusService::get ($id);
		} else {
			$menu = MenusService::create ($id);
		}

		// suppression des anciens éléments
		if ($pEvent->getParam ('reset', false)) {
			$menu->clearChildren ();
		}

		// ajout des nouveaux éléments
		$children = $pEvent->getParam ('items', array ());
		if (count ($children) > 0) {
			$this->_addChildren ($menu, $children);
		}

		// sélection d'un item
		$selected = explode ('|', $pEvent->getParam ('selected'));
		// explode nous fait un joli tableau avec 0 => '' même si on n'a rien dans selected ...
		if (count ($selected) > 0 && $selected[0] != null) {
			$this->_select ($selected, $menu);
		}
	}
}