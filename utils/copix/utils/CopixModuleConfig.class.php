<?php
/**
* @package		copix
* @subpackage	core
* @author		Croës Gérald, Bertrand Yan
* @copyright 	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Représente les options de configuration d'un module
 * @package copix
 * @subpackage core
 */
class CopixModuleConfig {
	/**
    * Le nom du module que l'on charge
    * @var string
    */
	var $module      = null;

	/**
    * Les variables de configuration
    * @var array
    */
	private $_configVars = array ();
	
	/**
	 * Les valeurs des paramètres
	 */
	private $_values = array ();
	
	/**
	 * Sauvegarde la node parameters
	 */
	private $_xmlParameters = null;
	
	/**
	 * Types de paramètres possibles (par défaut : text)
	 */
	private $_allowedTypes = array ('text', 'bool', 'int', 'select', 'multiSelect', 'email');
	
	/**
    * constructor.
    * @param string $name
    */
	public function __construct ($name){
		$this->module = $name;
		if ($this->_needsCompilation ()){
			$this->_loadFromXML ();
			$this->_loadFromDatabase ();
			$this->_writeInDatabase ();
			$this->_writeInPHPCache ();
		}else{
			$_load = array ();
			require ($this->_getCompiledFileName());
			$this->_values = $_load;
		}
	}

	/**
    * Indique le chemin du fichier compilé
    * @return string
    */
	private function _getCompiledFileName (){
		return COPIX_CACHE_PATH.'php/config/'.str_replace (array ('|', ':'), array ('_M_', '_K_'), $this->module).'.php';
	}

	/**
    * Indique si les paramètres du module à besoin d'être recompilé ou non
    * @return boolean
    */
	private function _needsCompilation () {
		//force compilation ?
		$config = CopixConfig::instance ();
		if ($config->force_compile){
			return true;
		}

		// DDT 2006-09-07 ajout test d'existence du fichier
        if (!file_exists($this->_getCompiledFileName())) {
            return true;
        }

		//don't check the compiled file
		if ($config->compile_check === false){
			return false;
		}

		//we needs to compile if the xml file is newer than de PHPCache file
		$select = CopixSelectorFactory::create ($this->module.'module.xml');
		$moduleXmlPath = $select->getPath ('module.xml');
		if (!file_exists ($moduleXmlPath)) {
			throw new CopixException (_i18n ('copix:errors.moduleXmlNotFound', array ($moduleXmlPath)));
		}
		return filemtime ($moduleXmlPath) > filemtime ($this->_getCompiledFileName ());
	}

	/**
    * Sauvegarde la valeur des paramètres dans un fichier PHP
    * @return void
    */
	private function _writeInPHPCache (){
		$generator = new CopixPHPGenerator ();
		$_resources = $generator->getPHPTags ($generator->getVariableDeclaration ('$_load', $this->_values));
		CopixFile::write ($this->_getCompiledFileName (), $_resources);
	}

	/**
    * Get the configVars from dao group array
    * We will not load values that do not exists in the XML file.
    * We will only load the values of the config variables, not their captions or so.
    * We remind that the database here is just a _Saving_ purpose in case the "temp" directory is deleted.
    * We will test the presence of the CopixDB plugin to store values in the database.
    */
	private function _loadFromDatabase () {
		if (! $this->_checkDBConnection ()){
			return;
		}

		$arVars = _ioDAO ('copix:CopixConfig')->findBy (_daoSP ()->addCondition ('module_ccfg','=',$this->module));
		foreach ($arVars as $vars) {
			$arExplode = explode ('|', $vars->id_ccfg);
			$paramName = $arExplode[count ($arExplode) - 1];
			$paramInfos = $this->_getParam ($paramName);
			
			if (isset ($this->_configVars[$vars->id_ccfg])){
				
				$this->_configVars[$vars->id_ccfg]['Value'] = $vars->value_ccfg;
				
				$listValues = $this->_strToArray ($paramInfos['ListValues']);
				switch ($paramInfos['Type']) {
					case 'select' :
					case 'multiSelect' :
						$valueStr = (isset ($listValues[$vars->value_ccfg])) ? $listValues[$vars->value_ccfg] : $vars->value_ccfg; 
						break;
						
					case 'bool' :
						$valueStr = ($vars->value_ccfg == 0) ? _i18n ('copix:copix.no') : _i18n ('copix:copix.yes');
						break;
						
					default :
						$valueStr = $vars->value_ccfg;
						break;
				}
				$this->_configVars[$vars->id_ccfg]['ValueStr'] = $valueStr;
			}
			if (isset ($this->_values[$vars->id_ccfg])){
               $this->_values[$vars->id_ccfg] = $vars->value_ccfg; 				
			}
		}
	}

