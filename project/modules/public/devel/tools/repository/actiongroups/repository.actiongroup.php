<?php

class ActionGroupRepository extends CopixActionGroup {

	/**
	 * Fonction par default
	 * List all files in the repository
	 * TODO : Extend it to a Welcome Page  
	 */
	function processDefault() {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('repository.title.list');
		$ppo->arStoredFile = _ioDao ('storedfile')->findAll();

		return _arPPO ($ppo, 'repository.list.php');
	}

	/**
	 *
	 */
	function processAddFile() {
		// On vérifie que l'utilisateur est enregistrer
		CopixAuth::getCurrentUser ()->assertCredential ('basic:registered');

		if (CopixRequest::get ('confirm', null) === null) {
			// We display the form 
			$ppo->TITLE_PAGE = _i18n ('repository.title.list');
			$ppo->uploader = CopixAuth::getCurrentUser()->getLogin();
			return _arPPO ($ppo, 'repository.addform.php');
		} else {
			// Adding file to repository
			if (is_uploaded_file ($_FILES['uploadfile']['tmp_name'])) {
				$path = COPIX_VAR_PATH . CopixConfig::get('repository|repositorypath') .'/';
				$filename = $_FILES['uploadfile']['name'];
				if (! is_dir ($path)) {
					if (! CopixFile::createDir ($path)) {
						return CopixActionGroup::process ('genericTools|Messages::getError', array ('message'=>CopixI18N::get ('repository.error.cannot.create', array('path'=>$path)), 'back'=>_url ('repository|repository|addfile')));
	
					}
				}
				$data = CopixFile::read ($_FILES['uploadfile']['tmp_name']);
				if ($data) {
					if (! CopixFile::write ($path.$filename, $data)) {
						return CopixActionGroup::process ('genericTools|Messages::getError', array ('message'=>CopixI18N::get ('repository.error.cannot.write', array('path'=>$path)), 'back'=>_url ('repository|repository|addfile')));
					}
				} else {
					return CopixActionGroup::process ('genericTools|Messages::getError', array ('message'=>CopixI18N::get ('repository.error.upload'), 'back'=>_url ('repository|repository|addfile')));
				}
				
				$dao = & CopixDAOFactory::create ('storedfile');
				$record = & CopixDAOFactory::createRecord ('storedfile');
				$record -> storedfile_name = $filename;
				$record -> storedfile_path = $path;
				$record -> storedfile_description = CopixRequest::get('comment');
				$record -> storedfile_nbdownload = 0; 
				$record -> storedfile_uploader = CopixAuth::getCurrentUser()->getLogin();
				$record -> storedfile_uploaddate = date ("Y-m-d h:i:s");	
				$dao->insert ($record);	
				return new CopixActionReturn(CopixActionReturn::REDIRECT, CopixUrl::get('repository|repository|'));
			} else {
				return CopixActionGroup::process ('genericTools|Messages::getError', array ('message'=>CopixI18N::get ('repository.error.upload'), 'back'=>_url ('repository|repository|addfile')));
			}
			

		
		}	
	}
	
	/**
	 *
	 */
	function processDownload() {
		// Recuperation de l'id
		if (CopixRequest::get ('id', null) !== null) {
			// On cherche et renvoie le fichier	
				
			$dao = & CopixDAOFactory::create ('storedfile');
			$sp = & CopixDAOFactory::createSearchParams();
			$sp->addCondition ("storedfile_id", "=", CopixRequest::get ('id'));
			$arRecord = $dao->findBy ($sp);
			if ( count($arRecord) >0 ) {
				$record = $arRecord[0];
				$data = file_get_contents($record->storedfile_path.$record->storedfile_name);
				$record-> storedfile_nbdownload +=1;
				$dao->update ($record);
				return new CopixActionReturn (CopixActionReturn::CONTENT, $data, array('filename'=>$record->storedfile_name, 'content-type'=>CopixMIMETypes::getFromFileName($record->storedfile_name)));
			} else {
				// @TODO : changer le message d'erreur
				return CopixActionGroup::process ('genericTools|Messages::getError', array ('message'=>CopixI18N::get ('repository.error.filenotfound'), 'back'=>_url ('repository|repository|')));
			}
		} else {
			return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('repository|repository|'));
		}
	}

} 
?>
