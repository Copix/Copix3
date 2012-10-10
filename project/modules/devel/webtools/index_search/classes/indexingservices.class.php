<?php
/**
 * @package		webtools
 * @subpackage	index_search
 * @author		Duboeuf Damien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Cette class permet l'ajout la supprestion de document à indexés
 * et le relancement d'une indexation gobal
 *
 */
class IndexingServices {

	/**
	 * Repertoire d'indexation du cache
	 *
	 */
	const CACHE_PATH = 'search_index/cache/';
	/**
	 * Repertoire avec les versions textes des documents
	 *
	 */
	const TEXT_PATH  = 'search_index/text/';

	/**
	 * Nombre minimum de caractères pour l'indexation
	 *
	 */
	const MIN_INDEX_WORD  = 3;

	/**
	 * Nombre maximum de caractères pour l'indexation
	 *
	 */
	const MAX_SIZE_WORD  = 50;

	/**
	 * Contante definissant le type PDF
	 */
	const TYPE_PDF = 'pdf';

	/**
	 * Contante definissant le type MSWORD DOC
	 */
	const TYPE_DOC = 'doc';

	/**
	 * Contante definissant le type HTML
	 */
	const TYPE_HTML = 'html';

	/**
	 * Contante definissant le type texte
	 */
	const TYPE_TXT = 'txt';


	/**
	 * Contante definissant le type texte brute sans fichier
	 */
	const TYPE_TXT_BRUTE = 'txtbrute';

	/**
	 * Contante definissant le type HTML brute sans fichier
	 */
	const TYPE_HTML_BRUTE = 'htmlbrute';


	/**
	 * Contante definissant le patron alpha-numerique avec accent;
	 *
	 * @FIXME Pour le moment on enlève les - et _ a voir s'il est opportun de les remettre
	 */
	//const PATTERN_ALPHANUM = '/([^a-zA-Z0-9éèêëäâàiîïuùûüoôöyÿç@\-\_])/';
	const PATTERN_ALPHANUM = '/([^a-zA-Z0-9éèêëäâàiîïuùûüoôöyÿç@])/';

	/**
	 * Identifiant en base du document
	 *
	 * @var int
	 */
	private $_idDocument;

	/**
	 * Type du document
	 *
	 * @var string
	 */
	private $_type;

	/**
	 * Url d'accet au document
	 * Peut être égal au $_pathFile
	 *
	 * @var string
	 */
	private $_url;

	/**
	 * Nom du document
	 *
	 * @var string
	 */
	private $_name;

	/**
	 * Texte à afficher lors du resultat de la recherche
	 *
	 * @var string
	 */
	private $_caption;

	/**
	 * Chaine de droit sur le fichier
	 *
	 * @var string
	 */
	private $_credentials;

	/**
	 * Contenu pour l'indexation d'un texte sans fichier cible
	 *
	 * @var string
	 */
	private $_contentTextBrute;

