<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croes Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Exceptions potentiellement générées par les services
 * @package copix
 * @subpackage core
 */
class CopixSession {
	/**
	 * Demarrage de la session
	 * @param	string	$pId	l'identifiant de la session, 
	 *    utile si vous avez plusieurs copix sur un même serveur
	 * 	  et que vous ne souhaitez pas partager les sessions
	 */
	public static function start ($pId = null){
		if ($pId === null){
			$pId = CopixConfig::instance ()->sessionName;
		}
		session_start ($pId);
	}

	/**
	 * Destruction de la session
	 */
	public static function destroy (){
		session_destroy ();
	}
	
	/**
	 * Destruction de toutes les informations qui ont été rajoutées dans le namespace indiqué. 
	 * @param	string	$pNamespace le nom des éléments à supprimer.
	 * @return void
	 */
	public static function destroyNamespace ($pNamespace){
		$_SESSION['COPIX'][$pNamespace] = array ();
	} 
	
	/**
	 * Définition d'un élément dans la session
	 * @param	string	$pPath	le chemin ou sauvegarder l'élément
	 * @param	mixed	$pValue la valeur à placer dans la session
	 * @param	string	$pNamespace le nom du namespace dans lequel on va placer l'élément
	 */
	public static function set ($pPath, $pValue, $pNamespace = 'default'){
		if ($pNamespace === null){
			$pNamespace = 'default';
		}

		if ($pValue === null){
			unset ($_SESSION['COPIX'][$pNamespace][$pPath]);
		}else{
			if (is_object ($pValue) && !($pValue instanceof CopixSessionObject)){
				$pValue = new CopixSessionObject ($pValue);
			}
			$_SESSION['COPIX'][$pNamespace][$pPath] = $pValue;
		}
	}
	
	/**
	 * Destruction d'un élément en session
	 * @param	string	$pPath	le chemin ou sauvegarder l'élément
	 * @param	string	$pNamespace le nom du namespace dans lequel on va placer l'élément
	 */
	public static function delete ($pPath, $pNamespace = 'default') {
	    self::set ($pPath, null, $pNamespace);
	}
	
	/**
	 * Définition d'un élément objet dans la session, en en spécifiant le type
	 * @param	string	$pPath	le chemin ou sauvegarder l'élément
	 * @param	mixed	$pValue la valeur à placer dans la session
	 * @param	string	$pDef	le sélecteur qui permet de relire l'élément
	 * @param	string	$pNamespace le nom du namespace dans lequel on va placer l'élément
	 * @see CopixSessionObject
	 */
	public static function setObject ($pPath, $pValue, $pDef, $pNamespace = 'default'){
		self::set ($pPath, new CopixSessionObject ($pValue, $pDef), $pNamespace);		
	}
	
	/**
	 * Définition d'un élément dans la session
	 * @param	string	$pPath	le chemin ou sauvegarder l'élément
	 * @param	mixed	$pValue la valeur à placer dans la session
	 * @param	string	$pNamespace le nom du namespace dans lequel on va placer l'élément
	 */
	public static function push ($pPath, $pValue, $pNamespace = 'default'){
		if (!isset ($_SESSION['COPIX'][$pNamespace][$pPath])){
			$_SESSION['COPIX'][$pNamespace][$pPath] = array ();			
		}
		$_SESSION['COPIX'][$pNamespace][$pPath][] = $pValue;
	}

	/**
	 * Récupération d'un élément depuis la session
	 * @param	string	$pPath	le chemin ou l'élément à été sauvegardé
	 * @param	string	$pNamespace le nom du namespace dans lequel est l'élément
	 * @return  mixed la valeur de l'élément ou null 
	 */
	public static function &get ($pPath, $pNamespace = 'default'){
		$value = null;
		if (isset ($_SESSION['COPIX'][$pNamespace][$pPath])) {
			if($_SESSION['COPIX'][$pNamespace][$pPath] instanceof CopixSessionObject) {
				$value = $_SESSION['COPIX'][$pNamespace][$pPath]->getSessionObject ();
			} else {
				return $_SESSION['COPIX'][$pNamespace][$pPath];
			}
		}
		return $value;
	}
}


/**
 * Objet pouvant être mis en session
 * @package copix
 * @subpackage core
 */
class CopixSessionObject extends CopixSerializableObject  {
   	/**
   	 * Retourne l'objet directement
   	 * @return object
   	 */
   	public function getSessionObject (){
   		return $this->getRemoteObject ();
   	}
} 
?>