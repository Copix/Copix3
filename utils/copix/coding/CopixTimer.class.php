<?php
/**
 * @package copix
 * @subpackage utils
 * @author Croës Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file 
 */

/**
 * Classe pour permettre la mesure simple de temps entre deux appels à start et stop
 * Capable de gérer de compteurs "stackables"
 * 
 * @package copix
 * @subpackage utils
 * <code>
 *    //Timer simple
 *    $timer = new CopixTimer ();
 *    $timer->start ();
 *    //traitements long
 *    ...
 *    $duration = $timer->stop ();
 * 
 *    //Calculs de temps multiples
 *    $timer = new CopixTimer ();
 *    $timer->start ();
 *    //traitements longs.
 *       //Sous traitements longs
 *       $timer->start ();
 *       ...
 *       $durationSlice = $timer->stop ();
 *    $duration = $timer->stop (); 
 * </code>
 */
class CopixTimer {
	/**
	 * La liste des temps actuellement comptabilisés
	 * 
	 * @var array
	 */
	private $_timers = array ();
	
	/**
	 * Instances de timers crés avec la méthode get
	 *
	 * @var array
	 */
	private static $_instances = array ();
	
	/**
	 * création d'un timer (afin de facilement les partager lors de processus de debogages)
	 *
	 * @param string $pTimerName le nom du timer que l'on souhaite récupérer / créer
	 * @param boolean $pAutoStart si l'on démarre automatiquement le timer a sa création
	 * @return CopixTimer
	 */
	public static function get ($pTimerName, $pAutoStart = true) {
		if (!isset (self::$_instances[$pTimerName])) {
			self::$_instances[$pTimerName] = new CopixTimer ();
			if ($pAutoStart) {
				self::$_instances[$pTimerName]->start ();
			}
		}
		return self::$_instances[$pTimerName];		
	}

	/**
	 * Démarre un nouveau compteur
	 * 
	 * @return int le temps mesuré au moment du start
	 */
	public function start () {
		$time = $this->_getMicroTime ();
		array_push ($this->_timers, $time);
		return $time;
	}

	/**
	 * Arrête le compteur en cours et retourne le temps en secondes depuis le dernier appel à start
	 * 
	 * @param boolean $pShow Indique si on souhaites afficher le temps à l'écran
	 * @param boolean $pShowCaller Indique si on veut afficher le fichier et la ligne d'où on appelé stop
	 * @return float
	 */
	public function stop ($pShow = false, $pShowCaller = true) {
		$stop = $this->_getMicroTime ();
		$start = array_pop ($this->_timers);
		$elapsedTime = $this->_elapsedTime ($start, $stop);
		if ($pShow) {
			$this->_show ($elapsedTime, $pShowCaller);
		}
		return $elapsedTime;
	}
   
	/**
	 * Retourne l'intervalle de temps courant du compteur en cours sans l'arréter
	 * 
	 * @param boolean $pShow Indique si on souhaites afficher le temps à l'écran
	 * @param int $pTimerCount Numéro du timer que l'on veut récupérer
	 * @param boolean $pShowCaller Indique si on veut afficher le fichier et la ligne d'où on appelé getInter
	 * @return float
	 */
	public function getInter ($pShow = false, $pTimerCount = null, $pShowCaller = true) {
		if ($pTimerCount === null){
			$pTimerCount = count ($this->_timers) - 1;
		}

   		$stop  = $this->_getMicroTime ();
   		$start = $this->_timers [$pTimerCount];

		$elapsedTime = $this->_elapsedTime ($start, $stop);
		if ($pShow){
        	$this->_show ($elapsedTime, $pShowCaller);
      	}
      	return $elapsedTime;
	}

	/**
	 * Log l'interval de temps courant du compteur
	 *
	 * @param int $pTimerCount Numéro du timer que l'on veut loger
	 * @param string $pType Type du log
	 */
	public function logInter ($pTimerCount = null, $pType = 'copixtimer') {
		$inter = $this->getInter (false, $pTimerCount);

		$extras = array ();
		$caller = CopixDebug::getCaller ();
		$extras['caller_file'] = $caller['file'];
		$extras['caller_line'] = $caller['line'];
		if ($pTimerCount === null){
			$pTimerCount = count ($this->_timers) - 1;
		}
		$extras['timer_count'] = $pTimerCount;
		_log ($inter, $pType, CopixLog::INFORMATION, $extras);
	}
   
	/**
     * Retourne le temps actuel de la machine
     * 
     * @return int Temps courant en millisecondes
     */
	private function _getMicroTime () {
		return microtime(true);
	}
   
	/**
	 * Retourne le temps passé (en secondes) entre deux chiffres en microsecondes
	 * 
	 * @param int $pStartTime Temps de début en microsecondes
	 * @param int $pStopTime Temps d'arrêt en microsecondes
	 * @return float
	 */
	private function _elapsedTime ($pStartTime, $pStopTime) {
		return max (0, intval (($pStopTime - $pStartTime) * 1000) / 1000);
	}
	
	/**
	 * Affiche le temps écoulé
	 *
	 * @param float $pElapsedTime Temps écoulé à afficher
	 * @param boolean $pShowCaller Indique si on veut afficher le fichier et la ligne qui ont appelé
	 */
	private function _show ($pElapsedTime, $pShowCaller) {
		$echo = '<br />[CopixTimer] ';
		if ($pShowCaller) {
			$caller = CopixDebug::getCaller (1);
			$echo .= '[' . CopixFile::extractFileName ($caller['file']) . ' (' . $caller['line'] . ')] ';
		}
		$echo .= '<b>' . $pElapsedTime . '</b>';
		
		echo $echo;
	}
}