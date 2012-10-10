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

/**
 * 
 */
class CopixDaoDatasource {

    /**
     * Connexion si différente de default 
     *
     * @var string
     */
    private $_ct = null;

    /**
     * La dao a créer
     *
     * @var string
     */
    private $_daoname = null;

    /**
     * La dao
     *
     * @var StdClass
     */
    private $_dao = null;

    private $_fieldOrder = null;
    
    private $_daoid = null;
    
    /**
     * Liste des champs (type champ de dao)
     * qui permet de connaitre les pk
     */
    private $_fields = array ();
    
    /**
     * Contient le SearchParam
     */
    private $_sp = null;
    
    /**
     * Nombre de max d'enregistrement pour la pager
     */
    private $_max = null;
    
    /**
     * Nombre de page du dernier retour
     */
    private $_nbpages = null;
    
    private $_nbrecord = null;
    
    public function __wakeup () {
        $this->_dao = $this->_dao->getSessionObject (); 
    }
    
    public function __sleep () {
	    $this->_dao = new CopixSessionObject ($this->_dao);
	    return array_keys (get_object_vars ($this)); 
    }
    
    public function __construct ($pParams) {
        $this->_ct      = isset($pParams['ct']) ? $pParams['ct'] : null;
        $this->_daoname = $pParams['dao'];
        $this->_max     = isset($pParams['max']) ? $pParams['max'] : null;
        $this->_dao     = _dao ($this->_daoname,$this->_ct);
        $this->_sp      = _daoSP ();
        $this->_fields  = $this->_dao->getFieldsDescription ();
    }

    public function addCondition ($pField, $pCond, $pValue, $pType = 'and') {
        $this->_sp->addCondition ($pField, $pCond, $pValue, $pType);
    }

    public function startGroup () {
        $this->_sp->startGroup ();
    }

    public function endGroup () {
        $this->_sp->endGroup ();
    }
    
    public function addOrderBy ($pField, $pOrder = 'ASC') {
        $this->_sp->orderBy(array($pField,$pOrder));
    }
    
    public function find ($page=0, $pOrder=null, $pSens='ASC') {
        if ($pOrder!=null) {
            $this->_sp->orderBy(array ($pOrder, $pSens));
        }
    	if ($this->_max !== null) {
    	    if ($this->_max!=0) {
    	        $this->_nbrecord = $this->_dao->countBy ($this->_sp);
    	        $this->_nbpages = round ($this->_nbrecord/$this->_max);
    	    }
    	    $this->_sp->setLimit($this->_max*$page,$this->_max);
    	}
    	
    	$results = $this->_dao->findBy($this->_sp);
    	
    	$this->_sp      = _daoSP ();
    	
        return $results->fetchAll();
    }

    public function getNbPage () {
    	return $this->_nbpages;
    }
    
    public function getNbRecord () {
         return $this->_nbrecord;
    }
    
    public function save ($pRecord) {
        $daoRecord = CopixDAOFactory::createRecord ($this->_daoname);
        foreach ($pRecord as $key=>$record) {
            $daoRecord->$key = $record;
        }
        $this->_dao->insert($daoRecord);
        return $daoRecord;
    }

     public function check ($pRecord) {
        $daoRecord = CopixDAOFactory::createRecord ($this->_daoname);
        foreach ($pRecord as $key=>$record) {
            $daoRecord->$key = $record;
        }
        return $this->_dao->check($daoRecord);
    }
    
    public function update ($pRecord) {
        $daoRecord = CopixDAOFactory::createRecord ($this->_daoname);
        foreach ($pRecord as $key=>$record) {
            $daoRecord->$key = $record;
        }
        $this->_dao->update($daoRecord);
        return $daoRecord;
    }

    public function delete () {
        $pParams = func_get_args ();
        return call_user_func_array (array($this->_dao,'delete'), $pParams);
    }
    
    public function get () {
        $pParams = func_get_args ();
        return call_user_func_array (array($this->_dao,'get'), $pParams);
    }
    
    public function getPk () {
        $toReturn = array ();
        foreach ($this->_fields as $field) {
            if ($field->isPK) {
                $toReturn[] = $field->fieldName;
            } 
        }
        return $toReturn;
    }
    
    public function getFields () {
        return $this->_fields;
    }
}
?>