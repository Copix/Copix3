<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link        http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Gérald Croës
 */

class LazyTreeElement
{
    private $element;
    private $types;
    private $depth;
    private $visiblesOnly;
    private $hasChildren;

    public function __construct ($element, $depth = 100, $pVisiblesOnly = false, $pTypes = array())
    {
        $this->element = $element;

        if (HeadingElementInformationServices::VISIBILITY_INHERITED == $this->element->show_in_menu_hei) {
            $inherited = 'foo';
            $this->element->show_in_menu_hei = _ioClass ('HeadingElementInformationServices')->getVisibility($this->element->public_id_hei, $inherited);
        }

        $this->types = $pTypes;
        $this->depth = $depth;
        $this->visiblesOnly = $pVisiblesOnly;
  
        $this->public_id_hei = $this->element->public_id_hei;
        $this->caption_hei   = $this->element->caption_hei;
  	$this->show_in_menu_hei = $this->element->show_in_menu_hei;
	$this->type_hei = $this->element->type_hei;
	$this->menu_html_class_name_hei = $this->element->menu_html_class_name_hei;
    }

    public function __get ($pName)
    {
        if ($pName === 'path'){
            return $this->path = _url ('heading||', array ( 'public_id'=>$this->element->public_id_hei,
                                                            'caption_hei'=>$this->element->caption_hei,
                                                            'url_id_hei'=>$this->element->url_id_hei,
                                                            'target_hei'=>$this->element->target_hei), true);
        } elseif ($pName === 'children') {
            $this->children = array();
            if ($this->depth >= 0){
                $this->loadChildren();
            }
            return $this->children;
        } else {
            return $this->element->$pName;
        }
    }

    public function __isset($pName)
    {
        if ($this->element->type_hei != 'heading') {
           return;
        }

        if ($this->hasChildren === null) {
            $this->hasChildren = DAOcms_headingelementinformations::instance ()->countBy ($this->getConditions()) > 1;
        }
        return $this->hasChildren;
    }

   private function getConditions ()
    {
        $conditions = _daoSP ()->addCondition ('parent_heading_public_id_hei', '=', $this->element->public_id_hei)
                ->addCondition ('status_hei', '=', HeadingElementStatus::PUBLISHED)
                ->orderBy ('display_order_hei');

        if ($this->visiblesOnly && $this->element->show_in_menu_hei === HeadingElementInformationServices::VISIBLE){
            //Si l'élément courant est visible, on prend inherited et visible
            $conditions->addCondition('show_in_menu_hei', '<>', HeadingElementInformationServices::INVISIBLE);
        } elseif ($this->visiblesOnly && $this->element->show_in_menu_hei === HeadingElementInformationServices::INVISIBLE) {
            //si l'élément courant est invisible (possible pour le root)
            // alors on prend que les visibles (et pas les hérités / invisibles)
            $conditions->addCondition('show_in_menu_hei', '=', HeadingElementInformationServices::VISIBLE);
        }

        if (count ($this->types) > 1) {
            $conditions->addCondition ('type_hei', '=', $this->types);
        }

        return $conditions;
    }

    private function hasChildren ()
    {
        if ($this->element->type_hei != 'heading') {
           return;
        }

        if ($this->hasChildren === null) {
            $this->hasChildren = DAOcms_headingelementinformations::instance ()->countBy ($this->getConditions()) > 1;
        }
        return $this->hasChildren;
    }

    private function loadChildren ()
    {
        if ($this->element->type_hei != 'heading'){
           return;
        }

        if ($this->hasChildren === false){
            return array();
        }

        $conditions = $this->getConditions();

        foreach (DAOcms_headingelementinformations::instance ()->findBy ($conditions) as $item) {
            HeadingCache::set ('heiservices|_getElement|' . $item->public_id_hei, $item, true, true);
            if (HeadingElementCredentials::canShow($item->public_id_hei)) {
                if (HeadingElementInformationServices::VISIBILITY_INHERITED == $item->show_in_menu_hei) {
                    //Inutile de calculer via le service, on sait déjà la valeur parente
                    $item->show_in_menu_hei = $this->element->show_in_menu_hei;
                }
                $this->children[] = new LazyTreeElement($item, $this->depth-1, $this->visiblesOnly, $this->types);
            }
        }
    }
}

class HeadingElementInformationException extends CopixException {
}

class HeadingElementWorkflowException extends HeadingElementInformationException {
}

/**
 * Exceptions en cas de demande d'un élément qui n'existe pas
 */
class HeadingElementInformationNotFoundException extends HeadingElementInformationException {
    function __construct ($pIdHelt, $pTypeHei = null, $pStatus = null) {
        if ($pTypeHei !== null) {
            parent::__construct ('Impossible de trouver les informations sur l\'élément '.$pIdHelt.' de type '.$pTypeHei);
        }else {
            if ($pStatus === null) {
                parent::__construct ('Impossible de trouver les informations sur l\'élément d\'identifiant public '.$pIdHelt);
            }else {
                parent::__construct ('Impossible de trouver les informations sur la version '._ioClass('heading|HeadingElementStatus')->getCaption ($pStatus).' l\'élément d\'identifiant public '.$pIdHelt);
            }
        }
    }
}

/**
 * Classe de gestion des informations communes sur les éléments de rubrique
 * @package     cms
 * @subpackage  heading
 */
class HeadingElementInformationServices {
    static private $_childrens = array ();

    static private $_visibility = array ();
    static private $_theme = array ();
    static private $_robots = array ();
    static private $_base_url = array ();

    static private $_credentials = null;

    const INVISIBLE = 0;
    const VISIBLE = 1;
    const VISIBILITY_INHERITED = 2;

    /**
     * Ordre de tri pour getGroupByStatusMaxVersion
     */
    const SORT_SHOW = 1;
    const SORT_TYPE = 2;
    const SORT_CAPTION = 3;
    const SORT_STATUS = 4;

    /**
     * Retourne un élément, pris dans le cache ou en base
     *
     * @param int $pPublicId Identifiant public
     * @return DAORecordcms_headingelementinformations
     */
    private function _getElement ($pPublicId) {
        $cacheId = 'heiservices|_getElement|' . $pPublicId;
        if (HeadingCache::exists ($cacheId, true)) {
            return HeadingCache::get ($cacheId, true);
        }


/*
        $results = DAOcms_headingelementinformations::instance ()->findBy (_daoSP ()->addCondition ('public_id_hei', '=', $pPublicId)
                                                                                    ->orderBy (array ('version_hei', 'DESC')));
*/

        $results = _doQuery ('select id_hei, status_hei
                              from cms_headingelementinformations
                              where public_id_hei = :publicIdHei
                              order by version_hei DESC'
				, array(':publicIdHei'=>$pPublicId)
                            );
        if (! count ($results)) {
            throw new HeadingElementInformationNotFoundException ($pPublicId);
        }

        //On va récupérer la dernière version en ligne/archivée/supprimée
        //ou, en l'absence, la dernière version
        $publishedVersion = null;
        $firstArchive = null;
        $firstDelete = null;
        foreach ($results as $element) {
            if (in_array ($element->status_hei, array (HeadingElementStatus::PUBLISHED, HeadingElementStatus::ARCHIVE, HeadingElementStatus::DELETED))) {
                if ($element->status_hei == HeadingElementStatus::PUBLISHED && $publishedVersion == null){
                	$publishedVersion = $element;
                }
                if ($element->status_hei == HeadingElementStatus::ARCHIVE && $firstArchive == null){
                	$firstArchive = $element;
                }
                if ($element->status_hei == HeadingElementStatus::DELETED && $firstDelete == null){
                	$firstDelete = $element;
                }
            }
        }
        $toReturn = null;
        //si on a une version publiée, on la prends en priorité
        if($publishedVersion){
        	$toReturn = $publishedVersion;
        }
		//sinon, si on a une version archivée, on la prends en priorité
        else if($firstArchive){
        	$toReturn = $firstArchive;
        }
        //sinon en derniere option, si on a une version archivée
     	else if($firstDelete){
        	$toReturn = $firstDelete;
        }
		
        if($toReturn){
            $toReturn = DAOcms_headingelementinformations::instance ()->get($toReturn->id_hei);
	    HeadingCache::set ($cacheId, $toReturn, true, true);
	    return $toReturn;
        }

        //sinon on récupère le premier élément
        $toReturn = DAOcms_headingelementinformations::instance ()->get($results[0]->id_hei);
        HeadingCache::set ($cacheId, $toReturn, true, true);
	return $toReturn;
    }

    /**
     * Sauvegarde un élément dans le cache
     *
     * @param int $pPublicId Identifiant public
     * @param DAORecordcms_headingelementinformations $pRecord
     */
    private function _setElement ($pPublicId, $pRecord) {
        HeadingCache::set ('heiservices|_getElement|' . $pPublicId, $pRecord, true, true);
        return $pRecord;
    }

    /**
     * Indique si l'élément est en cache
     *
     * @param int $pPublicId Identifiant public
     * @return boolean
     */
    private function _existsElement ($pPublicId) {
        return HeadingCache::exists ('heiservices|_getElement|' . $pPublicId, true);
    }

    /**
     * Création d'une nouvelle version pour l'élément donné
     * @return int numéro de la nouvelle version
     */
    public function version ($pHeadingElementInformation) {
        HeadingCache::clear ();

        $headingElementInformation = _ppo ($pHeadingElementInformation);
		$element = $this->get ($pHeadingElementInformation->public_id_hei);

        //création d'un record pour les opération de sauvegarde
        $record = DAORecordcms_headingelementinformations::create ()->initFromDbObject ($headingElementInformation);

		if (CopixConfig::get ('heading|cleanupURLs') && ($record->url_id_hei !== null)) {
			$record->url_id_hei = CopixUrl::escapeSpecialChars ($record->url_id_hei, true, true, './-');
		}
        // insertion de l'objet
        $this->fillMissing ($record, 'version');

        $record->from_version_hei = $record->version_hei;
        $record->version_hei = $this->getNextVersion ($headingElementInformation['public_id_hei']);

        $record->status_hei = HeadingElementStatus::DRAFT;
        $record->published_date_hei = null;
        $record->end_published_date_hei = null;
        DAOcms_headingelementinformations::instance ()->insert ($record);

        // afin que toutes les données ajoutées / maj durant le processus soient disponibles
        // a la sortie de la méthode, applique les changements survenus
        _ppo ($record)->saveIn ($pHeadingElementInformation);

        HeadingCache::clear ();
		$this->_notifyActions ($element, $record);
		_ioClass ('HeadingActionsService')->notify (HeadingActionsService::VERSION, $record);

        // retour de l'identifiant public
        return $record->public_id_hei;
    }

    /**
     * Création d'un nouvel élément
     * @param object $pHeadingElementInformation
     * @return int public_id de l'élément nouvellement généré
     */
    public function insert ($pHeadingElementInformation) {
        HeadingCache::clear ();

        $headingElementInformation = _ppo ($pHeadingElementInformation);

        //création d'un record pour les opération de sauvegarde
        $record = DAORecordcms_headingelementinformations::create ()->initFromDbObject ($headingElementInformation);

		if (CopixConfig::get ('heading|cleanupURLs') && ($record->url_id_hei !== null)) {
			$record->url_id_hei = CopixUrl::escapeSpecialChars ($record->url_id_hei, true, true, './-');
		}
        // insertion de l'objet
        $this->fillMissing ($record, 'create');
        DAOcms_headingelementinformations::instance ()->insert ($record);
        $this->_updatePublicIdIfMissing ($record);

        $this->_fillHierarchy ($record);
        DAOcms_headingelementinformations::instance ()->update ($record);

        // afin que toutes les données ajoutées / maj durant le processus soient disponibles
        // a la sortie de la méthode, applique les changements survenus
        _ppo ($record)->saveIn ($pHeadingElementInformation);

		_ioClass ('HeadingActionsService')->notify (HeadingActionsService::INSERT, $pHeadingElementInformation);
        HeadingCache::clear ();

        // retour de l'identifiant public
        return $record->public_id_hei;
    }

