<?php
/**
 * @package copix
 * @subpackage tpl
 * @author Croës Gérald, Jouanneau Laurent, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Moteur de template générique
 * Offre une couche d'abstraction pour la manipulation de moteur de templates
 * Supporte les templates PHP (*.ptpl et *.php) et Smarty (*.tpl)
 * 
 * @package copix
 * @subpackage tpl
 */
class CopixTpl {
	/**
	 * Thème configuré, false si aucune base configurée, null si aucun thème configuré en base ou string pour les autres
	 * 
	 * @var mixed
	 */
	private static $_theme = false;
	
	/**
	 * Cache des chemins de templates
	 *
	 * @var array
	 */
	private static $_tplFilePathCache = array ();
	
	/**
	 * Variables assignées au template
	 * 
	 * @var array
	 */
	private $_vars = array ();

	/**
	 * Liste des répertoire de Smarty
	 * 
	 * @var array
	 */
	private static $_smartyPluginPath = false;
	
	
	/**
	 * Fichier template (.php, .ptpl ou .tpl)
	 * 
	 * @var string
	 */
	private $_templateFile;
	
	
	/**
	 * Retourne le chemin physique vers le thème demandé, ou false si ce thème n'existe pas 
	 * @see CopixTheme::getPath
	 */
	public static function getThemePath ($pTheme = null) {
		return CopixTheme::getPath ($pTheme);
	}
	
	/**
	 * Assignation d'une variable au template
	 * 
	 * @param string $pName Nom de la variable
	 * @param mixed $pValue Valeur de la variable
	 */
	public function assign ($pName, $pValue) {
		$this->_vars[$pName] = $pValue;
	}

	/**
	 * Assignation d'une variable au template par réfèrence
	 * 
	 * @param string $pName Nom de la variable
	 * @param mixed $pValue Valeur de la variable
	 */
	public function assignByRef ($pName, &$pValue) {
		$this->_vars[$pName] = &$pValue;
	}

	/**
	 * Assignation d'une zone à une variable de template
	 * 
	 * @param string $pName Nom de la variable
	 * @param string $pZoneName Nom de la zone
	 * @param array $pParams Paramètres de la zone 
	 */
	public function assignZone ($pName, $pZoneName, $pParams = array ()) {
		$this->_vars[$pName] = CopixZone::process ($pZoneName, $pParams);
	}

	/**
	 * Assignation du contenu d'un template à une variable d'un template
	 * 
	 * @param string $pName Nom de la variable
	 * @param string $pTemplate Template à utiliser
	 * @param array $pParams Paramètres à assigner au template $pTemplate
	 */
	public function assignTpl ($pName, $pTemplate, $pParams = array ()) {
		$tpl = new CopixTpl ();
		$tpl->_vars = $pParams;
		$this->_vars[$pName] = $tpl->fetch ($pTemplate);
	}

	/**
	 * Indique si la variable est assignée
	 * 
	 * @param string $pName Nom de la variable
	 * @return boolean
	 */
	public function isAssigned ($pName) {
		return isset ($this->_vars[$pName]);
	}

	/**
	 * Retourne une référence sur la variable assignée, ou null si elle n'existe pas
	 * 
	 * @param string $pName Nom de la variable
	 * @return mixed
	 */
	public function &getAssigned ($pName) {
		if ($this->isAssigned ($pName)) {
			return $this->_vars[$pName];
		}
		$return = null;		
		return $return;			
	}

	/**
	 * Effecute un echo du template parsé
	 * 
	 * @param string $pTemplate Template à utiliser
	 */
	public function display ($pTemplate) {
		echo $this->fetch ($pTemplate);
	}

