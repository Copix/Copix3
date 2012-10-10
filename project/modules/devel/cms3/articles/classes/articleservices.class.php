<?php
/**
 * Gestion des articles
 */
class ArticleServices extends HeadingElementServices {
	/**
	 * Retourne le nombre de mots d'un contenu HTML
	 *
	 * @param string $pString Chaine HTML
	 * @return int
	 */
	private function _countWords ($pString) {
		$caracters = utf8_decode (strip_tags ($pString));
		$caracters = str_replace ("\n", ' ', $caracters);
		$caracters = str_replace ("\r", null, $caracters);
		return count (explode (' ', $caracters));
	}

	/**
	 * Retourne une courte description pour l'affichage détails
	 *
	 * @param int $pIdHelt Identifiant interne
	 * @return string
	 */
	public function getDisplayDescription ($pIdHelt) {
		$article = $this->getById ($pIdHelt);
		$toReturn = 'Auteur : ' . $article->author_caption_create_hei . ' - ';

		// mots du contenu
		$words = $this->_countWords ($article->content_article);
		$toReturn .= $words . ' ' . ($words <= 1 ? 'mot' : 'mots') . ' - ';

		// mots de la description
		$words = $this->_countWords ($article->description_hei);
		$toReturn .= 'Description : ' . $words . ' ' . ($words <= 1 ? 'mot' : 'mots') . ' - ';

		// mots du résumé
		$words = $this->_countWords ($article->summary_article);
		$toReturn .= 'Résumé : ' . $words . ' ' . ($words <= 1 ? 'mot' : 'mots');

		return $toReturn;
	}
	
	/**
	 * 
	 * Retourne les publicId des éléments qui contiennent $toSearch
	 * @param string $toSearch
	 */
	public function search ($toSearch){
		$toReturn = array ();
		$res = DAOcms_articles::instance ()->findBy(_daoSP()->addSql('lower(content_article) like :toSearch', array('toSearch' => '%'.$toSearch.'%')));
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
		$article = $this->getById ($pIdHelt);
		$toreturn = '';
		switch ($article->editor_article){
			case CmsEditorServices::WYSIWYG_EDITOR :
				$toreturn = _class ('cms_editor|cmswysiwygparser')->transform ($article->content_article);
				break;
			default:
				$toreturn = _class ('cms_editor|cmswikiparser')->transform ($article->content_article);
		}
		return $toreturn;
	}
	
	
	
