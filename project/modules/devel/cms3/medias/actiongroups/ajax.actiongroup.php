<?php

class ActionGroupAjax extends CopixActionGroup {

	public function processGetMedia (){
		$ppo = new CopixPPO ();
		$toReturn = '';
		$portletElement = null;
		$id = _request ('id_media');
		$portlet = $this->_getEditedElement ();
		$portlet->setEtat (Portlet::UPDATED);
		$media = $id ? _class ('medias|mediasservices')->getByPublicId ($id) : null;

		//on ajoute un media à la portlet, on renvoie le contenu de l'media en fonction des options
		if($id){
			$options = CopixRequest::asArray(
				"x", 
				"y",
				'contenuAlternatif',
				'imagePresentation',
				'variable',
				'version',
				'typeAffichage'
			);
			
			//Type de média (flash ou vidéo)
			$mediaType = _request('mediaType');
			
			$toReturn = CopixZone::process ('medias|mediaformview', array('options'=>$options, 'media'=>$media, 'mediaType'=> $mediaType, 'identifiantFormulaire' => _request ('portletId').'_pos_'._request('position')));
			//si le media n'est pas encore ajouté
			if(($portletElement = $portlet->getPortletElementAt (_request('position'))) == null){
				$portletElement = $portlet->attach ($media->id_hei);
			}
			$portletElement->setOptions ($options);	
		}
		
		//si oldMedia = new Media on est en modification on ne supprime pas, sinon on supprime
		if ($portletElement == null || ($portletElement != null && $portletElement->getHeadingElement ()->id_hei != _request ('id_media'))){
			if($media == null){
				try{
					$portlet->dettach (_request('position'));
				}catch (CopixException $e){
					//si on arrive ici c'est qu'on a voulu faire des modifs dans les options alors qu'on n'a pas selectionné 
					CopixLog::log($e->getMessage(), 'errors', CopixLog::EXCEPTION);
				}
			}
			else{
				$portletElement->setHeadingElement ($media);
			}
		}
		$ppo->MAIN = $toReturn;
		return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl');
	}
	
	public function processAddEmptyMedia (){
	
		if($this->_getEditedElement () instanceof Page){
			$portlet = $this->_getEditedElement ()->findPortletById (_request('portletId'));
		}else{
			$portlet = $this->_getEditedElement ();
		}
		$identifiantFormulaire = $portlet->getRandomId ()."_new_"._request ('idMediaVide');
		//on renvoie le template en indiquant un newMediaVide : on n'affichera pas la div qui contient l'media => deja cree par l'appel javascript
		$tpl = new CopixTpl ();		
		$tpl->assign ('portlet', $portlet);
		$tpl->assign ('newMediaVide', true);
		$tpl->assign ('position', _request ('position'));
		$tpl->assign ('justAddMedia', false);
		$tpl->assign ('arTemplates', CopixTpl::find ('medias', '.media.tpl'));
		
		$tpl->assign ('mediaType', $portlet->getMediaType());
		
		$toReturn = $tpl->fetch ($portlet->getPortletFormTemplate());			
		
		$ppo = new CopixPPO ();
		$ppo->MAIN = $toReturn;
		return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	}
	
	/**
	 * Retourne la page en cours d'edition
	 *
	 * @return Page
	 */
	protected function _getEditedElement (){
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null){
			$portlet = CopixSession::get('portlet|edit|record', _request('editId'));			
		}
		if (!$portlet){
			throw new CopixException ('Portlet en cours de modification perdu');
		}
		return $portlet;
	}
}

?>