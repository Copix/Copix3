<?php
/**
 * @package		template
 * @author		Croës Gérald
 * @copyright	2001-2006 CopixTeam
 * @link		http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.htmlGNU Leser General Public Licence, see LICENCE file
 */
 
 /**
  * @ignore
  */
CopixClassesFactory::fileInclude ('template|copixtemplateeditor');

/**
 * Opérations d'administration sur les templates
 */
class ActionGroupAdmin extends CopixActionGroup {
	/**
    * Place un template à modifier en session
    */
	function doPrepareEdit () {
		$dao = & CopixDAOFactory::getInstanceOf ('copixtemplate');
		if (($record = $dao->get (CopixRequest::get ('id_ctpl'))) === false) {
			return CopixActionGroup::process ('generictools|Messages::getError',
			array ('message' => CopixI18N::get ('template.error.doNotExists'),
			'back' => CopixUrl::get ('template||')));
		}
		
		$DAORecord = CopixDAOFactory::createRecord ('copixtemplate');
		$DAORecord->initFromDBObject ($record);
		$record = $DAORecord;

		// generating an id for the edition process.
		$editId = uniqid ('template_');
		if ($record->qualifier_ctpl === null) {
			$record->dynamicTemplate = 1;
		} else {
			$record->dynamicTemplate = 0;
		}
		$record->mainTemplateUpdate = 0;
		$this->_setSessionTemplate($record, $editId);

		//création de l'éditeur & mise en session
		$editor = & new CopixTemplateEditor ();
		$editor->loadFromString ($record->generated_ctpl);
		$this->_setSessionTemplateEditor ($editor, $editId);

		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('admin|edit', array ('editId' => $editId)));
	}

	/**
    * Place un template à créer en session
    */
	function doCreate() {
		$template = CopixDAOFactory::createRecord ('copixtemplate');
		// On préparamètre l'objet en fonction du contexte dans lequel on a demandé la création de l'élément.
		$template->modulequalifier_ctpl = CopixRequest::get ('modulequalifier_ctpl', null, true);
		$template->id_ctpt = CopixRequest::get ('theme', null, true);
		// On regarde si l'on veut créer un tout nouveau template ou s'il s'agit de modifier un template standard
		$template->dynamicTemplate = CopixRequest::get ('dynamicTemplate', 0, true);
		$template->mainTemplateUpdate = CopixRequest::get ('dynamicTemplate', 0, true);
		if (CopixRequest::get ('dynamicTemplate', 0, true) !== 0) {
			$template->id_ctpt = null;
		}
		// generating an id for the edition process.
		$editId = uniqid ('template_');
		$this->_setSessionTemplate($template, $editId);

		//création de l'éditeur & mise en session
		$editor = & new CopixTemplateEditor ();
		$editor->loadFromString (null);
		$this->_setSessionTemplateEditor ($editor, $editId);

		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('admin|edit', array ('editId' => $editId)));
	}

	/**
    * Formulaire de modification du template actuellement en session
    */
	function getEdit () {
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true) {
			return $checkResult;
		}
		$selectedTab = CopixRequest::get ('selectedTab', 0, true);

		$tpl = new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', $edited->id_ctpl === null ? CopixI18N::get ('template.titlePage.createTemplate') : CopixI18N::get ('template.titlePage.updateTemplate'));
		$tpl->assign ('MAIN', CopixZone::process ('EditTemplate', array ('edited' => $edited, 'editId' => $editId, 'selectedTab' => $selectedTab, 'showErrors' => CopixRequest::get ('showErrors', false, true))));
		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}

	/**
    * Validation du formulaire, retourne sur la page d'édition
    */
	function doValidForm () {
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true) {
			return $checkResult;
		}

		$templateScanner = &CopixClassesFactory::create ('TemplateScanner');
		if (($templateId = CopixRequest::get ('standardTemplateImport', null, true)) !== null) {
			if (($content = $templateScanner->getTemplateContent($templateId)) !== null) {
				$edited->content_ctpl = $content;
			}
		}

		if (($templateId = CopixRequest::get ('nonStandardTemplateImport', null, true)) !== null) {
			$daoTemplates = &CopixDAOFactory::getInstanceOf ('copixtemplate');
			$template = null;
			$template = $daoTemplates->get($templateId);
			$edited->content_ctpl = $template->content_ctpl;
		}
		// Validation des données du formulaire et remise en session.
		$this->_validFromForm ($edited);
		$this->_setSessionTemplate($edited, $editId);
		// retourne sur la page d'édition
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('admin|edit', array ('editId' => $editId, 'selectedTab' => CopixRequest::get ('selectedTab', 0, true))));
	}

	/**
    * On valide le template en cours de modification et le sauvegarde
    */
	function doValid () {
		// Checking if we have an element
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true) {
			return $checkResult;
		}

		// Validating datas from the form
		$this->_validFromForm ($edited);
		$this->_setSessionTemplate ($edited, $editId);

		$dao = CopixDAOFactory::getInstanceOf ('copixtemplate');
		if (($checkResult = $dao->check ($edited)) !== true) {
			return CopixActionGroup::process ('Admin::getEdit', array ('showErrors' => 1, 'editId' => $editId));
		}
		// Trying to save the element
		$methodName = ($edited->id_ctpl === null) ? 'insert' : 'update';
		if ($dao->$methodName ($edited)) {
			if ($methodName == "insert" && $edited->publicid_ctpl == null) {
				$edited->publicid_ctpl = $edited->id_ctpl;
				$dao->update ($edited);
			}
			$edited->writeOnHardDrive ();
			$this->_setSessionTemplate (null, $editId);
			return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('|', array ('selectedTheme' => $edited->id_ctpt, 'selectedQualifier' => $edited->modulequalifier_ctpl)));
		} else {
			return CopixActionGroup::process ('generictools|Messages::getError',
			array ('message' => CopixI18N::get ('template.error.editedElementCannotBeSaved'),
			'back' => CopixUrl::get ('admin|edit', array ('editId' => $editId))));
		}
	}

	/**
    * Supprime le template demandé (id_ctpl)
    */
	function doDelete() {
		// On vérifie si le template existe
		$dao = & CopixDAOFactory::getInstanceOf ('copixtemplate');
		if (($record = $dao->get (CopixRequest::get ('id_ctpl'))) === null) {
			return CopixActionGroup::process ('generictools|Messages::getError',
			array ('message' => CopixI18N::get ('template.error.doNotExists'),
			'back' => CopixUrl::get ('template||')));
		} else {
			if (CopixRequest::get ('confirm', false, true) != 1) {
				return CopixActionGroup::process ('generictools|Messages::getConfirm', array ('message' => CopixI18N::get ('template.messages.confirmTemplateDeletion', array ($record->qualifier_ctpl)),
				'confirm' => CopixUrl::get ('admin|delete', array ('confirm' => 1, 'id_ctpl' => CopixRequest::get ('id_ctpl'))),
				'back' => CopixUrl::get ('|')));
			}
		}
		// Tous les contrôles sont passés, on supprime l'élément.
		if (! $dao->delete (CopixRequest::get ('id_ctpl'))) {
			// Echec lors de la suppression
			return CopixActionGroup::process ('generictools|Messages::getError',
			array ('message' => CopixI18N::get ('template.error.cannotDeleteTemplate'),
			'back' => CopixUrl::get ('template||')));
		}
		unlink ($record->getTemplatePath ());
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get('|'));
	}

	/**
    * On parcours la liste des champs de formulaire attendu et on valide les données
    * @access private.
    */
	function _validFromForm (&$edited) {
		// A t on donné un template standard à remplacer ?
		$sourceTemplate = CopixRequest::get ('sourceTemplate', null, false);

		$dao = &CopixDAOFactory::getInstanceOf ('copixtemplate');
		$criteres = CopixDAOFactory::createSearchParams();

		if (($qualifier = CopixRequest::get ('standardTemplate', null, false)) !== null) {
			if ($qualifier != '') {
				$edited->setQualifier ($qualifier);
				$criteres->addCondition('qualifier_ctpl', '=', $qualifier);
				foreach($dao->findby($criteres) as $elem) {
					$sourceTemplate = $elem->publicid_ctpl;
				}
			} else {
				$edited->setQualifier (null);
			}
		}
		// A t on donné un template non standard à remplacer ?
		if (isset($sourceTemplate)) {
			$edited->publicid_ctpl = $sourceTemplate;
		}
		// die($edited->publicid_ctpl);
		// les autres données classiques
		$toCheck = array ('caption_ctpl', 'id_ctpt', 'content_ctpl');
		foreach ($toCheck as $element) {
			if (($requestValue = CopixRequest::get ($element, null, true)) !== null) {
				$edited->$element = empty ($requestValue) ? null : $requestValue;
			}
		}

		if (($moduleQualifier = CopixRequest::get ('modulequalifier_ctpl', null, true)) !== null) {
			$edited->modulequalifier_ctpl = $moduleQualifier;
		}
	}

	/**
    * Récupère le template en cours de modification, depuis la bonne session d'édition.
    * @access private.
    */
	function _getSessionTemplate ($pId) {
		CopixDAOFactory::fileInclude ('copixtemplate');
		if (!isset ($_SESSION['MODULE_TEMPLATE']['TEMPLATE']['EDITED'][$pId])) {
			return null;
		}
		if (($toReturn = unserialize ($_SESSION['MODULE_TEMPLATE']['TEMPLATE']['EDITED'][$pId])) === false){
			return null;
		}
		return $toReturn;
	}

	/**
    * sets the current edited template.
    * @access private.
    */
	function _setSessionTemplate ($pToSet, $pId) {
		$_SESSION['MODULE_TEMPLATE']['TEMPLATE']['EDITED'][$pId] = serialize ($pToSet);
	}

	/**
	* Défini le générateur de template à placer dans la session
	* @param CopixTemplateEditor $pToSet l'éditeur de template à placer en session
	* @param string $pId 
	* @access private
	*/
	function _setSessionTemplateEditor ($pToSet, $pId){
		$_SESSION['MODULE_TEMPLATE']['TEMPLATE_EDITOR'][$pId] = serialize ($pToSet);
	}

	/**
	* Récupère l'éditeur de template depuis la session
	* @param string $pId l'identifiant unique de modification
	* @return	CopixTemplateEditor si trouvé, null sinon
	*/
	function _getSessionTemplateEditor ($pId){
		CopixClassesFactory::fileInclude ('copixtemplateeditor');
		//Rien dans la session
		if (!isset ($_SESSION['MODULE_TEMPLATE']['TEMPLATE_EDITOR'][$pId])) {
			return null;
		}
		//Impossible à désérialiser ?
		if (($toReturn = unserialize ($_SESSION['MODULE_TEMPLATE']['TEMPLATE_EDITOR'][$pId])) === false){
			return null;
		}
		return $toReturn;
	}

	/**
    * Check if there is a given editedId and a matching edited element.
    * @param CopixTemplateElement $pEdited Le template en cours de modification
    * @param string $pEditId L'identifiant d'édition (pour pouvoir éditer plusieurs éléments en même temps)
    * @return CopixActionReturn si problème, true si OK
    */
	function _checkEditedElement (& $pEdited, & $pEditor, & $pEditId) {
		// retrieve the edit id.
		if (($pEditId = CopixRequest::get ('editId', null, true)) === null) {
			return CopixActionGroup::process ('generictools|Messages::getError',
			array ('message' => CopixI18N::get ('template.error.noGivenId'),
			'back' => CopixUrl::get ('template||')));
		}
		// Retrieve the template
		if (($pEdited = $this->_getSessionTemplate ($pEditId)) === null) {
			return CopixActionGroup::process ('generictools|Messages::getError',
			array ('message' => CopixI18N::get ('template.error.editedElementHasBeenLost'),
			'back' => CopixUrl::get ('template||')));
		}

		//Retrive the template editor
		if (($pEditor = $this->_getSessionTemplateEditor ($pEditId)) === null){
			return CopixActionGroup::process ('generictools|Messages::getError',
			array ('message' => CopixI18N::get ('template.error.editedElementHasBeenLost'),
			'back' => CopixUrl::get ('template||')));
		}

		return true;
	}

	/**
    * Page de sélection d'un template standard défini en dur dans le framework
    * sauvegarde le formulaire en session
    */
	function getSelectStandardTemplate () {
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true) {
			return $checkResult;
		}

		$templateScanner = &CopixClassesFactory::create ('TemplateScanner');
		if (($templateId = CopixRequest::get ('standardTemplateImport', null, true)) !== null) {
			if (($content = $templateScanner->getTemplateContent($templateId)) !== null) {
				$edited->content_ctpl = $content;
			}
		}
		$this->_validFromForm($edited);
		$this->_setSessionTemplate($edited, $editId);

		$tpl = & new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('template.titlePage.selectStandardTemplate'));
		$tpl->assign ('MAIN', CopixZone::process ('SelectStandardTemplate', array ('editId' => $editId)));
		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}

	/**
    * Annulation des modifications sur un template donné
    */
	function doCancelEdit () {
		if (($editId = CopixRequest::get ('editId', null, true)) !== null) {
			$this->_setSessionTemplate(null, $editId);
		}
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('|'));
	}

	/**
    * affiche le contenu d'un template actuel pour l'importer dans la création des templates dynamiques
    */
	function getImportStandardTemplate () {
		// Checking if we have an element
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true) {
			return $checkResult;
		}

		$selectedTab = CopixRequest::get ('selectedTab', 1, true);
		if (($templateId = CopixRequest::get ('standardTemplate', null, true)) === null) {
			return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('admin|edit', array ('editId' => $editId, 'selectedTab' => $selectedTab)));
		}
		$templateScanner = &CopixClassesFactory::create ('TemplateScanner');
		if (($content = $templateScanner->getTemplateContent($templateId)) === null) {
			return CopixActionGroup::process ('generictools|Messages::getError',
			array ('message' => CopixI18N::get ('template.error.doNotExists'),
			'back' => CopixUrl::get ('template|admin|edit', array ('editId' => $editId))));
		}
		return CopixActionGroup::process ('generictools|messages::getConfirm',
		array ('confirm' => CopixUrl::get ('template|admin|validForm', array('standardTemplateImport' => $templateId, 'editId' => $editId, 'selectedTab' => $selectedTab)),
		'cancel' => CopixUrl::get ('template|admin|edit', array('editId' => $editId, 'selectedTab' => $selectedTab)),		'message' => str_replace (' ', '&nbsp;', nl2br (htmlentities ($content))),
		'title' => CopixI18N::get ('template.message.confirmImport', $templateId))
		);
	}

	/**
    * affiche le contenu d'un template dynamique pour l'importer dans la création des templates dynamiques
    */
	function getImportNonStandardTemplate () {
		// Checking if we have an element
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true) {
			return $checkResult;
		}

		$selectedTab = CopixRequest::get ('selectedTab', 1, true);
		if (($templateId = CopixRequest::get ('nonStandardTemplate', null, true)) === null) {
			return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('admin|edit', array ('editId' => $editId, 'selectedTab' => $selectedTab)));
		}

		$templateScanner = & CopixClassesFactory::create ('TemplateScanner');
		$daoTemplates    = & CopixDAOFactory::getInstanceOf ('copixtemplate');
		$template = null;
		if (($template = $daoTemplates->get($templateId)) === null) {
			return CopixActionGroup::process ('generictools|Messages::getError',
			array ('message' => CopixI18N::get ('template.error.doNotExists'),
			'back' => CopixUrl::get ('template|admin|edit', array ('editId' => $editId))));
		}
		return CopixActionGroup::process ('generictools|messages::getConfirm',
		array ('confirm' => CopixUrl::get ('template|admin|validForm', array('nonStandardTemplateImport' => $templateId, 'editId' => $editId, 'selectedTab' => $selectedTab)),
		'cancel' => CopixUrl::get ('template|admin|edit', array('editId' => $editId, 'selectedTab' => $selectedTab)),
		'message' => str_replace (' ', '&nbsp;', nl2br (htmlentities ($template->content_ctpl))),
		'title' => CopixI18N::get ('template.message.confirmImport', $template->caption_ctpl))
		);
	}

	/**
	* Ecran principal du générateur de template.
	*/
	function getTemplateGenerator() {
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true) {
			return $checkResult;
		}

		$tpl = new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', '');
		$tpl->assign ('MAIN', CopixZone::process ('TemplateGenerator',
		array ('editId'=>$editId,
		'editor'=>$editor,
		'elementId'=>CopixRequest::get ('elementId', null, true))));
		return new CopixActionReturn (CopixActionReturn::DISPLAY_IN, $tpl, '|blank.tpl');
	}

	/**
	* Ajoute un élément à l'élément sélectionné dans le générateur de template
	*/
	function doAddElementTemplateGenerator (){
		//Récupération du contexte de modification.
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true){
			return $checkResult;
		}

		//demande directe  à l'éditeur d'ajouter un élément
		if (($elementId = $editor->addElementTo (CopixRequest::get ('classname', null, true), CopixRequest::get ('elementId', null, true))) !== false){
			//Succès de l'ajout, on redirige sur la page d'édition
			$this->_setSessionTemplateEditor ($editor, $editId);
			return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('template|admin|templateGenerator', array ('editId'=>$editId, 'elementId'=>$elementId)));
		}else{
			return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('template|admin|templateGenerator', array ('editId'=>$editId)));
		}
	}

	/**
	* Affiche le rendu du template dynamique en cours d'édition
	*/
	function getHTMLParse() {
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true) {
			return $checkResult;
		}
		$root = & $editor->getRoot ();

		if (CopixRequest::get ('xml', false, true) === false){
			$tpl = new CopixTpl ();
			$tpl->assign ('TITLE_PAGE', "");
			$tpl->assign ('MAIN', $root->getHtml ());
			return new CopixActionReturn (CopixActionReturn::DISPLAY_IN, $tpl, '|blank.tpl');
		} else {
			            header('Content-Type: text/xml;charset=utf-8');
			echo utf8_encode ('<xmlresponse>
                      <response>
                       <html><![CDATA[
                        '.$root->getHtml ().'
                        ]]></html>
                      </response>
                     </xmlresponse>');
                     exit; 
			//'text/xml');
		}
	}

	/**
	* Récupère l'écran d'édition pour les propriété d'un élément (génère une réponse "Ajax")
	*/
	function getProperties () {
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true) {
			return $checkResult;
		}

		if (($element = & $editor->getTemplateElementById (CopixRequest::get ('elementId'))) !== null){
			//génération du contenu
			$htmlProperties = '<form name="propertyForm" id="propertyForm" method="post" action="'.CopixUrl::get ("template|admin|validProperties", array ('editId'=>$editId, 'elementId'=>$element->getId ())).'"  onsubmit="ajax_submitProperties (this, \''.$element->getId ().'\');return false;">';
			$htmlProperties .= '<input type="hidden" name="editId" value="'.$editId.'" />';
			$htmlProperties .= '<input type="hidden" name="elementId" value="'.$element->getId ().'" />';
			foreach ($element->getProperties () as $key=>$property){
				$property = $element->getProperty ($key);
				$htmlProperties .= $property->getHtml () ."<br />";
			}
			$htmlProperties .= '<input type="submit" value="Valider" />';
			$htmlProperties .= '</form>';

			$htmlProperties .= '<p>Ajouter un élément ?</p>';
			$arAddPossibilities = $editor->getAddPossibilitiesForElementById ($element->getId ());
			//affichage des éléments que l'on peut ajouter.
			foreach ($arAddPossibilities as $addElementClass=>$addElementCaption){
				$htmlProperties .= '<a href="'.CopixUrl::get ('template|admin|addElement', array ('editId'=>$editId,
				'classname'=>$addElementClass,
				'elementId'=>$element->getId ())).'" onclick="ajax_structureAdd (\''.$addElementClass.'\', \''.$element->getId ().'\'); return false;">'.$addElementCaption.'</a><br />';
			}
			            header('Content-Type: text/xml;charset=utf-8');
			echo utf8_encode ('<xmlresponse>
	                      <response>
	                       <html><![CDATA[
	                        '.$htmlProperties.'
	                        ]]></html>
	                      </response>
	                     </xmlresponse>'); 
	//		'text/xml');
	exit;
		}else{
			            header('Content-Type: text/xml;charset=utf-8');
			echo utf8_encode ('<xmlresponse>
	                      <response>
	                       <html><![CDATA[

	                       ]]></html>
	                      </response>
	                     </xmlresponse>');
	                      
			//'text/xml');
			exit;
		}
	}

	/**
	* Récupération de la structure
	*/
	function getStructure (){
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true) {
			return $checkResult;
		}
		if (($element = & $editor->getRoot ()) !== null){
			            header('Content-Type: text/xml;charset=utf-8');
			echo utf8_encode ('<xmlresponse>
	                      <response>
	                       <html><![CDATA[
	                        '.showTemplateLevel ($element, 0, $editId).'
	                        ]]></html>
	                      </response>
	                     </xmlresponse>'); 
			//'text/xml');
			exit;
		}else{
			            header('Content-Type: text/xml;charset=utf-8');
			echo  utf8_encode ('<xmlresponse>
	                      <response>
	                       <html><![CDATA[
	                        
	                        ]]></html>
	                      </response>
	                     </xmlresponse>'); 
			//'text/xml');
			exit;
		}
	}
	
	/**
	* Suppression d'un élément depuis le template.
	*/
	function doRemoveElement (){
		//Récupération du contexte de modification.
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true){
			return $checkResult;
		}

		$editor->remove (CopixRequest::get ('elementId'));
		$this->_setSessionTemplateEditor ($editor, $editId);
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('template|admin|templateGenerator', array ('editId'=>$editId)));
	}
	
	/**
	* Coupe l'élément donné dans le clipboard
	*/
	function doCutElement (){
		//Récupération du contexte de modification.
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true){
			return $checkResult;
		}
		
		$editor->cut (CopixRequest::get ('elementId'));
		$this->_setSessionTemplateEditor ($editor, $editId);
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('template|admin|templateGenerator', array ('editId'=>$editId)));
	}
	
	/**
	* Colle l'élément du clipboard dans l'élement donné
	*/
	function doPasteIn (){
		//Récupération du contexte de modification.
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true){
			return $checkResult;
		}
		
		$editor->paste (CopixRequest::get ('elementId'));
		$this->_setSessionTemplateEditor ($editor, $editId);
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('template|admin|templateGenerator', array ('editId'=>$editId)));
	}
	
	/**
	* Copie l'élément donné dans le clipboard
	*/
	function doCopyElement (){
		//Récupération du contexte de modification.
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true){
			return $checkResult;
		}
		
		$editor->copy (CopixRequest::get ('elementId'));
		$this->_setSessionTemplateEditor ($editor, $editId);
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('template|admin|templateGenerator', array ('editId'=>$editId)));
	}

	/**
	* Ajoute un élément à l'élément sélectionné dans le générateur de template et indique en XML quel
	*  est l'identifiant de l'élément ajouté
	*/
	function getAddElement (){
		//Récupération du contexte de modification.
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true){
			return $checkResult;
		}

		//demande directe  à l'éditeur d'ajouter un élément
		if (($elementId = $editor->addElementTo (CopixRequest::get ('classname', null, true), CopixRequest::get ('elementId', null, true))) !== false){
			//Succès de l'ajout, on redirige sur la page d'édition
			$this->_setSessionTemplateEditor ($editor, $editId);
			            header('Content-Type: text/xml;charset=utf-8');
			echo utf8_encode ('<xmlresponse>
	                      <response>
	                       <id>'.$elementId.'</id>
	                      </response>
	                     </xmlresponse>');
	                      
			//'text/xml');
			exit;
		}else{
			return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('template|admin|templateGenerator', array ('editId'=>$editId)));
		}
	}
	
	/**
	* Validation des propriétés d'un des éléments de l'éditeur de template.
	*/
	function doValidProperties (){
		//Récupération du contexte de modification.
		$editId = $edited = $editor = null;
		if (($checkResult = $this->_checkEditedElement ($edited, $editor, $editId)) !== true){
			return $checkResult;
		}
		$elementId = CopixRequest::get ('elementId');
		if (($element = & $editor->getTemplateElementById ($elementId)) !== null){
			$editor->validProperties ($elementId, $this->vars);
			$this->_setSessionTemplateEditor ($editor, $editId);
		}
		return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('template|admin|templateGenerator', array ('editId'=>$editId)));
	}
}

