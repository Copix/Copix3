<?php
/**
* @package	cms
* @subpackage cms_portlet_schedule
* @author	Bertrand Yan, Ferlet Patrice see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	cms
* @subpackage cms_portlet_schedule
* show the list of the known pages.
*/
class ZoneEditSchedule extends CopixZone {
	/**
	* Attends un objet de type textpage en paramètre.
	*/
	function _createContent (&$ToReturn){
	   $tpl = & new CopixTpl ();
	   $tpl->assign ('objSchedule', $this->_params['toEdit']);
	   
		//recherche de templates.

      switch ($this->_params['kind']){
         case 0:
            $kind = "general";
            break;

         case 1:
            $kind = "preview";
            break;

         default:
            $kind = "general";
            break;
      }
		$dao = & CopixDAOFactory::create ('copixheadings|copixheadings');
      if ($this->_params['toEdit']->id_head == 'root') {
         $heading = CopixI18N::get ('copixheadings|headings.message.root');
      }else{
         $heading = $dao->get ($this->_params['toEdit']->id_head);
         if ($heading!=null) $heading = $heading->caption_head;
      }
      $tpl->assign ('headingName', $heading);
		/*$tpl->assign ('possibleKinds', $finder->getList ());*/
		$possibleKinds = CopixTpl::find ('cms_portlet_schedule', '.portlet.?tpl');
		$possibleKinds = array_merge($possibleKinds, array('cms_portlet_schedule|normal.schedule.tpl'=>'normal'));
		$tpl->assign ('possibleKinds', $possibleKinds);
		
		$tpl->assign ('show', $this->_params['toEdit']->getParsed ("content"));
		$tpl->assign ('kind', $kind);
        $tpl->assign ('pageName', $this->_getNamePage($this->_params['toEdit']->id_page_subscribe));

		//appel du template.
		$ToReturn = $tpl->fetch ('cms_portlet_schedule|schedule.edit.tpl');
		return true;
	}
    /**
    *Récupère le nom de la page d'identifiant donné
    */
    function _getNamePage ($id) {
        $dao = & CopixDAOFactory::getInstanceOf ('cms|cmspage');
        $sp  = & CopixDAOFactory::createSearchParams();

        $sp->addCondition ('publicid_cmsp', '=', $id);
        $sp->orderBy (array('version_cmsp', 'DESC'));

        $data =  $dao->findBy( $sp ) ;
        if (count ($data)){
           return $data[0]->title_cmsp ;
        }
        return '';
    }	
}
?>
