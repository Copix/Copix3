<?php
/**
* @package   standard
* @subpackage plugin_speedview
* @author   Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Plugin SpeedView qui permet d'afficher le temps de calcul pour une page donnée
 * @package standard
 * @subpackage speedview
 */
class PluginSpeedView extends CopixPlugin {
   /**
   * Le timer utilisé pour le calcul des temps
   * @var int
   */
	private $_timer = 0;

   /**
   * Indique si l'on souhaite calculer les temps
   * @var boolean
   */
   private $_speedprocess = false;

   /**
   * Constructeur
   * @param PluginConfigSpeedView $config La configuration du plugin 
   */
   function __construct ($config){
		parent::__construct ($config);
		$this->_timer = new CopixTimer ();
	}

   /**
   * Démarre le compteur de temps
   */
   function beforeSessionStart(){
		$this->_timer->start ();
	}

	/**
    * @param string  $pContent le contenu à afficher
    */
	function beforeDisplay (& $pContent){
		$elapsedTime = $this->_timer->stop ();
		switch ($this->config->trigger){
			case 'url':
				if (CopixRequest::get ('SpeedView') == 'show'){ 
             		$this->_speedprocess = true;
				}
				break;
			case 'display':
    			$this->_speedprocess = true;
				break;
		}

		if ($this->_speedprocess){
			switch ($this->config->target){
				case 'comment': 
				   $pContent = str_replace ('<head>', '<head><!-- '.$elapsedTime.' -->
', $pContent); 
				   break;
				case 'display':
					$pContent = str_replace ('</body>', $elapsedTime.'</body>', $pContent); 
					break;
				case 'log':
					_log ($elapsedTime, 'speedview', CopixLog::INFORMATION);
			}
		}
	}
}
?>