<?php
/**
 * @package	webtools
 * @subpackage	rss
 * @author	Patrice Ferlet
 * @copyright CopixTeam
 * @link      http://copix.org
 * @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */
/**
 *
 */
class ListenerRSS extends CopixListener {
	public function processContent ($pEvent, $pEventResponse){
		$record = _record('rss_feeds');
		$record->rss_title = $pEvent->getParam('title');
		$record->rss_pubdate = date('YmdHis');
		$record->rss_desc = $pEvent->getParam('content');
		$record->rss_link = $pEvent->getParam('url');
		$record->rss_category = $pEvent->getParam('kind');
		_ioDAO('rss_feeds')->insert($record);
	}
}
?>