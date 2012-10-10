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
		return $this->processEdit ();
	}
	
    public function processEdit () {
        
        $ppo = new CopixPPO ();
        $ppo->extends = _ioDao ('dbuserextend')->findAll ();
        
        var_dump($ppo);
        
        return _arPPO($ppo, 'admin.edit.tpl');
    }
}
?>