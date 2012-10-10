<?php
/**
 * @package		standard 
 * @subpackage	generictools
* @author	Salleyron Julien
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
* @experimental
*/

/**
 * Actiongroup pour gérer les envois de CopixList
 * @package		standard 
 * @subpackage	generictools
 */
class ActionGroupCopixList extends CopixActionGroup {
	public function processGetTable () {
	    $ppo = new CopixPPO ();
	    // Si c'est pour de l'ajax
	    if (CopixRequest::get('url') == null) {
    	    try {
    	        //Recup les données
    			$id    = CopixRequest::get ('table_id');
    			$table = CopixListFactory::get ($id);
    			if (CopixRequest::get('submit') !== 'false') {
    			    $table->getFromRequest ();
    			}
    			//On génère le HTML
    			$ppo->MAIN = $table->generateTable ();
    	    } catch (Exception $e) {
    	        //En cas d'erreur en etant en ajax, on renvoi l'erreur
    	        $ppo->MAIN = $e->getMessage();
                return _arDirectPPO ($ppo, 'blank.tpl');
    	    }
		    return _arDirectPPO ($ppo, 'blank.tpl');
		//Si c'est pas en ajax
	    } else {
	        //On récup les données
	        $id    = CopixRequest::get ('table_id');
   			$table = CopixListFactory::get ($id);
   			if (CopixRequest::get('submit') !== 'false') {
    		    $table->getFromRequest ();
    		}
   			//Et on redirige
   			return _arRedirect(_url(CopixRequest::get('url')));
	    }
	}
}