	/**
	 * Ajoute un objet à indéxer dans al base
	 *
	 * @param string	$pName			Nom de l'objet à enregistrer il doit être unique et commencer par module|
	 *              	      			(ou module est remplacé par le nom du module).
	 * @param string	$pUrl			Adresse pour accéder au document
	 * @param string	$pCaption		Texte qui sera affiché pour les resultats.
	 * @param array		$pCredentials	Droit d'accès sur le fichier. Si aucun droit n'est spécifié tout le monde peut y acceder.
	 * @param string	$pType			Type du fichier à indexer.
	 * @param string $pPath Chemin de l'objet à indexer
	 */
	private function _addObject ($pName, $pUrl, $pCaption, $pCredentials, $pType, $pPath) {

	    // Convertie le tableau de
	    if ($pCredentials == NULL) {
	        $pCredentials = array ();
	    }
	    if (!is_array($pCredentials)) {
	        $pCredentials = array ($pCredentials);
	    }

		$this->_idDocument = null;
		$this->_url		= $pUrl;
		$this->_type	   = $pType;
		$this->_name	   = $pName;
		$this->_caption	= $pCaption;
	    $this->_credentials = $pCredentials;
		$this->_path = $pPath;
		_log ('PATH : ' . $this->_path, 'debug');

		// Lance la bonne méthode d'indexation en fonction du type si besoin
		switch ($this->_type) {

			case self::TYPE_PDF  :
			case self::TYPE_DOC  :
			case self::TYPE_HTML :
			case self::TYPE_TXT  :
			case self::TYPE_TXT_BRUTE :
			case self::TYPE_HTML_BRUTE :
				break;
			default :
				throw new CopixException (_i18n ('index_search|index_search.exception.filetypenotexist'));
		}

		$dao = _ioDao ('search_objectindex')->FindBy (_daoSP ()->addCondition ('objectindex_name', '=', $this->_name));

		// Crée ou met à jour l'entré du document dans la base
		// Nettoie le cache et les association mot indexé et document concerné
		if (count ($dao) == 0) {
			$record = _record ('search_objectindex');
			$record->objectindex_name	  = $this->_name;
			$record->objectindex_url	   = $this->_url;
			$record->objectindex_type	  = $this->_type;
			$record->objectindex_caption	= $this->_caption;
			$record->objectindex_path = $this->_path;
			_ioDao ('search_objectindex')->insert ($record);

			$this->_idDocument = $record->objectindex_id;

		}else{

			foreach ($dao as $elem) {
				$elem->objectindex_name	  = $this->_name;
				$elem->objectindex_url	   = $this->_url;
				$elem->objectindex_type	  = $this->_type;
				$elem->objectindex_caption	= $this->_caption;
				$elem->objectindex_path = $this->_path;
				_ioDao ('search_objectindex')->update ($elem);

				$this->_idDocument = $elem->objectindex_id;

				$this->_clearAssociation ();

			}
		}
		foreach ($this->_credentials as $credential) {
		    $record = _record ('search_objectcredential');
		    $record->objectindex_id              = $this->_idDocument;
		    $record->objectcredential_credential = $credential;
		    _ioDAO ('search_objectcredential')->insert ($record);
		}
	}

	/**
	 * Supprime un élément par son URL
	 *
	 * @param string $pURL Adresse
	 */
	public function delete ($pURL) {
		$object = _ioDAO ('search_objectindex')->findBy (_daoSP ()->addCondition ('objectindex_url', '=', $pURL));
		if (count ($object) != 1) {
			return false;
		}
		$id = $object[0]->objectindex_id;

		$sp = _daoSP ()->addCondition ('objectindex_id', '=', $id);
		_ioDAO ('search_objectcredential')->deleteBy ($sp);
		_ioDAO ('search_map')->deleteBy ($sp);
		_ioDAO ('search_objectindex')->deleteBy ($sp);
		// suppression des mots plus utilisés
		_doQuery ('DELETE FROM search_wordlist WHERE wordlist_id NOT IN (SELECT DISTINCT(wordlist_id) FROM search_map)');
	}

	/**
	 * Ajoute un fichier à indéxer
	 *
	 * @param string	$pName			Nom de l'objet à enregistrer il doit être unique et commencer par module|
	 *              	      			(ou module est remplcé par le nom du module).
	 * @param string	$pUrl			Adresse pour acceder au document
	 * @param string	$pCaption		Texte qui sera affiché pour les resultats.
	 * @param array		$pCredentials	Droit d'accet sur le fichier. Si aucun droit n'est spécifier tous el monde peut y acceder.
	 * @param string	$pType			Type du fichier à indexer.
	 * @param string $pPath Chemin de l'objet à indexer
	 */
	public function addFile ($pName, $pUrl, $pCaption, $pCredentials = NULL, $pType = self::TYPE_HTML, $pPath = null) {

		if ($this->_type == self::TYPE_TXT_BRUTE || $this->_type == self::TYPE_HTML_BRUTE) {
			$this->_contentTextBrute = file_get_contents ($this->_url);
		} else {
			$this->_addObject ($pName, $pUrl, $pCaption, $pCredentials, $pType, $pPath);
		}
		// Indexe le document
		$this->_indexDocument ();
	}

