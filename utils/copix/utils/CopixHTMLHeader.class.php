<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croes Gérald, Jouanneau Laurent
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet de manipuler l'en tête de la sortie HTML au développeur.
 * CopixHTMLHeader placera ses informations dans la variable du template principal nommé
 * {$HTML_HEAD}
 * @package copix
 * @subpackage core
 */
class CopixHTMLHeader {

	/**
	 * Code Javascript à ne jamais encapsuler dans l'événement domready.
	 *
	 * @see addJSCode
	 */
	const DOMREADY_NEVER = 0;

	/**
	 * Code Javascript à encapsuler dans l'événement domready si nécessaire.
	 *
	 * @see addJSCode
	 */
	const DOMREADY_AUTO = 1;

	/**
	 * Code Javascript à toujours encapsuler dans l'événement domready.
	 *
	 * @see addJSCode
	 */
	const DOMREADY_ALWAYS = 2;
	
	const MOOTOOLS = '*core*';
	const JQUERY = '*jquery*';

	/**
	 * Tableau de liens sur des feuilles de style
	 * @var array
	 */
	private static $_CSSLink = array ();

	/**
	 * Styles CSS définis
	 * @var array
	 */
	private static $_Styles  = array ();

	/**
	 * Tableau de liens sur des fichiers javascript à inclure
	 * @var array
	 */
	private static $_JSLink  = array ();

	/**
	 * Code Javascript à ajouter.
	 * @var array
	 */
	private static $_JSCode  = array ();
	
	private static $_JQueryCode  = array ();

	/**
	 * Autre en-têtes à ajouter.
	 * @var array
	 */
	private static $_Others  = array ();
	
	/**
	 * HTML à ajouter en fin de document.
	 * @var array
	 */
	private static $_HTMLFoot  = array ();

	/**
	 * A-t-on déjà demandé le framework Javascript ?
	 *
	 * @var boolean
	 */
	private static $_JSFrameworkAdded = array();
	
	/**
	 * L'icone actuellement demandée pour le site
	 *
	 * @var string
	 */
	private static $_favicon = null;
	
	/**
	 * Indique si CopixHTMLHeader traque les modifications effectuées
	 * 
	 * @var boolean  
	 */
	private static $_listeningForChanges = false;
	
	/**
	 * Etat avant écoute des changements
	 * 
	 * @var array
	 */
	private static $_stateBeforeListening = array ();
	
	/**
	 * Retourne l'état courant de CopixHTMLHeader.
	 * 
	 * @return array 
	 */
	public static function getState (){
		return array (
			'CSSLink'=>self::$_CSSLink, 
			'HTMLFoot'=>self::$_HTMLFoot, 
			'JSCode'=>self::$_JSCode, 
			'JSFrameworkAdded'=>self::$_JSFrameworkAdded, 
			'JSLink'=>self::$_JSLink, 
			'Others'=>self::$_Others, 
			'Styles'=>self::$_Styles, 
			'Favicon'=>array (self::$_favicon));
	}
	
	/**
	 * Défini l'état courant de CopixHTMLHeader
	 * 
	 * @param array $pState Le tableau des états, devant être du même format que celui renvoyé par getState
	 */
	public static function setState ($pState){
		self::$_CSSLink = $pState['CSSLink'];
		self::$_HTMLFoot = $pState['HTMLFoot'];
		self::$_JSCode = $pState['JSCode'];
		self::$_JSFrameworkAdded = $pState['JSFrameworkAdded'];
		self::$_JSLink = $pState['JSLink'];
		self::$_Others = $pState['Others'];
		self::$_Styles = $pState['Styles'];
		self::$_favicon = $pState['Favicon'][0];
	}
	
	/**
	 * Commence à écouter les modifications demandées à CopixHTMLHeader
	 */
	public static function startListeningForChanges (){
		if (self::$_listeningForChanges){
			return;
		}
		self::$_stateBeforeListening = self::getState ();
		self::$_listeningForChanges = true;
	}
	
	/**
	 * Arrête d'écouter les changements apportés à CopixHTMLHeader
	 * 
	 * @return array $pChanges
	 */
	public static function stopListeningForChanges (){
		$currentState = self::getState ();

		self::$_listeningForChanges = false;
		$toReturn = array ();

		foreach ($currentState as $key=>$values){
			if ($key === 'JSCode'){
				foreach ($values as $domType=>$content){
					$toReturn[$key][$domType] = array_diff ($content, isset (self::$_stateBeforeListening[$key][$domType]) ? self::$_stateBeforeListening[$key][$domType] : array ());
				}
			}else{
				$toReturn[$key] = array_diff_assoc ($values, self::$_stateBeforeListening[$key]);
			}
		}
		return $toReturn;
	}
	
