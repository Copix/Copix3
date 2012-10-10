<?php
/**
* @package	copix
* @subpackage template
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link		http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.htmlGNU Leser General Public Licence, see LICENCE file
*/
class ActionGroupTheme extends CopixActionGroup {
	/**
	* Création du theme
	*/
	function doCreate (){
		$record = & CopixDAOFactory::createRecord ('copixtemplate_theme');
		$this->_setSession ($record);

		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('theme|edit'));
	}

	/**
	* Préparation de modification
	*/
	function doPrepareEdit (){
		$dao    =  & CopixDAOFactory::getInstanceOf ('copixtemplate_theme');
		if (($record = $dao->get (CopixRequest::get ('id_ctpt'))) === null){
			return CopixActionGroup::process ('generictools|Messages::getError',
			   array ('message'=>CopixI18N::get ('template.error.doNotExists'),
			   'back'   =>CopixUrl::get ('template||')));
		}

		$this->_setSession ($record);
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('theme|edit'));
	}

	/**
	* Validation de l'élément
	*/
	function doValid (){
		if (($edited = $this->_getSession ()) === false){
			return CopixActionGroup::process ('generictools|Messages::getError',
			   array ('message'=>CopixI18N::get ('template.error.editedElementHasBeenLost'),
			   'back'   =>CopixUrl::get ('template||')));
		}
		$this->_validFromForm ($edited);
		$this->_setSession ($edited);

		$dao = & CopixDAOFactory::getInstanceOf ('copixtemplate_theme');
		if ($dao->check ($edited) === true){
			if ($edited->id_ctpt === null){
               $method = 'insert';
			}else{
				$method = 'update';
			}
			if ($dao->$method ($edited)){
				if (!empty ($edited->import_from)){
					$this->_importTheme ($edited->import_from, $edited->id_ctpt);
				}
				$this->_setSession (null);
				return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('|', array ('selectedTheme'=>$edited->id_ctpt)));
			}else{
				return CopixActionGroup::process ('generictools|Messages::getError',
				   array ('message'=>CopixI18N::get ('template.error.editedElementCannotBeSaved'),
				   'back'   =>CopixUrl::get ('theme|edit')));
			}
		}else{
			return CopixActionGroup::process ('Theme::getEdit', array ('showErrors'=>1));
		}
	}

	/**
	* Ecran de modification.
	*/
	function getEdit (){
		if (($edited = $this->_getSession ()) === false){
			return CopixActionGroup::process ('generictools|Messages::getError',
			   array ('message'=>CopixI18N::get ('template.error.editedElementHasBeenLost'),
			   'back'   =>CopixUrl::get ('template||')));
		}
		
		$tpl = & new CopixTpl ();
		if ($edited->id_ctpt !== null){
		   $tpl->assign ('TITLE_PAGE', CopixI18N::get ('template.titlePage.updateTheme'));
		}else{
			$tpl->assign ('TITLE_PAGE', CopixI18N::get ('template.titlePage.createTheme'));
		}
		$tpl->assign ('MAIN', CopixZone::process ('ThemeEdit', array ('edited'=>$edited, 'showErrors'=>CopixRequest::get ('showErrors', false, true))));
		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}

	/**
	* Supression d'un thème donné
	*/
	function doDelete (){
		//On vérifie si le thème existe
		$dao    =  & CopixDAOFactory::getInstanceOf ('copixtemplate_theme');
		if (($record = $dao->get (CopixRequest::get ('id_ctpt'))) === null){
			return CopixActionGroup::process ('generictools|Messages::getError',
			   array ('message'=>CopixI18N::get ('template.error.doNotExists'),
			   'back'   =>CopixUrl::get ('template||')));
		}else{
			if (CopixRequest::get ('confirm', false, true) != 1){
				return CopixActionGroup::process ('generictools|Messages::getConfirm', array ('message'=>CopixI18N::get ('template.messages.confirmThemeDeletion', $record->caption_ctpt), 
			                                                                       'confirm'=>CopixUrl::get ('theme|delete', array ('confirm'=>1, 'id_ctpt'=>CopixRequest::get ('id_ctpt'))),
				                                                                       'back'=>CopixUrl::get ('|')));
			}
		}

		//Tous les contrôles sont passés, on supprime l'élément.
		CopixDB::begin ();
		try {
		$ct = CopixDB::getConnection ();
		$daoTemplates = CopixDAOFactory::getInstanceOf ('copixtemplate');
		if (   !$daoTemplates->deleteByTheme (CopixRequest::get ('id_ctpt'))
		    || !$dao->delete (CopixRequest::get ('id_ctpt'))){
		    $ct->rollback ();
			//Echec lors de la suppression
            return CopixActionGroup::process ('generictools|Messages::getError',
			   array ('message'=>CopixI18N::get ('template.error.cannotDeleteTheme'),
			   'back'   =>CopixUrl::get ('template||')));
		}
		CopixDB::commit ();
		}catch (CopixDBException $e){
			CopixDB::rollback ();
		}
        //TODO agir en cas d'échec de la transaction		
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get('|'));
	}

	/**
	* Validate an element from its form
	*/
	function _validFromForm (& $toEdit){
		if (($captionValue = CopixRequest::get ('caption_ctpt', null, true)) !== null){
		   $toEdit->caption_ctpt = $captionValue;
		}
		if (($toImport = CopixRequest::get ('import_theme_from_harddrive', null, false)) !== null){
			$templateScanner = CopixClassesFactory::create ('TemplateScanner');
			$arExistingThemes = $templateScanner->getNonInstalledThemes ();
			$themeToImportName = isset ($arExistingThemes[$toImport]) ? $arExistingThemes[$toImport] : null;
			//on regarde si un nom de thème à été donné, sinon on prends celui du thème à importer
			if (empty ($captionValue)){
               $toEdit->caption_ctpt = $themeToImportName;
			}
			$toEdit->import_from  = $themeToImportName;
		}
	}

	/**
	* Place l'élément à éditer en session.
	*/
	function _setSession ($record){
		$_SESSION['MODULE_TEMPLATE']['THEME']['EDITED'] = $record === null ? null : serialize ($record);
	}

	/**
	* récupère l'élément à éditer depuis la session.
	*/
	function _getSession (){
		CopixDAOFactory::fileInclude('copixtemplate_theme');
		if (isset ($_SESSION['MODULE_TEMPLATE']['THEME']['EDITED'])){
		   return unserialize ($_SESSION['MODULE_TEMPLATE']['THEME']['EDITED']);
		}
		return false;
	}
	
	/**
	* Importe un theme graphique
	*/
	function _importTheme ($themeName, $themeId){
    	//récupération de la liste des templates standards
		$templateScanner = CopixClassesFactory::create ('templatescanner');
    	$templateList = $templateScanner->scanStandardTemplates ();
    	
    	//création d'un enregistrement qui va nous servir a remplir la base.
    	$dao    = & CopixDAOfactory::getInstanceOf ('copixtemplate');
    	$record = & CopixDAOfactory::createRecord ('copixtemplate');
    	foreach ($templateList as $qualifier=>$templates){
    		foreach ($templates as $templateId=>$templateName){
	    		$record->setQualifier ($templateId);
	    		$record->caption_ctpl = $templateName;
	    		$record->id_ctpt      = $themeName;
	    		if (file_exists($fileName = $record->getTemplatePath ())){
	    			$record->content_ctpl = file_get_contents($fileName);
	    			$record->id_ctpt = $themeId;
	                $dao->insert ($record);
	    		}
    		}
    	}
    	rename (COPIX_VAR_PATH.'data/templates/'.$themeName, COPIX_VAR_PATH.'data/templates/'.$themeId);
	}
}
?>