<?php
/**
 * Gestion des flux rss
 */
class RSSServices extends HeadingElementServices {
	
	//constante de tri par date de création
	const DATE_CREATE_ORDER = 1;
	//constante de tri par date de modification
	const DATE_UPDATE_ORDER = 2;
	//constante de tri =>même tri que dans l'admin
	const ORDER_HEI_ORDER = 3;
	
	/**
	 * Retourne une courte description pour l'affichage détails
	 *
	 * @param int $pIdHelt Identifiant interne
	 * @return string
	 */
	public function getDisplayDescription ($pIdHelt) {
		return "";
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
	 * 
	 * Retourne l'adresse pointée par l'élément
	 *
	 * @param int $pIdHelt Identifiant de l'élément
	 * @return string
	 */
	public function getURL ($pIdHelt) {
		$elem = $this->getById($pIdHelt);
		$params = array ('public_id'=>$elem->public_id_hei);
		
		// on rajoute les groupes de l'utilisateur en md5
		$userGroups = array();
		foreach (_currentUser()->getGroups() as $groupHandler => $groups){
			foreach ($groups as $group => $label){
				$userGroups[] = md5($groupHandler.'~'.$group);
			}
		}
		$params['groups'] = join('-', $userGroups);
		return _url ('heading||', $params);
	}
	
	/**
	 * Ajoute un flux rss
	 *
	 * @param mixed $pDescription Informations sur le flux
	 */
	public function insert ($pDescription){
		HeadingCache::clear ();
		$rssDescription = _ppo ($pDescription);

		CopixDB::begin ();
		try {
			//création de l'enregistrement
			$record = DAORecordcms_rss::create ()->initFromDbObject ($rssDescription);
			
			DAOcms_rss::instance ()->insert ($record);

			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $rssDescription[$propertyName];
			}
			$record->id_helt = $record->id_rss;
			$record->type_hei = 'rss';
			$record->caption_hei = $rssDescription['caption_hei'];
			$record->parent_heading_public_id_hei = $rssDescription['parent_heading_public_id_hei'];
			$record->element_types_rss = $rssDescription['element_types_rss'];
			$record->recursive_flag = $rssDescription['recursive_flag'];
			_ioClass ('heading|HeadingElementInformationServices')->insert ($record);
		
			//on met maintenant a jour l'élément $record avec les informations publiques mises à jour
			DAOcms_rss::instance ()->update ($record);

			//Application des changements
			_ppo ($record)->saveIn ($pDescription);
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e; 
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	/**
	 * Mise à jour d'un flux
	 * 
	 * @param array / object $pDescription
	 */
	public function update ($pDescription){
		HeadingCache::clear ();
		$rssDescription = _ppo ($pDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($rssDescription['id_rss']);

			//on met a jour les données spécifiques			
			$record->description_hei = $rssDescription['description_hei'];
			$record->heading_public_id_rss = $rssDescription['heading_public_id_rss'];
			$record->order_rss = $rssDescription['order_rss'];
			$record->element_types_rss = $rssDescription['element_types_rss'];
			$record->recursive_flag = $rssDescription['recursive_flag'];
			DAOcms_rss::instance ()->update ($record);
			
			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $rssDescription[$propertyName];
			}
			_ioClass ('heading|HeadingElementInformationServices')->update ($record);

			//Application des changements
			_ppo ($record)->saveIn ($pDescription);
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
	 * @param object $pDescription la description du flux dont on souhaite obtenir une nouvelle version
	 */
	public function version ($pDescription){
		HeadingCache::clear ();
		$rssDescription = _ppo ($pDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($rssDescription['id_rss']);

			//on met a jour les données spécifiques			
			$record->description_hei = $rssDescription['description_hei'];
			$record->heading_public_id_rss = $rssDescription['heading_public_id_rss'];
			$record->order_rss = $rssDescription['order_rss'];
			$record->element_types_rss = $rssDescription['element_types_rss'];
			$record->recursive_flag = $rssDescription['recursive_flag'];
			DAOcms_rss::instance ()->insert ($record);

			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $rssDescription[$propertyName];
			}
			$record->id_helt = $record->id_rss;			
			_ioClass ('heading|HeadingElementInformationServices')->version ($record, $rssDescription['id_rss']);

			//Application des changements
			_ppo ($record)->saveIn ($pDescription);
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	/* 
	 * Copie du flux
	 * @param array / object $pDocumentDescription
	 */
	public function copy ($pPublicId, $pHeading){
		HeadingCache::clear ();
		CopixDB::begin ();
		try {
			$record = $this->getByPublicId($pPublicId);
			$record->public_id_hei = null;
			$record->url_id_hei = $record->url_id_hei ? $record->url_id_hei . ' (copie)' : $record->url_id_hei;
			$record->parent_heading_public_id_hei = $pHeading;
			
			DAOcms_rss::instance ()->insert ($record);

			$record->id_helt = $record->id_rss;		
			$record->caption_hei = $record->caption_hei . ' (copie)';
			$record->display_order_hei = _ioClass ('heading|HeadingElementInformationServices')->getNextDisplayOrderValue ($pHeading);		

			_ioClass ('heading|HeadingElementInformationServices')->insert ($record);
			
			DAOcms_rss::instance ()->update ($record);
			
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e; 
		}
		CopixDB::commit ();
		HeadingCache::clear ();
		return $record->public_id_hei;
	}

	/**
	 * Récupère un enregistrement par son identifiant interne
	 * 
	 * @param int $pIdHelt Identifiant interne de l'élément à récupérer 
	 */
	public function getById ($pIdHelt){
		//on vérifie que l'élément existe
		if (! $record = DAOcms_rss::instance ()->get ($pIdHelt)){
			throw new HeadingElementInformationNotFoundException ($pIdHelt);
		}
		//on récupère les informations "communes"
		$element = _ioClass ('heading|HeadingElementInformationServices')->getById ($pIdHelt, 'rss');
		//fusion des informations communes et spécifiques
		_ppo ($record)->saveIn ($element);

		return $element;
	}
	
	/**
	 * Recupere un enregistrement par son identifiant public
	 *
	 * @param int $pPublicId
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
		if ( !$record = DAOcms_rss::instance ()->get ($element->id_helt)){
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
		DAOcms_rss::instance ()->deleteBy (_daoSp ()->addCondition ('public_id_hei', '=', $pArPublicId));
		DAOcms_rss_headingelementinformations::instance ()->deleteBy (_daoSp ()->addCondition ('rss_public_id', '=', $pArPublicId));
		HeadingCache::clear ();
	}

	/**
	 * Supression par identifiant interne
	 *
	 * @param int $pArId les identifiants des éléments à supprimer
	 */
	public function deleteById ($pArId) {
		DAOcms_rss::instance ()->deleteBy (_daoSp ()->addCondition ('id_rss', '=', $pArId));
		DAOcms_rss_headingelementinformations::instance ()->deleteBy (_daoSp ()->addCondition ('id_rss', '=', $pArId));
		HeadingCache::clear ();
	}
	
	/**
	 * Retourne les flux faisant référence à l'element de publicId $pPublicId
	 *
	 * @param int $pPublicId
	 */
	public function getDependencies ($pPublicId){
		return array ();
	}
	
	public function previewById ($pId) {
		return "<a target='_blank' href='"._url('cms_rss|rssfront|preview', array('id_rss'=>$pId))."'>Voir le flux</a>";
	}
	
	/**
	 * Retourne la liste des articles de la rubrique passé en paramètre
	 *
	 * @param int $pHeading
	 * @param int $pOrder
	 * @param boolean $pRecursive
	 * @return array
	 */
	public function getListElement ($pHeading, $types = array('article'),$pOrder = self::ORDER_HEI_ORDER, $pRecursive = false){
		$filterTypes = ''; 
		foreach ($types as $type){
			$filterTypes .= "'$type',";
		}
		$filterTypes = substr($filterTypes, 0, strlen($filterTypes) -1 );
		$query = "SELECT * FROM cms_headingelementinformations
				WHERE type_hei in ($filterTypes) 
				AND hierarchy_hei LIKE :heading
				AND status_hei = ".HeadingElementStatus::PUBLISHED;

		$queryRecursive = '';
		$queryOrder  ='';
		
		$params = array(':heading'=>'%'.$pHeading.'-%');
		
		if (!$pRecursive){
			$queryRecursive = " AND parent_heading_public_id_hei = :headingId";
			$params[':headingId']=$pHeading;
		}
		
		switch ($pOrder){
			case self::DATE_CREATE_ORDER :
				$queryOrder  = " ORDER by date_create_hei";
				break;
			case self::DATE_UPDATE_ORDER :
				$queryOrder  = " ORDER by date_update_hei";
				break;
			default :
				$queryOrder  = " ORDER by order_hei";
		}
		
		$query.= $queryRecursive.$queryOrder;
		
		$results = _doQuery($query, $params);	
		return $results; 
	}
	
	/**
	 * Retourne la liste des flux à afficher dans les header
	 *
	 * @return unknown
	 */
	public function getListFlux(){
		$query = "SELECT DISTINCT * FROM cms_headingelementinformations
				LEFT JOIN cms_rss ON id_helt = id_rss
				WHERE status_hei = ".HeadingElementStatus::PUBLISHED." 
				AND type_hei = 'rss'
				ORDER BY caption_hei";
		
		return _doQuery($query);
	}
	
	/**
	 * Retourne un tableau de la liste des flux pour un element de publicId donné
	 *
	 * @param int $pPublic_id
	 * @return array
	 */
	public function getHeadingElementListFlux($pPublic_id){
		$arElementFlux = DAOcms_rss_headingelementinformations::instance ()->findBy(_daoSP()->addCondition('headingelement_public_id', '=', $pPublic_id))->fetchAll();
		$arFlux = array();
		foreach ($arElementFlux as $headingelement_flux){
			$arFlux[] = $headingelement_flux->id_rss;
		}
		return $arFlux;
	}
	
	/**
	 * Calcul des flux hérités.
	 *
	 * @param int $pPublicId
	 */
	public function getInheritedHeadingElementListFlux ($pPublicId, &$pInheretedPublicId){
		$element = _ioClass('heading|headingelementinformationservices')->get($pPublicId);
		//chemin pour arriver a l'élément.
		$path = explode('-', $element->hierarchy_hei);
		$path = array_reverse($path);
		foreach ($path as $position=>$elementId){
			$arListeFlux = $this->getHeadingElementListFlux($elementId);
			if (!empty($arListeFlux)){
				$pInheretedPublicId = $elementId;
				return $arListeFlux;
			}	
		}
		return array();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see project/modules/devel/cms3/heading/classes/HeadingElementServices::findGhosts()
	 */
	public function findGhosts (){
		$toReturn['specific'] = _doQuery ('select * from cms_rss where id_rss not in(select id_helt from cms_headingelementinformations where type_hei = :type and status_hei <> :status)', array (':type'=>'rss', ':status'=>HeadingElementStatus::DELETED));
		$toReturn['general']  = _doQuery ('select * from cms_headingelementinformations where type_hei = :type and id_helt not in (select id_rss from cms_rss)', array (':type'=>'rss'));
		return $toReturn;
		
	}	
}