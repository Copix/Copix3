<?php
/**
 * MooCMS Moobox Exemple
 * Display an Hello World
 * 
 * @package MooCMS
 * @subpackage MooBox
 * @subpackage exemple
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 
 *
 */
class MooboxExample extends MooBox {
	
	/**
	 * Return Hello World
	 *
	 * @return string to display
	 */
	public function getContent(){
		return "<h2>Example !</h2> Hello World !!!";
	}
	
}
?>