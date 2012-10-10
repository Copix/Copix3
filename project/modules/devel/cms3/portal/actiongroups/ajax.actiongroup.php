<?php
class ActionGroupAjax extends CopixActionGroup {
	
	const ELEMENT_CHOOSER_MOD = 0;
	
	const IMAGE_CHOOSER_MOD = 1;
	
	const DOCUMENT_CHOOSER_MOD = 2;
	
	public function processUpdatePortlet (){
		$portlet = CopixSession::get ('portal|'._request ('portletId'), _request ('editId'));
		if ($portlet == null){
			$portlet = $this->_getEditedElement();
		}
		$portlet->setOption ('template', _request ('template', null));	
		return _arNone ();
	}
	
	public function processUpdateInfosGenerales (){
		CopixRequest::assert ('editId');
		if (!$element =	CopixSession::get ('page|edit|record', _request ('editId'))){
			throw new CopixException ('Page en cours de modification perdu');
		}
		_ppo (CopixRequest::asArray ('caption_hei', 'description_hei', 'template_page', 'browser_page', 'menu_caption_hei'))->saveIn ($element);
		return _arNone ();
	}
	
	public function processUpdatePortletInfos (){
		CopixRequest::assert ('editId');
		if (!$element =	CopixSession::get ('portlet|edit|record', _request ('editId'))){
			throw new CopixException ('Portlet en cours de modification perdu');
		}
		_ppo (CopixRequest::asArray ('caption_hei', 'description_hei'))->saveIn ($element);
		return _arNone ();
	}
	
	protected function _getEditedElement (){
		CopixRequest::assert ('editId');
		if (!$element =	CopixSession::get ('portlet|edit|record', _request ('editId'))){
			throw new CopixException ('Portlet en cours de modification perdu');
		}
		return $element;
	}
		
	public function processUpdateText (){
		$ppo = new CopixPPO ();
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null){
			$portlet = $this->_getEditedElement();
		}
		$portlet->setEtat (Portlet::UPDATED);

