<?php
/**
 * @package copix
 * @subpackage i18n
 * @author Croës Gérald, Jouanneau Laurent, Steevan BARBOYON, Duboeuf Damien
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet de gérer des contenus différents en fonction des langues / pays
 * Depuis les fichiers local
 * @package copix
 * @subpackage i18n
 */
class I18NLocalHandler implements ICopixI18N {

	/**
	 * Modules déja chargés
	 * 
	 * @var array[module][lang]
	 */
	private $_bundles;
	
	/**
	 * Retourne le message contenu dans un fichier .properties, pour la langue $pLocale, ou la langue courante, ou la langue par défaut
	 * 
	 * @param string $pKey Clef
	 * @param mixed String ou array, paramètre(s) %s à remplacer dans le message
	 * @param string $pLocale Force à retourne ce couple langue_PAYS
	 * @return string
	 */
	public function get ($pKey, $pArgs = null, $pLocale = null) {
		
		$keyValue = false;
		if (! $this->_exists ($pKey, $keyValue, $pLocale)){
			return NULL;
		}

		//here, we know the message
		// si args n'est pas un tableau, on le transforme pour pouvoir utiliser array_walk
		if (!is_array ($pArgs)) {
			$pArgs = array ($pArgs);
		}

		// permet de remplacer les valeurs null par __NULL__, pour que sprintf écrive un message indiquant que la variable vaut null
		array_walk ($pArgs, array ($this, '_walkArgs'));
		return call_user_func_array ('sprintf', array_merge (array ($keyValue), $pArgs));
	}


	/**
	 * Vérifie que les items sont différents de null, sinon, remplace par __NULL__
	 *
	 * @param string $pItem Valeur à vérifier
	 * @param int $pKey Clef
	 */
	private function _walkArgs (&$pItem, $pKey) {
		if ($pItem === null) {
			$pItem = '__NULL__';
		}elseif (is_object ($pItem)){
			$pItem = CopixDebug::getDump ($pItem);
		}elseif (is_array ($pItem)){
			$pItem = CopixDebug::getDump ($pItem);
		}
	}
	
	/**
	 * Indique si la clef $pKey existe. En sortie, indique dans $pKeyValue la valeur de cette dernière.
	 *
	 * @param string $pKey
	 * @param string $pKeyValue
	 * @param string $pLocale Couple langue_PAYS dont on veut vérifier l'existance, null pour le couple courant
	 */
	private function _exists ($pKey, & $pKeyValue, $pLocale = null){
		CopixI18N::getLangCountryByLocale ($pLocale, $lang, $country);
		$parts = $this->_parseKey ($pKey);
		if (($pKeyValue = $this->getBundle ($parts['bundle'], $lang, $country)->get ($parts['key'])) === null) {
			$pKeyValue = $parts['key']; 
			return false;
		}
		return true;
	}
	
	/**
	 * Indique si la clef $pKey existe
	 * 
	 * @param string $pKey Clef
	 * @param string $pLocale Couple langue_PAYS dont on veut vérifier l'existance, null pour le couple courant
	 * @return bool
	 */
	public function exists ($pKey, $pLocale = null) {
		$foo = $foo2 = false;
		return $this->_exists ($pKey, $foo2, $pLocale);
	}

	/**
	 * Retourne la clef avec la section ressource / clef
	 * 
	 * @param $pKey Clef à parser
	 * @return string
	 */
	private static function _parseKey ($pKey) {
		static $knownKeys = array ();
		if (isset ($knownKeys[$pKey])) {
			return $knownKeys[$pKey];
		}

		if (($posPipe = strpos ($pKey, '|')) !== false) {
			return $knownKeys[$pKey] = array ('bundle'=>substr ($pKey, 0, $posPipe), 'key'=>substr ($pKey, $posPipe +1));
		}elseif (($posColon = strpos ($pKey, ':')) !== false) {
			return $knownKeys[$pKey] = array ('bundle'=>substr ($pKey, 0, $posColon+1), 'key'=>substr ($pKey, $posColon +1));
		}

		return array ('bundle'=>CopixContext::get (), 'key'=>$pKey);
	}

	/**
	 * Récupération du bundle qui contient les traductions pour une locale donnée.
	 * 
	 * @param $pResource le nom de la ressource à charger
	 * @param $pLang     la langue
	 * @param $pCountry  le pays
	 * 
	 * @return CopixI18NBundle
	 */
	public function getBundle ($pResource, $pLang, $pCountry) {
		if (isset ($this->_bundles[$pResource][$pLang][$pCountry])) {
			return $this->_bundles[$pResource][$pLang][$pCountry];
		}

		return $this->_bundles[$pResource][$pLang][$pCountry] = new CopixI18NBundle ($pResource, $pLang, $pCountry);
	}
}