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
 * @package copix
 * @subpackage core
 */
class CopixRequestException extends CopixException {	
	/**
	* Les variables manquantes
	* @var array 
	*/ 
	private $_vars = array ();

	/**
	* Construction du message d'erreur
	* @param	array	$pMessage	Tableau des variables manquantes
	* @param	int		$pCode		Code de l'erreur
	*/
	public function __construct ($pMessage, $pCode=NULL){
   		$this->_vars = is_array ($pMessage) ? $pMessage : array ($pMessage); 
   		$i18n = (count ($this->_vars) <= 1) ? 'missingRequestVar' : 'missingRequestVars';
		parent::__construct (_i18n ('copix:copix.error.' . $i18n, implode (', ', $this->_vars)), $pCode);
	}
}

/**
 * Classe permettant de gérer la récupération des paramètres passés dans l'url
 * @package copix
 * @subpackage core
 */
 class CopixRequest {
 	/**
 	 * Les variables de l'application
 	 * @var array
 	 */
 	private static $_vars = false;
 	
	/**
 	 * S'assure que les variables données sont bien présentent dans l'url, génération d'une exception sinon
 	 * @throws CopixRequestException
 	 */
 	public static function assert (){
 		$missingKeys = array ();
 		$keys = array_keys (self::$_vars);
 		foreach (func_get_args() as $varName){
			if (!in_array ($varName, $keys)){
				$missingKeys[] = $varName;
			} 			
 		}

 		if (count ($missingKeys)){
 			throw new CopixRequestException ($missingKeys);
 		}
 	} 
 	
 	/**
 	 * Récupération d'une variable de la requête. Si la variable n'est pas présente, on retourne la valeur par défaut.
 	 * @param	string	$pVarName	le nom de la variable que l'on veut récupérer
 	 * @param 	mixed	$pDefaultValue	la valeur par défaut si rien n'est dans l'url
 	 * @param 	boolean	$pDefaultIdEmpty	demande de retourner la valeur par défaut si jamais le paramètre est vide (0, null, '')
 	 * @return 	mixed	valeur de la variable dans l'url
 	 */
 	public static function get ($pVarName, $pDefaultValue = null, $pDefaultIfEmpty = true){
		if (array_key_exists ($pVarName, self::$_vars)){
		    if (is_array(self::$_vars[$pVarName]) || is_object (self::$_vars[$pVarName]) || trim(self::$_vars[$pVarName])!==''){
		        return self::$_vars[$pVarName];		 
		    }else{
		        if (! $pDefaultIfEmpty){
		            return self::$_vars[$pVarName];  
		        }
		    }
		}
		return $pDefaultValue; 
 	}
 	
 	/**
 	 * Récupération d'un fichier
 	 * @param string $pVarName le nom de la variable du fichier
 	 * @param string $pPath chemin ou mettre le fichier
 	 * @param string $pFileName nom du fichier qui va être posé
 	 * @return mixed CopixUploadedFile
 	 */
 	public static function getFile ($pVarName, $pPath=null, $pFileName=null) {
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
 	 * @param	string	$pVarName	le nom de la variable que l'on veut récupérer
 	 * @param 	mixed	$pDefaultValue	la valeur par défaut si rien n'est dans l'url
 	 * @return	numeric
 	 */
 	public static function getNumeric ($pVarName, $pDefaultValue = null){
		if (($value = self::get ($pVarName, $pDefaultValue)) === $pDefaultValue){
 			//Si valeur par défaut, alors on retourne sans tester
 			return $value;
 		} 
 		return CopixFilter::getNumeric ($value);
 	}
 	
 	/**
 	 * Récupération d'une variable de la requête en vérifiant qu'elle appartient à une liste 
 	 *  de valeurs prédéfinies
 	 * 
 	 * @param 	string	$pVarName	variable à récupérer
 	 * @param 	array	$pArValues	liste des valeurs possibles
 	 * @param	mixed	$pDefaultValues	la valeur par défaut si jamais la valeur n'est pas dans le tableau ou n'est pas définie
 	 * @return mixed
 	 */
 	public static function getInArray ($pVarName, $pArValues = array (), $pDefaultValue = null){
 		$value = self::get ($pVarName, $pDefaultValue);
 		if (! in_array ($value, $pArValues)){
 			return $pDefaultValue;
 		}
 		return $value;
 	}
 	
 	/**
 	 * Récupération d'une variable de la requête sous la forme d'un entier
 	 * @param	string	$pVarName	le nom de la variable que l'on veut récupérer
 	 * @param 	mixed	$pDefaultValue	la valeur par défaut si rien n'est dans l'url
 	 * @return	int
 	 */
 	public static function getInt ($pVarName, $pDefaultValue = null){
		if (($value = self::get ($pVarName, $pDefaultValue)) === $pDefaultValue){
 			//Si valeur par défaut, alors on retourne sans tester
 			return $value;
 		}
  		return CopixFilter::getInt ($value);
 	}
 	
 	/**
 	 * Récupération d'une variable de la requête sous la forme de caractères alphabétiques uniquement
 	 * @param	string	$pVarName	le nom de la variable que l'on veut récupérer
 	 * @param 	mixed	$pDefaultValue	la valeur par défaut si rien n'est dans l'url
 	 * @return string
 	 */
 	public static function getAlpha ($pVarName, $pDefaultValue = null){
		if (($value = self::get ($pVarName, $pDefaultValue)) === $pDefaultValue){
 			//Si valeur par défaut, alors on retourne sans tester
 			return $value;
 		}
 		return CopixFilter::getAlpha ($value);
 	}
 	
 	/**
 	 * Récupération d'une variable de la requête sous la forme de caractères alphabétiques uniquement
 	 * @param	string	$pVarName	le nom de la variable que l'on veut récupérer
 	 * @param 	mixed	$pDefaultValue	la valeur par défaut si rien n'est dans l'url
 	 * @return  string
 	 */
 	public static function getAlphaNum ($pVarName, $pDefaultValue = null){
		if (($value = self::get ($pVarName, $pDefaultValue)) === $pDefaultValue){
 			//Si valeur par défaut, alors on retourne sans tester
 			return $value;
 		} 
 		return CopixFilter::getAlphaNum ($value);
 	}
 	
 	/**
 	 * Récupération d'un flottant dans l'url
 	 * @param	string	$pVarName	le nom de la variable que l'on veut récupérer
 	 * @param 	mixed	$pDefaultValue	la valeur par défaut si rien n'est dans l'url
 	 * @return float
 	 */
 	public static function getFloat ($pVarName, $pDefaultValue = null){
		if (($value = self::get ($pVarName, $pDefaultValue)) === $pDefaultValue){
 			//Si valeur par défaut, alors on retourne sans tester
 			return $value;
 		} 
 		return CopixFilter::getFloat ($value);
 	}

	/**
 	 * Définition d'une variable de requête
 	 * @param	string	$pVarName	Nom de la variable
 	 * @param 	mixed	$pValue		Valeur de la variable
 	 */
 	public static function set ($pVarName, $pValue){
 		self::$_vars[$pVarName] = $pValue;
 	}
 	
 	/**
 	 * Récupération des variables de la requête sous la forme d'un tableau
 	 * @return array
 	 */
 	public static function asArray (){
 		return self::$_vars;
 	}
 	
 	/**
 	 * Initialisation de la requête à partir d'un tableau de données
 	 * @param	array	$pArray	tableau des données pour l'url
 	 */
 	public static function setRequest ($pArray){
 		self::$_vars = $pArray;
 	}
 	
 	/**
 	 * Indique si la variable $pVarName à été donné dans le formulaire
 	 * @param 	string	$pVarName	le nom de la variable à tester
 	 * @return boolean
 	 */
 	public static function exists ($pVarName){
 		return array_key_exists ($pVarName, self::$_vars);
 	}
 }
?>