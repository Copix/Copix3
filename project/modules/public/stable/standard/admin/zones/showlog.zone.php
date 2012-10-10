<?php
/**
 * @package standard
 * @subpackage admin 
* 
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Affichage des logs
 * @package standard
 * @subpackage admin 
 * 
 */
class ZoneShowLog extends CopixZone{
	/**
	 * Création du contenu
	 * @param 	string	$toReturn	le contenu		 
	 */
    function _createContent (& $toReturn){        
    	$tpl    = new CopixTpl ();
    	$profil = $this->getParam  ('profil');
        $nbitems = $this->getParam  ('nbitems');
    	        		
	   	$niveau = CopixLog::getLog ($profil, $nbitems);
	   	$tpl->assign ('logs', $niveau);
	   	$tpl->assign ('profil', $profil);
	   	$tpl->assign ('nbitems', $nbitems);
        
	   	//$tpl->assign ('page', $page);
	   	$toReturn = $tpl->fetch ('logs.list.php');
        return true;
    }
}
?>