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
	public static function get ($pPath, $pNamespace = 'default'){
		if (isset ($_SESSION['COPIX'][$pNamespace][$pPath])){
			if ((is_object ($_SESSION['COPIX'][$pNamespace][$pPath]))  
			    && ($_SESSION['COPIX'][$pNamespace][$pPath] instanceof CopixSessionObject)) {
				return $_SESSION['COPIX'][$pNamespace][$pPath]->getSessionObject ();
			}else{
				return $_SESSION['COPIX'][$pNamespace][$pPath];
			}
		}
		return null;
	}
}

/**
 * Classe qui gère la possibilité de placer des objets en session sans se préocuper de 
 * la sérialisation / désérialisation.
 */
class CopixSessionObject extends CopixClassProxy {
	/**
	 * Constante pour indiquer que cet objet fait partit de l'autoload
	 */
	const AUTOLOADED = 0;
	
	/**
	 * Le nom du fichier qui contient la définition de l'objet
	 * @var string
	 */
	private $_fileName = null;
	
	/**
	 * Le type de l'élément (dao, class, object)
	 * @var string
	 */
	private $_type = null;
   	
	/**
   	 * Constructeur, l'objet et sa définition s'il y a lieu
   	 * @param	object	$pObject	l'objet à placer dans la session
   	 * @param	string	$pFileName	le chemin de la définition du fichier
   	 */
	public function __construct ($pObject, $pFileName = null){
		parent::__construct ($pObject);
	
		//Filename === null ? on essaye de déterminer automatiquement le type de l'élément.
		if ($pObject instanceof ICopixDAO){
			$this->_type = 'dao';
			if ($pFileName === null){
				$this->_fileName = $pObject->getDAOId ();
			}else{
				$this->_fileName = $pFileName;
			}
		}elseif ($pObject instanceof ICopixDAORecord){
			$this->_type = 'dao';
			if ($pFileName === null){
				$this->_fileName = $pObject->getDAOId ();
			}else{
				$this->_fileName = $pFileName;
			}
		}elseif ($pObject instanceof StdClass){
			$this->_type = 'stdclass';
		}elseif (($pFileName !== null) && (is_readable ($pFileName))){
			//On a spécifié un $pFileName et il est lisible, c'est donc un objet
			//libre
			$this->_type = 'object';
			$this->_fileName = $pFileName;
		}elseif (stripos (get_class ($pObject), "copix") === 0){
			$this->_type = 'autoload';
		}else{
			//soit on a spécifié un chemin et il n'est pas lisible, soit on a rien 
			//spécifié auquel cas on va considérer que la classe est dans un module normal de copix
			$this->_type = 'class';
			if (($this->_fileName = $pFileName) === null){
				$this->_fileName = CopixContext::get ().'|'.get_class ($pObject);
			}
		}			
   	}

   	/**
   	 * Retourne l'objet directement
   	 * @return object
   	 */
   	public function getSessionObject (){
   		return $this->getRemoteObject ();
   	}
   	
   	/**
   	 * Avant la sérialisation (qui va se produire pour la session)
   	 * @return array	liste des propriétés à récupérer
   	 */
   	public function __sleep (){
   		if ($this->_type != 'autoload'){
   			$this->_object = serialize ($this->_object);
   		}
   		return array ('_object', '_fileName', '_type');
   	}
   	
   	/**
   	 * Après la sérialisation, pour une récupération correcte de l'objet
   	 */
   	public function __wakeup (){
   		switch ($this->_type){
   			case 'dao':
   				_daoInclude ($this->_fileName);
   				break;
   			case 'object':
   				Copix::RequireOnce ($this->_fileName);
   				break;
   			case 'class':
   				_classInclude ($this->_fileName);
   				break;
   			case 'stdclass':
   				break;
   		}
   		if ($this->_type != 'autoload'){
   			$this->_object = unserialize ($this->_object);
   		}
   	}
}
?>