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
 * Cette class permet la recherche de document à indexés
 *
 */
class Researcher {
	
	/**
	 * Taille de la chaine de recherche max
	 * Si la chaine est plus grande elle sera tronquer
	 * @var int
	 */
	const MAX_SIZE_SEARCH_STR = 300;
	
	/**
	 * Nombre de test efefctué pour trouver une orthographe voisine
	 * Attention plus cette valeur est haute plus la recherche sera lente
	 * @var int
	 */
	const NB_TEST_SIMILARWORD = 1;
	
	
	/**
	 * Liste des resultats de la recherche
	 *
	 * @var array
	 */
	private $_listResults;
	
	/**
	 * Liste des mots avec leur orthographe voisine
	 * La clef est le mot => La valeur l'orthographe voisine
	 *
	 * @var array
	 */
	private $_arWords;
	
	/**
	 * Nombre de page pour la recherche
	 *
	 * @var int
	 */
	private $_pageNumber;
	
	/**
	 * Chaine de recherche
	 * @var string
	 */
	private $_searchString;
	
	/**
	 * Groupe une liste de résultats par Union (mots clef séparés par un espace)
	 * @param array $pListResultsByWord
	 * @return array
	 */
	private function _groupeResultUnion ($pListResultsByWord) {
		
		if (count ($pListResultsByWord) == 1) {
			return $pListResultsByWord[0];
		}
		$listResults = array ();
		foreach ($pListResultsByWord[0] as $pListResultsFirst) {
			$wordPoint = $pListResultsFirst->point;
			$indice    = $pListResultsFirst->idobject;
			$word      = array ($pListResultsFirst->word);
			
			for ($i = 1; $i < count ($pListResultsByWord); $i++) {
				if (isset ($pListResultsByWord[$i][$indice])) {
					$wordPoint += $pListResultsByWord[$i][$indice]->point;
					$word[]     = $pListResultsByWord[$i][$indice]->word;
				}
			}
			$result = new stdClass ();
			$result->caption      = $pListResultsFirst->caption;
			$result->url          = $pListResultsFirst->url;
			$result->idobject     = $pListResultsFirst->idobject;
			$result->type         = $pListResultsFirst->type;
			$result->word         = $word;
			$result->point        = $wordPoint;
			$listResults[$indice] = $result;
		}
		
		return $listResults;
	}
	
	
	/**
	 * Renvoi un tableau avec la valeur des points de chaque mot
	 * Ce tableau est trié relativement avec le tableau de resultats
	 * @param $pListResults
	 * @return unknown_type
	 */
	private function _getListPoints ($pListResults) {
		$listPoints = array ();
		foreach ($pListResults as $result ) {
			$listPoints[] = $result->point;
		}
		return $listPoints;
	}
	
	
	/**
	 * Retourne le nom de la session de recherche
	 * @param $pSearchString
	 * @return unknown_type
	 */
	private function _getSessionSearchName ($pSearchString) {
		
		_classInclude ('index_search|indexingservices');
		return strtr ($pSearchString, 'éèêëäâàiîïuùûüoôöyÿç@\-' , 'eeeeaaaiiiuuuuoooyyc___');
	}
	
	
	/**
	 * Renvoie un mot avec une orthographe voisine plus pertinent
	 * (avec plus de résultats)
	 * @param string $pWord
	 * @return array
	 */
	private function _getSimilarWord ($pWord, $pCountResult) {
		
		$arSimilarWords = array ();
		
		// TODO Penser a rajouter la selection des champs de table renvoyés quand ce sera possible pour optimiser
		$spSoundex = _daoSP ();
		$spSoundex->addCondition ('wordlist_phonetic', 'like', soundex ($pWord));
		$spSoundex->addCondition ('wordlist_text', '<>', $pWord);
		$daoSoundex = _dao ('search_wordlist')->findBy ($spSoundex);
		
		_classInclude ('index_search|indexingservices');
		
		// TODO Penser a rajouter la selection des champs de table renvoyés quand ce sera possible pour optimiser
		$spSortValue = _daoSP ();
		$spSortValue->addCondition ('wordlist_sortvalue', 'like', IndexingServices::getWordValue ($pWord));
		$spSortValue->addCondition ('wordlist_text', '<>', $pWord);
		$daoSortValue = _dao ('search_wordlist')->findBy ($spSortValue);
		
		foreach ($daoSoundex as $dao) {
			$arSimilarWords [$dao->wordlist_text] = levenshtein ($pWord, $dao->wordlist_text);
		}
		
		foreach ($daoSortValue as $dao) {
			//plus rapide de tester l'existence que de calculer la distance levenshtein 2 fois
			if (!isset ($arSimilarWords [$dao->wordlist_text])) {
				$arSimilarWords [$dao->wordlist_text] = levenshtein ($pWord, $dao->wordlist_text);
			}
		}
		
		// Trie et récupère le mots le plus semblable en premier de liste
		asort ($arSimilarWords);
		return array_keys ($arSimilarWords);
	}
	
