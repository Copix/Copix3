<?php
/**
 * @package cms_editor
 * @subpackage cms3
 * @copyright CopixTeam
 * @license	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Sylvain VUIDART, Steevan BARBOYON
 * @link http://www.copix.org
 */
class CmsWysiwygParser {
	/**
	 * Retourne les parseurs wysiwyg définit dans un module, callback de getParsedModuleInformation
	 *
	 * @param array $pModules Modules
	 * @return array
	 */
	public function _getParsersFromXML ($pModules) {
		$toReturn = array ();
		foreach ($pModules as $node) {
			foreach ($node as $parser) {
				$attributes = $parser->attributes ();
				$name = (string)$attributes['name'];
				$objet = _ioClass ($name);
				if ($objet instanceof ICmsWysiwygParser) {
					$toReturn[] = (string)$attributes['name'];
				}
			}
		}
		return $toReturn;
	}

	/**
	 * Retourne le texte passé dans tous es parseurs wysiwyg
	 *
	 * @param string $pText Texte de base
	 * @return string
	 */
	public function transform ($pText) {
		$parsers = CopixModule::getParsedModuleInformation ('CMSWysiwygParsers', "/moduledefinition/registry/entry[@id='CMSWysiwygParser']/*", array ($this, '_getParsersFromXML'));
		foreach ($parsers as $parser) {
			$pText = _ioClass ($parser)->transform ($pText);
		}
		return $pText;
	}
}