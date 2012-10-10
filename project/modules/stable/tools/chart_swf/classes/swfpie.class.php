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
* Camembert
* @package		tools 
 * @subpackage	chart_swf
* <code>
* //exemple d'utilisation/creation de Pie
* 	CopixClassesFactory::fileInclude("Chart|ChartFactory");
* $factory = ChartFactory::getChartFactory();
* $pie = $factory->createPie();
* $pie->setPlot("Label",array("libelle1"=>valeur1,"libelle2"=>valeur2,"lebelle3"=>valeur3));
* //Je veux un diagramme en 3D
* $pie->setOption("3d"=>true);
* //je veux mettre en evidence la part Libelle1, la valeur 15 est un % ... cf la doc swf charts
* $pie->splitPart(array("libelle1"=>15)); 
* //La fonction chart renvoie le code html a placé dans le tpl pour afficher le diagramme
* $tpl->assign("Diagramme",$pie->getChart());
* </code>
*/
class SWFpie extends SimpleSWFCharts implements pie{
	/**
	 * Constructeur de l'objet Bar
	 */	
	function __construct(){
		parent::__construct();
		$this->setTypeCharts("pie");
	}
	/**
	 * Fonction qui permet de faire ressortir une part du camembert
	 * @param array $pDissocier tableau de valeur pour la dissociation des parts du camembert
	 */
	public function splitPart($pDissocier){
		if(is_array($pDissocier)){
			$this->plotToData();
			if(is_array($this->_data['chart_data'][0])){
				//Cette fonction permet d'initialiser un tableau avec une meme valeur ici 0
				$pDissocierValide = array_fill(0,count($this->_data['chart_data'][0]),0);
								
				foreach($pDissocier as $key => $value){
					$pDissocierValide[array_search($key,$this->_data['chart_data'][0]) - 1] = $value;					
				}
				$this->editCharts('series_explode', $pDissocierValide);
			}
		}
	}	
	
	/**
	 * Cette fonction parcourt le tableau d'option et effectue un traitement pour chacune
	 */
	public function traiteOption(){
		if($this->getOption("3d")){
			$this->setTypeCharts("3d ".$this->getTypeCharts());
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