<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Gérald Croes
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
* @package		copix
* @subpackage 	core
*/
class CopixSelector {
	/**
	 * Le descripteur Copix "complet"
	 */
	private $_selector;

	/**
	 * L'objet capable de décoder les informations à partir du descripteur
	 * @var CopixSelectorInterpretor
	 */
	private $_interpretor;

	/**
	 * Constructeur
	 */
	public function __construct ($pSelector, $pInterpretor){
		$this->_interpretor = $pInterpretor;
	}
	
	/**
	 * Récupération du chemin du sélecteur
	 * @return string
	 */
	public function getPath (){
		$parts = array ();
		
		//explosion de gauche / droite pour les éléments type service ou actiongroup
		$rightParts = explode ('::', $this->_selector);
		if (count ($rightParts) == 2){
			$parts['method']  = $rightParts[1];
			$leftPart = $rightParts[0];
		}else{
			$leftPart = $this->_selector;
		}
		
		//On détermine maintenant le type
		return $this->_interpretor->getPath ();
	}
}

/**
* @package		copix
* @subpackage 	core
*/
abstract class CopixSelectorInterpretor {
	abstract public function getPath ($selectorParts);
}

/**
* @package		copix
* @subpackage 	core
*/
class CopixVarSelectorInterpretor {

}

/**
* @package		copix
* @subpackage 	core
*/
class CopixClassSelectorInterpretor {
	public function getPath ($selectorParts){
		return CopixModule::getPath ($selectorParts['module']).'/classes/'.$selectorParts['element'].'.class.php';
	}
}

/**
* @package		copix
* @subpackage 	core
*/
class CopixTemplateSelectorInterpretor {
}

/**
* @package		copix
* @subpackage 	core
*/
class CopixResourceSelectorInterpretor {
}

/**
* @package		copix
* @subpackage 	core
*/
class CopixPluginSelectorInterpretor {
}

/**
 * permet de récupèrer un objet selecteur selon le type du selecteur
 * un selecteur dans copix permet de spécifier un fichier/composant à utiliser et le module dan
 * lequel il se trouve.
 * le format d'un selecteur : "type:module|fichier"
 * @package copix
 * @subpackage core
 */
class CopixSelectorFactory {
	/**
	 * Type module qui représente une ressource située dans un module
	 * 
	 * C'est le sélecteur par défaut si rien n'est spécifié
	 */
	const MODULE	= 'module:';
	
	/**
	 * Type fichier qui représente un chemin absolu
	 */
	const FILE		= 'file:';
	
	/**
	 * Type var qui représente des ressources situées dans le répertoire de variable de Copix 
	 */
	const VAR_PATH = 'var:';
	
	/**
	 * Type copix qui représente des ressources situées dans le répertoire de Copix lui même
	 */
	const COPIX = 'copix:';
	
	/*
	 * Type Resource qui représente un élément situé dans la partie WWW
	 */
	const RESOURCE = 'resource:';

	/**
	 * Récupération d'un sélecteur pour calculer les chemins de plugin
	 * Les plugins ne peuvent être situés que dans des modules
	 */
	public static function getPlugin ($pPluginId){
		$pPluginId = self::normalize ($pPluginId);
	}
	
	/**
	 * 
	 */
	public static function getClass ($pClassId){
		return self::_getSelector (self::normalize ($pClassId), new CopixClassSelectorInterpretor ());
	}
	
	/**
	 * Récupération d'un objet sélecteur à partir d'un sélecteur (chaine) normalisé
	 * @param 	string	$pNormalizedSelector	le sélecteur en chaine de caractère normalisé
	 * @param 	string	$pType					le type de sélecteur que l'on souhaites obtenir	
	 */
	private function _getSelector ($pNormalizedSelector, $pType){
		if (isset (self::$_selector[get_class ($pType)][$pNormalizedSelector])){
			return self::$_selector[get_class ($pType)][$pNormalizedSelector];
		}
		return $_selector[get_class ($pType)][$pNormalizedSelector] = new CopixSelector ($pNormalizedSelector, $pType);
	} 
	
