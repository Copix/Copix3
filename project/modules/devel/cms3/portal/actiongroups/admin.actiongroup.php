<?php
class ActionGroupAdmin extends ActionGroupAbstractAdminHeadingElement  {
	
	/**
	 * Stocke la page courante (extraction des données en session)
	 * @var Page
	 */
	private $_page = null;
	
	protected function _beforeAction ($pActionName){
		parent::_beforeAction($pActionName);
		CopixHtmlHeader::addCSSLink (_resource ('heading|css/cms.css'));
		//CopixHTMLHeader::addCSSLink(_resource ('heading|css/mycmscssconfig.css'));
	}

	public function processDefault (){
		_tag ('mootools');
		CopixHtmlHeader::addJSLink (_resource ('|js/draggableportlet.js'));
		CopixHtmlHeader::addCSSLink (_resource ('|styles/pageedit.css'));
		CopixHTMLHeader::addCSSLink (_resource ('heading|css/mycmscssconfig.css'));

		$ppo = _ppo ();
		return _arPpo ($ppo, 'page.test.tpl');
	}

	/**
	 * Préparation des données à éditer
	 */
	public function processPrepareEdit (){
		//Récupération du numéro de modification
		$editId = _request ('editId', null);
		
		$toEdit = _class ('page');
		//on regarde le type d'action que l'on souhaite effectuer (création ou modification)
		if (CopixSession::exists ('id_helt', $editId)){
			$toEdit = _class ('pageServices')->getById (CopixSession::get ('id_helt', $editId));
		} else {
			$newRecord = DAORecordcms_pages::create ();
			$newRecord->parent_heading_public_id_hei = CopixSession::get ('heading', $editId); 
			$newRecord->template_page = Page::DEFAULT_TEMPLATE; 
			$toEdit->load ($newRecord);
		}
		
		//on met l'information à modifier en session
		CopixSession::set ('page|edit|record', $toEdit, $editId);

		//redirection vers l'écran de modification
		return _arRedirect (_url ('admin|edit', array ('editId'=>$editId, 'mode'=>($toEdit->id_page) ? 'edit' : 'general')));
	}

