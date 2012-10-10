<?php
/**
* @package		cms
* @subpackage	cms_portlet_wysiwyg
* @author		Croes Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|cmsportlettools');
PortletFactory::includePortlet ('wysiwygcontent');

/**
* @package	cms
* @subpackage cms_portlet_wysiwyg
* Page concernant la manipulation des portlets
*/
class ActionGroupwysiwygcontentPortlet extends CopixActionGroup {
	/**
    * Page de modification de la portlet.
    */
	function getEdit (){
		//Vérification des données à éditer.
		if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('wysiwyg.error.unableToGetContent'),
			'back'=>CopixUrl::get ('cms|admin|edit')));
		}

		if (!isset ($this->vars['kind'])){
			$this->vars['kind'] = 0;
		}

		//appel de la zone dédiée.
		$tpl = new CopixTpl ();
		$tpl->assign ('MAIN', CopixZone::process ('EditWYSIWYGContent', array ('toEdit'=>$toEdit,
		'kind'=>$this->vars['kind'])));
		$tpl->assign('TITLE_PAGE',CopixI18N::get('wysiwyg.titlePage.edit'));
		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}

	/**
    * Validation définitive de la portlet.
    */
	function doValid (){
		$this->_validFromPost ();
		if (! ($toEdit = CMSPortletTools::getSessionPortlet ())){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get('cms|cms.error.portletNotFound'),
			'back'=>CopixUrl::get ('cms|admin|edit')));
		}
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get('cms|admin|validPortlet'));
	}

	/**
    * validation temporaire, reste sur la page d'édition.
    */
	function doValidEdit (){
		$this->_validFromPost ();
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get('cms_portlet_wysiwygcontent||edit', array('kind'=>$this->vars['kind'])));
	}

	/**

    */
	function _validFromPost (){
		$data = CMSPortletTools::getSessionPortlet ();
		//définition des éléments a vérifier
		$toCheck = array ('subject', 'text_content');
		//parcours des éléments à vérifier.
		foreach ($toCheck as $varToCheck){
			if (isset ($this->vars[$varToCheck])){
				$data->$varToCheck = $this->vars[$varToCheck];
			}
		}
		if (array_key_exists ('template', $this->vars)){
			$data->setTemplate ($this->vars['template']);
		}

		//récupération de tous les domaines déclarés dans le site, puis construction de ar (domain=>domain.com/chemin/, script=>script)
		$copixheadingsServices = CopixClassesFactory::getInstanceOf ('copixheadings|CopixHeadingsServices');
		$arDomainInfos = array ();
		foreach ($copixheadingsServices->getDomainList () as $domainName){
			if (($scriptName = substr ($domainName, strrpos ($domainName, '/'))) != '/'){
			    $basePath = substr ($domainName, 0, strrpos ($domainName, '/'));
			}else{
				$scriptName = '/index.php';
				$basePath   = $domainName;
			}
			$arDomainInfos[] = array ('domain'=>$basePath, 'script'=>$scriptName);
		}
		//on ne prend que les domaines.
		preg_match_all ('~<[^>]+?(?:href|src)="([^"]+?)".*?>~', $data->text_content, $results, PREG_SET_ORDER);
		$arToReplace = array ();
		foreach ($results as $matchCount=>$matchedData){
			$this->_matchDomain ($matchedData[1], $arDomainInfos, $arToReplace);
		}
		foreach ($arToReplace as $itemToReplace){
			$data->text_content = str_replace ($itemToReplace['source'], $itemToReplace['destination'], $data->text_content);
		}
		
		//echo htmlentities ($data->text_content);
		CMSPortletTools::setSessionPortlet ($data);
	}

	/**
    * 
    */
	function _matchDomain ($pUrl, $pArDomains, & $arToReplace){
		//Dans un premier temps on remplace les urls complètes, puis on remplace les urls restantes avec 
		//<COPIX_URL_ROOT>

		//il faut récupérer l'ensemble des domaines "seuls" puis regarder par rapport à ./ si les fichiers existent.
		//Si c'est le cas, c'est que ce sont des fichier statiques
		
		//mondomaine.com/script/module/x/x/x/ accepté
		//mondomaine.com/nimporte/ root/
		//mondomaine.com doit être transformé en [root]
		
		//et en plus, si l'url est de type mondomaine.com/script/ on parse et décrypte
		foreach ($pArDomains as $domainPosition=>$domainInfos){
			$domainAndScript = $domainInfos['domain'].$domainInfos['script'];
			$domainOnly      = $domainInfos['domain'];
			//on a trouvé un domaine complet pris en charge (avec le script)
			if (strpos ($pUrl, $domainAndScript) === 0){
				$params = CopixUrl::parse (substr ($pUrl, strlen ($domainAndScript)), true, true);
				$arToReplace[] = array ('source'=>$pUrl, 'destination'=>'<COPIX_URL params=\''.serialize ($params).'\' />');
			}
		}
	}
}
?>