		switch (_request('editor', CmsEditorServices::WIKI_EDITOR)){
			case CmsEditorServices::WIKI_EDITOR :
				$portlet->setOption ('text', _request('text'));
				break;
			case CmsEditorServices::WYSIWYG_EDITOR :
				$portlet->setOption ('html', _request('text'));
				break;
		}		
		return _arNone();
	}
	
	public function processUpdateEditor (){
		$ppo = new CopixPPO ();
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null){
			$portlet = $this->_getEditedElement();
		}
		$portlet->setEtat (Portlet::UPDATED);
		$portlet->setOption ('editor', _request('editor'));
		
		return _arNone();
	}

    /**
     * Cette méthode enregistre les options du template
     * TODO documenter la manière dont il faut saisir les options dans les fichiers templates (xml)
     * @return CopixActionReturn
     */
    public function processUpdateOptions (){
        $ppo = new CopixPPO ();
        $portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
        if ($portlet == null){
            $portlet = $this->_getEditedElement();
        }
        $portlet->setEtat (Portlet::UPDATED);
        $erase = _request("erase", true);
        $options = CopixRequest::asArray();
        unset($options['module']);
        unset($options['group']);
        unset($options['action']);
        unset($options['portletId']);
        unset($options['editId']);
        unset($options['Copix']);
        if(isset($options['erase'])){
        	unset($options['erase']);
        }
        if ($erase){
        	$portlet->setOptions ($options);
        } else {
        	foreach ($options as $key=>$option){
        		$portlet->setOption ($key, $options);
        	}
        }
        return _arNone();
    }


    /**
     * Cette méthode retourne les options du template
     * TODO documenter la manière dont il faut saisir les options dans les fichiers templates (xml)
     * @return String
     */
	public function processGetOptions (){
		$ppo = new CopixPPO ();
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
        if ($portlet == null){
			$portlet = $this->_getEditedElement();
		}
		$ppo->MAIN = CopixJSON::encode($portlet->getOptions ());
		return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
    }
    	
	public function processGetTextPreview (){
		$text = _request('text');
		$newText = _ioClass('cms_editor|cmswikiparser')->transform($text);
		
		$ppo = new CopixPPO ();
		$ppo->MAIN = $newText;
		return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
	}
	
	public function processUpdateHtmlText (){
		$ppo = new CopixPPO ();
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null){
			$portlet = $this->_getEditedElement();
		}
		$portlet->setEtat (Portlet::UPDATED);
		$options = CopixRequest::asArray('htmltext');
		$portlet->setOptions ($options);
		
		return _arNone();
	}
	
	public function processUpdateMenu (){
		$ppo = new CopixPPO ();
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null){
			$portlet = $this->_getEditedElement();
		}
		$portlet->setEtat (Portlet::UPDATED);
		
		$options = CopixRequest::asArray('level_hem', 'depth_hem', 'portlet_hem', 'public_id_hem');
		$targetMenu = _class('heading|headingelementinformationservices')->get($options['public_id_hem']);
		$options['portlet_hem'] = $targetMenu->type_hei == 'portlet' ? 1 : 0;	
		_dump($options);
		$portlet->setOptions ($options);
		
		return _arNone();
	}
	
	public function processUpdateAnchor (){
		$ppo = new CopixPPO ();
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null){
			$portlet = $this->_getEditedElement();
		}
		$portlet->setEtat (Portlet::UPDATED);

		$options = CopixRequest::asArray('name');
		$matches = null;
		
		preg_match('/[a-zA-Z_][a-zA-Z0-9_]*/', $options['name'], $matches);
		$portlet->setOptions ($options);
		if ($matches[0] != $options['name']){
			return _arString("La chaine n'est pas valide : caractères alphanumériques seulement, sans commencer par un chiffre, ex : \"$matches[0]\".<br />");
		}
		return _arNone();
	}
	
	public function processUpdateDateUpdate (){
		$ppo = new CopixPPO ();
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null){
			$portlet = $this->_getEditedElement();
		}
		$portlet->setEtat (Portlet::UPDATED);
		
		$element = null;
		if ($portlet->id_page){
			$element = _ioClass('portal|pageservices')->getById ($portlet->id_page);
			$element = _ioClass('headingelementinformationservices')->get ($element->public_id_hei);
		
		} 
		$dateUpdateTpl = new CopixTpl ();
		$dateUpdateTpl->assign ('date', $element ? CopixDateTime::yyyymmddhhiissToFormat($element->date_update_hei, 'd/m/Y') : '');
		$dateUpdateTpl->assign ('heure', $element ? CopixDateTime::yyyymmddhhiissToFormat($element->date_update_hei, 'H:i:s') : '');
				
		$ppo = _ppo();
		$ppo->MAIN = $dateUpdateTpl->fetch(_request('template'));
		return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
	}
	
	public function processElementChooser(){
		$ppo = new CopixPPO ();
		$identifiantFormulaire = _request('identifiant'); 
		$selected = _request('selected');
		$filter = _request('filter');
		$arFilter = $filter ? explode(';', $filter) : array();
		$mode = self::ELEMENT_CHOOSER_MOD;
		
		if($filter == 'image'){
			$mode = self::IMAGE_CHOOSER_MOD; 
		}else if($filter == 'document'){
			$mode = self::DOCUMENT_CHOOSER_MOD;
		}
		
		$id = _request('id');
		$ppo->MAIN = CopixZone::process ('heading|headingelement/headingelementchooser', array('arTypes'=>$arFilter, 'showAnchor'=>true, 'inputElement'=>$id, 'identifiantFormulaire'=>$id, 'showSelection'=>true, 'mode'=>$mode, 'selectedIndex' => $selected));
		return _arDirectPPO($ppo, 'generictools|blank.tpl'); 
	}
	
	public function processUpdateColumns (){
		$ppo = new CopixPPO ();
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null){
			$portlet = $this->_getEditedElement();
		}
		$portlet->setEtat (Portlet::UPDATED);
		$options = CopixRequest::asArray('leftColumn', 'rightColumn');
		$portlet->setOptions ($options);
		return _arNone();
	}
	
	public function processUpdateTheme (){
		CopixRequest::assert ('editId');
		if (!$page =	CopixSession::get ('page|edit|record', _request ('editId'))){
			throw new CopixException ('Page en cours de modification perdu');
		}
		CopixTpl::setTheme(_request('theme'));
		$tpl = new CopixTpl();
		$tpl->assign('MAIN', $page->render (RendererMode::HTML, RendererContext::UPDATED, array ('editId'=>_request ('editId'), 'errors'=> array())));
		$tpl->assign('HTML_HEAD', '');
		$tpl->assign('TITLE_PAGE', '');
		$ppo = _ppo();
		$ppo->MAIN = $tpl->fetch('default|main.php');
		return _arDirectPPO($ppo, 'generictools|blank.tpl'); 
	}
	
}