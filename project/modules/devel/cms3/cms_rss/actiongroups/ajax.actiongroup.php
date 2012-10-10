<?php
class ActionGroupAjax extends CopixActionGroup {
		
	protected function _getEditedElement (){
		CopixRequest::assert ('editId');
		if (!$element =	CopixSession::get ('portlet|edit|record', _request ('editId'))){
			throw new CopixException ('Portlet en cours de modification perdu');
		}
		return $element;
	}
		
	public function processUpdateRss (){
		$ppo = new CopixPPO ();
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null){
			$portlet = $this->_getEditedElement();
		}
		$portlet->setEtat (Portlet::UPDATED);
		$portlet->setOption ('caption_rss', _request('caption_rss'));	
		if (_request('id_rss')){
			if(($portletElement = $portlet->getPortletElementAt (0)) == null){
				$portletElement = $portlet->attach (_request('id_rss'), 0);
			}
		}
		return _arNone();
	}
 	

}