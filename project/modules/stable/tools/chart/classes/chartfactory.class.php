<?php
/**
* @package		tools 
 * @subpackage	chart
* @author    Landry Benguigui
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file

/**
* AbstractFactory qui gère l'ensemble des factory de graphes.
* 
* Utilisera le paramètre chart|chartfactory pour savoir quelle fabrique concrète utiliser
* @todo réaliser les implémentation des factory jpg classiques sans passer par swf
* @package		tools 
 * @subpackage	chart
* <code>
* //exemple d'utilisation
* $af = CopixClassesFactory::getInstanceOf("Chart|ChartFactory");
* $factory = $af->getChartFactory ();
* $pie = $factory->createPie ();
* </code>
*/
abstract class ChartFactory {	
    /**
	 * ici on retourne la factory en fonction des parametres dans module.xml
	 */	
	public static function getChartFactory(){
		switch(CopixConfig::get ('Chart|chartfactory')){			
			case 'SwfChart':			
				return CopixClassesFactory::getInstanceOf('chart_swf|SwfChartFactory');
			break;			
		}		
	}
	/**
	 * Création d'un graphique de type Pie
	 * @return Pie
	 */
	abstract public function createPie ();
	/**
	 * Création d'un graphique de type Column
	 * @return Column
	 */
	abstract public function createColumn ();
	/**
	 * Création d'un graphique de type Bar
	 * @return Bar
	 */
	abstract public function createBar ();
}

/**
 * Interface principale pour la génération de graphes
* @package		tools 
 * @subpackage	chart
 */
interface chart {}

/**
 * Interfaces des PieCharts
* @package		tools 
 * @subpackage	chart
 */
interface pie extends chart {
	public function splitPart($pDissocier);
}

/**
 * Interfaces des ColumnCharts
* @package		tools 
 * @subpackage	chart
 */
interface column  extends chart {}

/**
 * Interfaces des BarCharts
* @package		tools 
 * @subpackage	chart
 */
interface bar extends chart {}
?>