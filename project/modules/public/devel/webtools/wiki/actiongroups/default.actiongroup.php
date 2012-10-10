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
 * Actions par défaut
 * @package	webtools
 * @subpackage	wiki
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Ajout du CSS wiki par défaut
	 */
	public function beforeAction (){
		CopixHtmlHeader::addCSSLink (_resource ('styles/wiki.css.php'));
	} 
	
	/**
	 * Action par défaut = Affichage
	 */
	public function processDefault (){
		return $this->processShow ();
	}

	/**
	 * Show requested page
	 * @var string title
	 */
	public function processShow () {
		if (!_ioClass ('wiki|wikiauth')->canRead ()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>_i18n ('wiki.error.cannot.show'),
			'back'=>_url ()));
		}

		//Ajout des librairies JS utilisées
		CopixHTMLHeader::addCSSLink (_resource ("js/mootools/css/slimbox.css"));
		_tag ('mootools', array ('plugin'=>'divider;slimbox'));		
		
		//Pas de page donnée ? on propose la création
		if (_request ('title') == "") {
			return _arPpo (new CopixPPO (), 'new.wikipage.tpl');
		}
		
		//Initialisation du PPO et titre de page
		$ppo = new CopixPPO ();
		$title = self::decodeUrlTitle(_request('title'));
		