	/**
	 * Renvoi un tableau de resultat en fonction 
	 * de mots recherchés séparés par un AND OR ou NOT ou un espace
	 *
	 * @param array $word
	 * @param string $pPath Chemin où chercher ce mot
	 * @return array
	 */
	private function _searchWords ($word, $pPath) {
		
		// TODO Penser a rajouter la selection des champs de table renvoyés quand ce sera possible pour optimiser
		$sp = _daoSP ();
		$sp->addCondition ('wordlist_text', '=', $word);
		if ($pPath != null) {
			$sp->addCondition ('objectindex_path', 'LIKE', $pPath . '%');
		}
		
		$dao = _dao ('index_search|search')->findBy ($sp);
		
		$listResults = array ();
		$listPoints  = array ();
		
		foreach ($dao as $objectIndex) {
			
			if (self::testCredential ($objectIndex->objectindex_id)) {
				$result = new stdClass ();
				$result->caption      = $objectIndex->objectindex_caption;
				$result->url          = $objectIndex->objectindex_url;
				$result->idobject     = $objectIndex->objectindex_id;
				$result->type         = $objectIndex->objectindex_type;
				$result->word         = $objectIndex->wordlist_text;
				$result->point        = (int)$objectIndex->map_point;
				
				if (stristr ($result->url, $word)) {
					$result->point += 5; // Correspondance floue
				}
				if (preg_match( '/\b'.$word.'\b/i', $result->caption)) {
					$result->point += 10; // Correspondance exacte entre limites de mots
				}
				$listResults[$objectIndex->objectindex_id] = $result;
			}
		}
		
		return $listResults;
	}
	
	
	/**
	 * Lance la recherche et met en session le resultat.
	 *
	 * @param string $searchString
	 * @param string $pPath Chemin où chercher
	 */
	public function search ($pSearchString, $pPath = null) {
		if (strlen ($pSearchString) > self::MAX_SIZE_SEARCH_STR) {
			$pSearchString = substr ($pSearchString, 0, self::MAX_SIZE_SEARCH_STR);
		}
		_classInclude ('index_search|indexingservices');
	    $this->_searchString = trim (utf8_encode (strtolower (IndexingServices::hideNoAlphaNumChar ($pSearchString))));
		
	    $sessionSearchName = $this->_getSessionSearchName ($this->_searchString);
	    
	    // Recupération de la recherche en session si elle a déjà été effectuée
		$this->_listResults = _sessionGet ('index_search'      , $sessionSearchName);
	    $user               = _sessionGet ('index_search_user' , $sessionSearchName);
	    $this->_arWords     = _sessionGet ('index_search_words', $sessionSearchName);
		
		if ( $this->_listResults == NULL || $this->_arWords == NULL || $user != _currentUser ()->getId ()) {
			$arWord = array_unique (explode (' ', $this->_searchString));
			$listResultsByWord = array ();
			foreach ($arWord as $word) {
				$result = $this->_searchWords ($word, $pPath);
				$this->_arWords[$word] = $this->_getSimilarWord($word, count ($result));
				$listResultsByWord[] = $result;
			}
		    $this->_listResults = $this->_groupeResultUnion ($listResultsByWord);
			$listPoints         = $this->_getListPoints ($this->_listResults);
	
			$objectByPage = CopixConfig::get ('index_search|objectByPage');
			
			$nbResult = count ($this->_listResults);
			$this->_pageNumber = ($nbResult == 0) ? $nbResult : ceil (($nbResult) / $objectByPage);
			
			// Trie les resultats en fonction des points
			array_multisort ($listPoints, SORT_DESC, $this->_listResults);
			
			foreach ($this->_listResults as $key=>$result) {
				$result->pourcent = floor ($listPoints[$key] / $listPoints[0] * 100);
			}
			
			// Sauvegarde de la recherche en session
			_sessionSet ('index_search_user' , _currentUser ()->getId (), $sessionSearchName);
			_sessionSet ('index_search'      , $this->_listResults      , $sessionSearchName);
			_sessionSet ('index_search_page'  , $this->_pageNumber       , $sessionSearchName);
			_sessionSet ('index_search_words', $this->_arWords          , $sessionSearchName);
			
			
		} else {
			$this->_pageNumber = _sessionGet('index_search_page', $sessionSearchName, 1);
		}
	}
	
	
	/**
	 * Test les droits sur un objet indexé
	 *
	 * @param int		$pIdObject
	 * @param boolean	$pAssert
	 */
	public static function testCredential ($pIdObject, $pAssert = false) {
	    $credentielOk = true;
		
		if (($dao = _ioDAO ('search_objectcredential')->findBy (_daoSP ()->addCondition ('objectindex_id' , '=', $pIdObject))) != NULL) {
		    foreach ($dao as $credential) {
				$credentielOk = false;
				if (($credentielOk = _currentUser()->testCredential ($credential->objectcredential_credential))) {
				    break;
				}
			}
		}
		
		if ($pAssert && !$credentielOk) {
			throw new CopixCredentialException ($credential->objectcredential_credential);
		}
		return $credentielOk;
	}
	
	
	/**
	 * Retourne un extrait du texte de $pContent possédant les mots $pWords
	 * @param $pWords
	 * @param $pContent
	 * @return String
	 */
	private function _getOverviewByWords ($pWords, $pContent) {
		$detail = ''; // Le texte avec les mots mis en valeur
		$posWords = array(); // Les positions des mots recherchés
		$firstPos = 0;
		$lastPos = 0;
		
		// Les positions de mots dans le texte, par ordre croissant
		foreach ($pWords as $word) {
			$posWords[ stripos ($pContent, utf8_decode ($word)) ] = $word;
		}
		$posWords = array_flip( $posWords );
		$firstPos = $posWords[$pWords[0]];
		
		// une chaine de 200 caractères autour du premier mot
		if ($firstPos < 50) {
			$detail = substr ($pContent, 0, 200). ' ...';
			$lastPos = 200;
		} else {
			$detail = '... '. substr ($pContent, $firstPos - 50, 200) . ' ...';
			$lastPos = 200 + $firstPos;
		}
		array_shift( $posWords ); // On a déjà traité la position du premier mot
		foreach ($posWords as $word => $position ) {
			if ($position > $lastPos) {
				$detail .= ' '.substr ($pContent, $position, 150) . ' ...';
				$lastPos = $position + 150;
			}
		}
		$detail = self::_highlightwords( $detail, $pWords );
		return utf8_encode ($detail);
	}
	
	
	/**
	 * Retourne les aperçu du contenu de la recherche
	 *
	 *
	 * @param stdClass $pResult
	 * @return string
	 */
	private function _getOverview ($pResult) {
		
		// Recuperation d'un aperçu du texte_classInclude ('IndexingServices');
		_classInclude ('index_search|indexingservices');
		$content  = indexingServices::getTextContent ($pResult->idobject, $pResult->type);
		
		// Conversion en ISO pour pouvoir traiter les chaînes
		if (indexingServices::isUTF8 ($content)) {
		    
		    // Conversion de caractères inconnus pour le html_entities
		    IndexingServices::specialCharReplace ($content);
		    $content = utf8_decode ($content);
		}
		// TODO faire ces modifications lors de l'indexation
		$content  = trim ($content);
		$content = preg_replace ('/\s+/', ' ', $content);
		if (!is_array($pResult->word)) {
			$pResult->word = array ($pResult->word);
		}
		
		return $this->_getOverviewByWords($pResult->word, $content);
	}
	
	
	/**
	 * Revoie la liste des resultats pour une page
	 *
	 * @param array $page
	 */
	public function getResult ($page = 0) {
		
		$objectByPage = CopixConfig::get ('index_search|objectByPage');
		
		// Test si la page entrer est correcte
		if ($page < 0) {
			$page = 0;
		}
		if ($page > $this->pageNumber()) {
			$page = $this->pageNumber();
		}
		
		$nbObject    = count ($this->_listResults);
		$listResults = array ();
		
		for ($i = $objectByPage * $page; $i < $objectByPage * ($page + 1) && $i < $nbObject; $i++) {
			$result = $this->_listResults[$i];
			$result->detail   = $this->_getOverview ($this->_listResults[$i]);
			$listResults[] = $result;
		}
		return $listResults;
	}
	
	
	/**
	 * Renvoie le nombre de resultat
	 *
	 * @return int
	 */
	public function getNumberResults () {
		return count ($this->_listResults);
	}
	
	
	/**
	 * Renvoie une chaine de recherche avec une orthographe voisine
	 * si cette dernière est plus pertinante.
	 * La chaine sera au format html;
	 * Renvoie NULL si elle ne l'est pas
	 * @return string
	 */
	public function getSimilarSearchString () {
		return isset ($this->_arWords[$this->_searchString]) ? $this->_arWords[$this->_searchString] : array ();
		
		$similarStr = utf8_decode ($this->_searchString);
		$toReturn = array ();
		$similarStr = IndexingServices::specialCharReplace ($similarStr);
		$similarStr = explode (' ', $similarStr);
		
		foreach ($similarStr as $word){
			if (isset ($this->_arWords[$word])){
				foreach ($this->_arWords[$word] as $wordToAdd){
					$toReturn[$wordToAdd] = $wordToAdd;					
				}
			}
		}
		return $toReturn;
	}
	
	/**
	 * renvoi le nombre de page de la recherche
	 *
	 * @return int
	 */
	public function pageNumber () {
		return $this->_pageNumber;
	}
	
	private static function _highlightwords ($pStr, $pWords = array() ) {
		// Balises utilisées pour la mise en valeur
		$cssClass = CopixConfig::get('index_search|cssClass');
		$highlightBefore = (empty($cssClass)) ? '<strong>' : '<span class="'.$cssClass.'">';
		$highlightAfter  = (empty($cssClass)) ? '</strong>' : '</span>';
		
		if (!is_array($pWords)) {
			$pWords = array( $pWords );
		}
		
		foreach ($pWords as $word) {
			$word = utf8_decode( $word );
			$pStr = preg_replace ('/\b'.$word.'\b/i', $highlightBefore.$word.$highlightAfter, $pStr);
		}
		return $pStr;
	}
}