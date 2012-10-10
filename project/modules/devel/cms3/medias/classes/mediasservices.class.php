<?php




/**
 * Exceptions si l'extension du fichier média n'est pas reconnue
 */
class MediaExtensionNotSupportedException extends CopixException  {
	
	/**
	 * Redéfini le message d'erreur à afficher
	 * @param $pStringFileName
	 * @return unknown_type
	 */
	function __construct ($pStringFileName) {
		$errorMsg = 'L\'extension du fichier média : ' . $pStringFileName . ' n\'est pas supportée par l\'application';
		parent::__construct ($errorMsg);
	}
}

/**
 * Service des médias
 */
class MediasServices extends HeadingElementServices {
	
	const MEDIA_PATH = 'medias';
	
	//Types de médias correspondant au type_hei de heading element
	const MEDIA_TYPE_FLASH = 'flash';
	const MEDIA_TYPE_VIDEO = 'video';
	const MEDIA_TYPE_AUDIO = 'audio';

	/**
	 * Retourne une courte description pour l'affichage détails
	 *
	 * @param int $pIdHelt Identifiant interne
	 * @return string
	 */
	public function getDisplayDescription ($pIdHelt) {
		$element = $this->getById ($pIdHelt);
		$toReturn = 'Type : ' . $element->type_hei . ' - ';
		$toReturn .= (strlen ($element->caption_hei) > 30) ? substr ($element->caption_hei, 0, 30) : $element->caption_hei;
		return $toReturn;
	}
	
	
	/**
	 * 
	 * Retourne la description de l'élément
	 * @param int $pIdHelt
	 */
	public function getDescription ($pIdHelt){
		$element = $this->getById ($pIdHelt);
		return $element->caption_hei;
	}
	
	/**
	 * Tableau de correspondance entre les différents types de médias et les extension de fichiers associées
	 * @var array
	 */
	private static $_arCorrespondaceTypeMediaExtension = 
		array (
			self::MEDIA_TYPE_FLASH => array('swf'),
			self::MEDIA_TYPE_VIDEO => array('flv', 'mp4', 'xml'),
			self::MEDIA_TYPE_AUDIO => array('mp3')
		);
	
