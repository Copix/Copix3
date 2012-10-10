<?php

class Module {
	static private $_xmlVersion = '1.0';
	static private $_xmlEncoding = 'UTF-8';
	
	/**
	 * Vérifie qu'un nom de module est valide, et disponible (non existant)
	 * 
	 * @param string $pName Nom
	 * @return bool
	 */
	static public function isAvailable ($pName) {
		if (self::isValid ($pName)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Vérifie qu'un nom de module est valide (mais pas forcément disponible)
	 * 
	 * @param string $pName Nom
	 * @return bool
	 */
	static public function isValid ($pName) {
		return (str_replace (' ', '', $pName) == $pName && strlen (trim ($pName)) > 2);
	}
	
	/**
	 * Création d'un module
	 * 
	 * @param string $pPath Répertoire où seront créés les fichiers
	 * @param mudleinfos $pModuleInfos Informations sur le module
	 */
	static public function createModule ($pPath, $pModuleInfos) {
		$fullPath = $pPath . $pModuleInfos->getName () . '/';
		// répertoire du module
		if (is_dir ($fullPath)) {
			//throw new CopixException (_i18n ('createmodule.error.folderExists', array ($pPath . $pName)));
		}
		CopixFile::createDir ($fullPath);
		
		// module.xml
		self::xmlCreate ($pPath, $pModuleInfos);
	}
	
	/**
	 * Charge le fichier module.xml
	 * 
	 * @param string $pModule Nom du module
	 * @return DOMDocument
	 */
	static private function _loadModuleXml ($pModule) {
		$xmlPath = CopixModule::getPath ($pModule) . 'module.xml';
		$xml = new DOMDocument (self::$_xmlVersion, self::$_xmlEncoding);
		$xml->load ($xmlPath);
		return $xml;
	}
	
	/**
	 * Renvoie un lien vers le fichier module.xml
	 * 
	 * @param string $pModule Nom du module
	 * @return string
	 */
	static private function _getModuleXmlPath ($pModule) {
		return CopixModule::getPath ($pModule) . 'module.xml';
	}
	
	/**
	 * Création d'un fichier module.xml
	 * 
	 * @param string $pPath Chemin du module
	 * @param moduleinfos $pModuleInfos Informations sur le module
	 */
	static public function xmlCreate ($pPath, $pModuleInfos) {
		$xml = new DOMDocument (self::$_xmlVersion, self::$_xmlEncoding);
		$element = $xml->createElement ('moduledefinition', '');
		$xml->appendChild ($element);
		$xml->save ($pPath . $pModuleInfos->getName () . '/module.xml');
		
		self::xmlSetGeneral ($pModuleInfos->getName (), $pModuleInfos);
	}
	
	/**
	 * Définition des infos de la node general de module.xml
	 * 
	 * @param string $pModule Nom du module
	 * @param moduleinfos $pModuleInfos Informations sur le module
	 */
	static public function xmlSetGeneral ($pModule, $pModuleInfos) {
		$xml = self::_loadModuleXml ($pModule);
		
		// node general
		if (is_null ($xml->documentElement->getElementsByTagName('general')->item (0))) {
			$node = $xml->createElement ('general', '');
			$xml->documentElement->appendChild ($node);
		}
		
		// node default
		$node = $xml->createElement ('default', '');
		$node->setAttribute ('name', $pModule);
		
		// description
		if (!is_null ($pModuleInfos->descriptionI18n)) {
			$node->setAttribute ('descriptioni18n', $pModuleInfos->descriptionI18n);
		} else if (!is_null ($pModuleInfos->description)) {
			$node->setAttribute ('description', $pModuleInfos->description);
		}
		
		// longue description
		if (!is_null ($pModuleInfos->longDescriptionI18n)) {
			$node->setAttribute ('longescriptioni18n', $pModuleInfos->longDescriptionI18n);
		} else if (!is_null ($pModuleInfos->longDescription)) {
			$node->setAttribute ('longdescription', $pModuleInfos->longDescription);
		}
		
		$xml->documentElement->getElementsByTagName ('general')->item (0)->appendChild ($node);		
		$xml->save (self::_getModuleXmlPath ($pModule));
	}
	
	/**
	 * Définition des liens dans la node linsk de module.xml
	 * 
	 * @param string $pModule Nom du module
	 * @param array $pLinks Infos sur les liens (tableau de linkinfos)
	 */
	static public function xmlSetLinks ($pModule, $pLinks) {
		$xml = self::_loadModuleXml ($pModule);
		
		// création de la node admin si elle n'existe pas
		$nodeAdmin = $xml->documentElement->getElementsByTagName('admin')->item (0);
		if (is_null ($nodeAdmin)) {
			$node = $xml->createElement ('admin', '');
			$xml->documentElement->appendChild ($node);
			$nodeAdmin = $xml->documentElement->getElementsByTagName('admin')->item (0);
			
		// si elle existait, on supprime les nodes link qu'elle contenait
		} else {
			while (!is_null ($nodeAdmin->getElementsByTagName ('link')->item (0))) {
				$nodeAdmin->removeChild ($nodeAdmin->getElementsByTagName ('link')->item (0));
			} 
		}
		
		// création des nouvelles nodes link
		foreach ($pLinks as $linkInfos) {
			$node = $xml->createElement ('link', '');
			
			// url
			if (!is_null ($linkInfos->url)) {
				$node->setAttribute ('url', $linkInfos->url);
			} else {
				throw new CopixException (_i18n ('global.error.urlNotExists'));
			}
			
			// caption i18n
			if (!is_null ($linkInfos->captionI18n)) {
				$node->setAttribute ('captioni18n', $linkInfos->captionI18n);
			// caption
			} else if (!is_null ($linkInfos->caption)) {
				$node->setAttribute ('caption', $linkInfos->caption);
			// aucun caption indiqué
			} else {
				throw new CopixException (_i18n ('global.error.captionNotExists'));
			}
			
			// credentials
			if (!is_null ($linkInfos->credentials)) {
				$node->setAttribute ('credentials', $linkInfos->credentials);
			}
			
			$nodeAdmin->appendChild ($node);
		} 
		
		$xml->save (self::_getModuleXmlPath ($pModule));
	}
	
	/**
	 * Créé un actiongroup
	 * 
	 * @param string $pModule Nom du module
	 * @param actiongroupinfos $pActiongroupInfos Informations sur l'actiongroup
	 * @param actioninfos $pActionsInfos Informations sur les actions
	 * @param array $pBeforeAction Tableau de lignes à insérer dans beforeAction
	 * @param array $pAfterAction Tableau de lignes à insérer dans afterAction
	 */
	static public function createActionGroup ($pModule, $pActiongroupInfos, $pActionsInfos, $pBeforeAction = null, $pAfterAction = null) {		
		$path = CopixModule::getPath ($pModule) . 'actiongroups/';
		if (!is_dir ($path)) {
			CopixFile::createDir ($path);
		}		
		$php = new CopixPHPGenerator ();
		
		$content = '/**' . "\n";
		$content .= ' * @package ' . $pActiongroupInfos->package . "\n";
		$content .= ' * @subpackage ' . $pActiongroupInfos->subpackage . "\n";
		$content .= ' * @copyright ' . $pActiongroupInfos->copyright . "\n";
		$content .= ' * @license ' . $pActiongroupInfos->license . "\n";
		$content .= ' * @author ' . $pActiongroupInfos->author . "\n";
		$content .= ' * @link ' . $pActiongroupInfos->link . "\n";
		$content .= ' */' . "\n\n";

		$content .= '/**' . "\n";
		$content .= ' * ' . $pActiongroupInfos->description . "\n";
		$content .= ' * @package ' . $pActiongroupInfos->package . "\n";
		$content .= ' * @subpackage ' . $pActiongroupInfos->subpackage . "\n";
		$content .= ' */' . "\n";
		
		$content .= 'class ActionGroup' . ucfirst ($pActiongroupInfos->getName ()) . ' extends CopixActionGroup {' . "\n\n";
		
		// si on veut un beforeAction
		if (!is_null ($pBeforeAction) && is_array ($pBeforeAction)) {
			$content .= "\t" . 'public function beforeAction ($pActionName) {' . "\n";
			foreach ($pBeforeAction as $line) {
				$content .= "\t\t" . $line . "\n";
			}
			$content .= "\t" . '}' . "\n\n";
		}
		
		// si on veut un afterAction
		if (!is_null ($pAfterAction) && is_array ($pAfterAction)) {
			$content .= "\t" . 'public function afterAction ($pActionName, $pToReturn) {' . "\n";
			foreach ($pAfterAction as $line) {
				$content .= "\t\t" . $line . "\n";
			}
			$content .= "\t" . '}' . "\n\n";
		}
		
		// écriture des actions
		foreach ($pActionsInfos as $actionInfo) {
			$content .= "\t" . '/**' . "\n";
			$desc = (!is_null ($actionInfo->description)) ? $actionInfo->description : _i18n ('createmodule.enterDescription');
			$content .= "\t" . ' * ' . $desc .  "\n";
			$content .= "\t" . ' */' . "\n";
			$content .= "\t" . 'public function process' . ucfirst ($actionInfo->getName ()) . ' () {' . "\n";
			$content .= "\t\t" . '$ppo = new CopixPPO ();' . "\n";
			$content .= "\t\t" . '$ppo->TITLE_PAGE = \'\';' . "\n";
			$content .= "\t\t" . "\n";
			$content .= "\t\t" . "\n";
			$content .= "\t\t" . "\n";
			$content .= "\t\t" . 'return _arPPO ($ppo, \'\');' . "\n";
			$content .= "\t" . '}' . "\n\n";
		}
		
		$content .= '}';
		$content = $php->getPHPTags ("\n" . $content . "\n");
		CopixFile::write ($path . strtolower ($pActiongroupInfos->getName ()) . '.actiongroup.php', $content);
	}
	
	/**
	 * Créé un template
	 * 
	 * @param string $pModule Nom du module
	 * @param string $pName Nom du template
	 * @param string $pType Type du template (smarty, php)
	 */
	static function createTemplate ($pModule, $pName, $pType = 'smarty') {
		$path = CopixModule::getPath ($pModule) . 'templates/';
		if (!is_dir ($path)) {
			CopixFile::createDir ($path);
		}
		
		$ext = ($pType == 'smarty') ? 'tpl' : 'ptpl';
		$name = str_replace (' ', '_', $pName) . '.' . $ext;
		CopixFile::write ($path . $name, '');
	}
}
?>
