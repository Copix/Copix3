<?php

class ActionGroupUpload extends CopixActionGroup {
 
	public function processSaveFiles (){
		$id_session = _request('id_session');
		
		$mediasValues = _request('media');
		$path = '';

		$query = "SELECT * FROM cms_uploader_files as uf
					LEFT JOIN cms_uploader_sessions as us ON uf.id_session = us.id_session
					WHERE uf.id_session = :id_session";
		$results = _doQuery ($query, array(':id_session'=>$id_session));
		
		$selected = array();
		foreach ($results as $file){
			//on vérifie que le fichier en base n'a pas été supprimé de la page et n'a pas été envoyé.
			if (array_key_exists($file->id_file, $mediasValues)){
				$media = _ppo (DAORecordcms_medias::create ());
				$media->parent_heading_public_id_hei = _request("parent_heading_public_id_hei", CopixSession::get ('heading', $id_session));				
				$media->caption_hei = $mediasValues[$file->id_file]['caption_hei'];
				$media->description_hei = $mediasValues[$file->id_file]['description_hei'];
				//nom du fichier
				$media->file_media = $file->name_file;
				$media->size_media = filesize($file->path_session.$id_session.$file->name_file);
				_class ('MediasServices')->insert ($media);
				
				$filename = strtr(utf8_decode($media->caption_hei),
					utf8_decode('ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ'), 
					'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
				$filename = preg_replace('/([^.a-z0-9]+)/i', '_', $filename);
				$media->file_media = $media->public_id_hei."_".$media->version_hei."_".$filename.".".pathinfo($media->file_media, PATHINFO_EXTENSION);
				_class ('MediasServices')->update ($media);
				if (_request('topublish', false)){
					_ioClass('heading|headingelementinformationservices')->publishById ($media->id_media, $media->type_hei);
				}
				
				//on deplace le fichier en vérifiant que le dossier existe bien
				CopixFile::createDir(COPIX_VAR_PATH.MediasServices::MEDIA_PATH.DIRECTORY_SEPARATOR);
				if (!rename($file->path_session.$id_session.$file->name_file, COPIX_VAR_PATH.MediasServices::MEDIA_PATH.DIRECTORY_SEPARATOR.$media->file_media)){
					_log("Impossible de renommer le fichier pour le média ".$media->caption_hei.' ('.$media->public_id_hei."), vous n'avez pas les droits.", "errors", CopixLog::ERROR);
				}
				$path = $file->path_session;
				$selected[] = $media->id_media . '|' . $media->type_hei;
			}
			//on supprime l'enregistrement en base du fichier
			DAOcms_uploader_files::instance ()->delete ($file->id_file);
		}
		//on supprime l'enregistrement en base de la session
		$criteres = _daoSP()->addCondition ('id_session', '=', $id_session);
		DAOcms_uploader_sessions::instance ()->deleteBy ($criteres);
		//on supprime le repertoire temporaire
		if($path != ''){
			CopixFile::removeDir($path);
		}
		
		return _arRedirect(_url('heading|element|finalizeEdit', array ('editId'=>$id_session, 'result'=>'saved', 'selected'=>$selected)));
	}
	
	/**
	 * 
	 * Retour de page aprés ajout dynamique de média en édition de page
	 */
	public function processConfirmMediaChooser(){
		$ppo = new CopixPPO();
		CopixConfig::instance()->mainTemplate = "default|popup.php";
		return _arPPO($ppo, "confirmmediachooser.php");
	}
}