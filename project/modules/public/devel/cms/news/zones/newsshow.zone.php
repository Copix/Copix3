<?php
/**
* @package	cms
* @subpackage news
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package	cms
 * @subpackage news
 * ZoneNewsShow
 */
class ZoneNewsShow extends CopixZone {
	function _createContent (&$toReturn) {
      $tpl     = & new CopixTpl ();
      $daoNews = & CopixDAOFactory::getInstanceOf ('News');

      //on donne au template la liste des news.
      $tpl->assign ('toShow', $daoNews->get ($this->_params['id_news']));

      $toReturn = $tpl->fetch ('news.show.tpl');
      return true;
	}
}
?>