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

	/**
	 * Tableau de liens sur des feuilles de style
	 * @var array
	 */
	static private $_CSSLink = array ();

	/**
	 * Styles CSS définis
	 * @var array
	 */
	static private $_Styles  = array ();

	/**
	 * Tableau de liens sur des fichiers javascript à inclure
	 * @var array
	 */
	static private $_JSLink  = array ();

	/**
	 * Code Javascript à ajouter.
	 * @var array
	 */
	static private $_JSCode  = array ();

	/**
	 * Autre en-têtes à ajouter.
	 * @var array
	 */
	static private $_Others  = array ();

	/**
	 * A-t-on déjà demandé le framework Javascript ?
	 *
	 * @var boolean
	 */
	static private $_JSFrameworkAdded = array();

	/**
	 * Construit un identifiant à partir d'une URL.
	 *
	 * @param string $pUrl URL
	 * @return string Un identifiant bon à utiliser dans une balise HTML.
	 */
	static private function _buildId ($pUrl) {
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
			self::$_Others[] = $content;
		}else{
			self::$_Others[$key] = $content;
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
	public static function addJSCode ($pCode, $pId = null, $pDomReady = self::DOMREADY_AUTO){
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
			self::$_JSCode[$pDomReady][] = $pCode;
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

	/**
	 * récupère le contenu à rajouter dans l'en tête
	 * @return string
	 */
	public static function get (){
		return self::getCSSLink () . "\n" . self::getJSLink () . "\n" . self::getStyles ()."\n" . self::getJSCode ().self::getOthers ();
	}

	/**
	 * Récupération de la partie d'en tête "autres"
	 * @return string
	 */
	public static function getOthers (){
		return implode ("\n", self::$_Others);
	}

	/**
	 * Génère un tag script pour insérer du Javascript.
	 *
	 * @param array $pCode Code Javasscript.
	 * @return string Code XHTML pour l'inclusion du javascript.
	 */
	static private function _buildJSTag ($pCode) {
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
	static private function _buildJSOnEvent ($pElement, $pEvent, $pCode) {
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

		// Si le framework JS est chargé, ajoute l'execution des événéments mis en queue
		if(isset(self::$_JSFrameworkAdded['*core*'])) {
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
	public static function getCSSLink (){
		if(count (self::$_CSSLink) == 0 || CopixAJAX::isAJAXRequest()) {
			return '';
		}
		$built = array ();
		foreach (self::$_CSSLink as $src=>$params){
			$more = '';
			if(isset($params['comment'])) {
				$built[] = '<!-- '.$params['comment'].' -->';
				unset($params['comment']);
			}
			foreach ($params as $param_name=>$param_value){
				if($param_value === true) {
					$more .= $param_name.' ';
				} elseif($param_value !== false) {
					$more .= $param_name.'="'.$param_value.'" ';
				}
			}
			$built[] = '<link rel="stylesheet" type="text/css" href="'.$src.'" '.$more.' />';
		}
		return implode ("\n", $built);
	}

	/**
	 * Récupération des liens vers les fichiers javascript
	 * @return string <head> En tête HTML
	 */
	public static function getJSLink (){
		if (count(self::$_JSLink) == 0 || CopixAJAX::isAJAXRequest()) {
			return '';
		}
		$built = array ();
		foreach (self::$_JSLink as $src=>$params){
			$more = '';
			if(isset($params['comment'])) {
				$built[] = '<!-- '.$params['comment'].' -->';
				unset($params['comment']);
			}
			foreach ($params as $param_name=>$param_value){
				if($param_value === true) {
					$more .= $param_name.' ';
				} elseif($param_value !== false) {
					$more .= $param_name.'="'.$param_value.'" ';
				}
			}
			$built[] = '<script type="text/javascript" src="'.$src.'" '.$more.'></script>';
		}
		return implode ("\n", $built);
	}

	/**
	 * supression de tous les éléments définis dans l'en tête HTML
	 * @return void
	 */
	public static function clear ($what){
		$cleanable = array ('CSSLink', 'Styles', 'JSLink', 'JSCode', 'Others');
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
		self::addOthers ('<link rel="icon" href="'.$pPicturePath.'" />');
	}


	/**
	 * Demande le chargement de Mootools.
	 *
	 * @param array $pPlugins Liste de plugins à charger.
	 */
	static public function addJSFramework($pPlugins = null) {

		// Charge le noyau
		if(!isset(self::$_JSFrameworkAdded['*core*'])) {
			self::$_JSFrameworkAdded['*core*'] = true;

			// Initialise Mootools et l'identifiant de session
			if(!CopixAJAX::isAJAXRequest()) {

				// Ajoute MooTools et FirebugLite				
				if(CopixConfig::instance()->getMode() == CopixConfig::DEVEL) {
					// MooTools non compressé et FirebugLite normal
					self::addJSLink (_resource ('js/firebuglite/firebug.js'), array('id' => 'firebug_js'));
					self::addJSLink (_resource ('js/mootools/mootools-devel.js'), array('id' => 'mootools_core_js'));
				} else {
					// MooTools compressé et FirebugLite qui ne fait rien.
					self::addJSLink (_resource ('js/firebuglite/firebugx.js'), array('id' => 'firebug_js'));
					self::addJSLink (_resource ('js/mootools/mootools.js'), array('id' => 'mootools_core_js'));
				}
				
				// Ajoute le framework JS spécifique de Copix
				self::addJSLink (_resource ('js/copix.js'), array('id' => 'copix_js', 'charset' => 'UTF-8'));
				
				// Ajoute le code d'initialisation
				$urlBase = CopixUrl::get();
				self::addJSCode(
					sprintf('Copix = new CopixClass(%s);', CopixJSON::encode(array(
						'ajaxSessionId'      => CopixAJAX::getSessionId(),
						'module'             => CopixContext::get(),
						'urlBase'            => $urlBase,
						'resourceUrlBase'    => CopixResource::getResourceBaseUrl($urlBase, CopixTpl::getTheme(), CopixI18N::getLang(), CopixI18N::getCountry()),
					))),
					'copixajax_init',
					CopixHTMLHeader::DOMREADY_ALWAYS					
				);

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

					if (file_exists (CopixUrl::getResourcePath ($path = 'js/mootools/plugins/'.$pluginName.'.js'))){
						self::addJSLink(_resource ($path), array("id"=>$scriptId));

					} elseif (file_exists (CopixUrl::getResourcePath ($path = 'js/mootools/plugins/'.$pluginName.'.js.php'))){
						self::addJSLink(_resource ($path), array("id"=>$scriptId));

					} else {
						throw new CopixException ('[Mootools] Plugin '.$pluginName.' not found in '.$pluginPath);
					}

					if (file_exists (CopixUrl::getResourcePath ($path = 'js/mootools/css/'.$pluginName.'.css'))){
						self::addCssLink(_resource ($path), array("id"=>$stylesheetId));
					}
				}
			}
		}
	}

}
?>
