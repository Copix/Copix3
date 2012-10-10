<?php
/**
 * @package		tutorials
 * @subpackage 	news_3
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Groupe d'action appelé par défaut dans le module.
 * @package	tutorials
 * @subpackage	news_3
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Page appelée par défaut dans le module
	 */
	public function processDefault (){
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = 'Liste des nouvelles';
		$ppo->arNews = _dao ('news_3')->findBy (_daoSp ()->orderBy (array ('date_news', 'DESC')));
		return _arPpo ($ppo, 'news.list.tpl');
	}
	
	/**
	 * Affichage complet de la nouvelle
	 */
	public function processShow (){
		//Vérification de la présence de l'identifiant de nouvelle.
		CopixRequest::assert ('id_news');
		
		//Vérification que la nouvelle demandée existe
		if (! ($news = _dao ('news_3')->get (CopixRequest::getInt ('id_news')))){
			return _arRedirect (_url ('|'));			
		}

		//Réponse
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = $news->title_news;
		$ppo->news = $news;
		return _arPpo ($ppo, 'news.show.tpl');
	}
}
?>