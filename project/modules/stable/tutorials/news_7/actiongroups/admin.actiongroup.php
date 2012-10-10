<?php
/**
 * @package		tutorials
 * @subpackage 	news_7
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actions d'administration pour les nouvelles
 * @package	tutorials
 * @subpackage	news_7
 */ 
class ActionGroupAdmin extends CopixActionGroup {
	/*
	 * On vérifie les droits d'administration
	 */
	public function beforeAction ($pActionName){
		_currentUser ()->assertCredential ('module:Ecrire@news_7');
	}

	/**
	 * Supression d'un enregistrement
	 */
	public function processDelete (){
		//On s'assure qu'un id_news à bien été donné    
		CopixRequest::assert ('id_news');

		//On vérifie si l'enregistrement existe, si tel n'est pas le cas, 
		//on ne s'embête pas et on retourne en liste.
		if (! ($record = _ioDAO ('news_7')->get (CopixRequest::getInt ('id_news')))){
			return _arRedirect (_url ('|'));//'|' signifie action par défaut dans l'actiongroup par défaut du module courant, on aurait pu ici écrire 'news|default|default' ou 'news||'
		}

		if (! _request ('confirm', false, true)){
			return CopixActionGroup::process ('generictools|Messages::getConfirm',
			array ('message'=>'Etes vous sûr de vouloir supprimer cet élément '.$record->title_news.' ?',
			'confirm'=>_url ('admin|delete', array ('id_news'=>$record->id_news, 'confirm'=>1)),
			'cancel'=>_url ('|')));
		}else{
			_ioDAO ('news_7')->delete ($record->id_news);
			_notify ('DeletedContent', array ('id'=>$record->id_news,
				'kind'=>'news'));
			
		}
		return _arRedirect (_url ('|'));
	}

	/**
	 * Formulaire de modification / création
	 */
	public function processEdit (){
		//Si un identifiant d'élément à modifier est donné et que ce dernier existe, on le place
		//dans la session pour qu'il soit défini comme élément à modifier
		if ($news_id = CopixRequest::getInt ('id_news')){
			if ($toEdit = _ioDAO ('news_7')->get ($news_id)){
				CopixSession::set ('news|edit', $toEdit);
			}
		}

		//On regarde s'il existe un élément en cours de modification, si ce n'est pas le cas on 
		//passe en mode création. On passe en création également si demandé explicitement
		if ((($toEdit = CopixSession::get ('news|edit')) === null) || (_request ('new') == 1)){
			CopixSession::set ('news|edit', $toEdit = _record ('news_7'));
		}

		//Préparation de la page
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = $toEdit->id_news ? "Modification de l'élément" : "Création d'un élément";
		$ppo->toEdit = $toEdit;
		//si on demande à afficher les messages d'erreurs
		$ppo->arErrors = _request ('errors') ? _ioDAO ('news_7')->check ($toEdit) : array ();
		return _arPpo ($ppo, 'news.form.tpl');
	}

	/**
	 * Validation en base de données du formulaire
	 */
	public function processValid (){
		//On vérifie que l'on est bien en train de modifier un élément, sinon on retourne à la liste.
		if (($toEdit = CopixSession::get ('news|edit')) === null){
			return _arRedirect (_url ('|'));
		}

		//validation des modification depuis le formulaire
		$toEdit->title_news = _request ('title_news');
		$toEdit->summary_news = _request ('summary_news');
		$toEdit->content_news = _request ('content_news');
		$toEdit->date_news = CopixDateTime::DateToyyyymmdd (_request ('date_news'));
		
		CopixSession::set ('news|edit', $toEdit);

		//Si tout va bien, on sauvegarde
		if (_ioDAO ('news_7')->check ($toEdit) === true){
			if ($toEdit->id_news){
				_ioDAO ('news_7')->update ($toEdit);
			}else{
				_ioDAO ('news_7')->insert ($toEdit);
			}
			
			_notify ('Content', array ('id'=>$toEdit->id_news,
			'kind'=>'news',
			'keywords'=>'',
			'title'=>$toEdit->title_news,
			'summary'=>$toEdit->summary_news,
			'content'=>$toEdit->content_news,
			'url'=>_url ('show', array ('id_news'=>$toEdit->id_news))));
			
			//on vide la session
			CopixSession::set ('news|edit', null);
			return _arRedirect (_url ('show', array ('id_news'=>$toEdit->id_news)));
		}else{
			//un problème ? on retourne sur la page de modification en indiquant qu'il existe un problème
			return _arRedirect (_url ('admin|edit', array ('errors'=>1)));
		}
	}
}
?>