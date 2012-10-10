<?php

class DocumentServices extends HeadingElementServices {
	
	const DOCUMENT_PATH = "documents/";

	/**
	 * Retourne une courte description pour l'affichage détails
	 *
	 * @param int $pIdHelt Identifiant interne
	 * @return string
	 */
	public function getDisplayDescription ($pIdHelt) {
		$document = $this->getById ($pIdHelt);
		$toReturn = 'Type : ' . substr (CopixFile::extractFileExt ($document->file_document), 1);
		$toReturn .= ' - ' . ((strlen ($document->description_hei) > 30) ? substr ($document->description_hei, 0, 30) . '...' : $document->description_hei);
		return $toReturn;
	}
	
	/**
	 * 
	 * Retourne la description de l'élément
	 * @param int $pIdHelt
	 */
	public function getDescription ($pIdHelt){
		$document = $this->getById ($pIdHelt);
		return $document->description_hei;
	}
	
	/**
	 * Création d'un nouveau document
	 * @param array / object $pDocumentDescription
	 */
	public function insert ($pDocumentDescription){
		HeadingCache::clear ();
		$documentDescription = _ppo ($pDocumentDescription);

		CopixDB::begin ();
		try {
			//création de l'enregistrement
			$record = DAORecordcms_documents::create ()->initFromDbObject ($documentDescription);

			DAOcms_documents::instance ()->insert ($record);

			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $documentDescription[$propertyName];
			}
			$record->id_helt = $record->id_document;
			$record->type_hei = 'document';

			//dans $record->id_helt on a le nouvel identifiant
			_ioClass ('heading|HeadingElementInformationServices')->insert ($record);
			
			$filename = strtr(utf8_decode($record->caption_hei),
				utf8_decode('ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ'), 
				'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
			$filename = preg_replace('/([^.a-z0-9]+)/i', '_', $filename);
			$record->file_document = $record->public_id_hei."_".$record->version_hei."_".$filename.".".pathinfo($record->file_document, PATHINFO_EXTENSION);
			
			//on met maintenant a jour l'élément $record avec les informations publiques mises à jour
			DAOcms_documents::instance ()->update ($record);

			//Application des changements
			_ppo ($record)->saveIn ($pDocumentDescription);
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e; 
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	/**
	 * Mise à jour d'une page (création d'une nouvelle version)
	 * 
	 * @param array / object $pDocumentDescription
	 */
	public function update ($pDocumentDescription){
		HeadingCache::clear ();
		$documentDescription = _ppo ($pDocumentDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($documentDescription['id_document']);

			//on met a jour les données spécifiques			
			$record->description_hei = $documentDescription['description_hei'];
			if($documentDescription['file_document']){
				$filename = strtr(utf8_decode($documentDescription['caption_hei']),
					utf8_decode('ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ'), 
					'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
				$filename = preg_replace('/([^.a-z0-9]+)/i', '_', $filename);
				$record->file_document = $record->public_id_hei."_".$record->version_hei."_".$filename.".".pathinfo($record->file_document, PATHINFO_EXTENSION);
			}
			
			DAOcms_documents::instance ()->update ($record);
			
			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $documentDescription[$propertyName];
			}
			_ioClass ('heading|HeadingElementInformationServices')->update ($record);

			//Application des changements
			_ppo ($record)->saveIn ($pDocumentDescription);
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	/**
	 * Création d'une nouvelle version a partir de l'élément passé en paramètre
	 * 
	 * @param object $pDocumentDescription la description de la page dont on souhaite obtenir une nouvelle version
	 */
	public function version ($pDocumentDescription){
		HeadingCache::clear ();
		$documentDescription = _ppo ($pDocumentDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($documentDescription['id_document']);

			//on met a jour les données spécifiques			
			$record->description_hei = $documentDescription['description_hei'];
			$newVersion = _ioClass ('heading|HeadingElementInformationServices')->getNextVersion ($record->public_id_hei);

			if($documentDescription['file_document'] != $record->file_document){
				$record->file_document = $record->public_id_hei."_".$newVersion."_".$record->caption_hei.".".pathinfo($documentDescription['file_document'], PATHINFO_EXTENSION);
			}
			else{
				$infos = explode('_', $record->file_document);
				$public_id_hei = $infos[0];
				$version = $infos[1];
				$caption = implode('_', array_slice($infos, 2));
	
				$filename = $public_id_hei."_".$newVersion."_".$caption;
				copy (COPIX_VAR_PATH.DocumentServices::DOCUMENT_PATH.$record->file_document ,COPIX_VAR_PATH.DocumentServices::DOCUMENT_PATH.$filename);
				$record->file_document = $filename;
			}
			
			
			DAOcms_documents::instance ()->insert ($record);

			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $documentDescription[$propertyName];
			}
			$record->id_helt = $record->id_document;
			_ioClass ('heading|HeadingElementInformationServices')->version ($record, $documentDescription['id_document']);

			//Application des changements
			_ppo ($record)->saveIn ($pDocumentDescription);
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	/* 
	 * Création d'un nouveau document
	 * @param array / object $pDocumentDescription
	 */
	public function copy ($pPublicId, $pHeading){
		HeadingCache::clear ();
		CopixDB::begin ();
		try {
			$document = $this->getByPublicId($pPublicId);
			$document->public_id_hei = null;
			$document->url_id_hei = $document->url_id_hei ? $document->url_id_hei . ' (copie)' : $document->url_id_hei;
			$document->parent_heading_public_id_hei = $pHeading;
			
			DAOcms_documents::instance ()->insert ($document);

			$document->id_helt = $document->id_document;
			$document->caption_hei = $document->caption_hei . ' (copie)';	
			$document->display_order_hei = _ioClass ('heading|HeadingElementInformationServices')->getNextDisplayOrderValue ($pHeading);	

			_ioClass ('heading|HeadingElementInformationServices')->insert ($document);
						
			$oldFileName = $document->file_document;
			$infos = explode('_', $document->file_document);
			$public_id_hei = $infos[0];
			$version = $infos[1];
			$filename = implode('_', array_slice($infos, 2));
			
			$document->file_document = $document->public_id_hei."_".$document->version_hei."_".$filename;
			@copy (COPIX_VAR_PATH.DocumentServices::DOCUMENT_PATH.$oldFileName, COPIX_VAR_PATH.DocumentServices::DOCUMENT_PATH.$document->file_document);
			
			DAOcms_documents::instance ()->update ($document);
			
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e; 
		}
		CopixDB::commit ();
		HeadingCache::clear ();
		return $document->public_id_hei;
	}

	/**
	 * Récupère un enregistrement par son identifiant interne
	 * 
	 * @param int $pIdHelt Identifiant interne de l'élément à récupérer 
	 */
	public function getById ($pIdHelt){
		//on vérifie que l'élément existe
		if (! $record = DAOcms_documents::instance ()->get ($pIdHelt)){
			throw new HeadingElementInformationNotFoundException ($pIdHelt);
		}
		//on récupère les informations "communes"
		$element = _ioClass ('heading|HeadingElementInformationServices')->getById ($pIdHelt, 'document');
		//fusion des informations communes et spécifiques
		_ppo ($record)->saveIn ($element);

    	return $element;
	}
	
	/**
	 * Recupere un enregistrement par son identifiant public
	 *
	 * @param unknown_type $pPublicId
	 * @return unknown
	 */
	public function getByPublicId ($pPublicId){
		//on récupère les informations "communes"
		$element = _ioClass ('heading|HeadingElementInformationServices')->get($pPublicId);
		
		//pour les liens		
		if ($element->type_hei == "link"){
			$lien = _class ('heading|linkservices')->getByPublicId ($pPublicId);
			$linkPublicId = $pPublicId;
			$pPublicId = $lien->linked_public_id_hei;
			if(is_null($pPublicId)){
				throw new HeadingElementInformationException ($pPublicId);
			}
			$element = _ioClass ('heading|HeadingElementInformationServices')->get($pPublicId);
		}
			
		//infos specifiques
		if ( !$record = DAOcms_documents::instance ()->get ($element->id_helt)){
			throw new HeadingElementInformationNotFoundException ($pPublicId);
		}
		
		//fusion des informations communes et spécifiques
		_ppo ($record)->saveIn ($element);		
	
		//dans le cas d'un lien, on remplace les identifiants public de l'element par celui du lien
		$element->public_id_hei = (isset ($linkPublicId)) ? $linkPublicId : $element->public_id_hei; 
		$element->id_hei = (isset ($linkPublicId)) ? $linkPublicId : $element->public_id_hei; 

		return $element;
	}
	

	/**
	 * Supprime une ou plusieurs pages données en fonction du public_id 
	 * 
	 * Cette fonction supprime toutes les version des pages demandées
	 *
	 * @param int $pArPublicId le ou les identifiants 
	 */
	public function delete ($pArPublicId) {
		HeadingCache::clear ();

		$records = DAOcms_documents::instance ()->findBy(_daoSp ()->addCondition ('public_id_hei', '=', $pArPublicId));
		if(!empty($records)){
			foreach ($records as $record){
				@unlink(COPIX_VAR_PATH.self::DOCUMENT_PATH.$record->file_document);
				DAOcms_documents::instance ()->delete($record -> id_document);
			}
		}

		HeadingCache::clear ();
	}
	/**
	 * Supression par identifiant interne
	 *
	 * @param int $pArId les identifiants des éléments à supprimer
	 */
	public function deleteById ($pArId) {
		HeadingCache::clear ();

		$record = DAOcms_documents::instance ()-> get ($pArId);
		@unlink(COPIX_VAR_PATH.self::DOCUMENT_PATH.$record->file_document);
		DAOcms_documents::instance ()->delete($pArId);

		HeadingCache::clear ();
	}

	/**
	 * Prévisualisation de l'élément
	 *
	 * @param string $pId
	 */
	public function previewById ($pId) {
		$element = $this->getById ($pId);
		return CopixZone::process ('heading|headingelement/headingelementpreview', array (
			'record' => $element,
			'link' => _url ('document|documentfront|ShowDocumentFile', array ('public_id' => $element->public_id_hei)),
			'infos' => array (
				'type' => array ('caption' => 'Type', 'value' => 'Document ' . strtoupper (pathinfo ($element->file_document, PATHINFO_EXTENSION))),
				'weight' => array ('caption' => 'Poids', 'value' => ($element->size_document) ? _filter ('bytesToText')->get ($element->size_document) : '(Inconnue)')
			)
		));
	}
	
	/**
	 * Retourne les éléments qui sont liés à $pPublicId
	 *
	 * @param int $pPublicId Identifiant publique
	 * @return array
	 */
	public function getDependencies ($pPublicId) {
		return array ();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see project/modules/devel/cms3/heading/classes/HeadingElementServices::findGhosts()
	 */
	public function findGhosts (){
		$toReturn['specific'] = _doQuery ('select * from cms_documents where id_document not in(select id_helt from cms_headingelementinformations where type_hei = :type and status_hei <> :status)', array (':type'=>'document', ':status'=>HeadingElementStatus::DELETED));
		$toReturn['general']  = _doQuery ('select * from cms_headingelementinformations where type_hei = :type and id_helt not in (select id_document from cms_documents)', array (':type'=>'document'));
		return $toReturn;
	}

	/**
	 * Retourne la liste des liens morts
	 *
	 * @return array
	 */
	public function findDeadLinks () {
		$toReturn = array ();
		$query = 'SELECT doc.* FROM cms_documents doc, cms_headingelementinformations hei WHERE doc.id_document = hei.id_helt AND status_hei = :status AND hei.type_hei = :type';
		$params = array (':status' => HeadingElementStatus::PUBLISHED, ':type' => 'document');
		$varPath = CopixFile::getRealPath (COPIX_VAR_PATH);
		foreach (_doQuery ($query, $params) as $record) {
			$path = $varPath . DocumentServices::DOCUMENT_PATH . $record->file_document;
			if (!is_file ($path) || !is_readable ($path)) {
				$toReturn[] = array (
					'element' => $this->getById ($record->id_document),
					'linked_public_id_hei' => $record->public_id_hei,
					'error' => 'Le fichier "' . $path . '" n\'existe pas ou n\'a pas les droits de lecture.'
				);
			}
		}
		return $toReturn;
	}
	
	/**
	 * On peut exporter les documents
	 */
	public function canExport(){
		return true;
	}
	
	/**
	 * 
	 * Methode d'export des elements correspondant à la classe de service 
	 */
	public function export($pZipName, $pElement){
		$element = $this->getByPublicId($pElement->public_id_hei);
		$filename = strtr(utf8_decode($element->caption_hei),
				utf8_decode('ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ'), 
				'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$filename = preg_replace('/([^.a-z0-9]+)/i', '_', $filename);
		$filename = $filename.".".pathinfo($element->file_document, PATHINFO_EXTENSION);
		$path = "";
		
		$hierarchy = explode('-', $pElement->hierarchy_hei);
		foreach ($hierarchy as $public_id){
			$folder = _ioClass('heading|headingelement/headingelementinformationservices')->get($public_id);
			if ($folder->public_id_hei && $folder->public_id_hei != $element->public_id_hei){
				$path .= $folder->caption_hei ."/";
			}
		}

		$zip = new CopixZip ();
		$zip->open($pZipName, ZIPARCHIVE::CREATE);
		$zip->addFile(COPIX_VAR_PATH.self::DOCUMENT_PATH.$element->file_document, $path.$filename);
		$zip->close();
	}
}