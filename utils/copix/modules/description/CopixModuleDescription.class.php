<?php
/**
 * @package copix
 * @subpackage modules
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Description d'un module
 *
 * @package copix
 * @subpackage modules
 */
class CopixModuleDescription {
    /**
     * Dans Copix 3.0.x, CopixModule::getInformations renvoyait un stdClass avec des propriétés
     * Pour conserver cette compatibilité, ce tableau indique les liens entre les anciennes propriétés et les nouvelles méthodes
     *
     * @var array
     */
    private $_allowGet = array (
            'name' => 'getName', 'version' => 'getVersion', 'installedVersion' => 'getInstalledVersion', 'description' => 'getDescription',
            'longDescription' => 'getLongDescription', 'path' => 'getPath', 'icon' => 'getIcon', 'group' => 'getGroup', 'dependencies' => 'getDependencies',
            'credential_notspecific' => 'getCredentialsNotSpecifics', 'credential' => 'getCredentials', 'update' => 'getUpdates'
    );

    /**
     * Chaine du numéro de version X.Y.Z
     *
     * @var string
     */
    private $_version = null;

	/**
	 * Version après avoir passé les scripts d'installation du module, forme X.Y.Z
	 *
	 * @var string
	 */
	private $_installVersion = null;

    /**
     * Chaine du numéro de version installée X.X.X
     *
     * @var string
     */
    private $_updateFrom = null;

    /**
     * Description
     *
     * @var string
     */
    private $_description = null;

    /**
     * Description longue
     *
     * @var string
     */
    private $_longDescription = null;

    /**
     * Chemin physique du module
     *
     * @var string
     */
    private $_path = null;

    /**
     * Adresse de l'icone
     *
     * @var string
     */
    private $_icon = null;

    /**
     * Groupe du module
     *
     * @var CopixModuleGroup
     */
    private $_group = null;

    /**
     * Dépendances
     *
     * @var CopixModuleDependency[]
     */
    private $_dependencies = array ();

    /**
     * Liens à afficher dans la partie admin
     *
     * @var CopixModuleLinksGroup[]
     */
    private $_adminLinksGroups = array ();

    /**
     * Droits dynamiques créés par le module
     *
     * @var CopixModuleCredentials[]
     */
    private $_credentials = array ();

    /**
     * Droits dynamiques non spécifiques
     *
     * @var CopixModuleCredentials[]
     */
    private $_credentialsNotSpecifics = array ();

    /**
     * Scripts à executer pour les mises à jour
     *
     * @var CopixModuleUpdate[]
     */
    private $_updates = array ();

    /**
     * Nom du module
     *
     * @var string
     */
    private $_name = null;

    /**
     * Stratégies de log
     *
     * @var CopixLogStrategyDescription[]
     */
    private $_logStrategies = array ();

    /**
     * Types de log
     *
     * @var CopixModuleLogType[]
     */
    private $_logTypes = array ();

    /**
     * Stratégies de cache
     *
     * @var CopixCacheStrategyDescription[]
     */
    private $_cacheStrategies = array ();

    /**
     * Préférences utilisateur
     *
     * @var CopixModulePreferencesGroup[]
     */
    private $_userPreferencesGroups = array ();

	/**
     * Préférences utilisateur
     *
     * @var CopixModulePreferencesGroup[]
     */
    private $_groupPreferencesGroups = array ();

    /**
     * Pour la compatibilité avec Copix 3.0.x
     *
     * @param string $pName Propriété dont on veut la valeur
     * @return mixed
     */
    public function __get ($pName) {
        if (array_key_exists ($pName, $this->_allowGet)) {
            return $this->{$this->_allowGet[$pName]} ();
        }
    }

