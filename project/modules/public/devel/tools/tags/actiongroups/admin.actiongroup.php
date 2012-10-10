<?php
/**
 * 
 */

/**
 * Actions d'administration sur les tags
 */
class ActionGroupAdmin extends CopixActionGroup {
	/**
	 * Liste des tags
	 */
	public function processDefault (){
		$ppo = new CopixPpo (array ('TITLE_PAGE'=>'Liste des Tags'));
		$ppo->arTags = _ioDao ('tags')->findBy (_daoSp ()->orderBy ('name_tag'));
		return _arPpo ($ppo, 'tags.list.php');		
	}
	
	/**
	 * Ajout d'un nouveau tag
	 */
	public function processAdd (){
		if (! $record = (_ioDao ('tags')->get ($tag = CopixRequest::get ('name_tag')))){
			$tagR = _record ('tags');
			$tagR->name_tag = $tag;
			_ioDao ('tags')->insert ($tagR);
		}
		return _arRedirect (_url ('admin|'));
	}
	
	/**
	 * Supression d'un tag 
	 */
	public function deleteTag (){
		if (! _ioDAO ('tags')->get ($tag = _request::get ('name_tag'))){
			return _arRedirect (_url ('admin|'));
		}

		if (!_request ('confirm')){
			return CopixActionGroup::process ('generictools|Messages::getConfirm', 
				array ('message'=>_i18n ('tags.confirmDeleteTag', $tag),
						'confirm'=>_url ('admin|delete', array ('name_tag'=>$tag, 'confirm'=>1)),
						'cancel'=>_url ('admin|'))); 
		}else{
			_ioDAO ('tags')->delete ();
		}
	}
}
?>