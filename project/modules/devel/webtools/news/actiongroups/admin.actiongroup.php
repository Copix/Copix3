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
 * Actions d'administration pour les nouvelles
 * @package	webtools
 * @subpackage	news
 */ 
class ActionGroupAdmin extends CopixActionGroup {
	/*
	 * On vérifie les droits d'administration
	 */
	public function beforeAction (){
		_currentUser ()->assertCredential ('module:Ecrire@news');
	}

	/**
	 * Supression d'un enregistrement
	 */
	public function processDelete (){
		//On s'assure qu'un id_news à bien été donné    
		CopixRequest::assert ('id_news');

		//On vérifie si l'enregistrement existe, si tel n'est pas le cas, 
		//on ne s'embête pas et on retourne en liste.
		if (! ($record = _ioDAO ('news')->get (CopixRequest::getInt ('id_news')))){
			return _arRedirect (_url ('|'));//'|' signifie action par défaut dans l'actiongroup par défaut du module courant, on aurait pu ici écrire 'news|default|default' ou 'news||'
		}

		if (! _request ('confirm', false, true)){
			return CopixActionGroup::process ('generictools|Messages::getConfirm',
			array ('message'=>'Etes vous sûr de vouloir supprimer cet élément '.$record->title_news.' ?',
			'confirm'=>_url ('admin|delete', array ('id_news'=>$record->id_news, 'confirm'=>1)),
			'cancel'=>_url ('|')));
		}else{
			_ioDAO ('news')->delete ($record->id_news);
			_notify ('DeletedContent', array ('id'=>$record->id_news,
				'kind'=>'news'));
			
		}
		return _arRedirect (_url ('|'));
	}

	/**
	 * Formulaire de modification / création
	 */
	public function processEdit (){
		// ajout de quelques styles pour l'édition des news
		CopixHTMLHeader::addStyle(
			'.news_form input.news_input',
			'width:80%;'
		);
		//Si un identifiant d'élément à modifier est donné et que ce dernier existe, on le place
		//dans la session pour qu'il soit défini comme élément à modifier
		if ($news_id = CopixRequest::getInt ('id_news')){
			if ($toEdit = _ioDAO ('news')->get ($news_id)){
				CopixSession::set ('news|edit', $toEdit);
			}
		}

		//On regarde s'il existe un élément en cours de modification, si ce n'est pas le cas on 
		//passe en mode création. On passe en création également si demandé explicitement
		if ((($toEdit = CopixSession::get ('news|edit')) === null) || (_request ('new') == 1)){
			CopixSession::set ('news|edit', $toEdit = _record ('news'));
			$toEdit->date_news=CopixDateTime::yyyymmddToDate(date('Ymd'));
		}
		
		//Préparation de la page
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = $toEdit->id_news ? "Modification de l'élément" : "Création d'un élément";
		$ppo->toEdit = $toEdit;
		if ($toEdit->id_news) {
			$ppo->typeRedac = $toEdit->type_news;
			// récupération de la liste des tags associés à la news
			$arrTags = _class ('tags|tagservices')->getAssociation($toEdit->id_news,'news');
			$ppo->tagList = implode(',',$arrTags);
		} else {
			//gestion des type de rédaction
			$ppo->typeRedacAvailable = array();
			//par défaut, seule la rédaction de type "text" est possible
			$ppo->typeRedacAvailable[] = 'text';
			$ppo->typeRedac = 'text';
			
			//Si les droits sont disponibles, rédaction de type wiki ou html (par défaut, rédaction en html)
			if (CopixAuth::getCurrentUser ()->testCredential ('module:EcrireWiki@news')) {
				$ppo->typeRedacAvailable[] = 'wiki';
				$ppo->typeRedac = 'wiki';
			}
			if (CopixAuth::getCurrentUser ()->testCredential ('module:EcrireWysiwyg@news')) {
				$ppo->typeRedacAvailable[] = 'wysiwyg';
				$ppo->typeRedac = 'wysiwyg';
			}
			if (sizeof($ppo->typeRedacAvailable) > 1) {
				$ppo->multipleRedac = true;
			}
			if ( in_array(_request('typeRedac'),$ppo->typeRedacAvailable) !== false ) {
				$ppo->typeRedac = _request('typeRedac');
			}
			
		}
		
		//si on demande à afficher les messages d'erreurs
		$ppo->arErrors = _request ('errors') ? _ioDAO ('news')->check ($toEdit) : array ();
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
		$arrHeure = _request ('heure_news_ar');
		$toEdit->heure_news = CopixDateTime::timeToHHMMSS ($arrHeure['news_Hour'].':'.$arrHeure['news_Minute'].':'.$arrHeure['news_Second']);
		switch (_request ('typeRedac')) {
			case 'wiki' :
				$toEdit->type_news = 'wiki';
				break;
			case 'wysiwyg' :
				$toEdit->type_news = 'wysiwyg';
				break;
			default :
				$toEdit->type_news = 'text';
				break;
		}
		
		CopixSession::set ('news|edit', $toEdit);

		//Si tout va bien, on sauvegarde
		if (_ioDAO ('news')->check ($toEdit) === true){
			if ($toEdit->id_news){
				_ioDAO ('news')->update ($toEdit);
			}else{
				_ioDAO ('news')->insert ($toEdit);
			}
			
			_notify ('Content', array ('id'=>$toEdit->id_news,
			'kind'=>'news',
			'keywords'=>'',
			'title'=>$toEdit->title_news,
			'summary'=>$toEdit->summary_news,
			'content'=>$toEdit->content_news,
			'type_news'=>$toEdit->type_news,
			'url'=>_url ('show', array ('id_news'=>$toEdit->id_news))));
			
			//On supprime tous les tags associés
			_class ('tags|tagservices')->deleteAssociation($toEdit->id_news,'news');
			//On ajoute ensuite les nouveaux tags
			_class ('tags|tagservices')->addAssociation($toEdit->id_news,'news',explode(',',_request ('tag_list_news')));
			
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