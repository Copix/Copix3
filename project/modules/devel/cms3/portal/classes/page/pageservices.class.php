<?php
class PageException extends CopixException {}
class PageStateException extends PageException {};

class PageServices extends HeadingElementServices {
	const BREADCRUMB_TYPE_AUTO = 1;
	const BREADCRUMB_TYPE_NONE = 2;

	/**
	 * Retourne une courte description pour l'affichage détails
	 *
	 * @param int $pIdHelt Identifiant interne
	 * @return string
	 */
	public function getDisplayDescription ($pIdHelt) {
		$element = $this->getById ($pIdHelt);
		$title = (strlen ($element->title_hei) > 20) ? substr ($element->title_hei, 0, 20) . '...' : $element->title_hei;
		return 'Auteur : ' . $element->author_caption_create_hei . ' - ' . $title;
	}

	/**
	 *
	 * Retourne la description de l'élément
	 * @param int $pIdHelt
	 */
	public function getDescription ($pIdHelt) {
		$element = $this->getById ($pIdHelt);
		return $element->description_hei;
	}

	/**
	 * Création d'une nouvelle rubrique
	 * @param array / object $pPageDescription
	 */
	public function insert ($pPageDescription) {
		HeadingCache::clear ();
		$pageDescription = _ppo ($pPageDescription);

		CopixDB::begin ();
		try {
			//création de l'enregistrement
			$record = DAORecordcms_pages::create ()->initFromDbObject ($pageDescription);
			$record->breadcrumb_type_page = self::BREADCRUMB_TYPE_AUTO;
			DAOcms_pages::instance ()->insert ($record);

			//Mise à jour pour les informations génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName) {
				$record->$propertyName = $pageDescription[$propertyName];
			}
			$record->id_helt = $record->id_page;
			$record->type_hei = 'page';

			//dans $record->id_helt on a le nouvel identifiant
			_ioClass ('heading|HeadingElementInformationServices')->insert ($record);
            //on met maintenant a jour l'élément $record avec les informations publiques mises à jour

			DAOcms_pages::instance ()->update ($record);
			//Application des changements
			_ppo ($record)->saveIn ($pPageDescription);

			//enregistrements des portlets
			foreach ($pPageDescription->getportlets () as $columnPortlets) {
				foreach ($columnPortlets as $portlet) {
					$portlet->id_page = $pPageDescription->id_page;
					_class ('portal|PortletServices')->insert ($portlet, true);
				}
			}
		}catch (CopixException $e) {
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}

