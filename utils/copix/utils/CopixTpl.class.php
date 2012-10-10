<?php
/**
* @package		copix
* @subpackage	core
* @author		Croës Gérald, Jouanneau Laurent
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Moteur de template générique
* Offre une couche d'abstraction pour la manipulation de moteur de templates
* Supporte les templates PHP (*.ptpl) et Smarty (*.tpl)
* @package copix
* @subpackage core
*/
class CopixTpl {
	/**
	 * Théme par défaut
	 */
	static private $_theme = false;
	
    /**
    * Tableau associatif des variables déjà assignées au template
    * @var   array
    */
    var $_vars = array ();

    /**
    * fichier du template
    * @var string
    */
    var $templateFile;

    /**
    * Assignation d'une variable au conteneur.
    * @param string  $varName    nom de la variable
    * @param mixed   $varValue   valeur de la variable
    */
    public function assign ($varName, $varValue){
        $this->_vars[$varName] = $varValue;
    }

    /**
    * Assignation d'une variable par réfèrence.
    * @param string $varName nom de la variable
    * @param mixed $varData les données à assigner à la variable
    */
    public function assignByRef ($varName, & $varData){
        $this->_vars[$varName] = & $varData;
    }

    /**
    * Assignation d'une zone à une variable de template
    * @param string $pVarName le nom de la variable template
    * @param string $pZoneName le nom de la zone à utiliser
    * @param array $pArParams les paramètres à donner à la zone. 
    */
    public function assignZone($pVarName, $pZoneName, $pArParams=array()){
        $this->_vars[$pVarName] = CopixZone::process ($pZoneName, $pArParams);
    }

    /**
    * Assignation du contenu d'un template à une variable d'un template
    * @param string $pVarName le nom de la variable de template 
    * @param string $TplId l'identifiant de template
    * @param array $pArParams le tableau de paramètres (variables) à assigner au template destiné
    *   à être affiché dans $pVarName 
    */
    public function assignTpl ($pVarName, $pTplId, $pArParams=array()){
        $tpl = new CopixTpl();
        $tpl->_vars = $pArParams;
        $this->_vars[$pVarName] =  $tpl->fetch($pTplId);
    }

    /**
    * Regarde si la variable $pVarName est assignée ou non.
    * @param string  $varName    nom de la variable
    * @return    boolean indique si variable assignée ou non
    */
    public function isAssigned ($pVarName){
        return isset ($this->_vars[$pVarName]);
    }

    /**
    * retourne la donnée assignée (si elle existe)
    * @param string  $varName    nom de la variable
    * @return    mixed   valeur de la variable ou null si inexistante
    */
    public function & getAssigned ($pVarName){
        if ($this->isAssigned ($pVarName)){
            return $this->_vars[$pVarName];
        }
        $return = null;        
        return $return;            
    }

    /**
    * Affiche à l'écran la sortie du template avec ses données rapprochées.
    * @param string $tplName   nom du fichier template
    */
    public function display ($pTplId){
    	echo $this->fetch ($pTplId);
    }

    /**
    * Retourne le template parsé dans une chaine de caractère
    * @param string $tplName   nom du fichier template
    * @return    string  contenu resultat du template parsé
    */
    public function fetch ($tplName){
        if(!$this->_prepareTpl ($tplName)){
            throw new CopixException (_i18n('copix:copix.error.unfounded.template',$tplName));
        }
    	$pushed = $this->_pushContext ($tplName);

        if ($this->isSmarty ($this->templateFile)){
        	$toReturn = $this->_smartyPass ($this->templateFile);
        	if ($pushed){
        		$this->_popContext ();
        	}
        	return $toReturn;
        }

        //déclare les variables locales pour le template.
        extract ($this->_vars);
        ob_start ();
        include ($this->templateFile);
        $toReturn = ob_get_contents();
        ob_end_clean();
        if ($pushed){
           $this->_popContext ();
        }
        return $toReturn;
    }
    
