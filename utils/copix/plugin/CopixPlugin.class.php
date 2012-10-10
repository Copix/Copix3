<?php
/**
 * @package copix
 * @subpackage plugin
 * @author Croes Gérald, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion d'un plugin
 * 
 * @package copix
 * @subpackage plugin
 */
abstract class CopixPlugin implements ICopixPlugin {
	/**
	 * Objet de configuration dont la classe à pour nom nom.plugin.conf.php (nommage par défaut)
	 * 
	 * @var object
	 */
	protected $config;
	
 	/**
	 * Constructeur
	 * 
	 * @param object $pConfig Objet de configuration du plugin
	 */
	public function __construct ($pConfig = null) {
		$this->config = $pConfig;
	}
	
	/**
	 * Retourne l'objet de configuration du plugin
	 * 
	 * @return PluginConf
	 */
	public function getConfig () {
		return $this->config;
	}
}