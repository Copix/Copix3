<?php
/**
* @package		standard 
 * @subpackage	generictools
* @version    1.0
* @author     Nicolas CHARLOT, nicolas.charlot@isics.fr
* @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Zone affichant le fil d'ariane (CopixBreadcrumbs)
*
* Paramètres
* - breadcrumbsId : identifiant du fil d'ariane (si non précisé, 'main' est pris par défaut)
* - from          : niveau de départ (si non précisé, 0 est pris par défaut)
* - title         : titre du fil (facultatif)
* - rootLink      : url du lien racine (ne sert à rien si le texte du lien n'est pas précisé)
* - rootText      : texte du lien racine
* - template      : template à utiliser (si non précisé, 'breadcrumbs.tpl' est pris par défaut)
* @package		standard 
 * @subpackage	generictools
*/
class ZoneCopixBreadcrumbs extends CopixZone {
	function  _createContent (& $toReturn){
		$tpl = new CopixTpl ();

		// ID du fil à afficher (Si non précisé, on prend 'main' par défaut)
		$breadcrumbsId = $this->getParam ('breadcrumbsId', 'main');

		$cBc = CopixClassesFactory::getInstanceOf ('generictools|CopixBreadcrumbs', $breadcrumbsId);

		// Niveau de départ (Si non précisé, on prend 0 par défaut, c'est à dire la racine)
		$from = $this->getParam ('from', 0);

		// Titre éventuel du fil d'ariane (ex. : "Vous êtes ici :")
		$tpl->assign ('title', $this->getParam ('title', null));

		// Récupération du fil d'ariane (tableau de liens)
		$arCBcItems = $cBc->getItems ($from);

		// Racine du chemin (si l'url n'est pas précisée, on prend celle de la page par défaut)
		if (isset ($this->_params['rootText']) && $from == 0){
			$link = $this->getParam ('rootLink', CopixUrl::get ().CopixConfig::get ('|homePage'));
			array_unshift ($arCBcItems, new CopixBreadcrumbsItem ($link, $this->_params['rootText']));// Ajout de l'élément racine en DEBUT de fil (d'où le dernier paramètre à vrai)
		}
		$tpl->assign ('arBreadcrumbsItems', $arCBcItems);

		// Template à utiliser (Si non précisé, on prend 'breadcrumbs.tpl' par défaut)
		$toReturn = $tpl->fetch ($this->getParam ('template', 'breadcrumbs.tpl'));
		return true;

	}
}
?>