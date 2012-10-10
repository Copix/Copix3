<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      VUIDART Sylvain
 */

/**
 * Services des liens
 * @package     cms
 * @subpackage  heading
 */
class LinkServices extends HeadingElementServices {
	const CAPTION_LINK = 0;
	const CAPTION_ELEMENT = 1;
	const URL_LINK = 0;
	const URL_ELEMENT = 1;
	
	private static $_elements = array ();

	/**
	 * Retourne une courte description pour l'affichage détails
	 *
	 * @param int $pIdHelt Identifiant interne
	 * @return string
	 */
	public function getDisplayDescription ($pIdHelt) {
		$element = $this->getById ($pIdHelt);
		if ($element->href_link != null) {
			return 'Lien : <a href="'.$element->href_link.'" target="_blank">'.substr ($element->href_link, 0, 30) . (strlen ($element->href_link) > 30 ? '...' : '') . '</a>';
		} else if ($element->linked_public_id_hei != null) {
			$element = _ioClass ('HeadingElementInformationServices')->get ($element->linked_public_id_hei);
			return 'Pointe sur <a href="'._url ('heading|element|', array ('heading' => $element->parent_heading_public_id_hei, 'id_helt' => $element->id_helt, 'type_hei' => $element->type_hei)) . '" target="_blank">' . $element->caption_hei . '</a>';
		}
	}
	
