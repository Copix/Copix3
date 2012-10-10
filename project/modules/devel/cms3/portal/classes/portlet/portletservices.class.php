<?php
/**
 * @package    standard
 * @subpackage portal
 * @author     Gérald CROËS, Alexandre JULIEN
 * @copyright  2001-2008 CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Services sur les portlets
 */
class PortletServices extends HeadingElementServices {

	/**
	 * Retourne une courte description pour l'affichage détails
	 *
	 * @param int $pIdHelt Identifiant interne
	 * @return string
	 */
	public function getDisplayDescription ($pIdHelt) {
		$element = $this->getById ($pIdHelt);
		$toReturn = 'Type : ' . strtolower (substr ($element->type_portlet, 7)) . ' - ';
		$toReturn .= (strlen ($element->description_hei) > 20) ? substr ($element->description_hei, 0, 20) . '...' : $element->description_hei;
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
	 * Retourne l'identifiant de la zone capable de prendre en charge
	 *  l'affichage / modification de la portlet
	 *
	 * @return String : libellé du portlet
	 */
	public function getPortletInformations ($pId) {
		$arData = $this->getList();
		if (!array_key_exists ($pId, $arData)){
			throw new CopixException ('La portlet de type '.$pId.' est introuvable');
		}
		return $arData[$pId];
	}
	
	/**
	 * retourne un tableau associatif avec en clef l'identifiant de la portlet en valeur
	 * le libellé traduit
	 * 
	 * @return array : liste des libellés
	 */
	public function getList () {
		$xml = CopixModule::getParsedModuleInformation ("portal_PortletTypes", "/moduledefinition/registry/entry[@id='Portlet']/*", array ($this, 'getPortletsFromXML'));
		$captions = array ();
		foreach ($xml as $name => $infos) {
			$captions[] = $infos['caption'];
		}
		array_multisort ($captions, $xml);
		return $xml;
	}
	
	/**
	 * Utilisé par CopixModule::getParsedModuleInformation pour décoder les informations sur les portlets dans le XML 
	 *
	 * @param SimpleXMLElement $moduleNode
	 * @return array
	 */
	public function getPortletsFromXML ($moduleNode) {
		$arData = array ();
		foreach ($moduleNode as $moduleName=>$moduleNodes){
			foreach ($moduleNodes as $node){
				if ($node->getName () === 'type') {
					$id = _toString($node['id']);
					$arData[$id] = array ();

					if (_toString($node['caption']) !== '') {
						$arData[$id]['caption'] = _toString($node['caption']);
					} elseif (_toString($node['captioni18n']) !== '') {
						$arData[$id]['caption'] = CopixI18N::get(_toString($node['captioni18n']));
					}
					if (_toString($node['portlettype']) !== '') {
						$arData[$id]['portlettype'] = _toString($node['portlettype']);
					} 
					if (_toString($node['icon']) !== '') {
						$arData[$id]['icon'] = _toString($node['icon']);
					} 
					if (_toString($node['group']) !== '') {
						$arData[$id]['group'] = _toString($node['group']);
					} 
					if (_toString($node['description']) !== '') {
						$arData[$id]['description'] = _toString($node['description']);
					} 
					$arData[$id]['module'] = $moduleName;
				}
			}
		}
		return $arData;
	}

	/**
	 * Création d'une portlet sans contenu
	 * 
	 * @param string $pPortletType le type de la portlet a créer : image, menu, text
	 * @return Portlet
	 */
	public function create ($pPortletType){
		$informations = $this->getPortletInformations ($pPortletType);
		return _class ($informations['module'].'|Portlet'.$pPortletType);
	}
	
	/**
	 * Création d'une portlet sans contenu
	 * 
	 * @param string $pPortletType le type de la portlet a créer : PortletText, PortletImage
	 * @return Portlet
	 */
	public function createByPortletType ($pPortletType){
		$informations = null;
		foreach ($this->getList() as $portletInfo){
			if ($portletInfo['portlettype'] == $pPortletType){
				$informations = $portletInfo;
			}
		}
		return ($informations != null) ? _class ($informations['module'].'|'.$pPortletType) : null;
	}
	

	/**
	 * Création d'une nouvelle portlet
	 * @param array / object $pPortletDescription
	 */
	public function insert ($pPortletDescription, $pInsertIntoCurrentTransaction = false){
		HeadingCache::clear ();
		$portletDescription = _ppo ($pPortletDescription);

		if (!$pInsertIntoCurrentTransaction){
			CopixDB::begin ();
		}
		try {
			//on récupère l'enregistrement actuel
			$record = DAORecordcms_portlets::create ()->initFromDbObject ($portletDescription);
			//on serialize l'objet
			$pPortletDescription->serialized_object = null;
			$record->serialized_object = CopixXmlSerializer::serialize ($pPortletDescription);
			//insertion dans la bdd
			DAOcms_portlets::instance ()->insert ($record);
			//Application des changements
			_ppo ($record)->saveIn ($pPortletDescription);
			//enregistrements des headingElementInformation
			$listeElements = $this->_getDistinct ($pPortletDescription->getElementsToSave ());
			if(!empty($listeElements)){
				foreach ($listeElements as $portletElement){
					$record = DAORecordcms_portlets_headingelementinformations::create ();
					$record->id_portlet = $pPortletDescription->getId ();
					$record->public_id_hei = $portletElement->getHeadingElement ()->public_id_hei;	
					DAOcms_portlets_headingelementinformations::instance ()->insert ($record);
				}
			}			
		}catch (CopixException $e){
			if (!$pInsertIntoCurrentTransaction){
				CopixDB::rollback ();
			}
			HeadingCache::clear ();
			throw $e;
		}
		if (!$pInsertIntoCurrentTransaction){
			CopixDB::commit ();
		}
		HeadingCache::clear ();
	}
		
	/**
	 * Mise à jour d'une portlet 
	 * 
	 * @param array / object $pPortletDescription
	 */
	public function update ($pPortletDescription, $pInsertIntoCurrentTransaction = false){
		HeadingCache::clear ();
		$portletDescription = _ppo ($pPortletDescription);
		if (!$pInsertIntoCurrentTransaction){
			CopixDB::begin ();
		}
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($portletDescription['id_portlet']);
			//on met a jour les données spécifiques
			$portletDescription->saveIn ($record);
			//on serialize l'objet en vidant de l'objet de base, l'objet deja serializé
			$pPortletDescription->serialized_object = null;
			$record->serialized_object = CopixXmlSerializer::serialize ($pPortletDescription);
			//mise à jour de la portlet
			DAOcms_portlets::instance ()->update ($record);
			//Application des changements
			_ppo ($record)->saveIn ($portletDescription);
			//suppression puis enregistrements des headingElementInformation
			$criteres = _daoSP ()->addCondition('id_portlet', '=', $pPortletDescription->getId ());
			DAOcms_portlets_headingelementinformations::instance ()->deleteBy ($criteres);
			$listeElements = $this->_getDistinct ($pPortletDescription->getElementsToSave ());
			if(!empty($listeElements)){
				foreach ($listeElements as $portletElement){
					$record = DAORecordcms_portlets_headingelementinformations::create ();
					$record->id_portlet = $pPortletDescription->getId ();
					$record->public_id_hei = $portletElement->getHeadingElement ()->public_id_hei;
					DAOcms_portlets_headingelementinformations::instance ()->insert ($record);
				}
			}

		}catch (CopixException $e){
			if (!$pInsertIntoCurrentTransaction){
				CopixDB::rollback ();
			}
			HeadingCache::clear ();
			throw $e;
		}
		if (!$pInsertIntoCurrentTransaction){
			CopixDB::commit ();
		}
		HeadingCache::clear ();
	}
	