	/**
	 * Donne un tableau de modifications à appliquer.
	 * 
	 * @param array $pToApply Tableau de modifications, tableau correspondant au format retourné par la méthode getState
	 */
	public static function applyChanges ($pToApply){
		foreach ($pToApply as $where=>$values){
			foreach ($values as $key=>$value){
				switch ($where){
					case 'CSSLink':
						self::addCSSLink($key, $value);
						break;
					case 'HTMLFoot':
						self::addHTMLFoot ($value, $key);
						break; 
					case 'JSCode':
						foreach ($value as $id=>$code){
							self::addJSCode ($code, $id, $key);
						}
						break; 
					case 'JSFrameworkAdded':
						self::$_JSFrameworkAdded[$key] = $value; 
						break; 
					case 'JSLink':
						self::addJSLink($key, $value);
						break; 
					case 'Others':
						self::addOthers($value, $key);
						break; 
					case 'Styles':
						self::addStyle($key, $value);
						break; 
					case 'Favicon':
						self::addFavIcon ($value);
						break;
				}
			}
		}
	}

	/**
	 * Construit un identifiant à partir d'une URL.
	 *
	 * @param string $pUrl URL
	 * @return string Un identifiant bon à utiliser dans une balise HTML.
	 */
	private static function _buildId ($pUrl) {
		return preg_replace ('/[^\w]+/', '_', str_replace (CopixUrl::get (), '', $pUrl));
	}

	/**
	 * Ajoute un lien vers un fichier Javascript. N'ajoutera pas deux fois un même lien
	 * @param string $src le chemin vers le javascript (tel qu'il apparaitra)
	 * @param array $params tableau de paramètres suppélemntaires à ajouter à l'inclusion du fichier
	 */
	public static function addJSLink ($src, $params=array()){
		if (! isset ($params['id'])){
			$params['id'] = self::_buildId ($src);
		}
		self::$_JSLink[$src] = $params;
	}

	/**
	 * Ajoute un lien vers un fichier CSS. N'ajoutera pas deux fois le même lien
	 * @param string $src le chemin vers le fichier CSS (tel qu'il apparaitra)
	 * @param array $params tableau de paramètres suppélmentaires à ajouter dans l'inclusion du fichier
	 */
	public static function addCSSLink ($src, $params=array ()){
		if(!isset($params['id'])) {
			$params['id'] = self::_buildId ($src);
		}
		self::$_CSSLink[$src] = $params;
	}

	/**
	 * Ajoute la définition d'un style CSS
	 * @param string $selector le nom du sélecteur que l'on souhaites définir
	 * @param string $def la définition complète u style que l'on souhaites
	 *    définir tel qu'il apparaitra dans la feuille de style)
	 *  Si $def vaut null, alors on considère que $selector contient en fait un ensemble de
	 *  style valides
	 */
	public static function addStyle ($selector, $def = null){
		if (!isset (self::$_Styles[$selector])){
			self::$_Styles[$selector] = $def;
		}
	}

	/**
	 * Ajoute d'autres élements au code HTML d'en tête.
	 * @param string $content le contenu que l'on souhaite rajouter
	 * @param string $key la clef pour identifier la chaine ajoutée
	 */
	public static function addOthers ($content, $key = null){
		if ($key === null){
			self::$_Others[uniqid ()] = $content;
		}else{
			self::$_Others[$key] = $content;
		}
	}

	/**
	 * Ajoute de code HTML en fin de page (juste avant le javascript)
	 * @param string $content le contenu que l'on souhaite rajouter
	 * @param string $key la clef pour identifier la chaine ajoutée
	 */
	public static function addHTMLFoot ($content, $key = null){
		if ($key === null){
			self::$_HTMLFoot[uniqid ()] = $content;
		}else{
			self::$_HTMLFoot[$key] = $content;
		}
	}

