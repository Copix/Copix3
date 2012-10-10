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
 	 * Action par défaut
 	 *
 	 * @return function processGetChartsDatas
 	 */
 	public function processDefault (){
 		return $this->processgetChartsDatas();
 	}
 
 	/**
 	 * Retourne les données du chart
 	 *
 	 */
	public function processGetChartsDatas(){
		$cle = CopixRequest::get('cle');
		echo CopixSession::get ("charts|datas|$cle");
		CopixSession::set ("charts|datas|$cle", null);
		return _arNone ();
	}
 }
 ?>