	public function insertHeadingElementPortlet ($pPortletDescription){
		HeadingCache::clear ();
		$portletDescription = _ppo ($pPortletDescription);
		CopixDB::begin ();
		try {
			//création de l'enregistrement
			$record = DAORecordcms_portlets::create ()->initFromDbObject ($portletDescription);
			$record->variable = $record->variable == null ? 0 : $record->variable; 
			$record->position = $record->position == null ? 0 : $record->position; 
			$record->serialized_object = null;
			$record->serialized_object = CopixXmlSerializer::serialize ($pPortletDescription);
			DAOcms_portlets::instance ()->insert ($record);

			//Mise à jour pour les informations génériques
			$record->id_helt = $record->id_portlet;
			$record->type_hei = 'portlet';
			$record->caption_hei = empty($portletDescription['caption_hei']) ? '' : $portletDescription['caption_hei'];
			$record->parent_heading_public_id_hei = $portletDescription['parent_heading_public_id_hei'];

			_ioClass ('heading|HeadingElementInformationServices')->insert ($record);

			_ppo ($record)->saveIn ($pPortletDescription);
			$record->serialized_object = null;
			$record->serialized_object = CopixXmlSerializer::serialize ($pPortletDescription);
			
			//on met maintenant a jour l'élément $record avec les informations publiques mises à jour
			DAOcms_portlets::instance ()->update ($record);
			//Application des changements
			_ppo ($record)->saveIn ($pPortletDescription);

			$listeElements = $this->_getDistinct ($pPortletDescription->getElements ());
			if(!empty($listeElements)){
				foreach ($listeElements as $portletElement){
					$record = DAORecordcms_portlets_headingelementinformations::create ();
					$record->id_portlet = $pPortletDescription->getId ();
					$record->public_id_hei = $portletElement->getHeadingElement ()->public_id_hei;	
					DAOcms_portlets_headingelementinformations::instance ()->insert ($record);
				}
			}	
		}
		catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e; 
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	
	public function updateHeadingElementPortlet ($pPortletDescription){
		HeadingCache::clear ();
		$portletDescription = _ppo ($pPortletDescription);
		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($portletDescription['id_portlet']);
			//on met a jour les données spécifiques
			$portletDescription->saveIn ($record);
			//on serialize l'objet en vidant de l'objet de base, l'objet deja serializé
			$pPortletDescription->serialized_object = null;
			$record->serialized_object = CopixXmlSerializer::serialize ($pPortletDescription);
			//mise à jour de la portlet
			DAOcms_portlets::instance ()->update ($record);
			
			_ioClass ('heading|HeadingElementInformationServices')->update ($record);
			//Application des changements
			_ppo ($record)->saveIn ($portletDescription);
			//suppression puis enregistrements des headingElementInformation
			$criteres = _daoSP ()->addCondition('id_portlet', '=', $pPortletDescription->getId ());
			DAOcms_portlets_headingelementinformations::instance ()->deleteBy ($criteres);
			$listeElements = $this->_getDistinct ($pPortletDescription->getElements ());
			if(!empty($listeElements)){
				foreach ($listeElements as $portletElement){
					$record = DAORecordcms_portlets_headingelementinformations::create ();
					$record->id_portlet = $pPortletDescription->getId ();
					$record->public_id_hei = $portletElement->getHeadingElement ()->public_id_hei;	
					DAOcms_portlets_headingelementinformations::instance ()->insert ($record);
				}
			}

		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	public function versionHeadingElementPortlet ($pPortletDescription){
		HeadingCache::clear ();
		$portletDescription = _ppo ($pPortletDescription);
		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($portletDescription['id_portlet']);

			//on met a jour les données spécifiques			
			$record->description_hei = $portletDescription['description_hei'];
			
			DAOcms_portlets::instance ()->insert ($record);

			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $portletDescription[$propertyName];
			}
			$record->id_helt = $record->id_portlet;
			_ioClass ('heading|HeadingElementInformationServices')->version ($record, $portletDescription['id_portlet']);
						
			//Application des changements
			_ppo ($record)->saveIn ($pPortletDescription);
			$record->serialized_object = CopixXmlSerializer::serialize ($pPortletDescription);
			
			//on met maintenant a jour l'élément $record avec les informations publiques mises à jour
			DAOcms_portlets::instance ()->update ($record);
			
			_ppo ($record)->saveIn ($pPortletDescription);

			$listeElements = $this->_getDistinct ($pPortletDescription->getElements ());
			if(!empty($listeElements)){
				foreach ($listeElements as $portletElement){
					$record = DAORecordcms_portlets_headingelementinformations::create ();
					$record->id_portlet = $pPortletDescription->getId ();
					$record->public_id_hei = $portletElement->getHeadingElement ()->public_id_hei;	
					DAOcms_portlets_headingelementinformations::instance ()->insert ($record);
				}
			}
	
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	public function copy ($pPublicId, $pHeading){
		HeadingCache::clear ();
		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getHeadingElementPortletByPublicId ($pPublicId);
			$record->id_portlet = null;
			$record->public_id_hei = null;
			$record->url_id_hei = $record->url_id_hei ? $record->url_id_hei . ' (copie)' : $record->url_id_hei;
			DAOcms_portlets::instance ()->insert ($record);

			$record->id_helt = $record->id_portlet;
			$record->parent_heading_public_id_hei = $pHeading;
			$record->caption_hei = $record->caption_hei . ' (copie)';	
			$record->display_order_hei = _ioClass ('heading|HeadingElementInformationServices')->getNextDisplayOrderValue ($pHeading);
			_ioClass ('heading|HeadingElementInformationServices')->insert ($record);
			
			//Application des changements
			$record->serialized_object = CopixXmlSerializer::serialize ($record);

			//on met maintenant a jour l'élément $record avec les informations publiques mises à jour
			DAOcms_portlets::instance ()->update ($record);

			$listeElements = $this->_getDistinct ($record->getElements ());

			if(!empty($listeElements)){
				foreach ($listeElements as $portletElement){
					$element = DAORecordcms_portlets_headingelementinformations::create ();
					$element->id_portlet = $record->id_helt;
					$element->public_id_hei = $portletElement->getHeadingElement ()->public_id_hei;	
					DAOcms_portlets_headingelementinformations::instance ()->insert ($element);
				}
			}
	
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
	 * Nettoie la liste des elements d'une portlet pour eviter les erreurs de duplication dans la base
	 *
	 * @param array $pListeElements
	 * @return array
	 */
	private function _getDistinct (array $pListeElements){
		$toReturn = array();
		foreach ($pListeElements as $portletElement){
			if (!array_key_exists($portletElement->getHeadingElement ()->public_id_hei, $toReturn)){
				$toReturn[$portletElement->getHeadingElement ()->public_id_hei] = $portletElement;
			}
		}
		return $toReturn;
	}
	
	/**
	 * Récupère un enregistrement par son identifiant interne
	 * 
	 * @param int $pIdPortlet Identifiant interne de l'élément à récupérer 
	 */
	public function getById ($pIdPortlet) {
		$cacheId = 'portletservices|getById|' . $pIdPortlet;
		if (HeadingCache::exists ($cacheId)) {
			return HeadingCache::get ($cacheId);
		}

		//on vérifie que l'élément existe
		if (! $element = DAOcms_portlets::instance ()->get ($pIdPortlet)) {
			throw new HeadingElementInformationNotFoundException ($pIdPortlet);
		}
					
		if ($element->public_id_hei){
			//fusion des informations communes et spécifiques						
			$unserializedElement = CopixXMLSerializer::unserialize ($element->serialized_object);
			if (!$unserializedElement->serialized_object){
				$unserializedElement->serialized_object = $element->serialized_object;
			}
			$record = _ioClass ('heading|HeadingElementInformationServices')->get ($element->public_id_hei);
			$element = $unserializedElement;
			_ppo ($record)->saveIn ($element);
		}
		
		HeadingCache::set ($cacheId, $element, false);
		return $element;
	}

	public function getByPublicId ($pPublicId) {
		$element = _ioClass ('heading|HeadingElementInformationServices')->get ($pPublicId);
		if (($record = DAOcms_portlets::instance ()->get ($element->id_helt)) === false) {
			throw new HeadingElementInformationNotFoundException ($pPublicId);
		}
		_ppo ($record)->saveIn ($element);
		return $element;
	}
	
	public function getHeadingElementPortletById ($pIdHelt){
		return $this->getById($pIdHelt);
	}

	public function getHeadingElementPortletByPublicId ($pPublicId){
		$cacheId = 'portletservices|getHeadingElementPortletByPublicId|' . $pPublicId;
		if (HeadingCache::exists ($cacheId)) {
			return HeadingCache::get ($cacheId);
		}

		$element = _ioClass ('heading|HeadingElementInformationServices')->get($pPublicId);

		//on vérifie que l'élément existe
		if (! $record = DAOcms_portlets::instance ()->get ($element->id_helt)){
			throw new HeadingElementInformationNotFoundException ($pPublicId);
		}

		//fusion des informations communes et spécifiques
		_ppo ($record)->saveIn ($element);

		$portlet = CopixXMLSerializer::unserialize ($record->serialized_object);
		_ppo ($element)->saveIn ($portlet);

		HeadingCache::set ($cacheId, $portlet, false);
		return $portlet;
	}
	
	/**
	 * Recupere la liste des portlets par identifiant de page
	 *
	 * @param int $pIdPage
	 */
	public function getPortletsByPageId ($pIdPage){
		$cacheId = 'portletservices|getPortletsByPageId|' . $pIdPage;
		if (HeadingCache::exists ($cacheId)) {
			return HeadingCache::get ($cacheId);
		}
	    	
		$criteres = _daoSP ()->addCondition ('id_page', '=', $pIdPage)->orderby ('position');
		$results = DAOcms_portlets::instance ()->findBy ($criteres)->fetchAll ();
		
		$listePortlets = array();
		if(!empty ($results)){
		    // @TODO (améliorer) Fixe pour que les données d'une autre base soit déserializé correctement

			foreach ($results as $portletDescription){
		        // @TODO (améliorer) Fixe pour que les données d'une autre base soit déserializé correctement			    
			    $xml = simplexml_load_string($portletDescription->serialized_object);
			    $portletType = (string)$xml['class'];

			    $portlet = CopixXMLSerializer::unserialize ($portletDescription->serialized_object);
				if ($portlet instanceof CopixSerializableObject){
					$portlet = $portlet->getRemoteObject ();
				}
				if( $portlet instanceof Portlet ){
					_ppo ($portletDescription)->saveIn ($portlet);
					array_push ($listePortlets, $portlet);
				} else {
					_log('Impossible de désérialiser la portlet "'.$portletType.'" : fichier de classe introuvable.', 'debug', CopixLog::WARNING, $portlet);
				}
			}
		}

		HeadingCache::set ($cacheId, $listePortlets, false);
		return $listePortlets;
	}
	
	/**
	 * Suppression de portlets par identifiant de page
	 *
	 * @param array/int $pArId identifiant(s) de page
	 */
	public function deleteByPageId ($pArId){
		HeadingCache::clear ();
		CopixDB::begin ();
		try {
			$listePortlets = DAOcms_portlets::instance ()->findBy (_daoSP ()->addCondition ('id_page', '=', $pArId))->fetchAll ();
			foreach ($listePortlets as $portlet){
				DAOcms_portlets_headingelementinformations::instance ()->deleteBy (_daoSP ()->addCondition ('id_portlet', '=', $portlet->id_portlet));
			}		
			DAOcms_portlets::instance ()->deleteBy (_daoSp ()->addCondition ('id_page', '=', $pArId));
		}
		catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	/**
	 * Suppression de portlet par identifiant de portlet
	 *
	 * @param int $pId identifiant de portlet
	 */
	public function deleteById ($pId, $pInsertIntoCurrentTransaction = false){
		HeadingCache::clear ();
		if (!$pInsertIntoCurrentTransaction){
			CopixDB::begin ();
		}
		try {
			DAOcms_portlets_headingelementinformations::instance ()->deleteBy (_daoSP()->addCondition('id_portlet', '=', $pId));
			DAOcms_portlets::instance ()->deleteBy (_daoSp ()->addCondition ('id_portlet', '=', $pId));
		}
		catch (CopixException $e){
			if (!$pInsertIntoCurrentTransaction){
				CopixDB::rollback ();
			}
			HeadingCache::clear ();
			throw $e;
		}
		if (!$pInsertIntoCurrentTransaction){
			CopixDB::commit ();
		}
		HeadingCache::clear ();
	}
	
	public function getPortletInstance ($pType){
		return _ioClass($this->getPortletSelector ($pType));
	}
	
    public function getPortletSelector ($pType){
		$arTypes = _ioClass ('portal|portletservices')->getList ();

		foreach ($arTypes as $key=>$type){
			if (isset ($type['portlettype']) &&  $type['portlettype'] == $pType){
				return $type['module'].'|'.$pType;
			}
		}
		throw new CopixException ('La portlet de type '.$pType.' est introuvable');
	}
	
	public function delete ($pArPublicId) {
		HeadingCache::clear ();
		$results = DAOcms_headingelementinformations::instance ()-> findBy (_daoSp ()->addCondition ('public_id_hei', '=', $pArPublicId))->fetchAll();
		foreach ($results as $resultat){
			$this->deleteById ($resultat->id_helt);
		}
		HeadingCache::clear ();
	}
	
	/**
	 * Retourne les portlets faisant référence à l'element de publicId $pPublicId
	 *
	 * @param int $pPublicId
	 */
	public function getDependencies ($pPublicId){
		$query = "SELECT p.id_portlet from cms_portlets_headingelementinformations phei
					LEFT JOIN cms_portlets p ON phei.id_portlet = p.id_portlet
					WHERE p.public_id_hei IS NOT NULL
					AND phei.public_id_hei = :public_id";

		$results = _doQuery($query, array(":public_id"=>$pPublicId));
		
		$toReturn = array();
		foreach ($results as $result) {
			$toReturn[] = $this->getById ($result->id_portlet);
		}
		return $toReturn;
	}

	/**
	 * Prévisualisation
	 *
	 * @param int $pId Identifiant
	 * @return string
	 */
	public function previewById ($pId) {
		$record = $this->getById ($pId);
		$infos = array ('type' => array ('caption' => 'Type', 'value' => $record->type_portlet));
		return CopixZone::process ('heading|headingelement/headingelementpreview', array ('record' => $record,	'infos' => $infos));
	}

	/**
	 * Permet de changer les actions (couper, copier, etc) possibles sur un élément
	 * /!\ A ne pas appeler directement, passer par HeadingElementInformationServices::getActions ()
	 *
	 * @param stdClass $pElement Enregistrement de l'élément
	 * @param stdClass $pActions Actions déja prédéfinies par HeadingElementInformationServices::getActions
	 */
	public function getActions ($pElement, $pActions) {
		$pActions->show = false;
	}
	
	/**
	 * 
	 * Retourne les publicId des éléments qui contiennent $toSearch
	 * @param string $toSearch
	 */
	public function search ($toSearch){
		$toReturn = array();
		$arIdPages = array();
		// on fait un union car comme on joint sur 2 index différents, les bases de données sont plus performantes avec deux requêtes
		$query = "select distinct p.public_id_hei , p.id_page 
					from cms_portlets as p, cms_headingelementinformations as hei
					where
					p.type_portlet = 'PortletText'
					and hei.status_hei = :status
					and p.public_id_hei is not null
					and hei.public_id_hei = p.public_id_hei
					and hei.id_helt = p.id_portlet
					and lower(p.serialized_object) like :content
					
					union
					
					select distinct p.public_id_hei , p.id_page 
					from cms_portlets as p, cms_headingelementinformations as hei
					where
					p.type_portlet = 'PortletText'
					and hei.status_hei = :status
					and p.public_id_hei is null
					and hei.type_hei = 'page'
					and hei.id_helt = p.id_page
					and lower(p.serialized_object) like :content
					
					";
		$ct = CopixDB::getConnection ();
		$res = $ct->doQuery($query, array('content' => '%'.$toSearch.'%', 'status' => HeadingElementStatus::PUBLISHED ));
		
		foreach ($res as $r){
			if($r->public_id_hei){
				$toReturn[] = $r->public_id_hei;
			}else{
				$arIdPages[] = $r->id_page;
			}
		}
		$arIdPages = array_unique($arIdPages);
		if(count($arIdPages) > 0){
			$res = DAOcms_pages::instance ()->findBy(_daoSP()->addSql('id_page in ('.join(',', $arIdPages).')'));
			foreach ($res as $r){
				$toReturn[] = $r->public_id_hei;
			}
		}
		return $toReturn;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see project/modules/devel/cms3/heading/classes/HeadingElementServices::findGhosts()
	 */
	public function findGhosts (){
		//les portlets ayant un publicid qui n'existe pas dans hei
		$specific = _doQuery ('select * from cms_portlets where public_id_hei is not null and id_portlet not in(select id_helt from cms_headingelementinformations where type_hei = :type and status_hei <> :status)', array (':type'=>'portlet', ':status'=>HeadingElementStatus::DELETED));
		//les portlets appartenant a des pages qui n'existent plus
		$pages = _doQuery ('select * from cms_portlets where id_page not in (select id_page from cms_pages, cms_headingelementinformations where id_page = id_helt and status_hei <> :status)', array (':status'=>HeadingElementStatus::DELETED));
		$toReturn['specific'] = array_merge ($specific, $pages);
		$toReturn['general']  = _doQuery ('select * from cms_headingelementinformations where type_hei = :type and id_helt not in (select id_portlet from cms_portlets where public_id_hei IS NOT NULL)', array (':type'=>'portlet'));
		return $toReturn;
	}
}