	/**
	 * Retourne le template parsé
	 * 
	 * @param string $pTemplate Template à utiliser
	 * @return string
	 * @throws CopixException Template non trouvé
	 */
	public function fetch ($pTemplate) {
		if (!$this->_prepareTpl ($pTemplate)) {
			throw new CopixException (_i18n ('copix:copix.error.unfounded.template', $pTemplate));
		}
		$pushed = $this->_pushContext ($pTemplate);

		if ($this->isSmarty ($this->_templateFile)) {
			$toReturn = $this->_smartyPass ($this->_templateFile);
			if ($pushed) {
				CopixContext::pop ();
			}
			return $toReturn;
		}

		//déclare les variables locales pour le template.
		extract ($this->_vars);
		ob_start ();
		include ($this->_templateFile);
		$toReturn = ob_get_contents ();
		ob_end_clean ();
		if ($pushed) {
			CopixContext::pop ();
		}
		return $toReturn;
	}
	
	/**
	 * Vide le cache de chemins des templates
	 */
	public static function clearFilePathCache () {
		self::$_tplFilePathCache = array ();
	}
	
	/**
	 * Retourne le chemin physique du template $pTemplate
	 * 
	 * @param string $pTemplate Template à utiliser
	 * @param string $pTheme le theme à utiliser
	 * @return string
	 */
	public static function getFilePath ($pTemplate, $pTheme = false) {
        $cache = (strpos ($pTemplate, '|') !== false || strpos ($pTemplate, 'copix:') !== false || strpos ($pTemplate, 'file:') !== false) ? $pTemplate : CopixContext::get () . '|' . $pTemplate;
        
		if (isset (self::$_tplFilePathCache[$cache])) {
			return self::$_tplFilePathCache[$cache];
		}
		
		// Using a selector to find out the fileName
		$fileSelector = CopixSelectorFactory::create ($cache);
		$fileName = $fileSelector->fileName;
		$config = CopixConfig::instance ();
		$toReturn = false;
		
		// On a donné un chemin complet direct, on retourne directement
		if ($fileSelector->type !== 'module') {
			if (is_readable ($templateFilePath = $fileSelector->getPath () . $fileName)) {
				$toReturn = $templateFilePath;
			}
		} else {
			$toReturn = CopixResource::findThemeTemplate (
				$fileSelector->fileName, 
				$fileSelector->module,
				$fileSelector->getPath (),
				$pTheme ? $pTheme : self::getTheme (),
				$config->i18n_path_enabled,
				CopixI18N::getLang (),
				CopixI18N::getCountry()
			);
		}
		
		// Met en cache le résultat
		self::$_tplFilePathCache[$cache] = $toReturn;
		
		return $toReturn;
	}

	/**
	 * Préparation du chemin complet du fichier template en fonction de son identifiant
	 * 
	 * @param string $pTemplate Template dont on veut le chemin
	 * @return string
	 */
	private function _prepareTpl ($pTemplate) {
		return $this->_templateFile = $this->getFilePath ($pTemplate);
	}

	/**
	 * Passage du traitement à smarty, aprés inclusion si nécessaire
	 * 
	 * @param string $pTemplate Template à utiliser
	 * @return string
	 */
	private function _smartyPass ($pTemplate) {
		// Smarty gère les variables qui n'existent pas en masquant les notices.
		// Comme le error handler de Copix ne tient pas compte de la valeur configurée dans error_reporting (), 
		// on force la variable E_NOTICE à "aucune réaction" le temps d'afficher le template
		$config = CopixConfig::instance ();
		if ($config->copixerrorhandler_enabled){
			$oldNoticeValue = $config->copixerrorhandler_actions[E_NOTICE];
			$config->copixerrorhandler_actions[E_NOTICE] = new CopixErrorHandlerAction (false, null);		
		} 

		$tpl = $this->_createSmartyTpl ();
		// dirty, because we use private member, but improves speed
		$tpl->_tpl_vars = $this->_vars;
		$toReturn = $tpl->fetch ('file:' . $pTemplate);

		// restauration de la valeur originale pour les notices
		if ($config->copixerrorhandler_enabled){
			$config->copixerrorhandler_actions[E_NOTICE] = $oldNoticeValue;
		}		
		return $toReturn;
	}

