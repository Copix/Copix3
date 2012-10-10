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
CopixClassesFactory::fileInclude ('cms|Portlet');

/**
 * Portlet d'affichage d'un contenu WYSIWYG
 * @package cms
 * @subpackage cms_portlet_wysiwyg
 */
class PortletWYSIWYGContent extends Portlet {
    /**
    * subject of the article.
    * @var string
    */
    var $subject = null;

    /**
    * article content (html format)
    * @var string
    */
    var $text_content = null;

    /**
    * gets the parsed content.
    */
    function getParsed ($context) {
        $tpl = & new CopixTpl ();
        $tpl->assign ('portlet', $this->getCopy ());
        return $tpl->fetch ($this->templateId ? $this->getTemplateSelector() : 'cms_portlet_wysiwygcontent|normal.wysiwyg.tpl');
    }
    
    function getCopy (){
    	$portletCopy = new stdClass();
    	$portletCopy->subject = $this->subject;
    	$portletCopy->text_content = $this->text_content;
    	$portletCopy->templateId = $this->templateId;
    	$toReplace = array ();
		//on recherche les éléments remplacés pour remettre les bonnes url
    	preg_match_all ('~<COPIX_URL params=\'(.+?)\' />~', $portletCopy->text_content, $results, PREG_SET_ORDER);
		//print_r ($results);
    	foreach ($results as $matchedData){
			if (($params = unserialize ($matchedData[1])) !== false){
				$dest = $params['module'].'|'.$params['desc'].'|'.$params['action'];
				unset ($params['module']);
				unset ($params['desc']);
				unset ($params['action']);
				$replaceBy = CopixUrl::get ($dest, $params);
				$toReplace[] = array ('source'=>'<COPIX_URL params=\''.$matchedData[1].'\' />', 'destination'=>$replaceBy);
			}
		}
		//on parcours les éléments à rétablir
		foreach ($toReplace as $replacePattern){
			$portletCopy->text_content = str_replace ($replacePattern['source'], $replacePattern['destination'], $portletCopy->text_content);
		}
		//et maintenant on met en place les roots simples
		$portletCopy->text_content = str_replace ('<COPIX_URL_ROOT>', CopixUrl::getRequestedBasePath (), $portletCopy->text_content);
		return $portletCopy;
    }

    function getGroup (){
        return 'general';
    }
    function getI18NKey (){
        return 'cms_portlet_wysiwygcontent|wysiwyg.portletdescription';
    }
    function getGroupI18NKey (){
        return 'cms_portlet_wysiwygcontent|wysiwyg.group';
    }
}
/**
 * @package cms
 * @subpackage cms_portlet_wysiwyg
 * Pour des raisons de compatibilité, l'ancienne convention étant de nommer les portlet XXXPortlet et non PortletXXX
 */
class WYSIWYGContentPortlet extends PortletWYSIWYGContent {}
?>