	/**
	 * Ajoute un fichier à indéxer
	 *
	 * @param string	$pName		 	Nom de l'objet à enregistrer il doit être unique et commencer par module|
	 *              	      			(ou module est remplcé par le nom du module).
	 * @param string	$pContent		Contenu à indexer
	 * @param string	$pUrl			Adresse pour acceder au document
	 * @param string	$pCaption		Texte qui sera affiché pour les resultats.
	 * @param array		$pCredentials	Droit d'accet sur le fichier. Si aucun droit n'est spécifier tous el monde peut y acceder.
	 * @param string	$pType			Type du fichier à indexer.
	 * @param string $pPath Chemin de l'objet à indexer
	 */
	public function addContent ($pName, $pContent, $pUrl, $pCaption, $pCredentials = NULL, $pType = self::TYPE_HTML_BRUTE, $pPath = null) {

		$this->_addObject ($pName, $pUrl, $pCaption, $pCredentials, $pType, $pPath);

		switch ($this->_type) {
			case self::TYPE_TXT_BRUTE  :
			case self::TYPE_HTML_BRUTE :
				$this->_contentTextBrute = $pContent;
				break;
			default :
				throw new CopixException (_i18n ('index_search|index_search.exception.filetypenotexist'));
		}
		// Indexe le document
		$this->_indexDocument ();
	}

	/**
	 * Renvoi le chemin en cache du fichier dans le cache
	 *
	 * @param string $pIdDoc Id du document
	 * @return string
	 */
	public static function getCachePathFile ($pIdDoc) {
		return COPIX_VAR_PATH.self::CACHE_PATH.$pIdDoc;
	}

	/**
	 * Renvoi le chemin en mode texte du fichier dans le cache
	 *
	 * @param string $pIdDoc Id du document
	 * @return string
	 */
	public static function getTextPathFile ($pIdDoc) {
		return COPIX_VAR_PATH.self::TEXT_PATH.$pIdDoc;
	}

	/**
	 * Renvoi le contenu texte du document indéxé.
	 *
	 * @param string	$pIdDocument	Identifiant du document
	 * @param string	$pType			Type du document. S'il n'est pas spécifié, on va le chercher en base.
	 * @param bollean	$pForceRewrite	Force la reconversion du document vers un fichier texte.
	 * @return unknown
	 */
	public static function getTextContent ($pIdDocument, $pType = null, $pForceRewrite = false) {

		// Creer le repertoire de versions textes de document si il n'existe pas.
		if (!file_exists (COPIX_VAR_PATH.self::TEXT_PATH)) {
			CopixFile::createDir (COPIX_VAR_PATH.self::TEXT_PATH);
		} else if (!is_dir (COPIX_VAR_PATH.self::TEXT_PATH)) {
			unlink (COPIX_VAR_PATH.self::TEXT_PATH);
			CopixFile::createDir (COPIX_VAR_PATH.self::TEXT_PATH);
		}

		_classInclude('all2txt|all2Txt');

		$fileForIndex = '';

		if ($pType === null) {
			$pType = _ioDAO ('search_objectindex')->get ($pIdDocument)->objectindex_type;
		}

		// Convertie les documents binaires en texte
		switch ($pType) {

			case self::TYPE_PDF : {
				// Convertit le document
				if ($pForceRewrite || !file_exists (self::getTextPathFile ($pIdDocument))) {
					_class ('all2txt|all2txt')->pdf2txt (self::getCachePathFile ($pIdDocument), self::getTextPathFile ($pIdDocument));
				}
				$fileForIndex = self::getTextPathFile ($pIdDocument);
				break;
			}

			case self::TYPE_DOC : {
				// Convertit le document
				if ($pForceRewrite || !file_exists (self::getTextPathFile ($pIdDocument))) {
					_class ('all2txt|all2txt')->doc2txt (self::getCachePathFile ($pIdDocument), self::getTextPathFile ($pIdDocument));
				}
				$fileForIndex = self::getTextPathFile ($pIdDocument);
				break;
			}

			case self::TYPE_HTML : {
				// Convertit le document
				if ($pForceRewrite || !file_exists (self::getTextPathFile ($pIdDocument))) {
					_class ('all2txt|all2txt')->html2txt (self::getCachePathFile ($pIdDocument), self::getTextPathFile ($pIdDocument));
				}
				$fileForIndex = self::getTextPathFile ($pIdDocument);
				break;
			}

			case self::TYPE_TXT : {
				// Convertit le document
				$fileForIndex = self::getCachePathFile ($pIdDocument);
				break;
			}

			case self::TYPE_TXT_BRUTE : {
				// Convertit le document
				$fileForIndex = self::getCachePathFile ($pIdDocument);
				break;
			}
			case self::TYPE_HTML_BRUTE : {
				// Convertit le document
				if ($pForceRewrite || !file_exists (self::getTextPathFile ($pIdDocument))) {
					_class ('all2txt|all2txt')->html2txt (self::getCachePathFile ($pIdDocument), self::getTextPathFile ($pIdDocument));
				}
				$fileForIndex = self::getTextPathFile ($pIdDocument);
				break;
			}
			default :
				throw new CopixException (_i18n ('index_search|index_search.exception.filetypenotexist'));
		}

		return file_get_contents ($fileForIndex);
	}

