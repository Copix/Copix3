<?php
/**
 * @package		simplehelp
 * @author		Audrey Vassal
 * @copyright	2001-2007 CopixTeam
 * @link		http://copix.org
 * @licence		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Opérations d'affichage pour l'aide
 */
class ActionGroupDisplay extends CopixActionGroup {
    /**
     * Fonction qui est appellée pour afficher l'aide
     */
    function processDefault (){
        if(_request ('id_sh', null) === null){
            return CopixActionGroup::process ('generictools|Messages::getError',
            array ('message'=>_i18n ('simplehelp.error.missingParameters'),
            'back'=>_url('simplehelp|admin|listAide')));
        }
        $tpl = new CopixTpl ();
        
        $aide = _ioDAO ('simplehelp')->get (_request ('id_sh', null));
        $tpl->assign ('TITLE_PAGE', $aide->title_sh);
        $tpl->assign ('MAIN'      , $aide->content_sh);
        return new CopixActionReturn (CopixActionReturn::DISPLAY_IN, $tpl, 'popup.tpl');
    }
}
?>