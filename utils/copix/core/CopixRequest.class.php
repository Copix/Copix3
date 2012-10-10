<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base pour les exceptions sur la requête
 * 
 * @package		copix
 * @subpackage	core
 */
class CopixRequestException extends CopixException {	
	/**
	 * Les variables manquantes
	 * 
	 * @var array 
	 */ 
	private $_vars = array ();

	/**
	 * Construction du message d'erreur
	 * 
	 * @param array $pMessage Tableau des variables manquantes
	 * @param int $pCode Code de l'erreur
	 */
	public function __construct ($pMessage, $pCode = null) {
		$this->_vars = is_array ($pMessage) ? $pMessage : array ($pMessage); 
		parent::__construct (_i18n ('copix:copix.error.missingRequestVar', implode (', ', $this->_vars)), $pCode);
	}

	/**
	 * Indique quelles sont les variables manquantes dans la requête
	 *
	 * @return array
	 */
	public function getMissing () {
		return $this->_vars;
	}
}

/**
 * Classe permettant de gérer la récupération des paramètres passés dans l'url
 * 
 * @package		copix
 * @subpackage	core
 */
 class CopixRequest {
	/**
	 * Les variables de l'application
	 * 
	 * @var array
	 */
	static private $_vars = array ();
	
	/**
	 * Constante disant a setRequest d'écraser tout le tableau des valeurs du request
	 *
	 */
	const REPLACE     = 0;
	
	/**
	 * Constante disant a setRequest de rajouter les valeurs dans la tableau du
	 * si elles n'existaient pas
	 */
	const ADD         = 1;
	
	/**
	 * Constante disant a setRequest de rajouter les valeurs dans la tableau et 
	 * d'ecraser avec sa valeur si elle existait deja dans le tableau
	 */
	const ADD_REPLACE = 2;
	 
	/**
	 * S'assure que les variables données sont bien présentent dans l'url, génération d'une exception sinon
	 * 
	 * @throws CopixRequestException
	 */
	public static function assert () {
		$missingKeys = array ();
		$keys = array_keys (self::$_vars);
		foreach (func_get_args () as $varName) {
			if (!in_array ($varName, $keys)) {
				$missingKeys[] = $varName;
			}
		}

		if (count ($missingKeys)) {
			throw new CopixRequestException ($missingKeys);
		}
	} 	 
 	
	/**
	 * Récupération d'une variable de la requête. Si la variable n'est pas présente, on retourne la valeur par défaut.
	 * 
	 * @param string $pVarName Nom de la variable que l'on veut récupérer
	 * @param mixed	$pDefaultValue Caleur par défaut si rien n'est dans l'url
	 * @param boolean $pDefaultIdEmpty Demande de retourner la valeur par défaut si jamais le paramètre est vide (0, null, '')
	 * @return mixed Valeur de la variable dans l'url
	 */
	public static function get ($pVarName, $pDefaultValue = null, $pDefaultIfEmpty = true) {
		if (array_key_exists ($pVarName, self::$_vars)) {
			if (is_array (self::$_vars[$pVarName]) || is_object (self::$_vars[$pVarName]) || trim (self::$_vars[$pVarName])!=='') {
				return self::$_vars[$pVarName];		 
			} else {
				if (!$pDefaultIfEmpty){
					return self::$_vars[$pVarName];
				}
			}
		}
		return $pDefaultValue; 
	}

	/**
	 * Récupération d'un fichier
	 * 			
	 * @param string $pVarName Nom de la variable du fichier
	 * @param string $pPath Chemin ou mettre le fichier
	 * @param string $pFileName Nom du fichier qui va être posé
	 * @return mixed CopixUploadedFile
	 */
	public static function getFile ($pVarName, $pPath = null, $pFileName = null) {
		$file = CopixUploadedFile::get ($pVarName);
		if ($pPath !== null) {
			if ($file !== false) {
				$file->move ($pPath, $pFileName);
			}
		}
		return $file;
	}

	/**
	 * Récupération d'une variable de la requête sous forme numérique
	 * 
	 * @param string $pVarName Nom de la variable que l'on veut récupérer
	 * @param mixed	$pDefaultValue Valeur par défaut si rien n'est dans l'url
	 * @return numeric
	 */
	public static function getNumeric ($pVarName, $pDefaultValue = null) {
		if (($value = self::get ($pVarName, $pDefaultValue)) === $pDefaultValue) {
			// Si valeur par défaut, alors on retourne sans tester
			return $value;
		}
		return CopixFilter::getNumeric ($value);
	}

	/**
	 * Récupération d'une variable de la requête en vérifiant qu'elle appartient à une liste de valeurs prédéfinies
	 * 
	 * @param string $pVarName Variable à récupérer
	 * @param array	$pArValues Liste des valeurs possibles
	 * @param mixed	$pDefaultValues	Valeur par défaut si jamais la valeur n'est pas dans le tableau ou n'est pas définie
	 * @return mixed
	 */
	public static function getInArray ($pVarName, $pArValues = array (), $pDefaultValue = null) {
		$value = self::get ($pVarName, $pDefaultValue);
		if (! in_array ($value, $pArValues)) {
			return $pDefaultValue;
		}
		return $value;
	}

	/**
	 * Récupération d'une variable de la requête sous la forme d'un entier
	 * 
	 * @param string $pVarName Noom de la variable que l'on veut récupérer
	 * @param mixed	$pDefaultValue Valeur par défaut si rien n'est dans l'url
	 * @return int
	 */
	public static function getInt ($pVarName, $pDefaultValue = null) {
		if (($value = self::get ($pVarName, $pDefaultValue)) === $pDefaultValue) {
			// Si valeur par défaut, alors on retourne sans tester
			return $value;
		}
		return CopixFilter::getInt ($value);
	}

	/**
	 * Récupération d'une variable de la requête sous la forme de caractères alphabétiques uniquement
	 * 
	 * @param string $pVarName Nom de la variable que l'on veut récupérer
	 * @param mixed	$pDefaultValue Valeur par défaut si rien n'est dans l'url
	 * @return string
	 */
	public static function getAlpha ($pVarName, $pDefaultValue = null) {
		if (($value = self::get ($pVarName, $pDefaultValue)) === $pDefaultValue) {
			// Si valeur par défaut, alors on retourne sans tester
			return $value;
		}
		return CopixFilter::getAlpha ($value);
	}

	/**
	 * Récupération d'une variable de la requête sous la forme de caractères alphabétiques uniquement
	 * 
	 * @param string $pVarName Nom de la variable que l'on veut récupérer
	 * @param mixed	$pDefaultValue Valeur par défaut si rien n'est dans l'url
	 * @return string
	 */
	public static function getAlphaNum ($pVarName, $pDefaultValue = null) {
		if (($value = self::get ($pVarName, $pDefaultValue)) === $pDefaultValue) {
			//Si valeur par défaut, alors on retourne sans tester
			return $value;
		}
		return CopixFilter::getAlphaNum ($value);
	}
 	
	/**
	 * Récupération d'un flottant dans l'url
	 * 
	 * @param string $pVarName Nom de la variable que l'on veut récupérer
	 * @param mixed	$pDefaultValue Valeur par défaut si rien n'est dans l'url
	 * @return float
	 */
	public static function getFloat ($pVarName, $pDefaultValue = null) {
		if (($value = self::get ($pVarName, $pDefaultValue)) === $pDefaultValue) {
			// Si valeur par défaut, alors on retourne sans tester
			return $value;
		}
		return CopixFilter::getFloat ($value);
	}
	
	/**
	 * Récupération d'un boolean dans l'url
	 * 
	 * @param string $pVarName Nom de la variable que l'on veut récupérer
	 * @param mixed	$pDefaultValue Valeur par défaut si rien n'est dans l'url
	 * @return boolean
	 */
	public static function getBoolean ($pVarName, $pDefaultValue = null) {
		if (($value = self::get ($pVarName, $pDefaultValue)) === $pDefaultValue) {
			// Si valeur par défaut, alors on retourne sans tester
			return $value;
		}
		return CopixFilter::getBoolean ($value);
	}

	/**
	 * Définition d'une variable de requête
	 * 
	 * @param string $pVarName	Nom de la variable
	 * @param mixed $pValue Valeur de la variable
	 */
	public static function set ($pVarName, $pValue) {
		self::$_vars[$pVarName] = $pValue;
	}

	/**
	 * Récupération des variables de la requête sous la forme d'un tableau
	 * 
	 * @return array
	 */
	public static function asArray () {
		return self::$_vars;
	}

	/**
	 * Initialisation de la requête à partir d'un tableau de données
	 * 
	 * @param array $pArray Tableau des données pour l'url
	 * @param array $pReplace Constant pour savoir si les données doivent écrasé les anciennes
	 * 
	 */
	public static function setRequest ($pArray, $pReplace = CopixRequest::REPLACE) {
		switch ($pReplace) {
			case CopixRequest::REPLACE:
				self::$_vars = $pArray;
				break;
			case CopixRequest::ADD:
				self::$_vars = array_merge ($pArray, self::$_vars);
				break;
			case CopixRequest::ADD_REPLACE:
				self::$_vars = array_merge (self::$_vars, $pArray);
				break;
		}
	}
	
	/**
	 * Indique si la variable $pVarName à été donnée dans le formulaire
	 * 
	 * @param string $pVarName Nom de la variable à tester
	 * @return boolean
	 */
	public static function exists ($pVarName) {
		return array_key_exists ($pVarName, self::$_vars);
	}
	
	/**
	* Alias à CopixAjax::isAjaxRequest
	*
	* @see CopixAjax::isAjaxRequest
	*/
	static public function isAJAX () {
		return CopixAjax::isAJAXRequest ();
	}
}
?>