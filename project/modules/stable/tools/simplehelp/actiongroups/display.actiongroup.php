<?php
/**
 * @package		simplehelp
 * @author		Audrey Vassal, Brice Favre
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		tools 
 * @subpackage	simplehelp 
 */
class ActionGroupDisplay extends CopixActionGroup {
    /**
     * Fonction qui est appellée pour afficher l'aide
     */
    public function processDefault (){
        if(_request ('id_sh', null) === null){
            return CopixActionGroup::process ('generictools|Messages::getError',
            array ('message'=>_i18n ('simplehelp.error.missingParameters'),
            'back'=>_url('simplehelp|admin|listAide')));
        }
       
        $aide = _ioDAO ('simplehelp')->get (_request ('id_sh', null));
        
        $ppo = new CopixPPO (array ('TITLE_PAGE'=>$aide->title_sh));
		$ppo->MAIN = CopixZone::process ('ShowAide', array('id_sh'=>CopixRequest::get ('id_sh', null)));
		return _arDirectPPO ($ppo, 'popup.tpl');

    }
}
?>