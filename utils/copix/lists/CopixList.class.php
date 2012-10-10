<?php
/**
 * @package		copix
 * @subpackage	lists
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

/*
 * CopixList
 *
 */
class CopixList {
	
	private $_id = null;

	private $_formid = null;
	
	private $_datasourceName = null;
	
	private $_datasourceParams = array ();
	
	private $_url = null;
	
	private $_orderField = null;
	
	private $_order = 'ASC';
	
	private $_currentPage = 1;
	
	private $_nbPage = 0;
	
	private $_datasource = null;
	
	private $_currentTemplate = null;
	private $_currentContext = null;
	
	private $_conditions = array ();
	
	private $_mapping = array ();
	
	/**
	 * Fait le choix des propriétés sauvées
	 *
	 * @return array
	 */
	public function __sleep () {
		return array ('_id', '_currentTemplate', '_currentContext', '_formid', '_datasourceName', '_datasourceParams', '_url', '_orderField', '_order', '_currentPage', '_nbPage', '_conditions', '_mapping');
	}
	
	/**
	 * Constructeur qui stock l'id de cette liste stocké en session
	 *
	 * @param string $pId l'id stocké en session
	 */
	public function __construct ($pId) {
		$this->_id = $pId; 
	}
	
	public function getListUrl () {
		return $this->_url;
	}
	
	public function getOrderBy () {
		return $this->_orderField;
	}
	
	public function getOrder () {
		return $this->_order;
	}
	
	/**
	 * Permet d'enregistrer le datasource
	 *
	 * @param array $pDatasourceParams liste des paramètres du datasource
	 * @param string $pDatasourceName Nom du datasource, par défaut C une dao
	 * @return mixed false si le datasource a echoué sinon renvoi le datasource
	 */
	public function setDatasource ($pDatasourceName, $pDatasourceParams = array ()) {
		$this->_datasourceName   = $pDatasourceName;
		$this->_datasourceParams = $pDatasourceParams;
		return $this;
	}
	
	public function setDao ($pDao, $pParams = array ()) {
		$pParams['dao'] = $pDao;
		return $this->setDatasource ('dao', $pParams);
	}
	
	public function addCondition () {
		$this->_conditions[] = func_get_args();
	}
	
  public function resetConditions () {
    $this->_conditions = array ();
  }

	public function addConditions ($pDatasource) {
		foreach ($this->_conditions as $conditions) {
			call_user_func_array(array($pDatasource, 'addCondition'), $conditions);
		}
	}
	
	/**
	 * Renvoi le datasource
	 *
	 * @return ICopixDatasource le datasource
	 */
	public function getDatasource () {
		if ($this->_datasource !== null) {
			return $this->_datasource;
		}
		
		if ($this->_datasourceName === null) {
			return false;
		}
		
		$this->_datasource = CopixDatasourceFactory::get ($this->_datasourceName, $this->_datasourceParams);
		return $this->_datasource;
	}

	public function setMapping($pMapping) {
	    $this->_mapping = $pMapping;
	}
	
	/**
	 * Renvoi le contenu HTML du tableau courant
	 *
	 * @param string $pTemplate template de rapprochement pour le retour
	 * @return string Le HTML
	 */
	public function getTable ($pTemplate = 'copix:/templates/copixlist.php') {

		if ($pTemplate != 'copix:/templates/copixlist.php') {
			$this->_currentTemplate = $pTemplate;
			$this->_currentContext = CopixContext::get();
		}
		
		if ($this->_formid != null) {
			$form = _form ($this->_formid);
			$form->addConditions ($this->getDatasource ());
		}

		$this->addConditions ($this->getDatasource());

		$tpl = new CopixTpl ();

		//Récupère le résultats de la recherche
		try {
			$results = $this->find ();
		} catch (Exception $e) {
			$results = array ();
			$tpl->assign ('error', $e->getMessage ());
		}

		
				
		//On récupère la liste des champs du datasource pour faire le mapping des colonnes
		if ($this->_mapping == null) {
    		$this->_mapping = array ();
    		if (is_array($this->_datasource->getFields ())) {
	            foreach ($this->_datasource->getFields () as $key=>$result) {
	                $this->_mapping[$result->name] = $result->fieldName;
	            }
    		}
		}
		$tpl->assign ('class', 'CopixTable');
		$tpl->assign ('mapping', $this->_mapping);
		$tpl->assign ('results', $results);
		$tpl->assign ('idlist', $this->_id);
		

		if ($this->_currentTemplate != null) {
			$pTemplate = $this->_currentTemplate;
			CopixContext::push ($this->_currentContext);
		}

		$toReturn = $tpl->fetch ($pTemplate);
		if ($this->_currentTemplate != null) {
		    CopixContext::pop();
		}
		return $toReturn;
	}
	