//		//Données de traductions
//		$ppo->from = _request ('from');
//		$ppo->pagesource = _request ('pagesource');
				
		//lang ?
		$lang = _request('lang',CopixI18N::getLang ());
		
		//en premier lieu la page comme on la demande
		$fromcache = false;
		//--en cache ?

		if(!_request ('refresh', false) && CopixCache::exists (_request ("heading")."_"._request("title").$lang,'wiki')){

			$page = _record ("wikipages");
			$page = CopixCache::read (_request("heading")."_"._request("title").$lang,"wiki");
			$fromcache = true;
		}else{
	        //--ou en base ?
			$page = _ioClass ('wiki|wikiservices')->getPatched (_request ("title"), $lang, _request('heading', ""), true);
		}

		//on va chercher la page juste avec le titre
		if (!$page){
			$nohead = _ioClass ('wiki|wikiservices')->getPatched (_request ("title"),$lang,_request('heading',""),true,true);
			if($nohead){
					return _arRedirect(_url("wiki|admin|listPages",array(
															'title'=>_request('title'),
															'heading'=>_request('heading'),
															'lang'=>$lang
					)));
			}
		}
		
		//à partir de là, je sais que cette page n'existe pas du tout
		if (!$page || $page->deleted_wiki == 1) {
			if (_ioClass ('wiki|wikiauth')->canWrite ()){
				return _arRedirect (_url ("wiki|admin|edit", array ('title'=>_request ('title'),'lang'=>$lang,'heading'=>_request('heading',""))));
			}else{
				return CopixActionGroup::process ('generictools|Messages::getError',
				array ('message'=>_i18n ('wiki.page.not.exists.and.cannot.write'),
				'back'=>_url  ('wiki||')));
			}
		}

		//récupération des informations de contributeurs sur la page
		if(!$fromcache){
			$authors = _ioClass ('wiki|wikiservices')->getContributors (_request ('title'),$lang,_request('heading',""));
		}else{
			$authors = $page->contributors;
		}
		$ppo->original_author = $authors['original'];
		$ppo->contribs = $authors['contributors'];
		$ppo->last_modifier = isset($ppo->contribs[count ($ppo->contribs)-1]) && strlen($ppo->contribs[count($ppo->contribs)-1])>0 ? $ppo->contribs[count($ppo->contribs)-1] : $ppo->original_author; 

		//traductions
		if(!$fromcache){
			$tr = _ioClass ('wiki|wikiservices')->getTranslatedPages ($page);
		}else{
			$tr = $page->translations;
		}
		if (count($tr)){
			$translations = '<div id="wiki_transations">';
			foreach($tr as $translation){
				$translations.='<a href="'._url ("wiki||show", array('title'=>$translation->title_wiki,'lang'=>$translation->lang_wiki,'heading'=>$translation->heading_wiki)).'"><img src="'._resource ('img/tools/flags/'.$translation->lang_wiki.'.png').'" alt="'.$translation->lang_wiki.'"/></a> ';
			}
			$translations.="</div>\n";
		}
		
		$ppo->translations=$translations;
		
		if(!$fromcache || _request("refresh",false)){
			//on recalcul la page

			$page->content_wiki = _ioClass ('wiki|wikiservices')->render ($page);
			$page->contributors = _ioClass ('wiki|wikiservices')->getContributors (_request ('title'),$lang,_request('heading',""));
			$page->translations = _ioClass ('wiki|wikiservices')->getTranslatedPages ($page);
			CopixCache::write ($page->heading_wiki."_".$page->title_wiki.$page->lang_wiki,$page, 'wiki');

			//notifie une visite			
			_notify ('Visited', array ('id'=>$page->heading_wiki."/".$page->title_wiki.'/'.$lang,
			'kind'=>'wiki',
			'keywords'=>$page->keywords_wiki,
			'title'=>$page->title_wiki,
			'summary'=>$page->description_wiki,
			'content'=>$page->content_wiki,
			'url'=>_url ('show', array ('title'=>$page->title_wiki))));
		
		}
		
		//Meta informations sur la page
		CopixHTMLHeader::addOthers ('<meta name="keywords" content="'._Copix_utf8_htmlentities($page->keywords_wiki).'" />');
		CopixHTMLHeader::addOthers ('<meta name="description" content="'._Copix_utf8_htmlentities($page->description_wiki).'" />');
		
		$ppo->arian = $this->_doArianeWire (_request ('title'),_request ('heading'));
		$ppo->page = $page;
		$ppo->langs = explode(";",CopixConfig::get('wiki|langs'));
		$ppo->canedit = _ioClass ('wiki|wikiauth')->canWrite ();
		$ppo->TITLE_PAGE = strlen($page->displayedtitle_wiki)>0 ? $page->displayedtitle_wiki : $title;
		$ppo->TITLE_BAR = strlen($page->heading_wiki) ? $page->heading_wiki."/".$ppo->TITLE_PAGE : $ppo->TITLE_PAGE;
		return _arPpo ($ppo, 'show.wikipage.tpl');
	}
	
	/**
	 * S'occupe de gérer le fil d'ariane
	 *
	 * @param string $pTitle le titre de la page à ajouter / contrôler dans le fil d'ariane
	 * @return array
	 */
	private function _doArianeWire ($pTitle,$pHeading=""){
		if (($arianeWire = CopixSession::get ('wiki|arianewire')) === null){
			$arianeWire = array ();			
		}
		
		//on supprime $pTitle s'il est déja présent.
		if (isset ($arianeWire[self::decodeUrlTitle($pTitle).$pHeading])){
			unset ($arianeWire[self::decodeUrlTitle($pTitle).$pHeading]);
		}

		//Ajout de $pTitle au tableau
		$tmp = new stdClass ();
		$tmp->title = $pTitle;
		$tmp->heading = $pHeading;
		$arianeWire[self::decodeUrlTitle($pTitle).$pHeading] = $tmp;

		//on limite le tableau au nombre d'éléments configuré
		$nblimit = CopixConfig::get ("wiki|arianlimit");
		if (count ($arianeWire > $nblimit)){
			$arianeWire = array_slice ($arianeWire, $nblimit * -1);
		}

		CopixSession::set ('wiki|arianewire', $arianeWire);
		return $arianeWire;
	}
	
	private function decodeUrlTitle($pTitle){
		$pTitle = str_replace("__","@@double.underscores@@",$pTitle);
		$pTitle = str_replace("_"," ",$pTitle);
		return str_replace("@@double.underscores@@","_",$pTitle);		
	}
}
?>