	/**
	 * Formulaire de modification de l'élément
	 */
	public function processEdit (){
		$element = $this->_getEditedElement ();
		$ppo = _ppo ();
		$ppo->editedElement = $element;
		$ppo->editId = _request ('editId');
		$ppo->id_page = $this->_getEditedElement ()->id_page;
		$ppo->arTemplates = CopixTpl::find ('portal', 'visuel.page.tpl');
		$ppo->TITLE_PAGE = $ppo->editedElement->public_id_hei ? 'Modifier une page' : 'Créer une page';

		_notify ('breadcrumb', array ('path' => array ('#' => $ppo->TITLE_PAGE)));
		
		//dans le cas de l'admin dans un theme different que la page, on a besoin de connaitre les templates du theme de la page
        //on charge donc le teme de la page, puis on charge les template du theme.
		$currentTheme = CopixTpl::getTheme();
   		$theme = $this->_headingElementInformationServices->getTheme ($this->_getPublicId (), $fooParameterIn);
   		if ($theme && CopixConfig::get ('heading|useDefinedThemeForPage')) {
			CopixTpl::setTheme ($theme);
		}
		CopixTpl::clearFilePathCache();
		
		$isTemplateValid = file_exists (CopixTpl::getFilePath ("portal|pagetemplates/" . $element->getTemplate ()));
        
        if (($mode = _request ('mode', 'general')) == 'general' || !$isTemplateValid){       	
			$ppo->xmlPath = CopixTpl::getFilePath("portal|pagetemplates/pagetemplates.xml");
			$ppo->selected  = '';
			
            // Ajouté pour afficher le nom du modèle de page sélectionné
            $ppo->textBouton = 'Cliquez ici pour choisir le modèle de page';
            // on récupère le nom du template dans le fichier xml
            if( (is_readable ($ppo->xmlPath)) && ($xml = simplexml_load_file ($ppo->xmlPath))) {
                $sXpath = '/templates/template[tpl="'.$ppo->editedElement->template_page.'"]';
                if ($aXmlElement = $xml->xpath ($sXpath) ){
                    $ppo->textBouton = $aXmlElement[0]->name;
                }
            }
                        
			if (!$isTemplateValid){
				//on a une erreur
				$ppo->error = "Veuillez selectionner un template, le template ".$element->getTemplate ()." n'existe pas.";
			}	
			//on recharge maintenant le theme de l'admin
			CopixTpl::setTheme ($currentTheme);
			CopixHtmlHeader::addCSSLink (_resource ('|styles/pageedit.css'));
			//Modification de la page des informations générales
			return _arPpo ($ppo, 'page.form.tpl');			
		}
				
		$ppo->errors = CopixSession::get ('portal|edit|errors', _request ('editId'));
		
		//on supprimer directement pour que l'erreur n'apparaisse pas lors de la mise à jour des portlets
		CopixSession::delete('portal|edit|errors', _request ('editId'));
		
		CopixHtmlHeader::addCSSLink (_resource ('|styles/pageupdatemenu.css'));
		CopixHtmlHeader::addCSSLink (_resource ('|styles/pageedit.css'));
		//modification de la page en elle même
		$ppo->editedElement->setEtat (Page::UPDATE);
		$ppo->MAIN .= CopixZone::process('portal|pageupdateheadermenu', array('public_id_hei'=>$ppo->editedElement->public_id_hei, 'renderContext'=>RendererContext::UPDATED, 'element'=>$ppo->editedElement, 'parent_public_id'=>$ppo->editedElement->parent_heading_public_id_hei, 'caption_hei'=>$ppo->editedElement->caption_hei));
		$ppo->MAIN .= $ppo->editedElement->render (RendererMode::HTML, RendererContext::UPDATED, array ('editId'=>$ppo->editId,'errors'=>$ppo->errors));
		
		return _arPpo ($ppo, array('template'=>'generictools|blanknohead.tpl', 'mainTemplate'=>$this->_headingElementInformationServices->getTemplate ($this->_getPublicId ())));		
	}

	/**
	 * Sauvegarde de la page
	 *
	 * @return unknown
	 */
	public function processValid (){
		$element = $this->_getEditedElement ();
		$oldStatus = (isset ($element->status_hei)) ? $element->status_hei : null;
		//mise à jour de l'enregistrement en cours de modification
		_ppo (CopixRequest::asArray ('caption_hei', 'title_hei', 'browser_page', 'menu_caption_hei', 'description_hei', 'template_page'))->saveIn ($element);
        $arErrors = $this->_checkPage();
        if (count($arErrors) > 0) {
			//la page n'est pas valide on revoit sur l'édition
			CopixSession::set ('portal|edit|errors', $arErrors, _request ('editId'));
			return _arRedirect(_url ('admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit')));
		} else {
			CopixSession::delete('portal|edit|errors', _request ('editId'));
		}
		
		$this->_savePage();
		
		$toPlan = _ioClass ('HeadingElementInformationServices')->checkPlanningDates(_request('published_date', false), _request('end_published_date', false));

		//On crée un nouvel élément si  
		// id_page === null (enregistrement jamais crée)
		if ($element->id_page === null){
			_class ('pageServices')->insert ($element);
		}elseif ($element->status_hei == HeadingElementStatus::DRAFT || ($element->status_hei == HeadingElementStatus::PLANNED && $toPlan)){
			//On modifie directement l'objet si 
			//le statut de l'élément est brouillon
			//ou planifié ET que l'on modifie la date de planification
			_class ('pageServices')->update ($element);
		}else{
			//Dans tous les autres cas
			//on crée une nouvelle version de l'élément 
			_class ('pageServices')->version ($element);
		}

		if (_request('toCreate', false)){
			CopixSession::set ('then', _url('heading|element|prepareCreate', array('type'=>_request('toCreate'), 'heading'=>$element->parent_heading_public_id_hei, 'then'=>_url('heading|element|prepareEdit', array('type'=>'page','id'=>$element->id_helt, 'heading'=>$element->parent_heading_public_id_hei)))), _request ('editId'));
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
			$toPublish = $element->getListElementsInPage(HeadingElementStatus::DRAFT);
			if (!empty($toPublish) && !CopixSession::get('then', _request ('editId'), false)){
				CopixSession::set ('then', _url('portal|default|publishElementsInPage', array('public_id_hei'=>$element->public_id_hei)), _request ('editId'));			
			}
		}
		