	public function setFormId ($pId) {
		$this->_formid = $pId;
	}
	
	public function getFormId () {
		return $this->_formid;
	}
	
	public function find () {
		if ($this->getDatasource () === false) {
			throw new CopixException ('Problème avec le datasource');
		}
		if (isset ($this->_orderField)) {
			$this->getDatasource()->addOrderBy ($this->_orderField, $this->_order);
		}
		$result = $this->getDatasource ()->find ($this->_currentPage-1);
		$this->_nbPage = $this->getDatasource()->getNbPage ();
		if (($this->_nbPage !=0) && $this->_nbPage < $this->_currentPage) {
		    $this->_currentPage = $this->_nbPage;
		    $result = $this->getDatasource ()->find ($this->_currentPage-1);
		}
		return $result;
	}
	
	public function setPage ($pKind) {
		switch ($pKind) {
			case 'first':
				$this->_currentPage = 1;
				break;
			case 'previous':
				$this->_currentPage--;
				if ($this->_currentPage < 1) {
					$this->_currentPage = 1;
				}
				break;
			case 'next':
				$this->_currentPage++;
				if ($this->_currentPage > $this->_nbPage) {
					$this->_currentPage = $this->_nbPage;
				}
				break;
			case 'last':
				$this->_currentPage = $this->_nbPage;
				break;
			case 'self':
				break;
			default:
				$this->_currentPage = 1;
				
		}
		if (!isset($this->_currentPage)) {
			$this->_currentPage = 1;
		}
	}
	
	public function setOrderBy ($pField) {
		if (isset ($this->_orderField) && $this->_orderField == $pField) {
			$this->_order = ($this->_order == 'ASC') ? 'DESC' : 'ASC';
		} else {
			$this->_order = 'ASC';
		}
		$this->_orderField = $pField;
	}
	
	public function getHTML ($pTemplate = 'copix:/templates/copixlist.php', $pMaj = true) {
	    _tag ('mootools');
		CopixHTMLHeader::addJSLink(_resource('js/taglib/copixlist.js'));
		CopixHTMLHeader::addJSDOMReadyCode(
		"
		    Copix.register_copixlist ('$this->_id',{formid:'$this->_formid'});
		"
		);
		if ($pTemplate != 'copix:/templates/copixlist.php') {
			$this->_currentTemplate = $pTemplate;
		}
		$this->_url = _url ('#');
		$toReturn  = '<div id="'.$this->_id.'">';
		if ($pMaj) {
			$toReturn .= $this->getTable ($pTemplate);
		}
		$toReturn .= '</div>';
		return $toReturn;
	}
	
	public function getOrderUrl ($pField) {
		return _url ('generictools|copixlist|orderby', array ('field'=>$pField, 'currentForm'=>$this->_formid, 'currentList'=>$this->_id));
	}
	
	public function getGoToUrl ($pKind) {
		return _url ('generictools|copixlist|goto', array ('kind'=>$pKind, 'currentForm'=>$this->_formid, 'currentList'=>$this->_id));
	}
	
	public function getCurrentPage () {
		return $this->_currentPage;
	}
	
	public function getNbPage () {
		return  $this->_nbPage;
	}
        public function setCurrentTemplate($pTemplate){
            $this->_currentTemplate = $pTemplate;
        }
}