	/**
	 * Mise à jour d'une page
	 *
	 * @param array / object $pPageDescription
	 */
	public function update ($pPageDescription) {
		HeadingCache::clear ();
		$pageDescription = _ppo ($pPageDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($pageDescription['id_page']);

			//on met a jour les données spécifiques
			$pageDescription->saveIn ($record);
			DAOcms_pages::instance ()->update ($record);

			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName) {
				$record->$propertyName = $pageDescription[$propertyName];
			}

			_ioClass ('heading|HeadingElementInformationServices')->update ($record);

			//enregistrement, mise à jour et suppression des portlets
			$criteres = _daoSP ()->addCondition ('id_page', '=', $pPageDescription->id_page);
			foreach ($pPageDescription->getportlets () as $columnPortlets) {
				foreach ($columnPortlets as $portlet) {
					if($portlet->getId ()) {
						_class ('portal|PortletServices')->update ($portlet, true);
					}
					else{
						$portlet->id_page = $pPageDescription->id_page;
						_class ('portal|PortletServices')->insert ($portlet, true);
					}
					$criteres->addCondition ('id_portlet', '<>', $portlet->getId());
				}
			}

			$portletsToDelete = DAOcms_portlets::instance ()->findBy ($criteres)->fetchAll ();
			if(!empty ($portletsToDelete)) {
				foreach ($portletsToDelete as $portlet)
				{
					_class ('portal|portletservices')->deleteById ($portlet->id_portlet, true);
				}
			}

			//Application des changements
			_ppo ($record)->saveIn ($pPageDescription);
		}
		catch (CopixException $e) {
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
	 * @param object $pPageDescription la description de la page dont on souhaite obtenir une nouvelle version
	 */
	public function version ($pPageDescription) {
		HeadingCache::clear ();
		$pageDescription = _ppo ($pPageDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($pageDescription['id_page']);

			//on met a jour les données spécifiques
			$record->description_hei = $pageDescription['description_hei'];
			$record->template_page = $pageDescription['template_page'];
			$record->browser_page = $pageDescription['browser_page'];
			$record->menu_caption_hei = $pageDescription['menu_caption_hei'];

			DAOcms_pages::instance ()->insert ($record);

			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName) {
				$record->$propertyName = $pageDescription[$propertyName];
			}
			$record->id_helt = $record->id_page;
			_ioClass ('heading|HeadingElementInformationServices')->version ($record, $pageDescription['id_page']);

			//Application des changements
			_ppo ($record)->saveIn ($pPageDescription);
			//enregistrements des portlets
			foreach ($pPageDescription->getportlets () as $columnPortlets) {
				foreach ($columnPortlets as $portlet) {
					$portlet->id_page = $pPageDescription->id_page;
					_class ('portal|PortletServices')->insert ($portlet, true);
				}
			}
		}catch (CopixException $e) {
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}

 	/* Création d'une nouvelle version a partir de l'élément passé en paramètre
	 *
	 * @param object $pPageDescription la description de la page dont on souhaite obtenir un clone
	 */
	public function copy ($pPublicId, $pHeading) {
		HeadingCache::clear ();
		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getByPublicId($pPublicId);
			$record->public_id_hei = null;
			$record->url_id_hei = $record->url_id_hei ? $record->url_id_hei . ' (copie)' : $record->url_id_hei;
			DAOcms_pages::instance ()->insert ($record);

			$record->id_helt = $record->id_page;
			$record->parent_heading_public_id_hei = $pHeading;
			$record->caption_hei = $record->caption_hei . ' (copie)';
			$record->display_order_hei = _ioClass ('heading|HeadingElementInformationServices')->getNextDisplayOrderValue ($pHeading);
			_ioClass ('heading|HeadingElementInformationServices')->insert ($record);

			DAOcms_pages::instance ()->update ($record);

			//enregistrements des portlets
			foreach ($record->getportlets () as $columnPortlets) {
				foreach ($columnPortlets as $portlet) {
					$portlet->id_page = $record->id_page;
					_class ('portal|PortletServices')->insert ($portlet, true);
				}
			}
		}catch (CopixException $e) {
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		CopixDB::commit ();
		HeadingCache::clear ();
		return $record->public_id_hei;
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
		$results = DAOcms_headingelementinformations::instance ()-> findBy (_daoSp ()->addCondition ('public_id_hei', '=', $pArPublicId))->fetchAll();
		foreach ($results as $resultat) {
			$this->deletePageContentById ($resultat->id_helt);
		}
		DAOcms_pages::instance ()->deleteBy (_daoSp ()->addCondition ('public_id_hei', '=', $pArPublicId));
		HeadingCache::clear ();
	}

	/**
	 * Supression par identifiant interne
	 *
	 * @param int $pArId les identifiants des éléments à supprimer
	 */
	public function deleteById ($pArId) {
		HeadingCache::clear ();
		$this->deletePageContentById ($pArId);
		DAOcms_pages::instance ()->deleteBy (_daoSp ()->addCondition ('id_page', '=', $pArId));
		HeadingCache::clear ();
	}

	/**
	 * Supprime les portlets par identifiant de page
	 *
	 * @param int $pArId
	 */
	public function deletePageContentById ($pArId) {
		_class ('portal|PortletServices')->deleteByPageId ($pArId);
		HeadingCache::clear ();
	}

	/**
	 * Récupère un enregistrement par son identifiant interne
	 *
	 * @param int $pIdHelt Identifiant interne de l'élément à récupérer
	 */
	public function getById ($pIdHelt) {
		$cacheId = 'pageservices|getById|' . $pIdHelt;
		if (HeadingCache::exists ($cacheId)) {
			return HeadingCache::get ($cacheId);
		}

		//on vérifie que l'élément existe
		if (! $record = DAOcms_pages::instance ()->get ($pIdHelt)) {
			throw new HeadingElementInformationNotFoundException ($pIdHelt);
		}
		//on récupère les informations "communes"
		$element = _ioClass ('heading|HeadingElementInformationServices')->getById ($pIdHelt, 'page');

		//fusion des informations communes et spécifiques
		_ppo ($record)->saveIn ($element);

		//chargement des informations dans un nouvel objet
		$page = new Page ();
		$page->load ($element);

		//chargement des portlets de la page
		$listePortlets = _class ('portal|PortletServices')->getPortletsByPageId ($pIdHelt);
		if(!empty ($listePortlets)) {
			foreach ($listePortlets as $portlet) {
				$page->addPortlet($portlet, $portlet->variable, $portlet->position);
			}
		}

		HeadingCache::set ($cacheId, $page, false);
		return $page;
	}

	/**
	 * Récupère un enregistrement par son identifiant interne
	 *
	 * @param int $pPublicId Identifiant public de l'élément à récupérer
	 */
	public function getByPublicId ($pPublicId) {
		$id = _ioClass ('heading|HeadingElementInformationServices')->getId ($pPublicId);
		return $this->getById ($id['id_helt']);
	}

	/**
	 * Fonction simple pour créer une page d'exemple pour le CMS et pouvoir travailler sur les exemplaes
	 */
	public function createFooPage () {
		$pageDescription = _ppo ('caption_hei=>Titre de ma page exemple;description_hei=>Description de ma page;template_page=>portal|colonne_1.page.tpl');
		$page = new Page ();
		$page->load ($pageDescription);
		return $page;
	}

	/**
	 * Prévisualisation de l'élément
	 *
	 * @param string $pId
	 */
	public function previewById ($pId) {
		$record = $this->getById ($pId);
		$infos = array ();

		if ($record->title_hei != null) {
			$infos[] = array ('title' => array ('caption' => 'Titre', 'value' => $record->title_hei));
		}

		if ($record->browser_page != null) {
			$infos[] = array ('browser' => array ('caption' => 'Navigateur', 'value' => $record->browser_page));
		}

		// Récupération des infos de template en fonction du thème de la page
		$currentTheme = CopixTpl::getTheme ();
		CopixTpl::setTheme (preg_replace('/\|.*/', '', $record->theme_id_hei));

		try {
			$theme = _ioClass("heading|headingelement/headingelementinformationservices")->getTheme($record->public_id_hei, $foo);
			$template = _ioClass ('portal|TemplateServices')->getInfos (CopixTpl::getFilePath ('portal|pagetemplates/pagetemplates.xml', $theme), $record->template_page);
			$templateName = $template->name;
		} catch (CopixException $e) {
			$templateName = 'Template "' . $record->template_page . '" inexistant dans le thème ' . $record->theme_id_hei;
		}
		CopixTpl::setTheme ($currentTheme);
		$infos = array ('template' => array ('caption' => 'Template', 'value' => $templateName));

		return CopixZone::process ('heading|headingelement/headingelementpreview', array ('record' => $record, 'infos' => $infos));
	}

	/**
	 * Retourne le contenu d'une page
	 *
	 * @param int $pPublicId Identifiant public, si null sera _request ('public_id')
	 * @return string
	 */
	public function getContent ($pPublicId) {
		$ppo = _ppo ();
		$editedElement = _ioClass('portal|pageservices')->getByPublicId ($pPublicId);
		$currentTheme = CopixTpl::getTheme();
		$theme = _ioClass('heaing|headingElementInformationServices')->getTheme ($pPublicId, $fooParameterIn);
   		if ($theme) {
			CopixTpl::setTheme ($theme);
		}
		$ppo->content = $editedElement->render (RendererMode::HTML, RendererContext::SEARCHED) ;

		CopixTpl::setTheme ($currentTheme);

		$ppo->title   = $editedElement->caption_hei;
		$ppo->summary = $editedElement->description_hei;
		return $ppo;
	}

	public function getPublishedPagesList ($pOrderBy = null, $pSearch = array ()) {
		$cacheId = 'pageservices|getPublishedPagesList|' . var_export ($pOrderBy, true) . '|' . var_export ($pSearch, true);
		if (HeadingCache::exists ($cacheId)) {
			return HeadingCache::get ($cacheId);
		}

		$params = array();
		$query = 'SELECT h.*, ';
		$query .= '(SELECT COUNT(id_helt) FROM cms_headingelementinformations h2 WHERE h.public_id_hei = h2.public_id_hei AND h2.id_hei > h.id_hei) as have_next_status ';
		$query .= 'FROM cms_headingelementinformations h, cms_pages p WHERE h.status_hei = '.HeadingElementStatus::PUBLISHED.' AND h.type_hei = \'page\' AND h.id_helt = p.id_page';

		// paramètres de recherche
		if (array_key_exists ('title', $pSearch)) {
			$query .= ($pSearch['title']) ? ' AND (LENGTH(h.title_hei) > 0 OR h.title_hei IS NOT NULL)' : ' AND (LENGTH(h.title_hei) = 0 OR h.title_hei IS NULL)';
		}
		if (array_key_exists ('description', $pSearch)) {
			$query .= ($pSearch['description']) ? ' AND (LENGTH(h.description_hei) > 0 OR h.description_hei IS NOT NULL)' : ' AND (LENGTH(h.description_hei) = 0 OR h.description_hei IS NULL)';
		}
		if (array_key_exists ('url', $pSearch)) {
			$query .= ($pSearch['url']) ? ' AND (LENGTH(h.url_id_hei) > 0 OR h.url_id_hei IS NOT NULL)' : ' AND (LENGTH(h.url_id_hei) = 0 OR h.url_id_hei IS NULL)';
		}
		if (array_key_exists ('inheading', $pSearch)) {
			$query .= $pSearch['inheading'] ? ' AND h.hierarchy_hei LIKE :heading' : '';
			$params[':heading'] = $pSearch['inheading'] == 0 ? $pSearch['inheading'].'-%' : '%-'.$pSearch['inheading'].'-%';
		}

		// tri du retour
		if ($pOrderBy != null) {
			$query .= ' ORDER BY ' . $pOrderBy;
		}

		$toReturn = _doQuery ($query, $params);
		HeadingCache::set ($cacheId, $toReturn, false);
		return $toReturn;
	}

	/**
	 * Retourne la liste des ancres d'une page
	 *
	 * @param int $pPublicId
	 */
	public function getPageAnchors ($pPageId) {
		$cacheId = 'pageservices|getPageAnchors|' . $pPageId;
		if (HeadingCache::exists ($cacheId)) {
			return HeadingCache::get ($cacheId);
		}

		$sp = _daoSP()->addCondition('id_page', '=', $pPageId)->addCondition ('type_portlet', '=', 'PortletAnchor');
		$toReturn = DAOcms_portlets::instance ()->findBy($sp);
		HeadingCache::set ($cacheId, $toReturn, false);
		return $toReturn;
	}

	/**
	 * Retourne les pages faisant référence à l'element de publicId $pPublicId
	 *
	 * @param int $pPublicId
	 */
	public function getDependencies ($pPublicId) {
		$query = "SELECT id_page from cms_portlets_headingelementinformations phei
					LEFT JOIN cms_portlets p ON phei.id_portlet = p.id_portlet
					WHERE p.public_id_hei IS NULL
					AND phei.public_id_hei = :public_id";

		$toReturn = array();
		$results = _doQuery($query, array(":public_id"=>$pPublicId));
		foreach ($results as $result) {
			try{
				$toReturn[] = $this->getById ($result->id_page);
			} catch(HeadingElementInformationNotFoundException $e){
				_log("Element fantome, page id_helt : ".$result->id_page."<br />".$e->getMessage(), "errors");
			}
		}
		return $toReturn;
	}

	/**
	 * (non-PHPdoc)
	 * @see project/modules/devel/cms3/heading/classes/HeadingElementServices::findGhosts()
	 */
	public function findGhosts (){
		$toReturn['specific'] = _doQuery ('select * from cms_pages where id_page not in(select id_helt from cms_headingelementinformations where type_hei = :type and status_hei <> :status)', array (':type'=>'page', ':status'=>HeadingElementStatus::DELETED));
		$toReturn['general']  = _doQuery ('select * from cms_headingelementinformations where type_hei = :type and id_helt not in (select id_page from cms_pages)', array (':type'=>'page'));
		return $toReturn;

	}
}