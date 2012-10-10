<?php
/**
 * @package		copix
 * @subpackage	datasource
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Source de données pour un DAO
 * 
 * @package		copix
 * @subpackage	datasource
 */
class CopixDaoDatasource implements ICopixDataSource {
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
	 * Le dao
	 *
	 * @var object
	 */
	private $_dao = null;

	/**
	 * Ordre des champs
	 *
	 * @var unknown_type
	 */
	private $_fieldOrder = null;
	
	/**
	 * Identifiant du DAO
	 *
	 * @var string
	 */
	private $_daoid = null;
	
	/**
	 * Liste des champs qui permettent de connaitre les PK
	 * 
	 * @var array
	 */
	private $_fields = array ();
	
	/**
	 * Contient le SearchParam
	 * 
	 * @var CopixDAOSearchParams
	 */
	private $_sp = null;
	
	/**
	 * Nombre de d'enregistrements max pour la page
	 * 
	 * @var int
	 */
	private $_max = null;
	
	/**
	 * Nombre de pages du dernier retour
	 * 
	 * @var int
	 */
	private $_nbpages = null;
	
	/**
	 * Nombre d'enregistrements
	 *
	 * @var int
	 */
	private $_nbrecord = null;
		
	/**
	 * Constructeur
	 *
	 * @param array $pParams Paramètres : ct, dao, max.
	 */
	public function __construct ($pParams) {
		$this->_ct = (isset($pParams['ct'])) ? $pParams['ct'] : null;
		$this->_daoname = $pParams['dao'];
		$this->_max = (isset($pParams['max'])) ? $pParams['max'] : null;
		$this->_dao = _dao ($this->_daoname, $this->_ct);
		$this->_sp = _daoSP ();
		$this->_fields = $this->_dao->getFieldsDescription ();
	}

	/**
	 * Ajoute une condition
	 *
	 * @param string $pFieldID Nom du champ de la dao sur lequel on ajoute la condition
	 * @param string $pCondition Condition à appliquer (=, !=, <>, <, > like, ...)
	 * @param mixed $pValue Valeur de recherche (inutile de quotter les chaines)
	 * @param string $pKind Type de condition : AND ou OR
	 * @return CopixDAOSearchparams
	 */
	public function addCondition ($pFieldID, $pCondition, $pValue, $pKind = 'and') {
		$this->_sp->addCondition ($pFieldID, $pCondition, $pValue, $pKind);
		return $this;
	}

	/**
	 * Permet de rajouter directement du SQL dans la recherche
	 * 
	 * @param string $pSQL SQL à intégrer dans la requête
	 * @param array $pParams Tableau de paramètres relatifs à la chaine
	 * @param string $pKind Type de condition : AND ou OR
	 * @return CopixDAOSearchParams
	 */
	public function addSQL ($pSql, $pParams = array (), $pType = 'and') {
		$this->_sp->addSQL ($pSql, $pParams, $pType);
		return $this;
	}

	/**
	 * Début d'un groupe de conditions
	 *
	 * @return CopixDAOSearchparams
	 */
	public function startGroup ($pKind = 'AND') {
		$this->_sp->startGroup ($pKind);
		return $this;
	}

	/**
	 * Fin d'un groupe de conditions
	 *
	 * @return CopixDAOSearchparams
	 */
	public function endGroup () {
		$this->_sp->endGroup ();
		return $this;
	}
	
	/**
	 * DEPRECATED - utiliser orderBy à la place : Ajoute un ordre de tri
	 */
	public function addOrderBy ($pField, $pOrder = 'ASC') {
		$this->orderBy (array ($pField, $pOrder));
		return $this;
	}

	/**
	 * Ajoute un ordre de tri
	 *
	 * @param mixed $pArg array ('champ', 'ASC/DESC') ou string 'champ'
	 * @return CopixDAOSearchparams
	 */
	public function orderBy ($pArg) {
		$this->_sp->orderBy ($pArg);
	}
	
