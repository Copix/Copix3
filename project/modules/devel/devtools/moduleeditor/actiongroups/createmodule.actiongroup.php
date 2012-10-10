<?php
/**
 * @package devtools
 * @subpackage moduleeditor
 * @author		Steevan Barboyon
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions de création de module.
 */
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
		$modulePath = _request ('modulePath');
		$moduleName = _request ('moduleName');
		$moduleFullPath = $modulePath . $moduleName . '/';
		
		// vérification des données du formulaire
		$arErrors = $this->_verifCreateForm ();
		if (count ($arErrors) > 0) {
			throw new CopixException ($arErrors[0]);
		}
		
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
		
		// définition de la version
		$moduleVersion = (trim (_request ('moduleVersion')) != '') ? trim (_request ('moduleVersion')) : null;
		
		$moduleInfos = _class ('moduleinfos', array ($moduleName, $moduleDescription, $moduleLongDescription, $moduleDescriptionI18n, $moduleLongDescriptionI18n, $moduleVersion));
		
		// groupe du module sélectionné dans la liste
		if (_request ('moduleGroupIdSelect') != '') {
			
		// groupe du module à créer
		} else if (_request ('moduleGroupId') <> '') {
			$moduleInfos->group->id = _request ('moduleGroupId');
		}
		if (is_null (_request ('moduleGroupCaptionCreateI18n'))) {
			$moduleInfos->group->caption = (trim (_request ('moduleGroupCaption')) == '') ? null : _request ('moduleGroupCaption');
		} else {
			$moduleInfos->group->captionI18n = _request ('moduleGroupCaption');
		}
		
		Module::createModule ($modulePath, $moduleInfos);
		
		// si on veut créer un fichier i18n pour la description ou longdescription
		if (_request ('moduleDescriptionCreateI18n') || _request ('moduleLongDescriptionCreateI18n')) {
			// @todo : api dans le module languages pour créer un fichier i18n
		}
		
		// si on veut créer un actiongroup
		if (_request ('actiongroup') == 1) {
			_classInclude ('actiongroupinfos');
			_classInclude ('actioninfos');				
			
			// recherche du package (répertoire parent) et du subpackage (nom du module)
			$pathLinux = trim (str_replace ('\\', '/', $modulePath));
			$pathElements = explode ('/', $pathLinux);
			$package = $pathElements[count ($pathElements) - 2];
			$subpackage = $moduleName;
			
			$beforeAction = array ();
			
			// si on veut un credential sur tout l'actiongroup
			if (_request ('actiongroupCredential') != '') {
				$beforeAction[] = '_currentUser ()->assertCredential (\'' . _request ('actiongroupCredential') . '\');';
			}
			
			// informations sur l'actiongroup
			$actiongroupInfos = new ActiongroupInfos (_request ('actiongroupName'), _request ('actiongroupDescription'), _request ('actiongroupAuthor'), $package, $subpackage);
			
			// informations sur les actions
			$actionsInfos = array ();
			$actionIndex = 1;
			while (!is_null (_request ('actionName' . $actionIndex))) {
				if (trim (_request ('actionName' . $actionIndex)) <> '') {
					$credential = (trim (_request ('actionCredential' . $actionIndex)) != '') ? trim (_request ('actionCredential' . $actionIndex)) : null;
					$actionsInfos[$actionIndex] = new ActionInfos (_request ('actionName' . $actionIndex), _request ('actionDescription' . $actionIndex), $credential);
				}
				$actionIndex++;
			}
			
			// création de l'actiongroup
			Module::createActionGroup ($moduleName, $actiongroupInfos, $actionsInfos, $beforeAction);
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
		
		// si on veut créer des lien
		$links = array ();
		for ($boucle = 0; $boucle < 4; $boucle++) {
			$links[] = _class ('linkinfos', array ('module|cationgroup|action', 'caption', null, 'basic:admin'));
		}
		Module::xmlSetLinks ('test', $links);
			
        return CopixActionGroup::process ('generictools|Messages::getInformation',
			array ('message'=>_i18n ('createmodule.information.moduleSuccefullyCreated', $moduleName),
			'continue'=>_url  ('admin||')));
	}
	
	/**
	 * Vérifie la validité des données du formulaire de création
	 */
	private function _verifCreateForm () {
		$moduleName = trim (_request ('moduleName'));
		$modulePath = _request ('modulePath');		
		$arErrors = array ();
		
		// temp : pour ne pas avoir à supprimer le répertoire à la main à chaque test
		CopixFile::removeDir ('E:\Sites internet\Copix3\project/modules/public/stable/standard/test/');
		CopixModule::clearCache ();
		// fin temp
		
		// si le nom de module n'est pas valide
		if (!CopixModule::isValidName ($moduleName)) {
			$arErrors[] = _i18n ('createmodule.error.invalidModuleName', array ($moduleName));
		
		// si le nom de module n'est pas disponible
		} else if (!CopixModule::isAvailable ($moduleName)) {
			$arErrors[] = _i18n ('createmodule.error.moduleNameNotAvailable', array ($moduleName));
		}
		
		// si on n'a pas les droits d'écriture sur le répertoire demandé
		if (!is_writable ($modulePath)) {
			$arErrors[] = _i18n ('createmodule.error.dirNotWritable', array ($modulePath));
		}
		
		// si on n'a pas sélectionné de groupe en particulier
		if (_request ('moduleGroupIdSelect') == '') {
			if (_request ('moduleGroupId') != '' && _request ('moduleGroupCaption') == '') {
				$arErrors[] = _i18n ('createmodule.error.emptyGroupCaption');
			} else if (_request ('moduleGroupId') == '' && _request ('moduleGroupCaption') != '') {
				$arErrors[] = _i18n ('createmodule.error.emptyGroupId');
			}
		}
		
		return $arErrors;
	}
	
	/**
	 * Vérifie la validité des données du formulaire de création en Ajax
	 */
	public function processVerifCreate () {
		$ppo = new CopixPPO ();
		$ppo->arErrors = $this->_verifCreateForm ();
		return _arDirectPPO ($ppo, 'createmodule.verif.tpl');
	}
}
?>