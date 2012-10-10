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
 * Services to treat wiki pages
 * @package	webtools
 * @subpackage	wiki
 */
class wikiservices {

	static $__instance;
	static $__id=0;
	
	/**
	 * Récupère les tags considérés comme "spéciaux"
	 * @return array of tags
	 */
	public function getTags () {
		$tags = CopixConfig::get ('wiki|writespecialtags');
		$tags = explode(",", $tags);
		return $tags;
	}

	/**
	 * Récupération des 
	 *
	 * @param record $page
	 * @return array 
	 */
	public function getTranslatedPages ($page){
		$sources = _ioDao ('wikipages')->findBy(_daoSp ()
									   ->addCondition ('title_wiki', "=", $page->translatefrom_wiki)
									   ->addCondition ('lang_wiki', "=", $page->fromlang_wiki)
						 	    	   ->orderBy(array("modificationdate_wiki","DESC")));
						 	    	   /*('>groupBy('title_wiki', 'displayedtitle_wiki'));*/
		//var_dump($sources);
		if(count ($sources) < 1){
			$source = $page;
		}else{
			$source = $sources[0];
			while (!is_null ($source->translatefrom_wiki) && strlen ($source->translatefrom_wiki) > 0){
				//on a une page mère
				$sources = _ioDao('wikipages')->findBy(_daoSp ()->addCondition ('title_wiki', "=", $source->translatefrom_wiki)
							 	    		  ->orderBy(array("modificationdate_wiki","DESC")));
	    		if(count($sources)<1){
	    			$source = $sources[0];
	    		}else{
	    			break;		
	    		}
			}
			$source = $sources[0];
		}
		$children = array("$source->title_wiki"=>$source);		
		$this->getTranslationsFrom($source,$children);
		return $children;
	}
	
	public function getTranslationsFrom ($page, &$children){
		$tr = _ioDao('wikipages')->findBy (_daoSp ()->addCondition ('translatefrom_wiki', "=", $page->title_wiki)
				 	    		 ->orderBy (array ("modificationdate_wiki", "DESC")));
   		
		foreach ($tr as $child){
			if (!isset ($children[$child->title_wiki.$child->lang_wiki])){
				$children[$child->title_wiki.$child->lang_wiki] = $child;
				$this->getTranslationsFrom ($child, $children);
			}
		}
	}
	
	/**
	 * Get Last version of a page
	 * 
	 * @param string title of page to get
	 * @return string page content in wiki format
	 */
	public function getPatched ($title,$lang,$heading="",$checkOtherLang=true,$checkOtherHeadings=false) {
		$heagin=trim($heading);
		$pages = _ioDao ('wikipages')->findBy (_daoSp ()->
												addCondition ('title_wiki', "=", $title)->
												addCondition('lang_wiki','=',$lang)->
												addCondition('heading_wiki','=',$heading)->
									 	    	orderBy(array("modificationdate_wiki","DESC"))
									 	       );
		//si pas de page, peut-être que la langue n'est pas dispo, on test
		if($checkOtherLang && count($pages)<1){
			$pages = _ioDao ('wikipages')->findBy (_daoSp ()->
												addCondition ('translatefrom_wiki', "=", $title)->
												addCondition('heading_wiki','=',$heading)->
									 	    	orderBy(array("modificationdate_wiki","DESC"))
									 	       );			
		}
		
		//peut-être que la page est dans une autre rubrique, on va tester, et sans la langue
		if($checkOtherHeadings && count($pages)<1){
			$pages = _ioDao ('wikipages')->findBy (_daoSp ()->
												addCondition ('title_wiki', "=", $title)->
									 	    	orderBy(array("modificationdate_wiki","DESC"))
									 	       );
			//on nettoie
			$arpages = array();
			foreach($pages as $page){
				if(!isset($arpages[$page->title_wiki.$page->heading_wiki])){
					$arpages[$page->title_wiki.$page->heading_wiki]=$page;
				}
			}
			$pages = $arpages;
			return $pages;
		}

		return isset ($pages[0]) ? $pages[0] : null;
	}

	/**
	 * get original author and contributors
	 * @param string titlepage
	 * @return array ['orignal'] string for first author, ['contributors'] array of string for contributors names
	 */
	public function getContributors ($title,$lang,$heading="") {
		$already = array ();
		$pages = _ioDao ('wikipages')->findBy (_daoSp ()->
												addCondition ('title_wiki', "=", $title)->
												addCondition('lang_wiki','=',$lang)->
												addCondition('heading_wiki','=',$heading)->
  					   	  	 		 			orderBy("modificationdate_wiki"))->fetchAll ();					 

		$original = $pages[0]->author_wiki;

		unset ($pages[0]);
		$contributors = array ();
		foreach ($pages as $page) {
			if ($page->author_wiki != $original and !in_array($page->author_wiki, $already)) {
				$contributors[] = $page->author_wiki;
				$already[] = $page->author_wiki;
			}
		}
		
		$toReturn = array ();
		$toReturn['original'] = $original;
		$toReturn['contributors'] = $contributors;
		return $toReturn;
	}

	/**
	 * Return the translations array if exists
	 *
	 * @return array transations or boolean false
	 */
	public function getTranslations () {
		return (count ($this->translations)>0) ? $this->translations : false;
	}
	
	
	/**
	 * Translate wiki language into HTML
	 * @param string content
	 * @return string HTML content
	 */
	public function render ($page) {
		//TODO: appeler la libraire Wiki
		$wiki = _ioClass('wikirender|wiki');
		return $wiki->render($page->content_wiki,_ioClass ('wiki|wikiauth')->canWriteSpecialsTags(),self::getTags());
	}
	
	
	public function checkLang($pagecontent,$lang){
		$elems = $this->_prepareElements($pagecontent);
		foreach($elems as $elem){ 
			if($elem->type=="lang" && $elem->other==$lang){
				return $elem->data;
			}
		}
		return false;
	}
	
	public function _prepareElements($content){
		$wiki = _ioClass("wikirender|wiki");
		return $wiki->_prepareElements($content);
		
	}
}
?>