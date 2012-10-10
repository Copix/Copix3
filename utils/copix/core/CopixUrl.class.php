<?php
/**
* @package		copix
* @subpackage	core
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Interface de base pour les classes qui prennent en charge les URL.
 * @package copix
 * @subpakage core
 */
interface ICopixUrlHandler {
	/**
	 * Analyse une URL et la transforme en un tableau de paramètres.
	 * @param	array	$pUrl	tableau qui reçoit en paramètre les éléments "chemin" de l'url
	 * @param	string	$pMode	le mode de gestion de l'url
	 * @return false si ne gère pas l'url, array si l'url à été gérée.
	 */
	public function parse ($pUrl, $pMode);

	/**
	 * Construction d'une URL.
	 * @param	array	$pDest		destination (trigramme copx) qui décrit l'action
	 * @param	array	$pParams	liste des paramètres supplémentaires donnés pour l'url
	 * @param 	string	$pMode		Le mode de gestion des URL actuel
	 * @return CopixUrlHandlerGetResponse, false si pas pris en charge
	 */
	public function get   ($pDest, $pParams, $pMode);
}

/**
 * Classe de base pour les handler d'url
 * @package copix
 * @subpackage core	
 */
abstract class CopixUrlHandler implements ICopixUrlHandler {
	/**
	 * Construction d'une URL.
	 * @param	array	$pDest		destination (trigramme copx) qui décrit l'action
	 * @param	array	$pParams	liste des paramètres supplémentaires donnés pour l'url
	 * @param 	string	$pMode		Le mode de gestion des URL actuel
	 * @return CopixUrlHandlerGetResponse, false si pas pris en charge
	 */
	public function get ($pDest, $pParams, $pMode){
		return false;
	}

	/**
	 * Analyse une URL et la transforme en un tableau de paramètres.
	 * @param	array	$pUrl	tableau qui reçoit en paramètre les éléments "chemin" de l'url
	 * @param	string	$pMode	le mode de gestion de l'url
	 * @return false si ne gère pas l'url, array si l'url à été gérée.
	 */
	public function parse ($pUrl, $pMode){
		return false;
	}
}

/**
 * Réponse apportée par un handler sur une demande de type get
 * @package copix
 * @subpackage core	
 */
class CopixUrlHandlerGetResponse {
	public $path;
	public $vars;
	public $scriptName;
	public $basePath;
	public $protocol;
	public $externUrl;

	/**
	 * Construction de la réponse avec les valeurs par défaut de l'url courante
	 */
	function __construct (){
		$this->vars = null;
		$this->path = null;
		$this->scriptName = CopixUrl::getRequestedScriptName ();
		$this->basePath = CopixUrl::getRequestedBasePath ();
		$this->protocol = CopixUrl::getRequestedProtocol ();
		$this->externUrl = null;
	}
}

/**
 * Interface de base pour surcharger les modes de gestion d'url (pour créer un mode au même
 *  titre que prepend ou none)
 * @todo implémenter
 */
interface ICopixUrlEngine {}

/**
* Classe permettant de récupérer / générer des URL
*
* http://monsite.com/chemin/index.php/sous/che/min?param1=valeur1&param2=valeur2
* scriptname = /chemin/index.php
* pathinfo = /sous/che/min
* params = array('param1'=>'valeur1', 'param2'=>'valeur2');
* 
* @package		copix
* @subpackage	core
*/
class CopixUrl {
	/**
	 * le protocole utilisé (false tant que pas initialisé) 
	 * @var string
	 */
	private static $_protocol = false;
	
	/**
	 * Le chemin du script courant
	 * @var string
	 */
	private static $_scriptPath = false;
	
	/**
	 * le nom du script courant
	 * @var string
	 */
	private static $_scriptName = false;

    /**
     * Chemin de base du script (sans le protocole)
     * @var string
     */
     private static $_basePath = false;
     
     /**
      * Chemin allant jusqu'au script (avec le protocole)
      * @var string
      */
      private static $_baseUrl = false;
     
     /**
      * Le pathinfo 
      * @var string
      */	
     private static $_pathinfo = false;
      
     /**
      * Url demandée (stockée pour XML et aussi pour utilisation directe)
      * @var array  
      */
	 private static $_url = array ();
	 
	 /**
	  * Les handlers déjà chargés
	  * @var array
	  */
	 private static $_handlers = array (); 
	 
