<?php
/**
 * @package cms
 * @subpackage cms_portlet_searchengine
 * show the list of the known pages.
*/

/**
 * @package cms
 * @subpackage cms_portlet_searchengine
 * ZoneEditSearch
 */
class ZoneEditSearch extends CopixZone {
	/**
	* Permet de paramétrer le texte de présentation de la portlet de recherche
	*/
	function _createContent (&$ToReturn){
		$tpl   = & new CopixTpl ();

		$tpl->assign ('toEdit', $this->_params['toEdit']);

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
			$tpl->assign ('show', $this->_params['toEdit']->getParsed ("content"));
		}

		//recherche de templates.
		$tpl->assign ('possibleKinds', CopixTpl::find ('cms_portlet_searchengine', '.portlet.?tpl'));

		$tpl->assign ('kind', $kind);
		if (isset($this->_params['toEdit']->idPortletResultPage)){
			$tpl->assign ('pageName', $this->_getNamePage($this->_params['toEdit']->idPortletResultPage));
		}

		//appel du template.
		$ToReturn = $tpl->fetch ('cms_portlet_searchengine|searchengine.edit.tpl');
		return true;
	}

	function _getNamePage ($id) {
		$daoPage =  & CopixDAOFactory::createRecord ('cms|cmspage');
		$dao = & CopixDAOFactory::getInstanceOf ('cms|cmspage');
		$sp  = & CopixDAOFactory::createSearchParams();

		$sp->addCondition ('id_cmsp', '=', $id);
		$sp->orderBy (array('version_cmsp', 'DESC'));

		$data =  $dao->findBy( $sp ) ;
		if (count ($data)){
		   return $data[0]->title_cmsp ;
		}
		return null;
	}
}
?>