	/**
	 * Ajoute du javascript dans le header.
	 * 
	 * Si $pId est fourni et a déjà été utilisé, le code existant est remplacé par $pCode. 
	 *
	 * @param string $pCode Code à rajouter
	 * @param string $pId Identifiant du fragment, pour éviter les doublons
	 * @param boolean $$pDomReady Le code doit-il être encapsulé dans l'événément domready ?
	 */
	public static function addJSCode ($pCode, $pId = null, $pDomReady = CopixHTMLHeader::DOMREADY_AUTO){
		if($pDomReady != self::DOMREADY_ALWAYS && $pDomReady != self::DOMREADY_NEVER) {
			$pDomReady = self::DOMREADY_AUTO;
		}
		if($pDomReady == self::DOMREADY_ALWAYS) {
			self::addJSFramework();
		}
		if(is_array($pCode)) {
			$pCode = join("", $pCode);
		} else {
			$pCode = _toString($pCode);
		}
		if($pId !== null) {
			self::$_JSCode[$pDomReady][$pId] = $pCode;
		} else {
			self::$_JSCode[$pDomReady][uniqid ()] = $pCode;
		}
	}
	
	public static function addJQueryCode ($pCode, $pId = null, $pDomReady = CopixHTMLHeader::DOMREADY_AUTO){
		if($pDomReady != self::DOMREADY_ALWAYS && $pDomReady != self::DOMREADY_NEVER) {
			$pDomReady = self::DOMREADY_AUTO;
		}
		
		self::addJQuery();
		
		if(is_array($pCode)) {
			$pCode = join("", $pCode);
		} else {
			$pCode = _toString($pCode);
		}
		if($pId !== null) {
			self::$_JQueryCode[$pDomReady][$pId] = $pCode;
		} else {
			self::$_JQueryCode[$pDomReady][uniqid ()] = $pCode;
		}
	}
	
	/**
	 * Ajoute du javascript à encapsuler dans domready.
	 * 
	 * Si $pId est fourni et a déjà été utilisé, le code existant est remplacé par $pCode. 
	 *
	 * @param string $pCode Code à rajouter
	 * @param string $pId Identifiant du fragment, pour éviter les doublons
	 */
	public static function addJSDOMReadyCode ($pCode, $pId = null){
		self::addJSCode ($pCode, $pId, self::DOMREADY_ALWAYS);
	}

	public static function addJQueryDocumentReadyCode ($pCode, $pId = null){
		self::addJQueryCode($pCode, $pId, self::DOMREADY_ALWAYS);
	}
	
	/**
	 * Indique si un contenu du type donné est défini. Si on a passé un identifiant lors de l'ajout, on peut le redonner ici pour être plus précis.
	 * @param string $contentType le type de contenu
	 * @param string $key (facultatif) une clef identifiant un contenu particulier
	 */
	public static function isContentAdded ($contentType, $key = null){
		$contentTypes = array (
			'CSSLink' => self::$_CSSLink, 
			'HTMLFoot' => self::$_HTMLFoot, 
			'JSCode' => self::$_JSCode, 
			'JSFrameworkAdded' => self::$_JSFrameworkAdded, 
			'JSLink' => self::$_JSLink, 
			'Others' => self::$_Others, 
			'Styles' => self::$_Styles, 
			'Favicon' => array (self::$_favicon)
		);
		if (!array_key_exists ($contentType, $contentTypes)) {
			throw new CopixException ("Le type de contenu '$contentType' n'est pas géré par CopixHTMLHeader. Valeurs attendues : '". implode("', '", array_keys ($contentTypes))."'.");
		}
		if ($key === null) {
			return implode ('', $contentTypes[$contentType]) !== '';
		} else {
			return array_key_exists ($key, $contentTypes[$contentType]);
		}
	}

	/**
	 * récupère le contenu à rajouter dans l'en tête
	 * @return string
	 */
	public static function get (){
		$toReturn = self::getCSSLink () . "\n" . self::getJSLink () . "\n" . self::getStyles () . "\n" . self::getJSCode () . "\n" . self::getOthers ();
		if (!CopixRequest::isAJAX ()) {
			$toReturn .= "\n" . self::getCredits ();
		}
		return $toReturn;
	}
	
	/**
	 * récupère le contenu à rajouter dans l'en tête (sans les scripts)
	 * @return string
	 */
	public static function getHeader (){
		$toReturn = self::getCSSLink () . "\n" . self::getStyles () . "\n" . self::getOthers ();
		if (!CopixRequest::isAJAX ()) {
			$toReturn .= "\n" . self::getCredits ();
		}
		return $toReturn;
	}
	