	/**
	* Retourne le script demandé, sans les informations infopath ou paramètres de la requête
	* @return string
	* <code>
	*   //Si appelé depuis http://mysite.com/subdir/index.php/mypath/myaction?myparams=myvalues
	*   echo CopixUrl::getRequestedScript ();
	*   //affiche "/subdir/index.php"
	* </code>
	*/
	public static function getRequestedScript (){
		return $_SERVER[CopixConfig::instance ()->url_requestedscript_variable];
	}

	/**
	* Retourne le chemin du script demandé.
	* <code>
	*    //Si appelé avec http://mysite.com/subdir/index.php/mypath/myaction?myparams=myvalues
	*    echo CopixUrl::getRequestedScriptPath ();
	*    //affiche "/subdir/"
	* </code>
	* @return string
    */
	public static function getRequestedScriptPath (){
		if (self::$_scriptPath === false){
			self::$_scriptPath = substr (self::getRequestedScript (), 0, strrpos (self::getRequestedScript (), '/')).'/';
		}
		return self::$_scriptPath;
	}

	/**
	* Retourne le nom du script demandé
	* <code>
	*   //Si appelé avec http://mysite.com/subdir/index.php/mypath/myaction?myparams=myvalues
	*   echo CopixUrl::getRequestedScriptName ();
	*   //affiche "index.php"
	* </code>
	* @return string
	*/
	public static function getRequestedScriptName (){
		if (self::$_scriptName === false){
			self::$_scriptName = substr (self::getRequestedScript (), strrpos (self::getRequestedScript (), '/')+1);
		}
		return self::$_scriptName;
	}

	/**
    * Récupère le nom de domaine du script demandé.
	* <code>
	*   //si appelé avec http://mysite.com/subdir/index.php/mypath/myaction?myparams=myvalues
	*   echo CopixUrl::getRequestedDomain ();
	*   //affiche mysite.com
	* </code>    
    * @return string
	*/
	public static function getRequestedDomain (){
		return $_SERVER ['HTTP_HOST'];
	}

	/**
    * Récupère le début de l'url
 	* <code>
	*    //Si appelé avec http://mysite.com/subdir/index.php/mypath/myaction?myparams=myvalues
	*    echo CopixUrl::getRequestedScriptPath ();
	*    //affiche "mysite.com/subdir/"
	* </code>
    * @return string
    */
	public static function getRequestedBasePath (){
		if (self::$_basePath === false){
			self::$_basePath = self::getRequestedDomain ().self::getRequestedScriptPath ();
		}
		return self::$_basePath;
	}
	
	/**
	* Récupère le chemin allant jusqu'au script exclus en incluant le protocole utilisé.
 	* <code>
	*    //Si appelé avec http://mysite.com/subdir/index.php/mypath/myaction?myparams=myvalues
	*    echo CopixUrl::getRequestedScriptPath ();
	*    //affiche "http://mysite.com/subdir/"
	* </code>
	* @return string
	*/
	public static function getRequestedBaseUrl (){
		if (self::$_baseUrl === false){
			self::$_baseUrl = self::getRequestedProtocol ().self::getRequestedBasePath ();
		}
		return self::$_baseUrl;
	}
	
