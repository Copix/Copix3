<?php

class ActionGroupCreateModule extends CopixActionGroup {
	
	/**
	 * Executé avant toute action
	 */
	public function beforeAction ($pAction) {
		_classInclude ('module');
	}
	
	/**
	 * Formulaire de création du module
	 */
	public function processDefault () {
		$ppo = new CopixPPO ();		
		$ppo->TITLE_PAGE = _i18n ('createmodule.title.createModule');
		
		return _arPpo ($ppo, 'createmodule.tpl');
	}
	
	/**
	 * Création même du module
	 */
	public function processCreate () {
		CopixRequest::assert ('modulePath');
		$modulePath = _request ('modulePath');
		$moduleName = _request ('moduleName');
		$moduleFullPath = $modulePath . $moduleName . '/';
		
		// si le nom de module est valide, et disponible
		if (Module::isAvailable ($moduleName)) {
			// définition de la description et descriptioni18n
			if (_request ('moduleDescriptionCreateI18n_temp')) {
				$moduleDescription = null;
				$moduleDescriptionI18n = 'module.description';
				$arI18nModule['description'] = _request ('moduleDescription');
			} else {
				$moduleDescription = _request ('moduleDescription');
				$moduleDescriptionI18n = null;
			}
			
			// définition de longdescription et longdescriptioni18n
			if (_request ('moduleLongDescriptionCreateI18n_temp')) {
				$moduleLongDescription = null;
				$moduleLongDescriptionI18n = 'module.longdescription';
				$arI18nModule['longdescription'] = _request ('moduleLongDescription');
			} else {
				$moduleLongDescription = _request ('moduleLongDescription');
				$moduleLongDescriptionI18n = null;
			} 
			
			$moduleInfos = _class ('moduleinfos', array ($moduleName, $moduleDescription, $moduleLongDescription, $moduleDescriptionI18n, $moduleLongDescriptionI18n));
			Module::createModule ($modulePath, $moduleInfos);
			
			// si on veut créer un fichier i18n pour la description ou longdescription
			if (_request ('moduleDescriptionCreateI18n') || _request ('moduleLongDescriptionCreateI18n')) {
				// @todo : api dans le module languages pour créer un fichier i18n
			}
			
			// si on veut créer un actiongroup
			if (_request ('actiongroup') == 1) {
				_classInclude ('actiongroupinfos');
				_classInclude ('actioninfos');				
				
				$pathLinux = trim (str_replace ('\\', '/', $modulePath));
				$pathElements = explode ('/', $pathLinux);
				$package = $pathElements[count ($pathElements) - 2];
				$subpackage = $moduleName;
				
				$actiongroupInfos = new ActiongroupInfos (_request ('actiongroupName'), _request ('actiongroupDescription'), _request ('actiongroupAuthor'), $package, $subpackage);
				$actionsInfos = array ();
				$actionIndex = 1;
				while (!is_null (_request ('actionName' . $actionIndex))) {
					if (_request ('actionName' . $actionIndex) <> '') {
						$actionsInfos[$actionIndex] = new ActionInfos (_request ('actionName' . $actionIndex), _request ('actionDescription' . $actionIndex));
					}
					$actionIndex++;
				}
				Module::createActionGroup ($moduleName, $actiongroupInfos, $actionsInfos);
			}
			
			// si on veut créer des templates
			if (_request ('templates') == 1) {
				$templateIndex = 1;
				while (!is_null (_request ('templateName' . $templateIndex))) {
					if (_request ('templateName' . $templateIndex) <> '') {
						Module::createTemplate ($moduleName, _request ('templateName' . $templateIndex), _request ('templateType' . $templateIndex));
					}
					$templateIndex++;
				}
			}
			
			$links = array ();
			for ($boucle = 0; $boucle < 4; $boucle++) {
				$links[] = _class ('linkinfos', array ('module|cationgroup|action', 'caption', null, 'basic:admin'));
			}
			Module::xmlSetLinks ('test', $links);
			
		// si le nom de module n'est pas disponible
		} else {
			$i18n = (Module::isValid ($moduleName)) ? 'createmodule.error.nameNotAvailable' : 'createmodule.error.nameNotValid';
			throw new CopixException (_i18n ($i18n, array ($moduleName)));
		}
		
		return _arNone ();
	}
}
?>
