<?php
/**
 * @package	webtools
 * @subpackage	wiki
* @author	Patrice Ferlet
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Zone de modification des pages de wiki
 * @package	webtools
 * @subpackage	wiki
 */
class ZoneEdit extends CopixZone {
	function _createContent(& $toReturn) {
		CopixHTMLHeader::addCSSLink(_resource ("js/mootools/css/slimbox.css"));
		_tag ('mootools', array ('plugin'=>'slimbox;posteditor'));
		CopixHTMLHeader::addJSLink (_resource ("js/mootools/plugins/language.COPIXWIKI.js"));

		//try to find wiki page with title name given
		$page = $this->getParam ('page', false);
		if (!$page){
			$page = _ioClass ('wikiservices')->getPatched ($this->getParam ("title_wiki"),$this->getParam('lang'),$this->getParam('heading'),false);			
		}
		
		$langhead="";
		//page dosen't exists
		if (!$page || $page->deleted_wiki==1) {
			$page = _record ('wikipages');
			$page->title_wiki = $this->getParam('title_wiki');
			$page->heading_wiki = $this->getParam('heading_wiki');
		}
		
		//preview ?
		$preview=false;
		if($this->getParam ('preview',false)){
			$preview = _ioClass ('wikiservices')->render($page);
		}

 		$tpl = new CopixTpl();
 		$lang = ($page->lang_wiki) ? $page->lang_wiki : $this->getParam('lang');
 		$from = ($page->translatefrom_wiki) ? $page->translatefrom_wiki : $this->getParam('pagesource');  
 		$fromlang = ($page->fromlang_wiki) ? $page->fromlang_wiki : $this->getParam('fromlang');
 		$heading = ($page->heading_wiki) ? $page->heading_wiki : $this->getParam('heading');
 		$displayedtitle= ($page->displayedtitle_wiki) ? $page->displayedtitle_wiki : $this->getParam('displayedtitle_wiki');
 		$tpl->assign('pagesource',$from);
 		$tpl->assign('fromlang',$fromlang);
 		$tpl->assign('heading',$heading);
 		$tpl->assign('langs',explode(';',CopixConfig::get('wiki|langs')));
 		$tpl->assign('lang',$lang);
 		$tpl->assign('displayedtitle',$displayedtitle);
 		
 		CopixLog::log("création de page dans ".$heading);
 		
 		$parentpage=$page->title_wiki;
 		if(strlen($from)){
 			$parentpage=$from;
 		}
 		$tpl->assign('parent',$parentpage);
		//if logged, we can set User
		$user = CopixAuth::getCurrentUser ()->getLogin();
		if(!is_null($user)){
			$tpl->assign("user",$user);
		}
		
		
		//javascripts
		$js= new CopixTpl();
		$js->assign('page', $page);
		CopixHTMLHeader::addOthers($js->fetch('wikiscripts.js.tpl'));
	
		$tpl->assign('page', $page);
		$tpl->assign('preview', $preview);

		$toReturn = $tpl->fetch('edit.wikipage.tpl');
		return true;
	}
}
?>