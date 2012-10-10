<?php
/**
* @package  cms
* @author   Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');


/**
 * ActionGroupCMSPage
 * @package cms
 */
class ActionGroupCMSPage extends CopixActionGroup {
    /**
    * gets a single page, from its last revision.
    * @param int $this-getRequest ('id')    pageId
    */
    function getPage () {
    	//Given an id ?
    	$id = CopixRequest::get ('id', null, true);
    	if ($id === null){
    		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ());
    	}
    	//does the page exists ?
        $page = ServicesCMSPage::getOnline ($id);
        if ($page === null){
            return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('||'));
        }

        CopixEventNotifier::notify (new CopixEvent ('HeadingThemeRequest', array ('id'=>$page->id_head)));
        //everything's ok, processing
        if (strlen ($page->summary_cmsp) > 0){
        	CopixHTMLHeader::addOthers ('<meta name="description" content="'.htmlentities ($page->summary_cmsp).'">');
        }
        if (strlen ($page->keywords_cmsp) > 0){
        	CopixHTMLHeader::addOthers ('<meta name="keywords" content="'.htmlentities ($page->keywords_cmsp).'">');
        }
        $error = array ();//Création du tableau pour pouvoir recevoir les messages d'erreur
        $content = ServicesCMSPage::getPageContent ($page, $error);
        if ($error) {
        	return $error;
        }

        //template stuff
        $tpl = new CopixTpl ();
        $tpl->assign ('TITLE_BAR', strlen ($page->titlebar_cmsp) ? $page->titlebar_cmsp : $page->title_cmsp);
        $tpl->assign ('TITLE_PAGE', $page->title_cmsp);
        $tpl->assign ('MAIN', $content);
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
    }
}
?>