	/**
	 * Création d'un nouveau media
	 * @param array / object $pMediaDescription
	 */
	public function insert ($pMediaDescription){
		HeadingCache::clear ();
		$mediaDescription = _ppo ($pMediaDescription);

		CopixDB::begin ();
		try {
			//création de l'enregistrement
			$record = DAORecordcms_medias::create ()->initFromDbObject ($mediaDescription);

			DAOcms_medias::instance ()->insert ($record);

			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $mediaDescription[$propertyName];
			}
			$record->id_helt = $record->id_media;
			//Calcul du type en fonction de l'extension du fichier
			$record->type_hei = self::getMediaTypeByFileName($record->file_media);

			//dans $record->id_helt on a le nouvel identifiant
			_ioClass ('heading|HeadingElementInformationServices')->insert ($record);
			
			$filename = strtr(utf8_decode($record->caption_hei),
				utf8_decode('ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ'), 
				'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
			$filename = preg_replace('/([^.a-z0-9]+)/i', '_', $filename);
			$record->file_media = $record->public_id_hei."_".$record->version_hei."_".$filename.".".pathinfo($record->file_media, PATHINFO_EXTENSION);

			//on met maintenant a jour l'élément $record avec les informations publiques mises à jour
			DAOcms_medias::instance ()->update ($record);

			//Application des changements
			_ppo ($record)->saveIn ($pMediaDescription);
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
	 * @param array / object $pMediaDescription
	 */
	public function update ($pMediaDescription){
		HeadingCache::clear ();
		$mediaDescription = _ppo ($pMediaDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($mediaDescription['id_media']);
			//on met a jour les données spécifiques			
			$record->description_hei = $mediaDescription['description_hei'];
			if($mediaDescription['file_media']){
				$filename = strtr(utf8_decode($mediaDescription['caption_hei']),
					utf8_decode('ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ'), 
					'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
				$filename = preg_replace('/([^.a-z0-9]+)/i', '_', $filename);
				$record->file_media = $record->public_id_hei."_".$record->version_hei."_".$filename.".".pathinfo($record->file_media, PATHINFO_EXTENSION);
			}
            if($mediaDescription['image_media']) {
                $record->image_media = $mediaDescription['image_media'];
            }

			DAOcms_medias::instance ()->update ($record);
			
			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $mediaDescription[$propertyName];
			}
			_ioClass ('heading|HeadingElementInformationServices')->update ($record);

			//Application des changements
			_ppo ($record)->saveIn ($pMediaDescription);
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
	 * @param object $pMediaDescription la description de la page dont on souhaite obtenir une nouvelle version
	 */
	public function version ($pMediaDescription){
		HeadingCache::clear ();
		$mediaDescription = _ppo ($pMediaDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($mediaDescription['id_media']);

			//on met a jour les données spécifiques			
			$record->description_img = $mediaDescription['description_hei'];
			if($mediaDescription['file_media']){
				$record->file_media = $mediaDescription['file_media'];
			}
			DAOcms_medias::instance ()->insert ($record);

			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $mediaDescription[$propertyName];
			}
			$record->id_helt = $record->id_media;			
			_ioClass ('heading|HeadingElementInformationServices')->version ($record, $mediaDescription['id_media']);

			//Application des changements
			_ppo ($record)->saveIn ($pMediaDescription);
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
			$media = $this->getByPublicId($pPublicId);
			$media->public_id_hei = null;
			$media->url_id_hei = $media->url_id_hei ? $media->url_id_hei . ' (copie)' : $media->url_id_hei;
			$media->parent_heading_public_id_hei = $pHeading;
			
			DAOCms_Medias::instance ()->insert ($media);

			$media->id_helt = $media->id_media;		
			$media->caption_hei = $media->caption_hei . ' (copie)';		
			$media->display_order_hei = _ioClass ('heading|HeadingElementInformationServices')->getNextDisplayOrderValue ($pHeading);

			_ioClass ('heading|HeadingElementInformationServices')->insert ($media);
			
			DAOCms_Medias::instance ()->update ($media);
			
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e; 
		}
		CopixDB::commit ();
		HeadingCache::clear ();
		return $media->public_id_hei;
	}

	/**
	 * Récupère un enregistrement par son identifiant interne
	 * 
	 * @param int $pIdHelt Identifiant interne de l'élément à récupérer 
	 */
	public function getById ($pIdHelt){
		//on vérifie que l'élément existe
		if (! $record = DAOcms_medias::instance ()->get ($pIdHelt)){
			throw new HeadingElementInformationNotFoundException ($pIdHelt);
		}
		//on récupère les informations "communes"
		$element = _ioClass ('heading|HeadingElementInformationServices')->getById ($pIdHelt, self::getMediaTypeByFileName($record->file_media));
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
		$element = _ioClass ('heading|HeadingElementInformationServices')->get ($pPublicId);
		
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
		if ( !$record = DAOcms_medias::instance ()->get ($element->id_helt)){
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
		$records = DAOcms_medias::instance ()->findBy(_daoSp ()->addCondition ('public_id_hei', '=', $pArPublicId));
		if(!empty($records)){
			foreach ($records as $record){
				@unlink(COPIX_VAR_PATH.self::MEDIA_PATH.DIRECTORY_SEPARATOR.$record -> file_media);
				DAOcms_medias::instance ()->delete($record -> id_media);
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
		DAOcms_medias::instance ()->deleteBy (_daoSp ()->addCondition ('id_media', '=', $pArId));
		HeadingCache::clear ();
	}
	
	/**
	 * Prévisualisation de l'élément
	 *
	 * @param string $pId
	 */
	public function previewById ($pId){	
		$element = $this->getById($pId);
		$infos['weight'] = array ('caption' => 'Poids', 'value' => ($element->size_media) ? _filter ('bytesToText')->get ($element->size_media) : '(Inconnue)');
		//TODO
		$infos['preview'] = array (
				'caption' => 'Média',
				'value' => CopixZone::process ('medias|mediaformview', array('admin'=>true, 'options'=>array(), 'media'=>$element, 'mediaType'=> $element->type_hei, 'identifiantFormulaire' => 'mediapreview'.$element->id_media))
			);
		return CopixZone::process ('heading|headingelement/headingelementpreview', array (
			'record' => $element,
			'infos' => $infos,
			'link' => _url("medias|mediafront|getMedia", array('id_media'=>$pId, 'content_disposition' => 'attachement'))
		));
	}		
	
	/**
	 * Renvoit le type de média (flash, vidéo...) en fonction du nom de fichier
	 * en se bassant sur l'extension
	 * @param $pStringFileName
	 * @return String
	 */
	public static function getMediaTypeByFileName($pStringFileName) {
		
		$fileExtension = pathinfo($pStringFileName, PATHINFO_EXTENSION);
		
		foreach (self::$_arCorrespondaceTypeMediaExtension as $mediaType => $arExtensions) {
			if (in_array($fileExtension, $arExtensions)) {
				return $mediaType;
			}
		}
		throw new MediaExtensionNotSupportedException ($pStringFileName);
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
		$toReturn['specific'] = _doQuery ('select * from cms_medias where id_media not in(select id_helt from cms_headingelementinformations where (type_hei = :type or type_hei = :type_2) and status_hei <> :status)', array (':type'=>'flash', ':type_2'=>'video', ':status'=>HeadingElementStatus::DELETED));
		$toReturn['general']  = _doQuery ('select * from cms_headingelementinformations where (type_hei = :type or type_hei = :type_2) and id_helt not in (select id_media from cms_medias)', array (':type'=>'flash', ':type_2'=>'video'));
		return $toReturn;
	}

	/**
	 * Retourne la liste des liens morts
	 *
	 * @return array
	 */
	public function findDeadLinks () {
		$toReturn = array ();
		$query = 'SELECT cms_medias.* FROM cms_medias, cms_headingelementinformations hei WHERE cms_medias.id_media = hei.id_helt AND status_hei = :status AND hei.type_hei = :type';
		$params = array (':status' => HeadingElementStatus::PUBLISHED, ':type' => 'media');
		$varPath = CopixFile::getRealPath (COPIX_VAR_PATH) . self::MEDIA_PATH . DIRECTORY_SEPARATOR;
		foreach (_doQuery ($query, $params) as $record) {
			$path = $varPath . $record->file_media;
			if (!is_file ($path) || !is_readable ($path)) {
				$toReturn[] = array (
					'element' => $this->getById ($record->id_media),
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
		$filename = $filename.".".pathinfo($element->file_media, PATHINFO_EXTENSION);
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
		$zip->addFile(COPIX_VAR_PATH.self::MEDIA_PATH.DIRECTORY_SEPARATOR.$element->file_media, $path.$filename);
		$zip->close();
	}
}