<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croës Gérald
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Objet capable de créer un CopixMOduleDescription depuis les fichiers XML parsés par CopixModule::getParsedModuleInformations
 * 
 * @package		copix
 * @subpackage	core
 */
class CopixModuleXmlParser {
	/**
	 * Pour spécifier le module auquel on s'interresse
	 * 
	 * @param string $pModuleName
	 */
	function __construct ($pModuleName){
		$this->_moduleName = $pModuleName;		
	}

	/**
	 * Retourne le CopixModuleDescription
	 *
	 * @param SimpleXmlElement $pXMLNode
	 * @return CopixModuleDescription
	 */
	public function getDescriptionFromXml ($pXMLNode){
		//On ne s'interresse qu'a la partie _moduleName
		return new CopixModuleDescription ($pXMLNode[$this->_moduleName][0]);
	}

	/**
	 * Retourne le CopixModuleDescription
	 *
	 * @param SimpleXmlElement $pXMLNode
	 * @return CopixModuleDescription
	 */
	public function getDefinitionFromXml ($pXMLNode){
		//On ne s'interresse qu'a la partie _moduleName 
		return new CopixModuleDefinition ($pXMLNode[$this->_moduleName][0]);
	}	
}