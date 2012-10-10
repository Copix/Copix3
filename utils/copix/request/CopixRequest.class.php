<?php
/**
 * @package copix
 * @subpackage core
 * @author Croës Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe permettant de gérer la récupération des paramètres passés (via POST ou GET)
 * 
 * @package copix
 * @subpackage core
 */
 class CopixRequest {
	/**
	 * Variables de l'application
	 * 
	 * @var array
	 */
	private static $_vars = array ();
	
	/**
	 * Indique à setRequest d'écraser tout le tableau des valeurs du request
	 */
	const REPLACE = 0;
	
	/**
	 * Indique a setRequest de rajouter les valeurs dans le tableau si elles n'existaient pas
	 */
	const ADD = 1;
	
	/**
	 * Indique a setRequest de rajouter les valeurs dans la tableau et d'ecraser avec sa valeur si elle existait deja dans le tableau
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
				if (self::getFile ($varName) === false){
					$missingKeys[] = $varName;
				}
			}
		}

		if (count ($missingKeys)) {
			throw new CopixRequestException ($missingKeys);
		}
	} 	 
 	
	/**
	 * Retourne un paramètre. Si il n'existe pas, la valeur par défaut sera retournée.
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
				if (!$pDefaultIfEmpty) {
					return self::$_vars[$pVarName];
				}
			}
		}
		return $pDefaultValue; 
	}

	/**
	 * Retourne un fichier uploadé, et permet de le déplacer via $pPath et $pFileName
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
	 * Retourne un paramètre sous forme numérique
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
	 * Retourne un paramètre en vérifiant qu'il appartient à une liste de valeurs prédéfinies, sinon, retourne la valeur par défaut
	 * 
	 * @param string $pVarName Variable à récupérer
	 * @param array	$pArValues Liste des valeurs possibles
	 * @param mixed	$pDefaultValues	Valeur par défaut si jamais la valeur n'est pas dans le tableau ou n'est pas définie
	 * @return mixed
	 */
	public static function getInArray ($pVarName, $pArValues = array (), $pDefaultValue = null) {
		$value = self::get ($pVarName, $pDefaultValue);
		if (!in_array ($value, $pArValues)) {
			return $pDefaultValue;
		}
		return $value;
	}

	/**
	 * Retourne un paramètre sous la forme d'un entier
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
	 * Retourne un paramètre sous la forme de caractères alphabétiques uniquement
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
	 * Retourne un paramètre sous la forme de caractères alphabétiques uniquement
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
	 * Retourne un paramètre sous la forme d'un flottant
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
	 * Retourne un paramètre sous la forme d'un boolean
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
	 * Définition d'un paramètre
	 * 
	 * @param string $pVarName Nom de la variable
	 * @param mixed $pValue Valeur de la variable
	 */
	public static function set ($pVarName, $pValue) {
		self::$_vars[$pVarName] = $pValue;
	}

	/**
	 * Récupération des paramètres sous la forme d'un tableau
	 * 
	 * Il est possible de passer a la fonction les noms de paramètres que l'on souhaite récupérer.
	 * Si rien n'est donné, toute la requête est passée dans la réponse
	 * Si une liste de chaine est donnée, seule les clefs existantes dans la requête sont passé
	 * Il est possible de passer un tableau pour chaque élément
	 *  * tableau associatif nomParametre=>valeur par défaut
	 *  * tableau indicé 0=>clef, 1=>valeur 
	 * 
	 * CopixRequest::get (array ('p1'=>'v1', 'p2'=>'v2'));
	 * @return array
	 */
	public static function asArray () {
		if (count ($args = func_get_args ()) === 0){
			return self::$_vars;
		}else{
			$toReturn = array ();
			foreach ($args as $name=>$value){
				if (is_array ($value)){
					if (count ($value) == 1){
						list ($key, $defaultValue) = each ($value);
					}else{
						$key = $value[0];
						$defaultValue = $value[1];
					}
					$toReturn[$key] = self::get ($key, $defaultValue);
				}else{
					if (array_key_exists ($value, self::$_vars)){
						$toReturn[$value] = self::$_vars[$value]; 
					}
				}
			}
		}
		return $toReturn;
	}

	/**
	 * Initialisation des paramètres à partir d'un tableau de données
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
	public static function isAJAX () {
		return CopixAjax::isAJAXRequest ();
	}
}