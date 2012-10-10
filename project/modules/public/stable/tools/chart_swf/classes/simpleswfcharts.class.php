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
* Classe mère de tous les diagrammes swf
* @package		tools 
 * @subpackage	chart_swf
*/
abstract class SimpleSWFCharts{	
	/**
	 * variable qui contient les donnees du diagramme 
	 */
	protected $_data = null;	
	/**
	 * indique le type de diagramme de l'objet 
	 */	
	private $_typeCharts = null;	
		
	/**
	 * Clef pour l'unicité des données en session
	 * @var string
	 */
	private $_cle;
	/**
	 * tableau 
	 * @var array
	 */
	private $_plot;		

	/**
	 * tableau des options du diagramme
	 * @var array
	 */
	private $_option;
	/**
	 * Constructeur de l'objet simpleCharts on cree une cle unique pour la sauvegarde en session
	 */
	function __construct (){
		$this->_cle = md5(uniqid(rand(), true));
		$this->_data = array();
		$this->_plot = array();
		$this->_option = array();
	}
	
	/**
	 * Cette fonction permet d'editer le contenu total des données
	 * @param array $pData données a inserer
	 */
	private function setData ($pData){
		$this->_data['chart_data'] = $pData;
	}	
	/**
	 * Cette fonction ajoute des plots a l'objet
	 * @param $pLabel String variable qui definit le label
	 * @param $pValeur mixed valeur attaché au label
	 */
	public function setPlot($pLabel,$pValeur){
		$this->_plot[$pLabel] = $pValeur;
	}	
	/**
	 * Cette fonction sert a transformer les informations des plot en data swf valide
	 */
	protected function plotToData(){
		//cas particulier de la première case => un blanc
		$dataTemp = array();
		$dataTemp[0][0] = "";
		$entete = false;
		if(!empty($this->_plot)){
			foreach($this->_plot as $key => $value){				
				if(!$entete){
					$dataTemp[0] = array_merge($dataTemp[0],array_keys($value));
					$entete = true;
				}
				$dataTemp[] = array_merge(array($key),array_values($value));
			}
			$this->setData($dataTemp);		
		}
	}

	/**
	 * Cette fonction permet d'éditer une partie des données
	 * @param	string	$pTypeData	la partie des données a modifier (ex: 'chart_type')
	 * @param	array	$pdata		données a inserer
	 * 
	 * <code>
	 * //exemple d'utilisation de editcharts
	 * //modification des proprietes de l'axes des ordonnes
	 * $Charts->editCharts("axis_value",array("min"=>20,"max"=>120,"steps"=>6)); 
	 * //modification du type du chart
	 * $Charts->editCharts("chart_type","bar");
	 * </code>
	 */
	protected function editCharts($pTypeData, $pData){
		$this->_data[$pTypeData] = $pData;
	}
	
	/**
	 * Cette fonction renseigne le type de diagramme (bar,column ...) 
	 * @param String $pType Type du diagramme
	 */
	protected function setTypeCharts($pType) {
		$this->_typeCharts = $pType;	
	}

	/**
	 * retourne la valeur du champs type
	 * @return valeur du champs typeChart
	 */
	protected function getTypeCharts(){
		return $this->_typeCharts;	
	}
	
	/**
	 * génère le tableau de données
	 */
	protected function traiteOption(){
		//generation du tableau de donnée	
		$this->plotToData();
		$this->_data[ 'chart_type' ] = $this->_typeCharts;
		
		foreach($this->_option as $keys=>$value){
			/**
			 * @todo voir comment on peut mieux adapter le parametrage
			 */
			if($keys != "3d" && $keys != "stacked"){
				$this->_data[$keys] = $value;
			}
		}
	}
	
	/**
	 * ajoute le tableau $pOption au tableau d'option de l'objet
	 * @param boolean $pOption  
	 */
	public function setOption($pOption){
		//Ici on aurait bien evidemment pu mettre l'opérateur +, mais pour resté modeste on laisse array_merge
		$this->_option = array_merge($this->_option,$pOption);
	}
	
	/**
	 * Ici on retourne la valeur associé a une cle, ou si rien le tableau entier
	 * @param String $pKeys cle dont on veut la valeur
	 * @return valeur associé a une cle, ou le tableau entier
	 */
	public function getOption($pKeys = -1){
		if ($pKeys == -1){
			 return $this->_option;	
		}elseif (array_key_exists ($pKeys,$this->_option)){
			return $this->_option[$pKeys];
		}else{
			return false;
		}
	}
		
	/**
	 * retourne le code html qui sera attaché au tpl
	 * @return code html a passer au template
	 */
	protected function getChart ($width = 400, $height = 250){
		_classInclude ('chart_swf|CopixSwfChart');
		$chart = new CopixSwfChart ();
		CopixSession::set ("charts|datas|$this->_cle", $chart->getChartData ($this->_data));
		$swf = _class ('chart_swf|CopixSwfChart');
		return $swf->getChart (_url ("chart_swf||getChartsDatas", array("cle"=>$this->_cle)), $width, $height);
	}
}
?>