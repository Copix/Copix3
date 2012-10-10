<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Services sur les images  
 * 
 * @package cms
 * @subpackage images
 */
class ImageServices extends HeadingElementServices {

	/**
	 * Emplacement ou seront sauvegardées les images dans COPIX_VAR
	 */
	const IMAGE_PATH = "images/";
	
	/**
	 * Retourne une courte description pour l'affichage détails
	 *
	 * @param int $pIdHelt Identifiant interne
	 * @return string
	 */
	public function getDisplayDescription ($pIdHelt) {
		$image = $this->getById ($pIdHelt);
		$imageInfos = CopixImage::load (COPIX_VAR_PATH . ImageServices::IMAGE_PATH . $image->file_image);
		if ($imageInfos == null) {
			$toReturn = '(Image non trouvée)';
		} else {
			$toReturn = 'Type : ' . substr (CopixFile::extractFileExt ($image->file_image), 1);
			$toReturn .= ' - ' . $imageInfos->getWidth () . 'x' . $imageInfos->getHeight ();
			$toReturn .= ' - Taille : ' . (is_numeric($image->size_image) && $image->size_image > 0 ? _filter ('bytesToText')->get ($image->size_image) : " - ");
		}
		return $toReturn;
	}
	
	/**
	 * 
	 * Retourne la description de l'élément
	 * @param int $pIdHelt
	 */
	public function getDescription ($pIdHelt){
		$element = $this->getById ($pIdHelt);
		return $element->description_hei;
	}
	
