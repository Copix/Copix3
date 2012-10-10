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
 * Affiche un lien + une image pour trier un affichage
 *
 * @package copix
 * @subpackage taglib
 * @example {order caption="Nom de colonne" order=MonChampDeTri value=MonChampDeTriActuel}
 *
 * Paramètres requis
 *      order : nom du champ de tri, dans l'adresse généré sera suffixé de _asc ou _desc en fonction du tri demandé
 * Paramètres optionnels
 * 		caption ou captioni18n : libellé du lien
 *      value : champ de tri utilisé actuellement, avec le suffixe _asc ou _desc (permet d'afficher l'icone de tri ou non)
 */
class TemplateTagOrder extends CopixTemplateTag {
	/**
	 * Génération de l'HTML
	 *
	 * @param string $pContent Contenu de base
	 */
	public function process ($pContent = null) {
		$this->assertParams ('order');
		
		$toReturn = null;
		$order = $this->getParam ('order');
		$orderASC = $order . '_asc';
		$orderDESC = $order . '_desc';
		$newOrder = $orderASC;
		
		// libellé du lien di demandé
		if (($caption = $this->getParam ('caption')) != null) {
			$toReturn .= $caption . ' ';
		} else if (($captioni18n = $this->getParam ('captioni18n')) != null) {
			$toReturn .= _i18n ($captioni18n) . ' ';
		}
		
		// icone si demandé
		if (($value = $this->getParam ('value')) != null) {
			if ($value == $orderASC) {
				$toReturn .= '<img src="' . _resource ('img/tools/down.png') . '" />';
				$newOrder = $orderDESC;
			} else if ($value == $orderDESC) {
				$toReturn .= '<img src="' . _resource ('img/tools/up.png') . '" />';
			}
		}
		
		// création du lien
		$url = CopixURL::appendToURL (CopixURL::getCurrentUrl (), array ('order' => $newOrder));
		$toReturn = '<a href="' . $url . '">' . $toReturn . '</a>';
		
		return $toReturn;
	}
}