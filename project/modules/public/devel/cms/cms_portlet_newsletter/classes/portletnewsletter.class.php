<?php
/**
* @package	cms
* @subpackage cms_portlet_newsletter
* @author	???
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|Portlet');

/**
* @package	cms
* @subpackage cms_portlet_newsletter
* Portlet permettant l'inscription à des newsletter
*/
class PortletNewsletter extends Portlet {
	/**
	* Titre de la zone
	*/
	var $title;
	
	/**
	* A quels groupes la portlet permet elle de s'inscrire ?
	*/
	var $id_group;
	
	/**
    * Contenu de la portlet en HTML
    */
	function getParsed ($context) {
		//Choix du type du bouton
		if ($context=="edit") {
			$submit = "0";
		}else{
			$submit = "1";
		}

		//Assignation des variables
		$tpl = & new CopixTpl ();
		$tpl->assign ('portletTitle', $this->title);
		$dao   = & CopixDAOFactory::create ('newsletter|newslettergroups');
		$tpl->assign ('groupList', $dao->findAll());
		$tpl->assign ('idGroup',   $this->id_group);
		$tpl->assign ('submit'   , $submit);

		return $tpl->fetch ($this->templateId ? $this->getTemplateSelector() : 'cms_portlet_newsletter|normal.newsletter.tpl');
	}

	function getGroup (){
		return 'general';
	}
	function getI18NKey (){
		return 'cms_portlet_newsletter|newsletter.description';
	}
	function getGroupI18NKey (){
		return 'cms_portlet_newsletter|newsletter.group';
	}
}
/**
* @package	cms
* @subpackage cms_portlet_newsletter
* Pour des raisons de compatibilité, l'ancienne convention étant de nommer les portlet XXXPortlet et non PortletXXX
*/
class NewsletterPortlet extends PortletNewsletter {}
?>