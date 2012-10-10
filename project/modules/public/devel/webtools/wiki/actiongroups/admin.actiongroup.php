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
 * Actions d'administration
 * @package	webtools
 * @subpackage	wiki
 */
class ActionGroupAdmin extends CopixActionGroup {
	/**
	 * Ajout du CSS wiki par défaut
	 */
	public function beforeAction (){
		CopixHtmlHeader::addCSSLink (_resource ('styles/wiki.css.php'));
	}

	/**
	 * Get a page list from title
	 * @var string title
	 */
	public function processListPages(){
		$pages = _ioClass ('wiki|wikiservices')->getPatched (_request ("title"),
		_request('lang'),
		_request('heading',""),
		true,
		true);

		$ppo = new CopixPPO();
		$ppo->pages = $pages;
		$ppo->title = _request('title');
		$ppo->heading = _request('heading');
		$ppo->canwrite = _ioClass('wikiauth')->canWrite();
		return _arPpo($ppo,"listpages.tpl");
	}

	/**
	 * Get creation/modification page for requested wikipage
	 * @var string title
	 */
	public function processEdit() {
		if (!_ioClass ('wiki|wikiauth')->canWrite()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>_i18n ('wiki.error.cannot.edit'),
			'back'=>_url ()));
		}

		if (!_request ('title', false)) {
			//we cannot add page without title so let's go to creation form
			return _arRedirect (_url ('wiki||'));
		}

		$tpl = new CopixTpl();

		$title = self::decodeUrlTitle(_request('title'));
		$tpl->assign('TITLE_PAGE', $title);

		$tpl->assign('MAIN', CopixZone::process ('Edit', array (
		'title_wiki' => CopixUrl::escapeSpecialChars(_request ('title')),
		'pagesource'=>_request ('pagesource',false,false),
		'fromlang'=>_request ('fromlang',false,false),
		'lang' => _request('lang',CopixI18N::getLang ()),
		'heading' => _request('heading',""),
		'langs' => explode(";",CopixConfig::get("wiki|langs"))
		)));
		return _arDisplay ($tpl);
	}

	/**
	 * Save the page for creation or modification
	 * @var string title_wiki
	 */
	public function processSave() {
		if(!_ioClass ('wiki|wikiauth')->canWrite()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>_i18n ('wiki.error.cannot.edit'),
			'back'=>_url ()));
		}

		if (!_request ('title_wiki', false)) {
			//we cannot add page without title so let's go to creation form
			return _arRedirect (_url ('wiki||'));
		}

		if (_request('heading',false)){
			//check if heading is known
			$result = _ioDao('wikiheadings')->findBy(_daoSp()
			->addCondition('heading_wikihead',"=",_request('heading'))
			);
			if(count($result)<1){
				$head = _record('wikiheadings');
				$head->heading_wikihead = _request('heading');
				_ioDao('wikiheadings')->insert($head);
			}
		}

		$title = _request('title_wiki');
		//try to get wiki page
		$dao = _ioDao ('wikipages');
		$renderer = _ioClass ('wikiservices');
		$page = $renderer->getPatched ($title,_request('lang',""),_request('heading',""));

		$author = _request ('author', false, false);
		$author.="@".$_SERVER['REMOTE_ADDR'];

		$user = CopixAuth::getCurrentUser()->getLogin();

		if (!$page) {
			$page = _record('wikipages');
			$page->deleted_wiki = 0;
			$page->creationdate_wiki = date("YmdHis");
			if(!is_null ($user) && strlen ($user)>0){
				$page->author_wiki = $user;
			}else{
				$page->author_wiki = $author;
			}
		}else{
			if (strlen (trim (_request ("content_wiki"))) == 0){
				//Page have to be deleted
				$dao = _ioDao ('wikipages');
				$sp  = _daoSp ();

				$sp->addCondition ('title_wiki', "=", $title);
				$sp->orderBy (array ("modificationdate_wiki", 'DESC'));
				$pages = $dao->findBy($sp);
				$page = $pages[0]; //get the last page
				$page->deleted_wiki=1;
				$dao->update($page);
				_notify ('DeletedContent', array ('id'=>$page->title_wiki,
				'kind'=>'wiki'));

			}else{
				$page->deleted_wiki = 0;
				//set this version author
				if(!is_null($user) && strlen($user)>0){
					$page->author_wiki=$user;
				}
				else{
					$page->author_wiki=$author;
				}
			}
		}

		//then we can write if not deleted
		if($page->deleted_wiki!=1){
			$page->content_wiki = _request ("content_wiki");
			$page->modificationdate_wiki = date("YmdHis");
			$page->lock_wiki="0";
			$this->_validFromPost($page);
			$page->lang_wiki=_request('lang');
			$page->translatefrom_wiki=_request('frompage',"");
			$page->fromlang_wiki=_request('fromlang',"");
			$page->heading_wiki=_request('heading',"");
			$page->displayedtitle_wiki = _request('displayedtitle_wiki');
			//TODO verifier si changement réel
			$dao->insert($page);
			//empty caches
			$translations = _ioClass ('wiki|wikiservices')->getTranslatedPages ($page);
			foreach($translations as $tr){
				$name = $tr->heading_wiki."_".$tr->title_wiki.$tr->lang_wiki;
				if(CopixCache::exists($name,"wiki")){
					CopixCache::clear($name,"wiki");
				}
			}
			$page->contributors = _ioClass ('wiki|wikiservices')->getContributors (_request ('title_wiki'),_request('lang'),_request('heading',""));
			$page->translations = _ioClass ('wiki|wikiservices')->getTranslatedPages ($page);
			//write cache
			$page->content_wiki=$renderer->render($page);
						
			CopixCache::write ($page->heading_wiki."_".$page->title_wiki.$page->lang_wiki, $page, 'wiki');
			_notify ('Content', array ('id'=>$page->title_wiki,
			'kind'=>'wiki',
			'keywords'=>$page->keywords_wiki,
			'title'=>$page->title_wiki,
			'summary'=>$page->description_wiki,
			'content'=>$page->content_wiki,
			'url'=>_url ('show', array ('title'=>$page->title_wiki,'heading'=>$page->heading_wiki, 'lang'=>$page->lang_wiki))));
		}
		return _arRedirect (_url('show', array ('title' => $page->title_wiki, 'heading' => $page->heading_wiki, 'lang'=>$page->lang_wiki)));

	}

	public function processPreview(){
		if (!_ioClass ('wiki|wikiauth')->canWrite()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>_i18n ('wiki.error.cannot.edit'),
			'back'=>_url ()));
		}
		_tag ('mootools', array ('plugin'=>'divider;slimbox'));
		$page = new stdClass();
		$page->content_wiki = _request('content');
				
		$ppo = new CopixPPO ();
		$ppo->MAIN = _ioClass ('wikiservices')->render($page);;
		return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	}

	function removeCache(){
		if (_request ('title', false, false)){
			$config = CopixConfig::instance();
			CopixCache::clear  (_request ('title'), 'wiki');
		}
		return _arRedirect (_url('show', array ('title'=>_request ('title'),'heading'=>_request ('heading'))));
	}

	/**
	 * Fill the given page by reference
	 * @param DAOWikipages page
	 */
	function _validFromPost ($page) {
		$params = array (
		'title_wiki',
		'keywords_wiki',
		'description_wiki'
			);
		foreach ($params as $param) {
			$page->$param = _request ($param);
		}
	}

	/**
	 * S'occupe de gérer le fil d'ariane
	 *
	 * @param string $pTitle le titre de la page à ajouter / contrôler dans le fil d'ariane
	 * @return array
	 */
	private function _doArianeWire ($pTitle, $pHeading=""){
		if (($arianeWire = CopixSession::get ('wiki|arianewire')) === null){
			$arianeWire = array ();
		}

		//on supprime $pTitle s'il est déja présent.
		if (($pos = array_search ($pTitle, $arianeWire)) !== false){
			unset ($arianeWire[$pos]);
		}
		//Ajout de $pTitle au tableau
		$tmp=new stdClass();
		$tmp->title = $pTitle;
		$tmp->heading = $pHeading;
		$arianeWire[self::decodeUrlTitle($pTitle)] = $tmp;

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