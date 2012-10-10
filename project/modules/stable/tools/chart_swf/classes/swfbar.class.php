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
 * @ignore
 */ 
CopixClassesFactory::fileInclude ('chart_swf|SimpleSWFCharts');

/**
* Diagramme en baton (horizontal)
* @package		tools 
 * @subpackage	chart_swf
* <code>
* //exemple d'utilisation/creation de Column
* CopixClassesFactory::fileInclude("Chart|ChartFactory");
* $factory = ChartFactory::getChartFactory();
* $bar = $factory->createBar();
* $bar->setPlot("Label",array("libelle1"=>valeur1,"libelle2"=>valeur2,"lebelle3"=>valeur3));
* //Je veux un diagramme en 3D
* //je veux mettre stacker mes batons
* $bar->setOption("stacked"=>true);
* //La fonction chart renvoie le code html a placé dans le tpl pour afficher le diagramme
* $tpl->assign("Diagramme",$bar->getChart());
* </code>
*/
class SWFBar extends SimpleSWFCharts implements bar{

	/**
	 * Constructeur de l'objet Bar
	 */
	function __construct(){
		parent::__construct();
		$this->setTypeCharts("bar");
	}	
	
	/**
	 * Cette fonction parcourt le tableau d'option et effectue un traitement pour chacune
	 */
	public function traiteOption(){		
		if($this->getOption("stacked")){
			$this->setTypeCharts("stacked ".$this->getTypeCharts());
		}
		parent::traiteOption();
	}
	
	/**
	 * fonction qui traite l'objet et retourne le code html a afficher
	 * @param $width int largeur
	 * @param $height int hauteur
	 * @return code html a afficher
	 */
	public function getChart($width = 400, $height = 250){		
		$this->traiteOption();
		return parent::getChart($width,$height);
	}

}
?>