	/**
	 * Effectue une recherche
	 *
	 * @param int $pPage Numéro de la page à retourner
	 * @param string $pOrder Order de tri
	 * @param string $pSens Type de tri : ASC ou DESC
	 * @return array
	 */
	public function find ($pPage = 0, $pOrder = null, $pSens = 'ASC') {
		if ($pOrder != null) {
			$this->_sp->orderBy (array ($pOrder, $pSens));
		}
		$sp = $this->_sp;
		$this->_sp = _daoSP ();
		if ($this->_max !== null) {
			if ($this->_max!=0) {
				$this->_nbrecord = $this->_dao->countBy ($sp);
				$this->_nbpages = ceil ($this->_nbrecord / $this->_max);
			}
			$sp->setLimit ($this->_max * $pPage, $this->_max);
		}

		$results = $this->_dao->findBy ($sp);
		return $results->fetchAll ();
	}

	/**
	 * Retourne le nombre de pages
	 *
	 * @return int
	 */
	public function getNbPage () {
		return $this->_nbpages;
	}
	
	/**
	 * Retourne le nombre d'enregistrements
	 *
	 * @return int
	 */
	public function getNbRecord () {
		 return $this->_nbrecord;
	}
	
	/**
	 * Sauvegarde le record $pRecord dans la table
	 *
	 * @param array $pRecord Tableau des données à sauvegarder : array ('monChamp1' => 12, 'monChamp2' => 'test')
	 * @return object
	 */
	public function insert ($pRecord) {
		$daoRecord = CopixDAOFactory::createRecord ($this->_daoname);
		foreach ($pRecord as $key => $record) {
			$daoRecord->$key = $record;
		}
		$this->_dao->insert ($daoRecord);
		return $daoRecord;
	}

	/**
	 * Vérifie la validité des informations de $pRecord
	 *
	 * @param array $pRecord Tableau des données à vérifier : array ('monChamp1' => 12, 'monChamp2' => 'test')
	 * @return boolean
	 */
	public function check ($pRecord) {
		$daoRecord = CopixDAOFactory::createRecord ($this->_daoname);
		foreach ($pRecord as $key => $record) {
			$daoRecord->$key = $record;
		}
		return $this->_dao->check ($daoRecord);
	}
	
	/**
	 * Mise à jour des données
	 *
	 * @param array $pRecord Tableau des données à modifierr : array ('monChamp1' => 12, 'monChamp2' => 'test')
	 * @return ICopixDAORecord
	 */
	public function update ($pRecord) {
		$daoRecord = CopixDAOFactory::createRecord ($this->_daoname);
		foreach ($pRecord as $key => $record) {
			$daoRecord->$key = $record;
		}
		$this->_dao->update ($daoRecord);
		return $daoRecord;
	}
	
	/**
	 * Supprime un / des enregistrement(s)
	 *
	 * @params array Nombre de paramètres non définis, dépendant du delete du DAO
	 * @return int Nombre d'enregistrements supprimés, ou false si echec
	 */
	public function delete () {
		$pParams = func_get_args ();
		return call_user_func_array (array ($this->_dao, 'delete'), $pParams);
	}
	
	/**
	 * Retourne un enregistrement
	 *
	 * @params array Nombre de paramètres non définis, dépendant du get du DAO
	 * @return ICopixDAORecord
	 */
	public function get () {
		$pParams = func_get_args ();
		return call_user_func_array (array ($this->_dao,'get'), $pParams);
	}
	
	/**
	 * Retourne la clef primaire
	 *
	 * @return array Tableau de type array (0 => 'clefPrimaire1', 1 => 'clefPrimaire2')
	 */
	public function getPk () {
		$toReturn = array ();
		foreach ($this->_fields as $field) {
			if ($field->isPK) {
				$toReturn[] = $field->fieldName;
			} 
		}
		return $toReturn;
	}
	
	/**
	 * Retourne les champs du DAO
	 *
	 * @return array
	 */
	public function getFields () {
		return $this->_fields;
	}
}