<?php
/**
* @package		standard
* @subpackage	copixtest
* @author		Croës Gérald
* @copyright	2001-2008 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package		standard
 * @subpackage	copixtest
 */
class CopixTest_CopixTimerTest extends CopixTest {

	/**
	 * Test des fonctionnalités simples de timing
	 */
	function testSimple (){
		$timer = new CopixTimer ();

		// Test du timer
		$timer->start ();
		usleep (100000);
		$result = $timer->stop ();
		$this->assertTrue ($result >= 0.1);
		$this->assertTrue ($result <= 0.2);


		// Test de la récupération d'un intervalle
		$timer->start ();
		usleep (100000);
		$result = $timer->getInter();
		$this->assertTrue ($result >= 0.1);
		$this->assertTrue ($result <= 0.2);
		usleep (100000);
		$result = $timer->stop ();
		$this->assertTrue ($result >= 0.2);
		$this->assertTrue ($result <= 0.3);
	}

	/**
	 * Test d'appels multiples
	 */
	public function notestNestedCalls (){
		ob_start ();
		$results = array ();

		$timer = new CopixTimer ();
		$arrayOfWaitingTime = array (100000, 200000, 300000, 400000);
		$sumWaitingTime = array ();
		foreach ($arrayOfWaitingTime as $position=>$sleepFor){
			$timer->start ();
			usleep ($sleepFor);
		}

		//parcours pour vérifier que les stop sont tous ok et correspondent à plus de leur intervalle respectifs.
		$totalTime = 0;
		$stop = 0;
		for ($i=0; $i<count ($arrayOfWaitingTime); $i++){
			$stop = $timer->stop ();
			$totalTime += $stop;
			$this->assertTrue ($stop > (array_sum (array_slice ($arrayOfWaitingTime, count ($arrayOfWaitingTime) - ($i)))/1000000));
		}
		$this->assertTrue ($totalTime > (array_sum ($arrayOfWaitingTime) / 1000000));
		$buff = ob_end_clean ();
		$this->assertEquals ($buff, '');
	}

	public function testShow (){
		// Test d'affichage d'un timer
		ob_start ();
		$timer = new CopixTimer ();
		$timer->start ();
		usleep (100000);
		$result = $timer->stop (true);
		$this->assertTrue ($result >= 0.1);
		$this->assertTrue ($result <= 0.2);
		$buff = ob_end_clean ();
		$this->assertNotEquals ($buff, '');

		// Test d'affichage d'un intervalle
		ob_start ();
		$timer->start ();
		usleep (100000);
		$result = $timer->getInter(true);
		$this->assertTrue ($result >= 0.1);
		$this->assertTrue ($result <= 0.2);
		$buff = ob_end_clean ();
		$this->assertNotEquals ($buff, '');

	}

}
?>