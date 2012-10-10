<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('pictures|invalidparams');
/**
* @package	cms
* @subpackage pictures
* Admin services for the pictures module.
*/

class ActionGroupAdmin extends CopixActionGroup {
	/**
    * Indique si l'on peut coller un élément dans la rubrique
    * @param int this->vars['level'] the heading where we wants to paste the cutted element into

    * @return bool
    */
	function _canPaste (){
		if (!$this->_hasCut()){
			return false;
		}

		$dao = & CopixDAOFactory::getInstanceOf ('pictures');
		if (!$toPaste = $dao->get ($this->_getCut())){
			$this->_clearCut ();
			return false;
		}

		//does the destination heading exists ?
		if (($level = CopixRequest::get ('id_head', null, true)) !== null){
			$dao = & CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
			if (($record = $dao->get ($level)) === false) {
				return false;
			}
		}

		//do we have write permissions on the destination ?
		$headingProfileServices = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($headingProfileServices->getPath($level),
		'pictures') < PROFILE_CCV_WRITE) {
			return false;
		}
		//do we have write permissions on the cutted element ?
		if (CopixUserProfile::valueOf ($headingProfileServices->getPath($toPaste->id_head),
		'pictures') < PROFILE_CCV_PUBLISH) {
			return false;
		}
		return true;
	}

	/**
    * paste the element, from the session.
    * we have to have an element in the pseudo clipboard.
    * we have to have write permissions on both destination level and "from" level
    * The document have to exists. We move all the version of the document.
    * The dest heading must exists
    */
	function doPaste () {
		$this->vars['id_head'] = CopixRequest::get ('id_head', null, true);

		//do we have an element in the clipboard ?
		if (!$this->_hasCut()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindCut'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array ('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('pictures');
		if (!$toPaste = $dao->get ($this->_getCut())){
			$this->_clearCut ();
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPicture'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
		}

		//does the destination heading exists ?
		if ($this->vars['id_head'] !== null){
			$daoHeading = & CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
			if (($record = $daoHeading->get ($this->vars['id_head'])) === false) {
				$this->_clearCut ();
				return CopixActionGroup::process ('genericTools|Messages::getError',
				array ('message'=>CopixI18N::get ('copixheadings|admin.error.cannotFindHeading')));
			}
		}

		//do we have write permissions on the destination ?
		$headingProfileServices = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($headingProfileServices->getPath($this->vars['id_head']),
		'pictures') < PROFILE_CCV_WRITE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotPasteHere')));
		}

		//do we have write permissions on the cutted element ?
		if (CopixUserProfile::valueOf ($headingProfileServices->getPath($toPaste->id_head),
		'forms') < PROFILE_CCV_PUBLISH) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotMoveElement')));
		}

		$dao->moveHeading ($toPaste->id_pict, $this->vars['id_head']);
		$this->_clearCut ();
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'pictures', 'id_head'=>$this->vars['id_head'])));
	}

	/**
    * cuts a document
    * We have to have the rights to write in the given heading the cutted element belongs to to be able to do so.
    * @param int id_doc the document we wants to cut
    */
	function doCut () {
		//No given element.
		if (! isset ($this->vars['id_pict'])) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin', array('browse'=>'pictures')));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('pictures');
		if (!$toCut = $dao->get ($this->vars['id_pict'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPicture'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures'))));
		}
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toCut->id_head), 'pictures') < PROFILE_CCV_WRITE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures','id_head'=>$toCut->id_head))));
		}

		//ok, we can cut the element
		$this->_setCut ($this->vars['id_pict']);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array ('browse'=>'pictures', 'id_head'=>$toCut->id_head)));
	}

	/**
    * prepare edition of heading properties
    */
	function doPrepareEditProperties () {
		if (!isset($this->vars['id_head'])){
			return $this->_missingParameters();
		}
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($this->vars['id_head']), 'pictures') < PROFILE_CCV_MODERATE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
		}

		$dao     = & CopixDAOFactory::getInstanceOf ('picturesheadings');
		$id_head = strlen($this->vars['id_head']) > 0 ? $this->vars['id_head'] : null;
		if (!($toEdit = $dao->get ($id_head))){
			//if there is no properties, check headings exists and cretae default properties from father heading.
			$daoHeading = & CopixDAOFactory::getInstanceOf ('copixheadings|copixheadings');
			if (!($heading = $daoHeading->get($id_head))){
				return CopixActionGroup::process ('genericTools|Messages::getError',
				array ('message'=>CopixI18N::get ('pictures.error.cannotFindHeading'),
				'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
			}
			//set current cat with fathercat
			$fatherCat       = $dao->get($heading->father_head);
			$toEdit          = $fatherCat;
			$toEdit->id_head = $id_head;
			$dao->insert ($toEdit);
		}
		$this->_setSessionProperties ($toEdit);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('pictures|admin|editProperties'));
	}

	/**
    * Gets the heading parameters list.
    * @param this->vars[''] ==
    */
	function getEditProperties (){
		if (!$toEdit = $this->_getSessionProperties()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotGetPropertiesBack'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>'', 'kind'=>'1'))));
		}
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'pictures') < PROFILE_CCV_MODERATE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toEdit->id_head))));
		}
		$captionHead = $this->_getCaptionHeading($toEdit->id_head);
		if ($captionHead === false) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('copixheadings|admin.error.cannotFindHeading'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toEdit->id_head, 'kind'=>'1'))));
		}

		$tpl = & new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('pictures.titlePage.editProperties'));
		$tpl->assign ('MAIN', CopixZone::process ('EditProperties',array('e'=>isset ($this->vars['e']), 'toEdit'=>$toEdit, 'heading'=>$captionHead)));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
   * CHeck new properties and save to database if it's ok
   */
	function doValidProperties () {
		if (!$toValid = $this->_getSessionProperties()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotGetPropertiesBack'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures'))));
		}
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toValid->id_head), 'pictures') < PROFILE_CCV_MODERATE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toValid->id_head))));
		}

		$this->_validFromPostProperties($toValid);

		//insertion d'une ligne dans la table category à l'aide des dao
		$dao           = & CopixDAOFactory::getInstanceOf ('picturesheadings');
		$daoPicture    = & CopixDAOFactory::getInstanceOf ('pictures');
		$sp            = & CopixDAOFactory::createSearchParams ();

		//On récupère les images liées à la catégorie pour vérifier que les changements sont possibles
		$sp->addCondition('id_head','=',$toValid->id_head);
		$pictureList            = $daoPicture->findBy($sp);

		$maxX         = 0;
		$maxY         = 0;
		$maxWeight    = 0;
		$invalidFormat= array();

		if (count($pictureList)>0) {
			foreach ($pictureList as $picture){
				if ($picture->x_pict>$maxX){
					$maxX = $picture->x_pict;
				}
				if ($picture->y_pict>$maxY){
					$maxY = $picture->y_pict;
				}
				if ($picture->weight_pict>$maxWeight){
					$maxWeight = $picture->weight_pict;
				}
				$find=false;
				foreach ($this->vars['format'] as $format){
					if ($format==$picture->format_pict || $picture->format_pict=='unknown'){
						$find=true;
					}
				}
				if (!$find){
					$invalidFormat[]=$picture->format_pict;
				}
			}
		}
		$first = true;
		//si au mons un format à été choisi
		if (count($this->vars['format'])) {
			$toValid->format_cpic='';
			foreach ($this->vars['format'] as $format){
				if ($first<>true) {
					$toValid->format_cpic.=';';
				}
				$toValid->format_cpic.=$format;
				$first=false;
			}
		}
		//si les maximums sont respectés on modifie sinon on envoie une erreur
		if ((($toValid->maxX_cpic<$maxX && $toValid->maxX_cpic != '0')||
		($toValid->maxY_cpic<$maxY && $toValid->maxY_cpic != '0')||
		($toValid->maxWeight_cpic<$maxWeight && $toValid->maxWeight_cpic != '0') && (count($pictureList)>0))||
		(count($invalidFormat))||
		($dao->check ($toValid) !== true)){
			$toValid->invalidParams = & new InvalidParams($maxX,$maxY,$maxWeight,array_unique($invalidFormat));
			$this->_setSessionProperties($toValid);
			return new CopixActionReturn (CopixactionReturn::REDIRECT,CopixUrl::get ('pictures|admin|editProperties', array('e'=>1)));
		}else{
			$dao->update ($toValid);
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'kind'=>'1', 'id_head'=>$toValid->id_head)));
		}
	}

	/**
    * apply updates to the edited picture
    */
	function _validFromPostProperties (& $toUpdate){
		$arMaj = array ('name_cpic', 'maxX_cpic', 'maxY_cpic', 'maxWeight_cpic');
		foreach ($arMaj as $var){
			if (isset ($this->vars[$var])){
				$toUpdate->$var = $this->vars[$var];
			}
		}
	}

	/**
	* Fonction privée pour indiquer qu'il manque des paraètres à l'appel de notre fonction
	*/
	function _missingParameters ($id_head = null){
		return CopixActionGroup::process ('genericTools|Messages::getError',
		array ('message'=>CopixI18N::get ('pictures.error.missingParameters'),
		'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$id_head))));
	}

	/**
    * move pictures before deleting e theme.
    * @param this->vars['id_tpic'] == id of the theme
    *         this->vars['moveTo']  == id where to move pictures
    */
	function doMovePicture () {
		if (!isset($this->vars['id_head'])){
			return $this->_missingParameters ();
		}
		if (!isset ($this->vars['id_tpic'])){
			return $this->_missingParameters ($this->vars['id_head']);
		}
		$dao = & CopixDAOFactory::getInstanceOf ('picturesthemes');
		if (!$dao->get ($this->vars['id_tpic'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPicture'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
		}

		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOfIn ('pictures', 'modules|copixheadings') < PROFILE_CCV_MODERATE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toDelete->id_head))));
		}

		$daoPicturesLinkThemes = CopixDAOFactory::getInstanceOf ('pictureslinkthemes');
		if ($daoPicturesLinkThemes->getCountPictures($this->vars['id_tpic']) > 0) {
			if (!isset ($this->vars['moveTo'])){
				return $this->_missingParameters($this->vars['id_head']);
			}

			if (!$dao->get ($this->vars['moveTo'])){
				return CopixActionGroup::process ('genericTools|Messages::getError',
				array ('message'=>CopixI18N::get ('pictures.error.cannotFindPictureTheme'),
				'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
			}
			//si le theme de destination est différent de celui de départ
			if ($this->vars['moveTo']<>$this->vars['id_tpic']) {
				//deplacement des images
				$daoPicturesLinkThemes->moveTheme($this->vars['id_tpic'],$this->vars['moveTo']);
				$dao->delete($this->vars['id_tpic']);
			}else{
				return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('pictures|admin|prepareDelTheme', array('id_tpic'=>$this->vars['id_tpic'], 'id_head'=>$this->vars['id_head'])));
			}
		}else{
			$daoPicturesLinkThemes->deleteTheme ($this->vars['id_tpic']);
			$dao->delete($this->vars['id_tpic']);
		}
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head'], 'kind'=>'1')));
	}
	
	/**
    * Supression d'un theme
    */
	function doDeleteTheme (){
		$id_tpic = CopixRequest::get ('id_tpic');
		$dao = & CopixDAOFactory::getInstanceOf ('picturesthemes');
		$daoPicturesLinkThemes = & CopixDAOFactory::getInstanceOf ('pictureslinkthemes');
		if (!$dao->get ($this->vars['id_tpic'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPictureTheme'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>CopixRequest::get ('id_head')))));
		}
		$daoPicturesLinkThemes->deleteTheme ($this->vars['id_tpic']);
		$dao->delete($this->vars['id_tpic']);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>CopixRequest::get ('id_head'), 'kind'=>'1')));
	}

	/**
    * Delete a picture
    */
	function doDeletePicture (){
		if (!isset ($this->vars['id_pict'])){
			return $this->_missingParameters();
		}

		$dao = & CopixDAOFactory::getInstanceOf ('pictures');
		if (!$toDelete = $dao->get ($this->vars['id_pict'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPicture'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures'))));
		}

		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		$capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toDelete->id_head), 'pictures');
		$workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
		if ($capability < PROFILE_CCV_WRITE ||
		($capability < PROFILE_CCV_PUBLISH && $toDelete->status_pict == $workflow->getPublish())) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures','id_head'=>$toDelete->id_head))));
		}

		//Check if we have the confirmation infomation, if not, asks the user if he really wants to delete the given news.
		if ($toDelete->status_pict != $workflow->getTrash() && !isset ($this->vars['confirm'])){
			return CopixActionGroup::process ('genericTools|messages::getConfirm',
			array ('confirm'=>CopixUrl::get ('pictures|admin|deletePicture',array('browse'=>'pictures','id_pict'=>$toDelete->id_pict, 'id_head'=>$toDelete->id_head,'confirm'=>1)),
			'cancel'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures','id_head'=>$toDelete->id_head)),
			'message'=>CopixI18N::get ('pictures.messages.confirmDeletePicture', $toDelete->name_pict))
			);
		}

		$cachePath = CopixConfig::get ('pictures|path').$toDelete->id_pict.'/';
		if (is_dir($cachePath)) {
			require_once (CopixModule::getPath('pictures').'pictures/'.COPIX_CLASSES_DIR.'pictures.services.class.php');
			$picturesServices = & new PicturesServices ();
			$picturesServices->clearCacheFor ($toDelete->id_pict);
		}
		if (file_exists (CopixConfig::get ('pictures|path').$toDelete->id_pict.'.'.$toDelete->format_pict)){
			@unlink(CopixConfig::get ('pictures|path').$toDelete->id_pict.'.'.$toDelete->format_pict);
		}
		$dao->delete ($this->vars['id_pict']);

		$daoPicturesLinkThemes = & CopixDAOFactory::getInstanceOf ('pictureslinkthemes');
		$daoPicturesLinkThemes->deletePicture($this->vars['id_pict']);

		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toDelete->id_head)));
	}

	/**
    * Set online a picture
    */
	function doStatusPublishPicture () {
		$workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

		if (!isset ($this->vars['id_pict'])){
			return $this->_missingParameters($this->vars['id_head']);
		}

		$dao = & CopixDAOFactory::getInstanceOf ('pictures');
		if (!$toEdit = $dao->get ($this->vars['id_pict'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPicture'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures','id_head'=>$this->vars['id_head']))));
		}

		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'pictures') < PROFILE_CCV_PUBLISH) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures','id_head'=>$toEdit->id_head))));
		}
		$plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
		$user      = & $plugAuth->getUser();
		$toEdit->status_pict       = $workflow->getPublish ();
		$toEdit->statusauthor_pict = $user->login;
		$toEdit->statusdate_pict   = date('Ymd');
		$toEdit->statuscomment_pict= isset($this->vars['statuscomment_pict_'.$toEdit->id_pict]) ? $this->vars['statuscomment_pict_'.$toEdit->id_pict] : null;
		$dao->update($toEdit);

		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
		}else{
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toEdit->id_head)));
		}
	}

	/**
    * Set picture status to valid
    */
	function doStatusValidPicture (){
		$workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

		if (!isset ($this->vars['id_pict'])){
			return $this->_missingParameters($this->vars['id_head']);
		}

		$dao = & CopixDAOFactory::getInstanceOf ('pictures');
		if (!$toEdit = $dao->get ($this->vars['id_pict'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPicture'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures','id_head'=>$this->vars['id_head']))));
		}

		$plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
		$user      = & $plugAuth->getUser();
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'pictures') < PROFILE_CCV_VALID &&
		$toEdit->status_pict == $workflow->getPublish ()) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures','id_head'=>$toEdit->id_head))));
		}

		$toEdit->status_pict       = $workflow->getValid ();
		$toEdit->statusauthor_pict = $user->login;
		$toEdit->statusdate_pict   = date('Ymd');
		$toEdit->statuscomment_pict= isset($this->vars['statuscomment_pict_'.$toEdit->id_pict]) ? $this->vars['statuscomment_pict_'.$toEdit->id_pict] : null;
		$dao->update($toEdit);

		//launch event
		CopixEventNotifier::notify (new CopixEvent ('PictureValid',array ('picture'=>$toEdit)));


		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
		}else{
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toEdit->id_head)));
		}
	}

	/**
    * Set picture status to draft from trash
    */
	function doStatusDraftPicture (){
		$workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

		if (!isset ($this->vars['id_pict'])){
			return $this->_missingParameters($this->vars['id_head']);
		}

		$dao = & CopixDAOFactory::getInstanceOf ('pictures');
		if (!$toEdit = $dao->get ($this->vars['id_pict'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPicture'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures','id_head'=>$this->vars['id_head']))));
		}

		$plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
		$user      = & $plugAuth->getUser();
		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'pictures') < PROFILE_CCV_VALID &&
		$toEdit->status_pict != $workflow->getTrash () && $toEdit->statusauthor_pict != $user->login) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures','id_head'=>$toEdit->id_head))));
		}
		$toEdit->status_pict       = $workflow->getDraft ();
		$toEdit->statusauthor_pict = $user->login;
		$toEdit->statusdate_pict   = date('Ymd');
		$toEdit->statuscomment_pict= isset($this->vars['statuscomment_pict_'.$toEdit->id_pict]) ? $this->vars['statuscomment_pict_'.$toEdit->id_pict] : null;
		$dao->update($toEdit);

		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
		}else{
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toEdit->id_head)));
		}
	}

	/**
    * Set picture status to refuse
    */
	function doStatusRefusePicture (){
		$workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

		if (!isset ($this->vars['id_pict'])){
			return $this->_missingParameters($this->vars['id_head']);
		}

		$dao = & CopixDAOFactory::getInstanceOf ('pictures');
		if (!$toEdit = $dao->get ($this->vars['id_pict'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPicture'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures','id_head'=>$this->vars['id_head']))));
		}

		$plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
		$user      = & $plugAuth->getUser();

		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		$capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'pictures');
		if (($capability < PROFILE_CCV_VALID && $toEdit->status_pict == $workflow->getPropose()) ||
		($capability < PROFILE_CCV_PUBLISH && $toEdit->status_pict == $workflow->getValid()) ||
		$toEdit->status_pict == $workflow->getPublish()) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures','id_head'=>$toEdit->id_head))));
		}

		$toEdit->status_pict       = $workflow->getRefuse ();
		$toEdit->statusauthor_pict = $user->login;
		$toEdit->statusdate_pict   = date('Ymd');
		$toEdit->statuscomment_pict= isset($this->vars['statuscomment_pict_'.$toEdit->id_pict]) ? $this->vars['statuscomment_pict_'.$toEdit->id_pict] : null;
		$dao->update($toEdit);

		//launch event
		CopixEventNotifier::notify (new CopixEvent ('PictureRefuse',array ('picture'=>$toEdit)));

		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
		}else{
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toEdit->id_head)));
		}
	}

	/**
    * Set picture status to propose
    */
	function doStatusProposePicture (){
		$workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

		if (!isset ($this->vars['id_pict'])){
			return $this->_missingParameters($this->vars['id_head']);
		}

		$dao = & CopixDAOFactory::getInstanceOf ('pictures');
		if (!$toEdit = $dao->get ($this->vars['id_pict'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPicture'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures','id_head'=>$this->vars['id_head']))));
		}
		$plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
		$user      = & $plugAuth->getUser();

		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		$capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'pictures');

		if ($capability < PROFILE_CCV_WRITE &&
		$toEdit->status_pict == $workflow->getPublish ()) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures','id_head'=>$toEdit->id_head))));
		}

		$toEdit->status_pict       = $workflow->getPropose ();
		//récuperation de l'etat proposer/créer dans le worflow
		if (CopixConfig::get ('pictures|easyWorkflow') == 1){
			$toEdit->status_pict = $workflow->getBest ($toEdit->id_head, 'pictures');
		}
		$toEdit->statusauthor_pict = $user->login;
		$toEdit->statusdate_pict   = date('Ymd');
		$toEdit->statuscomment_pict= isset($this->vars['statuscomment_pict_'.$toEdit->id_pict]) ? $this->vars['statuscomment_pict_'.$toEdit->id_pict] : null;
		$dao->update($toEdit);

		//launch event
		if ($toEdit->status_pict == $workflow->getPublish ()) {
		}elseif($toEdit->status_pict == $workflow->getValid ()) {
			CopixEventNotifier::notify (new CopixEvent ('PictureValid',array ('picture'=>$toEdit)));
		}else{
			CopixEventNotifier::notify (new CopixEvent ('PicturePropose',array ('picture'=>$toEdit)));
		}

		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
		}else{
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toEdit->id_head)));
		}
	}

	/**
    * Set picture status to trash
    */
	function doStatusTrashPicture (){
		$workflow = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

		if (!isset ($this->vars['id_pict'])){
			return $this->_missingParameters($this->vars['id_head']);
		}

		$dao = & CopixDAOFactory::getInstanceOf ('pictures');
		if (!$toEdit = $dao->get ($this->vars['id_pict'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPicture'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures','id_head'=>$this->vars['id_head']))));
		}

		$plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
		$user      = & $plugAuth->getUser();

		//check if the user has the rights
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		$capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'pictures');

		if ($capability < PROFILE_CCV_WRITE ||
		($capability < PROFILE_CCV_VALID && $toEdit->status_pict == $workflow->getPropose()) ||
		($capability < PROFILE_CCV_PUBLISH && $toEdit->status_pict == $workflow->getValid()) ||
		$toEdit->status_pict == $workflow->getPublish()) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures','id_head'=>$toEdit->id_head))));
		}

		$toEdit->status_pict       = $workflow->getTrash ();
		$toEdit->statusauthor_pict = $user->login;
		$toEdit->statusdate_pict   = date('Ymd');
		$toEdit->statuscomment_pict= isset($this->vars['statuscomment_pict_'.$toEdit->id_pict]) ? $this->vars['statuscomment_pict_'.$toEdit->id_pict] : null;
		$dao->update($toEdit);

		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			return new CopixActionReturn (CopixactionReturn::REDIRECT, $this->vars['back']);
		}else{
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toEdit->id_head)));
		}
	}

	/**
    * initialize picture object
    */
	function doCreatePicture () {
		if (!isset($this->vars['id_head'])){
			return $this->_missingParameters(CopixRequest::get ('id_head'));
		}
		//check if the user has the rights to write pages into the given heading.
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($this->vars['id_head']), 'pictures') < PROFILE_CCV_WRITE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
		}
		$picture = & CopixDAOFActory::createRecord ('pictures');
		$picture->id_head = strlen($this->vars['id_head'] > 0) ? $this->vars['id_head'] : null;
		$picture->theme = array ();
		$this->_setSessionPicture ($picture);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('pictures|admin|editPicture'));
	}

	/**
    * initialize theme object
    */
	function doCreateTheme () {
		if (!isset($this->vars['id_head'])){
			return $this->_missingParameters(CopixRequest::get ('id_head'));
		}
		//check if the user has the rights to write pages into the given heading.
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($this->vars['id_head']), 'pictures') < PROFILE_CCV_MODERATE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
		}
		$theme = & CopixDAOFActory::createRecord ('picturesthemes');
		//just to go back to admin heading page....
		$theme->id_head = $this->vars['id_head'];
		$this->_setSessionTheme ($theme);
		//return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('pictures|admin|editTheme'));
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head'], 'kind'=>1)));
	}

	/**
    * Prepare picture edition
    */
	function doPrepareEditPicture () {
		if (!isset ($this->vars['id_pict'])){
			return $this->_missingParameters(CopixRequest::get ('id_head'));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('pictures');
		if (!$toEdit = $dao->get ($this->vars['id_pict'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPicture'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
		}

		//check if the user has the rights to write pages into the given heading.
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toEdit->id_head), 'pictures') < PROFILE_CCV_WRITE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
		}

		$daoPicturesLinkThemes = & CopixDAOFactory::getInstanceOf ('pictureslinkthemes');
		$sp                    = & CopixDAOFactory::createSearchParams ();
		$sp->addCondition ('id_pict', '=', $this->vars['id_pict']);
		$arTheme = $daoPicturesLinkThemes->findBy ($sp);
		$toEdit->theme = array();
		foreach ($arTheme as $theme){
			$toEdit->theme[] = $theme->id_tpic;
		}

		if (isset($this->vars['back']) && strlen($this->vars['back']) > 0) {
			$toEdit->__Copix_Internal_UrlBack = $this->vars['back'];
		}

		$this->_setSessionPicture($toEdit);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('pictures|admin|editPicture'));
	}

	/**
    * Prepare theme edition
    */
	function doPrepareEditTheme () {
		if (!isset ($this->vars['id_tpic'])){
			return $this->_missingParameters(CopixRequest::get ('id_head'));
		}
		if (!isset($this->vars['id_head'])){
			return $this->_missingParameters(CopixRequest::get ('id_head'));
		}
		//check if the user has the rights to write pages into the given heading.
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($this->vars['id_head']), 'pictures') < PROFILE_CCV_MODERATE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
		}
		$dao = & CopixDAOFactory::getInstanceOf ('picturesthemes');
		if (!$toEdit = $dao->get ($this->vars['id_tpic'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPicture'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
		}
		//just to go back to admin heading page....
		$toEdit->id_head = $this->vars['id_head'];
		$this->_setSessionTheme($toEdit);
		//return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('pictures|admin|editTheme'));
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head'], 'kind'=>1)));
	}

	/**
    * Picture edition
    */
	function getEditPicture () {
		$tpl     = & new CopixTpl ();
		if (!$picture = $this->_getSessionPicture ()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotGetPictureBack'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures'))));
		}

		$workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		$capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($picture->id_head), 'pictures');

		if ($capability < PROFILE_CCV_WRITE ||
		($capability < PROFILE_CCV_VALID && $picture->status_pict == $workflow->getPropose()) ||
		($capability < PROFILE_CCV_PUBLISH && $picture->status_pict == $workflow->getValid()) ||
		($capability < PROFILE_CCV_PUBLISH && $picture->status_pict == $workflow->getPublish()) ||
		$picture->status_pict == $workflow->getTrash()) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures','id_head'=>$picture->id_head))));
		}

		//check if the user has the rights to write pages into the given heading.
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($picture->id_head), 'pictures') < PROFILE_CCV_WRITE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$picture->id_head))));
		}
		$tpl->assign ('TITLE_PAGE', ($picture->id_pict > 0) ? CopixI18N::get ('pictures.titlePage.updatePicture') : CopixI18N::get ('pictures.titlePage.createPicture'));
		$captionHead = $this->_getCaptionHeading($picture->id_head);
		if ($captionHead === false) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures|pictures.error.cannotFindHeading'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$picture->id_head))));
		}
		$tpl->assign ('MAIN', CopixZone::process ('EditPicture',array('toEdit'=>$picture,'e'=>isset ($this->vars['e']),'heading'=>$captionHead)));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * theme edition
    */
 	function getEditTheme () {
		$tpl     = & new CopixTpl ();
		$theme = $this->_getSessionTheme ();
		//check if the user has the rights to write pages into the given heading.
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($theme->id_head), 'pictures') < PROFILE_CCV_MODERATE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$theme->id_head))));
		}
		$tpl->assign ('TITLE_PAGE', ($theme->id_tpic > 0) ? CopixI18N::get ('pictures.titlePage.updateTheme') : CopixI18N::get ('pictures.titlePage.createTheme'));
		$tpl->assign ('MAIN', CopixZone::process ('EditTheme',array('toEdit'=>$theme,'e'=>isset ($this->vars['e']))));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
    * Cancel the edition...... empty the session data
    */
	function doCancelEditPicture (){
		$level = '';
		if ($picture = $this->_getSessionPicture()){
			$level = $picture->id_head;
		}
		$this->_setSessionPicture(null);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$level)));
	}

	/**
    * Cancel the edition...... empty the session data
    */
	function doCancelEditTheme (){
		$level = '';
		if ($theme = $this->_getSessionTheme()){
			$level = $theme->id_head;
		}
		$this->_setSessionTheme(null);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$level, 'kind'=>'1')));
	}

	/**
    * apply updates on the edited theme.
    * save to datebase if ok and save file.
    */
	function doValidTheme (){
		if (!$toValid = $this->_getSessionTheme()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotGetThemeBack'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'kind'=>'1'))));
		}
		//check if the user has the rights to write pages into the given heading.
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($toValid->id_head), 'pictures') < PROFILE_CCV_MODERATE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toValid->id_head))));
		}
		$dao = & CopixDAOFactory::getInstanceOf ('picturesthemes');
		$this->_validFromFormTheme($toValid);
		if ($dao->check ($toValid) !== true){
			$this->_setSessionTheme($toValid);
			return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toValid->id_head, 'kind'=>'1', 'e'=>'1')));
		}
		//inserting or updating.
		if ($toValid->id_tpic > 0){
			$dao->update($toValid);
		}else{
			$dao->insert($toValid);
		}
		$this->_setSessionTheme (null);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$toValid->id_head, 'kind'=>'1')));
	}

	/**
    * add a picture for the PictureBrowser.
    * @param this->vars['id_cpic']      == picture category
    *         this->vars['id_fpic']      == picture format
    *         this->vars['desc_pict']    == picture description
    *         this->vars['name_pict']    == picture name
    *         $_FILES['imageFile']== Picture to insert
    */
	function doValidPicture () {
		if (!$toValid = $this->_getSessionPicture()){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotGetPictureBack'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures'))));
		}
		$toValid->errors = array ();

		//check if the user has the rights to write pages into the given heading.
		$workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		$capability       = CopixUserProfile::valueOf ($servicesHeadings->getPath ($toValid->id_head), 'pictures');

		if ($capability < PROFILE_CCV_WRITE ||
		($capability < PROFILE_CCV_VALID && $toValid->status_pict == $workflow->getPropose()) ||
		($capability < PROFILE_CCV_PUBLISH && $toValid->status_pict == $workflow->getValid())) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get ('copixheadings|admin|',array('browse'=>'pictures','id_head'=>$toValid->id_head))));
		}

		$this->_validFromPost ($toValid);

		$plugAuth            = & CopixController::instance ()->getPlugin ('auth|auth');
		$user                = & $plugAuth->getUser();
		$login               = $user->login;
		//Creation des DAO
		$daoPicture          = & CopixDAOFactory::getInstanceOf ('pictures');
		$daoPictureLinkTheme = & CopixDAOFactory::getInstanceOf ('pictureslinkthemes');
		$daoPictureHeadings  = & CopixDAOFactory::getInstanceOf ('picturesheadings');
		$recordPLT           = & CopixDAOFactory::createRecord ('pictureslinkthemes');

		if (!(count($toValid->theme)>0)) {
			$toValid->errors[] = CopixI18N::get('pictures.error.needOneFormatAtLeast');
		}
		//inserting or updating.
		if ($toValid->id_pict > 0){
			if (count($toValid->errors) > 0) {
				$toValid->id_pict  = null;
				$this->_setSessionPicture($toValid);
				return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('pictures|admin|editPicture', array('e'=>'1')));
			}
			$toValid->nameindex_pict = $daoPicture->getNextNameIndex($toValid->name_pict, $toValid->format_pict);
			if ($daoPicture->check ($toValid) !== true){
				return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('pictures|admin|editPicture', array('e'=>'1',)));
			}else{
				if (isset($this->vars['doBest']) && $this->vars['doBest']==1) {
					if (CopixConfig::get ('pictures|easyWorkflow') == 1){
						$toValid->status_pict       = $workflow->getBest ($toValid->id_head,'pictures');
					}else{
						$toValid->status_pict       = $workflow->getNext ($toValid->id_head,'pictures',$toValid->status_pict);
					}
					$toValid->statusdate_pict   = date('Ymd');
					$toValid->statuscomment_pict= '';
				}else{
					//if status is refuse then we change to draft
					if ($toValid->status_pict == $workflow->getRefuse ()) {
						$toValid->status_pict       = $workflow->getDraft ();
						$toValid->statusdate_pict   = date('Ymd');
						$toValid->statuscomment_pict= '';
					}
				}
				$daoPictureLinkTheme->deletePicture($toValid->id_pict);
				foreach ($toValid->theme as $id_tpic){
					$recordPLT->id_tpic  = $id_tpic;
					//Enregistrement de la table picturelinktheme
					$recordPLT->id_pict  = $toValid->id_pict;
					$daoPictureLinkTheme->insert ($recordPLT);
				}
				$daoPicture->update($toValid);
				$this->_setSessionPicture (null);
				if (isset($toValid->__Copix_Internal_UrlBack) && strlen($toValid->__Copix_Internal_UrlBack) > 0) {
					return new CopixActionReturn (CopixactionReturn::REDIRECT,$toValid->__Copix_Internal_UrlBack);
				}else{
					return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('id_head'=>$toValid->id_head, 'browse'=>'pictures')));
				}
			}
		}else{
			$toValid->author_pict       = $login;
			$toValid->statusauthor_pict = $login;
			$toValid->statusdate_pict   = date('Ymd');
			if (isset($this->vars['doBest']) && $this->vars['doBest']==1) {
				if (CopixConfig::get ('pictures|easyWorkflow') == 1){
					$toValid->status_pict       = $workflow->getBest ($toValid->id_head,'pictures');
				}else{
					$toValid->status_pict       = $workflow->getNext ($toValid->id_head,'pictures',$toValid->status_pict);
				}
			}else{
				$toValid->status_pict       = $workflow->getDraft();
			}

			if (isset($toValid->url_pict) && strlen($toValid->url_pict) > 0) { // l'image est un lien externe
			$toValid->id_pict        = date("YmdHis").rand(0,100);
			//if there is 2 pict with same name, add
			$toValid->format_pict    = 'unknown';
			$toValid->nameindex_pict = $daoPicture->getNextNameIndex($toValid->name_pict, $toValid->format_pict);
			$toValid->x_pict         = 0;
			$toValid->y_pict         = 0;
			$toValid->weight_pict    = 0;

			//dao error
			if ($daoPicture->check ($toValid) !== true){
				$toValid->id_pict  = 0;
				$this->_setSessionPicture($toValid);
				return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('pictures|admin|editPicture', array('e'=>'1')));
			}else{
				foreach ($toValid->theme as $id_tpic){
					$recordPLT->id_tpic  = $id_tpic;
					//Enregistrement de la table picturelinktheme
					$recordPLT->id_pict  = $toValid->id_pict;
					$daoPictureLinkTheme->insert ($recordPLT);
				}
				$daoPicture->insert ($toValid);
				$this->_setSessionPicture (null);
				if (isset($toValid->__Copix_Internal_UrlBack) && strlen($toValid->__Copix_Internal_UrlBack) > 0) {
					return new CopixActionReturn (CopixactionReturn::REDIRECT,$toValid->__Copix_Internal_UrlBack);
				}else{
					return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('id_head'=>$toValid->id_head, 'browse'=>'pictures')));
				}
			}

			}elseif (is_uploaded_file($_FILES['imageFile']['tmp_name'])) { // l'image est un fichier
			$original_size        = getimagesize($_FILES['imageFile']['tmp_name']);
			$toValid->format_pict = strrchr($_FILES['imageFile']['name'],'.');
			$toValid->format_pict = strtolower(substr($toValid->format_pict,1));
			$toValid->nameindex_pict = $daoPicture->getNextNameIndex($toValid->name_pict, $toValid->format_pict);
			$toValid->id_pict     = date("YmdHis").rand(0,100);
			$toValid->x_pict      = $original_size[0];
			$toValid->y_pict      = $original_size[1];
			$toValid->weight_pict = (intval($_FILES['imageFile']['size'])/1000);
			$toValid->errors      = array ();
			$properties           = $daoPictureHeadings->get($toValid->id_head);
//			var_dump($properties);
			$propertiesFormat     = explode(';',$properties->format_cpic);
			$formatIsOk           = false;

			foreach ($propertiesFormat as $currentFormat){
				if ($currentFormat==$toValid->format_pict){
					$formatIsOk = true;
				}
			}

			//Vérification que le format sélectionné correspond bien a celui de l'image uploader
			if (!$formatIsOk) {
				$toValid->errors[] = CopixI18N::get('pictures.error.wrongFormat', array($toValid->format_pict)) ;
			}
			//Verification que l'image correspond bien aux paramètres de la categorie
			if ((($original_size[0]      > @$properties->maxX_cpic) && (@$properties->maxX_cpic != 0)) ||
			(($original_size[1]      >@$properties->maxY_cpic) && (@$properties->maxY_cpic != 0)) ||
			(($toValid->weight_pict   >@$properties->maxWeight_cpic) && (@$properties->maxWeight_cpic != 0))){
				$toValid->errors[] = CopixI18N::get('pictures.error.wrongProperties');
			}

			if (!(count($toValid->theme)>0)) {
				$toValid->errors[] = CopixI18N::get('pictures.error.needOneFormatAtLeast');
			}

			if (count($toValid->errors) > 0) {
				$toValid->id_pict  = null;
				$this->_setSessionPicture($toValid);
				return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('pictures|admin|editPicture', array('e'=>'1')));
			}

			//dao error
			if ($daoPicture->check ($toValid) !== true){
				$toValid->id_pict  = 0;
				$this->_setSessionPicture($toValid);
				return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('pictures|admin|editPicture', array('e'=>'1')));
			}else{
				foreach ($toValid->theme as $id_tpic){
					$recordPLT->id_tpic  = $id_tpic;
					//Enregistrement de la table picturelinktheme
					$recordPLT->id_pict  = $toValid->id_pict;
					$daoPictureLinkTheme->insert ($recordPLT);
				}
				if ((move_uploaded_file ($_FILES['imageFile']['tmp_name'], CopixConfig::get ('pictures|path').$toValid->id_pict.'.'.$toValid->format_pict))&&
				($daoPicture->insert ($toValid))){
					$this->_setSessionPicture (null);
					if (isset($toValid->__Copix_Internal_UrlBack) && strlen($toValid->__Copix_Internal_UrlBack) > 0) {
						return new CopixActionReturn (CopixactionReturn::REDIRECT,$toValid->__Copix_Internal_UrlBack);
					}else{
						return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('id_head'=>$toValid->id_head, 'browse'=>'pictures')));
					}
				}else{
					return CopixActionGroup::process ('genericTools|Messages::getError',
					array ('message'=>CopixI18N::get ('pictures.error.cannotMoveUploadedFile'),
					'back'=>CopixUrl::get('pictures|admin|editPicture')));
				}
			}
			}else{
				return CopixActionGroup::process ('genericTools|Messages::getError',
				array ('message'=>CopixI18N::get ('pictures.error.cannotMoveUploadedFile'),
				'back'=>CopixUrl::get('pictures|admin|editPicture')));
			}
		}

	}

	/**
    * apply updates to the edited picture
    */
	function _validFromPost (& $toUpdate){
		$arMaj = array ('name_pict', 'desc_pict', 'theme', 'url_pict');
		foreach ($arMaj as $var){
			if (isset ($this->vars[$var])){
				$toUpdate->$var = $this->vars[$var];
			}
		}
	}

	/**
    * prepare to delete a theme in the database.
    * @param this->vars['id']        == id of the theme
    */
	function doPrepareDelTheme () {
		if (!isset($this->vars['id_head'])){
			return $this->_missingParameters(CopixRequest::get ('id_head'));
		}
		if (!isset ($this->vars['id_tpic'])){
			return $this->_missingParameters(CopixRequest::get ('id_head'));
		}
		//check if the user has the rights to write pages into the given heading.
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($this->vars['id_head']), 'pictures') < PROFILE_CCV_MODERATE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
		}

		$dao = & CopixDAOFactory::getInstanceOf ('picturesthemes');
		if (!$toEdit = $dao->get ($this->vars['id_tpic'])){
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.cannotFindPicture'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head']))));
		}

		$tpl = & new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', CopixI18N::get ('pictures.titlePage.deleteTheme'));
		$tpl->assign ('MAIN', CopixZone::process ('DeleteTheme',array('id_tpic'=>$this->vars['id_tpic'])));
		return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
	}

	/**
	* Valide le theme
	*/
	function _validFromFormTheme (& $toUpdate) {
		$toCheck = array ('name_tpic');
		foreach ($toCheck as $elem){
			if (isset ($this->vars[$elem])){
				$toUpdate->$elem = $this->vars[$elem];
			}
		}
	}

	/**
    * get caption of the heading
    */
	function _getCaptionHeading ($id_head) {
		//check if the heading exists. In the mean time, getting its caption
		$dao = & CopixDAOFactory::getInstanceOf ('copixheadings|CopixHeadings');
		if ($heading = $dao->get ($id_head)) {
			$caption_head = $heading->caption_head;
		} else {
			if ($id_head == null){
				$caption_head = CopixI18N::get('copixheadings|headings.message.root');
			}else{
				return false;
			}
		}
		return $caption_head;
	}

	/**
    * gets the current edited picture.

    */
	function _getSessionPicture (){
		CopixDAOFactory::fileInclude ('pictures');
		return isset ($_SESSION['MODULE_PICTURES_EDITED_PICTURE']) ? unserialize ($_SESSION['MODULE_PICTURES_EDITED_PICTURE']) : null;
	}

	/**
    * sets the current edited picture.

    */
	function _setSessionPicture ($toSet){
		$_SESSION['MODULE_PICTURES_EDITED_PICTURE'] = $toSet !== null ? serialize($toSet) : null;
	}

	/**
    * gets the current edited theme.

    */
	function _getSessionTheme (){
		CopixDAOFactory::fileInclude ('picturesthemes');
		return isset ($_SESSION['MODULE_PICTURES_EDITED_THEME']) ? unserialize ($_SESSION['MODULE_PICTURES_EDITED_THEME']) : null;
	}

	/**
    * sets the current edited theme.

    */
	function _setSessionTheme ($toSet){
		$_SESSION['MODULE_PICTURES_EDITED_THEME'] = $toSet !== null ? serialize($toSet) : null;
	}

	/**
    * gets the current edited properties.

    */
	function _getSessionProperties () {
		CopixDAOFactory::fileInclude ('Picturesheadings');
		return isset ($_SESSION['MODULE_PICTURES_EDITED_PROPERTIES']) ? unserialize ($_SESSION['MODULE_PICTURES_EDITED_PROPERTIES']) : null;
	}

	/**
    * sets the current edited properties.

    */
	function _setSessionProperties ($toSet){
		$_SESSION['MODULE_PICTURES_EDITED_PROPERTIES'] = $toSet !== null ? serialize($toSet) : null;
	}

	/**
    * cuts the element
    */
	function _setCut ($id_forms) {
		$_SESSION['MODULE_PICTURES_CUT'] = $id_forms;
	}

	/**
    * gets the cutted heading
    */
	function _getCut () {
		if (isset ($_SESSION['MODULE_PICTURES_CUT'])){
			return $_SESSION['MODULE_PICTURES_CUT'];
		}else{
			return null;
		}
	}

	/**
    * says if there's a cutted element
    */
	function _hasCut (){
		return isset ($_SESSION['MODULE_PICTURES_CUT']);
	}

	/**
    * clear the pseudo clipboard
    */
	function _clearCut (){
		session_unregister('MODULE_PICTURES_CUT');
	}

   /**
    * Import des images
    */
	function doImport(){
		set_time_limit(120);
        
		//check if the user has the rights to write pages into the given heading.
		$servicesHeadings = & CopixClassesFactory::getInstanceOf ('copixheadings|CopixProfileForHeadingServices');
		if (CopixUserProfile::valueOf ($servicesHeadings->getPath ($this->vars['id_head']), 'pictures') < PROFILE_CCV_WRITE) {
			return CopixActionGroup::process ('genericTools|Messages::getError',
			array ('message'=>CopixI18N::get ('pictures.error.notAnAuthorizedHead'),
			'back'=>CopixUrl::get('copixheadings|admin|', array('browse'=>'pictures', 'id_head'=>$this->vars['id_head'], 'kind'=>2))));
		}

		$daoPicture          = & CopixDAOFactory::create ('pictures');
		$daoPictureLinkTheme = & CopixDAOFactory::create ('pictureslinkthemes');
		$daoPictureHeadings  = & CopixDAOFactory::create ('picturesheadings');
		$recordPLT           = & CopixDAOFactory::createRecord ('pictureslinkthemes');
		$workflow         = & CopixClassesFactory::getInstanceOf ('copixheadings|workflow');


		$plugAuth            = & CopixController::instance ()->getPlugin ('auth|auth');
		$user                = & $plugAuth->getUser();
		$login               = $user->login;

		if (!isset($this->vars['theme'])) {
			$toValid->errors[] = CopixI18N::get('pictures.error.needOneFormatAtLeast');
		}
		$themes = $this->vars['theme'];
		$id_head = strlen ($this->vars['id_head']) > 0 ? $this->vars['id_head'] : null;
		$source = CopixConfig::get ('pictures|importDirectory');
		$dest = CopixConfig::get ('path');

		/***************************************/
		$handle = opendir($source);
		while(false !== ($filename = readdir($handle) ) ) {
			if (is_dir($source.$filename))
				continue;

			$file = $source.$filename;
            $name = substr($filename,0,strrpos($filename,'.'));

            //Vérification que le format sélectionné correspond bien a celui de l'image uploader
            $format = strrchr($file,'.');
			$format = strtolower(substr($format,1));
			$properties           = $daoPictureHeadings->get ($id_head);
			$propertiesFormat     = explode(';',$properties->format_cpic);
			$formatIsOk           = false;

			foreach ($propertiesFormat as $currentFormat){
				if ($currentFormat==$format){
					$formatIsOk = true;
				}
			}

            if ($formatIsOk){
                $toValid = null;
                $sp            = & CopixDAOFactory::createSearchParams ();
                $sp->addCondition ('name_pict', '=', $name);
                $new = true;
                $pictSaved = $daoPicture->findBy($sp);

                if(count($pictSaved) > 0 ){
                    foreach($pictSaved as $img){
                        $toValid = $img;
                    }
                    $new = false;
                } else {
                    $toValid->id_pict     		= date("YmdHis").rand(0,100000);
                    $toValid->desc_pict	  		= '';
                    $toValid->url_pict			= '';
                    $toValid->statuscomment_pict= '';
                    $new = true;
                }

                $toValid->author_pict       = $login;
                $toValid->statusauthor_pict = $login;
                $toValid->statusdate_pict   = date('Ymd');
                $toValid->id_head			= $id_head;

                $toValid->last_consultation_pict = date('Ymd');
                $toValid->status_pict       = $workflow->getPublish();
                $toValid->theme				= $themes;
                $toValid->errors 			= array();

                $original_size        		= getimagesize($file);
                $toValid->name_pict   		= $name;
                $toValid->format_pict 		= $format;

                $toValid->nameindex_pict 	= $daoPicture->getNextNameIndex($toValid->name_pict, $toValid->format_pict);

                $toValid->x_pict      		= $original_size[0];
                $toValid->y_pict      		= $original_size[1];
                $toValid->weight_pict 		= (filesize($file)/1000);

                //Verification que l'image correspond bien aux paramètres de la categorie
                if ((($original_size[0]      >$properties->maxX_cpic) && ($properties->maxX_cpic != 0)) ||
                (($original_size[1]      >$properties->maxY_cpic) && ($properties->maxY_cpic != 0)) ||
                (($toValid->weight_pict   >$properties->maxWeight_cpic) && ($properties->maxWeight_cpic != 0))){
                    $toValid->errors[] = CopixI18N::get('pictures.error.wrongProperties');
                }

                if (count($toValid->errors) > 0) {
                    continue;
                }

                foreach ($toValid->theme as $id_tpic){
                    $recordPLT->id_tpic  = $id_tpic;
                    //Enregistrement de la table picturelinktheme
                    $recordPLT->id_pict  = $toValid->id_pict;
                    $daoPictureLinkTheme->insert ($recordPLT);
                }
                copy ($file, $dest.$toValid->id_pict.'.'.$toValid->format_pict);
                if($new){
                    $daoPicture->insert ($toValid);
                } else {
                    $daoPicture->update ($toValid);
                }
            }else{
                //$toValid->errors[] = CopixI18N::get('pictures.error.wrongFormat', array($toValid->format_pict)) ;
            }
		}
		closedir($handle);
		return new CopixActionReturn (CopixactionReturn::REDIRECT, CopixUrl::get ('copixheadings|admin|', array('id_head'=>$toValid->id_head,
             'browse'=>'pictures')));
	}
}
?>
