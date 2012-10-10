<?php
/**
 * @package	webtools
 * @subpackage	wiki
 * @author	Patrice Ferlet
 * @copyright CopixTeam
 * @link      http://copix.org
 * @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion des évènements
 * @package	webtools
 * @subpackage	wiki
 */
class ListenerWiki extends CopixListener {
	public function processListContent ($pEvent, $pEventResponse){
		//on liste le contenu du wiki
		$arResults = _doQuery ('select distinct title_wiki from wikipages');
		foreach ($arResults as $key=>$resultTitle){
			$urls[$key] = _url ('wiki|default|show', array ('title'=>$resultTitle->title_wiki));
		}
		$pEventResponse->add (array ('url'=>$urls));
	} 
}
?>