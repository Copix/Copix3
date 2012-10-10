<?php
/**
* @package		copix
* @subpackage	utils
* @author		Croës Gérald
* @copyright	2001-2008 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file 
*/

/**
 * Classe pour permettre la mesure simple de temps entre deux appels à start et stop.
 * Capable de gérer de compteurs "stackables"
 * @package		copix
 * @subpackage	utils
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
    * @var array
    */
   private $_timers = array ();

   /**
    * Démarre un nouveau compteur
    * @return	int	le temps mesuré au moment du start
    */
   public function start (){
      $time = $this->_getMicroTime ();
      array_push ($this->_timers, $time);
      return $time;
   }

   /**
    * Arrête le compteur en cours
    * @param	boolean 	$pShow	si l'on souhaites afficher le temps à l'écran (default false)
    * @return 	float 		le temps en secondes depuis le dernier appel à start
    */
   public function stop ($pShow = false){
      $stop  = $this->_getMicroTime ();
      $start = array_pop ($this->_timers);
      $elapsedTime = $this->_elapsedTime ($start, $stop);
      if ($pShow){
         echo $elapsedTime;
      }
      return $elapsedTime;
   }
   
   /**
    * Return l'intervalle de temps courant du compteur en cours sans l'arréter
    * 
    * @return	float	
    */
	public function getInter ($pShow = false){
   		$stop  = $this->_getMicroTime ();
   		$start = $this->_timers [count ($this->_timers)-1];

		$elapsedTime = $this->_elapsedTime ($start, $stop);
		if ($pShow){
        	echo $elapsedTime;
      	}
      	return $elapsedTime;
	}
   
	/**
     * Retourne le temps actuel de la machine
     * @return	int		l'heure courante
     */
	private function _getMicroTime (){
		return microtime(true);
	}
   
	/**
	 * Retourne le temps passé (en secondes) entre deux chiffres en microsecondes
	 * @param		int		$pStartTime l'heure de début en microsecondes
	 * @param		int 	$pStopTime	l'heure d'arrêt en microsecondes
	 * @return	float 	le temps en secondes écoulé
	 */
	private function _elapsedTime ($pStartTime, $pStopTime){
		return max (0, intval(($pStopTime - $pStartTime) * 1000) / 1000);
	}
}
?>