    /**
     * Constructeur
     *
     * @param string $pModuleName Le nom du module a décrire
     */
    public function __construct ($pXmlParsedFile) {
        $parsedFile = $pXmlParsedFile;

        // informations générales
        $defaultAttr = $parsedFile->general->default->attributes ();
        $this->_name = (string)$defaultAttr['name'];
        // définition du contexte, entre autres pour les clefs i18n sans le nom du module devant
        CopixContext::push ($this->_name);
        $this->_version = (isset($defaultAttr['version'])) ? (string)$defaultAttr['version'] : 0;
		$this->_updateFrom = (isset($defaultAttr['updatefrom'])) ? (string)$defaultAttr['updatefrom'] : $this->_version;
        if (CopixModule::isEnabled ($this->_name)) {
            $record = DAOCopixModule::instance ()->get ($this->_name);
            $this->_installedVersion = $record->version_cpm;
        }
        $this->_description = (isset ($defaultAttr['descriptioni18n'])) ? _i18n ((string)$defaultAttr['descriptioni18n']) : (string)$defaultAttr['description'];

        $this->_longDescription = (isset ($defaultAttr['longdescriptioni18n'])) ? _i18n ((string)$defaultAttr['longdescriptioni18n']) : ((isset ($defaultAttr['longdescription'])) ? (string) $defaultAttr['longdescription'] : $this->_description);

        $this->_path = CopixModule::getBasePath ($this->_name);
        if (isset ($defaultAttr['icon']) && is_readable (_resourcePath ((string)$defaultAttr['icon']))) {
            $this->_icon = _resource ((string)$defaultAttr['icon']);
        } else {
            $this->_icon = null;
        }

        // groupe du module
        if (isset ($parsedFile->general->group)) {
            $attributes = $parsedFile->general->group->attributes ();
            $groupID = (isset ($attributes['id'])) ? (string)$attributes['id'] : null;
            if (isset ($attributes['caption'])) {
                $groupCaption = (string)$attributes['caption'];
            } else if (isset ($attributes['captioni18n'])) {
                $groupCaption = _i18n ((string)$attributes['captioni18n']);
            } else {
                $groupCaption = $groupID;
            }
           // pas de groupe spécifié
        } else {
            $groupID = 'default';
            $groupCaption = _i18n ('copix:copixmodule.group.default');
        }
        $this->_group = new CopixModuleGroup ($groupID, $groupCaption);

        // dépendances
        if (isset ($parsedFile->dependencies)) {
            foreach ($parsedFile->dependencies->dependency as $dependency) {
                $attributes = $dependency->attributes ();
				$version = (isset ($attributes['version'])) ? (string)$attributes['version'] : null;
                $this->_dependencies[] = new CopixModuleDependency (_copix_utf8_decode ((string)$attributes['name']), _copix_utf8_decode ((string)$attributes['kind']), $version);
            }
        }

        // node admin
        if (isset ($parsedFile->admin)) {
            $adminAttributes = $parsedFile->admin->attributes ();

            // liens dans la partie admin sans groupe
            if (isset ($parsedFile->admin->link)) {
                // groupe des liens
                $caption = null;
                $id = (isset ($adminAttributes['groupid'])) ? (string)$adminAttributes['groupid'] : null;
                if (isset ($adminAttributes['groupcaption'])) {
                    $caption = (string)$adminAttributes['groupcaption'];
                } else if (isset ($adminAttributes['groupcaptioni18n'])) {
                    $caption = _i18n ((string)$adminAttributes['groupcaptioni18n']);
                } else {
                    $caption = 'Défaut';
                }
                $group = $this->_adminLinksGroups[$id] = new CopixModuleLinksGroup ($id, $caption, $adminAttributes['groupicon']);
                $this->_addLinks ($parsedFile->admin->link, $group);
            }

            // liens dans la partie admin avec un groupe
            if (isset ($parsedFile->admin->group)) {
                foreach ($parsedFile->admin->group as $groupNode) {
                    $attributes = $groupNode->attributes ();
                    $id = (string)$attributes['id'];
                    $caption = (isset ($attributes['captioni18n'])) ? _i18n ((string)$attributes['captioni18n']) : (string)$attributes['caption'];
                    $group = $this->_adminLinksGroups[$id] = new CopixModuleLinksGroup ($id, $caption, (string)$attributes['icon']);
                    $this->_addLinks ($groupNode->link, $group);
                }
            }
        }

        // droits dynamiques
        if (isset ($parsedFile->credentials) && isset ($parsedFile->credentials->credential)) {
            foreach ($parsedFile->credentials->credential as $credential) {
                if (isset ($credential['specific']) && (string)$credential['specific'] == 'false') {
                    $this->_credentialsNotSpecifics[(string)$credential['name']] = array ();
                    $currentCredential = &$this->_credentialsNotSpecifics[(string)$credential['name']];
                } else {
                    $this->_credentials[(string)$credential['name']] = array ();
                    $currentCredential = &$this->_credentials[(string)$credential['name']];
                }

                foreach ($credential->value as $value) {
                    $name = (string)$value['name'];
                    $level = (isset ($value['level'])) ? (string)$value['level'] : null;
                    $currentCredential[] = new CopixModuleCredential ($name, $level);
                }
            }
        }

        // scripts de mise à jour
        if (isset ($parsedFile->updates) && isset ($parsedFile->updates->update)) {
            foreach ($parsedFile->updates->update as $update) {
                $attributes = $update->attributes ();
                $script = (isset ($attributes['script'])) ? (string)$attributes['script'] : null;
                $from = (isset ($attributes['from'])) ? (string)$attributes['from'] : null;
                $to = (isset ($attributes['to'])) ? (string)$attributes['to'] : null;
                $this->_updates[] = new CopixModuleUpdate ($script, $from, $to);
            }
        }

        // logs
        if (isset ($parsedFile->logs)) {
            // stratégies de log
            if (isset ($parsedFile->logs->strategies->strategy)) {
                foreach ($parsedFile->logs->strategies->strategy as $strategy) {
                    $attributes = $strategy->attributes ();
                    $class = (string)$attributes->class;
                    if (strpos ($class, '|') === false) {
                        $class = $this->_name . '|' . $class;
                    }
					$caption = null;
					if (isset ($attributes->caption)) {
						$caption = (string)$attributes->caption;
                    } else if (isset ($attributes->captioni18n)) {
                        $caption = (string)$attributes->captioni18n;
                    }
					$description = null;
					if (isset ($attributes->description)) {
						$description = (string)$attributes->description;
                    } else if (isset ($attributes->descriptioni18n)) {
                        $description = (string)$attributes->descriptioni18n;
                    }
                    $strategy = new CopixLogStrategyDescription ($class, $caption, $description);
                    $this->_logStrategies[$strategy->getId ()] = $strategy;
                }
            }

            // types de log
            if (isset ($parsedFile->logs->types->type)) {
                foreach ($parsedFile->logs->types->type as $type) {
                    $attributes = $type->attributes ();

                    // recherche de l'identifiant et création du type
                    $id = (string)$attributes->id;
                    if (strpos ($id, '|') === false) {
                        $id = $this->_name . '|' . $id;
                    }
                    $type = new CopixLogType ($id);

                    // recherche du nom du type
                    if (isset ($attributes->caption)) {
                        $type->setCaption ((string)$attributes->caption);
                    } else if (isset ($attributes->captioni18n)) {
                        $type->setCaption (_i18n ((string)$attributes->captioni18n));
                    }

                    $this->_logTypes[$type->getId ()] = $type;
                }
            }
        }

        // cache
        if (isset ($parsedFile->caches)) {
            // stratégies de cache
            if (isset ($parsedFile->caches->strategies->strategy)) {
                foreach ($parsedFile->caches->strategies->strategy as $strategy) {
                    $attributes = $strategy->attributes ();
                    $class = (string)$attributes->class;
                    if (strpos ($class, '|') === false) {
                        $class = $this->_name . '|' . $class;
                    }
                    $caption = (isset ($attributes['captioni18n'])) ? _i18n ((string)$attributes['captioni18n']) : (string)$attributes['caption'];
                    $description = (isset ($attributes['descriptioni18n'])) ? _i18n ((string)$attributes['descriptioni18n']) : (string)$attributes['description'];
                    $strategy = new CopixCacheStrategyDescription ($class, $caption, $description);
                    $this->_cacheStrategies[$strategy->getId ()] = $strategy;
                }
            }
        }

        // préférences utilisateur
        if (isset ($parsedFile->userpreferences)) {

            // préférences sans groupe
            if (isset ($parsedFile->userpreferences->preference)) {
                $group = $this->_userPreferencesGroups['default'] = new CopixModulePreferencesGroup ('default', _i18n ('copix:modules.group.default'));
                $this->_addPreferences ($parsedFile->userpreferences->preference, $group);
            }

            // préférences avec un groupe
            if (isset ($parsedFile->userpreferences->group)) {
                foreach ($parsedFile->userpreferences->group as $groupNode) {
                    $attributes = $groupNode->attributes ();
                    $id = ((string)$attributes['id'] != null) ? (string)$attributes['id'] : uniqid ('group_');
                    $caption = (isset ($attributes['captioni18n'])) ? _i18n ((string)$attributes['captioni18n']) : (string)$attributes['caption'];
                    $group = $this->_userPreferencesGroups[$id] = new CopixModulePreferencesGroup ($id, $caption, (string)$attributes['icon']);
                    $this->_addPreferences ($groupNode->preference, $group);
                }
            }
        }

		// préférences de groupes
        if (isset ($parsedFile->grouppreferences)) {

            // préférences sans groupe
            if (isset ($parsedFile->grouppreferences->preference)) {
                $group = $this->_groupPreferencesGroups['default'] = new CopixModulePreferencesGroup ('default', _i18n ('copix:modules.group.default'));
                $this->_addPreferences ($parsedFile->grouppreferences->preference, $group);
            }

            // préférences avec un groupe
            if (isset ($parsedFile->grouppreferences->group)) {
                foreach ($parsedFile->grouppreferences->group as $groupNode) {
                    $attributes = $groupNode->attributes ();
                    $id = ((string)$attributes['id'] != null) ? (string)$attributes['id'] : uniqid ('group_');
                    $caption = (isset ($attributes['captioni18n'])) ? _i18n ((string)$attributes['captioni18n']) : (string)$attributes['caption'];
                    $group = $this->_groupPreferencesGroups[$id] = new CopixModulePreferencesGroup ($id, $caption, (string)$attributes['icon']);
                    $this->_addPreferences ($groupNode->preference, $group);
                }
            }
        }

        CopixContext::pop ();
    }

