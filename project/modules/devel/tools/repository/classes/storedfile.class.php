<?php

class StoredFile{

	/**
	 * Méthode permettant d'ajouter un fichier au repository
	 *
	 * @param string $pNameField Nom du champ
	 * @todo Passer un CopixUploadFile en paramètre et $ppo
	 */
	public function store ($pFieldName){
		$path = COPIX_VAR_PATH . CopixConfig::get('repository|repositorypath') .'/';
		$file = CopixRequest::getFile($pFieldName, $path);
		if ($file !== false){
			$dao = & CopixDAOFactory::create ('storedfile');
			$record = & CopixDAOFactory::createRecord ('storedfile');
			$record -> name = $file->getName ();
			$record -> title = CopixRequest::get('file_title');
			$record -> path = $path;
			$record -> description = CopixRequest::get('file_comment');
			$record -> nbdownload = 0;
			$record -> uploader = CopixAuth::getCurrentUser()->getCaption();
			$record -> category_id = _request ('file_category');
			
			$dao->insert ($record);
			return $record->id;
		}
		return false;
	}

	public function addInformation ($pFileId){
		$record = _dao ('storedfile')->get ($pFileId);
		$record -> title = _request('file_title', '');
		$record -> description = _request('file_comment');
		$record -> uploader = CopixAuth::getCurrentUser()->getCaption();
		$record -> category_id = _request ('file_category');
		$record -> subcategory_id = _request ('file_subcategory');
		$record -> uploaddate = date ("YmdHis");
		_dao ('storedfile')->update ($record);
	}
	/**
	 * Récupération d'une liste de fichier
	 *
	 * @param int $pIdCategory
	 * @param int $pIdSubCategory
	 * @return array
	 */
	public function getList ($pIdCategory = null, $pIdSubCategory  = null){
		if ($pIdCategory === null && $pIdSubCategory === null) {
			return _ioDao ('storedfile')->findAll();
		}
		$dao = _ioDao ('storedfile');
		$sp = _daoSp ()->addCondition ('storedfile_category_id', '=', $pIdCategory);
		if ($pIdSubCategory != null) {
			$sp = $sp->addCondition ('storedfile_subcategory_id', '=', $pIdSubCategory);
		}
		return _ioDao ('storedfile')->findBy ($sp);
	}

	public function count ($pIdCategory = null, $pIdSubCategory = null){
		if ($pIdCategory === null && $pIdSubCategory === null) {
			return count (_ioDao ('storedfile')->findAll());
		}
		$dao = _ioDao ('storedfile');
		$sp = _daoSp ()->addCondition ('storedfile_category', '=', $pIdCategory);
		if ($pIdSubCategory != null) {
			$sp = $sp->addCondition ('storedfile_subcategory', '=', $pIdSubCategory);
		}
		return _ioDao ('storedfile')->countBy ($sp);
	}
}