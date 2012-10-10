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
class ActionGroupCopixForms extends CopixActionGroup {
    /**
     * méthode qui lance la validation des champs et qui lance l'enregistrement
     */
    public function processValidForm () {
	    $id = CopixRequest::get ('form_id');
	    $url = CopixUrl::get (CopixRequest::get ('url_param'));
	    $form = CopixFormFactory::get ($id);
	    $form->doRecord();
	    return _arRedirect($url);
	}
	
    /**
     * méthode qui repond pour ajax
     * elle renvoi le code HTML d'un champ en fonction de son mode (et de son type)
     */
	public function processGetInput() {
	    $id = CopixRequest::get ('form_id');
	    $form = CopixFormFactory::get ($id);
	    $params['mode_'.CopixRequest::get('form_id')] = CopixRequest::get('mode_'.CopixRequest::get('form_id'),'view');
	    $ppo->MAIN = $form->getInput(CopixRequest::get('field'),$params);
	    return _arDirectPPO($ppo,'blank.tpl');
	}

	/**
	 * Actiongroup qui permet de gérer le check et le record des CopixForms
	 *
	 */
	public function processCheckRecord() {
	    $validUrl  = CopixRequest::get('validUrl');
        $urlParams = array();
   	    $urlParams['mode_'.CopixRequest::get('form_id')]='view';
	    $form = CopixFormFactory::get (CopixRequest::get('form_id'));
	    $arPk = array();
	    try {
	        $arPk = $form->doRecord();
	    } catch(CopixFormException $e) {
	        $urlParams['mode_'.CopixRequest::get('form_id')]='edit';
	        $urlParams['error_'.CopixRequest::get('form_id')]='true';
	    }
	    if ($validUrl !== null && !$urlParams['error_'.CopixRequest::get('form_id')]) {
    	    return _arRedirect(CopixUrl::get($validUrl));	        
	    } else {
    	    return _arRedirect(CopixUrl::get(CopixRequest::get('url'), array_merge($urlParams,$arPk)));
	    }
	}
	
	public function processDelete() {
	    $form = CopixFormFactory::get (CopixRequest::get('form_id'));
	    $form->delete(CopixRequest::asArray());
    	$url = CopixRequest::get('url');
    	return _arRedirect(CopixUrl::get($url));
	}   
	
}
?>