<?php
class ActionGroupAdmin extends ActionGroupAbstractAdminHeadingElement {
	
	/**
	 * Prépare l'édition d'un document
	 * 
	 * @return CopixActionReturn
	 */
	public function processPrepareEdit () {
		parent::processPrepareEdit ();
		$form = CopixUserPreferences::get ('document|uploadForm');
		return _arRedirect (_url ('admin|edit', array ('editId' => _request ('editId'), 'classic' => ($form == 'classic'))));
	}
	
	/**
	 * Formulaire de modification de l'élément
	 */
	public function processEdit (){
		$ppo = _ppo ();
		$ppo->editedElement = $this->_getEditedElement ();
		$ppo->editId = _request ('editId');
		$ppo->TITLE_PAGE = $ppo->editedElement->public_id_hei && !_request('classic', false) ? 'Modification d\' un média' : 'Création d\'un media';
		
		//Calcul du type média pour affichage de l'îcone correspondant
		if (!empty($ppo->editedElement->file_media)) {
			$ppo->type = $ppo->editedElement->media_type = MediasServices::getMediaTypeByFileName($ppo->editedElement->file_media);
			$ppo->editedElement->media_name = substr($ppo->editedElement->file_media, strpos($ppo->editedElement->file_media, '#') + 1);
		}
        // utile pour savoir s'il faut afficher ou non le champ Image de substitution (uniquement pour les flashs)
        else {
            $ppo->type = (CopixSession::get('type_hei', $ppo->editId));
        }
		$ppo->chooseHeading = false;
		$ppo->popup = false;
		if (CopixSession::get ('then', $ppo->editId, false)){
			$ppo->popup = true;
			$ppo->chooseHeading = true;
			CopixConfig::instance()->mainTemplate = "default|popup.php";
		}
		return _arPpo ($ppo, $ppo->editedElement->public_id_hei || _request('classic', false) ? 'medias.edit.php' : 'medias.form.php');
	}

