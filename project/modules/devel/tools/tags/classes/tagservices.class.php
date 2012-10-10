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
 * Cette classe permet l'utilisation des Tags
 */
class TagServices {
	/**
	 * Type keyword des meta-informations
	 *
	 * @var string
	 */
	public static $T_META_KEYWORD = 'keyword';
	/**
	 * Type link des meta-informations
	 *
	 * @var string
	 */
	public static $T_META_LINK    = 'link';


	/**
	 * Ajoute un tag si il n'existe pas
	 *
	 * Si $pNameTag est un tableau et qu'il y a une erreur dans ses éléments
	 * (duplication de tag ou nom vide) alors aucun tag n'est créé
	 *
	 * @param string array  $pNameTag   Tag à ajouter
	 */
	public function add ($pNameTag) {
		// Convertit $pNameTag en tableau si il est une chaine
		if(!is_array ($pNameTag)) {
			$pNameTag = array ($pNameTag);
		}

		// Test les erreurs possibles
		foreach ($pNameTag as $name) {
			// - Le tag existe déjà
			if (_ioDAO ('tags')->get ($name)) {
				throw (new CopixException (_i18n ('tags|tags.exception.alreadyexist', $name)));
			}
			// - Le tag est a null ou est vide
			if ($name === '' || $name === null) {
				throw (new CopixException (_i18n ('tags|tags.exception.empty', $name)));
			}
		}

		// Ajoutes les tags
		foreach ($pNameTag as $name) {
			$tagsR = DAORecordTags::create ();
			$tagsR->name_tag = $name;
			DAOTags::instance ()->insert ($tagsR);
		}
	}

	/**
	 * Supprime un tag
	 *
	 * Si $pNameTag est un tableau et qu'il y a une erreur dans ses éléments
	 * (tag inexistant) alors aucun tag n'est supprimé
	 *
	 * @param string array  $pNameTag   tag à supprimer
	 */
	public function delete ($pNameTag) {
		// Convertit $pNameTag en tableau si il est une chaine
		if(!is_array ($pNameTag)) {
			$pNameTag = array ($pNameTag);
		}

		// test si tous les tags existent
		foreach ($pNameTag as $name) {
			if (!DAOTags::instance ()->get ($name)) {
				throw (new CopixException (_i18n ('tags|tags.exception.notexist', $name)));
			}
		}

		// Supprime les tags
		foreach ($pNameTag as $name) {
			DAOtags_content::instance ()->deleteBy (_daoSP ()->addCondition ('name_tag', '=', $name));
			$this->deleteAllInformations ($name);
			DAOTags::instance ()->delete ($name);
		}
	}

	/**
	 * Renomme un tag
	 *
	 * @param string $pNameTag      Nom du tag source
	 * @param string $pNewNameTag   Nom du tag destination
	 */
	public function rename ($pNameTag, $pNewNameTag) {
		// Test si le Tags destination existent déja
		if (DAOTags::instance ()->get ($pNewNameTag)) {
			throw (new CopixException (_i18n ('tags|tags.exception.alreadyexist', $pNewNameTag)));
		}

		$this->move ($pNameTag, $pNewNameTag, true);
	}

	/**
	 * Déplace des associations vers un nouveau tag
	 *
	 * @param string $pNameTag      Tag au quel est encore associé l'objet
	 * @param string $pNewNameTag   Nouveau tag ou de tag au quel sera associé l'objet
	 * @param string $pDeleteSource Si $delete est a true supprime l'ancien tag
	 */
	public function move ($pNameTag, $pNewNameTag, $pDeleteSource = false) {
		// Test si le Tags source existent
		if (!DAOTags::instance ()->get ($pNameTag)) {
			throw (new CopixException (_i18n ('tags|tags.exception.notexist', $pNameTag)));
		}

		// Crée un tag si la destination n'existe pas
		if (!DAOTags::instance ()->get ($pNewNameTag)) {
			$this->add ($pNewNameTag);
			$daoDesc = DAOTags::instance ()->get ($pNewNameTag);
			$daoDesc->description_tag = _ioDAO ('tags')->get ($pNameTag)->description_tag;
			DAOTags::instance ()->update ($daoDesc);
		}

		$sp = _daoSp()->addCondition('name_tag', '=', $pNameTag);

		foreach (DAOTags_content::instance ()->findBy($sp) as $content) {
			$contentR = DAORecordTags_content::create ();
			$contentR->name_tag    = $pNewNameTag;
			$contentR->kindobj_tag = $content->kindobj_tag;
			$contentR->idobj_tag   = $content->idobj_tag;

			if (!DAOTags_content::instance ()->get ($pNewNameTag, $content->kindobj_tag, $content->idobj_tag)) {
				DAOTags_content::instance ()->insert ($contentR);
			}
			$this->deleteAssociation ($content->idobj_tag, $content->kindobj_tag, $pNameTag);
		}
		foreach (DAOTags_informations::instance ()->findBy($sp) as $infos) {
			$infos->name_tag = $pNewNameTag;
			DAOTags_informations::instance ()->update ($infos);
		}
		if ($pDeleteSource) {
			DAOTags::instance ()->delete ($pNameTag);
		}
	}