	/**
	 * récupère les scripts à rajouter en fin de page
	 * @return string
	 */
	public static function getFooter (){
		$toReturn = self::getHTMLFoot () . "\n" . self::getJSLink () . "\n" . self::getJSCode ();
		return $toReturn;
	}

	/**
	 * Récupération de la partie d'en tête "autres"
	 * @return string
	 */
	public static function getOthers (){
		if ($picturePath = self::getFavIcon ()){
			$append = '<link rel="icon" href="'.$picturePath.'" />';
		}else{
			$append = "";
		}
		return implode ("\n", self::$_Others).$append;
	}

	/**
	 * Récupération de la partie HTML à mettre en fin de page
	 * @return string
	 */
	public static function getHTMLFoot (){
		return implode ("\n", self::$_HTMLFoot);
	}

	/**
	 * Génère un tag script pour insérer du Javascript.
	 *
	 * @param array $pCode Code Javasscript.
	 * @return string Code XHTML pour l'inclusion du javascript.
	 */
	private static function _buildJSTag ($pCode) {
		if (!empty ($pCode)) {
			return "<script type=\"text/javascript\">//<![CDATA[\n".$pCode."\n//]]></script>";
		} else {
			return '';
		}
	}

	/**
	 * Génère un gestionnaire d'événéments.
	 *
	 * @param string $pEvent Nom de l'évément.
	 * @param string $pElement Nom de l'élément.
	 * @param array $pCode Code du gestionnaire (tableau ou chaîne)
	 * @return string
	 */
	private static function _buildJSOnEvent ($pElement, $pEvent, $pCode) {
		return !empty ($pCode) ? sprintf (
			"%s.addEvent('%s', function(){\n\t%s\n});",
		$pElement,
		$pEvent,
		is_array ($pCode) ? implode ("\n\t", $pCode) : $pCode
		) : '';
	}

	/**
	 * Récupération du code javascript ajouté
	 * @return string <head> HTML Content
	 */
	public static function getJSCode (){
		
		$isDevel = CopixConfig::instance()->getMode() == CopixConfig::DEVEL; 

		// Récupère les portions de code
		$neverCode = isset (self::$_JSCode[self::DOMREADY_NEVER]) ? self::$_JSCode[self::DOMREADY_NEVER] : array ();
		$autoCode = isset (self::$_JSCode[self::DOMREADY_AUTO]) ? self::$_JSCode[self::DOMREADY_AUTO] : array ();
		$alwaysCode = isset (self::$_JSCode[self::DOMREADY_ALWAYS]) ? self::$_JSCode[self::DOMREADY_ALWAYS] : array ();

		$neverJqueryCode = isset (self::$_JQueryCode[self::DOMREADY_NEVER]) ? self::$_JQueryCode[self::DOMREADY_NEVER] : array ();
		$autoJqueryCode = isset (self::$_JQueryCode[self::DOMREADY_AUTO]) ? self::$_JQueryCode[self::DOMREADY_AUTO] : array ();
		$alwaysJqueryCode = isset (self::$_JQueryCode[self::DOMREADY_ALWAYS]) ? self::$_JQueryCode[self::DOMREADY_ALWAYS] : array ();

		// Si le framework JS est chargé, ajoute l'execution des événéments mis en queue
		if(isset(self::$_JSFrameworkAdded[self::MOOTOOLS])) {
			$alwaysCode[] = 'Copix.fireQueuedEvents();';
		}
		
		// En AJAX, ajoute le charge des feuilles de styles et javascripts distants
		if (CopixAJAX::isAJAXRequest ()) {
				
			$linksCode = array();
				
			// Ajoute les liens
			foreach (array ('css' => self::$_CSSLink, 'javascript' => self::$_JSLink) as $kind => $links) {
				foreach ($links as $url=>$params) {
					$params['kind'] = $kind;
					$params['url'] = $url;
					unset ($params['comment']);
					$linksCode[] = "Copix.addLink(".CopixJSON::encode ($params).");";
				}
			}
				
			if (count ($linksCode) > 0) {
				// On a des liens : liens, NEVER, puis AUTO et ALWAYS dans encapsulés
				$code = array_merge ($linksCode, $neverCode);
				$linkloadedCode = array_merge($autoCode, $alwaysCode);
				if (count ($linkloadedCode) > 0) {
					$code[] = self::_buildJSOnEvent ('window', 'linksloaded', $linkloadedCode);
				}

			} else {
				// Pas de lien : tout à la suite
				$code = array_merge ($neverCode, $autoCode, $alwaysCode);
			}
				
		} else {
			// Non AJAX : NEVER et AUTO ensembles, ALWAYS encapsulé 
			$code = array_merge($neverCode, $autoCode);
			if (count ($alwaysCode) > 0) {
				$code[] = self::_buildJSOnEvent ('window', 'domready', $alwaysCode);
			}
		}
		
		if(count($neverJqueryCode) > 0 || count($autoJqueryCode) > 0 || count($alwaysJqueryCode) > 0 ){
			self::addJQuery();
			
			if (count ($alwaysJqueryCode) > 0) {
				$jQueryDomReady = implode ("\n", $alwaysJqueryCode);
				$code[] = "jQuery.noConflict();\njQuery(document).ready(function(\$) {\n$jQueryDomReady\n});";
			}
			$jqCode = array_merge($neverJqueryCode, $autoJqueryCode);
			$srtJqCode = implode ("\n", $jqCode);
			$code[]  = "jQuery.noConflict();\n(function($) {\$(function() {\n$srtJqCode\n});})(jQuery);";
		}

		// Encapsule le tout dans un tag javascript
		return self::_buildJSTag (implode ("\n", $code));
	}