	/**
	 * Récupération du protocole (http/https pour le moment)
	 * <code>
	 *    //Si appelé avec http://www.copix.org
	 *    echo CopixUrl::getRequestedProtocol ();
	 *    //affiche "http://"
	 * </code>
	 * @return string
	 */
	public static function getRequestedProtocol (){
		if (self::$_protocol !== false){
			return self::$_protocol;
		}
		
		if (isset ($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
			return self::$_protocol = 'https://';
		}
		return self::$_protocol = 'http://';
	}

	/**
    * Récupère le pathinfo
    * <code>
    *  //Si appelé via http://localhost/copix_3/test.php/stuff/stuff2/stuff3?test=simpletest|
    *  echo CopixUrl::getRequestedPathInfo ();
    *  //affiche "/stuff/stuff2/stuff3" 
    * </code>
    * @return string
    */
	public static function getRequestedPathInfo (){
		if (self::$_pathinfo === false){
			//following is index.php/mypath/myaction
			if (isset ($_SERVER['PATH_INFO'])){
				self::$_pathinfo = isset ($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : $_SERVER['PATH_INFO'];
				$pos      = strpos (self::$_pathinfo, self::getRequestedScriptPath ().self::getRequestedScriptName ());
				if ($pos !== false){
					//under IIS, we may get as PATH_INFO /subdir/index.php/mypath/myaction (wich is incorrect)
					self::$_pathinfo = substr (self::$_pathinfo, strlen (self::getRequestedScriptPath ().self::getRequestedScriptName ()));
				}
			}else{
				self::$_pathinfo = substr($_SERVER["PHP_SELF"], strlen($_SERVER['SCRIPT_NAME']));
			}
		}
		return self::$_pathinfo;
	}

	/**
    * Récupère l'url demandée
    * @param	boolean		$pForXML si l'on génère en XML ou non (&amp; au lieu de &)
    * @return	string		l'url demandée
    * @deprecated
    * @see CopixUrl::getRequestedUrl ();
	*/
	public static function getCurrentUrl ($pForXML = false) {
		return self::getRequestedUrl ($pForXML);
	}

	/**
    * Récupère l'url demandée
    * @param boolean $pForXML si l'on génère en XML ou non (&amp; au lieu de &)
    * @return string l'url demandée
    */
	public static function getRequestedUrl ($pForXML = false){
		if (!isset (self::$_url[$pForXML])){
			$currentUrl = self::valueToUrl (null, $_GET, false, $pForXML);
/*
			foreach ($_GET as $key=>$elem){
				$currentUrl .= ($currentUrl !== ''? ($pForXML ? '&amp;' : '&') : '').$key.'='.urlencode ($elem);
			}
*/
			if ($currentUrl !== ''){
			   $currentUrl = self::getRequestedBaseUrl ().self::getRequestedScriptName ().self::getRequestedPathInfo ().'?'.$currentUrl;
			}else{
               $currentUrl = self::getRequestedBaseUrl ().self::getRequestedScriptName ().self::getRequestedPathInfo ();
			}
			self::$_url[$pForXML] = $currentUrl;
		}
		return self::$_url[$pForXML];
	}
	
	/**
	* Convertit une valeur en sa représentation dans une URL.
	* 
	* Si $pName est null, alors $pValue doit être un tableau associatif avec $pName=>$pValue
	* 
	* @param	string	$pName	le nom de la variable à convertir
	* @param	string/int/array	$pValue	La valeur de la variable $pName
	* @param	boolean	$start
	* @param	boolean	$pForXml	Si l'on doit générer l'url pour du XML ou non
	*/
	public static function valueToUrl ($pName, $pValue, $pStartWithAmp = false, $pForXml = false){
	    $amp = $pForXml ? '&amp;' : '&';
	    $show = $pStartWithAmp;
	    if ($pName !== null){
			if (is_array ($pValue) && count ($pValue)){
				$toReturn = '';
				foreach ($pValue as $key=>$itemValue){
					$toReturn .= self::valueToUrl ($pName.'['.$key.']', $itemValue, $show, $pForXml);
					$show = true;
				}
				return $toReturn;
			}else{
				return ($pStartWithAmp ? $amp : '').$pName.'='.urlencode ($pValue);
			}
	    }else{
	    	if (is_array ($pValue) && count ($pValue)){
	    		$toReturn = array ();
		    	foreach ($pValue as $key=>$itemValue){
		    		$toReturn[] = self::valueToUrl ($key, $itemValue, false, $pForXml);  
		    	}
		    	return ($pStartWithAmp ? $amp : '').implode ($amp, $toReturn);
	    	}
	    }
	    return '';	
	}

	/**
    * Ajoute des paramètres à l'url donnée
    * 
    * Si jamais l'url donnée contient déja les paramètres que l'on a demandé de rajouter, 
    *   les paramètres donnés remplaçeront les exisant
    * <code>
    *    echo CopixUrl::appendToUrl ('http://www.copix.org/index.php?module=test', array ('group'=>'value'));
    *    //affiche http://www.copix.org/index.php?module=test&group=value
    *    echo CopixUrl::appendToUrl ('http://www.copix.org/index.php?group=test', array ('group'=>'value'));
    *    //affiche http://www.copix.org/index.php?group=value
    * </code>
    *  
    * @param	string	$pUrl		l'url à qui on veut ajouter des paramètres
    * @param	array	$pParams	les paramètres à ajouter à l'url
    * @param	boolean	$pForXML	si l'on ajoute à une URL destinée XML ou non
    * @return	string	l'url finale
    */
	public static function appendToUrl ($pUrl, $pParams = array (), $pForXML = false){
		$pParams = (array) $pParams;
		if (count ($pParams) === 0){
			return $pUrl;
		}

		$pUrl = self::removeParams ($pUrl, array_keys ($pParams), $pForXML);
		if ((($pos = strpos ($pUrl, '?')) !== false) && ($pos !== (strlen ($pUrl)-1))){
			return $pUrl . self::valueToUrl (null, $pParams, true, $pForXML);
		}else{
			return $pUrl . '?'. self::valueToUrl (null, $pParams, false, $pForXML);
		}
	}

	/**
    * Analyse de la requête donnée et retourne la liste des paramètres envoyés au script
    * 
    * @param	string	$pUrl	l'url à analyser
    * @param	boolean $pFromString	Indique si la requête est arrivée uniquement sous la forme de chaine (on utilisera pas les variables d'environnement pour l'analyser (_GET, _POST)
    * @param 	boolean	$pFromXml		Indique si la requête donnée est au format XML (&amp; au lieu de &)
    * @return	array	tableau des paramètres envoyés au script 
    */
	public static function parse ($pUrl, $pFromString = false, $pFromXML = false) {
		//deleting parameters we don't need. We could avoid this.....
		$vars = array ();

        $pFullUrl = $pUrl;
		if (($pos = strpos ($pUrl, '?')) !== false){
			$pUrl  = substr ($pUrl, 0, $pos);
		}
		
		// si la chaine contient le SCRIPT_NAME, on doit l'enlever
		$posScriptName = strpos ($pUrl, $_SERVER['SCRIPT_NAME']);
		if ($posScriptName !== false) {
			$pUrl = substr ($pUrl, $posScriptName + strlen ($_SERVER['SCRIPT_NAME']));		
		}

		$config = CopixConfig::instance ();
		switch ($config->significant_url_mode){
			case 'default':
				$vars = array ();
				break;

			case 'prependIIS':
				if (isset ($_GET[$config->significant_url_prependIIS_path_key])){
					$pUrl = $_GET[$config->significant_url_prependIIS_path_key];
					$pUrl = $config->stripslashes_prependIIS_path_key === true ? stripslashes($pUrl) : $pUrl;
				}

			case 'prepend':
				$vars = self::_parsePrepend ($pUrl);
				break;

			default:
				throw new CopixException ('Unknown significant url handler in $config->significant_url_mode '.$config->significant_url_mode);
		}
		
		if ($pFromString){
			//Demande effectuée depuis une chaine de caractère, on analyse la partie "requête"
			return array_merge (self::extractParams ($pFullUrl, $pFromXML), $vars);
		}else{
			//Demande effectuée pour l'url courante, on utilise request pour les paramètres supplémentaires
		    return array_merge ($_REQUEST, $vars);
		}
	}

	/**
    * Analyse l'url $pUrl et retourne un tableau associatif avec les paramètres trouvés. 
    *  index.php/modulename/group/action as a default
    * 
    * @param string $pUrl l'url à analyser
    * @return array les paramètres de l'url
    */
	private static function _parsePrepend ($pUrl){
		//We don't want the first slash in the string
		if (strpos ($pUrl, '/') === 0){
			$pUrl = substr ($pUrl, 1);
		}

		//We unescape spaces (we replaced spaces with - and - with -- before)
		//We only unescape the path part of the url, not the parameters
		$pUrl = strtr ($pUrl, array ('--'=>'-', '-'=>' '));

		//exploding the url with slashes
		$urlX = explode ('/', $pUrl);
		if (((($countUrl = count ($urlX)) === 1) && ($urlX[0] === ''))){
			//no parameter
			return array ();
		}

		$module = $urlX[0];

		//On essaye ensuite de transmettre l'url au module dont le nom commence par la première occurence du chemin
		if (($significantUrlHandler = self::_createModuleHandler ($module)) !== null){
			if (($vars = $significantUrlHandler->parse ($urlX, 'prepend')) !== false) {
				return $vars;
			}
		}

		//Cela n'a rien donné, on va tenter l'expérience avec tous les autres modules
		foreach (CopixModule::getList () as $moduleKey=>$moduleName){
			if (($significantUrlHandler = self::_createModuleHandler ($moduleName)) !== null){
				if (($vars = $significantUrlHandler->parse ($urlX, 'prepend')) !== false) {
					return $vars;
				}
			}
		}
		
		//Aucun handler trouvé
		if ($countUrl >= 2){
			$group = $urlX[1];
		}else{
			$group = 'default';
		}

		if ($countUrl >= 3){
			$action = $urlX[2];
		}else{
			$action = 'default';
		}
		
		return array ('module'=>$module, 'group'=>$group, 'action'=>$action);
	}

	/**
    * Gets the url string from parameters
    * @param string  $pDest the module|dest|action string
    * @param array   $pParams an associative array with the parameters
    * @param boolean $pForXML the string has to be for html
    * @return string the url
    */
	public static function get ($pDest = null, $pParams = array (), $pForXML = false) {
		$pParams = (array) $pParams;

		//On demande l'url courante ?
		if ($pDest === "#"){
			return self::appendToUrl (self::getRequestedUrl ($pForXML), $pParams, $pForXML);
		}
		
		//On supporte les urls de type http:// ou autre
		if (strpos ($pDest, 'http://') === 0 ||
		    strpos ($pDest, 'https://') === 0 ||
		    strpos ($pDest, 'ftp://') === 0 ||
		    strpos ($pDest, 'ftps://')
		    ){
		    return self::appendToUrl ($pDest, $pParams, $pForXML);
		}

		if ($pDest === null){
			return self::getRequestedBaseUrl ();
		}

		switch (CopixConfig::instance ()->significant_url_mode){
			case 'default':
			return self::_getDefault ($pDest, $pParams, $pForXML);

			case 'prependIIS':
			case 'prepend':
			return self::_getPrepend ($pDest, $pParams, $pForXML);

			default:
				throw new CopixException ('Unknown significant url handler in $config->significant_url_mode '.CopixConfig::instance ()->significant_url_mode);
		}
	}

	/**
    * Retourne l'url classique (index.php?module=stuff&action=stuff...)
    * 
    * @param string  $pDest the module|dest|action string
    * @param array   $pParams an associative array with the parameters
    * @param boolean $pForXml the string has to be for html
    * @return string
    */
	private static function _getDefault ($pDest, $pParams = array (), $pForXML = false){
		$urlObject = false;
		$dest = self::_getDest ($pDest);

		if (($significantUrlHandler = self::_createModuleHandler ($dest['module'])) !== null){
			$urlObject = $significantUrlHandler->get ($dest, $pParams, 'default');
		}

		if ($urlObject === false){
			$urlObject = new CopixUrlHandlerGetResponse ();
			//Le handler ne prend pas la fonctionnalité en charge
			$urlObject->path = $dest;
			$urlObject->vars = $pParams;
			$urlObject->scriptName = self::getRequestedScriptName () ;
			$urlObject->basePath   = self::getRequestedBasePath ();
			$urlObject->protocol   = self::getRequestedProtocol ();
		}else{
			//Le handler à pris la fonctionnalité en charge, on se contente de vérifier sa sortie et de la formatter en conséquence.
			if (!isset ($urlObject->vars)){
				$urlObject->vars = $pParams;
			}
			if (!isset ($urlObject->path)){
				$urlObject->path = $dest;
			}
			if (!isset ($urlObject->scriptName)){
				$urlObject->scriptName = self::getRequestedScriptName ();
			}
			if (!isset ($urlObject->basePath)){
				$urlObject->basePath   = self::getRequestedBasePath ();
			}
			if (!isset ($urlObject->protocol)){
				$urlObject->protocol   = self::getRequestedProtocol ();
			}
			if (isset ($urlObject->externUrl) && strlen ($urlObject->externUrl) > 0){
				return $urlObject->externUrl;
	        }			
		}

		foreach ($urlObject->path as $key=>$value){
			$urlObject->path[$key] = urlencode (strtr ($value, array ('-'=>'--', ' ' =>'-')));
		}
		
		//$toReturn = $urlObject->protocol.$urlObject->basePath.$urlObject->scriptName.'?'.implode ('/', $urlObject->path);
		$toReturn = $urlObject->protocol.$urlObject->basePath.$urlObject->scriptName.'?'.self::valueToUrl (null, $urlObject->path, false, $pForXML);
		if (count($urlObject->vars) > 0){
			$toReturn .= self::valueToUrl (null, $urlObject->vars, true, $pForXML);
		}
		return $toReturn;
	}

	/**
    * Retourne l'url en mode prepend (/index.php/someStuff/somePath/someSubPath/)
    *
    * @param string  $pDest the module|dest|action string
    * @param array   $pParams an associative array with the parameters
    * @param boolean $pForXML the string has to be for html
    * @return string the prepended url
    */
	private static function _getPrepend ($pDest, $pParams = array (), $pForXML = false){
		$urlObject = false;
		$dest = self::_getDest ($pDest);

		if (($significantUrlHandler = self::_createModuleHandler ($dest['module'])) !== null){
			$urlObject = $significantUrlHandler->get ($dest, $pParams, 'prepend');
		}

		if ($urlObject === false){
			//Le handler ne prend pas la fonctionnalité en charge
			$urlObject = new CopixUrlHandlerGetResponse ();
			$urlObject->path = $dest;
			$urlObject->vars = $pParams;
			$urlObject->scriptName = self::getRequestedScriptName () ;
			$urlObject->basePath   = self::getRequestedBasePath ();
			$urlObject->protocol   = self::getRequestedProtocol ();
		}else{
			//Le handler à pris la fonctionnalité en charge, on se contente de vérifier sa sortie et de la formatter en conséquence.
			if (!isset ($urlObject->vars)){
				$urlObject->vars = $pParams;
			}
			if (!isset ($urlObject->path)){
				$urlObject->path = $dest;
			}
			if (!isset ($urlObject->scriptName)){
				$urlObject->scriptName = self::getRequestedScriptName ();
			}
			if (!isset ($urlObject->basePath)){
				$urlObject->basePath   = self::getRequestedBasePath ();
			}
			if (!isset ($urlObject->protocol)){
				$urlObject->protocol   = self::getRequestedProtocol ();
			}
			if (isset ($urlObject->externUrl) && strlen ($urlObject->externUrl) > 0){
				return $urlObject->externUrl;
	        }			
		}

		foreach ($urlObject->path as $key=>$value){
			$urlObject->path[$key] = urlencode (strtr ($value, array ('-'=>'--', ' ' =>'-')));
		}
		//on limite les default|default|default
		$check = array ('module', 'group', 'action');
		for ($i=2; $i>=0; $i--){
			if (isset ($urlObject->path[$check[$i]])){
				if ($urlObject->path[$check[$i]] == "default"){
					unset ($urlObject->path[$check[$i]]);
				}else{
					break;
				}
			}
		}
		
		$toReturn = $urlObject->protocol.$urlObject->basePath.$urlObject->scriptName.'/'.implode ('/', $urlObject->path);
		if (count($urlObject->vars) > 0){
			$toReturn .= '?'.self::valueToUrl (null, $urlObject->vars, false, $pForXML);
		}
		return $toReturn;
	}

	/**
    * Création de la classe capable d'interpretter les url pour un module donné
    * @param string $pModule le nom du module dont on veut l'interpretteur
    * @return object
    */
	private static function _createModuleHandler ($pModule){
		if (array_key_exists($pModule, self::$_handlers)){
			return self::$_handlers[$pModule];  
		}

		if (file_exists ($filePath = self::_getModuleHandlerFileName($pModule))){
           Copix::RequireOnce ($filePath);
		   $className = 'UrlHandler'.$pModule;
		   return self::$_handlers[$pModule] = new $className ();
		}else{
           return self::$_handlers[$pModule] = null;			
		}
	}

	/**
    * Gets the significant url handler filename for the given module
    * @param string $pModule the module name. null if we wants the project
    * @return string le chemin de la classe capable d'interpretter les url pour le module donné
    */
	private static function _getModuleHandlerFileName ($pModule){
       return CopixModule::getPath ($pModule).COPIX_CLASSES_DIR.$pModule.'.urlhandler.php';
	}

	/**
    * gets the module/group/action parameters from the destination string.
    *   dest is described as modules|group|action where module & group are optionnal.
    * @param string $pDest the destination to parse
    * @return assocative array where keys are module, group and action
    */
	private static function _getDest ($pDest){
		static $loaded = array ();
		$context = CopixContext::get ();
		if (isset ($loaded[$context][$pDest])){
			return $loaded[$context][$pDest];
		}

		$tabUrl    = explode ('|', $pDest);
		$urlParams = array ();
		switch (count ($tabUrl)){
			case 1:
			$urlParams = array ('module'=>$context, 'group'=>'default', 'action'=>$tabUrl[0]);
			break;

			case 2:
			$urlParams = array ('module'=>$context, 'group'=>$tabUrl[0], 'action'=>$tabUrl[1]);
			break;

			case 3:
			$urlParams = array ('module'=>$tabUrl[0], 'group'=>$tabUrl[1], 'action'=>$tabUrl[2]);
			break;

			default :
			$urlParams = array ();
		}

		if ($urlParams['module'] == '' || $urlParams['module'] == null){
			$urlParams['module'] = 'default';
		}
		if ($urlParams['group'] == '' || $urlParams['group'] == null){
			$urlParams['group'] = 'default';
		}
		if ($urlParams['action'] == '' || $urlParams['action'] == null){
   			$urlParams['action'] = 'default';
		}
		$loaded[$context][$pDest] = $urlParams;
		return $loaded[$context][$pDest];
	}

    /**
    * Supprime les champs spéciaux (irréversible, utilisable a de simples fin de présentation)
    * @param string $pString la chaine dont on veut supprimer les caractères spéciaux
    * @return string
    */
	public static function escapeSpecialChars ($pString){
		$pString = str_replace (array ('\\', '/', ',', '?', '.', '\$'), array (), $pString);
		$pString = str_replace (array ('à', 'â', 'ä', 'é', 'è', 'ê', 'ë', 'î', 'ï', 'ÿ', 'ô', 'ö', 'ù', 'ü', 'ç', 'ñ', 'À', 'Â', 'Ä', 'É', 'È', 'Ê', 'Ë', 'Î', 'Ï', 'Ÿ', 'Ô', 'Ö', 'Ù', 'Ü', 'Ç', 'Ñ'), 
								array ('a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'y', 'o', 'o', 'u', 'u', 'c', 'n', 'A', 'A', 'A', 'E', 'E', 'E', 'E', 'I', 'I', 'Y', 'O', 'O', 'U', 'U', 'C', 'N'), $pString);
		return preg_replace('/[^a-zA-Z0-9]/', '_', $pString); 
	}

	/**
    * Supprime de l'url $pUrl les paramètres $pParams
    * 
    * Le paramètre $pForXml est pris en compte à la fois pour l'interprétation de la requête d'origine
    * et à la fois pour la génération. Il n'est pas possible d'avoir un fonctionnement dissocié 
    *
    * @param string $pUrl url a nettoyer
    * @param array $pParams les pièces à supprimer de l'url (array ('param', 'param2', 'param3');
    * @param boolean $pForXML si l'on génère une sortie html ou non (&amp; au lieu de &)
    * @return string l'url
    */    
	public static function removeParams ($pUrl, $pParams, $pForXML = false) {
		//Analyse de l'url
		$separator = $pForXML ? '&amp;' : '&';
		$url   = parse_url ($pUrl);
		$queryString = array ();
		//On parcours les éléments de la requête pour regarder si ils figurent dans $pParams
		if (isset ($url['query'])){
			foreach (explode ($separator, $url['query']) as $queryElement){
				list ($key, $value) = explode ('=', $queryElement);
				if (!in_array ($key, $pParams)){
					$queryString[] = $queryElement;
				}
			}
			//retour de l'url nettoyée
			return substr ($pUrl, 0, -(strlen ($url['query'])+(count ($queryString) ? 0 : 1))).implode ($separator, $queryString);
		} else {
			//Pas de partie query, on retourne l'url telle qu'elle
			return $pUrl;
		}
	}
	
	/**
	* Extrait les paramètres d'une requête $pUrl
	* @param string $pUrl l'url à analyser
	* @param boolean $pFromXML si l'on analyse une chaine provenant de HTML ou non
	* @return array tableau des paramètres trouvés
	*/
	public static function extractParams ($pUrl, $pFromXML){
		$separator = $pFromXML ? '&amp;' : '&';
		$url   = parse_url ($pUrl);
		
		$params = array ();
		//On parcours les éléments de la requête pour regarder si ils figurent dans $pParams
		if (isset ($url['query'])){
			foreach (explode ($separator, $url['query']) as $queryElement){
				list ($key, $value) = explode ('=', $queryElement);
				$params[$key] = $value;
			}
		}
		return $params;
	}
	
	/**
	 * Récupère un chemin de ressource (situé dans www)
	 * 
	 * Ira chercher dans l'ordre de priorité dans 
	 *  ./nom_theme/lang_COUNTRY/$path
	 *  ./nom_theme/lang/$path
	 *  ./nom_theme/$path
	 *  ./default/lang_COUNTRY/$path
	 *  ./default/lang/$path
	 *  ./default/$path
	 *  ./$path
	 * 
	 * <code>
	 *   //on souhaites récupérer la feuille de style 
	 *   $path = CopixURL::getRessource ('styles/copix.css');
	 *   //$path == http://www.domaine.fr/chemin/vers/le/script/themes/nom_du_theme/styles/copix.css si le fichier existe
	 * </code>
	 * 
	 * @param	string	$resourcePath	le chemin du fichier que l'on souhaites récupérer
	 *        www/$ressourcePath (doit représenter un fichier)
	 * @return	string	le $ressourcePath complet en fonction des thèmes
	 */
	public static function getResource ($pResourcePath){
    	return self::_resourceUrl ('./'.self::getResourcePath ($pResourcePath));
	}
	
	/**
	 * Récupère un chemin de ressource (situé dans www)
	 * 
	 * Ira chercher dans l'ordre de priorité dans 
	 *  ./nom_theme/lang_COUNTRY/$path
	 *  ./nom_theme/lang/$path
	 *  ./nom_theme/$path
	 *  ./default/lang_COUNTRY/$path
	 *  ./default/lang/$path
	 *  ./default/$path
	 *  ./$path
	 * 
	 * <code>
	 *   //on souhaites récupérer la feuille de style 
	 *   $path = CopixURL::getRessourcePath ('styles/copix.css');
	 *   //$path == /var/www/themes/nom_du_theme/styles/copix.css si le fichier existe
	 * </code>
	 * 
	 * @param	string	$resourcePath	le chemin du fichier que l'on souhaites récupérer
	 *        www/$ressourcePath (doit représenter un fichier)
	 * @return	string	le $ressourcePath complet en fonction des thèmes
	 */
	public static function getResourcePath ($pResourcePath) {
		static $calculated = array ();
		if (isset ($calculated[$pResourcePath])){
			return $calculated[$pResourcePath]; 
		}

		//On enlève le premier slash au cas ou l'utilisateur l'a saisi
        if (strpos ($pResourcePath, '/') === 0){
        	$pResourcePath = substr ($pResourcePath, 1);
        }
        $pResourceDir = dirname($pResourcePath);
        $pResourceName = basename($pResourcePath);
        
        $config = CopixConfig::instance ();
		
    	$resourceBasePath ='./themes/';

		if ($config->i18n_path_enabled && file_exists ($resourceFilePath = $resourceBasePath.CopixTpl::getTheme ().'/'.$pResourceDir.'/'.CopixI18N::getLang ().'_'.CopixI18N::getCountry().'/'.$pResourceName)){
    		return $calculated[$pResourcePath] = $resourceFilePath;
    	}elseif ($config->i18n_path_enabled && file_exists ($resourceFilePath = $resourceBasePath.CopixTpl::getTheme ().'/'.$pResourceDir.'/'.CopixI18N::getLang ().'/'.$pResourceName)){
    		return $calculated[$pResourcePath] = $resourceFilePath;
    	}elseif (file_exists ($resourceFilePath = $resourceBasePath.CopixTpl::getTheme ().'/'.$pResourceDir.'/'.$pResourceName)){
    		return $calculated[$pResourcePath] = $resourceFilePath;
    	}       	
    	if (CopixTpl::getTheme () !== "default"){
        	if ($config->i18n_path_enabled && file_exists ($resourceFilePath = $resourceBasePath.'default/'.$pResourceDir.'/'.CopixI18N::getLang ().'_'.CopixI18N::getCountry().'/'.$pResourceName)){
        		return $calculated[$pResourcePath] = $resourceFilePath;
        	}elseif ($config->i18n_path_enabled && file_exists ($resourceFilePath = $resourceBasePath.'default/'.$pResourceDir.'/'.CopixI18N::getLang ().'/'.$pResourceName)){
        		return $calculated[$pResourcePath] = $resourceFilePath;
        	}elseif (file_exists ($resourceFilePath = $resourceBasePath.'default/'.$pResourceDir.'/'.$pResourceName)){
        		return $calculated[$pResourcePath] = $resourceFilePath;
        	}       	        		
    	}
    	
    	return $calculated[$pResourcePath] = $pResourcePath;
	}
	
	/**
	 * Transforme un chemin "fichier" en chemin URL pour les ressources
	 * @param	string	$pFilePath	le chemin obtenu par la fonction getResource ();
	 * @return	string	le chemin url (http://)
	 */
	private static function _resourceUrl ($pFilePath){
		return self::getRequestedBaseUrl ().substr ($pFilePath, 2);
	}  
}
?>