	/**
	 * 
	 * Retourne les publicId des éléments qui contiennent $toSearch
	 * @param string $toSearch
	 */
	public function search ($toSearch){
		$toReturn = array ();
		$sp = _daoSP();
		$sp->addSql('lower(href_link) like :toSearch', array('toSearch' => '%'.$toSearch.'%'));
		$sp->addSql('lower(module_link) like :toSearch', array('toSearch' => '%'.$toSearch.'%'));
		$res = DAOcms_links::instance ()->findBy($sp);
		foreach ($res as $r){
			$toReturn[] = $r->public_id_hei;
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
	 * Création d'un nouvel lien
	 * @param array / object $pLienDescription
	 */
	public function insert ($pLienDescription){
		HeadingCache::clear ();
		$lienDescription = _ppo ($pLienDescription);

		CopixDB::begin ();
		try {
			//création de l'enregistrement
			$record = DAORecordcms_links::create ()->initFromDbObject ($lienDescription);
			//champ content obligatoire à passer à nul si rien n'a été saisi, pour lever l'excepion
			$record->content = ($lienDescription['content'] == '')? null : $lienDescription['content'];
			
			DAOcms_links::instance ()->insert ($record);

			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $lienDescription[$propertyName];
			}
			$record->id_helt = $record->id_link;
			$record->type_hei = 'link';

			//dans $record->id_helt on a le nouvel identifiant
			_ioClass ('heading|HeadingElementInformationServices')->insert ($record);
		
			//on met maintenant a jour l'élément $record avec les informations publiques mises à jour
			DAOcms_links::instance ()->update ($record);

			//Application des changements
			_ppo ($record)->saveIn ($pLienDescription);
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
	 * @param array / object $pLienDescription
	 */
	public function update ($pLienDescription){
		HeadingCache::clear ();
		$lienDescription = _ppo ($pLienDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($lienDescription['id_link']);

			//on met a jour les données spécifiques			
			$record->description_hei = $lienDescription['description_hei'];
			$record->href_link = $lienDescription['href_link'];
			$record->linked_public_id_hei = $lienDescription['linked_public_id_hei'];
			$record->not_rewritten_link = $lienDescription['not_rewritten_link'];
			$record->module_link = $lienDescription['module_link'];
			$record->extra_link = $lienDescription['extra_link'];
			$record->caption_link = $lienDescription['caption_link'];
			$record->url_link = $lienDescription['url_link'];
			DAOcms_links::instance ()->update ($record);
			
			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $lienDescription[$propertyName];
			}
			_ioClass ('heading|HeadingElementInformationServices')->update ($record);

			//Application des changements
			_ppo ($record)->saveIn ($pLienDescription);
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
	 * @param object $pLienDescription la description de la page dont on souhaite obtenir une nouvelle version
	 */
	public function version ($pLienDescription){
		HeadingCache::clear ();
		$lienDescription = _ppo ($pLienDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($lienDescription['id_link']);

			//on met a jour les données spécifiques			
			$record->description_hei = $lienDescription['description_hei'];
			$record->href_link = $lienDescription['href_link'];
			$record->linked_public_id_hei = $lienDescription['linked_public_id_hei'];
			$record->module_link = $lienDescription['module_link'];
			$record->not_rewritten_link = $lienDescription['not_rewritten_link'];
			$record->extra_link = $lienDescription['extra_link'];
			$record->caption_link = $lienDescription['caption_link'];
			$record->url_link = $lienDescription['url_link'];
			
			DAOcms_links::instance ()->insert ($record);

			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $lienDescription[$propertyName];
			}
			$record->id_helt = $record->id_link;			
			_ioClass ('heading|HeadingElementInformationServices')->version ($record, $lienDescription['id_link']);

			//Application des changements
			_ppo ($record)->saveIn ($pLienDescription);
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
			$lien = $this->getByPublicId($pPublicId);
			$lien->public_id_hei = null;
			$lien->url_id_hei = $lien->url_id_hei ? $lien->url_id_hei . ' (copie)' : $lien->url_id_hei;
			$lien->parent_heading_public_id_hei = $pHeading;
			
			DAOcms_links::instance ()->insert ($lien);

			$lien->id_helt = $lien->id_link;		
			$lien->caption_hei = $lien->caption_hei . ' (copie)';		
			$lien->display_order_hei = _ioClass ('heading|HeadingElementInformationServices')->getNextDisplayOrderValue ($pHeading);

			_ioClass ('heading|HeadingElementInformationServices')->insert ($lien);
			
			DAOcms_links::instance ()->update ($lien);
			
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e; 
		}
		CopixDB::commit ();
		HeadingCache::clear ();
		return $lien->public_id_hei;
	}

	/**
	 * Récupère un enregistrement par son identifiant interne
	 * 
	 * @param int $pIdHelt Identifiant interne de l'élément à récupérer 
	 */
	public function getById ($pIdHelt){
		//on vérifie que l'élément existe
		if (! $record = DAOcms_links::instance ()->get ($pIdHelt)){
			throw new HeadingElementInformationNotFoundException ($pIdHelt);
		}
		//on récupère les informations "communes"
		$element = _ioClass ('heading|HeadingElementInformationServices')->getById ($pIdHelt, 'link');
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
		$element = _ioClass ('heading|HeadingElementInformationServices')->get ($pPublicId);
				
		//infos specifiques
		if (array_key_exists ($element->id_helt, self::$_elements)){
			$record = self::$_elements[$element->id_helt]; 
		} else if ( !$record = DAOcms_links::instance ()->get ($element->id_helt)){
			throw new HeadingElementInformationNotFoundException ($pPublicId);
		}
		
		self::$_elements[$element->id_helt] = $record;

		//fusion des informations communes et spécifiques
		_ppo ($record)->saveIn ($element);

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
		DAOcms_links::instance ()->deleteBy (_daoSp ()->addCondition ('public_id_hei', '=', $pArPublicId));
		HeadingCache::clear ();
	}

	/**
	 * Supression par identifiant interne
	 *
	 * @param int $pArId les identifiants des éléments à supprimer
	 */
	public function deleteById ($pArId) {
		DAOcms_links::instance ()->deleteBy (_daoSp ()->addCondition ('id_link', '=', $pArId));
		HeadingCache::clear ();
	}

	/**
	 * Prévisualisation des informations sur le lien
	 */
	public function previewById ($pId) {
		$element = $this->getById ($pId);
		$infos = array ();

		$infos['link'] = array ('caption' => 'Lien');
		if ($element->href_link != null) {
			$infos['link']['value'] = 'Adresse extérieure';
		} else if ($element->linked_public_id_hei != null) {
			$infos['link']['value'] = 'Elément du CMS';
		} else if ($element->module_link != null) {
			$infos['link']['value'] = 'Module Copix';
		}

		$url = $this->getURL ($element->id_helt);
		$caption = $this->getcaption ($element->id_helt);
		$infos['url'] = array ('caption' => 'Adresse', 'value' => '<a href="' . $url . '" target="_blank" title="' . $caption . '">' . substr ($caption, 0, 30) . (strlen ($caption) > 30 ? '...' : null) . '</a>');

		if ($element->href_link != null || $element->module_link != null) {
			$infos['rewriting'] = array ('caption' => 'Réécriture', 'value' => ($element->not_rewritten_link) ? 'Non' : 'Oui');
		}
		
		return CopixZone::process ('heading|headingelement/headingelementpreview', array (
			'record' => $element,
			'infos' => $infos,
			'link' => $this->getURL ($element->id_helt)
		));
	}
	
	/**
	 * Change l'enregistrement des extras en bas, en tableau.
	 *
	 * @param String $pExtra
	 */
	public function getArExtra ($pExtra){
		$arExtra = array();
		$listeExtras = explode(";", $pExtra);
		foreach ($listeExtras as $extra){
			if ($extra != ""){
				list($key, $value) = explode (':', $extra);
				$arExtra[$key] = $value;
			}
		}
		return $arExtra;
	}
	
	/**
	 * Retourne les liens faisant référence à l'element de publicId $pPublicId en linked_public_id_hei
	 *
	 * @param int $pPublicId
	 */
	public function getDependencies ($pPublicId){
		$query = 'SELECT DISTINCT id_link FROM cms_links WHERE linked_public_id_hei = :public_id';
		$toReturn = array ();
		foreach (_doQuery ($query, array (':public_id' => $pPublicId)) as $result) {
			try {
				$toReturn[] = $this->getById ($result->id_link);	
			} catch (CopixException $e){
				// il y a un problème en base : le lien a un enregistrement dans la table link mais pas dans headingelementinformation
				_log("Le lien d'identifiant id_link ".$result->id_link." ne possède pas d'enregistrement dans la table cms_headingelementinformations !", "errors");
			}
		}
		return $toReturn;
	}

	/**
	 * Retourne l'adresse pointée par un lien
	 *
	 * @param int $pIdHelt Identifiant du lien
	 * @return string
	 */
	public function getURL ($pIdHelt) {
		$element = $this->getById ($pIdHelt);

		// adresse extérieure
		if (!is_null ($element->href_link)) {
			if (substr($element->href_link, 0, 1) == '/') {
				return CopixURL::getRequestedBaseUrl () . 'index.php' . $element->href_link;
			} else {
				return _url (str_replace ('{$copixurl:domain}', CopixURL::getRequestedDomain (), $element->href_link));
			}

		// trigramme copix
		} else if (!is_null ($element->module_link)) {
			return _url ($element->module_link);

		// élément du CMS
		} else if (!is_null ($element->linked_public_id_hei)) {
			if ($element->url_link == self::URL_ELEMENT) {
				$CMSElement = $this->_hei->get ($element->linked_public_id_hei);
				return _url ('heading||', array ('public_id' => $CMSElement->public_id_hei));
			} else {
				return _url ('heading||', array ('public_id' => $element->public_id_hei));
			}
		}
		return false;
	}

	/**
	 * Retourne le libellé du lien
	 *
	 * @param int $pIdHelt Identifiant du lien
	 * @return string
	 */
	public function getCaption ($pIdHelt) {
		$element = $this->getById ($pIdHelt);
		if ($element->caption_link == self::CAPTION_ELEMENT && $element->linked_public_id_hei != null) {
			return $this->_hei->get ($element->linked_public_id_hei)->caption_hei;
		}
		return $element->caption_hei;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see project/modules/devel/cms3/heading/classes/HeadingElementServices::findGhosts()
	 */
	public function findGhosts (){
		$toReturn['specific'] = _doQuery ('select * from cms_links where id_link not in(select id_helt from cms_headingelementinformations where type_hei = :type and status_hei <> :status)', array (':type'=>'link', ':status'=>HeadingElementStatus::DELETED));
		$toReturn['general']  = _doQuery ('select * from cms_headingelementinformations where type_hei = :type and id_helt not in (select id_link from cms_links)', array (':type'=>'link'));
		return $toReturn;
	}

	/**
	 * Retourne la liste des liens morts
	 *
	 * @return array
	 */
	public function findDeadLinks () {
		$hei = _ioClass ('HeadingElementInformationServices');
		$toReturn = array ();
		$query = 'SELECT * FROM cms_links WHERE linked_public_id_hei IS NOT NULL AND id_link IN (SELECT id_helt FROM cms_headingelementinformations WHERE status_hei = :status AND type_hei = :type)';
		foreach (_doQuery ($query, array (':status' => HeadingElementStatus::PUBLISHED, ':type' => 'link')) as $link) {
			try {
				$hei->get ($link->linked_public_id_hei);
			} catch (Exception $e) {
				try {
					$record = $hei->get ($link->public_id_hei);
				} catch (Exception $e) {
					$record = _ppo ();
				}
				$toReturn[] = array (
					'element' => $record,
					'linked_public_id_hei' => $link->linked_public_id_hei,
					'error' => $e->getMessage ()
				);
			}
		}
		return $toReturn;
	}
}