	/**
	 * Recherche les modifications effectuées entre les 2 records et effectue une notification pour chacun d'elle
	 *
	 * @param DAORecordcms_headingelementinformations $pPrevRecord Record de base
	 * @param DAORecordcms_headingelementinformations $pNewRecord Record avec les nouvelles valeurs
	 */
	private function _notifyActions ($pPrevRecord, $pNewRecord) {
		// commentaire
		if ($pPrevRecord->comment_hei != $pNewRecord->comment_hei) {
			_ioClass ('HeadingActionsService')->notify (HeadingActionsService::COMMENT_CHANGE, $pNewRecord);
		}
		// cible
		if ($pPrevRecord->target_hei != $pNewRecord->target_hei || $pPrevRecord->target_params_hei != $pNewRecord->target_params_hei) {
			$extras = array (
				'target_prev' => HeadingElementTargetHandler::getCaption ($pPrevRecord->target_hei) . '|' . $pPrevRecord->target_params_hei,
				'target_new' => HeadingElementTargetHandler::getCaption ($pNewRecord->target_hei) . '|' . $pNewRecord->target_params_hei
			);
			_ioClass ('HeadingActionsService')->notify (HeadingActionsService::TARGET_CHANGE, $pNewRecord, $extras);
		}
		// thème
		if ($pPrevRecord->theme_id_hei != $pNewRecord->theme_id_hei) {
			$extras = array ('theme_prev' => $pPrevRecord->theme_id_hei, 'theme_new' => $pNewRecord->theme_id_hei);
			_ioClass ('HeadingActionsService')->notify (HeadingActionsService::THEME_CHANGE, $pNewRecord, $extras);
		}
		// url
		if ($pPrevRecord->base_url_hei != $pNewRecord->base_url_hei || $pPrevRecord->url_id_hei != $pNewRecord->url_id_hei) {
			$extras = array (
				'base_url_hei_prev' => $pPrevRecord->base_url_hei,
				'base_url_hei_new' => $pNewRecord->base_url_hei,
				'url_id_hei_prev' => $pPrevRecord->url_id_hei,
				'url_id_hei_new' => $pNewRecord->url_id_hei
			);
			$action = ($pNewRecord->base_url_hei == null) ? HeadingActionsService::URL_INHERITED : HeadingActionsService::URL_CHANGE;
			_ioClass ('HeadingActionsService')->notify ($action, $pNewRecord, $extras);
		}
	}

    /**
     * Modification de l'élément donné sans modification de version
     * -- Les conditions de l'update sont sur le id_helt / type_helt --
     *
     * Les champs pouvant être modifiés sont parent_heading_public_id, comment_hei, publiched_date_hei, end_published_date_hei,
     *  status_hei, version_hei, from_version_hei, show_in_menu_hei, base_url_hei, url_id_hei, public_id_root_menu_hei,
     *  public_id_contextual_menu_hei, theme_id_hei
     *
     * Les champs mis à jour automatiquement par la méthode sont author_id_update_hei, author_handler_update_hei, author_caption_update_hei,
     *  date_update_hei
     */
    public function update ($pHeadingElementInformation, $pMarkupdated = true) {
        HeadingCache::clear ();

		$element = $this->getById ($pHeadingElementInformation->id_helt, $pHeadingElementInformation->type_hei);
        $headingElementInformation = _ppo ($pHeadingElementInformation);

        //création d'un record pour les opération de sauvegarde
        $record = DAORecordcms_headingelementinformations::create ()->initFromDbObject ($headingElementInformation);

		if (CopixConfig::get ('heading|cleanupURLs') && ($record->url_id_hei !== null)) {
			$record->url_id_hei = CopixUrl::escapeSpecialChars ($record->url_id_hei, true, true, './-');
		}
        //insertion de l'objet
        $this->fillMissing ($record, 'update', $pMarkupdated);
        DAOcms_headingelementinformations::instance ()->update ($record);
        //afin que toutes les données ajoutées / maj durant le processus soient disponibles
        //a la sortie de la méthode, applique les changements survenus
        _ppo ($record)->saveIn ($pHeadingElementInformation);

        HeadingCache::clear ();

		// notifications de modification
		$this->_notifyActions ($element, $record);
		_ioClass ('HeadingActionsService')->notify (HeadingActionsService::UPDATE, $pHeadingElementInformation);

        //retour de l'identifiant public
        return $record->public_id_hei;
    }

    /**
     * Copie d'un élément
     *
     * @param int $pPublicId
     */
    public function copy ($pPublicId, $pHeading) {
        HeadingCache::clear ();
		_ioClass ('HeadingActionsService')->notifyByPublicId (HeadingActionsService::COPY, $pPublicId, array ('to' => $pHeading));
        $element = $this->get ($pPublicId);
        $typeInformations = _ioClass('heading|headingelementtype')->getInformations ($element->type_hei);
        $toReturn = _ioClass($typeInformations['classid'])->copy ($pPublicId, $pHeading);
        HeadingCache::clear ();
		return $toReturn;
    }

    /**
     * Supression de l'élément dans la table
     *
     * @param string $pId   l'identifiant de l'élément à supprimer
     * @param string $pType le type de l'élément à supprimer
     * @param bool   $pInTransaction s'il faut insérer les requêtes dans une transaction. (mettre false si l'appelant réalise déja une transaction)
     * @param boolean $pHardDelete	indique s'il faut supprimer définitivement (true) ou de façon logique (false) 
     */
    public function deleteById ($pId, $pType, $pInTransaction = true, $pHardDelete = false) {
        HeadingCache::clear ();

        $element = $this->getById ($pId, $pType);
        if ($element->type_hei == 'heading') {
            //Si c'est une rubrique, on demande la supression par public id qui gère le coté récursif dans les sous rubriques
            return $this->delete ($element->public_id_hei);
        }

        //si ce n'est pas une rubrique, demande la supression classique de l'élément
        $arHeadingElementType = _ioClass('heading|headingelementtype')->getList ();
        try {
            if ($pInTransaction) {
                CopixDb::begin ();
            }

            // appel de l'événement DeletedContent
            $headings = array ();
            if ($pType == 'page') {
                $headings[$element->public_id_hei] = _url ('heading||', array ('public_id' => $element->public_id_hei));
            } else {
                // on met l'identifiant en clef parceque getDependencies ne renvoie pas que la dernière version,
                // on peut donc avoir plusieurs versions d'une page pour le même public_id
                foreach (_ioClass ('portal|PageServices')->getDependencies ($element->public_id_hei) as $page) {
                    $headings[$page->public_id_hei] = _url ('heading||', array ('public_id' => $page->public_id_hei));
                }
            }
            foreach ($headings as $id => $url) {
                _notify ('DeletedContent', array ('url' => $url));
            }

			_ioClass ('HeadingActionsService')->notifyById (HeadingActionsService::DELETE, $pId, $pType);

			$parameters = _daoSP ()->addCondition ('id_helt', '=', $pId)->addCondition ('type_hei', '=', $pType);
			if ($pHardDelete){
            	_ioClass($arHeadingElementType[$pType]['classid'])->deleteById ($pId);
			}
            
            $update = _ppo (array('status_hei' => HeadingElementStatus::DELETED));
            DAOcms_headingelementinformations::instance ()->updateBy ($update, $parameters);
            if ($pInTransaction) {
                CopixDB::commit ();
            }
        }catch (Exception $e) {
            if ($pInTransaction) {
                CopixDb::rollback ();
            }
            HeadingCache::clear ();
            throw $e;
        }
        HeadingCache::clear ();
    }

    /**
     * Supression de toutes les versions de l'élément dont le public id est passé en paramètre
     *
     * @param int / array $pPublicId        l'identifiant de l'élément à supprimer, ou un tableau d'identifiant
     * @param array       $pArByIdElements  les éléments à supprimer par identifiants internes
     * @param boolean	$pHardDelete	Indique s'il faut supprimer définitivement les éléments (true) ou logiquement (false)
     *
     * @return void
     */
    public function delete ($pPublicId, $pArByIdElements = array (), $pHardDelete = false) {
        HeadingCache::clear ();

        // On caste le paramètre en tableau
        $arPublicId = (array) $pPublicId;

        // Récupération de l'ensemble des identifiants à supprimer
        $finalPublicId = array ();
        foreach ($arPublicId as $id) {
            $this->_findAllChildrensPublicId ($id, $finalPublicId);
        }

        //éclatement des publicId par types, séparation des id de rubrique dans un autre tableau
        $arByTypePublicId = array ();
        $arHeadingPublicId = array ();
        $arAllPublicId = array ();
        foreach ($finalPublicId as $publicId) {
            if ($publicId['type_hei'] === 'heading') {
                $arHeadingPublicId[] = $publicId['public_id_hei'];
            }else {
                $arByTypePublicId[$publicId['type_hei']][] = $publicId['public_id_hei'];
            }
            $arAllPublicId[] = $publicId['public_id_hei'];
        }

        //Une fois que tous les public_id ont étés trouvés, supression
        $arHeadingElementType = _ioClass('heading|headingelementtype')->getList ();
        try {
            CopixDb::begin ();

            // appel de l'événement DeletedContent
            foreach ($arPublicId as $publicId) {
                $element = $this->get ($publicId);
                $headings = array ();
                if ($element->type_hei == 'page') {
                    $headings[$publicId] = _url ('heading||', array ('public_id' => $publicId));
                } else {
                    // on met l'identifiant en clef parceque getDependencies ne renvoie pas que la dernière version,
                    // on peut donc avoir plusieurs versions d'une page pour le même public_id
                    foreach (_ioClass ('portal|PageServices')->getDependencies ($publicId) as $page) {
                        $headings[$page->public_id_hei] = _url ('heading||', array ('public_id' => $page->public_id_hei));
                    }
                }
                foreach ($headings as $url) {
                    _notify ('DeletedContent', array ('url' => $url));
                }
            }

            //Supression des tables spécifiques (hors rubriques)
            foreach ($arByTypePublicId as $type=>$arPublicId) {
				foreach ($arPublicId as $public_id) {
					_ioClass ('HeadingActionsService')->notifyByPublicId (HeadingActionsService::DELETE, $public_id);
					if ($pHardDelete){
						_ioClass($arHeadingElementType[$type]['classid'])->delete ($public_id);
					}
				}
            }

            //supression des tables spécifiques (pour les rubriques)
			foreach ($arHeadingPublicId as $public_id) {
				_ioClass ('HeadingActionsService')->notifyByPublicId (HeadingActionsService::DELETE, $public_id);
				if ($pHardDelete){
					_ioClass($arHeadingElementType['heading']['classid'])->delete ($public_id);
				}
			}

            //supression de la table commune pour tous les éléments
            DAOcms_headingelementinformations::instance ()->updateBy (_ppo (array ('status_hei'=>HeadingElementStatus::DELETED)), _daoSP ()->addCondition ('public_id_hei', '=', $arAllPublicId));

            //supression des éléments par identifiants internes
            foreach ($pArByIdElements as $element) {
                $this->deleteById ($element->id_helt, $element->type_hei, false);
            }
            CopixDb::commit ();
        }catch (Exception $e) {
            CopixDb::rollback ();
            HeadingCache::clear ();
            throw $e;
        }
        HeadingCache::clear ();
    }

    /**
     * Archivage des élements indiqués en paramètres
     *
     * @param int/array
     */
    public function archive ($pPublicId) {
        HeadingCache::clear ();

        // On cas $pPublicId en tableau
        $arPublicId = (array) $pPublicId;

        // Récupération de l'ensemble des identifiants à archiver
        $finalPublicId = array ();
        foreach ($arPublicId as $id) {
            $this->_findAllChildrensPublicId ($id, $finalPublicId);
        }

        try {
            CopixDb::begin ();
            foreach ($finalPublicId as $element) {
                $record = $this->get ($element['public_id_hei']);
                if ($record->type_hei != 'heading') {
					_ioClass ('HeadingActionsService')->notifyByPublicId (HeadingActionsService::ARCHIVE, $record->public_id_hei);
                    $record->status_hei = HeadingElementStatus::ARCHIVE;
                    $record->date_update_hei = date ('Ymdhis');
                    DAOcms_headingelementinformations::instance ()->update ($record);
                }
            }
            CopixDb::commit ();
        }catch (Exception $e) {
            CopixDb::rollback ();
            HeadingCache::clear ();
            throw $e;
        }
        HeadingCache::clear ();
    }

    /**
     * Complète le tableau en paramètre pour y placer l'ensemble des identifiants dans l'arborescence parente $pPublicId
     *
     * @param int   $pPublicId   l'élémént dont on souhaite connaitre la descendance
     * @param array $pArPublicId le tableau a compléter
     *
     * @return void
     */
    protected function _findAllChildrensPublicId ($pPublicId, & $pArPublicId) {
        $element = $this->get ($pPublicId);

        if (!in_array ($pPublicId, $pArPublicId)) {
            $pArPublicId[] = array ('public_id_hei'=>$pPublicId, 'type_hei'=>$element->type_hei);
        }

        if ($this->get ($pPublicId)->type_hei == 'heading') {
            foreach ($this->getChildren ($pPublicId) as $child) {
                if ($child->type_hei === 'heading') {
                    $this->_findAllChildrensPublicId ($child->public_id_hei, $pArPublicId);
                }else {
                    $pArPublicId[] = array ('public_id_hei'=>$child->public_id_hei, 'type_hei'=>$child->type_hei);
                }
            }
        }
    }

    /**
     * Récupération des fils d'un élément (ne peut avoir des fils que si c'est une rubrique)
     * @param int $pPublicid
     */
    public function getChildren ($pPublicId) {
        if ($this->get ($pPublicId)->type_hei !== 'heading') {
            $toReturn = array ();
        } else {
            $toReturn = DAOcms_headingelementinformations::instance ()->findBy (_daoSP ()->addCondition ('parent_heading_public_id_hei', '=', $pPublicId)->addCondition ('status_hei', '<>', HeadingElementStatus::DELETED));
        }
        return $toReturn;
    }

