<?php
/**
 * @package	 	menu
 * @author	 	Steevan BARBOYON
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Zone qui affiche un commentaire
 * @param	comments 
 */
class ZoneMenu extends CopixZone {
    function _createContent (&$toReturn){
    	
		_classInclude ('ItemsServices');
    	$toReturn = ItemsServices::getItemsHTML($this->getParam ('idmenu', 0));
		
        return true;
    } // function
} // class
?>