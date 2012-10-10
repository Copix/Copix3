<?php
/**
* @package		standard 
 * @subpackage	generictools
* @author	Salleyron Julien
* @copyright 2001-2007 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
* @experimental
*/

/**
* @package		standard 
 * @subpackage	generictools
 */
class ActionGroupCopixForms extends CopixFormActionGroup {
    
    /**
     * méthode qui repond pour ajax
     * elle renvoi le code HTML d'un champ en fonction de son mode (et de son type)
     */
	public function processGetInput() {
		$ppo = new CopixPPO ();
		
	    $id = _request ('form_id');
	    $form = CopixFormFactory::get ($id);
	    $params['mode_'._request('form_id')] = _request('mode_'._request('form_id'),'view');
	    $ppo->MAIN = $form->getInput(_request('field'),$params);
	    return _arDirectPPO($ppo,'blank.tpl');
	}

	/**
	 * Actiongroup qui permet de gérer le check et le record des CopixForms
	 *
	 */
	public function processCheckRecord() {
	    $validUrl  = _request('onValid');
        $urlParams = array ();
   	    $urlParams['mode_'._request('form_id')] = 'view';
   	    $urlParams['error_'._request('form_id')]=false;
   	    $urlParams['form_id'] = _request('form_id');
   	    $urlParams['url'] = _request('url');
	    $form = CopixFormFactory::get (_request('form_id'));
	    $arPk = array();
	    try {
	        $form->doValid();
        	$arPk = $form->doRecord();
	    	return _arRedirect(_url($validUrl), array_merge($urlParams,$arPk));
	    } catch(CopixFormCheckException $e) {
            $urlParams['mode_'._request('form_id')]='edit';
	        $urlParams['error_'._request('form_id')]=true;
	        $form->setErrors ($e->getErrors());
	        return _arRedirect(_url(_request('url'), array_merge($urlParams,$arPk)));
	    }
	    
	}
	
	/**
	 * Actiongroup qui permet de gérer la suppression
	 *
	 */
	public function processDelete() {
	    $form = CopixFormFactory::get (_request('form_id'));
	    $form->delete(CopixRequest::asArray());
    	$url = _request('url');
    	return _arRedirect(_url($url));
	}   

	
	public function processNewForm () {
		$this->_form->save ();
		if ($this->_form->getOnSuccessUrl () != null) {
			return _arRedirect (_url ($this->_form->getOnSuccessUrl()));
		}
		return _arRedirect (_url ($this->_form->getFormUrl ()));
	}
}
?>