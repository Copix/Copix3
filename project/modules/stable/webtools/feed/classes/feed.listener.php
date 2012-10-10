<?php
/**
 * @package	webtools
 * @subpackage feed
 * @author Patrice Ferlet
 * @copyright CopixTeam
 * @link http://copix.org
 * @licence http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Sur l'événement Content, ajoute les infos pour faire un flux automatique
 */
class ListenerFeed extends CopixListener {
	/**
	 * Evenement qui indique qu'on ajoute un contenu
	 *
	 * @param CopixEvent $pEvent
	 * @param CopixEventResponse $pEventResponse
	 */
	public function processContent ($pEvent, $pEventResponse) {
		if (!CopixConfig::get ('feed|eventContentEnabled')) {
			return ;
		}

		$desc = $pEvent->getParam('summary');

		if (empty ($desc)) {
			$desc = $pEvent->getParam ('content');
		}

		$d = time ();
		$pdate = $pEvent->getParam ('date');
		if (!empty ($pdate)) {
			//we try to get a valid timestamp
			if (strlen ($pdate) == 14) {
				//probably a YYYYmmddHHiiss
				$d = CopixDateTime::yyyymmddhhiissToTimeStamp ($pdate);
			} else {
				$d = $pdate;
			}
		}

		$record = new DAORecordFeeds ();
		$record->feed_title = '<![CDATA[' . htmlentities ($pEvent->getParam ('title')).']]>';
		$record->feed_pubdate = $d;
		$record->feed_desc = '<![CDATA[' . htmlspecialchars_decode ($desc) . ']]>';
		$record->feed_content = '<![CDATA[' . htmlspecialchars_decode ($pEvent->getParam ('content')) . ']]>';
		$record->feed_link = $pEvent->getParam ('url');
		$record->feed_category = $pEvent->getParam ('kind');
		if ($pEvent->getParam ('author')) {
			$record->feed_author = $pEvent->getParam ('author');
		}
		_ioDAO ('feeds')->insert ($record);
	}

	/**
	 * Evenement avant une indexation de contenu
	 *
	 * @param CopixEvent $pEvent
	 * @param CopixEventResponse $pEventResponse
	 */
	public function processBeforeIndexing ($pEvent, $pEventResponse) {
		_ioDAO ('feeds')->deleteBy (_daoSP ());
	}
}