    /**
     * Cache interne des chemins de templates.
     *
     * @var array
     */
    static private $_tplFilePathCache = array();
    
    /**
     * Vide le cache de chemins.
     *
     */
    static public function clearFilePathCache() {
    	self::$_tplFilePathCache = array();
    }
    
    /**
     * Récupère le chemin du fichier template qui sera utilisé pour l'identifiant donné
     * @param string	$pTplName	L'identifiant du template à utiliser
     * @return string 	le chemin du fichier qui sera utilisé pour l'identifiant donnée
     */
    public function getFilePath ($pTplName){
		$cache = (strpos ($pTplName, '|') !== false || strpos ($pTplName, 'copix:') !== false || strpos ($pTplName, 'file:') !== false) ? $pTplName : CopixContext::get () . '|' . $pTplName;
    	if(isset(self::$_tplFilePathCache[$cache])) {
    		return self::$_tplFilePathCache[$cache];
    	}
    	
        //Using a selector to find out the fileName
        $fileSelector = CopixSelectorFactory::create ($cache);
        $fileName     = $fileSelector->fileName;
        $config = CopixConfig::instance ();
        
        $toReturn = false;
        
        //On a donné un chemin complet direct, on retourne directement
        if ($fileSelector->type !== 'module'){
        	if (file_exists ($templateFilePath = $fileSelector->getPath() . $fileName)){
        		$toReturn = $templateFilePath;
        	}
        } else {
        	$toReturn = CopixResource::findThemeTemplate (
	        	$fileSelector->fileName, 
	        	$fileSelector->module,
	        	$fileSelector->getPath(),
	        	self::getTheme (),
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
    * met à jour la variable $this->templateFile
    * 
    * @param	string 	$tplName	le nom du template dont on souhaites calculer le chemin
    * @return	boolean
    */
    private function _prepareTpl($pTplName){
    	return $this->templateFile = $this->getFilePath ($pTplName);
    }

    /**
    * passage du traitement à smarty.... (aprés inclusion si nécessaire.)
    * @param string  $tplName    nom du fichier template
    */
    private function _smartyPass ($tplName){
    	//Smarty gère les variables qui n'existent pas en masquant les notices.
    	//Comme le error handler de Copix ne tient pas compte de la valeur configurée dans error_reporting (), 
    	//on force la variable E_NOTICE à "aucune réaction" le temps d'afficher le template
    	$config = CopixConfig::instance (); 
    	$oldNoticeValue = $config->copixerrorhandler_actions[E_NOTICE];
		$config->copixerrorhandler_actions[E_NOTICE] = new CopixErrorHandlerAction (false, null);    	

    	$tpl = $this->_createSmartyTpl ();
        // dirty, because we use private member, but improves speed
        $tpl->_tpl_vars  = $this->_vars;
        $toReturn =  $tpl->fetch ('file:'.$tplName);

		//restauration de la valeur originale pour les notices
        $config->copixerrorhandler_actions[E_NOTICE] = $oldNoticeValue;    	
        return $toReturn;
    }
    
    /**
     * Création d'un objet de type Smarty configuré pour Copix
     *  Note: Utilise le clonage d'objet pour retourner l'objet aux appels suivants.
     */
    private function _createSmartyTpl (){
    	//static $tpl = false;
    	//if ($tpl === false ){
            $config = CopixConfig::instance ();
            Copix::RequireOnce (COPIX_SMARTY_PATH.'Smarty.class.php');
	        $tpl = new Smarty ();
	        $tpl->compile_dir   = COPIX_CACHE_PATH.'php/templates/';
	        $tpl->compile_check = $config->compile_check;
	        $tpl->force_compile = $config->force_compile;
	        $tpl->caching       = $config->template_caching;
	        $tpl->use_sub_dirs  = $config->template_use_sub_dirs;
	        $tpl->cache_dir     = COPIX_CACHE_PATH.'html/templates/';
	        $tpl->plugins_dir   = array ('plugins', COPIX_PATH.'smarty_plugins');
    	//}
    	//$tpl->clear_all_assign ();
    	return $tpl;
    }

    /**
    * regarde si le template est de type Smarty (s'il porte l'extention .tpl)
    *   si .ptpl, c'est un template écrit en PHP
    * @param string  $tplName    nom du template
    * @return    boolean true si Smarty, false si PHP
    */
    public function isSmarty ($tplName){
    	$ext = substr ($tplName, strrpos ($tplName, '.'));
        return (($ext !== '.ptpl') && ($ext !== '.php'));
    }

    /**
    * Récupère la liste des variable déjà assignées.
    * @return    array   variables assignées
    */
    public function & getTemplateVars (){
        return $this->_vars;
    }
    
    /**
     * Assigne en un seul coup plusieurs variables au template
     * @param array $pVariables tableau de données clef/valeurs 
     *    ou clef = nom de la variable et valeur = valeur des variables à assigner
     */
    public function assignTemplateVars ($pVariables){
    	$this->_vars = array_merge ($this->_vars, $pVariables);
    }
    
    /**
    * Force le contexte au contexte de tplId et indique si le contexte à été 
    *  modifié dans la variable de retour.
    * @param	string		$pTplId le template dont on souhaites forcer le contexte
    * @return	boolean		si nous avons bien mis à jour le contexte (pour que l'appelant puisse 
    * savoir s'il faudra rétablir le contexte ou non)
    */
    private function _pushContext ($pTplId){
    	try {
    		$tpl = CopixSelectorFactory::create($pTplId);
      	   if ($tpl->type == 'module'){
		      CopixContext::push ($tpl->module);
		      return true;
    	   }
    	}catch (Exception $e){
           return false;    		
    	}
    }

    /**
    * Pop le contexte (alias à CopixContext::pop au sein de la classe)
    * @return string
    */
    private function _popContext (){
    	CopixContext::pop ();
    }
    
    /**
    * Récupère le thème en cours d'utilisation
    * @return string le nom du thème
    */
    static public function getTheme (){
    	if (self::$_theme === false){
    		if (CopixModule::isEnabled ('admin')){
      	       self::$_theme = CopixConfig::get ('admin|defaultThemeId');
    		}
    	}
    	return self::$_theme;
    }

    /**
    * Définition du thème à utiliser
    * @param string $themeName le nom du thème à utiliser
    * @return string le thème qui a été mis en place
    */
    static public function setTheme ($themeName){
    	return self::$_theme = $themeName;
    }
    
    /**
     * Récupère la liste des thèmes graphiques connus
     * @return array
     */
    static public function getThemesList () {
		$templateBasePath = COPIX_PROJECT_PATH.'themes/';
		$arList = scandir($templateBasePath);
		$themes = array ();
		
		foreach ($arList as $key=>$Dir) {
			if (!is_dir($templateBasePath.$Dir) || $Dir=='default' || !file_exists($templateBasePath.$Dir.'/theme.xml')) {
				//unset($arList[$key]);
			} else {
				$themeInfos = self::getThemeInformations ($Dir);
				$themes[$themeInfos->id] = $themeInfos->name;
			}
		}
		
		asort ($themes);
		return array_keys ($themes);
    }
    
    /**
     * Fonction qui donne toutes les infos d'un thème depuis son fichier theme.xml
	 * @param idTheme L'identifiant du theme
	 * @return StdClass class qui contient les infos	 
	 */
	static public function getThemeInformations ($idTheme) {
		$templatePath = COPIX_PROJECT_PATH.'themes/';
		
		$toReturn = new StdClass();
		$xml=null;
		
		$toReturn->id = $idTheme;
		if (file_exists($templatePath.$idTheme.'/theme.xml')) {
			$xml = simplexml_load_file($templatePath.$idTheme.'/theme.xml');
		}
		$toReturn->name = isset($xml->name) ? htmlentities(utf8_decode(((string)$xml->name))) : htmlentities(utf8_decode($idTheme));
		$toReturn->author = isset($xml->author) ? htmlentities(utf8_decode(((string)$xml->author))) : null;
		$toReturn->description = isset($xml->description) ? htmlentities(utf8_decode(((string)$xml->description))) : null;
		$toReturn->website = isset($xml->website) ? htmlentities(utf8_decode(((string)$xml->website))) : null;
		$toReturn->tpl = isset($xml->tpl) ? (string)$xml->tpl : null;
		if (isset($xml->image) && file_exists($templatePath.$idTheme.'/'.(string)$xml->image)) {
			$toReturn->image = (string)$xml->image;
		} else {
			$toReturn->image = null;
		}
		return $toReturn;
	}

    /**
     * Recherche de templates dans un module et d'extension donnée.
     * 
     * Cette fonction va rechercher les templates dans le répertoire module template par défaut
     * et également dans le répertoire de surcharge dans le thème default
     * (project/modules/module_name/templates/* & project/themes/default/module_name/*)
     * 
     * <code>
     * $arTemplates = CopixTpl::find ('module_name', '.dyn.tpl');
     * //recherche de plusieurs types de templates
     * $arTemplates = CopixTpl::find ('module_name', array ('.dyn.tpl', '.dyn.ptpl'));
     * //recherche avec masques
     * $arTemplates = CopixTpl::find ('module_name', '.dyn.*');
     * </code>
     *     
     * @param	string	$pModuleName	le nom du module dans lequel on va chercher le template
     * @param 	mixed  	$pExtension		l'extension que l'on recherche (ou un tableau d'extensions)
     * 
     * @return array Retourne un tableau contenant en clé 'modules|fichier_trouvé'  et en valeur le nom dans (tpl){*@name NOM} (ptpl)/*@name NOM si aucun nom n'est trouvé, on mets la clé
     */
    static public function find ($pModuleName, $pExtension){
    	if (!is_array ($pExtension)){
    		$pExtension = array ($pExtension);
    	}
    	$files = array ();
   	   	foreach ($pExtension as $extension){
    		$files = array_merge ($files, CopixFile::search ('*'.$extension,COPIX_PROJECT_PATH.'themes/default/'.$pModuleName.'/',false));
    		$files = array_merge ($files, CopixFile::search ('*'.$extension,CopixModule::getPath ($pModuleName).'templates/',false));
    	}
    	
    	$arFiles=array();
    	/*TODO Ajouter la gestion des ptpl*/
    	foreach ($files as $key=>$file) {
    		$name = ($pModuleName.'|'.basename($file));
    		$tpl = CopixFile::read($file);
    		if (substr($file,strlen($file)-4)=='ptpl') {
    			$nom = preg_replace('".*\/*@name ([^\*]+[^/]+)\*/.*"','\1', $tpl);
    		}else{
    			$nom = preg_replace('".*{*@name ([^\*]+[^}]+)\*\}.*"','\1', $tpl);
    		}
    		if (strlen($nom)==strlen($tpl)) {
    			$nom=$name;
    		}
    		$arFiles[$name]= $nom;
    	}
    	return $arFiles;
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
    static public function tag ($pTagName, $pParams = array (), $pContent = null){
       if (strpos($pTagName,'|') === false) {
	       $path = COPIX_PATH;
       } else {
           list ($module, $pTagName) = explode ('|', $pTagName);
           $path = CopixModule::getBasePath($module).$module.'/';
       }
       
       Copix::RequireOnce ($path.'taglib/'.$pTagName.'.templatetag.php');
       
       $className = 'TemplateTag'.$pTagName;
	   $tag = new $className ($pTagName);
	   $tag->setParams($pParams);
	   return $tag->process ($pParams, $pContent);
    } 
}
?>