    /**
     * Ajoute les liens de la node dans le groupe indiqué
     *
     * @param SimpleXMLNode $pNode Node contenant les liens
     * @param CopixModuleLinksGroup $pGroup Groupe auquel on veut ajouter les liens
     */
    private function _addLinks ($pNode, $pGroup) {
        foreach ($pNode as $link) {
            $attributes = $link->attributes ();

            if (isset ($attributes['captioni18n'])) {
                $caption = _i18n ((string)$attributes['captioni18n']);
            } else if (isset ($attributes['caption'])) {
                $caption = _copix_utf8_decode ((string)$attributes['caption']);
            } else {
                $caption = $this->_name;
            }

			if (isset ($attributes['shortcaptioni18n'])) {
                $shortCaption = _i18n ((string)$attributes['shortcaptioni18n']);
            } else if (isset ($attributes['shortcaption'])) {
                $shortCaption = _copix_utf8_decode ((string)$attributes['shortcaption']);
            } else {
                $shortCaption = $caption;
            }
			
            $urlParams = array ();
            if (isset ($attributes['urlparams'])) {
                $params = explode (';', (string)$attributes['urlparams']);
                $urlParams = array ();
                foreach ($params as $param) {
                    list ($name, $value) = explode ('=>', $param);
                    $urlParams[$name] = $value;
                }
            }
            $url = _url ((string)$attributes['url'], $urlParams);
			
            $credentials = (isset ($attributes['credentials'])) ? (string)$attributes['credentials'] : null;
            $icon = (isset ($attributes['icon'])) ? (string)$attributes['icon'] : null;
			$bigIcon = (isset ($attributes['bigicon'])) ? (string)$attributes['bigicon'] : null;

            $pGroup->addLink ($shortCaption, $caption, $url, $credentials, $icon, $bigIcon);
        }
    }

