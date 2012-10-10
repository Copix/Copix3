<?php
/**
 * @copyright 2001-2006 CopixTeam
 * @author Patrice Ferlet
 * @package cms
 * @link http://www.copix.org
 */
 
 /**
  * @package cms
  * ZoneShowPortlet
  */
class ZoneShowPortlet extends CopixZone {
    /**
    * Affichage du contenu d'une portlet de classe donnée ("name") à laquelle on
    *  va assigner des propriétés
    */
	function _createContent(& $toReturn) {
		$name = $this->getParam('name', false);
		unset ($this->_params['name']);
		if (!isset ($name) or strlen($name) < 1) {
			return false;
		}

		CopixClassesFactory::fileInclude ('PortletFactory');
		CopixClassesFactory::fileInclude ('CmsParseContext');

		$p = PortletFactory::create ($name);
		foreach ($this->_params as $param=>$value) {
			$p->$param = $value;
		}

		$toReturn = $p->getParsed(CmsParseContext::front ());
		return true;
	}
}
?>