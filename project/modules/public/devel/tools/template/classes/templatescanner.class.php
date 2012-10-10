<?php
class TemplateScanner {
	/**
	* recherche tous les templates déclarés dans Copix.
	*/
	function scanStandardTemplates (){
		//Scan les templates des modules
		$modules = CopixModule::getList();
		foreach ($modules as $module){
			$templates[$module] = $this->_scanDirectory(CopixModule::getPath ($module).$module.'/'.COPIX_TEMPLATES_DIR, $module.'|');
		}
		return $templates;
	}

	function getNonStandardTemplatesList($newTemplates){
		//récupere la dao
		$templates = array();
		$daoTpls   = CopixDAOFactory::getInstanceOf ('copixtemplate');
		$daoThemes = CopixDAOFactory::getInstanceOf ('copixtemplate_theme');

		//Scan les templates des modules
		$modules = array_merge(array("") , CopixModule::getList());

		//recupere les templates modofiés
		$operator = ($newTemplates) ? '=' : '<>';
		$themes = array_merge(array(null),$daoThemes->findAll());

		foreach ($themes as $theme){
			foreach ($modules as $module){
				$criteres = CopixDAOFactory::createSearchParams ();
				$criteres->addCondition ('qualifier_ctpl', $operator, null);
				$criteres->addCondition ('modulequalifier_ctpl', '=', $module . "|");
				$criteres->addCondition ('id_ctpt', '=', isset ($theme) ? $theme->id_ctpt : null );

				foreach($daoTpls->findBy ($criteres) as $template){
					$moduleInformations = CopixModule::getInformations($module);
					//$templates[$theme->caption_ctpt][$module][] =$template;
					$templates[$theme->caption_ctpt][$moduleInformations->description][] = $template;
				}
			}
		}
		return $templates;
	}

	/**
	*  Scan a specific directory for templates
	*/
	function _scanDirectory ($directory, $matchingQualifier){
		$templates = array ();
		$dir = @opendir ($directory);
		while (false !== ($file = @readdir($dir))){
			if ($file != '.' && $file != '..' && is_file ($directory.$file)){
				if (substr ($file, -4) == '.tpl' || substr ($file, -5) == '.ptpl'){
					//le fichier porte bien une extention de type .tpl ou .ptpl
					if (CopixI18N::exists ($matchingQualifier.'templates.'.$file)){
						$templates[$matchingQualifier.$file] = CopixI18N::get($matchingQualifier.'templates.'.$file);
					}else{
						$templates[$matchingQualifier.$file] = $matchingQualifier.$file;
					}
				}
			}
		}
		@closedir ($dir);
		return $templates;
	}

	/**
	* gets the template content
	* @param $templateId The template copix id. Only works with CopixTemplates
	*/
	function getTemplateContent ($templateId){
		$fileSelector = & CopixSelectorFactory::create ($templateId);
		$fileName     = $fileSelector->fileName;
		$fileName     = $fileSelector->getPath(COPIX_TEMPLATES_DIR) . $fileName;
		if (is_readable($fileName)){
			return file_get_contents($fileName);
		}else{
			return null;
		}
	}

	/**
	* Gets the non installed themes
	*/
	function getNonInstalledThemes (){
		$daoThemes = CopixDAOFactory::getInstanceOf ('copixtemplate_theme');
		$arTheme = array ();
		foreach ($daoThemes->findAll () as $theme){
			$arTheme[] = $theme->id_ctpt;
		}
		$arNewThemes = array ();
		$dir = @opendir (COPIX_VAR_PATH.'data/templates/');
		while (false !== ($file = @readdir($dir))){
			if (($file != '.') && ($file != '..') && (is_dir(COPIX_VAR_PATH.'data/templates/'.$file))){
				if (!in_array ($file, $arTheme)){
					//le fichier porte bien une extention de type .tpl ou .ptpl
					$arNewThemes[] = $file;
				}
			}
		}
		@closedir ($dir);
		return $arNewThemes;
	}
}
?>