    /**
     * Récupération du couple identifiant de l'élément en fonction de son public id
     * On prend systématiquement l'éléemnt publié (ou plannifié par défaut)
     * @return array[id_helt] / array[type_hei]
     */
    public function getId ($pPublicId) {
        $element = $this->get ($pPublicId);
        return array ('id_helt' => $element->id_helt,
                'type_hei' => $element->type_hei);
    }

    /**
     * Récupération du public id de l'élément
     * @param int    $pIdHelt  l'identifian de l'élément
     * @param string $pTypeHei le type de l'élément
     * @return int le public id
     */
    public function getPublicId ($pIdHelt, $pTypeHei) {
        return $this->getById ($pIdHelt, $pTypeHei)->public_id_hei;
    }

    /**
     * Indique si l'élément donné dispose d'enfants
     * @param	$pPublicId	l'identifiant public de l'élément a tester
     * @return  boolean
     */
    public function hasChild ($pPublicId) {
        return DAOcms_headingelementinformations::instance ()->countBy (_daoSP ()->addCondition ('parent_heading_public_id_hei', '=', $pPublicId)->addCondition ('status_hei', '<>', HeadingElementStatus::DELETED)) > 0;
    }

    /**
     * Indique si l'élément donné dispose d'enfants
     * Note : seules les rubriques peuvent avoir des enfants, ce qui simplifie le problème
     */
    public function hasChildById ($pIdHelt, $pTypeHei) {
        return $this->hasChild ($this->getPublicId ($pIdHelt, $pTypeHei));
    }

    /**
     * Remplis les informations manquantes sur l'élément
     */
    public function fillMissing ($pHEI, $pMode, $pMarkUpdated = true) {
        //Dates de mises à jour
        $date = date ('YmdHis');
        if (!isset ($pHEI->date_create_hei ) ||  $pMode != 'update') {
            $pHEI->date_create_hei = $date;
        }
        //on met tout le temps à jour la date de mise à jour
        $pHEI->date_update_hei = $date;

        //Auteurs
        $id      = _currentUser ()->getId ();
        $handler = _currentUser ()->getHandler ();
        $caption = _currentUser ()->getCaption ();

        //ID
        if ($pMarkUpdated) {
            $pHEI->author_id_update_hei = $id;
        }
        if (!isset ($pHEI->author_id_create_hei)) {
            $pHEI->author_id_create_hei = $id;
        }
        //handler
        if ($pMarkUpdated){
            $pHEI->author_handler_update_hei = $handler;
        }
        if (!isset ($pHEI->author_handler_create_hei)) {
            $pHEI->author_handler_create_hei = $handler;
        }
        //libellé
        if ($pMarkUpdated){
            $pHEI->author_caption_update_hei = $caption;
        }
        if (!isset ($pHEI->author_caption_create_hei)) {
            $pHEI->author_caption_create_hei = $caption;
        }

        //SiteId
        if (!isset ($pHEI->site_id_hei)) {
            $pHEI->site_id_hei = CopixConfig::get ('heading|site_id');
        }

        //Status - Si aucun donné, on considère "PUBLISHED"
        if (!isset ($pHEI->status_hei)) {
            if ($pHEI->type_hei == 'heading') {
                $pHEI->status_hei = HeadingElementStatus::PUBLISHED;
            }else {
                $pHEI->status_hei = HeadingElementStatus::DRAFT;
            }
        }

        //Version / From Version
        if (!isset ($pHEI->version_hei) || $pHEI->version_hei == 0) {
            $pHEI->version_hei = 1;
        }
        if (!isset ($pHEI->from_version_hei)) {
            $pHEI->from_version_hei = 0;
        }

        if (!isset ($pHEI->display_order_hei)) {
            $pHEI->display_order_hei = $this->getNextDisplayOrderValue ($pHEI->parent_heading_public_id_hei);
        }

        // Affichage dans le menu
        if (!isset ($pHEI->show_in_menu_hei)) {
            $pHEI->show_in_menu_hei = 2;
        }

        if ($pHEI->parent_heading_public_id_hei === 0) {
            if (!isset ($pHEI->theme_id_hei)) {
                $pHEI->theme_id_hei = array_search (CopixTpl::getTheme(), CopixTpl::getThemesList());
            }
        }

        if (!isset ($pHEI->tags_inherited_hei)) {
            $pHEI->tags_inherited_hei = 1;
        }

        if (!isset ($pHEI->credentials_inherited_hei)) {
            $pHEI->credentials_inherited_hei = 1;
        }
    }

    /**
     * Cette fonction met à jour le publicid dans l'enregistrement si ce dernier n'est pas présent
     * @return boolean	s'il a fallu mettre l'enregistrement a jour ou non
     */
    protected function _updatePublicIdIfMissing ($pHEI) {
        if (!isset ($pHEI->public_id_hei)) {
            $pHEI->public_id_hei = $pHEI->id_hei;
            DAOcms_headingelementinformations::instance ()->update ($pHEI);
            HeadingCache::clear ();
            return true;
        }
        return false;
    }

    /**
     * Donne le numéro de version "suivant" pour un contenu d'identifiant donné
     *
     * @param int $pPublicId	l'identifiant public de l'élément dont on souhaite connaitre le numéro de version
     * @return int	le numéro de version a venir
     */
    public function getNextVersion ($pPublicId) {
        return $this->getMaxVersion ($pPublicId) + 1;
    }

    /**
     * Donne le numéro de version maximum pour un identifiant donné
     *
     * @param int $pPublicId	l'identifiant public de l'élément dont on souhaite connaitre le numéro de version
     * @return int	le numéro de version a venir
     */
    public function getMaxVersion ($pPublicId) {
        //On utilise $this->get pour s'assurer que l'enregistrement existe
        $results = _doQuery ('select MAX(version_hei) max_version from cms_headingelementinformations where public_id_hei = :public_id', array (':public_id' => $this->get ($pPublicId)->public_id_hei));
        return $results[0]->max_version;
    }

    /**
     * Récupération du chemin des rubriques en fonction d'un identifiant public
     *
     * @param	int    $pPublicId   l'identifiant de l'élément dont on recherche le chemin
     * @return array
     */
    public function getHeadingPath ($pPublicId) {
        return _ioClass ('heading|headingservices')->getPath ($pPublicId);
    }

	/**
	 * Retourne les caption_hei des rubriques pour aller au public_id demandé
	 *
	 * @param int $pPublicId Identifiant public
	 * @return array
	 */
	public function getHeadingPathCaption ($pPublicId) {
		$toReturn = array ();
		$headings = array_reverse (_ioClass ('heading|headingservices')->getPath ($pPublicId));
		foreach ($headings as $public_id) {
			$element = $this->get ($public_id);
			if ($element->type_hei == 'heading') {
				$toReturn[] = $element->caption_hei;
			}
		}
		return $toReturn;
	}

    /**
     * Récupération des informations d'un élément en fonction de son PublicId
     *
     * @param	int $pPublicId l'identifiant public de l'élémént que l'on souhaite récupérer
     * @return  DAORecordcms_headingelementinformations
     */
    public function get ($pPublicId) {
        return $this->_getElement ($pPublicId);
    }

    /**
     * Récupération des informations d'un élément en fonction de son couple identifiant
     *
     * @param	int $pIdHelt  l'identifiant de l'élémént que l'on souhaite récupérer
     * @param	int $pTypeHei le type de l'élément que l'on souhaite récupérer
     * @return  CopixDAORecord
     */
    public function getById ($pIdHelt, $pTypeHei) {
        $results = DAOcms_headingelementinformations::instance ()->findBy (_daoSP ()->addCondition ('id_helt', '=', $pIdHelt)->addCondition('type_hei', '=', $pTypeHei));
        if (count ($results)) {
            return $results[0];
        }
        throw new HeadingElementInformationNotFoundException ($pIdHelt, $pTypeHei);
    }
    
    
    /**
     * 
     * Vérifie les dates de planification des différents éléments pour un public_id donné
     * Publie la version qui doit l'être, archive la version publiée si besoin
     * @param $pPublicId
     */
    public function checkElementPlanning ($pPublicId){
    	try{
    		$plannedVersions = $this->getPlannedVerions($pPublicId);
    		$nearestDate = 0;
    		$elementToPublish = null;
    		foreach ($plannedVersions as $version){
    			if ($version->published_date_hei && ($timeStamp = CopixDateTime::yyyymmddhhiissToTimeStamp($version->published_date_hei)) <= time()){
    				if($timeStamp > $nearestDate){
    					$nearestDate = $timeStamp;
    					$elementToPublish = $version;
    				}    			
    			}    			
    		}
    		if($elementToPublish){
    			$this->publishById($elementToPublish->id_helt, $elementToPublish->type_hei);
    		}
    	} catch (HeadingElementInformationNotFoundException $e){
    		//il n'y a pas de version splanifiées
    	}
    	//on vérifie que la version publiée n'a pas atteint sa date de fin de publication
    	$publishedElement = $this->get($pPublicId);
    	if ($publishedElement->status_hei == HeadingElementStatus::PUBLISHED
    			&& $publishedElement->end_published_date_hei 
    			&& CopixDateTime::yyyymmddhhiissToTimeStamp($publishedElement->end_published_date_hei) < time()){
    		$this->archive($pPublicId);
    		//on renvoie l'element archivé pour mesage d'erreur ou erreur 404
    		return $this->get($pPublicId);
    	}
    	//on renvoie l'element publié
    	return $publishedElement;
    }

    /**
     * Donne tous les frères d'un élément
     *
     * @param int $pPublicId
     * @return unknown $elements
     */
    public function getHeadingBrothers ($pPublicId) {
        return $this->getHeadingChildren ($this->get ($pPublicId)->parent_heading_public_id_hei);
    }

    /**
     * Recherche des éléments en fonction de divers critères
     *
     * @param array $pParams['parent_id_hei', 'author_id_update_hei_from',
     *  'author_id_update_hei_to', 'author_handler_update',
     *  'date_update_hei', 'date_create_hei_from', 'date_create_hei_to',
     *  'type_hei', 'version_hei', 'caption_hei',
     *  'status_hei', 'published_date_hei_from', 'published_date_hei_to', 'end_published_date_hei_from',
     *  'end_published_date_hei_to'] critères de recherche
     * @return array
     */
    public function find ($pParams) {
        $sp = _daoSP ();
        $value = $pParams;

        if (isset($value['id_helt'])) {
            $sp->addCondition ('id_helt', '=', $value['id_helt']);
        }
        if (isset($value['type_hei'])) {
            $sp->addCondition ('type_hei', '=', $value['type_hei']);
        }
        if (isset($value['public_id_hei'])) {
            $sp->addCondition ('public_id_hei', '=', $value['public_id_hei']);
        }
        if (isset($value['parent_heading_public_id_hei'])) {
            $sp->addCondition ('parent_heading_public_id_hei', '=', $value['parent_heading_public_id_hei']);
        }
        if (isset($value['author_id_update_hei'])) {
            if (!is_array ($value['author_id_update_hei'])) {
                $sp->addCondition ('author_id_update_hei', '=', $value['author_id_update_hei']);
            } else {
                foreach ($value['author_id_update_hei'] as $author) {
                    $sp->addCondition ('author_id_update_hei', '=', $author);
                }
            }
        }
        if (isset($value['author_handler_update'])) {
            $sp->addCondition ('author_handler_update', '=', $value['author_handler_update']);
        }
        if (isset($value['date_update_hei_from'])) {
            $sp->addCondition ('date_update_hei', '>=', $value['date_update_hei_from']);
        }
        if (isset ($value['date_update_hei_to'])) {
            $sp->addCondition ('date_update_hei', '<=', $value['date_update_hei_to']);
        }
        if (isset ($value['date_create_hei_from'])) {
            $sp->addCondition ('date_create_hei', '>=', $value['date_create_hei_from']);
        }
        if (isset ($value['date_create_hei_to'])) {
            $sp->addCondition ('date_create_hei', '<=', $value['date_create_hei_to']);
        }
        if (isset ($value['type_hei'])) {
            $sp->addCondition ('type_hei', '=', $value['type_hei']);
        }
        if (isset ($value['version_hei'])) {
            $sp->addCondition ('version_hei', '=', $value['version_hei']);
        }
        if (isset ($value['status_hei'])) {
            $sp->addCondition ('status_hei', '=', $value['status_hei']);
        }
        if (isset ($value['caption_hei'])) {
            $sp->addCondition ('caption_hei', 'LIKE', $value['caption_hei']);
        }
        if (isset ($value['published_date_hei_from'])) {
            $sp->addCondition ('published_date_hei', '>=', $value['published_date_hei_from']);
        }
        if (isset ($value['published_date_hei_to'])) {
            $sp->addCondition ('published_date_hei', '>=', $value['published_date_hei_to']);
        }
        if (isset ($value['end_published_date_hei_from'])) {
            $sp->addCondition ('end_published_date_hei', '<=', $value['end_published_date_hei_from']);
        }
        if (isset ($value['end_published_date_hei_to'])) {
            $sp->addCondition ('end_published_date_hei_to', '<=', $value['end_published_date_hei_to']);
        }

        if (isset ($value['display_order_hei'])) {
            $sp->addCondition ('display_order_hei', '=', $value['display_order_hei']);
        }

    	if (isset($value['hierarchy_hei'])) {
            $sp->addCondition ('hierarchy_hei', 'LIKE', ($value['hierarchy_hei'] == 0 ? "" : "%-").$value['hierarchy_hei']."-%");
        }
        
        if (isset($value['order_by'])) {
            $sp->orderBy ($value['order_by']);
        }
        
        return DAOcms_headingelementinformations::instance ()->findBy ($sp);
    }

