<?php
/**
 * Gestion des mv testing
 */
class MVTestingServices extends HeadingElementServices {
	/**
	 * Choix d'affichage : aléatoire
	 */
	const CHOICE_RANDOM = 1;

	/**
	 * Choix d'affichage : élément suivant
	 */
	const CHOICE_NEXT = 2;

	/**
	 * Choix d'affichage : pourcentage spécifié sur chaque élément
	 */
	const CHOICE_PERCENT = 3;

	/**
	 * Conservation de l'élément pour un visiteur : non
	 */
	const CONSERVE_NONE = 1;

	/**
	 * Conservation de l'élément pour un visiteur : en session
	 */
	const CONSERVE_SESSION = 2;

	/**
	 * Conservation de l'élément pour un visiteur : par cookie
	 */
	const CONSERVE_COOKIE = 3;

	/**
	 * Type d'élément : élément du CMS
	 */
	const TYPE_CMS = 1;

	/**
	 * Type d'élément : trigramme d'un module copix
	 */
	const TYPE_MODULE = 2;

	/**
	 * Informations sur le mv testing utilisé
	 *
	 * @var array
	 */
	private $_showed = array ();

	/**
	 * Retourne la liste des choix d'élément à afficher
	 *
	 * @return array
	 */
	public function getChoicesList () {
		return array (
			self::CHOICE_RANDOM => 'Aléatoire',
			self::CHOICE_NEXT => 'Elément suivant',
			self::CHOICE_PERCENT => 'Pourcentage spécifique'
		);
	}

	/**
	 * Retourne la liste des méthodes de conservation de l'élément visité
	 *
	 * @return array
	 */
	public function getConserveList () {
		return array (
			self::CONSERVE_NONE => 'Non',
			self::CONSERVE_SESSION => 'Session',
			self::CONSERVE_COOKIE => 'Cookie'
		);
	}

	public function getTypeList () {
		return array (
			self::TYPE_CMS => 'Elément du CMS',
			self::TYPE_MODULE => 'Module Copix'
		);
	}

	/**
	 * Retourne des informations sur le mv testing utilisé
	 *
	 * @return array
	 */
	public function getShowed () {
		if (count ($this->_showed) == 0 && CopixRequest::exists ('mvtesting')) {
			list ($publicId, $elementId) = explode ('|', _request ('mvtesting'));
			try {
				$this->_showed['mvtesting'] = _ioClass ('MVTestingServices')->getByPublicId ($publicId);
				foreach ($this->_showed['mvtesting']->elements as $index => $element) {
					if ($element->id_element == $elementId) {
						$this->_showed['index'] = $index;
						break;
					}
				}
			} catch (Exception $e) {}
		}
		return $this->_showed;
	}

