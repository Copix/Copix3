<?php
/**
 * @package copix
 * @subpackage tpl
 * @author Croës Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */


/**
 * Opérations sur les templates
 * @package copix
 * @subpackage tpl
 */
class CopixTplOperations {
	/**
	 * Retourne les templates dans un module avec l'extension donnée
	 * 
	 * Cette fonction va rechercher les templates dans le répertoire module template par défaut
	 * et également dans le répertoire de surcharge dans le thème default
	 * (project/modules/module_name/templates/* & COPIX_THEMES_PATH/default/module_name/*)
	 * 
	 * <code>
	 * $arTemplates = CopixTpl::find ('module_name', '.dyn.tpl');
	 * //recherche de plusieurs types de templates
	 * $arTemplates = CopixTpl::find ('module_name', array ('.dyn.tpl', '.dyn.ptpl'));
	 * //recherche avec masques
	 * $arTemplates = CopixTpl::find ('module_name', '.dyn.*');
	 * </code>
	 *	 
	 * @param string $pModuleName Nom du module dans lequel on va chercher le template
	 * @param mixed $pExtension Extension que l'on recherche (ou un tableau d'extensions)
	 * @return array Clé 'modules|fichier_trouvé' et en valeur le nom dans (tpl){*@name NOM} (ptpl)/*@name NOM si aucun nom n'est trouvé, on mets la clé
	 */
	public static function find ($pModuleName, $pExtension) {
		if (!is_array ($pExtension)) {
			$pExtension = array ($pExtension);
		}
		$files = array ();
		foreach ($pExtension as $extension){
			$files = array_merge ($files, CopixFile::search ('*' . $extension, CopixTpl::getThemePath ('default') . COPIX_TEMPLATES_DIR . $pModuleName . '/', false));
			$files = array_merge ($files, CopixFile::search ('*' . $extension, CopixModule::getPath ($pModuleName) . COPIX_TEMPLATES_DIR, false));
		}
		
		$arFiles = array ();
		foreach ($files as $key => $file) {
			$name = ($pModuleName . '|' . basename ($file));
			$tpl = CopixFile::read ($file);
			if (substr ($file, strlen ($file) - 4) == 'ptpl') {
				$nom = preg_replace ('".*\/*@name ([^\*]+[^/]+)\*/.*"','\1', $tpl);
			} else {
				$nom = preg_replace ('".*{*@name ([^\*]+[^}]+)\*\}.*"','\1', $tpl);
			}
			if (strlen ($nom) == strlen ($tpl)) {
				$nom = $name;
			}
			$arFiles[$name] = $nom;
		}
		return $arFiles;
	}
}