	/**
	 * Création d'un objet de type Smarty configuré pour Copix
	 * 
	 * @return Smarty
	 */
	private function _createSmartyTpl () {
		$config = CopixConfig::instance ();
		Copix::RequireOnce (COPIX_SMARTY_PATH . 'Smarty.class.php');
		$tpl = new Smarty ();
		$tpl->compile_dir = COPIX_CACHE_PATH . 'templates/';
		$tpl->compile_check = $config->compile_check;
		if (CopixAJAX::isAJAXRequest ()) {
			$tpl->force_compile = (isset ($_SERVER['HTTP_X_COPIX_AJAX_CACHE_TEMPLATE']) && $_SERVER['HTTP_X_COPIX_AJAX_CACHE_TEMPLATE'] == 0) ? true : $config->force_compile;
		} else {
			$tpl->force_compile = !_request ('cacheTemplate', !$config->force_compile);
		}
		$tpl->caching = $config->template_caching;
		$tpl->use_sub_dirs = $config->template_use_sub_dirs;
		$tpl->cache_dir = COPIX_CACHE_PATH . 'html/templates/';
		$tpl->plugins_dir = self::_getSmartyPluginPathList ();
		return $tpl;
	}
	
	
	/**
	 * Charge la liste des répertoires des plugins Smarty.
	 * 
	 * Si le fichier de cache existe et que CopixConfig::instance ()->force_compile 
	 * est faux, on le charge. Sinon, on recrée la liste.
	 * 
     * @param boolean $pForceReload Force une recréation de la liste
	 */
	private static function _getSmartyPluginPathList ($pForceReload = false) {
		if (!$pForceReload && self::$_smartyPluginPath !== false){
			return self::$_smartyPluginPath;
		}
		
		$conf = CopixConfig::instance ();
		
		// Récupère la liste des répertoires
		$cacheFile = self::_getCompiledFileName ();
		
		
		if (!$pForceReload && is_readable ($cacheFile) && !$conf->force_compile) {
			// Depuis le fichier de cache
			self::$_smartyPluginPath = self::_loadPHPSmartyPathCacheFromFile($cacheFile);
		} else {
			// Recréation de la liste
			self::$_smartyPluginPath =  array ('plugins', COPIX_PATH . COPIX_SMARTYPLUGIN_DIR);
			foreach (CopixModule::getList () as $module ){
				if (is_dir($path = CopixModule::getPath($module).COPIX_SMARTYPLUGIN_DIR)) {
					self::$_smartyPluginPath [] = $path;
				}
			}
			
			self::_writeInPHPCache (self::$_smartyPluginPath);
		}
		
		return self::$_smartyPluginPath;
	}
	
	/* Nom du fichier de cache pour repertoire smarty
     * @return string	
     */
    private static function _getCompiledFileName (){
        return  COPIX_CACHE_PATH.'smartypluginpath.php';
    }

	/**
	 * Ecriture d'un fichier PHP dans lequel existera un tableau associatif (nommodule=>chemin)
	 * @param array   $arSmartyPluginPath   le tableau que l'on souhaites écrire.
	 * @param boolean $pRestricted Si arModulesPath ne concerne que les modules installés
	 */
	private static function _writeInPHPCache ($arSmartyPluginPath) {
		$generator = new CopixPHPGenerator ();
		$PHPString = $generator->getPHPTags ($generator->getVariableDeclaration ('$arSmartyPluginPath', $arSmartyPluginPath));
		CopixFile::write (self::_getCompiledFileName (), $PHPString);
	}
	
	/**
	 * Charge la liste des repertoires de plugin smarty depuis un fichier de cache.
	 *
	 * @param string $pFilePath
	 * @return array Liste des modules
	 */
	private static function _loadPHPSmartyPathCacheFromFile ($pFilePath) {
		include ($pFilePath);
		return $arSmartyPluginPath;	
	}
	
	
	/**
	 * Indique si le template est de type Smarty (s'il porte l'extention .tpl)
	 * 
	 * @param string $pTemplate Template dont on veut savoir le type
	 * @return boolean
	 */
	public function isSmarty ($pTemplate) {
		$ext = substr ($pTemplate, strrpos ($pTemplate, '.'));
		return (($ext !== '.ptpl') && ($ext !== '.php'));
	}

