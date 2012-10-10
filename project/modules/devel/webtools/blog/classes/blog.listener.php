<?php
/**
* @package	webtools
* @subpackage	blog
* @author	Patrice Ferlet
* @copyright CopixTeam
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * 
 */
class Listenerblog extends CopixListener {
	public function processListContent ($pEvent, $pEventResponse){
		//on liste le contenu du wiki
		$arResults = _ioDao('blog_ticket')->findAll();
		_ioClass('blog|blogservices')->cutDates($arResults);
		foreach ($arResults as $key=>$resultTitle){
			$urls[$key] = _url ('blog|default|showticket', array ('title'=>$resultTitle->title_blog,
																		  'day'=>$resultTitle->day,
																		  'month'=>$resultTitle->month,
																		  'year'=>$resultTitle->year,
																		  'notify'=>1
																		  )
									 );
		}
		$pEventResponse->add (array ('url'=>$urls));
	} 
}
?>