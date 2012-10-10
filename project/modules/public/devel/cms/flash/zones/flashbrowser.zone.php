<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
require_once (COPIX_UTILS_PATH.'CopixPager.class.php');

/**
* @package	cms
* @subpackage pictures
* Zone d'affichage du browser d'images.
*/
class ZoneFlashBrowser extends CopixZone {
    function _createContent (&$toReturn){
        $tpl = new CopixTpl ();

        //Creation des DAO
        $daoFlash         = CopixDAOFactory::getInstanceOf ('flash');
		$id_head = null;
        $arFlash = $daoFlash->findAllLastVersionByHeading ($id_head);        
        $tpl->assign('arFlash',$arFlash);
        $tpl->assign ('id_head'      , $this->_params['id_head']);

        $toReturn = $tpl->fetch ('browser.tpl');
        return true;
    }

}
?>