	/**
	 * Ajoute un article
	 *
	 * @param mixed $pDescription Informations sur l'article
	 */
	public function insert ($pDescription){
		HeadingCache::clear ();
		$articleDescription = _ppo ($pDescription);

		CopixDB::begin ();
		try {
			//création de l'enregistrement
			$record = DAORecordcms_articles::create ()->initFromDbObject ($articleDescription);
			//champ content obligatoire à passer à nul si rien n'a été saisi, pour lever l'excepion
			$record->content_article = ($articleDescription['content_article'] == '')? null : $articleDescription['content_article'];
			
			DAOcms_articles::instance ()->insert ($record);
			
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $articleDescription[$propertyName];
			}
			$record->id_helt = $record->id_article;
			$record->type_hei = 'article';

			//dans $record->id_helt on a le nouvel identifiant
			_ioClass ('heading|HeadingElementInformationServices')->insert ($record);
		
			//on met maintenant a jour l'élément $record avec les informations publiques mises à jour
			DAOcms_articles::instance ()->update ($record);

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
	 * Mise à jour d'une page (création d'une nouvelle version)
	 * 
	 * @param array / object $pDescription
	 */
	public function update ($pDescription){
		HeadingCache::clear ();
		$articleDescription = _ppo ($pDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($articleDescription['id_article']);

			//on met a jour les données spécifiques			
			$record->description_hei = $articleDescription['description_hei'];
			$record->summary_article = $articleDescription['summary_article'];
			$record->editor_article = $articleDescription['editor_article'];
			if($articleDescription['content_article'] != ''){
				$record->content_article = $articleDescription['content_article'];
			}

			DAOcms_articles::instance ()->update ($record);
			
			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $articleDescription[$propertyName];
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
	 * @param object $pDescription la description de la page dont on souhaite obtenir une nouvelle version
	 */
	public function version ($pDescription){
		HeadingCache::clear ();
		$articleDescription = _ppo ($pDescription);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($articleDescription['id_article']);

			//on met a jour les données spécifiques			
			$record->description_hei = $articleDescription['description_hei'];
			$record->summary_article = $articleDescription['summary_article'];
			$record->content_article = $articleDescription['content_article'];
			$record->editor_article = $articleDescription['editor_article'];
			
			DAOcms_articles::instance ()->insert ($record);

			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $articleDescription[$propertyName];
			}
			$record->id_helt = $record->id_article;			
			_ioClass ('heading|HeadingElementInformationServices')->version ($record, $articleDescription['id_article']);

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
	 * Création d'un nouveau document
	 * @param array / object $pDocumentDescription
	 */
	public function copy ($pPublicId, $pHeading){
		HeadingCache::clear ();
		CopixDB::begin ();
		try {
			$article = $this->getByPublicId($pPublicId);
			$article->public_id_hei = null;
			$article->url_id_hei = $article->url_id_hei ? $article->url_id_hei . ' (copie)' : $article->url_id_hei;
			$article->parent_heading_public_id_hei = $pHeading;
			
			DAOcms_articles::instance ()->insert ($article);

			$article->id_helt = $article->id_article;		
			$article->caption_hei = $article->caption_hei . ' (copie)';
			$article->display_order_hei = _ioClass ('heading|HeadingElementInformationServices')->getNextDisplayOrderValue ($pHeading);		

			_ioClass ('heading|HeadingElementInformationServices')->insert ($article);
			
			DAOcms_articles::instance ()->update ($article);
			
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e; 
		}
		CopixDB::commit ();
		HeadingCache::clear ();
		return $article->public_id_hei;
	}

	/**
	 * Récupère un enregistrement par son identifiant interne
	 * 
	 * @param int $pIdHelt Identifiant interne de l'élément à récupérer 
	 */
	public function getById ($pIdHelt){
		//on vérifie que l'élément existe
		if (! $record = DAOcms_articles::instance ()->get ($pIdHelt)){
			throw new HeadingElementInformationNotFoundException ($pIdHelt);
		}
		//on récupère les informations "communes"
		$element = _ioClass ('heading|HeadingElementInformationServices')->getById ($pIdHelt, 'article');
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
		if ( !$record = DAOcms_articles::instance ()->get ($element->id_helt)){
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
	 * Supprime un ou plusieurs articles données en fonction du public_id 
	 * 
	 * Cette fonction supprime toutes les version des articles demandées
	 *
	 * @param int $pArPublicId le ou les identifiants 
	 */
	public function delete ($pArPublicId) {
		DAOcms_articles::instance ()->deleteBy (_daoSp ()->addCondition ('public_id_hei', '=', $pArPublicId));
		HeadingCache::clear ();
	}

	/**
	 * Supression par identifiant interne
	 *
	 * @param int $pArId les identifiants des éléments à supprimer
	 */
	public function deleteById ($pArId) {
		DAOcms_articles::instance ()->deleteBy (_daoSp ()->addCondition ('id_article', '=', $pArId));
		HeadingCache::clear ();
	}
	
	/**
	 * Retourne les articles faisant référence à l'element de publicId $pPublicId
	 *
	 * @param int $pPublicId
	 */
	public function getDependencies ($pPublicId){
		$query = "SELECT DISTINCT id_article FROM cms_articles WHERE content_article LIKE :link OR content_article LIKE :image1 OR content_article LIKE :image2";
		$params = array (
			':link' => '%(cms:' . $pPublicId . ')%',
			':image1' => '%(image:' . $pPublicId . ')%',
			':image2' => '%public_id="' . $pPublicId . '")%'
		);
		$results = _doQuery ($query, $params);
		$toReturn = array ();
		foreach ($results as $result) {
			$toReturn[] = $this->getById ($result->id_article);
		}
		return $toReturn;
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
	 * (non-PHPdoc)
	 * @see project/modules/devel/cms3/heading/classes/HeadingElementServices::findGhosts()
	 */
	public function findGhosts () {
		$toReturn['specific'] = _doQuery ('select * from cms_articles where id_article not in(select id_helt from cms_headingelementinformations where type_hei = :type and status_hei <> :status)', array (':type'=>'article', 'status'=>HeadingElementStatus::DELETED));
		$toReturn['general'] = _doQuery ('select * from cms_headingelementinformations where type_hei = :type and id_helt not in (select id_article from cms_articles)', array (':type'=>'article'));
		return $toReturn;
	}
}