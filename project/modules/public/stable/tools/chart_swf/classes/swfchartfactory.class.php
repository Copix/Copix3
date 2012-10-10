<?php
/**
* @package		tools 
 * @subpackage	chart_swf
* @author    Landry Benguigui
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Factory qui gère l'ensemble des objets qui dependent de la librairie swf
* @package		tools 
 * @subpackage	chart_swf
*/
class SwfChartFactory extends ChartFactory {
    /**
     * Création d'un camembert
     * @return objet Pie
     */
    public function createPie(){    			
		return CopixClassesFactory::create ("chart_swf|SWFPie");
    }    
	/**
     * Création d'un histogramme vertical
     * @return objet Bar
     */
	public function createBar(){
		return CopixClassesFactory::create ("chart_swf|SWFBar");
	}	
	/**
     * Création d'un histogramme horizontal
     * @return objet Column
     */
	public function createColumn(){
		return CopixClassesFactory::create ("chart_swf|SWFColumn");
	}
}
?>