	/**
    * Regarde si une connexion à une base de données est disponible
    * @return boolean 
    */
	private function _checkDBConnection (){
		static $dbOk = null;
		if ($dbOk === null){
			try {
				CopixConfig::instance ()->copixdb_getProfile ();
				return $dbOk = true;
			}catch (Exception $e){
				return $dbOk = false;
			}
		}
		return $dbOk;
	}

	/**
    * Sauvegarde la valeur des paramètres dans la base de données.
    * Ne fait rien si aucune base n'est disponible
    * @return void
    */
	private function _writeInDatabase () {
		if (! $this->_checkDBConnection ()){
			return;
		}

		$dao = _ioDAO ('copix:CopixConfig');
		foreach ($this->_configVars as $attribute){
			$toInsert               = _record ('copix:CopixConfig');
			$toInsert->id_ccfg      = $this->module.$attribute['Name'];
			$toInsert->module_ccfg   = $this->module;
			$toInsert->value_ccfg   = $attribute['Value'];
			if ($dao->get ($toInsert->id_ccfg) === false){
				$dao->insert ($toInsert);//did not exists before
			}else{
				$dao->update ($toInsert);//updates the DB values
			}
		}
	}

	/**
    * Récupère la valeur du paramètre $id
    * @param string $id l'identifiant du paramètre
    * @return string la valeur du paramètre
    */
	public function get ($id) {
		if (array_key_exists ($id, $this->_values)){
			return $this->_values[$id];
		}else{
			throw new CopixException ('Unknow variable '.$id);
		}
	}

	/**
    * Check if the given param exists.
    * @param	string	$pId	l'identifiant du paramètre	
    * @return boolean
    */
	public function exists ($pId){
		return array_key_exists ($pId, $this->_values);
	}

	/**
    * gets the list of known params.
    */
	public function getParams (){
		if (count ($this->_configVars) == 0){
           $this->_loadFromXML ();
   		   $this->_loadFromDatabase ();
		}
		return $this->_configVars;
	}

	/**
    * Saves the value for id, will compile if different from the actual value.
    * @param string $id l'identifiant de l'élément
    * @param string $value la valeur du paramètre de configuration
    * @return void 
    */
	public function set ($id, $value){
		//if the config var exists only....
		if (array_key_exists ($id, $this->_values)){
			//Update the value in the file.
			$this->_configVars[$id]['Value'] = $value;
			$this->_values[$id] = $value;

			//Saves changes in the database
			if ($this->_checkDBConnection ()){
				$dao      = _ioDAO ('copix:CopixConfig');
				$toInsert = _record ('copix:CopixConfig');
				$toInsert->id_ccfg      = $id;
				$toInsert->module_ccfg   = $this->module;
				$toInsert->value_ccfg   = $value;
				if ($dao->get ($toInsert->id_ccfg) === false){
					$dao->insert ($toInsert);//did not exists before
				}else{
					$dao->update ($toInsert);//updates the DB values
				}
			}

			//Saves changes in the PHP File
			$this->_writeInPHPCache();
		}else{
			throw new CopixException (_i18n ('copix.error.module.unknowParameter', array ($id, $this->module)));
		}
	}

	/**
    * Charge la valeurs des paramètres depuis le fichier XML
    * @return void
    */
	private function _loadFromXML (){
		$module = $this->module === "|" ? "default" : substr ($this->module, 0, -1);
		$select = CopixSelectorFactory::create ($this->module . 'module.xml');

		//checks if the file exists
		$fileName = $select->getPath ($select->fileName);
		if (! is_readable ($fileName) ){
			return false;
		}

		$this->_configVars = array ();
		$xml = simplexml_load_file ($fileName);
		if (isset ($xml->parameters->parameter) || isset ($xml->parameters->group)){
			// nodes parameter sans group
			if (isset ($xml->parameters->parameter)) {
				$this->_xmlParameters[$this->module]['no-group'] = $xml->parameters->parameter;
				foreach ($xml->parameters->parameter as $key=>$child){
					$this->_loadParameterFromNode ($module, $child);
				}
			}
			
			// nodes parameter dans un group
			if (isset ($xml->parameters->group)) {
				$temp = 0;
				foreach ($xml->parameters->group as $groupKey => $groupChild) {
					$attributes = $groupChild->attributes ();
					if (isset ($attributes['caption'])) {
						$groupName = (string)$attributes['caption'];
					} else if (isset ($attributes['captioni18n'])) {
						CopixContext::push ($module);
						$groupName = _i18n ((string)$attributes['captioni18n']);
						CopixContext::pop ();
					} else {
						throw new CopixException (_i18n ('copix:copixmodule.error.parametersGroupCaptionEmpty', array ($module)));
					}
					$this->_xmlParameters[$this->module][$groupName] = $groupChild;
					foreach ($groupChild as $key => $child) {
						$this->_loadParameterFromNode ($module, $child);
					}
					$temp++;
				}
			}
		}
		
		//echo '<pre>';
		//print_r ($this->_xmlParameters);
	}
	