	/**
	 * Sauvegarde de la page
	 *
	 * @return unknown
	 */
	public function processValid (){
		$element = $this->_getEditedElement ();
		$oldStatus = $element->status_hei;
		//mise à jour de l'enregistrement en cours de modification
		_ppo (CopixRequest::asArray ('caption_hei', 'description_hei'))->saveIn ($element);
		if (($fichier = CopixUploadedFile::get ('file_media')) !== false){
			$fileName = uniqid () . '#' . $fichier -> getName(); 	
			$element->file_media = $fileName;
			$element->size_media = $fichier->getSize();
	  	}
        // Image de substitution (Flash uniquement)
		if (($image = CopixUploadedFile::get ('image_media')) !== false) {
            $aImageFileInfo = pathinfo($image->getName());
            $imageFileName = CopixUrl::escapeSpecialChars($aImageFileInfo['filename']);
            $imageFileName = preg_replace('/([^.a-z0-9]+)/i', '_', $imageFileName);
            $imageFileName .= '.'.$aImageFileInfo['extension'];
            $element->image_media = uniqid () . '#' . $imageFileName;
	  	}
	  	
	  	$toPlan = _ioClass ('HeadingElementInformationServices')->checkPlanningDates(_request('published_date', false), _request('end_published_date', false));
	    	
		//On crée un nouvel élément si  
		// id_media === null (enregistrement jamais crée)
		if ($element->id_media === null){
			if ($element->file_media == ""){
				$ppo = _ppo ();
				$ppo->editedElement = $this->_getEditedElement ();
				$ppo->editId = _request ('editId');
				$ppo->errors = 'Vous devez choisir un fichier';
				$ppo->TITLE_PAGE = 'Création d\'un média';
				//Calcul du type média pour affichage de l'îcone correspondant
				if (!empty($ppo->editedElement->file_media)) {
					$ppo->type = $ppo->editedElement->media_type = MediasServices::getMediaTypeByFileName($ppo->editedElement->file_media);
					$ppo->editedElement->media_name = substr($ppo->editedElement->file_media, strpos($ppo->editedElement->file_media, '#') + 1);
				}
		        // utile pour savoir s'il faut afficher ou non le champ Image de substitution (uniquement pour les flashs)
		        else {
		            $ppo->type = (CopixSession::get('type_hei', $ppo->editId));
		        }
				return _arPpo ($ppo, 'medias.edit.php');
			}
			_class ('MediasServices')->insert ($element);
		}elseif ($element->status_hei == HeadingElementStatus::DRAFT
				|| ($element->status_hei == HeadingElementStatus::PLANNED && $toPlan)){
			//On modifie directement l'objet si 
			//le statut de l'élément est brouillon
			_class ('MediasServices')->update ($element);
		}else{
			//Dans tous les autres cas
			//on crée une nouvelle version de l'élément 
			_class ('MediasServices')->version ($element);
		}
		
		//si aucune exception n'a été jeté, on upload
		if ($fichier){	
			$fichier -> move (COPIX_VAR_PATH.MediasServices::MEDIA_PATH.DIRECTORY_SEPARATOR, $element->file_media);
	  	}
		//si aucune exception n'a été jeté, on upload l'image de substitution
		if ($image){
			if(!$image->move (COPIX_VAR_PATH.MediasServices::MEDIA_PATH.DIRECTORY_SEPARATOR, $element->image_media)) {
                _log('Impossible de déplacer le média '.$element->image_media.' ('.$element->public_id_hei.')', 'errors', CopixLog::ERROR);
                throw new CopixException('Impossible de déplacer le média '.$element->image_media.' ('.$element->public_id_hei.')');
            }
	  	}

		//planification
		if($toPlan){
			$published_date_hei = _request('published_date', false) ? CopixDateTime::DateTimeToyyyymmddhhiiss(_request('published_date')) : null;
			$end_published_date_hei = _request('end_published_date', false) ? CopixDateTime::DateTimeToyyyymmddhhiiss(_request('end_published_date')) : null;
			
			//si on part d'une version publiée sur laquelle on veut juste ajouter une date d'archivage, on publie d'abord la nouvelle version
			if($oldStatus == HeadingElementStatus::PUBLISHED && !$published_date_hei && $end_published_date_hei){
				_ioClass ('HeadingElementInformationServices')->publishById ($element->id_helt, $element->type_hei);
			}
			//on planifie
			_ioClass ('HeadingElementInformationServices')->planById ($element->id_helt, $element->type_hei, $published_date_hei, $end_published_date_hei);
		}
		//publication
		else if (CopixRequest::getBoolean ('publish')) {
			_ioClass ('HeadingElementInformationServices')->publishById ($element->id_helt, $element->type_hei);
			// Possibilité de notifier par email qu'un contenu a été publié
			$headingElementType = new HeadingElementType ();
			$typeInformations = $headingElementType->getInformations ($element->type_hei);
			if (CopixUserPreferences::get($typeInformations['module'].'|'.$element->type_hei.'Notification') == '1') {
                // Previous Action
                $aParam['prevaction'] = 'publish';
            }
		}

		//retour sur l'écran d'admin générale
  		return _arRedirect (_url ('heading|element|finalizeEdit', array ('editId' => _request ('editId'), 'result'=>'saved', 'selected'=>array($element->id_helt . '|' . $element->type_hei))));
	}
	
	/**
	 * Affiche le fichier d'un document
	 *
	 */
	public function processDownload(){
		$element = $this->_getEditedElement ();
		$path_parts = pathinfo($element -> file_media);
		$extension = $path_parts['extension'];	
		return _arFile (COPIX_VAR_PATH.MediasServices::MEDIA_PATH.DIRECTORY_SEPARATOR.$element -> file_media, array ('filename' => CopixUrl::escapeSpecialChars($element -> caption_hei) . "." . $extension));
	}
}