	/**
	 * 
	 */
	private static function _createInterpretor ($pKindOfInterpretor){
		switch ($pKindOfInterpretor){
			case self::FILE:
				return new CopixFileSelectorInterpretor ();
			case self::MODULE:
				return new CopixModuleSelectorInterpretor ();
			case self::VAR_PATH:
				return new CopixVarSelectorInterpretor ();
		}
	}
	
	/**
	 * Normalisation d'un sélecteur avec [type:]module|sélecteur
	 * @param	string	$pSelector	le sélecteur à normaliser
	 * @return string
	 */
	public static function normalize ($pSelector){
		//On test le cache de normalisation
		if (isset (self::$_normalizedSelector[CopixContext::get ()][$pSelector])){
			return self::$_normalizedSelector[CopixContext::get ()][$pSelector];
		}
		
		if ((($colon = strpos ($pSelector, ':')) !== false) 
		     && (strpos ($pSelector, '::') !== $colon)) {
			$selectorKind    = substr ($pSelector, 0, $colon+1);
			$selectorContent = substr ($pSelector, $colon+1);
		}else{
			$selectorKind    = self::MODULE;
			$selectorContent = $pSelector;
		}

		//Si le sélecteur est de type module, on souhaites mettre le contexte courant s'il n'est pas 
		//forcé
		if ($selectorKind === self::MODULE){
			if (count ($selectorParts = explode ('|', $selectorContent)) != 2){
				$selectorContent = CopixContext::get ().'|'.$selectorContent;
			}
		}

		//retourne le sélecteur final après stockage dans le cache de normalisation.
		self::$_normalizedSelector[CopixContext::get ()][$pSelector] = $selectorKind.$selectorContent;
		return self::$_normalizedSelector[CopixContext::get ()][$pSelector];
	}
	
	/**
	 * Liste des sélecteurs déjà normalisés
	 */
	private static $_normalizedSelector = array ();

	
	/**
	 * Liste des sélecteurs déjà créés en mémoire
	 */
	static private $_selector = array ();

	/**
	 * On ne veut pas que cette classe soit instanciée
	 */
	private function __construct (){}

	/**
	 * Création d'un sélecteur
	 * @param string $id l'identifiant Copix de l'élément.
	 * @return CopixFileSelector
	 */
	static public function create ($id, $type = null){
		if (substr ($id, 0, 5) !== 'file:'){
			$id = strtolower ($id);
		}
		$context = CopixContext::get ();

		//Regarde si cela existe déjà dans le cache
		if (isset (self::$_selector[$context][$id])){
			return self::$_selector[$context][$id];
		}

		if (($colon = strpos ($id, ':')) !== false) {
			switch (substr ($id, 0, $colon)) {
				case 'copix':
					self::$_selector[$context][$id] = new CopixCopixFileSelector ($id);
					break;

				case 'file':
					self::$_selector[$context][$id] = new CopixFileFileSelector ($id);
					break;

				case 'var':
					self::$_selector[$context][$id] = new CopixVarFileSelector($id);
					break;

				default:
					throw new CopixException (_i18n ('copix:copix.error.unknownSelector', $id));
			}
		}else{
			if (strpos($id, '|') === false){
				$fullId = $context.'|'.$id;
			}else{
				$fullId = $id;
			}

			//on regarde dans le cache avec l'identifiant complet
			if (isset (self::$_selector[$context][$fullId])){
				self::$_selector[$context][$id] = self::$_selector[$context][$fullId];
				return self::$_selector[$context][$id];
			}
			self::$_selector[$context][$id] = new CopixModuleFileSelector($fullId);
			self::$_selector[$context][$fullId] = self::$_selector[$context][$id];
		}
		return self::$_selector[$context][$id];
	}
}

/**
 * Classe de base des selecteurs
 * @package copix
 * @subpackage core
 */
abstract class CopixFileSelector {
	var $type      = null;
	var $typeValue = null;
	var $fileName  = null;