	/**
	 * Récupération des styles ajoutés à l'en tête
	 * @return string <head> Contenu HTML
	 */
	public static function getStyles (){
		$built = array ();
		foreach (self::$_Styles as $selector=>$value){
			if (strlen (trim ($value))){
				//il y a une paire clef valeur.
				$built[] = $selector.' {'.$value.'}';
			}else{
				//il n'y a pas de valeur, c'est peut être simplement une commande.
				//par exemple @import qqchose, ...
				$built[] = $selector;
			}
		}
		if(($css=implode ("\n", $built)) != ''){
			return '<style type="text/css"><!--
         '.$css.'
         //--></style>';
		}
	}

	/**
	 * Récupération des liens vers les feuilles de styles
	 * @return string <head> Contenu HTML
	 */
	public static function getCSSLink () {
		if (count (self::$_CSSLink) == 0 || CopixAJAX::isAJAXRequest()) {
			return '';
		}
		$concat = CopixConfig::instance ()->copixhtmlheader_concatCSS;
		$filesConcat = array ();
		$built = array ();
		$positionMaxConcat = null; // Position max des fichiers concaténés, pour ne pas avoir de conflit avec l'ordre d'inclusion des fichiers isolés

		// pour faciliter la gestion de la priorité d'insertion, on remet le tableau dans l'ordre des priorités
		// de base le tableau a pour clef l'url du fichier, ce qui permet de facilement gérer les doublons
		$cssLinks = array ();
		foreach (self::$_CSSLink as $src => &$params) {
			$params['src'] = $src;
			if (isset ($params['position'])) {
				// On incrémente la position si elle est déjà présente pour ne pas écraser la première
				while ((isset ($cssLinks[$params['position']]))) {
					$params['position']++;
				}
			} else {
				// array_pop génère une Only variable should be passed by reference si on passe directement le résultat de array_keys
				$keys = array_keys ($cssLinks);
				$params['position'] = (count ($cssLinks) == 0) ? 100 : array_pop ($keys) + 10;
			}
			$cssLinks[$params['position']] = $params;
		}
		ksort ($cssLinks);

		foreach ($cssLinks as $params) {
			if (!$params['src']) {
				continue; // Quand un fichier n'est pas trouvé
			}
			// on peut concaténer ce fichier
			if ($concat && (!isset ($params['concat']) || $params['concat'])) {
				// adresse directe
				if (($pos = strpos ($params['src'], 'resource.php')) === false) {
					$filesConcat[$params['id']] = substr( $params['src'], strpos( $params['src'], '/', 8) + strlen( CopixURL::getRequestedScriptPath () ) ); // Le chemin de la ressource, sans domain ni chemin de base
				// passage par resource.php
				} else {
					$srcPath = substr ($params['src'], $pos+13);
					$resource = new CopixResourceFetcher ();
					$resource->setFromURL ($params['src']);
					// fetch remplace le tag {copixresource}, il faut donc forcément l'appeler
					$filePath = $resource->getFilePath ();
					$srcPath = CopixResource::getPathWithoutVersion($srcPath);
					$cachePath = COPIX_CACHE_PATH . 'copixhtmlheader/css/' . $srcPath . '-' . md5 (filemtime ($filePath) . CopixUrl::getRequestedProtocol().CopixUrl::getRequestedDomain ()) . '.css';
					if (!file_exists ($cachePath)) {
						CopixFile::write ($cachePath, $resource->fetch (false));
					}
					$filesConcat[$params['id']] = $cachePath;
				}
				$positionMaxConcat = max ($positionMaxConcat, $params['position']);

			// on ne peut pas concaténer ce fichier
			} else {
				$more = '';
				$position = $params['position'];
				unset ($params['position']);
				if (isset($params['comment'])) {
					$built[] = '<!-- '.$params['comment'].' -->';
					unset($params['comment']);
				}
				foreach ($params as $param_name => $param_value) {
					if($param_name != 'src'){
						if ($param_value === true) {
							$more .= $param_name.' ';
						} else if ($param_value !== false) {
							$more .= $param_name . '="' . $param_value . '" ';
						}
					}
				}
				$built[$position] = '<link rel="stylesheet" type="text/css" href="' . $params['src'] . '" ' . $more . ' />';
			}
		}

		if (count ($filesConcat) > 0) {
			foreach ($filesConcat as $id => $src) {
				self::addJSDOMReadyCode ('Copix.addConcatLink (\'' . $id . '\')');
			}
			$built[$positionMaxConcat] = '<link rel="stylesheet" type="text/css" href="' . CopixConcat::getURL ($filesConcat, 'text/css') . '"  />';
		}
		// Trie le tableau en fonction de l'ordre de priorités.
		ksort ($built);
		return implode ("\n", $built);
	}

