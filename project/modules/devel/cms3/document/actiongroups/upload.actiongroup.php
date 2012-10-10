<?php

class ActionGroupUpload extends CopixActionGroup {

	public function processSaveFiles (){
		$id_session = _request('id_session');

		$documentsValues = _request('doc');
		$path = '';

		$query = "SELECT * FROM cms_uploader_files as uf
					LEFT JOIN cms_uploader_sessions as us ON uf.id_session = us.id_session
					WHERE uf.id_session = :id_session";
		$results = _doQuery ($query, array(':id_session'=>$id_session));
		
		$selected = array();
		foreach ($results as $file){
			//on vérifie que le fichier en base n'a pas été supprimé de la page et n'a pas été envoyé.
			if (array_key_exists($file->id_file, $documentsValues)){
				$document = _ppo (DAORecordcms_documents::create ());
				$document->parent_heading_public_id_hei = _request("parent_heading_public_id_hei", CopixSession::get ('heading', $id_session));
				$document->caption_hei = $documentsValues[$file->id_file]['caption_hei'];
				$document->description_hei = $documentsValues[$file->id_file]['description_hei'];
				//nom du fichier
				$document->file_document = $file->name_file;
				$document->size_document = filesize($file->path_session.$id_session.$file->name_file);
				_class ('DocumentServices')->insert ($document);
				
				$filename = strtr(utf8_decode($document->caption_hei),
				utf8_decode('ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ'), 
				'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
				$filename = preg_replace('/([^.a-z0-9]+)/i', '_', $filename);
				
				$document->file_document = $document->public_id_hei."_".$document->version_hei."_".str_replace(" ", "_", $filename).".".pathinfo($document->file_document, PATHINFO_EXTENSION);
				_class ('DocumentServices')->update ($document);
				if (_request('topublish', false)){
					_ioClass('heading|headingelementinformationservices')->publishById ($document->id_document, $document->type_hei);
				}
				
				//on deplace le fichier en verifiant que le dossier existe bien	
				CopixFile::createDir(COPIX_VAR_PATH.DocumentServices::DOCUMENT_PATH);
				if (!rename($file->path_session.$id_session.$file->name_file, COPIX_VAR_PATH.DocumentServices::DOCUMENT_PATH.$document->file_document)){
					_log("Impossible de renommer le fichier pour l'image ".$document->caption_hei.' ('.$document->public_id_hei."), vous n'avez pas les droits.", "errors", CopixLog::ERROR);
				}
				$path = $file->path_session;
				$selected[] = $document->id_document . '|' . $document->type_hei;
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
	 * Retour de page aprés ajout dynamique de document en édition de page
	 */
	public function processConfirmDocumentChooser(){
		$ppo = new CopixPPO();
		CopixConfig::instance()->mainTemplate = "default|popup.php";
		return _arPPO($ppo, "confirmdocumentchooser.php");
	}
}