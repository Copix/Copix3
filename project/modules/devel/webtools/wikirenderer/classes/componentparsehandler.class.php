<?php
class ComponentParseHandler {
	
	public static function parseXml ($pXml) {
		$toReturn = array ();
		foreach ($pXml as $module=>$components) {
			$toReturn[$module] = array ();
			foreach ($components as $component) {
				$tempComponent          = array ();
				$tempComponent['name']  = (string)$component['name'];
				$tempComponent['class'] = (string)$module.'|'.(string)$component['class'];
				$toReturn[$module][]             = $tempComponent; 
			}
		}
		return $toReturn;
	}
	
	public static function getComponents () {
		return CopixModule::getParsedModuleInformation('wikirenderer_component', '/moduledefinition/wikirenderer/components/component', array ('ComponentParseHandler', 'parseXml'));
	}
	
	public static function getInstallComponents () {
		require (COPIX_VAR_PATH . 'config/wiki_component.conf.php');
		$arComponentToOrder = array ();
		$arComponentNoOrder = array ();
		$arLength = array ();
		if (!isset($wiki_components)) {
			$wiki_components = array ();
		}
		foreach ($wiki_components as $component) {
			$currentComponent = _class ($component);
			if (($length = $currentComponent->getLength ()) != null) {
				$arComponentToOrder[] = $currentComponent;
				$arLength[] = $length;
			} else {
				$arComponentNoOrder[] = $currentComponent;
			}
		}
		if (count ($arComponentToOrder) >0) {
			array_multisort($arLength, SORT_DESC, $arComponentToOrder);
		}
		return array_merge ($arComponentNoOrder, $arComponentToOrder);
	}
}
?>