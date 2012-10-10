<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet l'autoload des DAO dans Copix
 * 
 * @package copix
 * @subpackage core 
 */
class CopixDAOAutoloader {
	/**
	 * Chargement de la classe demandée
	 *
	 * @param string $pClassName le nom de la classe recherchée
	 * @return boolean
	 */
	public static function autoload ($pClassName){
		$pClassName = strtolower ($pClassName);
		if (strpos ($pClassName, 'daorecord') === 0){
			return CopixDAOFactory::fileInclude (substr ($pClassName, 9));
		}
		if (strpos ($pClassName, 'dao') === 0){
			return CopixDAOFactory::fileInclude (substr ($pClassName, 3));
		}
		return false;
	}
}

spl_autoload_register (array ('CopixDAOAutoloader', 'autoload'));