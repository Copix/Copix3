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
 * Exceptions pour les classes de gestion des modules
 * /!\ Ne pas s'en servir à l'intérieur des modules, il est conseillé de faire une exception Module#NAME#Exception /!\
 * 
 * @package copix
 * @subpackage modules
 */
class CopixModuleException extends CopixException {
	/**
	 * XML de description de module non trouvé
	 */
	const XML_NOT_FOUND = 1;
	
	/**
	 * XML de description de module invalide
	 */
	const INVALID_XML = 2;
	
	/**
	 * Node general non trouvée dans le XML de description de module
	 */
	const GENERAL_NODE_NOT_FOUND = 3;
}