/**
* Affichage d'un niveau de l'arbre
* @param CopixTemplateElement $container L'élément à afficher 
*/
function showTemplateLevel ($pElement, $pLevel, $pEditId){
	if ($pLevel === 0){
		$result = '<table border="0" style="width: 100%;">';
	}else{
		$result = '';
	}
	$result .= '<tr>';
	$result .= '<td>'.str_repeat ('&nbsp;', $pLevel);
	$result .= '<a href="'.CopixUrl::get('template|admin|templateGenerator', array ('editId'=>$pEditId, 'elementId'=>$pElement->getId ())). '" onclick="ajax_refreshProperties (\''.$pElement->getId ().'\'); return false;">';
	$result .= $pElement->getCaption ();
	$result .= "</a></td>";
	$result .= '<td><a href="'.CopixUrl::get ('template|admin|doRemoveElement', array ('editId'=>$pEditId, 'elementId'=>$pElement->getId ())).'" onclick="ajax_removeElement (\''.$pElement->getId ().'\'); return false;"><img src="'.CopixUrl::get ().'img/tools/delete.png" alt="'.CopixI18N::get ('copix:common.buttons.delete').'" title="'.CopixI18N::get ('copix:common.buttons.delete').'" /></a></td>';
	$result .= "</tr>";
	if (is_a ($pElement, 'copixtemplatecontainer')){
		foreach ($pElement->getElements () as $key=>$child){
			$result .= showTemplateLevel ($child, $pLevel+1, $pEditId);
		}
	}

	if ($pLevel === 0){
		$result .= '</table>';
	}
	return $result;
}
?>
