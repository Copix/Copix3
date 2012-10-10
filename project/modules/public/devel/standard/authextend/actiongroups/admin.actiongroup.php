<?php
/**
 * @package standard
 * @subpackage default
* @author		Croes Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Actions par défaut réalisées par le framework
 * @package standard
 * @subpackage default
 */
class ActionGroupAdmin extends CopixActionGroup {
	
    /**
    * 
    */
	public function processDefault () {
		return $this->processView ();
	}
	
    public function processView () {
        
        $ppo = new CopixPPO ();
        $ppo->extends = _ioDao ('dbuserextend')->findAll ();
        $ppo->editUrl = _url('authextend|admin|edit');
        
        return _arPPO($ppo, 'admin.view.tpl');
    }
    
    public function processEdit () {
    	
    	_classInclude('AuthExtend');
    	
    	$ppo = new CopixPPO ();
    	$ppo->arType = AuthExtend::$type;
    	
        return _arPPO($ppo, 'admin.edit.tpl');
    }
}
?>