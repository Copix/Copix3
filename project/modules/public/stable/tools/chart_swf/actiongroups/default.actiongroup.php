<?php 
/**
* @package		tools 
 * @subpackage	chart_swf
* @author    Landry Benguigui
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* actiongroup par defaut du module chart_swf
* @package		tools 
 * @subpackage	chart_swf
*/
 class ActionGroupDefault extends CopixActionGroup {
 	/**
 	 * appel a la zone de test
 	 */
 	public function processDefault (){
		$tpl = new copixTpl();
		$tpl->assign("MAIN",CopixZone::Process("chapeland"));
		return new CopixActionReturn (CopixActionReturn::DISPLAY,$tpl);
 	}
 
 	/**
 	 * retourne les donnees du chart
 	 */
	public function processgetChartsDatas(){
		$cle = CopixRequest::get('cle');
		echo CopixSession::get ("charts|datas|$cle");
		CopixSession::set ("charts|datas|$cle", null);
		exit;
	}
 }
 ?>