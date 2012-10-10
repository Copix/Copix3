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
* Diagramme en baton (vertical)
* @package		tools 
 * @subpackage	chart_swf
* <code>
* //exemple d'utilisation/creation de Column
* CopixClassesFactory::fileInclude("Chart|ChartFactory");
* $factory = ChartFactory::getChartFactory();
* $column = $factory->createColumn();
* $column->setPlot("Label",array("libelle1"=>valeur1,"libelle2"=>valeur2,"lebelle3"=>valeur3));
* //Je veux un diagramme en 3D
* $column->setOption("3d"=>true);
* //je veux mettre stacker mes batons
* $column->setOption("stacked"=>true);
* //La fonction chart renvoie le code html a placé dans le tpl pour afficher le diagramme
* $tpl->assign("Diagramme",$column->getChart());
* </code>
*/
class SWFColumn extends SimpleSWFCharts implements column{
	
	/**
	 * Constructeur de l'objet Column
	 */
	function __construct(){
		parent::__construct();
		$this->setTypeCharts("column");
	}
	
	/**
	 * Cette fonction parcourt le tableau d'option et effectue un traitement pour chacune
	 */
	public function traiteOption(){
		if($this->getOption("3d")){
			$this->setTypeCharts("3d ".$this->getTypeCharts());
		}
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