<?php
/**
 * @package		tutorials
 * @subpackage 	news_6
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Groupe d'action appelé par défaut dans le module.
 * @package	tutorials
 * @subpackage news_6
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * On protège la lecture avec le droit module:Voir
	 */
	public function beforeAction ($pActionName){
		CopixAuth::getCurrentUser()->assertCredential ('module:Voir@news_6');
	}
	 
	/**
	 * Page appelée par défaut dans le module
	 */
	public function processDefault (){
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = 'Liste des nouvelles';
		$ppo->arNews = _dao ('news_6')->findBy (_daoSp ()->orderBy (array ('date_news', 'DESC')));
		$ppo->writeEnabled = CopixAuth::getCurrentUser ()->testCredential ('module:Ecrire@news_6');
		return _arPpo ($ppo, 'news.list.tpl');
	}
	
	/**
	 * Affichage complet de la nouvelle
	 */
	public function processShow (){
		//Vérification de la présence de l'identifiant de nouvelle.
		CopixRequest::assert ('id_news');
		
		//Vérification que la nouvelle demandée existe
		if (! ($news = _dao ('news_6')->get (_request ('id_news')))){
			return _arRedirect (_url ('|'));			
		}

		//Réponse
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = $news->title_news;
		$ppo->news = $news;
		$ppo->writeEnabled = CopixAuth::getCurrentUser ()->testCredential ('module:Ecrire@news_6');
		return _arPpo ($ppo, 'news.show.tpl');
	}
}
?>