<?php
class ActionGroupDocumentFront extends CopixActionGroup {
	
	/**
	 * Affiche un document
	 * n'est appelé directement que pour la preview dans l'admin
	 * A NE PAS APPELER DANS LES PAGES EN AFFICHAGE FRONT OFFICE => PASSER PAR processDefault !!
	 */
	public function processShowDocumentFile (){
		$listId = _request ("public_id", array ());
		$content_disposition = _request ('content_disposition' , 'attachement');

		if (!(is_array($listId)) || (is_array ($listId) && sizeof ($listId) == 1)){
			if (is_array($listId)){
				$listId = $listId[0];
			}
			$element = _class ('document|documentservices')->getByPublicId ($listId);	
			return _arFile (COPIX_VAR_PATH.DocumentServices::DOCUMENT_PATH.$element->file_document, array ('content-disposition'=>$content_disposition));
		}elseif (!empty ($listId)){	
			$zip = new CopixZip();
			CopixFile::createDir(COPIX_CACHE_PATH.DocumentServices::DOCUMENT_PATH);
			if (($zip->open(COPIX_CACHE_PATH.DocumentServices::DOCUMENT_PATH.'documents.zip', ZipArchive::OVERWRITE)) === true){
				foreach ($listId as $public_id_hei){
					$element = _class ('document|documentservices')->getByPublicId ($public_id_hei);	
					$ext = CopixFile::extractFileExt($element->file_document);
					$zip->addFile (COPIX_VAR_PATH.DocumentServices::DOCUMENT_PATH.$element->file_document, $element->caption_hei.$ext);
				}
				$zip->close ();
				return _arFile (COPIX_CACHE_PATH.DocumentServices::DOCUMENT_PATH.'documents.zip');
			}
		}	
		return new CopixActionReturn (CopixActionReturn::HTTPCODE, CopixHTTPHeader::get404 ());
	}
	
	/**
	 * On vérifie que Heading|| a lancé l'ordre d'affichage des éléments demandés.
	 *
	 */
	public function processDefault (){
		//HeadingFront utilise Copixregistry pour indiquer les public_id dont il a demandé l'affichage
		$front = CopixRegistry::instance ()->get ('headingfront');

		if ($front !== _request('public_id')){
			throw new CopixCredentialException ('basic:admin'); 
		}
		return $this->processShowDocumentFile ();
	}
}