    /**
     * Retourne les fils d'un élément sous forme d'un tableau
     *
     * @param int  $pPublicId     l'identifiant public de la rubrique dont on veut connaître les enfants
     * @param bool $pHeadingsOnly si l'on souhaite connaitre uniquement les enfants de type heading
     * @return array
     */
    public function getHeadingChildren ($pPublicId, $pHeadingsOnly = false) {
        $toReturn = array ();
        if (! array_key_exists ($pPublicId, self::$_childrens)) {
            $this->_loadHeadingChildren ($pPublicId);
        }
        if ($pHeadingsOnly) {
            foreach (self::$_childrens[$pPublicId] as $child) {
                if (HeadingElementCredentials::canWrite($child->public_id_hei)
                        && $child->type_hei == 'heading') {
                    $toReturn[] = $child;
                }
            }
        } else {
            $toReturn = self::$_childrens[$pPublicId];
        }
        return $toReturn;
    }

    private function _loadHeadingChildren ($pPublicId) {
        //on regarde les éléments qu'il faut vraiment charger.
        $toLoad = array ();
        if (is_array ($pPublicId)) {
            foreach ($pPublicId as $publicId) {
                if (! array_key_exists ($publicId, self::$_childrens)) {
                    $toLoad[] = $publicId;
                    self::$_childrens[$publicId] = array ();
                }
            }
        }else {
            $toLoad = array ($pPublicId);
            self::$_childrens[$pPublicId] = array ();
        }

        $sp = _daoSP ()->addCondition ('parent_heading_public_id_hei', '=', $pPublicId)
                       ->addCondition ('status_hei', '<>', HeadingElementStatus::DELETED)->orderBy ('display_order_hei');
        $records = DAOcms_headingelementinformations::instance ()->findBy ($sp);

        foreach ($records as $value) {
            if(!(array_key_exists($value->public_id_hei, self::$_childrens[$value->parent_heading_public_id_hei])) || HeadingElementStatus::PUBLISHED == $value->status_hei) {
                //on met dans le tableau en preference les elements publiés.
                self::$_childrens[$value->parent_heading_public_id_hei][$value->public_id_hei] = $value;
            }
        }
    }

    /**
     * Renvoie le numéro d'ordre d'affichage d'un nouvel élément
     *
     * @param int $pParentHeadingPublicIdHei la rubrique dont on souhaite connaitre l'indice de position maximum
     * @return unknown $results[0]->display_order_hei
     */
    public function getNextDisplayOrderValue ($pParentHeadingPublicIdHei) {
        if (count ($results = _doQuery ('select MAX(display_order_hei) from cms_headingelementinformations where parent_heading_public_id_hei = :parent_heading_public_id_hei', array (':parent_heading_public_id_hei' => $pParentHeadingPublicIdHei)))) {
            $display_order_hei = get_object_vars ($results[0]);
            list (, $display_order_hei) = each ($display_order_hei);
            return $display_order_hei+1;
        }
        return 1;
    }

    /**
     * Déplace l'élément donné dans une autre rubrique
     *
     * @param int $pPublicId    l'identifiant public de l'élément à déplacer
     * @param int $pDestHeading l'identifiant public de la rubrique destination
     */
    public function move ($pPublicId, $pDestHeading) {
        HeadingCache::clear ();
        $destHeading = $this->get ($pDestHeading);

		_ioClass ('HeadingActionsService')->notifyByPublicId (HeadingActionsService::MOVE, $pPublicId, array ('to' => $pDestHeading));

        //Modification de l'élément
        _doQuery ('update cms_headingelementinformations set parent_heading_public_id_hei = :parent_heading_public_id_hei,
							hierarchy_hei = :hierarchy,
							hierarchy_level_hei = :level
							where public_id_hei = :public_id_hei',
                array (':parent_heading_public_id_hei'=>$pDestHeading,
                ':public_id_hei'=>$pPublicId,
                ':hierarchy'=>$destHeading->hierarchy_hei . "-" . $pPublicId,
                ':level'=>$destHeading->hierarchy_level_hei + 1));

		HeadingCache::clear ();
        $element = $this->_getElement ($pPublicId);
        $this->_fillHierarchy ($element, true);
		$element = $this->_getElement ($pPublicId);
       HeadingCache::clear ();
    }

    /**
     * Déplace un élément vers le haut de la liste
     *
     * @param int $pPublicId l'identifiant public de l'élément à monter dans la liste
     */
    public function moveUp ($pPublicId) {
        HeadingCache::clear ();

        //récupère l'élément à modifier
        $element = $this->get ($pPublicId);
        $this->reorderLevel ($element->parent_heading_public_id_hei);
        //redemande la récupération de l'élément au cas ou le changement d'ordre est fait effet
        $element = $this->get ($pPublicId);

        if ($element->display_order_hei > 1) {
			_ioClass ('HeadingActionsService')->notifyByPublicId (HeadingActionsService::POSITION_CHANGE, $pPublicId, array ('to' => $element->display_order_hei - 1));
            //modification de la position de l'élément précédent
            _doQuery ('update cms_headingelementinformations set display_order_hei = :order
							where parent_heading_public_id_hei = :parentHeadingId
								and display_order_hei = :previousDisplayOrder',
                    array (':order'=>$element->display_order_hei,
                    ':parentHeadingId'=>$element->parent_heading_public_id_hei,
                    ':previousDisplayOrder'=>$element->display_order_hei-1));
            //modification de la position de l'élément a monter
            _doQuery ('update cms_headingelementinformations set display_order_hei = :order
							where public_id_hei = :id',
                    array (':order'=>$element->display_order_hei-1,
                    ':id'=>$pPublicId,
            ));
        }

        HeadingCache::clear ();
    }

    /**
     * Déplace un élément vers le bas de la liste
     *
     * @param int $pPublicId l'identifiant public de l'élément à descendre dans la liste
     */
    public function moveDown ($pPublicId) {
        HeadingCache::clear ();

        //récupère l'élément à modifier
        $element = $this->get ($pPublicId);
        $this->reorderLevel ($element->parent_heading_public_id_hei);
        //redemande la récupération de l'élément au cas ou le changement d'ordre est fait effet
        $element = $this->get ($pPublicId);

        if ($element->display_order_hei < $this->getNextDisplayOrderValue ($element->parent_heading_public_id_hei)) {
			_ioClass ('HeadingActionsService')->notifyByPublicId (HeadingActionsService::POSITION_CHANGE, $pPublicId, array ('new_position' => $element->display_order_hei + 1));
            //modification de la position de l'élément précédent
            _doQuery ('update cms_headingelementinformations set display_order_hei = :order
							where parent_heading_public_id_hei = :parentHeadingId
								and display_order_hei = :previousDisplayOrder',
                    array (':order'=>$element->display_order_hei,
                    ':parentHeadingId'=>$element->parent_heading_public_id_hei,
                    ':previousDisplayOrder'=>$element->display_order_hei+1));
            //modification de la position de l'élément a monter
            _doQuery ('update cms_headingelementinformations set display_order_hei = :order
							where public_id_hei = :id',
                    array (':order'=>$element->display_order_hei+1,
                    ':id'=>$pPublicId,
            ));
        }

        HeadingCache::clear ();
    }

    /**
     * Cette fonction récupère tous les enfants d'une rubrique donnée et s'assure que "display_order" respecte une séquence numérique complète
     * @param	int $pHeadingPublicId l'identifiant de la rubrique que l'on souhaite réordonner
     */
    public function reorderLevel ($pHeadingPublicId) {
        HeadingCache::clear ();
        CopixDb::begin ();
        try {
            $children = $this->getHeadingChildren ($pHeadingPublicId);
            $i = 0;
            foreach ($children as $childrenPublicId=>$childrenInformation) {
                $i++;
                if ($childrenInformation->display_order_hei != $i) {
                    $childrenInformation->display_order_hei = $i;
                    $this->_setOrder ($childrenPublicId, $i);
                }
            }
            CopixDb::commit ();
        }catch (Exception $e) {
            CopixDb::rollback ();
            HeadingCache::clear ();
            throw $e;
        }
        HeadingCache::clear ();
    }

    /**
     * Cette méthode permet de déplacer une rubrique à partie de sa postion initiale et de la nouvelle
     */
    public function reorderAndMoveWithOldPosition ($pPublicId, $pOldPosition,$pNewPosition) {
        HeadingCache::clear ();
        $element = $this->get ($pPublicId);
		$prevPosition = $element->display_order_hei;
        CopixDb::begin ();
        try {
            $children = $this->getHeadingChildren ($element->parent_heading_public_id_hei);
            foreach ($children as $childrenPublicId=>$childrenInformation) {
                // on affecte son identifiant d'ordre si on est sur l'élément modifié
                if ($childrenInformation->public_id_hei == $element->public_id_hei) {
                    $childrenInformation->display_order_hei = $pNewPosition;
                    $this->_setOrder ($childrenPublicId, $childrenInformation->display_order_hei);
                }
                else {

                    if($pOldPosition < $pNewPosition) {
                        // on repositionne les éléments contenus entre la nouvelle et l'ancienne position
                        if ($childrenInformation->display_order_hei <= $pNewPosition && $childrenInformation->display_order_hei > $pOldPosition) {
                            $childrenInformation->display_order_hei--;
                            $this->_setOrder ($childrenPublicId, $childrenInformation->display_order_hei);
                        }
                    }
                    else {
                        // on repositionne les éléments contenus entre la nouvelle et l'ancienne position
                        if ($childrenInformation->display_order_hei >= $pNewPosition && $childrenInformation->display_order_hei < $pOldPosition) {
                            $childrenInformation->display_order_hei++;
                            $this->_setOrder ($childrenPublicId, $childrenInformation->display_order_hei);
                        }
                    }
                }
            }
            CopixDb::commit ();
        }catch (Exception $e) {
            CopixDb::rollback ();
            HeadingCache::clear ();
            throw $e;
        }
		_ioClass ('HeadingActionsService')->notify (HeadingActionsService::POSITION_CHANGE, $element, array ('position_prev' => $prevPosition, 'position_new' => $pNewPosition));
        // problème de cache si activé
        //$this->reorderLevel ($element->parent_heading_public_id_hei);
        HeadingCache::clear ();
    }


    public function reorderAndMove ($pPublicId, $pNewPosition) {
        HeadingCache::clear ();
        $element = $this->get ($pPublicId);
        CopixDb::begin ();
        try {
            $children = $this->getHeadingChildren ($element->parent_heading_public_id_hei);
            foreach ($children as $childrenPublicId=>$childrenInformation) {
                if ($childrenInformation->display_order_hei >= $pNewPosition && $childrenInformation->public_id_hei != $element->public_id_hei) {
                    $childrenInformation->display_order_hei++;
                    $this->_setOrder ($childrenPublicId, $childrenInformation->display_order_hei);
                }
                else if ($childrenInformation->public_id_hei == $element->public_id_hei) {
                    $childrenInformation->display_order_hei = $pNewPosition;
                    $this->_setOrder ($childrenPublicId, $childrenInformation->display_order_hei);
                }
            }
            CopixDb::commit ();
        }catch (Exception $e) {
            CopixDb::rollback ();
            HeadingCache::clear ();
            throw $e;
        }
        $this->reorderLevel ($element->parent_heading_public_id_hei);
        HeadingCache::clear ();
    }

    /**
     * Retourne la liste des champs qui sont connus dans la table générique des informations
     * @return array
     */
    public function getFields () {
        return array_keys (get_object_vars (DAORecordcms_headingelementinformations::create ()));
    }