    /**
     * Ajoute les préférences de la node dans le groupe indiqué
     *
     * @param SimpleXMLNode $pNode Node contenant les préférences
     * @param CopixModulePreferencesGroup $pGroup Groupe auquel on veut ajouter les liens
     */
    private function _addPreferences ($pNode, $pGroup) {
        foreach ($pNode as $link) {
            $attributes = $link->attributes ();
            $name = (strpos ((string)$attributes['name'], '|') === false) ? $this->_name . '|' . (string)$attributes['name'] : (string)$attributes['name'];
            $caption = (isset ($attributes['captioni18n'])) ? _i18n ((string)$attributes['captioni18n']) : ((string)$attributes['caption']);
            $description = (isset ($attributes['descriptioni18n'])) ? _i18n ((string)$attributes['descriptioni18n']) : ((string)$attributes['description']);
            $type = (isset ($attributes['type'])) ? ((string)$attributes['type']) : 'text';
            $default = (isset ($attributes['default'])) ? ((string)$attributes['default']) : null;
            if (isset ($attributes['listValues'])) {
                $temp = explode (';', $attributes['listValues']);
                $listValues = array ();
                foreach ($temp as $listValue) {
                    list ($key, $value) = explode ('=>', $listValue);
                    $listValues[$key] = $value;
                }
            } else {
                $listValues = array ();
            }
            $toAdd = new CopixModulePreference ($name, $caption, $description, $type, $default, $listValues);
            $pGroup->add ($toAdd);
        }
    }

