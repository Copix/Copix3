<?php
/**
 * @package tools
 * @subpackage tags
 * @author  Florian JUDITH
 * @copyright   CopixTeam
 * @link        http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Zone qui affiche les tags associés à une association, avec en option un lien pour chaque tag
 * Paramètres : 
 * 	idObject / kindObject : les informations sur l'objet sur lequel porte l'association avec les tags
 * 	(TagServices::getAssociation())
 *  doGetLink : 'true' si on souhaite associer un lien avec chaque tag
 *  linkCorpse : le corp du lien à associer au tag
 *  linkParam : le nom du paramètre a fixer dans le lien avec le nom du tag
 * @package tools
 * @subpackage tags
 */
class ZoneGetTagsOfAssociation extends CopixZone {
	function _createContent(&$toReturn) {
		$idObjet = $this->getParam('idObject');
		$kindObject = $this->getParam('kindObject');
		$doGetLink = ($this->getParam('doGetLink') == 'true')?true:false;
		$arrTags = _class('tags|tagservices')->getAssociation($idObjet,$kindObject);
		$tpl = new CopixTpl();
		$tpl->assign('arrTags',$arrTags);
		$tpl->assign('doGetLink',$doGetLink);
		if ($doGetLink) {
			$tpl->assign('linkCorpse',$this->getParam('linkCorpse'));
			$tpl->assign('linkParam',$this->getParam('linkParam'));
		}
		$toReturn = $tpl->fetch('tags.getassociation.zone.php');
		return false;
	}
}