	/**
	 * Associe un tag ou une liste de tag à un objet
	 * Si un des tags à associer n'existe pas il le crée
	 *
	 * @param string $pIdObjet          Identifiant de l'objet au quel sera associé le tag
	 * @param string $pKindObjet        Nom du module parent à $idObjet ou sous element du module parent à $idObjet où sera associé le tag
	 * @param string array $pNameTag    Tag ou tableau de tag au quel sera associé l'objet
	 */
	public function addAssociation ($pIdObjet, $pKindObjet, $pNameTag) {
		// Convertit $nameTag en tableau si il est une chaine
		if(!is_array ($pNameTag)) {
			$pNameTag = array ($pNameTag);
		}

		foreach ($pNameTag as $name) {
			if ($name == '' || $name == null) {
				throw (new CopixException (_i18n ('tags|tags.exception.empty', $name)));
			}
		}

		foreach ($pNameTag as $name){
			// Création du tag si il n'existe pas
			if(!DAOTags::instance ()->get ($name)) {
				$this->add ($name);
			}

			if (!DAOTags_content::instance ()->get ($name, $pKindObjet, $pIdObjet)) {
				// Insertion des associations
				$tag_contentR = DAORecordTags_Content::create ();
				$tag_contentR->name_tag    = $name;
				$tag_contentR->kindobj_tag = $pKindObjet;
				$tag_contentR->idobj_tag   = $pIdObjet;
				DAOTags_content::instance ()->insert ($tag_contentR);
			}
		}
	}

	/**
	 * Renvoie les tags associé au kindObjet et à l'idObjet
	 *
	 * @param string $pIdObjet      Identifiant de l'objet au quel est associé le tag.
	 * @param string $pKindObjet    Nom du module parent à $idObjet ou sous element du module parent à $idObjet où est associé le tag
	 *
	 * @return array
	 */
	public function getAssociation ($pIdObjet, $pKindObjet) {
		$sp = _daoSp()->addCondition('kindobj_tag', '=', $pKindObjet)
		->addCondition('idobj_tag'  , '=', $pIdObjet)
		->orderBy ('name_tag');

		$listeName = array ();
		foreach (DAOTags_content::instance ()->findBy ($sp) as $obj) {
			$listeName[] = $obj->name_tag;
		}
		return $listeName;
	}

	/**
	 * Supprime les associations à un tag
	 * Si $nameTag est null suprime toutes les association de $kindObjet et $idObjet
	 *
	 * @param string $pIdObjet          Identifiant de l'objet au quel est associé le tag
	 * @param string $pKindObjet        Nom du module parent à $idObjet ou sous element du module parent à $idObjet où est associé le tag
	 * @param string array $pNameTag    Tag ou tableau de tag au quel est associé l'objet
	 */
	public function deleteAssociation ($pIdObjet, $pKindObjet, $pNameTag = null) {
		// Convertit $nameTag en tableau si il est une chaine
		if(is_string ($pNameTag)) {
			$pNameTag = array ($pNameTag);
		}

		$sp = _daoSp()->addCondition ('kindobj_tag', 'like', $pKindObjet, 'and');

		if ($pIdObjet !== null) {
			$sp->addCondition ('idobj_tag'  , 'like', $pIdObjet, 'and');
		}

		$sp->startGroup ('and');
		if ($pNameTag !== null) {
			foreach ($pNameTag as $name) {
				$sp->addCondition ('name_tag'  , 'like', $name, 'or');
			}
		}
		$sp->endGroup ();
		DAOTags_content::instance ()->deleteBy ($sp);
	}


