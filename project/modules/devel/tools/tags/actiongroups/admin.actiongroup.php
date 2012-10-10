<?php
/**
 * @package tools
 * @subpackage tags
 * @author   Duboeuf Damien
 * @copyright   CopixTeam
 * @link        http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actions d'administration sur les tags
 */
class ActionGroupAdmin extends CopixActionGroup {
	/**
	 * On vérifie les droits de modérations ou administration et on
	 */
	protected function beforeAction ($pActionName){
		_notify ('breadcrumb', array ('path' => array ('admin||' => 'Administration')));
		_currentUser()->assertCredential('module:edition|moderateur@tags');
	}

	/**
	 * Renomme un tag
	 *
	 * @param string $oldTag    Ancien tag
	 * @param string $newTag    Nouveau tag
	 * @return string Renvoie null si il n'y a pas d'erreur
	 */
	private function _rename ($pOldTag, $pNewTag) {
		try {
			_class ('tagservices')->rename ($pOldTag, $pNewTag);
			return null;
		}catch (CopixException $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Change la description du tag
	 *
	 * @param string $pTag          nom du tag
	 * @param string $pDescription  Description du tag
	 */
	private function updateDescription ($pTag, $pDescription) {
		$newDesc = DAOTags::instance ()->get ($pTag);
		$newDesc->description_tag = $pDescription;
		DAOTags::instance ()->update ($newDesc);
	}

	/**
	 * Appelle liste des tags
	 */
	public function processDefault () {
		return $this->processView ();
	}

	/**
	 * Liste des tags
	 */
	public function processView () {
		_notify ('breadcrumb', array ('path' => array ('tags|admin|view' => 'Administration des Tags')));

		$namespace = _request('namespace', uniqid());
		$tagWrite  = _request('tagwrite', '');

		$listeTags = array ();
		foreach (DAOTags::instance ()->findBy (_daoSp ()->orderBy ('name_tag')) as $tag) {
			$listeTags[] = $tag->name_tag;
		}

		$ppo             = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('Tag List');
		$ppo->arTags     = $listeTags;
		$ppo->namespace  = $namespace;
		$ppo->tagWrite   = $tagWrite;
		$ppo->errors     = CopixSession::get('tag|errors', $ppo->namespace);
		$ppo->maxlength  = 50;

		if ($ppo->errors === null) {
			$ppo->errors = array ();
		}

		CopixSession::destroyNamespace($namespace);
		return _arPpo ($ppo, 'tags.list.php');
	}

	/**
	 * Ajout d'un nouveau tag
	 */
	public function processAdd (){
		CopixRequest::assert('name_tag');

		$tag = _request('name_tag', '');
		$namespace = _request('namespace', uniqid());

		try {
			_class ('tagservices')->add ($tag);
			return _arRedirect (_url ('admin|view', array ('namespace' => $namespace)));
		}catch (CopixException $e){
			CopixSession::set('tag|errors', array ($e->getMessage()), $namespace);
			return _arRedirect (_url ('admin|view', array ('namespace' => $namespace, 'tagwrite' => $tag)));
		}
	}

	/**
	 * Édition des informations générales d'un tag
	 */
	public function processEdit () {
		$nameTag    = _request ('name_tag', '');
		$namespace  = _request ('namespace', uniqid());
		$daoTag     = DAOTags::instance ()->get ($nameTag);
		_notify ('breadcrumb', array ('path' => array ('tags|admin|view' => 'Administration des Tags', '#'=>_i18n ('Updating Tag [%s]', $nameTag))));

		// Récuperation des erreurs en session
		$errors = CopixSession::get ('tag|errors', $namespace);
		if ($errors === null) {
			$errors = array ();
		}

		$tagWrite = CopixSession::get ('tag|newTag', $namespace);
		if ($tagWrite === null) {
			$tagWrite = $nameTag;
		}

		$description = CopixSession::get ('tag|description', $namespace);
		if ($description === null) {
			$description = $daoTag->description_tag;
		}

		// Vidage de la session
		CopixSession::set('tag|errors', array (), $namespace);

		// Test si le tag n'existe pas
		if ($nameTag === '' || !DAOTags::instance ()->get ($nameTag)) {
			CopixSession::set('tag|errors', array (_i18n ('tags|tags.exception.notexist', $nameTag)), $namespace);
			return _arRedirect (_url('admin|view', array ('namespace' => $namespace)));
		}

		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE   = _i18n ('Updating Tag [%s]', $nameTag);
		$ppo->nameTag      = $nameTag;
		$ppo->namespace    = $namespace;
		$ppo->errors       = $errors;
		$ppo->tagWrite     = $tagWrite;
		$ppo->description  = $description;
		$ppo->admin	       = CopixAuth::getCurrentUser ()->testCredential ('module:edition|administrateur@tags');

		return _arPPO($ppo, 'tags.edit-general.tpl');
	}

	/**
	 * Affichage des informations d'association d'un tag
	 *
	 */
	public function processEditAssociations () {
		$nameTag    = _request ('name_tag', '');
		$namespace  = _request ('namespace', uniqid());

		// Test si le tag exist
		if ($nameTag === '' || !DAOTags::instance ()->get ($nameTag)) {
			CopixSession::set('tag|errors', array (_i18n ('tags|tags.exception.notexist', $nameTag)), $namespace);
			return _arRedirect (_url('admin|view', array ('namespace' => $namespace)));
		}

		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE   = _i18n ('tags.tagsEdit', $nameTag);
		$ppo->nameTag      = $nameTag;
		$ppo->namespace    = $namespace;
		$ppo->arAsociation = DAOTags_Content::instance ()->findBy (_daoSP()->addCondition('name_tag', 'like', $nameTag)
														 ->orderBy ('kindobj_tag'));
		return _arPPO($ppo, 'tags.edit-associations.tpl');
	}

	/**
	 * Affichage les meta-informations d'un tag
	 */
	public function processEditMetaInformations () {
		$nameTag     = _request ('name_tag', '');
		$namespace   = _request ('namespace', uniqid());
		$link_id     = _request ('link_id', '-1');
		$link_nid    = _request ('link_nid', '-1');
		$keyword_id  = _request ('keyword_id', '-1');
		$keyword_nid = _request ('keyword_nid', '-1');
		$arKeywordW  = array ();
		$arLinkW     = array ();

		// Récuperation des erreurs en session
		$errors = CopixSession::get ('tag|errors', $namespace);
		if ($errors === null) {
			$errors = array ();
		}

		// Récuperation du tableau des meta-infos lien
		$arLink = CopixSession::get ('tag|arLink', $namespace);
		if ($arLink === null) {
			$arLink = array ();
		}
		$arNewLink = CopixSession::get ('tag|arNewLink', $namespace);
		if ($arNewLink === null) {
			$arNewLink = array ();
		}
		$arKeyword = CopixSession::get ('tag|arKeyword', $namespace);
		if ($arKeyword === null) {
			$arKeyword = array ();
		}
		$arNewKeyword = CopixSession::get ('tag|arNewKeyword', $namespace);
		if ($arNewKeyword === null) {
			$arNewKeyword = array ();
		}

		$rel         = CopixSession::get('tag|rel'         , $namespace);
		$title       = CopixSession::get('tag|title'       , $namespace);
		$link        = CopixSession::get('tag|link'        , $namespace);
		$textKeyword = CopixSession::get('tag|textKeyword' , $namespace);
		$nRel        = CopixSession::get('tag|editRel'     , $namespace);
		$nTitle      = CopixSession::get('tag|editTitle   ', $namespace);
		$nLink       = CopixSession::get('tag|editLink'    , $namespace);
		$nText       = CopixSession::get('tag|editKeyword' , $namespace);

		// Vidage de la session
		CopixSession::delete('tag|errors'      , $namespace);
		CopixSession::delete('tag|rel'         , $namespace);
		CopixSession::delete('tag|title'       , $namespace);
		CopixSession::delete('tag|link'        , $namespace);
		CopixSession::delete('tag|editRel'     , $namespace);
		CopixSession::delete('tag|editTitle'   , $namespace);
		CopixSession::delete('tag|editLink'    , $namespace);
		CopixSession::delete('tag|editKeyword' , $namespace);

		// Test si le tag n'existe pas
		if ($nameTag === '' || !DAOTags::instance ()->get ($nameTag)) {
			CopixSession::set('tag|errors', array (_i18n ('tags|tags.exception.notexist', $nameTag)), $namespace);
			return _arRedirect (_url('admin|view', array ('namespace' => $namespace)));
		}

		_classInclude('tagservices');
		$spLink    = _daoSP()->addCondition('name_tag' , '=', $nameTag)
							->addCondition('type_tagi', '=', TagServices::$T_META_LINK);
		$spkeyword = _daoSP()->addCondition('name_tag' , '=', $nameTag)
							->addCondition('type_tagi', '=', TagServices::$T_META_KEYWORD);

		foreach (DAOTags_Informations::instance ()->findBy ($spLink) as $dao) {
			if (isset ($arLink [$dao->id_tagi])) {
				if (is_object ($arLink [$dao->id_tagi])) {
					$arLinkW[$dao->id_tagi] = $arLink [$dao->id_tagi];
				}
			} else {
				$obj        = new stdClass ();
				$xml = simplexml_load_string ($dao->content_tagi);
				$obj->rel   = $xml->rel;
				$obj->title = $xml->title;
				$obj->link  = $xml->href;
				$arLinkW[$dao->id_tagi] = $obj;
			}
		}

		foreach (DAOTags_Informations::instance ()->findBy ($spkeyword) as $dao) {
			if (isset ($arKeyword [$dao->id_tagi])) {
				if (is_object ($arKeyword [$dao->id_tagi])) {
					$arKeywordW[$dao->id_tagi] = $arKeyword [$dao->id_tagi];
				}
			} else {
				$obj        = new stdClass ();
				$xml = simplexml_load_string ($dao->content_tagi);
				$obj->texte   = $xml[0];
				$arKeywordW[$dao->id_tagi] = $obj;
			}
		}

		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE   = _i18n ('tags.tagsEdit', $nameTag);
		$ppo->nameTag      = $nameTag;
		$ppo->namespace    = $namespace;
		$ppo->errors       = $errors;
		$ppo->rel          = $rel;
		$ppo->title        = $title;
		$ppo->link         = $link;
		$ppo->textKeyword  = $textKeyword;
		$ppo->arLink       = $arLinkW;
		$ppo->arKeyword    = $arKeywordW;
		$ppo->arNewLink    = $arNewLink;
		$ppo->arNewKeyword = $arNewKeyword;
		$ppo->link_id      = $link_id;
		$ppo->link_nid     = $link_nid;
		$ppo->keyword_id   = $keyword_id;
		$ppo->keyword_nid  = $keyword_nid;
		$ppo->nTitle       = $nTitle;
		$ppo->nRel         = $nRel;
		$ppo->nLink        = $nLink;
		$ppo->nText        = $nText;
		$ppo->admin	       = CopixAuth::getCurrentUser ()->testCredential ('module:edition|administrateur@tags');
		 
		return _arPPO($ppo, 'tags.edit-metainformations.tpl');
	}

	/**
	 * Modifie un tag
	 *
	 */
	public function processUpdate () {
		$namespace   = _request ('namespace'  , uniqid());
		$nameTag     = _request ('name_tag'   , '');
		$newTag      = _request ('newTag'     , CopixSession::get ('tag|newTag', $namespace));
		$description = _request ('description', CopixSession::get ('tag|description', $namespace));

		// Test si le tag existe
		if ($nameTag === '' || !DAOTags::instance ()->get ($nameTag)) {
			CopixSession::set('tag|errors', array (_i18n ('tags|tags.exception.notexist', $nameTag)), $namespace);
			return _arRedirect (_url ('admin|view', array ('namespace'=>$namespace)));
		}

		// Test si l'on renomme le tag
		if ($newTag === NULL) {
			$newTag = $nameTag;
		}

		// Récuperation du tableau des meta-infos lien
		$arLink = CopixSession::get ('tag|arLink', $namespace);
		if ($arLink === null) {
			$arLink = array ();
		}
		$arNewLink = CopixSession::get ('tag|arNewLink', $namespace);
		if ($arNewLink === null) {
			$arNewLink = array ();
		}
		$arKeyword = CopixSession::get ('tag|arKeyword', $namespace);
		if ($arKeyword === null) {
			$arKeyword = array ();
		}
		$arNewKeyword = CopixSession::get ('tag|arNewKeyword', $namespace);
		if ($arNewKeyword === null) {
			$arNewKeyword = array ();
		}

		// Récupère la description du tag
		if ($description === null) {
			$description = DAOTags::instance ()->get ($nameTag)->description_tag;
		}

		// Si l'on change de page sans valider
		if (($valonglet = _request ('onglet', '')) !== '') {
			CopixSession::set ('tag|newTag'     , $newTag     , $namespace);
			CopixSession::set ('tag|description', $description, $namespace);

			if ($valonglet === 'meta') {
				return _arRedirect (_url ('admin|editMetaInformations', array ('name_tag'=>$nameTag, 'namespace'=>$namespace)));
			} else {
				return _arRedirect (_url ('admin|editAssociations', array ('name_tag'=>$nameTag, 'namespace'=>$namespace)));
			}
		}

		// Renomme le tag si besoin
		if ($newTag !== $nameTag) {
			if (($error = $this->_rename($nameTag, $newTag)) === null) {
				$nameTag = $newTag;
			} else {
				CopixSession::set ('tag|errors'     , array ($error), $namespace);
				CopixSession::set ('tag|newTag'     , $newTag       , $namespace);
				CopixSession::set ('tag|description', $description  , $namespace);

				return _arRedirect (_url ('admin|edit', array ('name_tag'=>$nameTag, 'namespace'=>$namespace)));
			}
		}

		// Met a jour la description
		$this->updateDescription ($nameTag, $description);

		// Update les meta-données modifié
		foreach ($arLink as $key=>$obj) {
			DAOTags_Informations::instance ()->delete ($key);
			if (is_object ($obj)) {
				_class ('tagservices')->addLink ($nameTag, $obj->rel, $obj->link, $obj->title);
			}
		}
		foreach ($arKeyword as $key=>$obj) {
			DAOTags_Informations::instance ()->delete ($key);
			if (is_object ($obj)) {
				_class ('tagservices')->addKeyword ($nameTag, $obj->texte);
			}
		}

		// Ajoute les nouvelles meta-données
		foreach ($arNewLink as $obj) {
			_class ('tagservices')->addLink ($nameTag, $obj->rel, $obj->link, $obj->title);
		}
		foreach ($arNewKeyword  as $obj) {
			_class ('tagservices')->addKeyword ($nameTag, $obj->texte);
		}

		CopixSession::destroyNamespace($namespace);

		return _arRedirect (_url ('admin|view', array ('namespace'=>$namespace)));
	}

	/**
	 * Supression d'un tag
	 */
	public function processDelete (){
		CopixRequest::assert('name_tag');

		$tagName   = _request('name_tag');
		$namespace = _request('namespace', uniqid());
		$yes       = _request('yes', null);
		$newTags   = _request('newTags', '');
		$listeTags = array ();

		if (!DAOTags::instance ()->get ($tagName)) {
			CopixSession::set('tag|errors', array (_i18n ('tags.exception.notexist', $tagName)), $namespace);
			return _arRedirect (_url ('admin|view', array ('namespace'=>$namespace)));
		}

		if ($yes !== null) {
			if ($newTags === '') {
				try {
					_class ('tagservices')-> delete ($tagName);
					return _arRedirect (_url ('admin|view', array ('namespace'=>$namespace)));
				} catch (CopixException $e) {
					CopixSession::set('tag|errors', array ($e->getMessage()), $namespace);
					return _arRedirect (_url ('admin|delete', array ('namespace'=>$namespace, 'name_tag'=>$tagName)));
				}
			} else {
				try {
					_class ('tagservices')-> move ($tagName, $newTags, true);
					return _arRedirect (_url ('admin|view', array ('namespace'=>$namespace)));
				} catch (CopixException $e) {
					CopixSession::set('tag|errors', array ($e->getMessage()), $namespace);
					return _arRedirect (_url ('admin|delete', array ('namespace'=>$namespace, 'name_tag'=>$tagName)));
				}
			}
		}

		foreach (DAOTags::instance ()->findBy (_daoSp ()->orderBy ('name_tag')) as $tag) {
			if ($tag->name_tag != $tagName) {
				$listeTags[$tag->name_tag] = $tag->name_tag;
			}
		}

		$ppo             = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('tags.tagsDelete');
		$ppo->arTags     = $listeTags;
		$ppo->nameTag    = $tagName;
		$ppo->namespace  = $namespace;
		$ppo->errors     = CopixSession::get('tag|errors', $ppo->namespace);

		if ($ppo->errors === null) {
			$ppo->errors = array ();
		}

		return _arPPO ($ppo, 'tags.move.tpl');
	}

	/**
	 * Ajoute et modifie des meta-informations à un tag
	 *
	 */
	public function processAddMeta () {
		$nameTag    = _request ('name_tag', '');
		$namespace  = _request ('namespace', uniqid());
		$link_id     = _request ('link_id', null);
		$link_nid    = _request ('link_nid', null);
		$keyword_id  = _request ('keyword_id', null);
		$keyword_nid = _request ('keyword_nid', null);

		// Récuperation du tableau des meta-infos lien
		$arNewLink = CopixSession::get ('tag|arNewLink', $namespace);
		if ($arNewLink === null) {
			$arNewLink = array ();
		}
		$arLink = CopixSession::get ('tag|arLink', $namespace);
		if ($arLink === null) {
			$arLink = array ();
		}
		$arKeyword = CopixSession::get ('tag|arKeyword', $namespace);
		if ($arKeyword === null) {
			$arKeyword = array ();
		}
		$arNewKeyword = CopixSession::get ('tag|arNewKeyword', $namespace);
		if ($arNewKeyword === null) {
			$arNewKeyword = array ();
		}

		// Test si le tag n'existe pas
		if ($nameTag === '' || !DAOTags::instance ()->get ($nameTag)) {
			CopixSession::set('tag|errors', array (_i18n ('tags|tags.exception.notexist', $nameTag)), $namespace);
			return _arRedirect (_url('admin|view', array ('namespace' => $namespace)));
		}

		// Test si il y a un id pour modification d'un link
		if ($link_id !== null || $link_nid !== null) {

			$rel        = _request ('editRel', '');
			$title      = _request ('editTitle', '');
			$link       = _request ('editLink', '');

			if ($rel === '' || $title === '' || $link === '' ) {
				CopixSession::set('tag|errors', array (_i18n ('tags|tags.errors.empty')), $namespace);
				CopixSession::set('tag|editRel'   ,$rel   , $namespace);
				CopixSession::set('tag|editTitle' ,$title , $namespace);
				CopixSession::set('tag|editLink'  ,$link  , $namespace);

				return _arRedirect (_url('admin|editMetaInformations', array ((($link_id === null) ? 'link_nid' : 'link_id') => (($link_id === null) ? $link_nid : $link_id),
                                                                              'name_tag'=>$nameTag, 'namespace' => $namespace)));
			}

			// Test si on travail sur un nouvel element ou un déjà enregistré
			if ($link_nid !== null) {
				if (isset ($arNewLink [$link_nid])) {
					$arNewLink [$link_nid]->rel   = $rel;
					$arNewLink [$link_nid]->title = $title;
					$arNewLink [$link_nid]->link  = $link;
					// Mise à jour dans la session
					CopixSession::set ('tag|arNewLink', $arNewLink, $namespace);
				}
			} else {
				$obj = new stdClass();
				$obj->rel   = $rel;
				$obj->title = $title;
				$obj->link  = $link;
				$arLink [$link_id] = $obj;
				// Mise à jour dans la session
				CopixSession::set ('tag|arLink', $arLink, $namespace);
			}

			return _arRedirect (_url('admin|editMetaInformations', array ('namespace' => $namespace, 'name_tag' => $nameTag)));
		}
		// Test si il y a un id pour modification d'un mot clef
		if ($keyword_id !== null || $keyword_nid !== null) {
			$texte = _request ('editKeyword', '');

			if ($texte === '') {
				CopixSession::set('tag|errors', array (_i18n ('tags|tags.errors.empty')), $namespace);
				CopixSession::set('tag|editKeyword', $texte   , $namespace);
				return _arRedirect (_url('admin|editMetaInformations', array ((($keyword_id === null) ? 'keyword_nid' : 'keyword_id') => (($keyword_id === null) ? $keyword_nid : $keyword_id),
                                                                              'name_tag'=>$nameTag, 'namespace' => $namespace)));
			}

			// Test si on travail sur un nouvel element ou un déjà enregistré
			if ($keyword_nid !== null) {
				if (isset ($arNewLink [$keyword_nid])) {
					$arNewKeyword [$keyword_nid]->texte = $texte;
					// Mise à jour dans la session
					CopixSession::set ('tag|arNewKeyword', $arNewKeyword, $namespace);
				}
			} else {
				$obj = new stdClass();
				$obj->texte = $texte;
				$arKeyword [$keyword_id] = $obj;
				// Mise à jour dans la session
				CopixSession::set ('tag|arKeyword', $arKeyword, $namespace);
			}

			return _arRedirect (_url('admin|editMetaInformations', array ('namespace' => $namespace, 'name_tag' => $nameTag)));
		}

		// Ajout d'un lien
		if (_request('addLink', false)) {
			$obj      = new stdClass ();
			$obj->rel   = _request('rel', '');
			$obj->title = _request('title', '');
			$obj->link  = _request('link', '');
			if ($obj->rel === '' || $obj->title === '' || $obj->link === '' ) {
				CopixSession::set('tag|errors', array (_i18n ('tags|tags.errors.empty')), $namespace);
				CopixSession::set('tag|rel'   , _request('rel', '')  , $namespace);
				CopixSession::set('tag|title' , _request('title', ''), $namespace);
				CopixSession::set('tag|link'  , _request('link', '') , $namespace);
				return _arRedirect (_url('admin|editMetaInformations', array ('name_tag'=>$nameTag, 'namespace' => $namespace)));
			}
			$arNewLink[] = $obj;
			CopixSession::set ('tag|arNewLink', $arNewLink, $namespace);
		}
		// Ajout d'un lien
		if (_request('addKeyword', false)) {
			$obj      = new stdClass ();
			$obj->texte   = _request('textKeyword', '');
			if ($obj->texte === '') {
				CopixSession::set('tag|errors'     , array (_i18n ('tags|tags.errors.empty')), $namespace);
				CopixSession::set('tag|textKeyword', _request('textKeyword', ''), $namespace);
				return _arRedirect (_url('admin|editMetaInformations', array ('name_tag'=>$nameTag, 'namespace' => $namespace)));
			}
			$arNewKeyword[] = $obj;
			CopixSession::set ('tag|arNewKeyword', $arNewKeyword, $namespace);
		}
		return _arRedirect (_url ('admin|editMetaInformations', array ('name_tag'=>$nameTag, 'namespace'=>$namespace)));
	}

	/**
	 * Supprime une meta-informations
	 */
	public function processDeleteMeta () {
		$nameTag    = _request ('name_tag', '');
		$namespace  = _request ('namespace', uniqid());
		$link_id     = _request ('link_id', null);
		$link_nid    = _request ('link_nid', null);
		$keyword_id  = _request ('keyword_id', null);
		$keyword_nid = _request ('keyword_nid', null);

		// Test si il y a un id
		if ($link_id    === null && $link_nid    === null &&
		$keyword_id === null && $keyword_nid === null) {
			return _arRedirect (_url('admin|editMetaInformations', array ('name_tag'=>$nameTag, 'namespace' => $namespace)));
		}

		// Test si le tag n'existe pas
		if ($nameTag === '' || !DAOTags::instance ()->get ($nameTag)) {
			CopixSession::set('tag|errors', array (_i18n ('tags|tags.exception.notexist', $nameTag)), $namespace);
			return _arRedirect (_url('admin|editMetaInformations', array ('namespace' => $namespace)));
		}
		// Récuperation du tableau des meta-infos lien
		$arLink = CopixSession::get ('tag|arLink', $namespace);
		if ($arLink === null) {
			$arLink = array ();
		}
		$arNewLink = CopixSession::get ('tag|arNewLink', $namespace);
		if ($arNewLink === null) {
			$arNewLink = array ();
		}
		$arKeyword = CopixSession::get ('tag|arKeyword', $namespace);
		if ($arKeyword === null) {
			$arKeyword = array ();
		}
		$arNewKeyword = CopixSession::get ('tag|arNewKeyword', $namespace);
		if ($arNewKeyword === null) {
			$arNewKeyword = array ();
		}

		// Test si l'on traite une meta-info existante
		if($link_id !== null ){
			$arLink[$link_id] = '';
			// Mise à jour dans la session
			CopixSession::set ('tag|arLink', $arLink, $namespace);
			// Sinon on traite une une meta-info ajoutée
		} else if($link_nid !== null ) {
			if (isset ($arNewLink [$link_nid])) {
				unset ($arNewLink [$link_nid]);
				// Mise à jour dans la session
				CopixSession::set ('tag|arNewLink', $arNewLink, $namespace);
			}
		} else if($keyword_id !== null ){
			if (isset ($arKeyword [$keyword_nid])) {
				$arKeyword[$keyword_id] = '';
				// Mise à jour dans la session
				CopixSession::set ('tag|arKeyword', $arKeyword, $namespace);
			}
			// Sinon on traite une une meta-info ajoutée
		} else {
			if (isset ($arNewKeyword [$keyword_nid])) {
				unset ($arNewKeyword [$keyword_nid]);
				// Mise à jour dans la session
				CopixSession::set ('tag|arNewKeyword', $arNewKeyword, $namespace);
            }
        }
        return _arRedirect (_url('admin|editMetaInformations', array ('name_tag'=>$nameTag, 'namespace' => $namespace)));
    }
}