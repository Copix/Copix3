<?php
class ActionGroupAdminPortlet extends ActionGroupAbstractAdminHeadingElement  {
	
	protected function _beforeAction ($pActionName){
		parent::_beforeAction($pActionName);
		CopixHtmlHeader::addCSSLink (_resource ('heading|css/cms.css'));
		//CopixHTMLHeader::addCSSLink (_resource ('heading|css/mycmscssconfig.css'));
	}
	
	/**
	 * Préparation des données à éditer
	 */
	public function processPrepareEdit (){
		//On regarde si on nous donne un identifiant de modification
		$editId = _request ('editId');
		
		$toEdit = null;
		//on regarde le type d'action que l'on souhaite effectuer (création ou modification)
		if (CopixSession::exists ('id_helt', $editId)){		
			$toEdit = _class ('PortletServices')->getHeadingElementPortletById (CopixSession::get ('id_helt', $editId));
		} elseif (_request('type_portlet')){
			$toEdit = _class('portal|portletservices')->getPortletInstance (_request ('type_portlet'));
			$toEdit->parent_heading_public_id_hei = CopixSession::get ('heading', $editId); 
			$toEdit->public_id_hei = null; 
		}
		
		//on met l'information à modifier en session
		CopixSession::set ('portlet|edit|record', $toEdit, $editId);

		//redirection vers l'écran de modification
		return _arRedirect (_url ('adminportlet|edit', array ('editId'=>$editId, 'mode'=>($toEdit!=null && $toEdit->id_portlet) ? 'edit' : 'general')));
	}

	/**
	 * Formulaire de modification de l'élément
	 */
	public function processEdit (){
		$ppo = _ppo ();		
		$ppo->editId = _request ('editId');		
		$ppo->heading = CopixSession::get ('heading', $ppo->editId);		
		$ppo->arTypes = array ();
		
		$arTypes = _class ('portal|portletservices')->getList ();
		foreach ($arTypes as $key=>$type) {
			$ppo->arTypes[$type['portlettype']] = $type['caption'];
		}
		
		try {
			$ppo->editedElement = $this->_getEditedElement ();		
			foreach ($arTypes as $key=>$type){
				if ($type['portlettype'] == $ppo->editedElement->type_portlet){					
					break;
				}
			}
			$ppo->type_portlet = $type['caption'];
		}
		catch (CopixException $e){
			//
		}

		CopixHtmlHeader::addCSSLink (_resource ('portal|styles/pageedit.css'));
		$ppo->TITLE_PAGE = $ppo->editedElement != null && $ppo->editedElement->public_id_hei ? 'Modification de portlet' : 'Création de portlet';
		if (($mode = _request ('mode', 'general')) == 'general'){					
			//Modification de la portlet des informations générales
			$ppo->backUrl = _url ('heading|element|', array ('heading' => $ppo->heading));
			return _arPpo ($ppo, 'portlet.form.tpl');			
		}else{
			//modification de la page portlet elle même
			$ppo->renderContext = $ppo->editedElement->getEtat () == Portlet::UPDATED ? RendererContext::UPDATED : RendererContext::DISPLAYED;
			return _arPpo ($ppo, 'portlet.edit.php');
		}
	}