	/**
	 * Ajoute une information à un tag
	 *
	 * @param string $pNameTag      Nom du tag cible
	 * @param string $pKind         Type de l'information
	 * @param string $pInformation  Information à enregistrer
	 */
	protected function _addInformation ($pNameTag, $pKind, $pInformation) {
		// test si le tag existe
		if (!DAOtags::instance ()->get ($pNameTag)) {
			throw (new CopixException (_i18n ('tags|tags.exception.notexist', $pNameTag)));
		}

		$record = DAORecordTags_informations::create ();
		$record->name_tag     = $pNameTag;
		$record->type_tagi    = $pKind;
		$record->content_tagi = $pInformation;
		DAOTags_informations::instance ()->insert ($record);
	}

	/**
	 * Supprime une information rattachée à un tag
	 *
	 * @param int $pIdInformation identifiant de l'information
	 */
	public function deleteInformation ($pIdInformation) {
		// test si le tag existe
		if (!DAOTags_informations::instance ()->get ($pIdInformation)) {
			throw (new CopixException (_i18n ('tags|tags.exception.idinfonotexist', $pIdInformation)));
		}
		DAOTags_informations::instance ()->delete ($pIdInformation);
	}

	/**
	 * Supprime toutes les informations attachée à un tag
	 *
	 * @param string $pNameTag Nom du tag
	 */
	public function deleteAllInformations ($pNameTag) {
		// test si le tag existe
		if (!DAOTags::instance ()->get ($pNameTag)) {
			throw (new CopixException (_i18n ('tags|tags.exception.notexist', $pNameTag)));
		}

		$sp = _daoSP()->addCondition('name_tag', '=', $pNameTag);
		DAOTags_informations::instance ()->deleteBy ($sp);
	}

	/**
	 * Ajoute un mot clef associé au tag
	 *
	 * @param string $pNameTag  Nom du tag
	 * @param string $pKeyword  Mot clef
	 */
	public function addKeyword ($pNameTag, $pKeyword) {
		$this->_addInformation($pNameTag, self::$T_META_KEYWORD, '<'.self::$T_META_KEYWORD.'>'.$pKeyword.'</'.self::$T_META_KEYWORD.'>');
	}

	/**
	 * Ajout d'un meta-lien associé au tag
	 *
	 * @param string $pNameTag  Nom du tag
	 * @param string $pRel      Champs rel de la balise link
	 * @param string $pHref     Cible de la balise link
	 * @param string $pTitle    Titre de la balise link
	 */
	public function addLink ($pNameTag, $pRel, $pHref, $pTitle){
		$this->_addInformation($pNameTag, self::$T_META_LINK, '<'.self::$T_META_LINK.'>'.
                                                                   '<rel>'.$pRel.'</rel>'.
                                                                   '<href>'.$pHref.'</href>'.
                                                                   '<title>'.$pTitle.'</title>'.
                                                              '</'.self::$T_META_LINK.'>');
	}

	/**
	 * Renvoie un la iste des identifiants associé à un kind et un tag donnée
	 *
	 * @param string $pNameTag  NOm du tag
	 * @param string $pKind     kindObject de l'association
	 * @return array
	 */
	public function getListId ($pNameTag, $pKind) {
	 	// 	test si le tag existe
		if (!DAOTags::instance ()->get ($pNameTag)) {
			throw (new CopixException (_i18n ('tags|tags.exception.notexist', $pNameTag)));
		}

		$sp = _daoSP ()->addCondition('name_tag'   , '=', $pNameTag)
		->addCondition('kindobj_tag', '=', $pKind);

		$dao = DAOTags_content::instance ()-> findBy ($sp);

		$arReturn = array ();
		foreach ($dao as $content) {
			$arReturn[] = $content->idobj_tag;
		}

		return $arReturn;
	}

	/**
	 * Renvoie tout les tags pour une association
	 *
	 * @param string $pKind     kindObject de l'association
	 * @return array
	 */
	public function listAll ($pKind = false) {
		if ($pKind){
			$sp = _daoSP ()->addCondition('kindobj_tag', '=', $pKind);
			$dao = DAOTags_Content::instance ()-> findBy ($sp);
		} else {
			$dao = DAOTags_Content::instance ()-> findAll();
    	}
        
        $arReturn = array ();
        foreach ($dao as $content) {
            $arReturn[] = $content->name_tag;
        }
        
        return $arReturn;
    }
}