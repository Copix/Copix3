<?php
/**
 * @package	copix
 * @subpackage	core
 * @author	Croës Gérald
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

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

	const HTTP = 'http://';
	const HTTPS = 'https://';

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
		static $requestedScriptVariable = false;
		if ($requestedScriptVariable === false){
			if (!is_array ($requestedScriptVariable = CopixConfig::instance ()->url_requestedscript_variable)){
				$requestedScriptVariable = array (CopixConfig::instance ()->url_requestedscript_variable);
			}
		}

		foreach ($requestedScriptVariable as $variableName){
			if (array_key_exists ($variableName, $_SERVER)){
				return $_SERVER[$variableName];
			}
		}
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
			//only if RewiteRules in .htaccess doesn't remove "index.php" from path:
			if(!isset($_SERVER['COPIX_REWRITE_RULES']) || strtoupper($_SERVER['COPIX_REWRITE_RULES'])!='ON' ){
				self::$_scriptName = substr (self::getRequestedScript (), strrpos (self::getRequestedScript (), '/')+1);
			}
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
	public static function getRequestedDomain () {
		if (!empty ($_SERVER ['HTTP_X_FORWARDED_HOST'])) {
			return $_SERVER ['HTTP_X_FORWARDED_HOST'];
		} else if (isset ($_SERVER ['HTTP_HOST']) && !empty ($_SERVER ['HTTP_HOST'])) {
			return $_SERVER ['HTTP_HOST'];
		}
		return $_SERVER ['SERVER_NAME'];
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
			return self::$_protocol = self::HTTPS;
		}
		return self::$_protocol = self::HTTP;
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
			if (isset ($_SERVER['ORIG_PATH_INFO']) || isset ($_SERVER['PATH_INFO'])){
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
	 * @param	boolean		$pForXML si l'on génère en XML ou non (&amp; au lieu de &
	 * @return	string		l'url demandée
	 * @deprecated
	 * @see CopixUrl::getRequestedUrl ();
	 */
	public static function getCurrentUrl ($pForXML = false) {
		return self::getRequestedUrl ($pForXML);
	}

	/**
	 * Change l'url courante avec celle passé en paramètre
	 * @param	string	L'url a mettre en url courante
	 */
	public static function setRequestedUrl ($pUrl) {
		self::$_url[false] = self::get ($pUrl, array (), false);
		self::$_url[true]  = self::get ($pUrl, array (), true);
	}

	/**
	 * Récupère l'url demandée
	 * @param boolean $pForXML si l'on génère en XML ou non (&amp; au lieu de &
	 * @return string l'url demandée
	 */
	public static function getRequestedUrl ($pForXML = false){
		if (!isset (self::$_url[$pForXML])){
			$currentUrl = http_build_query ($_GET, '', $pForXML ? '&amp;' : '&');
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
		return ($pStartWithAmp ? $amp : '').http_build_query ($pName !== null ? array ($pName=>$pValue) : $pValue, '', $amp);
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
	 * @param 	boolean	$pFromXml		Indique si la requête donnée est au format XML (&amp; au lieu de &
	 * @return	array	tableau des paramètres envoyés au script
	 */
	public static function parse ($pUrl, $pFromString = false, $pFromXML = false) {
		//deleting parameters we don't need. We could avoid this.....
		$vars = array ();

		$pFullUrl = $pUrl;
		if ($pFromString && (($pos = strpos ($pUrl, '?')) !== false)){
			$pUrl  = substr ($pUrl, 0, $pos);
		}

		// si la chaine contient le SCRIPT_NAME, on doit l'enlever
		$posScriptName = strpos ($pUrl, self::getRequestedScript ());
		if ($posScriptName !== false) {
			$pUrl = substr ($pUrl, $posScriptName + strlen (self::getRequestedScript ()));
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
			return array_merge ($_GET, $_POST, $vars);
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

		if ($module = isset ($urlX[0]) ? $urlX[0] : null){
			//On essaye ensuite de transmettre l'url au module dont le nom commence par la première occurence du chemin
			foreach (self::getList ($module) as $handlerId){
				if (($vars = self::_createHandler ($handlerId)->parse ($urlX, 'prepend')) !== false) {
					return $vars;
				}
			}
		}

		//Cela n'a rien donné, on va tenter l'expérience avec tous les autres modules
		foreach (self::getList () as $handlerId){
			if (($vars = self::_createHandler ($handlerId)->parse ($urlX, 'prepend')) !== false) {
				return $vars;
			}
		}

		//Aucun handler trouvé
		if (!$module) {
			$module = isset ($_GET ['module'])?$_GET ['module']:'';
		}if (($countUrl = count ($urlX))>= 2){
			$group = $urlX[1];
		}else{
			$group = isset ($_GET ['group'])?$_GET ['group']:'default';
		}

		if ($countUrl >= 3){
			$action = $urlX[2];
		}else{
			$action = isset ($_GET ['action'])?$_GET ['action']:'default';;
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

		//pour les url relatives
		if (stripos($pDest, '.') === 0){
			return $pDest;
		}

		//On supporte les urls de type http:// ou autre
		if (stripos ($pDest, self::HTTP) === 0 ||
		stripos ($pDest, self::HTTPS) === 0 ||
		stripos ($pDest, 'ftp://') === 0 ||
		stripos ($pDest, 'ftps://')
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

		$pParams = array_merge(self::extractParams ($pDest), $pParams);
		foreach (self::getList ($dest['module']) as $handlerId){
			if (($urlObject = self::_createHandler ($handlerId)->get ($dest, $pParams, 'default')) !== false){
				break;
			}
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
			// Pour éviter de générer une URL avec default dans module, action ou group
			if ($value === 'default' && in_array($key, array('module', 'group', 'action'))) {
				unset ($urlObject->path [$key]);
				continue;
			}
			$urlObject->path[$key] = urlencode (strtr ($value, array ('-'=>'--', ' ' =>'-')));
		}

		//$toReturn = $urlObject->protocol.$urlObject->basePath.$urlObject->scriptName.'?'.implode ('/', $urlObject->path);
		$toReturn = $urlObject->protocol.$urlObject->basePath.$urlObject->scriptName.'?'.http_build_query ($urlObject->path, '', $pForXML ? '&amp;' : '&');
		if (count($urlObject->vars) > 0){
			$toReturn .= ($pForXML ? '&amp;' : '&').http_build_query ($urlObject->vars, '', $pForXML ? '&amp;' : '&');
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

		$pParams = array_merge(self::extractParams ($pDest), $pParams);
		foreach (self::getList ($dest['module']) as $handlerId){
			if (($urlObject = self::_createHandler ($handlerId)->get ($dest, $pParams, 'prepend')) !== false){
				break;
			}
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
		if(strlen($urlObject->scriptName)>0){
			$urlObject->scriptName .= '/';
		}
		$toReturn = $urlObject->protocol.$urlObject->basePath.$urlObject->scriptName.implode ('/', $urlObject->path);
		if (count ($urlObject->vars) > 0){
			$toReturn .= '?'.http_build_query ($urlObject->vars, '', $pForXML ? '&amp;' : '&');
		}
		if (isset($urlObject->anchor) && $urlObject->anchor){
			$toReturn .= '#'.$urlObject->anchor;
		}
		return $toReturn;
	}

	/**
	 * Création de la classe capable d'interpretter les url pour un module donné
	 * @param string $pModule le nom du module dont on veut l'interpretteur
	 * @return object
	 */
	private static function _createHandler ($pId){
		if (array_key_exists($pId, self::$_handlers)){
			return self::$_handlers[$pId];
		}
		return self::$_handlers[$pId] = _class ($pId);
	}

	/**
	 * gets the module/group/action parameters from the destination string.
	 *   dest is described as modules|group|action where module & group are optionnal.
	 * @param string $pDest the destination to parse
	 * @return assocative array where keys are module, group and action
	 */
	private static function _getDest ($pDest) {
		static $loaded = array ();
		$context = CopixContext::get ();
		if (isset ($loaded[$context][$pDest])) {
			return $loaded[$context][$pDest];
		}

		$tabUrl = explode ('|', $pDest);
		$urlParams = array ();
		switch (count ($tabUrl)) {
			case 1:
				$urlParams = array ('module' => $context, 'group' => 'default', 'action' => $tabUrl[0]);
				break;

			case 2:
				$urlParams = array ('module' => $context, 'group' => $tabUrl[0], 'action' => $tabUrl[1]);
				break;

			case 3:
				$urlParams = array ('module' => $tabUrl[0], 'group' => $tabUrl[1], 'action' => $tabUrl[2]);
				break;

			default :
				_log ('Adresse invalide : ' . $pDest, 'errors', CopixLog::ERROR);
				$urlParams = array ('module' => 'default', 'group' => 'default', 'action' => 'default');
		}

		if ($urlParams['module'] == null) {
			$urlParams['module'] = 'default';
		}
		if ($urlParams['group'] == null) {
			$urlParams['group'] = 'default';
		}
		if ($urlParams['action'] == null) {
			$urlParams['action'] = 'default';
		}

		// Suppression des éventuels paramètres
		if (($pos = strpos ($urlParams['action'], '?')) !== false) {
			$urlParams['action'] = substr ($urlParams['action'], 0, $pos);
		}

		$loaded[$context][$pDest] = $urlParams;
		return $loaded[$context][$pDest];
	}

	/**
	 * Supprime les champs spéciaux (irréversible, utilisable a de simples fin de présentation)
	 * @param	string	$pString la chaine dont on veut supprimer les caractères spéciaux
	 * @param	boolean	$pCompressUnderscores (false) si oui, les underscores en doubles sont supprimés
	 * @param	boolean	$pReplaceSpaces (false) si oui, les espace sont remplacés par des tirets et les tirets sont doublés
	 * @param	string	$pCharsToKeep ('') les caractères spéciaux à conserver
	 * @return string
	 */
	public static function escapeSpecialChars ($pString, $pCompressUnderscores = false, $pReplaceSpaces = false, $pCharsToKeep = ''){
		if ($pReplaceSpaces && strpos ($pCharsToKeep, '-') === false) {
			$pCharsToKeep .= '-';
		}
		if ($pCharsToKeep) {
			// Retire des caractères spéciaux ceux qu'on souhaite conserver
			$defaultCharsToKeep = array ('\\', '/', ',', '?', '.', '$');
			$specialChars = array_diff ($defaultCharsToKeep, array_unique (str_split ($pCharsToKeep)));
			
			// Pour que preg_replace ne remplace pas les caractères à conserver
			$regexpCharsToKeep = preg_quote ($pCharsToKeep, '/');
			$pattern = '/[^a-zA-Z0-9'.$regexpCharsToKeep.']/';
		} else {
			$pattern = '/[^a-zA-Z0-9]/';
			$specialChars = array ('\\', '/', ',', '?', '.', '$');
		}
		
		$pString = trim ($pString);
		$pString = str_replace ($specialChars, array (), $pString);
		
		$pString = str_replace (
			array ('à', 'â', 'ä', 'é', 'è', 'ê', 'ë', 'î', 'ï', 'ÿ', 'ô', 'ö', 'ù', 'ü', 'ç', 'ñ', 'À', 'Â', 'Ä', 'É', 'È', 'Ê', 'Ë', 'Î', 'Ï', 'Ÿ', 'Ô', 'Ö', 'Ù', 'Ü', 'Ç', 'Ñ', '€'),
			array ('a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'y', 'o', 'o', 'u', 'u', 'c', 'n', 'A', 'A', 'A', 'E', 'E', 'E', 'E', 'I', 'I', 'Y', 'O', 'O', 'U', 'U', 'C', 'N', 'euro'),
			$pString);
		
		$pString = ($pReplaceSpaces) ? str_replace (' ', '-', $pString) : $pString;
		$pString = preg_replace ($pattern, '_', $pString);
		$pString = ($pCompressUnderscores) ? preg_replace ('/_+/s','_', $pString) : $pString;
		return $pString;
	}

	/**
	 * Supprime de l'url $pUrl les paramètres $pParams
	 *
	 * Le paramètre $pForXml est pris en compte à la fois pour l'interprétation de la requête d'origine
	 * et à la fois pour la génération. Il n'est pas possible d'avoir un fonctionnement dissocié
	 *
	 * @param string $pUrl url a nettoyer
	 * @param array $pParams les pièces à supprimer de l'url (array ('param', 'param2', 'param3');
	 * @param boolean $pForXML si l'on génère une sortie html ou non (&amp; au lieu de &
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
				list ($key) = explode ('=', $queryElement);
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
	public static function extractParams ($pUrl, $pFromXML = false){
		$separator = $pFromXML ? '&amp;' : '&';
		$url   = parse_url ($pUrl);

		$params = array ();
		//On parcours les éléments de la requête pour regarder si ils figurent dans $pParams
		if (isset ($url['query'])){
			if ($pFromXML){
				str_replace ('&amp;', '&', $url['query']);
			}
			parse_str ($url['query'], $params);
		}
		return $params;
	}

	/**
	 * Analyse et découpe une sélecteur de ressource.
	 *
	 * Accepte les deux formes suivantes :
	 * - chemin/vers/ressource.txt
	 * - module|chemin/vers/ressource.txt
	 *
	 * Le '/' initial du chemin est supprimé.
	 *
	 * @param unknown_type $pResourcePath Sélecteur de la ressource.
	 * @return array Tableau de la forme (chemin, nom_du_module ou null, chemin_du_module ou null)
	 */
	private static function _parseResourcePath($pResourcePath) {
		@list($moduleName, $resourcePath) = explode('|', $pResourcePath);
		if(is_null($resourcePath)) {
			// Ne contient pas de '|'
			$resourcePath = $moduleName;
			$moduleName = null;
		} elseif(empty($moduleName)) {
			// Module par défaut
			$moduleName = CopixContext::get();
		}
		if(substr($resourcePath, 0, 1) == '/') {
			$resourcePath = substr($resourcePath, 1);
		}
		return array($resourcePath, $moduleName, empty($moduleName) ? null : CopixModule::getPath($moduleName));
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
	public static function getResource ($pResourcePath, $pTheme = null, $withVersion = null){
		static $calculated = array ();
		
		// Si on n'a pas indiqué qu'on veut le numéro de version, on prend la config du projet
		if ($withVersion == null) {
			$withVersion = (CopixConfig::instance ()->copixresource_addVersionParam || CopixConfig::instance ()->copixresource_addVersionInFileName);
		}

		$theme = ($pTheme === null) ? CopixTpl::getTheme () : $pTheme;
		$i18n = CopixConfig::instance ()->i18n_path_enabled;
		$lang = CopixI18N::getLang ();
		$country = CopixI18N::getCountry ();

		$key = $theme.$i18n.$lang.$country.$pResourcePath;

		if (isset ($calculated[$key])){
			return $calculated[$key];
		}

		list($resourcePath, $moduleName, $modulePath) = self::_parseResourcePath($pResourcePath);

		// Utilise CopixResource pour trouver la ressource
		return $calculated[$key] =
		CopixResource::findResourceUrl(
		$resourcePath,
		$moduleName,
		$modulePath,
		$theme,
		$i18n,
		$lang,
		$country,
		$withVersion
		);
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
	public static function getResourcePath ($pResourcePath, $pTheme = null) {
		static $calculated = array ();

		$theme = ($pTheme === null) ? CopixTpl::getTheme () : $pTheme;
		$i18n = CopixConfig::instance ()->i18n_path_enabled;
		$lang = CopixI18N::getLang ();
		$country = CopixI18N::getCountry ();

		$key = $theme.$i18n.$lang.$country.$pResourcePath;

		if (isset ($calculated[$key])){
			return $calculated[$key];
		}

		list($resourcePath, $moduleName, $modulePath) = self::_parseResourcePath ($pResourcePath);

		// Utilise CopixResource pour trouver la ressource
		return $calculated[$key] =
		CopixResource::findResourcePath(
		$resourcePath,
		$moduleName,
		$modulePath,
		$theme,
		$i18n,
		$lang,
		$country
		);
	}

	/**
	 * Récupère la liste des URLHandler existant dans l'installation (identifiants uniquement)
	 * @return array of string
	 */
	public static function getList ($pModuleName = false){
		$toReturn = CopixModule::getParsedModuleInformation ('copix|urlhandlers',
														'/moduledefinition/urlhandlers/urlhandler',
		array ('CopixURLHandlerParser', 'parse'));
		if ($pModuleName === false){
			return new RecursiveIteratorIterator (new RecursiveArrayIterator ($toReturn));
		}elseif (isset ($toReturn[$pModuleName])){
			return $toReturn[$pModuleName];
		}
		return array ();
	}

	/**
	 * Retourne le nom de domaine de l'adresse donnée
	 *
	 * @param string $pUrl
	 * @return string
	 */
	public static function getDomain ($pUrl) {
		if (preg_match ('#(http|https|ftp)://(.*?)/#', CopixFile::trailingSlash ($pUrl), $match)) {
			return $match[2];
		}
	}
}