	/**
	 * Charge un paramètre depuis une node
	 * 
	 * @param simpleXlmNode $pNode Node qui contient des infos sur un paramètre
	 */
	private function _loadParameterFromNode ($pModule, $pNode) {
		$attributes = $pNode->attributes ();
		//we stores in a key with the following format module|attributeName
		CopixContext::push ($pModule);		
		$this->_configVars[$this->module.$attributes['name']] = $this->_getParam ($attributes['name']);		
		$this->_values[$this->module.$attributes['name']] = utf8_decode ((string)$attributes['default']);
		CopixContext::pop ();
	}
	
	/**
	 * Transforme une chaine de la forme 0=>oui;1=>non en tableau
	 */
	private function _strToArray ($pStr) {
		$keysValues = explode (';', $pStr);
		$values = array ();
		foreach ($keysValues as $keyValue) {
			if (strpos ($keyValue, '=>') !== false) {
				list ($key, $value) = explode ('=>', $keyValue);
				$values[trim ($key)] = trim ($value);
			}
		}
		return $values;
	}
	
	/**
	 * Renvoi des infos sur un paramètre
	 * 
	 * @param string $pParam Nom du paramètre
	 * @return array
	 */
	private function _getParam ($pParam) {
		// ce module n'a pas de paramètres sauvegardés
		if (!isset ($this->_xmlParameters[$this->module]) || count ($this->_xmlParameters[$this->module]) == 0) {
			return null;			
		}
				
		$module = $this->module === "|" ? "default" : substr ($this->module, 0, -1);
		CopixContext::push ($module);

		// boucle sur tous les paramètres
		foreach ($this->_xmlParameters[$this->module] as $groupKey => $groupChild) {
			foreach ($groupChild as $key => $child) {
				$attributes = $child->attributes ();
				
				// si c'est le paramètre $pParam
				if (isset ($attributes['name']) && $attributes['name'] == $pParam) {
					$type = (isset ($attributes['type']) && in_array ($attributes['type'], $this->_allowedTypes)) ? (string)$attributes['type'] : 'text';
					$default = (string) $attributes['default'];
					
					// récupération de la valeur par défaut "à afficher"
					if ($type == 'select' || $type == 'multiSelect') {
						$values = $this->_strToArray ((string) $attributes['listValues']);
						$defaultStr = (isset ($values[$default])) ? trim ($values[$default]) : $default;
					} else if ($type == 'bool') { 
						//echo '[$groupKey] [' . $groupKey . '] [$attributes] [' . $attributes['name'] . '] [$type] [' . $type . ']<br />';
						$defaultStr = ($default == 0) ? _i18n ('copix:copix.no') : _i18n ('copix:copix.yes');
					} else {
						$defaultStr = (string) $attributes['default'];
					}
					
					$toReturn = array (
						'Name' => (string) $attributes['name'],
						'Caption' => (isset ($attributes['captioni18n']) ? _i18n ((string) $attributes['captioni18n']) : utf8_decode ((string) $attributes['caption'])),
						'Default' => utf8_decode ($default),
						'DefaultStr' => $defaultStr,
						'Value' => $default,
						'ValueStr' => $defaultStr,
						'Type' => $type,
						'MinValue' => (isset ($attributes['minValue'])) ? (string) $attributes['minValue'] : null,
						'MaxValue' => (isset ($attributes['maxValue'])) ? (string) $attributes['maxValue'] : null,
						'MaxLength' => (isset ($attributes['maxLength'])) ? (string) $attributes['maxLength'] : null,
						'ListValues' => (isset ($attributes['listValues'])) ? (string) $attributes['listValues'] : null,
						'Group' => $groupKey
					);
						
					CopixContext::pop ();
					return $toReturn;
				}
			}
		}

		CopixContext::pop ();
		return null;
	}
}
?>