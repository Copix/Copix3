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
abstract class CopixPlugin {
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

	/**
	 * Appelée avant l'appel à session_start
	 */
	public function beforeSessionStart () {}

	/**
	 * Appelée avant l'exécution de l'action demandée
	 * 
	 * @param CopixAction $pAction Descripteur de l'action demandée
	 */
	public function beforeProcess (&$pAction) {}

	/**
	 * Appelée après l'exécution de l'action
	 * 
	 * @param CopixActionReturn $pActionReturn Retour de l'action
	 */
	public function afterProcess ($pActionReturn) {}

	/**
	 * Appelée avant l'affichage
	 * 
	 * @param string $pContent Contenu à afficher
	 */
	public function beforeDisplay (&$pContent) {}
	
	/**
	 * Appelée après l'affichage
	 */
	public function afterDisplay () {}
}