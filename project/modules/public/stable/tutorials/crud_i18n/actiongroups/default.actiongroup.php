<?php
/**
 * @package		tutorials
 * @subpackage	crud_i18n
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actions sur la table crud
 * @package		tutorials
 * @subpackage	crud_i18n
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Affichage de la liste des éléments
	 */
	public function processDefault (){
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = _i18n ('crud.title.list');
		$ppo->arData = _ioDAO ('tutorial_crud')->findAll ();
		return _arPpo ($ppo, 'crud.list.tpl');
	}

	/**
	 * Supression d'un enregistrement
	 */
	public function processDelete (){
		//On s'assure qu'un id_crud à bien été donné    
		CopixRequest::assert ('id_crud');

		//On vérifie si l'enregistrement existe, si tel n'est pas le cas, 
		//on ne s'embête pas et on retourne en liste.
		if (! ($record = _ioDAO ('tutorial_crud')->get (CopixRequest::getInt ('id_crud')))){
			return _arRedirect (_url ('|'));//'|' signifie action par défaut dans l'actiongroup par défaut du module courant, on aurait pu ici écrire 'crud|default|default' ou 'crud||'
		}

		if (! CopixRequest::get ('confirm', false, true)){
			return CopixActionGroup::process ('generictools|Messages::getConfirm',
			array ('message'=>_i18n ('crud.confirmDelete', $record->caption_crud),
			'confirm'=>_url ('delete', array ('id_crud'=>$record->id_crud, 'confirm'=>1)),
			'cancel'=>_url ('|')));
		}else{
			_ioDAO ('tutorial_crud')->delete ($record->id_crud);
		}
		return _arRedirect (_url ('|'));
	}

	/**
	 * Formulaire de modification / création
	 */
	public function processEdit (){
		//Si un identifiant d'élément à modifier est donné et que ce dernier existe, on le place
		//dans la session pour qu'il soit défini comme élément à modifier
		if ($crud_id = CopixRequest::getInt ('id_crud')){
			if ($toEdit = _ioDAO ('tutorial_crud')->get ($crud_id)){
				CopixSession::set ('crudi18n|edit', $toEdit);
			}
		}

		//On regarde s'il existe un élément en cours de modification, si ce n'est pas le cas on 
		//passe en mode création
		if ((($toEdit = CopixSession::get ('crudi18n|edit')) === null) || (_request ('new') == 1)){
			CopixSession::set ('crudi18n|edit', $toEdit = _record ('tutorial_crud'));
		}

		//Préparation de la page
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = $toEdit->id_crud ? _i18n ('crud.title.edit') : _i18n ('crud.title.create');
		$ppo->toEdit = $toEdit;
		//si on demande à afficher les messages d'erreurs
		$ppo->arErrors = CopixRequest::get ('errors') ? _ioDAO ('tutorial_crud')->check ($toEdit) : array ();
		return _arPpo ($ppo, 'crud.form.tpl');
	}

	/**
	 * Validation en base de données du formulaire
	 */
	public function processValid (){
		//On vérifie que l'on est bien en train de modifier un élément, sinon on retourne à la liste.
		if (($toEdit = CopixSession::get ('crudi18n|edit')) === null){
			return _arRedirect (_url ('|'));
		}

		//validation des modification depuis le formulaire
		$toEdit->caption_crud = CopixRequest::get ('caption_crud');
		$toEdit->description_crud = CopixRequest::get ('description_crud');
		CopixSession::set ('crudi18n|edit', $toEdit);

		//Si tout va bien, on sauvegarde
		if (_ioDAO ('tutorial_crud')->check ($toEdit) === true){
			if ($toEdit->id_crud){
				_ioDAO ('tutorial_crud')->update ($toEdit);
			}else{
				_ioDAO ('tutorial_crud')->insert ($toEdit);
			}
			//on vide la session
			CopixSession::set ('crudi18n|edit', null);
			return _arRedirect (_url ('|'));
		}else{
			//un problème ? on retourne sur la page de modification en indiquant qu'il existe un problème
			return _arRedirect (_url ('edit', array ('errors'=>1)));
		}
	}
}
?>