    /**
     * Publication de l'élément donné
     *
     * @param int    $pIdHelt  l'identifiant de l'élément
     * @param string $pTypeHei le type de l'élément
     */
    public function publishById ($pIdHelt, $pTypeHei) {
        HeadingCache::clear ();

        //Element à publier
        $element = $this->getById ($pIdHelt, $pTypeHei);

        if (! in_array ($element->status_hei, array (HeadingElementStatus::DRAFT, HeadingElementStatus::PROPOSED, HeadingElementStatus::PLANNED, HeadingElementStatus::ARCHIVE))) {
            throw new HeadingElementWorkflowException ("Impossible de publier un élément dont le statut n'appartient pas a brouillon, proposé, planifié ou archivé.");
        }

        // changement du contenu donc appel de l'événement updateContent
        $headings = array ();
        if ($pTypeHei == 'page') {
            $headings[$element->public_id_hei] = _url ('heading||', array ('public_id' => $element->public_id_hei));
        } else {
            // on met l'identifiant en clef parceque getDependencies ne renvoie pas que la dernière version,
            // on peut donc avoir plusieurs versions d'une page pour le même public_id
            foreach (_ioClass ('portal|PageServices')->getDependencies ($element->public_id_hei) as $page) {
                $headings[$page->public_id_hei] = _url ('heading||', array ('public_id' => $page->public_id_hei));
            }
        }

        //S'il existe, on passe l'élément actuellement publié en archivé
        if ($currentPublishedElement = $this->getPublished ($element->public_id_hei, false)) {
            $currentPublishedElement->status_hei = HeadingElementStatus::ARCHIVE;
            DAOcms_headingelementinformations::instance ()->update ($currentPublishedElement);
        }

        $element->status_hei = HeadingElementStatus::PUBLISHED;

        DAOcms_headingelementinformations::instance ()->update ($element);

		_ioClass ('HeadingActionsService')->notify (HeadingActionsService::PUBLISH, $element);

        HeadingCache::clear ();

        foreach ($headings as $id => $url) {
            $page = _ioClass ('PageServices')->getByPublicId ($id);
            $inherited = false;
            $tags = implode (',', $this->getTags ($id, $inherited));
            $content = _ioClass ('PageServices')->getContent ($id);
            _notify ('UpdateContent', array (
                    'url' => $url,
                    'new' => array (
                            'id' => $id,
                            'kind' => 'heading',
                            'keywords' => $tags,
                            'title' => $page->caption_hei,
                            'summary' => $content->summary,
                            'content' => $content->content,
                            'url' => _url ('heading||', array ('public_id' => $id, 'caption_hei' => $page->caption_hei)),
                            'credentials' => array ('cms:' . HeadingElementCredentials::READ . '@' . $id),
                            'path' => $page->hierarchy_hei
                    )
            ));
        }
    }
    
	/**
     * Planification de l'élément donné
     *
     * @param int    $pIdHelt  l'identifiant de l'élément
     * @param string $pTypeHei le type de l'élément
     */
    public function planById ($pIdHelt, $pTypeHei, $pPublishedDate, $pEndPublishedDate = null) {
        HeadingCache::clear ();

        //Element à publier
        $element = $this->getById ($pIdHelt, $pTypeHei);

        if (in_array ($element->status_hei, array (HeadingElementStatus::DELETED, HeadingElementStatus::ARCHIVE))) {
            throw new HeadingElementWorkflowException ("Impossible de planifier un élément dont le statut n'appartient pas a brouillon, proposé, publié ou planifié.");
        }
        
		$element->published_date_hei = $pPublishedDate;
		$element->end_published_date_hei = $pEndPublishedDate;

		//dans le cas ou on a un élément publié et que l'on veut juste donner une date d'archivage, on ne change pas le statut
		if (!($element->status_hei == HeadingElementStatus::PUBLISHED && !$pPublishedDate)){
        	$element->status_hei = HeadingElementStatus::PLANNED;
		}

        DAOcms_headingelementinformations::instance ()->update ($element);

		_ioClass ('HeadingActionsService')->notify (HeadingActionsService::PLANNED, $element);

        HeadingCache::clear ();
    }

    /**
     * Récupération des brouillon pour l'élément d'identifiant public donné
     */
    public function getDrafts ($pPublicId) {
        $sp = _daoSP ()->addCondition ('status_hei', '=', HeadingElementStatus::DRAFT)->addCondition ('public_id_hei', '=', $pPublicId);
        $records = DAOcms_headingelementinformations::instance ()->findBy ($sp);

        if (count ($records) == 0) {
            throw new HeadingElementInformationNotFoundException ($pPublicId, null, HeadingElementStatus::DRAFT);
        }
        return $records;
    }
    
    /**
     * Récupération des versions planifiées pour l'élément d'identifiant public donné
     */
    public function getPlannedVerions ($pPublicId) {
        $sp = _daoSP ()->addCondition ('status_hei', '=', HeadingElementStatus::PLANNED)->addCondition ('public_id_hei', '=', $pPublicId);
        $records = DAOcms_headingelementinformations::instance ()->findBy ($sp);

        if (count ($records) == 0) {
            throw new HeadingElementInformationNotFoundException ($pPublicId, null, HeadingElementStatus::PLANNED);
        }
        return $records;
    }

    /**
     * Récupère l'élément publié pour un document donné
     *
     * @param int  $pPublicId                 l'identifiant public de l'élément publié à récupérer
     * @param bool $pThrowExceptionOnNotFound S'il faut lancer une exception dans le cas ou on ne trouve aucun élément publié pour l'identifiant donnée
     * @return record de type cms_headingelementinformations / null si non trouvé
     * @throws HeadingElementInformationNotFoundException
     */
    public function getPublished ($pPublicId, $pThrowExceptionOnNotFound = true) {
        $sp = _daoSP ()->addCondition ('status_hei', '=', HeadingElementStatus::PUBLISHED)->addCondition ('public_id_hei', '=', $pPublicId);
        if (count ($results = DAOcms_headingelementinformations::instance ()->findBy ($sp)) == 0) {
            if ($pThrowExceptionOnNotFound) {
                throw new HeadingElementInformationNotFoundException ($pPublicId, null, HeadingElementStatus::PUBLISHED);
            }else {
                return null;
            }
        }

        return $results[0];
    }

    /**
     * Récupère un tableau d'éléments dont la clef est l'identifiants publics, avec pour chaque statut
     */
    public function getGroupByStatusMaxVersion ($pParentHeading, $pSort = self::SORT_SHOW) {
        $cacheId = 'heiservices|getGroupByStatusMaxVersion|' . $pParentHeading . '|' . $pSort;
        if (HeadingCache::exists ($cacheId)) {
            return HeadingCache::get ($cacheId);
        }

        /**
         * Cette requête ne passait pas avec d'autres SGBD que MySQL.
         * Idem pour le GROUP BY
         */

        $addSelect = null;
        $orderBy = array ();

        // tri par type
        if ($pSort == self::SORT_TYPE) {
            $addSelect = ', CASE type_hei WHEN \'heading\' THEN 1 WHEN \'page\' THEN 2 WHEN \'article\' THEN 3 ELSE 4 END weigth_type';
            $orderBy = array ('weigth_type', 'caption_hei', 'published_date_hei');

            // tri par libellé
        } else if ($pSort == self::SORT_CAPTION) {
            $orderBy = array ('caption_hei', 'published_date_hei');

            // tri par statut
        } else if ($pSort == self::SORT_STATUS) {
            $addSelect = ', CASE status_hei WHEN ' . HeadingElementStatus::DRAFT . ' THEN 1 ';
            $addSelect .= 'WHEN ' . HeadingElementStatus::PROPOSED . ' THEN 2 ';
            $addSelect .= 'WHEN ' . HeadingElementStatus::PLANNED . ' THEN 3 ';
            $addSelect .= 'WHEN ' . HeadingElementStatus::PUBLISHED . ' THEN 4 ';
            $addSelect .= 'WHEN ' . HeadingElementStatus::ARCHIVE . ' THEN 5 ';
            $addSelect .= 'WHEN ' . HeadingElementStatus::DELETED . ' THEN 6 ';            
            $addSelect .= 'ELSE 7 END weigth_status';
            $addSelect .= ', CASE type_hei WHEN \'heading\' THEN 1 WHEN \'page\' THEN 2 WHEN \'article\' THEN 3 ELSE 4 END weigth_type';
            $orderBy = array ('weigth_status', 'weigth_type', 'caption_hei', 'published_date_hei');

            // tri par ordre d'affichage
        } else {
            $orderBy = array ('display_order_hei', 'published_date_hei');
        }

        $query = "SELECT * " . $addSelect . " FROM cms_headingelementinformations h1
					where status_hei <> :planned_hei_1
					and status_hei <> ".HeadingElementStatus::DELETED."
					and parent_heading_public_id_hei = :parent_heading_public_id_hei_1
					and version_hei = (SELECT MAX(version_hei) from  cms_headingelementinformations h2 where h1.id_helt = h2.id_helt and h1.type_hei = h2.type_hei)
				Union
				select * " . $addSelect . " from cms_headingelementinformations h2
					where h2.status_hei = :planned_hei_2
					and status_hei <> ".HeadingElementStatus::DELETED."
					and h2.parent_heading_public_id_hei = :parent_heading_public_id_hei_2
				order by " . implode (', ', $orderBy);

        $results = _doQuery ($query,
                array (':planned_hei_1'=>HeadingElementStatus::PLANNED,
                ':planned_hei_2'=>HeadingElementStatus::PLANNED,
                ':parent_heading_public_id_hei_1'=>$pParentHeading,
                ':parent_heading_public_id_hei_2'=>$pParentHeading
        ));


        $finalResults = array ();
        foreach ($results as $element) {
            //L'élément n'est pas encore dans le tableau
            if (!isset ($finalResults[$element->public_id_hei])) {
                $finalResults[$element->public_id_hei] = array ();
            }

            //On crée le tableau de plannifiés au cas ou il n'existe pas pour l'élément donné
            if (($element->status_hei == HeadingElementStatus::PLANNED) && !isset ($finalResults[$element->public_id_hei][$element->status_hei])) {
                $finalResults[$element->public_id_hei][$element->status_hei] = array ();
            }

            //On ajoute enfin l'élément au tableau
            if ($element->status_hei == HeadingElementStatus::PLANNED 
            	|| $element->status_hei == HeadingElementStatus::DRAFT) {
                //Dans un tableau si c'est un élément planifié
                $finalResults[$element->public_id_hei][$element->status_hei][] = $element;
            }
            else {
                //Directement si c'est un élément d'un autre statut
                $finalResults[$element->public_id_hei][$element->status_hei] = $element;
            }
        }

        HeadingCache::set ($cacheId, $finalResults, false);
        return $finalResults;
    }
   
    function getTree2 ($origin = 0, $depth = 100, $pVisibleOnly = false, $pTypes = array()) {
        $originElement = $this->_getElement ($origin);
        $root = new LazyTreeElement($originElement, $depth, $pVisibleOnly, $pTypes);
	return $root->children;
    }


    /**
     * Retourne un arbre d'element, utilisé par les menus
     *
     * @param int $origin
     * @param int $depth
     * @param boolean $pOnlyPublished
     * @return Array
     */
    public function getTree ($origin = 0, $depth = 100, $pOnlyPublished = true, $pTypes = array()) {
        $originElement = $this->_getElement ($origin);

        $conditions = _daoSP ()->addCondition ('hierarchy_hei', 'LIKE', $originElement->hierarchy_hei . '-%')
                ->addCondition ('hierarchy_level_hei', '<=', intval($originElement->hierarchy_level_hei + $depth))
				->addCondition ('status_hei', '<>', HeadingElementStatus::DELETED)
                ->orderBy ('hierarchy_level_hei', 'display_order_hei');
        if ($pOnlyPublished) {
            $conditions->addCondition ('status_hei', '=', HeadingElementStatus::PUBLISHED);
        }

        if (count ($pTypes) > 1) {
            $conditions->addCondition ('type_hei', '=', $pTypes);
        }

        $tree = array ();
        $originElement->children = array();
        $tree[$origin] = $originElement;

        $filterTree = array();

        foreach (DAOcms_headingelementinformations::instance ()->findBy ($conditions) as $item) {
	    $this->_setElement($item->public_id_hei, $item, true, true);
	    if (in_array ($item->status_hei, array (HeadingElementStatus::PUBLISHED, HeadingElementStatus::ARCHIVE))
            	&& HeadingElementCredentials::canShow($item->public_id_hei)) {
                $item->path = _url ('heading||', array ('public_id'=>$item->public_id_hei, 'caption_hei'=>$item->caption_hei, 'url_id_hei'=>$item->url_id_hei, 'target_hei'=>$item->target_hei), true);

                if (self::VISIBILITY_INHERITED == $item->show_in_menu_hei) {
                    $inherited = 'foo';//on va mettre ici l'id dont on hérite la visibilité
                    $item->show_in_menu_hei = $this->getVisibility ($item->public_id_hei, $inherited);
                }

                $filterTree[$item->public_id_hei] = $item;
            }
        }
       
        //construction de l'arbre
        foreach ($filterTree as $item) {
            $hierarchy = explode('-', $item->hierarchy_hei);
            $leaf = &$tree;
            $level = 0;
            foreach ($hierarchy as $id) {
                if ($level >= $originElement->hierarchy_level_hei) {
                    if (!array_key_exists($id, $leaf) && array_key_exists($id, $filterTree)) {
                        $leaf[$id] = $filterTree[$id];
                    }
                    if ($id == $item->public_id_hei) {
                        //cette branche de l'arbre est terminée
                        break;
                    }
                    $element = (isset($leaf[$id])) ? $leaf[$id] : _Ppo();
                    if (!isset($element->children)) {
                        $element->children = array();
                    }
                    $leaf = &$element->children;
                }
                $level++;
            }
        }

        //trie de l'arbre selon display_order_hei
        $this->_cleanTree ($tree[$origin]->children);
        return $tree[$origin]->children;
    }

