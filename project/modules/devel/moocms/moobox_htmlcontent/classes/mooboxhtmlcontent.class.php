<?php
/**
 * MooBox which use htmlcontent edited by FCKEditor
 * 
 * @package MooCMS
 * @subpackage MooBox
 * @subpackage htmlcontent
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 *
 */
class MooboxHTMLContent extends MooBox {
	
	/**
	 * Return the HTML content
	 *
	 * @param array $params
	 * @return string to display
	 */
	public function getContent($pParams){
		return "<h2>".$pParams["title"]."</h2>".$pParams['htmlcontent'];
	}
	
	/**
	 * Get Edit display
	 *
	 * @return string edit page
	 */
	public function getEdit(){
		$tpl= new CopixTpl();
		return $tpl->fetch("moobox_htmlcontent|edit.php");
	}
	
}
?>