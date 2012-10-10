<?php
/**
 * @package		
 * @subpackage 	STARTERKIT
 * @author		
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * @package tools
 * @subpackage formtools
 */
class ActionGroupDefault extends CopixActionGroup {
	
	/**
	 * Objet de formulaire qui va servir à nos tests
	 * @var StClass
	 */
	private $_form ;
	
	/**
	 * Default module action
	 *
	 * @return CopixActionReturn 
	 */
	function processDefault (){
		_classInclude ('formtools|inputelementfactory');
		$ppo = _ppo ();
		if ($this->_form == null || _request ('form_id') != null) {
			CopixRequest::assert ('form_id');
			$record = _ioDao ('formobject')->get (_request ('form_id'));
			// @todo : Intégrer l'id dans l'objet formulaire 
			CopixSession::set ('form_id', _request ('form_id'));
			$this->_form = unserialize ($record->form);
			$this->_form->setSessionValues (null);
		}
		
		$ppo->form = $this->_form->showForm ($this->_form->getSessionValues (), (bool) _request ('error'));
		
		CopixSession::set ('formulaire', $this->_form, 'formtools');
		return _arPpo ($ppo, 'default.template.php');
	}
	
	function processValidForm (){
		$this->_form  = CopixSession::get ('formulaire', 'formtools');
		$this->_route = CopixSession::get ('route', 'formtools');
		$res = $this->_form->validateForm ($this->_form->setSessionValues ());

		if ($res === true)  {
			return _arRedirect ($this->_route->getUrlSuccess ());
		} else {
			return _arRedirect ($this->_route->getUrlFail ());
		}
		
	}
	
	/**
	 * 
	 */
	function processShowResult (){
		$this->_form  = CopixSession::get ('formulaire', 'formtools');
		$this->_route = CopixSession::get ('route', 'formtools');
		
		$arValues = $this->_form->getSessionValues ();
		
		$ppo = _ppo ();
		$ppo->form = $this->_form->showResult ($arValues);
		$ppo->applyingCondition = $this->_route->apply ($arValues);
		$ppo->return_url = $this->_route->getUrlForm ();
		
		return _arPpo ($ppo, 'default.template.php');
	}
}