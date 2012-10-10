<?php
/**
 * @package		webtools
 * @subpackage 	news
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Groupe d'action appelé par défaut dans le module.
 * @package	webtools
 * @subpackage	news
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * On protège la lecture avec le droit module:Voir
	 */
	public function beforeAction (){
		CopixAuth::getCurrentUser()->assertCredential ('module:Voir@news');
	}
	 
	/**
	 * Page appelée par défaut dans le module
	 */
	public function processDefault (){
		CopixHtmlHeader::addCSSLink (_resource ('styles/news.css'));
		CopixHTMLHeader::addOthers('<link rel="alternate" type="application/rss+xml"
    title="RSS" href="'._url('news|default|rss').'" />');
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = 'Liste des nouvelles';
		$reqToSelectNews = '`news`.`date_news` < CURDATE() OR (`news`.`date_news`= CURDATE() AND `news`.`heure_news` <= CURTIME() )';
		$criteres =_daoSp ()	->addSQL($reqToSelectNews)
								->orderBy (array ('date_news', 'DESC')); 
		$ppo->arNews = _dao ('news')->findBy ($criteres);
		$ppo->writeEnabled = CopixAuth::getCurrentUser ()->testCredential ('module:Ecrire@news');
		if ($ppo->writeEnabled) {
			$criteresNewsNonParues = 
					_daoSp ()	->addSQL('!('.$reqToSelectNews.')')
								->orderBy (array ('date_news', 'DESC'));
			$ppo->arNewsNonParues = _dao ('news')->findBy ($criteresNewsNonParues);
			if (count($ppo->arNewsNonParues) > 0) {
				$ppo->hidden_news = true;
			} else {
				$ppo->hidden_news = false;
			}
		}
		return _arPpo ($ppo, 'news.list.tpl');
	}
	
	/**
	 * Affichage de la liste des news sous forme de flux rss
	 */
	public function processRss () {
		$rss = _class ('syndication|syndication');
		$rss->title = 'Liste des news du site ';
    	$rss->link->uri = _url('|');
    	$rss->description = 'La liste des news du site';
		$reqToSelectNews = '`news`.`date_news` < CURDATE() OR (`news`.`date_news`= CURDATE() AND `news`.`heure_news` <= CURTIME() )';
		$criteres =_daoSp ()	->addSQL($reqToSelectNews)
								->orderBy (array ('date_news', 'DESC'))
								->setCount(10); 
		$arNews = _dao ('news')->findBy ($criteres);
    	foreach ($arNews AS $lineNews) {
			$item = $rss->addItem ();
			$item->title = $lineNews->title_news;
			$item->link->uri = _url('show',array('id_news'=>$lineNews->id_news,'title_news'=>$lineNews->title_news));
			$item->content->value = $lineNews->summary_news;
			$item->pubDate = $lineNews->date_news;
    	}
    	$ppo = new CopixPPO ();
    	$ppo->content = $rss->getContent (Syndication::RSS_2_0);
		return _arDirectPPO ($ppo, 'news.rss.tpl');
	}
	/**
	 * Affichage complet de la nouvelle
	 */
	public function processShow (){
		//Vérification de la présence de l'identifiant de nouvelle.
		CopixRequest::assert ('id_news');
		
		//Vérification que la nouvelle demandée existe
		if (! ($news = _dao ('news')->get (_request ('id_news')))){
			return _arRedirect (_url ('|'));			
		}

		//Réponse
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = $news->title_news;
		$ppo->news = _class ('news|newsrender')->renderNewsContent($news);
		$ppo->writeEnabled = CopixAuth::getCurrentUser ()->testCredential ('module:Ecrire@news');
		return _arPpo ($ppo, 'news.show.tpl');
	}
}
?>