	/**
	 * Récupération des liens vers les fichiers javascript
	 *
	 * @return string
	 */
	public static function getJSLink () {
		if (count (self::$_JSLink) == 0 || CopixAJAX::isAJAXRequest ()) {
			return '';
		}
		$built = array ();
		$concat = CopixConfig::instance ()->copixhtmlheader_concatJS;
		$filesConcat = array ();

		// pour faciliter la gestion de la priorité d'insertion, on remet le tableau dans l'ordre des priorités
		// de base le tableau a pour clef l'url du fichier, ce qui permet de facilement gérer les doublons
		$jsLinks = array ();
		foreach (self::$_JSLink as $src => &$params) {
			$params['src'] = $src;
			if (isset ($params['position'])) {
				while ((isset ($jsLinks[$params['position']]))) {
					$params['position']++;
				}
			} else {
				// array_pop génère une Only variable should be passed by reference si on passe directement le résultat de array_keys
				$keys = array_keys ($jsLinks);
				$params['position'] = (count ($jsLinks) == 0) ? 100 : array_pop ($keys) + 10;
			}
			$jsLinks[$params['position']] = $params;
		}
		ksort ($jsLinks);

		foreach ($jsLinks as $src => $params) {
			if (!$params['src']) {
				continue; // Quand un fichier n'est pas trouvé
			}
			// on peut concaténer ce fichier
			if ($concat && (!isset ($params['concat']) || $params['concat'])) {
				// adresse directe
				if (strpos ($params['src'], 'resource.php') === false) {
					$filesConcat[$params['id']] = substr( $params['src'], strpos( $params['src'], '/', 8) + strlen( CopixURL::getRequestedScriptPath () ) );
				// passage par resource.php
				} else {
					$resource = new CopixResourceFetcher ();
					$resource->setFromURL ($params['src']);
					$filesConcat[$params['id']] = $resource->getFilePath ();
				}
			
			// on ne peut pas concaténer ce fichier
			} else {
				unset ($params['position']);
				$more = '';
				if (isset ($params['comment'])) {
					$built[] = '<!-- '.$params['comment'].' -->';
					unset ($params['comment']);
				}
				foreach ($params as $param_name => $param_value) {
					if($param_name != 'src'){
						if ($param_value === true) {
							$more .= $param_name . ' ';
						} else if ($param_value !== false) {
							$more .= $param_name . '="' . $param_value . '" ';
						}
					}
				}
				$built[] = '<script type="text/javascript" src="' . $params['src'] . '" ' . $more . '></script>';
			}
		}

		if (count ($filesConcat) > 0) {
			foreach ($filesConcat as $id => $src) {
				self::addJSDOMReadyCode ('Copix.addConcatLink (\'' . $id . '\')');
			}
			$url = CopixConcat::getURL ($filesConcat, 'application/x-javascript', "\n\n", CopixConfig::instance ()->copixhtmlheader_concatCompressJS);
			$built[] = '<script type="text/javascript" src="' . $url . '"></script>';
		}
		return implode ("\n", $built);
	}