	/**
	 * Supprime les associations à entre les mots clef et un document
	 *
	 */
	private function _clearAssociation () {
		_ioDAO ('search_map')             ->deleteBy (_daoSP ()->addCondition ('objectindex_id', '=', $this->_idDocument));
		_ioDAO ('search_objectcredential')->deleteBy (_daoSP ()->addCondition ('objectindex_id', '=', $this->_idDocument));
	}

	/**
	 * Met en cache le document
	 * @param	string	$pText	Texte à mettre en cache si il n'y a pas de fichier source
	 *       	      	      	Est à null sil le ficheir source existe
	 */
	private function _addDocumentInCache ($pText = null) {

		// Creer le repertoire de cache de l'indexation si il n'existe pas.
		if (!file_exists (COPIX_VAR_PATH.self::CACHE_PATH)) {
			CopixFile::createDir (COPIX_VAR_PATH.self::CACHE_PATH);
		} else if (!is_dir (COPIX_VAR_PATH.self::CACHE_PATH)) {
			unlink (COPIX_VAR_PATH.self::CACHE_PATH);
			CopixFile::createDir (COPIX_VAR_PATH.self::CACHE_PATH);
		}

		// Copie le fichier dans le cache
		if ($pText === null) {
			_log ('cache 1 [' . $pText . ']', 'debug');
			copy ($this->_url, self::getCachePathFile ($this->_idDocument));
		} else {
			_log ('cache 2 [' . $pText . '] [' . self::getCachePathFile ($this->_idDocument) . ']', 'debug');
			CopixFile::write (self::getCachePathFile ($this->_idDocument), $pText);
		}
	}

	/**
	 * Test si la chaine est en utf8
	 *
	 * @param string $string
	 * @return boolean
	 */
	public static function isUTF8 ($pStr) {

	    // Il est intéresssant d'avoir la function mb_detect_encoding installé pour des raisons de performance
		if (function_exists ('mb_detect_encoding')) {
			return mb_detect_encoding ($pStr, array ('UTF-8', 'ISO-8859-15', 'ASCII')) == 'UTF-8';
		} else {
			// TODO découper la chaine car plante si le texte est trop grand. Tous les 1024 caractères
			return preg_match('/^(?:[\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})*$/', $pStr) ? true : false;
		}
	}

	/**
	 * Remplace les caractères spéciaux UTF-8 par des caractères lisibles pour html_entities
	 *
	 * @param string $pStr
	 * @return string
	 */
	public static function specialCharReplace (&$pStr) {
		$pStr = str_replace('’', '\'', $pStr);
		$pStr = str_replace('‘', '\'', $pStr);
		$pStr = str_replace('“', '"', $pStr);
		$pStr = str_replace('”', '"', $pStr);
		$pStr = str_replace('…', '...', $pStr);
		$pStr = str_replace('œ', 'oe', $pStr);
		$pStr = str_replace('»', '>>', $pStr);
		$pStr = str_replace('«', '<<', $pStr);
		$pStr = str_replace('€', 'euro', $pStr);

		return $pStr;
	}