	/**
	 * Le chemin de la ressource
	 * @var string
	 */
	private $_path = null;

	/**
	 * Le chemin surchargé de la ressource
	 * @var string
	 */
	private $_overloadedPath = null;

	/**
	 * Le sélecteur complet de la ressource
	 * @var string
	 */
	private $_selector = null;

	/**
	 * Le qualificateur Copix de l'élément
	 * @var string
	 */
	private $_qualifier = null;

	/**
	 * Récupère le chemin de la ressource, gère un cache d'appel aux méthodes des fil
	 * @return string le chemin
	 */
	public function getPath ($directory = ''){
		if (!isset ($this->_path[$directory])){
			$this->_path[$directory] = $this->_getPath ($directory);
		}
		return $this->_path[$directory];
	}
	abstract protected function _getPath ($directory);

	/**
	 * Récupère le chemin surchargé d'une ressource, gère un cache d'appel aux méthodes des fils.
	 * @return string le chemin surchargé
	 */
	public function getOverloadedPath ($directory=''){
		if (!isset ($this->_overloadedPath[$directory])){
			$this->_overloadedPath[$directory] = $this->_getOverloadedPath ($directory);
		}
		return $this->_overloadedPath[$directory];
	}
	protected function _getOverloadedPath ($directory){return null;}

	/**
	 * Ré&cupération du sélecteur avec implémentation d'un cache
	 */
	public function getSelector (){
		if (!isset ($this->_selector)){
			$this->_selector = $this->_getSelector ();
		}
		return $this->_selector;
	}
	abstract protected function _getSelector ();

	/**
	 * Récupèration du qualificateur avec implémentation d'un cache
	 */
	public function getQualifier (){
		if (!isset ($this->_qualifier)){
			$this->_qualifier = $this->_getQualifier ();
		}
		return $this->_qualifier;
	}
	abstract protected function _getQualifier ();
}

/**
 * Implémente les selecteurs de fichiers/composant de modules
 * @package copix
 * @subpackage core
 */
class CopixModuleFileSelector extends CopixFileSelector {
	/**
	 * Le nom du module attaché ('' ou null si projet)
	 * @var string
	 */
	var $module = "default";

	/**
	 * Constructeur
	 * @param string $selector le sélecteur Copix "module|element"
	 */
	function __construct($selector){
		$this->type = 'module';
		//ok, I don't use regexp here cause it's 0,40 ms slower :-)
		$tab = explode ('|', $selector);
		if (($counted = count ($tab)) > 1){
			$this->module = $tab[0] == '' ? "default" : $tab[0];
			$this->fileName=$tab[1];
		}else if ($counted == 1){
			$this->module = CopixContext::get ();
			$this->fileName = $tab[0];
		}else{
			throw new Exception (_i18n ('copix:copix.error.fileselector.invalidSelector', $selector));
		}
		$this->module = strtolower ($this->module);
	}

	/**
	 * Indique le chemin d'accès à la ressource.
	 * @param string $directory
	 */
	protected function _getPath ($directory){
		return CopixModule::getPath ($this->module).$directory;
	}

	/**
	 * Retourne le chemin surchargé pour l'élément.
	 * @todo chemin surchargé des ressources
	 */
	protected function _getOverloadedPath ($directory){
		return $directory.$this->module.'/';
	}

	/**
	 * Récupère le sélecteur Copix complet de l'élément
	 * @return string
	 */
	protected function _getSelector(){
		return $this->module.'|'.$this->fileName;
	}

	/**
	 * Récupère la partie qualifier de l'élément
	 * @return string
	 */
	protected function _getQualifier (){
		return $this->module.'|';
	}
}

/**
 * Implémente les selecteurs de plugins
 * @package copix
 * @subpackage core
 */
class CopixPluginFileSelector extends CopixFileSelector {
	/**
	 * name of the plugin (ID)
	 */
	var $pluginName = null;

	/**
	 * Module the plugin belongs to
	 */
	var $module = "default";