    /**
     * Retourne les stratégies de log définies dans le module
     *
     * @return CopixLogStrategy[]
     */
    public function getLogStrategies () {
        return $this->_logStrategies;
    }

    /**
     * Retourne les stratégies de cache
     *
     * @return CopixCacheStrategieDescription[]
     */
    public function getCacheStrategies () {
        return $this->_cacheStrategies;
    }

    /**
     * Retourne les types de log définit dans le module
     *
     * @return CopixLogType[]
     */
    public function getLogTypes () {
        return $this->_logTypes;
    }

    /**
     * Retourne le nom du module
     *
     * @return string
     */
    public function getName () {
        return $this->_name;
    }

    /**
     * Retourne les liens de la partie admin
     *
     * @return CopixModuleLinksGroup[]
     */
    public function getAdminLinksGroups () {
        return $this->_adminLinksGroups;
    }

    /**
     * Retourne les groupes de préférences
     *
     * @return CopixModulePreferencesGroup[]
     */
    public function getUserPreferencesGroups () {
        return $this->_userPreferencesGroups;
    }

	/**
     * Retourne les groupes de préférences des groupes
     *
     * @return CopixModulePreferencesGroup[]
     */
    public function getGroupPreferencesGroups () {
        return $this->_groupPreferencesGroups;
    }

    /**
     * Retourne les dépendances requises par ce module
     *
     * @return CopixModuleDependency[]
     */
    public function getDependencies () {
        return $this->_dependencies;
    }

    /**
     * Retourne les scripts à appeler pour les mises à jour
     *
     * @return CopixModuleUpdate[]
     */
    public function getUpdates () {
        return $this->_updates;
    }

    /**
     * Retourne les droits dynamiques créés par ce module
     *
     * @return CopixModuleCredential[]
     */
    public function getCredentials () {
        return $this->_credentials;
    }

    /**
     * Retourne les droits non spécifiques
     *
     * @return CopixModuleCredential[]
     */
    public function getCredentialsNotSpecifics () {
        return $this->_credentialsNotSpecifics;
    }

    /**
     * Retourne le groupe du module
     *
     * @return CopixModuleGroup
     */
    public function getGroup () {
        return $this->_group;
    }

    /**
     * Retourne l'adresse de l'icone
     *
     * @return string
     */
    public function getIcon () {
        return $this->_icon;
    }

    /**
     * Retourne le répertoire physique du module
     *
     * @return string
     */
    public function getPath () {
        return $this->_path;
    }

    /**
     * Retourne la description du module
     *
     * @return string
     */
    public function getDescription () {
        return $this->_description;
    }

    /**
     * Retourne la description longue du module
     *
     * @return string
     */
    public function getLongDescription () {
        return $this->_longDescription;
    }

    /**
     * Retourne la version du module
     *
     * @return string
     */
    public function getVersion () {
        return $this->_version;
    }

	/**
	 * Retourne la version après avoir passé les scripts d'install
	 *
	 * @return string
	 */
	public function getUpdateFrom () {
		return $this->_updateFrom;
	}

    /**
     * Retourne la version installée du module
     *
     * @return string
     */
    public function getInstalledVersion () {
        return $this->_installedVersion;
    }
}
