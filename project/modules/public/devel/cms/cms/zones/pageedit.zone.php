<?php
/**
* @package	cms
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package cms
 * ZonePageEdit
 */
class ZonePageEdit extends CopixZone {
	function _createContent (& $toReturn){
		//Asks for the copixheading theme only if we're in "preview" or "content"
		if (in_array ($this->_params['kind'], array (1, 2))){
		   CopixEventNotifier::notify (new CopixEvent ('HeadingThemeRequest', array ('id'=>$this->_params['toEdit']->id_head)));
		}
		$tpl = new CopixTpl ();
		$tpl->assign ('toEdit', $this->_params['toEdit']);
		$workflow = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
		if (CopixConfig::get ('cms|easyWorkflow') == 1){
			$bestActionCaption = $workflow->getCaption($workflow->getBest($this->_params['toEdit']->id_head,'cms'));
		}else{
			$bestActionCaption = $workflow->getCaption($workflow->getNext($this->_params['toEdit']->id_head,'cms',$this->_params['toEdit']->status_cmsp));
		}
		$tpl->assign ('WFLBestActionCaption', $bestActionCaption);
		$tpl->assign ('showErrors', $this->_params['errors']);
		if ($this->_params['errors'] == 1){
   		   $tpl->assign ('arErrors', $this->_params['toEdit']->check ());
		}

		switch ($this->_params['kind']){
			case 0:
			$kind = "general";
			//Check if we have a given heading
			if ($this->_params['toEdit']->id_head === null){
				$heading->caption_head = CopixI18N::get ('copixheadings|headings.message.root');
				$heading->id_head      = null;
			}
			//retrive the heading if given, error message if the heading does not exist
			if ($this->_params['toEdit']->id_head){
				$dao = CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
				$heading = $dao->get ($this->_params['toEdit']->id_head);
			}
			$tpl->assign ('heading', $heading);
			break;

			case 1:
			   $kind = "content";
			   break;

			case 2:
			$kind = "preview";
			break;

			default:
			$kind = "general";
			break;
		}

		$tpl->assign ('kind', $kind);
	    $tpl->assign ('possibleKinds', CopixTpl::find ('cms','*.portlet.?tpl'));

		if ($kind == "preview"){
    		$parsedToEdit = $this->_params['toEdit']->getParsed (CMSParseContext::front);
		}elseif ($kind == "content") {
            $parsedToEdit = $this->_params['toEdit']->getParsed (CMSParseContext::edit);
		}else{
			$parsedToEdit = null;
		}

		//gets the model page if any
		$response = CopixEventNotifier::notify (new CopixEvent ('HeadingModelRequest',
		 array ('id'=>$this->_params['toEdit']->id_head)));
		$modelPage = null;
		foreach ($response->getResponse () as $element) {
			//we will override if two model pages are found.
			if ($element['model'] !== null){
				$modelPage = $element['model'];
			}
		}
		$toShow = '';
		if ($modelPage !== null  && ($modelPage->id_cmsp !== $this->_params['toEdit']->id_cmsp) && ($this->_params['toEdit']->hasPortletOfKind ('page') === false)){
			$modelPage->addPortletMessage ('ModelPage', $parsedToEdit);
			$toShow = $modelPage->getParsed (CMSParseContext::front);
		}else{
			$toShow = $parsedToEdit;
		}

		//assign the result to the template
		$tpl->assign ('parsedToEdit', $toShow);
		$toReturn = $tpl->fetch ('cms|page.edit.tpl');
		return true;
	}
}
?>
