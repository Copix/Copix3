<?php
/**
* @package	cms
* @subpackage cms_portlet_newsletter
* Ecran de modification d'une portlet de type newsletter
*/

/**
* @package	cms
* @subpackage cms_portlet_newsletter
* ZoneEditNewsLetterPortlet
*/
class ZoneEditNewsletterPortlet extends CopixZone {
	/**
	* Attends un objet de type formulaire en paramètre.
	*/
	function _createContent (& $toReturn){
		$tpl   = & new CopixTpl ();

		$dao   = & CopixDAOFactory::create ('newsletter|NewsletterGroups');
		$tpl->assign ('listGroup', $dao->findAll ());
		$tpl->assign ('toEdit',$this->_params['toEdit']);

		switch ($this->_params['kind']){
			case 0:
			$kind = "general";
			break;

			case 1:
			$kind = "preview";
			break;

			default:
			$kind = "general";
			break;
		}

		if ($kind == "preview") {
			$tpl->assign ('show', $this->_params['toEdit']->getParsed ("edit"));
		}
		$tpl->assign ('kind', $kind);

		//recherche de templates d'affichage
		$possibleKinds = CopixTpl::find ('cms_portlet_newsletter', '.portlet.?tpl');
		$tpl->assign ('possibleKinds', $possibleKinds);

		//appel du template.
		$toReturn = $tpl->fetch ('cms_portlet_newsletter|newsletter.edit.tpl');
		return true;
	}
}
?>