	/**
	 * Retourne une courte description pour l'affichage détails
	 *
	 * @param int $pIdHelt Identifiant interne
	 * @return string
	 */
	public function getDisplayDescription ($pIdHelt) {
		$element = $this->getById ($pIdHelt);
		$choices = $this->getChoicesList ();
		$toReturn = count ($element->elements) . ' éléments - Affichage : ' . strtolower ($choices[$element->choice_mvt]);
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
	 * Prévisualisation
	 *
	 * @param int $pId Identifiant
	 * @return string
	 */
	public function previewById ($pId) {
		$record = $this->getById ($pId);
		$choices = $this->getChoicesList ();
		$conserve = $this->getConserveList ();
		$infos = array ();
		$infos['choice'] = array ('caption' => 'Visualisation', 'value' => $choices[$record->choice_mvt]);
		$infos['conserve'] = array ('caption' => 'Redondant', 'value' => $conserve[$record->conserve_mvt]);
		$infos['elements'] = array ('caption' => 'Eléments', 'value' => count ($record->elements) . ' éléments');
		return CopixZone::process ('heading|headingelement/headingelementpreview', array (
			'record' => $record,
			'infos' => $infos
		));
	}

	/**
	 * Sauvegarde les éléments d'un mv testing
	 *
	 * @param int $pIdHelt Identifiant du mv testing
	 * @param array $pElements Eléments à sauvegarder
	 */
	private function _saveElements ($pIdHelt, $pElements) {
		$dao = DAOcms_mvtestings_headingelementinformations::instance ();
		$dao->deleteBy (_daoSP ()->addCondition ('id_mvt', '=', $pIdHelt));

		$record = DAORecordcms_mvtestings_headingelementinformations::create ();
		foreach ($pElements as $element) {
			$record->id_element = null;
			$record->id_mvt = $pIdHelt;
			$record->type_element = $element->type_element;
			$record->value_element = $element->value_element;
			$record->percent_element = $element->percent_element;
			$record->show_element = $element->show_element;
			$dao->insert ($record);
		}
	}
	
	/**
	 * Ajoute un mv testing
	 *
	 * @param mixed $pDescription Informations
	 */
	public function insert ($pDescription) {
		HeadingCache::clear ();
		$desc = _ppo ($pDescription);

		CopixDB::begin ();
		try {
			$record = DAORecordcms_mvtestings::create ()->initFromDbObject ($desc);
			DAOcms_mvtestings::instance ()->insert ($record);

			$record->id_helt = $record->id_mvt;
			$record->type_hei = 'mvtesting';
			$record->caption_hei = $desc['caption_hei'];
			$record->parent_heading_public_id_hei = $desc['parent_heading_public_id_hei'];
			$this->_hei->insert ($record);

			$record->choice_mvt = $desc['choice_mvt'];
			$record->current_mvt = ($record->choice_mvt == self::CHOICE_NEXT) ? 1 : null;
			$record->conserve_mvt = $desc['conserve_mvt'];
			DAOcms_mvtestings::instance ()->update ($record);

			$this->_saveElements ($record->id_mvt, $desc['elements']);

			_ppo ($record)->saveIn ($pDescription);
		} catch (CopixException $e) {
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e; 
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	/**
	 * Mise à jour d'un mv testing
	 * 
	 * @param mixed $pDescription Informations
	 */
	public function update ($pDescription) {
		HeadingCache::clear ();
		$desc = _ppo ($pDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($desc['id_mvt']);

			//on met a jour les données spécifiques			
			$record->choice_mvt = $desc['choice_mvt'];
			$record->current_mvt = (isset ($desc['current_mvt'])) ? $desc['current_mvt'] : $record->current_mvt;
			$record->conserve_mvt = $desc['conserve_mvt'];
			DAOcms_mvtestings::instance ()->update ($record);
			
			//on met a jour les données génériques
			foreach ($this->_hei->getFields () as $propertyName) {
				$record->$propertyName = $desc[$propertyName];
			}
			$this->_hei->update ($record);

			$this->_saveElements ($record->id_mvt, $desc['elements']);

			//Application des changements
			_ppo ($record)->saveIn ($pDescription);
		} catch (CopixException $e) {
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
	 * @param object $pDescription Informations
	 */
	public function version ($pDescription) {
		HeadingCache::clear ();
		$desc = _ppo ($pDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($desc['id_mvt']);

			//on met a jour les données spécifiques			
			$record->public_id_hei = $desc['public_id_hei'];
			$record->choice_mvt = $desc['choice_mvt'];
			$record->current_mvt = $desc['current_mvt'];
			$record->conserve_mvt = $desc['conserve_mvt'];
			DAOcms_mvtestings::instance ()->insert ($record);

			//on met a jour les données génériques
			foreach ($this->_hei->getFields () as $propertyName){
				$record->$propertyName = $desc[$propertyName];
			}
			$record->id_helt = $record->id_mvt;
			$this->_hei->version ($record, $desc['id_mvt']);

			$this->_saveElements ($record->id_mvt, $desc['elements']);

			//Application des changements
			_ppo ($record)->saveIn ($pDescription);
		} catch (CopixException $e) {
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	/**
	 * Copie d'un mv testing
	 *
	 * @param int $pPublicId Identifiant publique
	 * @param int $pHeading Identifiant du répetoire où copier le mv testing
	 */
	public function copy ($pPublicId, $pHeading) {
		HeadingCache::clear ();
		CopixDB::begin ();
		try {
			$record = $this->getByPublicId ($pPublicId);
			$record->id_mvt = null;
			$record->public_id_hei = null;
			$record->parent_heading_public_id_hei = $pHeading;
			$record->current_mvt = ($record->choice_mvt == self::CHOICE_NEXT) ? 1 : null;
			DAOcms_mvtestings::instance ()->insert ($record);

			$record->id_helt = $record->id_mvt;
			$record->caption_hei = $record->caption_hei . ' (copie)';
			$this->_hei->insert ($record);

			$this->_saveElements ($record->id_mvt, $record['elements']);

			DAOcms_mvtestings::instance ()->update ($record);
		} catch (CopixException $e) {
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e; 
		}
		CopixDB::commit ();
		HeadingCache::clear ();
		return $record->public_id_hei;
	}

	/**
	 * Retourne un mv testing par son identifiant
	 *
	 * @param int $pIdHelt Identifiant
	 * @return DAORecordcms_mvtestings
	 */
	public function getById ($pIdHelt) {
		//on vérifie que l'élément existe
		$record = DAOcms_mvtestings::instance ()->get ($pIdHelt);
		if (!$record) {
			throw new HeadingElementInformationNotFoundException ($pIdHelt);
		}
		//on récupère les informations "communes"
		$mvtesting = $this->_hei->getById ($pIdHelt, 'mvtesting');
		//fusion des informations communes et spécifiques
		_ppo ($record)->saveIn ($mvtesting);

		// recherche des éléments
		$mvtesting->elements = DAOcms_mvtestings_headingelementinformations::instance ()->findBy (_daoSP ()->addCondition ('id_mvt', '=', $pIdHelt));
		return $mvtesting;
	}
	
	/**
	 * Recupere un enregistrement par son identifiant public
	 *
	 * @param unknown_type $pPublicId
	 * @return unknown
	 */
	public function getByPublicId ($pPublicId) {
		$element = $this->getById ($this->_hei->get ($pPublicId)->id_helt);
		return $element;
	}

	/**
	 * Supprime un ou plusieurs mv testing donnés en fonction du public_id
	 * Cette fonction supprime toutes les version
	 *
	 * @param int $pArPublicId le ou les identifiants 
	 */
	public function delete ($pArPublicId) {
		$idMVT = array ();
		foreach (DAOcms_mvtestings::instance ()->findBy (_daoSp ()->addCondition ('public_id_hei', '=', $pArPublicId)) as $record) {
			$idMVT[] = $record->id_mvt;
		}
		$this->deleteById ($idMVT);
	}

	/**
	 * Supression par identifiant interne
	 *
	 * @param int $pArId les identifiants des éléments à supprimer
	 */
	public function deleteById ($pArId) {
		DAOcms_mvtestings_headingelementinformations::instance ()->deleteBy (_daoSP ()->addCondition ('id_mvt', '=', $pArId));
		DAOcms_mvtestings::instance ()->deleteBy (_daoSp ()->addCondition ('id_mvt', '=', $pArId));
		HeadingCache::clear ();
	}
	
	/**
	 * Retourne les mv testing faisant référence à l'element de publicId $pPublicId
	 *
	 * @param int $pPublicId
	 */
	public function getDependencies ($pPublicId) {
		$toReturn = array ();
		foreach (DAOcms_mvtestings_headingelementinformations::instance ()->findBy (_daoSP ()->addCondition ('value_element', '=', $pPublicId)) as $record) {
			$toReturn[] = $this->getById ($record->id_mvt);
		}
		return $toReturn;
	}

	/**
	 * Retourne un nouvel élément
	 *
	 * @return CopixPPO
	 */
	public function getNew () {
		$toReturn = parent::getNew ();
		$toReturn->elements = array ();
		$toReturn->choice_mvt = self::CHOICE_NEXT;
		$toReturn->conserve_mvt = self::CONSERVE_SESSION;
		return $toReturn;
	}

	/**
	 * Retourne un nouvel élément
	 *
	 * @param int $pIdMVT Identifiant du mv testing associé
	 * @return DAORecordcms_mvtesting_elements
	 */
	public function getNewElement ($pIdMVT = null) {
		$toReturn = DAORecordcms_mvtestings_headingelementinformations::create ();
		$toReturn->id_mvt = $pIdMVT;
		$toReturn->type_element = self::TYPE_CMS;
		$toReturn->show_element = 0;
		return $toReturn;
	}

	/**
	 * Retourne l'élément à afficher
	 *
	 * @param int $pPublicId Identifiant publique du mv testing
	 * @param boolean $pSave Indique si on veut sauvegarder l'élément à visualiser + les stats de visualisation
	 * @return stdClass
	 */
	public function getNext ($pPublicId, $pSave = true) {
		$mvtesting = $this->getByPublicId ($pPublicId);
		$index = null;

		// élément redondant en session
		if ($mvtesting->conserve_mvt == self::CONSERVE_SESSION && CopixSession::exists ($mvtesting->id_helt, 'mvtesting')) {
			$index = CopixSession::get ($mvtesting->id_helt, 'mvtesting');
		// élément redondant dans un cookie
		} else if ($mvtesting->conserve_mvt == self::CONSERVE_COOKIE && CopixCookie::exists ($mvtesting->id_helt, 'mvtesting')) {
			$index = CopixCookie::get ($mvtesting->id_helt, 'mvtesting');
		}

		// si l'élément n'est pas redondant, ou l'est mais n'a pu être trouvé
		if ($index == null) {
			// élément suivant
			if ($mvtesting->choice_mvt == self::CHOICE_NEXT) {
				$index = (isset ($mvtesting->elements[$mvtesting->current_mvt + 1])) ? $mvtesting->current_mvt + 1 : 0;
				if ($pSave) {
					$mvtesting->current_mvt = $index;
					DAOcms_mvtestings::instance ()->update ($mvtesting);
				}
			// choix aléatoire
			} else if ($mvtesting->choice_mvt == self::CHOICE_RANDOM) {
				$index = rand (0, count ($mvtesting->elements) - 1);
			// pourcentage spécifique
			} else {
				$rand = rand (1, 100);
				$percents = 0;
				foreach ($mvtesting->elements as $elementIndex => $element) {
					$percents += $element->percent_element;
					if ($rand <= $percents) {
						$index = $elementIndex;
						break;
					}
				}
			}

			// sauvegarde si élément redondant en session
			if ($pSave) {
				if ($mvtesting->conserve_mvt == self::CONSERVE_SESSION) {
					CopixSession::set ($mvtesting->id_helt, $index, 'mvtesting');
				// sauvegarde si élément redondant dans un cookie
				} else if ($mvtesting->conserve_mvt == self::CONSERVE_COOKIE) {
					CopixCookie::set ($mvtesting->id_helt, $index, 'mvtesting');
				}
			}
		}
		
		if (!isset ($mvtesting->elements[$index])) {
			throw new CopixException ('La page demandée n\'existe pas.', 0, array ('public_id' => $pPublicId, 'mvtesting' => $mvtesting, 'index' => $index));
		}

		// si on veut sauvegarder les stats de visualisation
		if ($pSave) {
			_doQuery ('UPDATE cms_mvtestings_headingelementinformations SET show_element = show_element + 1 WHERE id_element = :id', array (':id' => $mvtesting->elements[$index]->id_element));
			$this->_showed['mvtesting'] = $mvtesting;
			$this->_showed['index'] = $index;
		}

		return $mvtesting->elements[$index];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see project/modules/devel/cms3/heading/classes/HeadingElementServices::findGhosts()
	 */
	public function findGhosts (){
		$toReturn['specific'] = _doQuery ('select * from cms_mvtestings where id_mvt not in(select id_helt from cms_headingelementinformations where type_hei = :type and status_hei <> :status)', array (':type'=>'mvtesting', ':status'=>HeadingElementStatus::DELETED));
		$toReturn['general']  = _doQuery ('select * from cms_headingelementinformations where type_hei = :type and id_helt not in (select id_mvt from cms_mvtestings)', array (':type'=>'mvtesting'));
		return $toReturn;
	}

	/**
	 * Retourne la liste des liens morts
	 *
	 * @return array
	 */
	public function findDeadLinks () {
		$toReturn = array ();
		$query = '
			SELECT el.* FROM cms_mvtestings_headingelementinformations el, cms_headingelementinformations hei
			WHERE el.type_element = ' . self::TYPE_CMS . '
			AND hei.id_helt = el.id_mvt
			AND hei.type_hei = :type
			AND hei.status_hei = :status
			AND el.value_element NOT IN (SELECT public_id_hei FROM cms_headingelementinformations WHERE status_hei = :status)
		';
		$params = array (':status' => HeadingElementStatus::PUBLISHED, ':type' => 'mvtesting');
		foreach (_doQuery ($query, $params) as $record) {
			$toReturn[] = array (
				'element' => $this->getById ($record->id_mvt),
				'linked_public_id_hei' => $record->value_element,
				'error' => 'L\'élément d\'identifiant publique "' . $record->value_element . '" n\'existe pas ou n\'est pas publié.'
			);
		}
		return $toReturn;
	}
}