	/**
	 * Constructeur
	 */
	public function __construct($selector){
		$this->type='plugin';
		$match=null;
		if(preg_match("/^plugin:([_0-9a-zA-Z-]*)\/(([_0-9a-zA-Z-]*)\|)?(.*)$/",$selector,$match)){
			if($match[2]!=''){
				$this->module=$match[3];
			}
			$this->pluginName=$match[1];
			$this->fileName=$match[4];
		}else{
			throw new Exception (_i18n ('copix:copix.error.fileselector.invalidSelector', $selector));
		}
	}

	/**
	 * gets the path relative to the selector
	 */
	protected function _getPath($directory){
		return CopixModule::getPath ($this->module).COPIX_PLUGINS_DIR.$this->pluginName.'/';
	}

	/**
	 * gets the full qualified selector
	 */
	protected function _getSelector(){
		return 'plugin:'.$this->module.'|'.$this->pluginName;
	}

	/**
	 * gets the qualifier (without the element id itself)
	 */
	protected function _getQualifier (){
		return 'plugin:'.$this->module.'|';
	}
}

/**
 * Implémente les selecteurs de fichiers/composant du noyau copix (dans COPIX_CORE_PATH)
 * @package copix
 * @subpackage core
 */
class CopixCopixFileSelector extends CopixFileSelector {
	var $module='[copix]';
	public function __construct($selector){
		$this->type = 'copix';
		if (($pos = strpos ($selector, 'copix:')) === 0){
			$this->fileName = substr ($selector, 6);//we know 'copix:' len is 6.
		}else{
			throw new Exception (_i18n ('copix:copix.error.fileselector.invalidSelector', $selector));
		}
	}
	protected function _getPath($directory){
		return COPIX_PATH.$directory;
	}
	protected function _getSelector(){
		return 'copix:'.$this->fileName;
	}
	protected function _getQualifier () {
		return 'copix:';
	}
}

/**
 * Sélecteur pour le répertoire COPIX_VAR_PATH
 * @package copix
 * @subpackage core
 */
class CopixVarFileSelector extends CopixFileSelector {
	public function __construct ($selector){
		$this->type = 'var';
		if (($pos = strpos ($selector, 'var:')) === 0){
			$this->fileName = substr ($selector, 4);//we know 'var:' len is 4.
		}else{
			throw new Exception (_i18n ('copix:copix.error.fileselector.invalidSelector', $selector));
		}
	}
	protected function _getPath($directory){
		return COPIX_VAR_PATH.$directory;
	}
	protected function _getSelector(){
		return 'var:'.$this->fileName;
	}
	protected function _getQualifier () {
		return 'var:';
	}
}

/**
 * Sélecteur capable de prendre en charge les chemins absolus de fichier (file:)
 * @package copix
 * @subpackage core
 */
class CopixFileFileSelector extends CopixFileSelector {
	/**
	 * Le nom du répertoire que l'on précalcule à la construction du sélecteur
	 */
	var $dirName = null;

	/**
	 * Constructeur
	 */
	public function __construct ($selector){
		$this->type = 'file';
		if (($pos = strpos ($selector, 'file:')) === 0){
			$this->fileName = basename (substr ($selector, 5));
			$this->dirName  = dirname (substr ($selector, 5)).'/';
		}else{
			throw new Exception (_i18n ('copix:copix.error.fileselector.invalidSelector', $selector));
		}
	}

	/**
	 * récupère le chemin du fichier (sans son nom
	 * @param string $pDirectory n'est pas utilisé ici, comme le sélecteur utilise des noms absolus
	 * @return string le chemin du fichier sans son nom
	 */
	protected function _getPath ($pDirectory){
		return $this->dirName;
	}

	/**
	 * récupère le sélecteur complet 
	 */
	protected function _getSelector(){
		return 'file:'.$this->dirName.$this->fileName;
	}

	/**
	 * retourne le qualificateur
	 * @return string
	 */
	protected function _getQualifier () {
		return $this->type;
	}
}
?>