	/**
	 * Remplace les caractères non-alphaNumériques
	 * Renvoi de l'ISO
	 *
	 * @param string $pContentTXT
	 * @return string
	 */
	public static function hideNoAlphaNumChar ($pContentTXT, $pStr = ' ') {
	    return preg_replace (utf8_decode (self::PATTERN_ALPHANUM), $pStr, self::isUTF8 ($pContentTXT) ? utf8_decode ($pContentTXT) :  ($pContentTXT));
	}


	/**
	 * Renvoi la valeur d'un mot
	 * @param string $pWord
	 * @return string
	 */
	public static function getWordValue ($pWord) {
		$arWordsort = array_unique(str_split(utf8_decode($pWord)));
		sort($arWordsort);
		return utf8_encode(implode ($arWordsort));
	}


	/**
	 * Index un document en base
	 *
	 */
	private function _indexDocument () {

		// Met en cache les document
		switch ($this->_type) {

			case self::TYPE_PDF  :
			case self::TYPE_DOC  :
			case self::TYPE_HTML :
			case self::TYPE_TXT  :
				// Met en cache le document
				$this->_addDocumentInCache ();
				break;

			case self::TYPE_TXT_BRUTE  :
			case self::TYPE_HTML_BRUTE :
				// Met en cache le document
				$this->_addDocumentInCache ($this->_contentTextBrute);
				break;

			default :
				throw new CopixException (_i18n ('index_search|index_search.exception.filetypenotexist'));
		}

		$contentTXT = self::getTextContent ($this->_idDocument, $this->_type, true);

		// Convertion de caractère inconnu pour le html_entities
		self::specialCharReplace ($contentTXT);
		$contentTXT = $this->hideNoAlphaNumChar ($contentTXT);


		// Attention sur les fichiers trop volumineux ont un dépassement de memoire sur l'explode
		// La solution consisterai a découper le contenu du fichier en bloc de 1024 octet
		// De manière à vider la mémoire
		$pos = 0;
		$size = strlen ($contentTXT);
		while ($pos < $size) {
			$oldPos = $pos;
			$pos = $oldPos + 1024;
			if ($pos > $size) {
				$pos = $size;
			}
			$pos = strpos ($contentTXT, ' ', $pos);
			if ($pos === false) {
				$pos = $size;
			}

			$arWordRaw = explode (' ', substr($contentTXT, $oldPos, $pos - $oldPos));
			$arWord = array();
			foreach ($arWordRaw as $word) {

				if ($word != '') {
					$word = strtolower ($word);
					if (isset ($arWord [utf8_encode ($word)])) {
						$arWord [utf8_encode ($word)]++;
					} else {
						$arWord [utf8_encode ($word)] = 1;
					}
				}
			}

			$idWord = null;
			foreach ($arWord as $word=>$point) {

				if (strlen ($word) >= self::MIN_INDEX_WORD && strlen ($word) <= self::MAX_SIZE_WORD) {
					$dao = _ioDao ('search_wordlist')->FindBy (_daoSP ()->addCondition ('wordlist_text', 'like', $word));
					if (count ($dao) == 0) {

						$record = _record  ('search_wordlist');
						$record->wordlist_text = $word;
						$record->wordlist_phonetic = soundex ($word);
						$record->wordlist_sortvalue = self::getWordValue ($word);
						_ioDao ('search_wordlist')->insert ($record);
						$idWord = $record->wordlist_id;
					} else {
						foreach ($dao as $elem) {
							$idWord = $elem->wordlist_id;
						}
					}
					$map = _ioDao ('search_map')->get ($this->_idDocument, $idWord);
					if ($map) {
					    $map = _record ('search_map');
					    $map->objectindex_id = $this->_idDocument;
					    $map->wordlist_id	 = $idWord;
					    $map->map_point	    += $point;
					    _ioDao ('search_map')->update ($map);
					} else {
					    $record = _record ('search_map');
					    $record->objectindex_id = $this->_idDocument;
					    $record->wordlist_id	= $idWord;
					    $record->map_point	    = $point;
					    _ioDao ('search_map')->insert ($record);
					}
				}
			}
		}
	}
}