		//retour sur l'écran d'admin générale
  		return _arRedirect (_url ('heading|element|finalizeEdit', $aParam));
	}
	
	public function processAddPortlet (){
		//On récupère la page en cours de modification tout en en vérifiant la présence
		$page = $this->_getEditedElement ();
		
		//On vérifie les paramètre requis pour l'ajout de portlet
		CopixRequest::assert ('position', 'type', 'editId');
		
		//création de la portlet a ajouter
		$portlet = _class ('PortletServices')->create (_request ('type'));
		$portlet->id_page = $page->id_page;
		if(_request('on')){
			$position = $page->getPortletPosition (_request ('on'));
			$page->addPortlet ($portlet, $position['column'], $position['position']);
		} else {
			$page->addPortlet ($portlet, _request ('position'));
		}

		return _arRedirect (_url ('portal|admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit'))."#".$portlet->getRandomId ());
	}
	
	/**
	 * Demande la supression de la portlet de la page
	 */
	public function processDeletePortlet (){
		CopixRequest::assert ('id');
		//On récupère la page en cours de modification tout en en vérifiant la présence
		$page = $this->_getEditedElement ();
		$page->deletePortlet (_request ('id'));
		CopixSession::set('portal|'._request ('id'), null, _request ('editId'));	
		return _arRedirect (_url ('admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit')));
	}
	
	/**
	 * Demande la sauvegarde de la portlet de la page
	 */
	public function processCancelPortlet (){
		CopixRequest::assert ('id');
		//On récupère la page en cours de modification tout en en vérifiant la présence
		$page = $this->_getEditedElement ();
		$portlet = $page->findPortletById (_request ('id'));
		$portlet->setEtat (Portlet::DISPLAYED);
		CopixSession::set('portal|'.$portlet->getRandomId(), $portlet, _request ('editId'));		
		return _arRedirect (_url ('admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit')));
	}
	
	/**
	 * Demande la sauvegarde de la portlet de la page
	 */
	public function processSavePortlet (){
		CopixRequest::assert ('id');
		//On récupère la page en cours de modification tout en en vérifiant la présence	
		$this->_savePortlet(_request ('id'));
		return _arRedirect (_url ('admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit')));
	}
	
	private function _savePortlet ($pPortletId){
		$page = $this->_getEditedElement ();
		$savePortlet = $page->findPortletById ($pPortletId);
		$portlet = CopixSession::get('portal|'.$pPortletId, _request('editId'));
		if($portlet != null){
			$savePortlet->loadFromObject($portlet);
			$portlet->setEtat (Portlet::DISPLAYED);
			$savePortlet->setEtat (Portlet::DISPLAYED);
		}
	}
	
	/**
	 * Copie de portlet
	 */
	public function processCopyPortlet (){
		CopixRequest::assert ('id');
		if (($clipBoard = CopixSession::get('portletClipBoard', 'cms3')) == null){
			$clipBoard = array();
		}
		$clipBoard[_request('id')] = CopixSession::get('portal|'._request('id'), _request('editId'));;
		CopixSession::set('portletClipBoard', $clipBoard, 'cms3');
		CopixSession::set('newElementInPortletClipBoard', true, 'cms3');
		return _arRedirect (_url ('admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit')));
	}
	
	/**
	 * Colle une portlet
	 */
	public function processPastePortlet (){
		//On récupère la page en cours de modification tout en en vérifiant la présence
		$page = $this->_getEditedElement ();
		
		//On vérifie les paramètre requis pour l'ajout de portlet
		CopixRequest::assert ('position', 'portletClipboardId', 'editId');
		
		if (($clipBoard = CopixSession::get('portletClipBoard', 'cms3')) != null){
			$portletToPaste = $clipBoard[_request('portletClipboardId')];
			$portlet = _class ('PortletServices')->createByPortletType ($portletToPaste->type_portlet);
			$portlet->loadFromObject($portletToPaste);
			$portlet->id_page = $page->id_page;
			$portlet->setEtat (Portlet::DISPLAYED);
			
			if(_request('on')){
				$position = $page->getPortletPosition (_request ('on'));
				$page->addPortlet ($portlet, $position['column'], $position['position']);
			} else {
				$page->addPortlet ($portlet, _request ('position'));
			}
		
			return _arRedirect (_url ('portal|admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit'))."#".$portlet->getRandomId ());
		}
		return _arRedirect (_url ('portal|admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit')));	
	}
	
	/**
	 * Colle une portlet
	 */
	public function processEmptyPortletClipboard (){
		//On récupère la page en cours de modification tout en en vérifiant la présence	
		CopixSession::delete('portletClipBoard', 'cms3');
		return _arRedirect (_url ('admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit')));
	}
	
	/**
	 * Vérification de la cohérence des portlets
	 * @return boolean
	 */
	private function _checkPage() {
		
		$arErrors = array();
		
		//Liste des formulaires de la page
		$forms = array();
		//Vérifie si l'un des formulaires est en mode partial
		$partialMode = false;
		$selectedFields = array();
		
		$page = $this->_getEditedElement ();
		foreach ($page->getPortlets() as $columnContainer){
			foreach ($columnContainer as $portlet){
				if ($portlet instanceof PortletCMSForm) {
					$forms[] = $portlet->getOption('cmsform');
					if (count($portlet->getOption('selectedFields')) > 0) {
						$partialMode = true;
						$selectedFields = array_merge($selectedFields, $portlet->getOption('selectedFields'));
					}
				}
			}
		}
		
		if (count($forms) > 0) {
			//En mode partial on ne peut afficher qu'un formulaire par page
			if ($partialMode && count(array_unique($forms)) > 1) {
				$arErrors[] = "Une page ne peut contenir qu'un seul formulaire, lorsque vous utilisez le rendu partiel.";
				return $arErrors;
			}
			
			//En mode partial, on doit véfifier que tout les champs du formulaire sont présents et le sont qu'une seule fois
			if ($partialMode) {
				//On verifie s'il n'y a pas de doublon
				if (count($selectedFields) != count(array_unique($selectedFields))) {
					$arErrors[] = "Vous n'avez pas le droit d'utilisé un champs plusieurs fois.";
					return $arErrors;
				}
				//Tous les éléments sont-ils présents? 
				$arContent = DAOcms_form::instance ()->getContentByIdElement($forms[0]);
				$diff = array_diff($selectedFields,$arContent);
				
				//Gestion du bouton valider et des éventuels élément vide
				$diff = array_flip($diff);
				unset($diff[''], $diff['submit']);
				
				if (count($diff) > 0) {
					$arErrors[] = "Le nombre de champs utilisé, n'est pas egal à celui présent dans le formulaire";
					return $arErrors;
				}
			}
		}
		return $arErrors;
	}
	
	private function _savePage (){
		$page = $this->_getEditedElement ();
		foreach ($page->getPortlets() as $variable){
			foreach ($variable as $portlet){
				$this->_savePortlet($portlet->getRandomId ());
				CopixSession::set('portal|'.$portlet->getRandomId(), null, _request('editId'));
			}
		}
	        
		$theme = $this->_headingElementInformationServices->getTheme ($page->parent_heading_public_id_hei, $fooParameterIn);
        if ($theme != _request('theme_id')) {
            $page->theme_id_hei = _request('theme_id');
        } 
	}
	
	/**
	 * Affiche la portlet de la page suivant l'etat passé en parametre
	 */
	public function processDisplayPortlet (){
		CopixRequest::assert ('id', 'editId');
		$portlet = CopixSession::get('portal|'._request ('id'), _request ('editId'));
		$portlet->setEtat (_request('etat'));
		return _arRedirect (_url ('admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit')));
	}
	
	/**
	 * On déplace la portlet
	 */
	public function processMove (){
		CopixRequest::assert ('id');
		//Si la colonne n'est pas donnée, alors on vérifie que position & on sont donnés
		if (! _request ('column')){
			CopixRequest::assert ('position', 'on');
		}	
		//récupération de la page en cours de modification
		$page = $this->_getEditedElement ();
		if (_request ('column')){
			$page->movePortlet (_request ('id'), _request ('column'));			
		}else{
			$position = $page->getPortletPosition (_request ('on'));
			$page->movePortlet (_request ('id'), $position['column'], _request ('position') == 'before' ? $position['position'] : $position['position']+1);
		}
		
		//on met à jour les portlets en sessions
		foreach ($page->getPortlets () as $variable){
			foreach ($variable as $portlet){
				if (($tempPortlet = CopixSession::get ('portal|'.$portlet->getRandomId (), _request ('editId'))) != null){
					$tempPortlet->position = $portlet->position;
					$tempPortlet->variable = $portlet->variable;
				}
			}
		}
		return _arNone ();
	}
	
	/**
	 * Affiche la page en mode display
	 */
	public function processDisplayPage (){
		$ppo = _ppo ();
		$ppo->editedElement = $this->_getEditedElement ();
		$ppo->editId = _request ('editId');
		$ppo->arTemplates = CopixTpl::find ('portal', '.page.tpl');
		$ppo->TITLE_PAGE = 'Aperçu de page Web';
		$oHeanding = _ioClass ('headingelementinformationservices');

        //on affiche le theme de la rubrique dans laquelle on est.
        if ($theme = _ioClass ('headingelementinformationservices')->getTheme ($ppo->editedElement->public_id_hei ? $ppo->editedElement->public_id_hei : $ppo->editedElement->parent_heading_public_id_hei, $foo)){
            CopixTpl::setTheme ($theme);
        }
		$ppo->editedElement->setEtat (Page::UPDATE);

		$ppo->MAIN = '<form id="formPage" action="' . _url ('portal|admin|valid', array ('editId' => _request ('editId'))) . '" method="POST">';
		$ppo->MAIN .= '<input type="hidden" name="publish" id="publish" />';
		$ppo->MAIN .= '</form>';
		$ppo->MAIN .= $ppo->editedElement->render (RendererMode::HTML, RendererContext::DISPLAYED_ADMIN, array ('editId'=>$ppo->editId));
		return _arPpo ($ppo, 'generictools|blanknohead.tpl');
	}
	
	/**
	 * On incrémente l'ordre d'affichage d'un élément
	 *
	 * @return unknown
	 */
	public function processMoveUpElement () {
		CopixRequest::assert ('editId', 'portal_id', 'position');
		$isPage = true;
		$portlet = CopixSession::get('portal|'._request ('portal_id'), _request('editId'));
		if ($portlet== null){
			$portlet = parent::_getEditedElement ();
			$isPage = false;
		}
		$portlet->setEtat (Portlet::UPDATED);
		$portlet->moveUpElement(_request('position'));
		
		if($isPage){
			return _arRedirect (_url ('portal|admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit'))."#".$portlet->getRandomId ());
		}else{
			return _arRedirect (_url ('portal|adminportlet|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit'))."#".$portlet->getRandomId ());
		}
	}

	/**
	 * On décrémente l'ordre d'affichage d'un élément
	 *
	 * @return unknown
	 */
	public function processMoveDownElement () {
		CopixRequest::assert ('editId', 'portal_id', 'position');
		$isPage = true;
		$portlet = CopixSession::get('portal|'._request ('portal_id'), _request('editId'));
		if ($portlet == null){
			$portlet = parent::_getEditedElement ();
			$isPage = false;
		}
		$portlet->setEtat (Portlet::UPDATED);
		$portlet->moveDownElement(_request('position'));
		if($isPage){
			return _arRedirect (_url ('portal|admin|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit'))."#".$portlet->getRandomId ());
		}else{
			return _arRedirect (_url ('portal|adminportlet|edit', array ('editId'=>_request ('editId'), 'mode'=>'edit'))."#".$portlet->getRandomId ());
		}
	}
	
}