	/**
	 * supression de tous les éléments définis dans l'en tête HTML
	 * @return void
	 */
	public static function clear ($what){
		$cleanable = array ('CSSLink', 'Styles', 'JSLink', 'JSCode', 'Others');
		if (is_string ($what)) {
			$what = array ($what);
		}
		foreach ($what as $elem){
			if (in_array ($elem, $cleanable)){
				$name = '_'.$elem;
				self::$$name = array ();
			}
		}
	}

	/**
	 * Ajout d'une icone "favicone"
	 * @param string $pPicturePath le chemin de l'image
	 */
	public static function addFavIcon ($pPicturePath){
		self::$_favicon = $pPicturePath;
	}
	
	/**
	 * Retourne l'icone du site actuellement configurée
	 * @return string (null si non défini)
	 */
	public static function getFavIcon (){
		return self::$_favicon;
	}

	/**
	 * Retourne les balises meta de crédits
	 *
	 * @return string
	 */
	public static function getCredits (){
		return '<meta name="generator" content="Copix '.COPIX_VERSION.'"/>'."\n".'<meta name="author" content="CopixTeam" />'."\n";		
	}

	/**
	 * Demande le chargement de Mootools.
	 *
	 * @param array $pPlugins Liste de plugins à charger.
	 */
	public static function addJSFramework($pPlugins = null, $pCompatibility = false) {
		// Charge le noyau
		if(!isset(self::$_JSFrameworkAdded[self::MOOTOOLS])) {
			self::$_JSFrameworkAdded[self::MOOTOOLS] = true;

			// Initialise Mootools et l'identifiant de session
			if(!CopixAJAX::isAJAXRequest()) {
				
				$config = CopixConfig::instance();
				$includeFirebugLite = $config->copixhtmlheader_includeFirebugLite; 

				// Ajoute MooTools et FirebugLite				
				if($config->getMode() == CopixConfig::DEVEL) {
					// MooTools non compressé et FirebugLite normal
					if ($includeFirebugLite && CopixResource::exists ('js/firebuglite/firebug.js')) {
						self::addJSLink (_resource ('js/firebuglite/firebug.js'), array('id' => 'firebug_js'));
					}
					self::addJSLink (_resource ('js/mootools/mootools-devel.js'), array('id' => 'mootools_core_js', 'position' => 1));
				} else {
					// MooTools compressé et FirebugLite qui ne fait rien.
					if ($includeFirebugLite && CopixResource::exists ('js/firebuglite/firebugx.js')) {
						self::addJSLink (_resource ('js/firebuglite/firebugx.js'), array('id' => 'firebug_js'));
					}
					
					self::addJSLink (_resource ('js/mootools/mootools.js'), array('id' => 'mootools_core_js', 'position' => 1));
				}
				
				// Ajoute le framework JS spécifique de Copix
				self::addJSLink (_resource ('js/copix.js'), array('id' => 'copix_js', 'charset' => 'UTF-8', 'position' => 2));
				
				// Ajoute le code d'initialisation
				$urlBase = CopixUrl::get();
				self::addJSCode(
					sprintf('Copix = new CopixClass(%s);', CopixJSON::encode(array(
						'ajaxSessionId'      => CopixAJAX::getSessionId(),
						'module'             => CopixContext::get(),
						'urlBase'            => $urlBase,
						'urlHandler'         => $config->significant_url_mode,
						'cacheTemplate'		=> _request ('cacheTemplate', '1'),
						'resourceUrlBase'    => CopixResource::getResourceBaseUrl($urlBase, CopixTpl::getTheme(), CopixI18N::getLang(), CopixI18N::getCountry()),
					))),
					'copixajax_init',
					CopixHTMLHeader::DOMREADY_ALWAYS					
				);

			}
		}
		
		//Met en place les éléments de compatibilité si demandé
		if ($pCompatibility){
			$compatibilityId = 'compatibility_'.str_replace ('.', '_', $pCompatibility);
			if (is_array ($pPlugins)){
				$pPlugins = array_merge (array ($compatibilityId), $pPlugins);				
			}else{
				$pPlugins = array ($compatibilityId);
			}
		}

		// Charge les plugins
		if(is_array($pPlugins)) {
			foreach ($pPlugins as $pluginName){
				if(!isset(self::$_JSFrameworkAdded[$pluginName])) {
					self::$_JSFrameworkAdded[$pluginName] = true;

					$pluginId = 'mootools_plugin_'.$pluginName;
					$scriptId = $pluginId.'_js';
					$stylesheetId = $pluginId.'_css';

					if (is_readable (CopixUrl::getResourcePath ($path = 'js/mootools/plugins/'.$pluginName.'.js'))){
						self::addJSLink(_resource ($path), array("id"=>$scriptId));

					} elseif (is_readable (CopixUrl::getResourcePath ($path = 'js/mootools/plugins/'.$pluginName.'.js.php'))){
						self::addJSLink(_resource ($path), array("id"=>$scriptId));
					} else {
						$message = '[Mootools] Plugin '.$pluginName.' not found in js/mootools/plugins/';
						if(CopixConfig::instance()->getMode() == CopixConfig::DEVEL) {
							throw new CopixException ($message);
						} else {
							_log ($message, 'errors', CopixLog::EXCEPTION);
						}
					}

					if (CopixResource::exists ($path = 'js/mootools/css/'.$pluginName.'.css')) {
						self::addCssLink(_resource ($path), array("id"=>$stylesheetId));
					}
				}
			}
		}
	}
	
	
	/**
	 * Demande le chargement de jQuery.
	 *
	 * @param array $pPlugins Liste de plugins à charger.
	 */
	public static function addJQuery($pPlugins = null, $pVersion = false) {
		$config = CopixConfig::instance ();
		$production = ($config->getMode() == CopixConfig::PRODUCTION);
		// Charge le noyau
		if (!isset (self::$_JSFrameworkAdded[self::JQUERY])) {
			self::$_JSFrameworkAdded[self::JQUERY] = true;
			// Initialise Mootools et l'identifiant de session
			if (!CopixAJAX::isAJAXRequest ()) {
				$path = 'js/jquery/jquery';
				if ($pVersion) {
					$path .= '.'.$pVersion;
				}
				
				if($production) {
					$path .= '.min';
				}
				// Ajoute jQuery
				self::addJSLink (_resource ($path.'.js'), array ('id' => 'jquery_core_js', 'position' => 1));
				self::addJSLink (_resource ('js/jquery/plugins/copixhelper.js'), array ('id' => 'jquery_copix_helper_js'));
				
			}
		}
		
		// Charge les plugins
		if (is_array ($pPlugins)) {
			foreach ($pPlugins as $pluginName){
				if (!isset (self::$_JSFrameworkAdded[$pluginName])) {
					self::$_JSFrameworkAdded[$pluginName] = true;

					$pluginId = 'jquery_plugin_'.$pluginName;
					$scriptId = $pluginId.'_js';
					$stylesheetId = $pluginId.'_css';
					
					// Try to use minified plugin code, otherwise normal code, finally with js and php extension
					if ($production && is_readable (CopixUrl::getResourcePath ($path = 'js/jquery/plugins/'.$pluginName.'.min.js'))){
						self::addJSLink(_resource ($path), array ('id' => $scriptId));
					} elseif (is_readable (CopixUrl::getResourcePath ($path = 'js/jquery/plugins/'.$pluginName.'.js'))){
						self::addJSLink(_resource ($path), array ('id' => $scriptId));
					} elseif (is_readable (CopixUrl::getResourcePath ($path = 'js/jquery/plugins/'.$pluginName.'.js.php'))){
						self::addJSLink(_resource ($path), array ('id' => $scriptId));
					} else {
						$message = '[jquery] Plugin '.$pluginName.' not found in js/jquery/plugins/';
						if(CopixConfig::instance()->getMode() == CopixConfig::DEVEL) {
							throw new CopixException ($message);
						} else {
							_log ($message, 'errors', CopixLog::EXCEPTION);
						}
					}
					if ($production && CopixResource::exists ($path = 'js/jquery/css/'.$pluginName.'.min.css')) {
						self::addCssLink (_resource ($path), array ('id' => $stylesheetId));
					} elseif (CopixResource::exists ($path = 'js/jquery/css/'.$pluginName.'.css')) {
						self::addCssLink (_resource ($path), array ('id' => $stylesheetId));
					}
				}
			}
		}
	}
}