    /**
     * Nettoie l'arbre de ses feuilles et branches vides.
     *
     * @param Array $tree
     */
    private function _cleanTree (&$tree) {
    	//on regarde si les éléments disposent d'enfants ou pas.
    	foreach ($tree as $publicId => $item){
    		if (isset ($item->children)){
    			$this->_cleanTree ($item->children);
    		}
    		
    		if (($item->show_in_menu_hei == self::INVISIBLE)
    		 && (!isset ($item->children) || count ($item->children) === 0)){
    		 	//Si pas visible et que pas d'enfants, on supprime de l'arbre
    		 	unset ($tree[$publicId]);
    		}
    	}
    }

    /**
     * Retourne l'arbre complet de tous les elements du cms
     *
     * @return array
     */
    public function getElementChooserTree ($origin = 0, $depth = false, $pCache = true) {
        $cacheId = 'heiservices|getElementChooserTree|' . $origin . '|' . $depth . '|' . _currentUser()->getLogin();
        if ($pCache && HeadingCache::exists ($cacheId)) {
            return HeadingCache::get ($cacheId);
        }

        $originElement = $this->_getElement ($origin);

        $sp = _daoSP ();
        $conditions = $sp->addCondition ('hierarchy_hei', 'LIKE', $originElement->hierarchy_hei . '-%')
				        ->addCondition ('status_hei', '<>', HeadingElementStatus::DELETED);

        if ($depth) {
            $sp->addCondition ('hierarchy_level_hei', '<=', $originElement->hierarchy_level_hei + $depth);
        }
        $sp->orderBy ('hierarchy_hei')->orderBy (array ('version_hei', 'DESC'));

        $tree = array ();
        $originElement->children = array();
        $tree[$origin] = $originElement;

        $filterTree = array();

        foreach (DAOcms_headingelementinformations::instance ()->findBy ($conditions) as $item) {
            $exists = $this->_existsElement ($item->public_id_hei);
            if (!$exists || ($exists && $this->_getElement ($item->public_id_hei)->status_hei != HeadingElementStatus::PUBLISHED)) {
                $this->_setElement ($item->public_id_hei, $item);
            }
            if (HeadingElementCredentials::canWrite($item->public_id_hei)
                    && (!array_key_exists($item->public_id_hei, $filterTree) || (array_key_exists($item->public_id_hei, $filterTree) &&
                                    $filterTree[$item->public_id_hei]->status_hei != HeadingElementStatus::PUBLISHED))) {
                $filterTree[$item->public_id_hei] = $item;
            }
        }

        foreach ($filterTree as $item) {
            $hierarchy = explode('-', $item->hierarchy_hei);
            $leaf = &$tree;
            $level = 0;
            foreach ($hierarchy as $id) {
                if ($level >= $originElement->hierarchy_level_hei) {
                    if (!array_key_exists($id, $leaf)) {
                        //la rubrique n'est pas visible mais ces enfants le sont.
                        if (!array_key_exists($id, $filterTree)) {
                            $leaf[$id] = $this->get($id);
                            $leaf[$id]->path = _url ('heading||', array ('public_id'=>$item->public_id_hei, 'caption_hei'=>$item->caption_hei, 'url_id_hei'=>$item->url_id_hei, 'target_hei'=>$item->target_hei));
                        } else {
                            $leaf[$id] = $filterTree[$id];
                        }
                    }
                    if ($id == $item->public_id_hei) {
                        //cette branche de l'arbre est terminée
                        break;
                    }

                    $element = $leaf[$id];
                    if (!isset($element->children)) {
                        $element->children = array();
                    }
                    $leaf = &$element->children;
                }
                $level++;
            }
        }

        $toReturn = (isset ($tree[$origin]->children)) ? $tree[$origin]->children : array ();
        if ($pCache){
        	HeadingCache::set ($cacheId, $toReturn, false);
        }
        return $toReturn;
    }


    /**
     * Retourne l'element parent au niveau definit par $pLevel
     *
     * @param int $pPublicId
     * @param int $pLevel
     * @return HeadingElement
     */
    public function getParentAtLevel ($pPublicId, $pLevel = -1) {
        $path = $this->getHeadingPath ($pPublicId);
        return isset ($path[abs($pLevel)]) ? $this->get ($path[abs($pLevel)]) : $this->get ($path[count ($path)-1]);
    }

    /**
     * Retourne tous les elements d'un type donné, si pas de type renseigné, on renvoie tout
     *
     * @return unknown
     */
    public function getListeElements ($type = null) {
        $criteres = _daoSP ();
        if ($type != null) {
            $criteres->addCondition ('type_hei', '=', $type);
        }
        $criteres->orderBy (array ('parent_heading_public_id_hei', 'DESC'))->groupBy ('public_id_hei');
        return DAOcms_headingelementinformations::instance ()->findBy ($criteres)->fetchAll ();
    }

    /**
     * Récupération du thème pour l'élément donné
     *
     * @param string  $pPublicId  l'identifiant de l'élément dont on cherche a connaître le thème
     * @param boolean $pInherited positionné a false si non hérité, a l'identifiant de la rubrique sinon,
     *   a null si cela prend le thème par défaut
     */
    public function getTheme ($pPublicId, & $pInherited) {
        $this->_calcInheritedStuff ($pPublicId);
        $pInherited = self::$_theme[$pPublicId]['inherited'] === $pPublicId ? false : self::$_theme[$pPublicId]['inherited'];
        // Si on a thème|template
        if (strpos (self::$_theme[$pPublicId]['theme'], '|') !== false) {
            $temp = explode('|', self::$_theme[$pPublicId]['theme']);
            return $temp[0];
        } else {
            return self::$_theme[$pPublicId]['theme'];
        }
    }

    /**
     * Récupération du template de page pour l'élément donné
     *
     * @param string  $pPublicId  l'identifiant de l'élément dont on cherche a connaître le template de page
     */
    public function getTemplate ($pPublicId) {
        $this->_calcInheritedStuff ($pPublicId);
        if (strpos (self::$_theme[$pPublicId]['theme'], '|') !== false) {
            $temp = explode('|', self::$_theme[$pPublicId]['theme']);
            if($temp[1]){
            	return 'default|'.$temp[1];
            }
        }
		return 'default|main.php';
    }

    /**
     * Récupération de la balise meta robots
     * pour l'élément donné
     *
     * @param string  $pPublicId  l'identifiant de l'élément
     * @param boolean $pInherited positionné a false si non hérité, a l'identifiant de la rubrique sinon,
     *   a null si cela prend le thème par défaut
     */
    public function getRobots ($pPublicId, & $pInherited) {
        $this->_calcInheritedStuff ($pPublicId);
        $pInherited = (self::$_robots[$pPublicId]['inherited'] === $pPublicId) ? false : self::$_robots[$pPublicId]['inherited'];
        return self::$_robots[$pPublicId]['robots'];
    }

    /**
     * Récupération des informations de visibilité
     *
     * @param string  $pPublicId
     * @param int $pInherited
     */
    public function getVisibility ($pPublicId, & $pInherited) {
        $this->_calcInheritedStuff ($pPublicId);
        $pInherited = self::$_visibility[$pPublicId]['inherited'] === $pPublicId ? false : self::$_visibility[$pPublicId]['inherited'];
        return self::$_visibility[$pPublicId]['visibility'];
    }

    /**
     * Récupération de l'adresse de base à utiliser pour la rubrique donnée
     *
     * @param string  l'identifiant public de l'élément dont on souhaite connaître l'adresse
     * @param boolean si l'adresse de base est héritée ou non
     */
    public function getBaseUrl ($pPublicId, & $pInherited) {
        $this->_calcInheritedStuff ($pPublicId);
        $pInherited = self::$_base_url[$pPublicId]['inherited'] === $pPublicId ? false : self::$_base_url[$pPublicId]['inherited'];
        return self::$_base_url[$pPublicId]['base_url'];
    }

    /**
     * Calcul des éléments hérités.
     *
     * @param unknown_type $pPublicId
     */
    private function _calcInheritedStuff ($pPublicId) {
        if (array_key_exists ($pPublicId, self::$_visibility)) {
            return;
        }

        $element = $this->get($pPublicId);
        //chemin pour arriver a l'élément.
        $path = explode('-', $element->hierarchy_hei);
        foreach ($path as $position=>$elementId) {

            //Utile de le calculer uniquement si pas déjà connu
            if (! array_key_exists ($elementId, self::$_visibility)) {
                $element = $this->get ($elementId);
                //visibilité
                if (intval($element->show_in_menu_hei) !== self::VISIBILITY_INHERITED) {
                    //elle n'est pas héritée
                    self::$_visibility[$element->public_id_hei] = array ('inherited'=>$element->public_id_hei, 'visibility'=>$element->show_in_menu_hei);
                }else {
                    //elle est héritée, on va trouver de ou.
                    if ($position === 0) {
                        //si c'est le premier élément, on ne cherche pas plus loin
                        self::$_visibility[$element->public_id_hei] = array ('inherited'=>null, 'visibility'=>self::INVISIBLE);
                    }else {
                        //ce n'était pas le premier élément, on prend le parent
                        self::$_visibility[$element->public_id_hei] = array ('inherited'=>self::$_visibility[$path[$position-1]]['inherited'], 'visibility'=>self::$_visibility[$path[$position-1]]['visibility']);
                    }
                }

                //Url de base
                if ($element->base_url_hei !== null) {
                    self::$_base_url[$element->public_id_hei] = array ('inherited'=>$element->public_id_hei, 'base_url'=>$element->base_url_hei);
                }else {
                    //elle est héritée, on va trouver de ou.
                    if ($position === 0) {
                        self::$_base_url[$element->public_id_hei] = array ('inherited'=>null, 'base_url'=>CopixUrl::getRequestedBaseUrl ());
                    }else {
                        self::$_base_url[$element->public_id_hei] = array ('inherited'=>self::$_base_url[$path[$position-1]]['inherited'], 'base_url'=>self::$_base_url[$path[$position-1]]['base_url']);
                    }
                }
                //Thème
                if ($element->theme_id_hei !== null) {
                    self::$_theme[$element->public_id_hei] = array ('inherited'=>$element->public_id_hei, 'theme'=>$element->theme_id_hei);
                }else {
                    //elle est héritée, on va trouver de ou.
                    if ($position === 0) {
                        self::$_theme[$element->public_id_hei] = array ('inherited'=>null, 'theme'=>CopixConfig::get ('default|publicTheme'));
                    }else {
                        self::$_theme[$element->public_id_hei] = array ('inherited'=>self::$_theme[$path[$position-1]]['inherited'], 'theme'=>self::$_theme[$path[$position-1]]['theme']);
                    }
                }
                //Robots
                if ($element->robots_hei !== null) {
                    self::$_robots[$element->public_id_hei] = array ('inherited'=>$element->public_id_hei, 'robots'=>$element->robots_hei);
                } else {
                    //elle est héritée, on va trouver de ou.
                    if ($position === 0) {
                        self::$_robots[$element->public_id_hei] = array ('inherited'=>null, 'robots'=>null);
                    }else {
                        self::$_robots[$element->public_id_hei] = array ('inherited'=>self::$_robots[$path[$position-1]]['inherited'], 'robots'=>self::$_robots[$path[$position-1]]['robots']);
                    }
                }
                //echo "calculated  $elementId <br />";
            }else {
                //echo "Matched $elementId <br />";
            }
        }
    }

    /**
     * Fonction qui retourne le code HTML pour la prévisualisation d'un élément
     * @param int    $pIdHelt  l'identifiant interne de l'élément a prévisualiser
     * @param string $pTypeHei le type de l'élément a prévisualiser
     */
    public function previewById ($pIdHelt, $pTypeHei) {
        $arHeadingElementType = _ioClass('heading|headingelementtype')->getList ();
        return _ioClass($arHeadingElementType[$pTypeHei]['classid'])->previewById ($pIdHelt);
    }

    /**
     * Modification de la position d'un élément par son public id dans la rubrique donnée
     *
     * @param int $pPublicId l'élément dont on souhaite modifier la position
     * @param int $pOrder la nouvelle position de l'élément
     */
    private function _setOrder ($pPublicId, $pOrder) {
        HeadingCache::clear ();
        _doQuery ('update cms_headingelementinformations set display_order_hei = :pOrder where public_id_hei = :pPublicId', array (':pPublicId'=>$pPublicId, ':pOrder'=>$pOrder));
        HeadingCache::clear ();
    }