	/**
	 * Création d'une nouvelle image
	 * @param array / object $pImageDescription
	 */
	public function insert ($pImageDescription){
		HeadingCache::clear ();
		$imageDescription = _ppo ($pImageDescription);

		CopixDB::begin ();
		try {
			$dao = DAOcms_images::instance ();
			
			//création de l'enregistrement
			$record = DAORecordcms_images::create ()->initFromDbObject ($imageDescription);
			$dao->insert ($record);

			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $imageDescription[$propertyName];
			}
			$record->id_helt = $record->id_image;
			$record->type_hei = 'image';

			//dans $record->id_helt on a le nouvel identifiant
			_ioClass ('heading|HeadingElementInformationServices')->insert ($record);

			$filename = CopixUrl::escapeSpecialChars($imageDescription['caption_hei'], true);
			$record->file_image = $record->public_id_hei."_".$record->version_hei."_".$filename.".".pathinfo($record->file_image, PATHINFO_EXTENSION);

			//on met maintenant a jour l'élément $record avec les informations publiques mises à jour
			$dao->update ($record);

			//Application des changements
			_ppo ($record)->saveIn ($pImageDescription);
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
	 * @param array / object $pImageDescription
	 */
	public function update ($pImageDescription){
		HeadingCache::clear ();
		$imageDescription = _ppo ($pImageDescription);
        CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($imageDescription['id_image']);
			//on met a jour les données spécifiques
			$record->description_hei = $imageDescription['description_hei'];
			if ($imageDescription['file_image']) {
				$filename = CopixUrl::escapeSpecialChars($imageDescription['caption_hei'], true);
                $sFileImage = $record->public_id_hei."_".$record->version_hei."_".$filename.".".pathinfo($imageDescription['file_image'], PATHINFO_EXTENSION);
                // Suppression de l'ancienne image
                if ($sFileImage != $record->file_image) {
					try {
						CopixFile::delete(COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$record->file_image);
					} catch (Exception $e) { }
                }
                $record->file_image = $sFileImage;

                // mise à jour de la taille du fichier
                if($imageDescription->size_image) {
                    $record->size_image = $imageDescription->size_image;
                }
			}
            
			DAOcms_images::instance ()->update ($record);
				
			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $imageDescription[$propertyName];
			}
			_ioClass ('heading|HeadingElementInformationServices')->update ($record);
            //Application des changements
			_ppo ($record)->saveIn ($pImageDescription);
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
	 * @param object $pImageDescription la description de la page dont on souhaite obtenir une nouvelle version
	 */
	public function version ($pImageDescription){
		HeadingCache::clear ();
		$imageDescription = _ppo ($pImageDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($imageDescription['id_image']);

			//on met a jour les données spécifiques
			$record->description_hei = $imageDescription['description_hei'];
			$newVersion = _ioClass ('heading|HeadingElementInformationServices')->getNextVersion ($record->public_id_hei);
			
			if($imageDescription['file_image']){			
				$record->file_image = $record->public_id_hei."_".$newVersion."_".$record->caption_hei.".".pathinfo($imageDescription['file_image'], PATHINFO_EXTENSION);
			}
			else{
				$infos = explode('_', $record->file_image);
				$public_id_hei = $infos[0];
				$version = $infos[1];
				$caption = implode('_', array_slice($infos, 2));
	
				$filename = $public_id_hei."_".$newVersion."_".$caption;
				copy (COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$record->file_image ,COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$filename);
			
				$record->file_image = $filename;
			}
			DAOcms_images::instance ()->insert ($record);

			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $imageDescription[$propertyName];
			}
			$record->id_helt = $record->id_image;
			_ioClass ('heading|HeadingElementInformationServices')->version ($record, $imageDescription['id_image']);

			//Application des changements
			_ppo ($record)->saveIn ($pImageDescription);
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	/* Création d'un nouveau document
	 * @param array / object $pDocumentDescription
	 */
	public function copy ($pPublicId, $pHeading){
		HeadingCache::clear ();
		CopixDB::begin ();
		try {
			$dao = DAOcms_images::instance ();

			$image = $this->getByPublicId($pPublicId);
			$image->public_id_hei = null;
			$image->url_id_hei = $image->url_id_hei ? $image->url_id_hei . ' (copie)' : $image->url_id_hei;
			$image->parent_heading_public_id_hei = $pHeading;
			
			$dao->insert ($image);

			$image->id_helt = $image->id_image;		
			$image->caption_hei = $image->caption_hei . ' (copie)';		
			$image->display_order_hei = _ioClass ('heading|HeadingElementInformationServices')->getNextDisplayOrderValue ($pHeading);

			_ioClass ('heading|HeadingElementInformationServices')->insert ($image);
				
			$oldFileName = $image->file_image;
			$infos = explode('_', $image->file_image);
			$public_id_hei = $infos[0];
			$version = $infos[1];
			$filename = implode('_', array_slice($infos, 2));
			
			$image->file_image = $image->public_id_hei."_".$image->version_hei."_".$filename;
			@copy (COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$oldFileName, COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$image->file_image);
			
			$dao->update ($image);
			
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e; 
		}
		CopixDB::commit ();
		HeadingCache::clear ();
		return $image->public_id_hei;
	}

	/**
	 * Récupère un enregistrement par son identifiant interne
	 *
	 * @param int $pIdHelt Identifiant interne de l'élément à récupérer
	 */
	public function getById ($pIdHelt){
		//on vérifie que l'élément existe
		if (! $record = DAOcms_images::instance ()->get ($pIdHelt)){
			throw new HeadingElementInformationNotFoundException ($pIdHelt);
		}
		//on récupère les informations "communes"
		$element = _ioClass ('heading|HeadingElementInformationServices')->getById ($pIdHelt, 'image');
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
			$lien = _ioClass ('heading|linkservices')->getByPublicId ($pPublicId);
			$linkPublicId = $pPublicId; 
			$pPublicId = $lien->linked_public_id_hei;
			if(is_null($pPublicId)){
				throw new HeadingElementInformationException ($pPublicId);
			}
			$element = _ioClass ('heading|HeadingElementInformationServices')->get($pPublicId);
		}
		
		//on vérifie que l'élément existe
		if ( !$record = DAOcms_images::instance ()->get ($element->id_helt)){
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
	 * Supprime une ou plusieurs images
	 *
	 * Cette fonction supprime toutes les versions des images demandées
	 *
	 * @param int $pArPublicId le ou les identifiants
	 */
	public function delete ($pArPublicId) {
		HeadingCache::clear ();
		$dao = DAOcms_images::instance ();
		foreach ($dao->findBy(_daoSp ()->addCondition ('public_id_hei', '=', $pArPublicId)) 
					as $record){
			@unlink (COPIX_VAR_PATH.self::IMAGE_PATH.$record->file_image);
			$dao->delete ($record->id_image);
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
		$record = DAOcms_images::instance ()->get ($pArId);
		@unlink (COPIX_VAR_PATH.self::IMAGE_PATH.$record -> file_image);
		DAOcms_images::instance ()->delete ($pArId);
		HeadingCache::clear ();
	}

	/**
	 * Prévisualisation de l'élément
	 *
	 * @param int $pId l'identifiant interne de l'élément a prévisualiser
	 */
	public function previewById ($pId) {
		$element = $this->getById ($pId);
		$infos = array ('type' => array ('caption' => 'Type', 'value' => 'Image ' . strtoupper (pathinfo ($element->file_image, PATHINFO_EXTENSION))));

		$oImage = CopixImage::load (COPIX_VAR_PATH.ImageServices::IMAGE_PATH . $element->file_image);
		if ($oImage == null) {
			$exists = false;
			$infos['preview'] = array ('caption' => 'Miniature', 'value' => '<font color="red">Image non trouvée</font>');
		} else {
			$exists = true;
			// Taille max de l'image
			$iMaxWidth = $iMaxHeight = 200;
			// Si une image est plus petite que la taille max, ne pas l'agrandir plus de 4 fois
			$iMaxZoom = 4;
			if($iMaxWidth / $oImage->getWidth() > $iMaxZoom) {
				$iMaxWidth = $oImage->getWidth() * $iMaxZoom;
			}
			if($iMaxHeight / $oImage->getHeight() > $iMaxZoom) {
				$iMaxHeight = $oImage->getHeight() * $iMaxZoom;
			}

			echo _tag('mootools', array ('plugins'=>"smoothbox"));
			$infos['size'] = array ('caption' => 'Dimensions', 'value' => $oImage->getWidth () . ' x ' . $oImage->getHeight () . ' px');
			$infos['weight'] = array ('caption' => 'Poids', 'value' => ($element->size_image) ? _filter ('bytesToText')->get ($element->size_image) : '(Inconnue)');
			$extension = pathinfo($element->file_image, PATHINFO_EXTENSION);
			$infos['preview'] = array (
				'caption' => 'Miniature',
				'value' => '<a href="'._url('images|imagefront|GetImage', array('id_image'=>$element->id_helt, 'extension'=>'.'.$extension)).'" class="smoothbox" >
						<img src="' . _url ('images|imagefront|getImage', array ('resizeIfNecessary'=>1,'id_image' => $pId, 'width' => $iMaxWidth, 'height' => $iMaxHeight, 'keepProportions' => 1, 'v'=>uniqid())) . '" /></a>'
			);
		}

		return CopixZone::process ('heading|headingelement/headingelementpreview', array (
			'record' => $element,
			'infos' => $infos,
			'canShow' => $exists,
			'link' => _url ('images|imagefront|getImage', array ('id_image' => $pId))
		));
	}
	
	/**
	 * Retourne les images faisant référence à l'element de publicId $pPublicId
	 *
	 * @param int $pPublicId
	 */
	public function getDependencies ($pPublicId){
		return array ();
	}

	/**
	 * Retourne le chemin physique de l'image avec son identifiant public
	 *
	 * @param int $pPublicId Identifiant public
	 * @return string
	 */
	public function getPathByPublicId ($pPublicId) {
		return COPIX_VAR_PATH . ImageServices::IMAGE_PATH . $this->getByPublicId ($pPublicId)->file_image;
	}

	/**
	 * Permet de changer les actions (couper, copier, etc) possibles sur un élément
	 * /!\ A ne pas appeler directement, passer par HeadingElementInformationServices::getActions ()
	 *
	 * @param stdClass $pElement Enregistrement de l'élément
	 * @param stdClass $pActions Actions déja prédéfinies par HeadingElementInformationServices::getActions
	 */
	public function getActions ($pElement, $pActions) {
		$element = $this->getById ($pElement->id_helt);
		$pActions->show = file_exists (COPIX_VAR_PATH.ImageServices::IMAGE_PATH . $element->file_image);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see project/modules/devel/cms3/heading/classes/HeadingElementServices::findGhosts()
	 */
	public function findGhosts (){
		$toReturn['specific'] = _doQuery ('select * from cms_images where id_image not in(select id_helt from cms_headingelementinformations where type_hei = :type and status_hei <> :status)', array (':type'=>'image', ':status'=>HeadingElementStatus::DELETED));
		$toReturn['general']  = _doQuery ('select * from cms_headingelementinformations where type_hei = :type and id_helt not in (select id_image from cms_images)', array (':type'=>'image'));
		return $toReturn;
	}

	/**
	 * Retourne la liste des liens morts
	 *
	 * @return array
	 */
	public function findDeadLinks () {
		$toReturn = array ();
		$query = 'SELECT img.* FROM cms_images img, cms_headingelementinformations hei WHERE img.id_image = hei.id_helt AND status_hei = :status AND hei.type_hei = :type';
		$params = array (':status' => HeadingElementStatus::PUBLISHED, ':type' => 'image');
		$varPath = CopixFile::getRealPath (COPIX_VAR_PATH) . self::IMAGE_PATH;
		foreach (_doQuery ($query, $params) as $record) {
			$path = $varPath . $record->file_image;
			if (!is_file ($path) || !is_readable ($path)) {
				$toReturn[] = array (
					'element' => $this->getById ($record->id_image),
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
		$filename = $element->caption_hei ? strtr(utf8_decode($element->caption_hei),
				utf8_decode('ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ'), 
				'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy') : "(sans titre)";
		$filename = preg_replace('/([^.a-z0-9]+)/i', '_', $filename);
		$filename = $filename.".".pathinfo($element->file_image, PATHINFO_EXTENSION);
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
		$zip->addFile(COPIX_VAR_PATH.self::IMAGE_PATH.$element->file_image, $path.$filename);
		$zip->close();
	}
}