<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Selvi ARIK
 */

/**
 * Actions ajax dans le processus d'édition d'une portlet
 * 
 * @package cms
 * @subpackage cms_rss_reader
 */
class ActionGroupAjax extends CopixActionGroup {

	public function processGetFeed () {
		$ppo = new CopixPPO ();
		$toReturn = '';
		$portletElement = null;
		$portlet = $this->_getEditedElement ();
        $portlet->setEtat (Portlet::UPDATED);
		$position = _request ('position');
		$aFeeds = $portlet->getOption ('feeds');

        if (empty ($aFeeds)) {
            $aFeeds = array ();
        }
        // commenté car trop long en cas de problème
        /*else {
            foreach ($aFeeds as $key => $feed) {
                if (!FeedReader::test($feed)) {
                    $toReturn .= 'Le flux n°'.$key.' est incorrect.<br />';
                }
            }
        }*/
		$aFeeds = _request ('url_feed_'._request('portletId'));
		$portlet->setOption ('feeds', $aFeeds);
		
		$ppo->MAIN = $toReturn;
		return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl');
	}
    public function processDeleteFeed () {
		$ppo = new CopixPPO ();
		$toReturn = 'Vide';
		$portletElement = null;
		$portlet = $this->_getEditedElement ();
        $portlet->setEtat (Portlet::UPDATED);
		$position = _request ('position');
		$aFeeds = $portlet->getOption ('feeds');
        if (empty ($aFeeds)) {
            $aFeeds = array ();
        }
		$feed = _request ('url_feed');

		$aFeeds[$position] = $feed;
        $portlet->setOption ('feeds', $aFeeds);
		$toReturn = CopixZone::process ('cms_rss_reader|feedformview', array('feed' => $feed));

		$ppo->MAIN = $toReturn;
		return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl');
	}
	
	/**
	 * Retourne la portlet en cours d'édition
	 *
	 * @return Page
	 */
	protected function _getEditedElement (){
		CopixRequest::assert ('editId');
		$portlet = CopixSession::get ('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null) {
			$portlet = CopixSession::get ('portlet|edit|record', _request ('editId'));
		}
		if (!$portlet) {
			throw new CopixException ('Portlet en cours de modification perdu');
		}
		return $portlet;
	}
}