	/**
	 * Sauvegarde de la portlet
	 *
	 * @return unknown
	 */
	public function processValid (){
		$element = $this->_getEditedElement ();
		if ($element->id_portlet != null) {
			$oldStatus = $element->status_hei;
		} else {
			$oldStatus = null;
		}
		_ppo (CopixRequest::asArray ('caption_hei', 'description_hei'))->saveIn ($element);

		$toPlan = _ioClass ('HeadingElementInformationServices')->checkPlanningDates(_request('published_date', false), _request('end_published_date', false));
	   
		//On crée un nouvel élément si  
		// id_portlet === null (enregistrement jamais crée)
		if ($element->id_portlet === null){
			_class ('portletServices')->insertHeadingElementPortlet ($element);
		}elseif ($element->status_hei == HeadingElementStatus::DRAFT
				 || ($element->status_hei == HeadingElementStatus::PLANNED && $toPlan)){
			//On modifie directement l'objet si 
			//le statut de l'élément est brouillon
			_class ('portletServices')->updateHeadingElementPortlet ($element);
		}else{
			//Dans tous les autres cas
			//on crée une nouvelle version de l'élément 
			_class ('portletServices')->versionHeadingElementPortlet ($element);
		}
		
		if (_request('toCreate', false)){
			CopixSession::set ('then', _url('heading|element|prepareCreate', array('type'=>_request('toCreate'), 'heading'=>$element->parent_heading_public_id_hei, 'then'=>_url('heading|element|prepareEdit', array('type'=>'portlet','id'=>$element->id_helt, 'heading'=>$element->parent_heading_public_id_hei)))), _request ('editId'));
		}

		$aParam = array (
			'editId' => _request ('editId'),
			'result'=>'saved',
			'selected'=>array($element->id_helt . '|' . $element->type_hei)
		);
		
		//planification
		if($toPlan){
			$published_date_hei = _request('published_date', false) ? CopixDateTime::DateTimeToyyyymmddhhiiss(_request('published_date')) : null;
			$end_published_date_hei = _request('end_published_date', false) ? CopixDateTime::DateTimeToyyyymmddhhiiss(_request('end_published_date')) : null;
			
			//si on part d'une version publiée sur laquelle on veut juste ajouter une date d'archivage, on publie d'abord la nouvelle version
			if($oldStatus == HeadingElementStatus::PUBLISHED && !$published_date_hei && $end_published_date_hei){
				_ioClass ('HeadingElementInformationServices')->publishById ($element->id_helt, $element->type_hei);
			}
			//on planifie
			_ioClass ('HeadingElementInformationServices')->planById ($element->id_helt, $element->type_hei, $published_date_hei, $end_published_date_hei);
		}
		//publication
		else if (CopixRequest::getBoolean ('publish')) {
			_ioClass ('HeadingElementInformationServices')->publishById ($element->id_helt, $element->type_hei);
			// Possibilité de notifier par email qu'un contenu a été publié
			$headingElementType = new HeadingElementType ();
			$typeInformations = $headingElementType->getInformations ($element->type_hei);
			if (CopixUserPreferences::get($typeInformations['module'].'|'.$element->type_hei.'Notification') == '1') {
                // Previous Action
                $aParam['prevaction'] = 'publish';
            }
		}

	  	//retour sur le module heading|admin
	  	return _arRedirect (_url ('heading|element|finalizeEdit', $aParam));
	}
	
	/**
	 * Demande la sauvegarde de la portlet de la page
	 */
	public function processSavePortlet (){
		CopixRequest::assert ('id');
		//On récupère la page en cours de modification tout en en vérifiant la présence	
		$this->_savePortlet(_request ('id'));
		return _arRedirect (_url ('heading|element|finalizeEdit', array ('editId'=>_request ('editId'), 'result'=>'saved')));
	}
	
	private function _savePortlet ($pPortletId){
		$portlet = $this->_getEditedElement ();
		$savePortlet = $page->findPortletById ($pPortletId);
		$portlet = CopixSession::get('portlet|'.$pPortletId, _request('editId'));
		if($portlet != null){
			$savePortlet->loadFromObject($portlet);
			$portlet->setEtat (Portlet::DISPLAYED);
			$savePortlet->setEtat (Portlet::DISPLAYED);
		}
	}
	
	/**
	 * Affiche la portlet de la page suivant l'etat passé en parametre
	 */
	public function processDisplayPortlet (){
		CopixRequest::assert ('editId');
		$portlet = $this->_getEditedElement ();
		$portlet->setEtat (_request ('etat'));
		return _arRedirect (_url ('adminportlet|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit')));
	}
	
	/**
	 * On incrémente l'ordre d'affichage d'un élément
	 *
	 * @return unknown
	 */
	public function processMoveUpElement () {
		CopixRequest::assert ('editId', 'portal_id');
		$portlet = $this->_getEditedElement ();
		$position = _request('position');
		$portlet->moveUpElement ($position);
		return _arRedirect (_url ('adminportlet|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit'))."#".$portlet->getRandomId ());
	}

	/**
	 * On décrémente l'ordre d'affichage d'un élément
	 *
	 * @return unknown
	 */
	public function processMoveDownElement () {
		CopixRequest::assert ('editId', 'portal_id');
		$portlet = $this->_getEditedElement ();
		$position = _request('position');
		$portlet->moveDownElement ($position);
		return _arRedirect (_url ('adminportlet|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit'))."#".$portlet->getRandomId ());
	}
}