	/**
	 * Retourne une référence de la liste des variable déjà assignées
	 * 
	 * @return array
	 */
	public function &getTemplateVars () {
		return $this->_vars;
	}
	
	/**
	 * Assigne en un seul appel plusieurs variables
	 * 
	 * @param array $pVariables Variables à assigner, forme : array ('var' => 'value')
	 */
	public function assignTemplateVars ($pVariables) {
		$this->_vars = array_merge ($this->_vars, $pVariables);
	}
	
	/**
	 * Si le template provient d'un module, définit le contexte dans ce module, et indique si le contexte a été changé ou non
	 * 
	 * @param string $pTemplate Nom du template
	 * @return boolean
	 */
	private function _pushContext ($pTemplate){
		try {
			$tpl = CopixSelectorFactory::create ($pTemplate);
			if ($tpl->type == 'module') {
				CopixContext::push ($tpl->module);
				return true;
			}
		} catch (Exception $e) {
			return false;			
		}
	}
	
	/**
	 * Retourne le thème courant
	 * Va chercher dans la config default|publicTheme (string ou null si aucun thème n'est configuré en base) si aucun thème n'est configuré
	 * Retourne false si aucun thème n'est configuré et si il n'y a pas de profil de base de données par défaut
	 * 
	 * @return mixed
	 */
	public static function getTheme () {
		if (self::$_theme === false) {
			self::$_theme = CopixConfig::get ('default|publicTheme');
		}
		return self::$_theme;
	}
	
	/**
	 * Retourne le nom du thème, 'default' si aucun thème n'est définit
	 *
	 * @return string
	 */
	public static function getThemeName () {
		// self::getTheme () peut retourner false (par défaut), null (aucun thème définit en base) ou une string
		return (($theme = self::getTheme ()) != '') ? $theme : 'default';
	}

	/**
	 * Définit le thème à utiliser et retourne son nom
	 * 
	 * @param string $pTheme Thème à utiliser
	 * @return string
	 */
	public static function setTheme ($pTheme) {
		return self::$_theme = $pTheme;
	}
	
	/**
	 * Retourne la liste des thèmes
	 * 
	 * @see CopixTheme::getList
	 */
	public static function getThemesList ($pGetDefaultTheme = false) {
		return CopixTheme::getList ($pGetDefaultTheme);
	}
	
	/**
	 * Retourne les informations d'un thème, ou false si le thème n'existe pas
	 * @see CopixTheme::getInformations 
	 */
	public static function getThemeInformations ($pTheme) {
		return CopixTheme::getInformations ($pTheme);
	}

	/**
	 * Retourne les templates dans un module avec l'extension donnée
	 * @see CopixTplOperations::find 
	 */
	public static function find ($pModuleName, $pExtension) {
		return CopixTplOperations::find ($pModuleName, $pExtension);
	}
	
	/**
	 * Retourne le process d'une balise "PHP" située dans utils/copix/taglib/
	 * Si le tag est de la forme module|tag, on cherchera le tag dans le repertoire taglib du module
	 * 
	 * @param string $pTagName Nom de la balise que l'on souhaites lancer
	 * @param mixed $pParams Paramètres qui ont étés envoyés à la balise
	 * @param string $pContent ???
	 * @return string
	 * @throws CopixTemplateTagException Fichier .templatetag.php non trouvé, code CopixTemplateTagException::FILE_NOT_FOUND
	 * @throws CopixTemplateTagException Classe non trouvée, code CopixTemplateTagException::CLASS_NOT_FOUND 
	 */
    public static function tag ($pTagName, $pParams = array (), $pContent = null){
    	$pTagName = CopixSelectorFactory::purge ($pTagName);

    	$className = 'TemplateTag'.$pTagName;
	    $tag = new $className ($pParams);
	    return $tag->process ($pContent);
    } 
}