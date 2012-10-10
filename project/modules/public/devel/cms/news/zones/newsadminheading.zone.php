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
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|ServicesCMSPage');
CopixClassesFactory::fileINclude ('news|NewsService');

/**
* @package	cms
* @subpackage news
* Enables the user to define the main model
*/
class ZoneNewsAdminHeading extends CopixZone {
   function _createContent (& $toReturn){
        $servicesHeading = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
        $tpl             = & new CopixTpl ();
        $workflow        = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $dao             = & CopixDAOFactory::getInstanceOf('News');

         //capability
        $contribEnabled  = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'news') >= PROFILE_CCV_WRITE;
        $publishEnabled  = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'news') >= PROFILE_CCV_PUBLISH;
        $validEnabled    = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'news') >= PROFILE_CCV_VALID;
        $moderateEnabled = CopixUserProfile::valueOf ($servicesHeading->getPath ($this->_params['id_head']), 'news') >= PROFILE_CCV_MODERATE;

        $tpl->assign ('contribEnabled' , $contribEnabled);
        $tpl->assign ('publishEnabled' , $publishEnabled);
        $tpl->assign ('validEnabled'   , $validEnabled);
        $tpl->assign ('moderateEnabled', $moderateEnabled);
        $tpl->assign ('id_head'        , $this->_params['id_head']);
        $tpl->assign ('pasteEnabled'   , CopixActionGroup::process ('news|NewsAdmin::canPaste', array ('id_head'=>$this->_params['id_head'])));

        if ($moderateEnabled) {
            $tpl->assign ('arNewsDraft'  , $dao->getNewsByStatus ($workflow->getDraft ()  ,$this->_params['id_head']));
            $tpl->assign ('arNewsTrash'  , $dao->getNewsByStatus ($workflow->getTrash ()  ,$this->_params['id_head']));
            $tpl->assign ('arNewsRefuse' , $dao->getNewsByStatus ($workflow->getRefuse () ,$this->_params['id_head']));
            $tpl->assign ('arNewsPropose', $dao->getNewsByStatus ($workflow->getPropose (),$this->_params['id_head']));
            $tpl->assign ('arNewsValid'  , $dao->getNewsByStatus ($workflow->getValid ()  ,$this->_params['id_head']));
        }elseif($publishEnabled) {
            $tpl->assign ('arNewsDraft'  , $dao->getNewsByUser ($workflow->getDraft ()  ,$this->_params['id_head']));
            $tpl->assign ('arNewsTrash'  , $dao->getNewsByStatusAuthor ($workflow->getTrash ()  ,$this->_params['id_head']));
            $tpl->assign ('arNewsRefuse' , $dao->getNewsByUser ($workflow->getRefuse () ,$this->_params['id_head']));
            $tpl->assign ('arNewsPropose', $dao->getNewsByStatus ($workflow->getPropose (),$this->_params['id_head']));
            $tpl->assign ('arNewsValid'  , $dao->getNewsByStatus ($workflow->getValid ()  ,$this->_params['id_head']));
        }elseif($validEnabled) {
            $tpl->assign ('arNewsDraft'  , $dao->getNewsByUser ($workflow->getDraft ()  ,$this->_params['id_head']));
            $tpl->assign ('arNewsTrash'  , $dao->getNewsByStatusAuthor ($workflow->getTrash ()  ,$this->_params['id_head']));
            $tpl->assign ('arNewsRefuse' , $dao->getNewsByUser ($workflow->getRefuse () ,$this->_params['id_head']));
            $tpl->assign ('arNewsPropose', $dao->getNewsByStatus ($workflow->getPropose (),$this->_params['id_head']));
            $tpl->assign ('arNewsValid'  , $dao->getNewsByUser ($workflow->getValid ()  ,$this->_params['id_head']));
        }elseif($contribEnabled) {
            $tpl->assign ('arNewsDraft'  , $dao->getNewsByUser ($workflow->getDraft ()  ,$this->_params['id_head']));
            $tpl->assign ('arNewsTrash'  , $dao->getNewsByStatusAuthor ($workflow->getTrash ()  ,$this->_params['id_head']));
            $tpl->assign ('arNewsRefuse' , $dao->getNewsByUser ($workflow->getRefuse () ,$this->_params['id_head']));
            $tpl->assign ('arNewsPropose', $dao->getNewsByUser ($workflow->getPropose (),$this->_params['id_head']));
            $tpl->assign ('arNewsValid'  , $dao->getNewsByUser ($workflow->getValid ()  ,$this->_params['id_head']));
        }else{
            $toReturn = '';
            return true;
        }

        $tpl->assign ('arNewsPublish', $dao->getNewsByStatus ($workflow->getPublish (),$this->_params['id_head']));

		  $toReturn = $tpl->fetch ('news.adminheading.tpl');
        return true;
   }
}
?>