    /**
     * Renvoie les tags
     *
     * @param int $pPublicId l'élément dont on souhaite modifier la position
     * @param boolean & $pInherited si l'adresse de base est héritée ou non (false : pas hérité, null : hérité mais vide, autre : public_id dont on hérite)
     *
     * @return array strings Le texte des tags associés
     */
    public function getTags ($pPublicId, & $pInherited) {
        // Si le module n'est pas installé, on renvoie vide
        if (!CopixModule::isEnabled ('tags')) {
            $pInherited = false;
            return array();
        }
        $tagKindObjet = 'headingelementinformation';

        $tags = _ioClass('tags|tagservices')->getAssociation( $pPublicId, $tagKindObjet );

        // Si l'élément n'hérite pas, on renvoie ses tags propres uniquement
        $element = $this->get ($pPublicId);
        if ($element->tags_inherited_hei == 0) {
            $pInherited = false;
            return $tags;
        }

        // il hérite, on interroge les parents
        foreach ($path = $this->getHeadingPath ($pPublicId) as $parentPublicId) {
            // Le premier élément du chemin c'est l'élément lui-même...
            if( $pPublicId == $parentPublicId ) {
                continue;
            }
            $element = $this->get ($parentPublicId);
            $tags = $tags + _ioClass('tags|tagservices')->getAssociation( $parentPublicId, $tagKindObjet );
            if ($element->tags_inherited_hei == 0) {
                $pInherited = $parentPublicId;
                return $tags;
            }
        }

        $pInherited = null;
        return $tags;
    }

    /**
     *
     *
     * @param int $pPublicId l'identifiant public de l'élément dont on doit changer les tags
     * @param PPO $pTagInformations Les informations de tag récupérées de la requête, sous forme de ppo
     */
    public function setTags( $pPublicId, $pTagInformations ) {
        HeadingCache::clear ();
        $tags = array();
        $tagKindObjet = 'headingelementinformation';
        if( $pTagInformations->new ) {
            $tmp = explode( ',', $pTagInformations->new );
            foreach( $tmp as $i=> $tag ) {
                $tags[] = trim( $tag );
            }
        }
        if( $pTagInformations->selected ) {
        	$tags = array_merge($tags, $pTagInformations->selected);
        }
        $tags = array_unique( $tags );

        //On retire toutes les associations...
        _ioClass ('tags|tagservices')->deleteAssociation( $pPublicId, $tagKindObjet);
        // ...et on remplace par les nouvelles.
        if( $tags ) {
            _ioClass ('tags|tagservices')->addAssociation( $pPublicId, $tagKindObjet, $tags);
        }
        HeadingCache::clear ();
        // Quelle valeur de retour ? Un booléen en cas de mise à jour correctement effectuée ?
        // return true;
    }


    /**
     * Retourne les droits d'un élément
     *
     * @param int $pPublicIdHei
     * @param int $pDbGroupId
     * @return record
     */
    public function getHeadingElementCredential ($pDbGroupId, $groupHandler, $pPublicIdHei) {
        //init des droits
        if (self::$_credentials == null) {
            self::$_credentials = array();

            $cacheId = 'heiservices|getHeadingElementCredential';
            if (HeadingCache::exists ($cacheId)) {
                $arCredentials = HeadingCache::get ($cacheId);
            } else {
                $arCredentials = DAOcms_headingelementinformations_credentials::instance ()->findAll();
                HeadingCache::set ($cacheId, $arCredentials, false);
            }

            foreach ($arCredentials as $credential) {
                if (!array_key_exists($credential->public_id_hei, self::$_credentials)) {
                    self::$_credentials[$credential->public_id_hei] = array();
                }
                if (!array_key_exists($credential->group_handler, self::$_credentials[$credential->public_id_hei])) {
                    self::$_credentials[$credential->public_id_hei][$credential->group_handler] = array();
                }
                self::$_credentials[$credential->public_id_hei][$credential->group_handler][$credential->id_group] = $credential;
            }
        }

        $toReturn = false;
        if (array_key_exists($pPublicIdHei, self::$_credentials) && array_key_exists($groupHandler, self::$_credentials[(string)$pPublicIdHei]) && array_key_exists($pDbGroupId, self::$_credentials[$pPublicIdHei][$groupHandler])) {
            $toReturn = self::$_credentials[$pPublicIdHei][$groupHandler][$pDbGroupId];
        }

        if (!$toReturn && $pPublicIdHei>0) {
        	$element = $this->get($pPublicIdHei);
        	if($element->credentials_inherited_hei){
	            $parent = $this->get($pPublicIdHei);
	            return $this->getHeadingElementCredential($pDbGroupId, $groupHandler, $parent->parent_heading_public_id_hei);
        	}else{
	        	return self::_getNoCredentialRecord($pDbGroupId, $groupHandler, $pPublicIdHei);
        	}
        } else if (!$toReturn && $pPublicIdHei == 0) { // cas ou aucun droits n'est defini pour ce groupe : on renvoie un enregistrement sans droit
            $record = self::_getNoCredentialRecord($pDbGroupId, $groupHandler, $pPublicIdHei);
            try{
            	DAOcms_headingelementinformations_credentials::instance ()->insert ($record);
            }catch (Exception $e){
            	// catch car parfois appellé plusieurs fois et mysql n'a pas encore inséré et donc on nous retourne une erreur de doublon qui n'a pas de raison d'être 
            }
            HeadingCache::clear ();
            return $record;
        }
        // cas héritage de l'élément parent
        if($toReturn && isset($toReturn->value_credential) && $toReturn->value_credential === ''){
        	$element = $this->get($pPublicIdHei);
			$parent = $this->get($pPublicIdHei);
			$toReturn = $this->getHeadingElementCredential($pDbGroupId, $groupHandler, $parent->parent_heading_public_id_hei);
        }
        return $toReturn;
    }
    
    private static function _getNoCredentialRecord($pDbGroupId, $groupHandler, $pPublicIdHei){
	    $record = DAORecordcms_headingelementinformations_credentials::create ();
		$record->id_group = $pDbGroupId;
	    $record->group_handler = $groupHandler;
		$record->value_credential = HeadingElementCredentials::NONE;
		$record->public_id_hei = $pPublicIdHei;
		return $record;
    }

	/**
     * Retourne les droits d'un élément
     *
     * @param int $pPublicIdHei
     * @return record
     */
    public function getAllHeadingElementCredential ($pPublicIdHei) {
        //init des droits
        if (self::$_credentials == null) {
            self::$_credentials = array();

            $cacheId = 'heiservices|getHeadingElementCredential';
            
            if (HeadingCache::exists ($cacheId)) {
                $arCredentials = HeadingCache::get ($cacheId);
            } else {
                $arCredentials = DAOcms_headingelementinformations_credentials::instance ()->findAll ();
                HeadingCache::set ($cacheId, $arCredentials, false);
            }

            foreach ($arCredentials as $credential) {
                if (!array_key_exists($credential->public_id_hei, self::$_credentials)) {
                    self::$_credentials[$credential->public_id_hei] = array();
                }
                self::$_credentials[$credential->public_id_hei][$credential->group_handler][$credential->id_group] = $credential;
            }
        }

        $toReturn = array();
        if (array_key_exists($pPublicIdHei, self::$_credentials)) {
            $toReturn = self::$_credentials[$pPublicIdHei];
        }

        if (!$toReturn && $pPublicIdHei>0) {
            $element = $this->get($pPublicIdHei);
            if($element->credentials_inherited_hei || $toReturn === ''){
	            $parent = $this->get($pPublicIdHei);
	            return $this->getAllHeadingElementCredential($parent->parent_heading_public_id_hei);
            }
        } 
        return $toReturn;
    }
    
    /**
     * Sauvegarde les droits pour un element et un groupe donné
     *
     * @param int $pGroupId
     * @param int $pPublicIdHei
     * @param int $pCredential
     */
    public function saveCredentials ($pGroupId, $groupHandler, $pPublicIdHei, $pCredential) {
        HeadingCache::clear ();

        $record = DAORecordcms_headingelementinformations_credentials::create ();
        $record->id_group = $pGroupId;
        $record->public_id_hei = $pPublicIdHei;
        $record->value_credential = $pCredential;
        $record->group_handler = $groupHandler;

        //si la mise à jour echoue c'est que l'enregistrement n'existe pas => on insere
        $dao = DAOcms_headingelementinformations_credentials::instance ();
		$recordActual = $dao->get ($pPublicIdHei, $pGroupId, $groupHandler);
        if ($recordActual) {
			if ($recordActual != $record) {
				$dao->update ($record);
				$extras = array ('group_id' => $pGroupId, 'group_handler' => $groupHandler, 'credential' => $pCredential);
				_ioClass ('HeadingActionsService')->notifyByPublicId (HeadingActionsService::CREDENTIAL_SAVE, $pPublicIdHei, $extras);
			}
        } else {
            $dao->insert ($record);
			$extras = array ('group_id' => $pGroupId, 'group_handler' => $groupHandler, 'credential' => $pCredential);
			_ioClass ('HeadingActionsService')->notifyByPublicId (HeadingActionsService::CREDENTIAL_SAVE, $pPublicIdHei, $extras);
        }

        HeadingCache::clear ();
    }

    /**
     * Retourne l'element parent qui definit les droits de l'element si l'element herite les droits de son parent.
     *
     * @param int $pPublicIdHei
     * @return int
     */
    public function getParentPublicIdForCredential ($pPublicIdHei) {
        //si on est dans la rubrique mere on renvoie directement : il n'y a pas de parent
        if ($pPublicIdHei == 0) {
            return 0;
        }
        $element = $this->get($pPublicIdHei);
        if ($element->credentials_inherited_hei) {
            return $this->getParentPublicIdForCredential($element->parent_heading_public_id_hei);
        }
        return $pPublicIdHei;
    }

    /**
     * Retourne l'element 0, element de base du cms
     *
     * @return record
     */
    public function getBaseElement () {
        $results = DAOcms_headingelementinformations::instance ()-> findBy (_daoSP ()->addCondition ('public_id_hei', '=', 0))->fetchAll ();
        return $results[0];
    }

    /**
     * Retourne l'element identifié par son url
     *
     * @param String $pUrlIdHei
     * @return Record
     */
    public function getByUrlId ($pUrl, $pUrlIdHei) {
        $cacheId = 'heiservices|getByUrlId|' . $pUrl . $pUrlIdHei;
        if (HeadingCache::exists ($cacheId)) {
            return HeadingCache::get ($cacheId);
        }
        //on récupère tous les éléments qui indiquent être capable de gérer
        //l'élément indiqué.
        $results = DAOcms_headingelementinformations::instance ()-> findBy (
                _daoSP ()->addCondition ('url_id_hei', '=', $pUrlIdHei)
                ->addCondition ('status_hei', '=', HeadingElementStatus::PUBLISHED)
                )->fetchAll ();

        //on va regarder parmis tous les éléments en question ceux qui sont capables de gérer
        //l'url pour le domaine concerné.
        $matched = null;
        foreach ($results as $result) {
            if ($matched !== null) {
                break;
            }

            $basepaths = explode (';', $this->getBaseUrl ($result->public_id_hei, $foo));
            foreach ($basepaths as $path) {
                if ($path === '') {
                    $matched = $result;
                    break;
                }
                if (strpos($pUrl, $path) !== false) {
                    $matched = $result;
                    break;
                }
            }
        }

        //on obtient la liste des urls qui peuvent correspondre vis à vis de l'url de fin.
        $toReturn = $matched;
       	HeadingCache::set ($cacheId, $toReturn);
        return $toReturn;
    }

    /**
     * Retourne la nouvelle adresse par le public_id d'une page identifiée par son url
     *
     * @param String $pUrlIdHei
     * @return Int Public_id
     */
    public function getNewUrlByUrlId ($pUrl, $pUrlIdHei) {
        $cacheId = 'heiservices|getNewUrlByUrlId|' . $pUrl . $pUrlIdHei;
        if (HeadingCache::exists ($cacheId)) {
            return HeadingCache::get ($cacheId);
        }

        $sp = _daoSP () ->addCondition ('url_id_hei', '=', $pUrlIdHei)
                ->addCondition ('status_hei', '<>', HeadingElementStatus::PUBLISHED)
                ->orderBy(array ('date_update_hei', 'DESC'));
        $results = DAOcms_headingelementinformations::instance ()-> findBy ($sp)->fetchAll ();

        $matched= null;
        foreach ($results as $result) {
            if ($matched !== null) {
                break;
            }

            $basepaths = explode (';', $this->getBaseUrl ($result->public_id_hei, $foo));
            foreach ($basepaths as $path) {
                if ($path === '') {
                    $matched = $result->public_id_hei;
                    break;
                }
                if (strpos($pUrl, $path) !== false) {
                    $matched = $result->public_id_hei;
                    break;
                }
            }
        }

       	HeadingCache::set ($cacheId, $matched);
        return $matched;
    }

    /**
     * Calcule les level et hierarchie pour un element donné
     *
     * @param Record $pElement
     * @return String
     */
    private function _fillHierarchy (& $pElement, $pSaveAndRecurse = true) {
        if ($pElement->parent_heading_public_id_hei === null) {
            return 0;
        }
        $parent = $this->get ($pElement->parent_heading_public_id_hei);

        $pElement->hierarchy_hei =  $parent->hierarchy_hei . "-" . $pElement->public_id_hei;
        $pElement->hierarchy_level_hei =  $parent->hierarchy_level_hei + 1;

        if ($pSaveAndRecurse) {
            DAOcms_headingelementinformations::instance ()->update ($pElement);
            foreach ($this->getChildren ($pElement->public_id_hei) as $element) {
                $this->_fillHierarchy ($element, true);
            }
        }
    }
    
