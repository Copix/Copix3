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
    private $_module      = null;

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
     * Indique le chemin du fichier compilé
     * @return string
     */
    public static function getCacheFileName ($pModule) {
        return COPIX_CACHE_PATH . 'config/' . str_replace (array ('|', ':'), array ('_M_', '_K_'), $pModule) . '.php';
    }

    /**
     * constructor.
     * @param string $name
     */
    public function __construct ($name) {
        $this->_module = $name;

        if ($this->_needsCompilation ()) {
            $this->_loadFromXML ();
            $this->_loadFromDatabase ();
            $this->_writeInDatabase ();
            $this->_writeInPHPCache ();
        }else {
            $_load = array ();
            require (self::getCacheFileName ($name));
            $this->_values = $_load;
        }
    }

    /**
     * Indique si les paramètres du module à besoin d'être recompilé ou non
     * @return boolean
     */
    private function _needsCompilation () {
        //force compilation ?
        $config = CopixConfig::instance ();
        if ($config->force_compile) {
            return true;
        }

        // DDT 2006-09-07 ajout test d'existence du fichier
		$compiled = self::getCacheFileName ($this->_module);
        if (!is_readable ($compiled)) {
            return true;
        }

        //don't check the compiled file
        if ($config->compile_check === false) {
            return false;
        }

        //we needs to compile if the xml file is newer than de PHPCache file
        $moduleXmlPath = CopixModule::getPath ($this->_module).'/module.xml';
        if (!is_readable ($moduleXmlPath)) {
            throw new CopixException (_i18n ('copix:copixmodule.error.xmlNotFound', array ($moduleXmlPath)));
        }
        return filemtime ($moduleXmlPath) > filemtime ($compiled);
    }

    /**
     * Sauvegarde la valeur des paramètres dans un fichier PHP
     * @return void
     */
    private function _writeInPHPCache () {
		$generator = new CopixPHPGenerator ();
		$_resources = $generator->getPHPTags ($generator->getVariableDeclaration ('$_load', $this->_values));
        CopixTemp::write (self::getCacheFileName ($this->_module), $_resources);
    }

    /**
     * Get the configVars from dao group array
     * We will not load values that do not exists in the XML file.
     * We will only load the values of the config variables, not their captions or so.
     * We remind that the database here is just a _Saving_ purpose in case the "temp" directory is deleted.
     * We will test the presence of the CopixDB plugin to store values in the database.
     */
    private function _loadFromDatabase () {
        if (! Copix::installed ()) {
            return;
        }

        $dao = DAOCopixConfig::instance ();
        $arVars = $dao->findBy (_daoSP ()->addCondition ('module_ccfg', '=', $this->_module));
        foreach ($arVars as $vars) {
            $arExplode = explode ('|', $vars->id_ccfg);
            $paramName = $arExplode[count ($arExplode) - 1];
            $paramInfos = $this->_getParam ($paramName);

            if (isset ($this->_configVars[$vars->id_ccfg])) {

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
            if (isset ($this->_values[$vars->id_ccfg])) {
                $this->_values[$vars->id_ccfg] = $vars->value_ccfg;
            }
        }
    }

    /**
     * Sauvegarde la valeur des paramètres dans la base de données.
     * Ne fait rien si aucune base n'est disponible
     * @return void
     */
    private function _writeInDatabase () {
        if (! Copix::installed ()) {
            return;
        }

        $dao = DAOCopixConfig::instance ();
        foreach ($this->_configVars as $attribute) {
            $toInsert               = _record ('CopixConfig');
            $toInsert->id_ccfg      = $this->_module.'|'.$attribute['Name'];
            $toInsert->module_ccfg   = $this->_module;
            $toInsert->value_ccfg   = $attribute['Value'];
            if ($dao->get ($toInsert->id_ccfg) === false) {
                $dao->insert ($toInsert);//did not exists before
            }else {
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
        if (array_key_exists ($id, $this->_values)) {
            return $this->_values[$id];
        }else {
            throw new CopixException ('Unknow variable '.$id);
        }
    }

    /**
     * Check if the given param exists.
     * @param	string	$pId	l'identifiant du paramètre
     * @return boolean
     */
    public function exists ($pId) {
        return array_key_exists ($pId, $this->_values);
    }

    /**
     * gets the list of known params.
     */
    public function getParams () {
        if (count ($this->_configVars) == 0) {
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
    public function set ($id, $value) {
        //if the config var exists only....
        if (array_key_exists ($id, $this->_values)) {
            //Update the value in the file.
            $this->_configVars[$id]['Value'] = $value;
            $this->_values[$id] = $value;

            //Saves changes in the database
            if (Copix::installed ()) {
                $dao      = DAOCopixConfig::instance ();
                $toInsert = _record ('CopixConfig');
                $toInsert->id_ccfg      = $id;
                $toInsert->module_ccfg   = $this->_module;
                $toInsert->value_ccfg   = $value;
                if ($dao->get ($toInsert->id_ccfg) === false) {
                    $dao->insert ($toInsert);//did not exists before
                }else {
                    $dao->update ($toInsert);//updates the DB values
                }
            }

            //Saves changes in the PHP File
            $this->_writeInPHPCache();
        }else {
            throw new CopixException (_i18n ('copix.error.module.unknowParameter', array ($id, $this->_module)));
        }
    }

    /**
     * Charge la valeurs des paramètres depuis le fichier XML
     * @return void
     */
    private function _loadFromXML () {
        //checks if the file exists
        $fileName = CopixModule::getPath ($this->_module).'module.xml';

        $this->_configVars = array ();
        $xml = simplexml_load_file ($fileName);

        if (isset ($xml->parameters->parameter) || isset ($xml->parameters->group)) {
            // nodes parameter sans group
            if (isset ($xml->parameters->parameter)) {
                $this->_xmlParameters[$this->_module][_i18n ('copix:log.group.default')] = $xml->parameters->parameter;
                foreach ($xml->parameters->parameter as $key=>$child) {
                    $this->_loadParameterFromNode ($child);
                }
            }

            // nodes parameter dans un group
            if (isset ($xml->parameters->group)) {
                $temp = 0;
                foreach ($xml->parameters->group as $groupKey => $groupChild) {
                    $attributes = $groupChild->attributes ();
                    if (isset ($attributes['caption'])) {
                        $groupName = (string)$attributes['caption'];
                    } elseif (isset ($attributes['captioni18n'])) {
                        CopixContext::push ($this->_module);
                        $groupName = _i18n ((string)$attributes['captioni18n']);
                        CopixContext::pop ();
                    } else {
                        throw new CopixException (_i18n ('copix:copixmodule.error.parametersGroupCaptionEmpty', array ($module)));
                    }
                    $this->_xmlParameters[$this->_module][$groupName] = $groupChild;
                    foreach ($groupChild as $key => $child) {
                        $this->_loadParameterFromNode ($child);
                    }
                    $temp++;
                }
            }
        }
    }

    /**
     * Charge un paramètre depuis une node
     *
     * @param simpleXlmNode $pNode Node qui contient des infos sur un paramètre
     */
    private function _loadParameterFromNode ($pNode) {
        $attributes = $pNode->attributes ();
        //we stores in a key with the following format module|attributeName
        CopixContext::push ($this->_module);
        $this->_configVars[$this->_module.'|'.$attributes['name']] = $this->_getParam ($attributes['name']);
        $this->_values[$this->_module.'|'.$attributes['name']] = utf8_decode ((string)$attributes['default']);
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
        if (!isset ($this->_xmlParameters[$this->_module]) || count ($this->_xmlParameters[$this->_module]) == 0) {
            return null;
        }

        CopixContext::push ($this->_module);

        // boucle sur tous les paramètres
        foreach ($this->_xmlParameters[$this->_module] as $groupKey => $groupChild) {
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
                        $defaultStr = ($default == 0) ? _i18n ('copix:copix.no') : _i18n ('copix:copix.yes');
                    } else {
                        $defaultStr = (string) $attributes['default'];
                    }

                    $description = null;
                    if (isset ($attributes['description'])) {
                        $description = (string)$attributes['description'];
                    } else if (isset ($attributes['descriptioni18n'])) {
                        $description = _i18n ((string)$attributes['descriptioni18n']);
                    }

                    $toReturn = array (
                            'Name' => (string) $attributes['name'],
                            'Caption' => (isset ($attributes['captioni18n']) ? _i18n ((string) $attributes['captioni18n']) : (string)$attributes['caption']),
                            'Default' => $default,
                            'DefaultStr' => $defaultStr,
                            'Value' => $default,
                            'ValueStr' => $defaultStr,
                            'Type' => $type,
                            'MinValue' => (isset ($attributes['minValue'])) ? (string) $attributes['minValue'] : null,
                            'MaxValue' => (isset ($attributes['maxValue'])) ? (string) $attributes['maxValue'] : null,
                            'MaxLength' => (isset ($attributes['maxLength'])) ? (string) $attributes['maxLength'] : null,
                            'ListValues' => (isset ($attributes['listValues'])) ? (string) $attributes['listValues'] : null,
                            'Group' => $groupKey,
                            'Description' => $description
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