    /**
     * Retourne les enfants d'une rubrique d'un type donné
     *
     * @param int $pPublicId
     * @param String $pTypeHei
	 * @param boolean $pOrder Indique si on veut trier par id_helt (false) ou par ordre d'affichage (true)
     * @return Array
     */
    public function getChildrenByType ($pPublicId, $pTypeHei, $pOrder = false) {
        $cacheId = 'heiservices|getChildrenByType|' . $pPublicId . '|' . $pTypeHei . '|' . $pOrder;
        if (HeadingCache::exists ($cacheId)) {
            return HeadingCache::get ($cacheId);
        }

        $element = _ioClass ('heading|headingelementinformationservices')->get ($pPublicId);
        $conditions = _daoSP()->addCondition ('hierarchy_hei', 'LIKE', $element->hierarchy_hei . '-%')
                ->addCondition ('type_hei', '=', $pTypeHei)
                ->addCondition ('status_hei', '<>', HeadingElementStatus::DELETED);
		if ($pOrder) {
			$conditions->orderBy ('display_order_hei');
		}

        $results = DAOcms_headingelementinformations::instance ()->findBy($conditions)->fetchAll ();

        $toReturn = array ();
        // on renvoie la dernière version de l'élément, sauf si c'est un brouillon d'un élément publié, on retourne l'élément publié
        foreach ($results as $item) {
            if (!array_key_exists($item->public_id_hei, $toReturn) || $item->status_hei == HeadingElementStatus::PUBLISHED) {
                $toReturn[$item->public_id_hei] = $item;
            }
        }

        HeadingCache::set ($cacheId, $toReturn);
        return $toReturn;
    }

    /**
     * Lance l'événement breadcrumb pour la partie admin
     */
    public function breadcrumbAdmin () {
        $path =  array (
			_url ('admin||', array ('modules' => array ('articles', 'cms_editor', 'document', 'form', 'heading', 'images', 'medias', 'package_cms', 'portal', 'uploader'))) => 'CMS',
        );
        if (CopixRequest::exists ('heading')) {
            $element = _ioClass ('HeadingElementInformationServices')->get (_request ('heading'));
            $path[_url ('heading|element|', array ('heading' => $element->public_id_hei))] = $element->caption_hei;
        } else if (CopixRequest::exists ('editId')) {
            $editId = _request ('editId');
            if (CopixSession::exists ('heading', $editId)) {
                $element = $this->get (CopixSession::get ('heading', $editId));
                $path[_url ('heading|element|', array ('heading' => $element->public_id_hei))] = $element->caption_hei;
            } else if (CopixSession::exists ('id_helt', $editId) && CopixSession::exists ('type_hei', $editId)) {
                $element = _ioClass ('HeadingElementInformationServices')->getById (CopixSession::get ('id_helt', $editId), CopixSession::get ('type_hei', $editId));
                $path[_url ('heading|element|', array ('heading' => $element->public_id_hei))] = $element->caption_hei;
            }
        }
        _notify ('breadcrumb', array ('path' => $path));
    }

	/**
	 * Retourne la liste des editeurs d'informations sur un élément
	 *
	 * @return array
	 */
	public function getInformationsEditors () {
		return CopixModule::getParsedModuleInformation (
			'heading|getInformationsEditors',
			"/moduledefinition/registry/entry[@id='ElementInformations']/*",
			array ($this, '_informationsEditorsCallBack')
		);
	}

	/**
	 * CallBack pour les nodes du module.xml
	 *
	 * @param array $pNodes Nodes contenant des editeurs
	 * @return array
	 */
	public function _informationsEditorsCallBack ($pNodes) {
		$toReturn = array ();
		foreach ($pNodes as $nodes) {
			foreach ($nodes as $node) {
				$attributes = $node->attributes ();
				$toReturn[] = array('mode'=>(string)$attributes['mode'], 'zoneid'=>(string)$attributes['zoneid']);
			}
		}
		return $toReturn;
	}

	/**
	 * Retourne les actions possibles sur un élément
	 *
	 * @param int $pIdHelt Identifiant
	 * @param string $pTypeHei Type
	 * @return stdClass
	 */
	public function getActions ($pIdHelt, $pTypeHei) {
		$element = $this->getById ($pIdHelt, $pTypeHei);
		$canWrite = HeadingElementCredentials::canWrite ($element->public_id_hei);
		$isPublishedArchive = ($element->status_hei == HeadingElementStatus::PUBLISHED || $element->status_hei == HeadingElementStatus::ARCHIVE || ($element->status_hei == HeadingElementStatus::DRAFT && $element->from_version_hei == 0));
		$toReturn = new stdClass ();
		if ($canWrite) {
			// si l'élément a un brouillon, on ne peut pas modifier cet élément, il faut modifier le brouillon
			if ($element->status_hei == HeadingElementStatus::PUBLISHED || $element->status_hei == HeadingElementStatus::ARCHIVE) {
				//maintenant on a droit à plusieurs brouillons
				/*$count = DAOcms_headingelementinformations::instance ()->countBy (_daoSP ()
					->addCondition ('public_id_hei', '=', $element->public_id_hei)
					->addCondition ('id_helt', '>', $element->id_helt)
					->addCondition ('status_hei', '<>', HeadingElementStatus::DELETED)
				);*/
				$toReturn->edit = 1;//($count == 0);
			} else {
				$toReturn->edit = ($element->status_hei != HeadingElementStatus::PROPOSED);
			}
		} else {
			$toReturn->edit = false;
		}
		$toReturn->publish = ($canWrite && (in_array($element->status_hei, array(HeadingElementStatus::DRAFT, HeadingElementStatus::ARCHIVE, HeadingElementStatus::PLANNED))));
		$toReturn->cut = ($element->public_id_hei > 0 && $canWrite && $isPublishedArchive);
		$toReturn->copy = ($canWrite && $isPublishedArchive);
		$toReturn->archive = ($canWrite && $element->status_hei == HeadingElementStatus::PUBLISHED);
		$toReturn->delete = ($canWrite && $element->public_id_hei > 0);
		$toReturn->move = ($canWrite && $element->public_id_hei > 0 && $isPublishedArchive);
		$toReturn->show = in_array ($element->status_hei, array (HeadingElementStatus::PLANNED, HeadingElementStatus::PROPOSED, HeadingElementStatus::PUBLISHED));
		$toReturn->planned = $pTypeHei != "heading" && $canWrite && in_array ($element->status_hei, array (HeadingElementStatus::DRAFT, HeadingElementStatus::PLANNED, HeadingElementStatus::PROPOSED, HeadingElementStatus::PUBLISHED));
		HeadingElementServices::call ($pTypeHei, 'getActions', array ($element, $toReturn));
		return $toReturn;
	}
	
	/**
	 * Retourne la ou les classes HTML définies pour l'élément ou ses parents
	 *
	 * @param int $publicId identifiant public de l'élément
	 * @param Bool $searchUpIfNull le passer à vrai pour chercher dans les parents
	 * @return string
	 */
	public function getMenuHTMLClasses ($publicId, $searchUpIfNull = false) {
		try {
			$heading = $this->get ($publicId);
		} catch ( Exception $e ) {
			return '';
		}
		if ($searchUpIfNull) {
			while ($heading->menu_html_class_name_hei === null) {
				try {
					$heading = $this->get ($heading->parent_heading_public_id_hei);
				} catch ( Exception $e ) {
					return '';
				}
			}
		}
		return $heading->menu_html_class_name_hei;
	}
	
	/**
	 * Retourne les elements faisant référence à l'element de publicId $pPublicId donné
	 *
	 * @param int $pPublicId
	 */
	public function getDependencies ($pPublicId){
		$types = _ioClass ('HeadingElementType')->getList ();
		$dependencies = array ();
		foreach ($types as $type => $infos) {
			$dependencies = array_merge ($dependencies, HeadingElementServices::call ($type, 'getDependencies', $pPublicId));
		}
		
		//pour les menus, on appelle directement le service ici
		$menuDependencies = _ioClass('heading|headingelementmenuservices')->getDependencies ($pPublicId);
		$dependencies = array_merge ($menuDependencies, $dependencies);
		return $dependencies;
	}
	
	public static function deleteCredential($groupId, $groupHandler = null, $publicId = null){
		_ioClass ('HeadingActionsService')->notifyByPublicId (HeadingActionsService::CREDENTIAL_DELETE_GROUP, $publicId, array ('group_id' => $groupId, 'group_handler' => $groupHandler));
		DAOcms_headingelementinformations_credentials::instance ()->delete ($publicId, $groupId, $groupHandler);
		HeadingCache::clear ();
	}
	
	public static function deleteCredentialsFromPublicId ($publicId) {
		$dao = DAOcms_headingelementinformations_credentials::instance ();
		$sp = _daoSP ()->addCondition ('public_id_hei', '=', $publicId);
		if ($dao->countBy ($sp) > 0) {
			$dao->deleteBy ($sp);
			_ioClass ('HeadingActionsService')->notifyByPublicId (HeadingActionsService::CREDENTIAL_INHERITED, $publicId);
			HeadingCache::clear ();
		}
	}
	
	/**
	 * 
	 * Verifie si il existe une version plus recente de l'element de publicId et id_helt donné
	 * @return retourne la version si elle existe, null sinon 
	 * @param int $pPublicId
	 * @param int $pIdHelt
	 */
	public function hasANewVersion ($pPublicId, $pIdHelt){
		$lastVersion = DAOcms_headingelementinformations::instance ()->findBy (_daoSP ()
			->addCondition ('public_id_hei', '=', $pPublicId)
			->addCondition ('id_helt', '>', $pIdHelt)
		);
		if (count($lastVersion)){
			return $lastVersion->current();
		}
		return null;
	}
	
	 /**
     * Reordonne les elements selon leur display_order_hei
     *
     * @param Array $pArElements
     * @param String $pField champ de tri
     * @return Array
     */
    public function orderElements ($pArElements, $pField = 'display_order_hei') {
        $order = array();
        foreach ($pArElements as $element) {
            if (array_key_exists($element->$pField, $order)) {
            	//au cas ou y est 2 fois le meme niveau d'ordre, on met le caption derriere
            	$order[$element->$pField.$element->caption_hei] = $element;    
            } else {
                $order[$element->$pField] = $element;
            }
        }
        ksort($order);
		return $order;
    }
    
    /**
     * 
     * Vérifie que les dates données permettent de planifier une publication
     * @param $pPublishedDate date de début de planifiction
     * @param $pEndPublishedDate date de fin de planification
     */
    public function checkPlanningDates ($pPublishedDate, $pEndPublishedDate){
    	if (! $pPublishedDate && !$pEndPublishedDate){
    		return false;
    	}
    	
    	$today = time();
    	if ($pPublishedDate){
    		$timestamp = CopixDateTime::yyyymmddhhiissToTimeStamp(CopixDateTime::DateTimeToyyyymmddhhiiss($pPublishedDate));
    		if ($timestamp < $today){
    			return false;
    		}
    	}
    	if ($pEndPublishedDate)
    	{
    		$endtimestamp = CopixDateTime::yyyymmddhhiissToTimeStamp(CopixDateTime::DateTimeToyyyymmddhhiiss($pEndPublishedDate));
    		if ($endtimestamp < $today){
    			return false;
    		}
    	}
    	if(isset($timestamp) && isset($endtimestamp) && $timestamp >$endtimestamp){
    		return false;
    	}
    	
    	return true;
    }
	
	/**
	 * Retourne les éléments liés au publicId donné
	 * 
	 * @param int $pPublicId
	 * @param string $pTypeHei Type d'élément (heading, page, etc)
	 * @return DAORecordcms_headingelementinformations[]
	 */
	public function getLinkedHeadingElements ($pPublicId, $pTypeHei = null) {
		$toReturn = array ();
		$sp = _daoSP ()->addCondition ('public_id_hei', '=', $pPublicId);
		foreach (DAOcms_headingelementinformations_linkedelements::create ()->findBy ($sp) as $record) {
			$element = $this->_getElement ($record->linked_public_id_hei);
			if ($pTypeHei === null ||$pTypeHei == $element->type_hei) {
				$toReturn[] = $element;
			}
		}
		return $toReturn;
	}
	
	/**
	 * Sauvegarde les liens entre éléments
	 * 
	 * @param int $pPublicId Identifiant publique de l'élément principal
	 * @param int[] $pLinkedElements Identifiants publiques des éléments liés à l'élément principal
	 */
	public function saveLinkedHeadingElements ($pPublicId, $pLinkedElements) {
		$dao = DAOcms_headingelementinformations_linkedelements::create ();
		$dao->deleteBy (_daoSP ()->addCondition ('public_id_hei', '=', $pPublicId));
		$record = DAORecordcms_headingelementinformations_linkedelements::create ();
		$record->public_id_hei = $pPublicId;
		foreach ($pLinkedElements as $linkedPublicId) {
			$record->linked_public_id_hei = $linkedPublicId;
			$dao->insert ($record);
		}
	}
}
