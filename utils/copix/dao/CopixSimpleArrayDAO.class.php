<?php
/**
 * @package tools
 * @subpackage ilmtools
 *
 * @author Morel Raoul (ilmir)
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Dao pour un fichier contenant un tableau de données
 * Gère les accés en lecture uniquement
 *
 * @package tools
 * @subpackage ilmtools
 */
abstract class CopixDAOSimpleArray implements ICopixDAO {
    /**
     * Extention des fichiers de données
     *
     * @var string
     */
    const EXT_FILE = ".data.php";
    /**
     * Connection à utiliser (nom du répertoire racine pouvant contenir les données)
     * Répertoire resources/ du module si non spécifié
     *
     * @var string
     */
    private $_connectionName = "";
    /**
     * Mémorisation de la demande en cours pour la génération d'erreurs
     *
     * @var string
     */
    private $_currentSP = "";
    /**
     * Permet de récupérer le nom du dao
     *
     * @return string
     */
    abstract public function getDAOId ();
    /**
     * Constructeur
     *
     * @param string $pConnectionName permet de spécifier un nouveau répertoire racine des données
     * @see ICopixDAO
     */
    public function __construct ($pConnectionName = null) {
        if ( $pConnectionName !== null ) {
            $this->_connectionName = $pConnectionName;
        }else {
            $this->_connectionName = CopixModule::getPath (CopixContext::get ()).COPIX_RESOURCES_DIR;
        }
    }
    /**
     * Retourne tous les enregistrements d'une table
     *
     * @return CopixDAORecordIterator
     * @see ICopixDAO
     */
    public function findAll () {
        return new CopixDAORecordIterator ($this->_getData (),$this->getDAOId ());
    }
    /**
     * Recherche selon des critères
     *
     * @param: CopixDAOSearchParams  $pSearchParams : Paramètres de recherche
     * @param: array $pLeftJoin : Non implémenté, lève une CopixException si non vide
     * @return CopixDAORecordIterator
     * @throws CopixException
     * @see ICopixDAO
     */
    public function findBy (CopixDAOSearchParams $pSearchParams, $pLeftJoin = array ()) {
        if ( count($pLeftJoin) > 0 ) {
            throw new CopixException (_i18n ('copix:copixsimplearraydao.error.join',array ($this->getDAOId ())));
        }
        $this->_currentSP = "<pre>".$pSearchParams->explainPHPSQL(array())."</pre>";
        $result = $this->_getData ();
        $result = $this->_applySelect ($result,$pSearchParams);
        $result = $this->_applyOrder ($result,$pSearchParams);
        $result = $this->_applyLimit ($result,$pSearchParams);
        return new CopixDAORecordIterator ( $result,$this->getDAOId ());
    }
    /**
     * Renvoie le nombre d'enregistrements selon des critères
     *
     * @param: CopixDAOSearchParams  $pSearchParams : Paramètres de recherche
     * @return int
     * @see ICopixDAO
     */
    public function countBy (CopixDAOSearchParams $pSearchParams) {
        return count ($this->findBy ($pSearchParams));
    }
    /**
     * Non implémenté, lève une erreur si utilisé
     *
     * @throws CopixException
     * @see ICopixDAO
     */
    public function insert ($pObject, $pUseId = false) {
        throw new CopixException (_i18n ('copix:copixsimplearraydao.error.insert',array ($this->getDAOId ())));
    }
    /**
     * Non implémenté, lève une erreur si utilisé
     *
     * @throws CopixException
     * @see ICopixDAO
     */
    public function update ($pObject, $pUseId = false) {
        throw new CopixException (_i18n ('copix:copixsimplearraydao.error.update',array ($this->getDAOId ())));
    }
    /**
     * Non implémenté, lève une erreur si utilisé
     *
     * @throws CopixException
     * @see ICopixDAO
     */
    public function deleteBy (CopixDAOSearchParams $pSearchParams) {
        throw new CopixException (_i18n ('copix:copixsimplearraydao.error.deleteby',array ($this->getDAOId ())));
    }

    /**
     * Retourne les données vérifiant les critères de sélections
     *
     * @param array $pData tableau de données
     * @param CopixDAOSearchParams $pSearchParams critères à appliquer
     * @return array
     */
    protected function _applySelect ($pData,$pSearchParams) {
        $result = array ();
        foreach ($pData as $entree) {
            if ( $this->_testGroupConditions ($entree,$pSearchParams->condition) ) {
                $result[] = $entree;
            }
        }
        return $result;
    }
    /**
     * Test si oui ou non l'entrée correspond aux critères
     *
     * @param array $pEntree tableau représentant une entrée des données
     * @param CopixDAOSearchParamsCondition  $pSPConditions critères à appliquer
     * @throws CopixException : genre de condition inconnu (AND et OR uniquement)
     * @return boolean
     */
    protected function _testGroupConditions ($pEntree,$pSPConditions) {
        $result = $this->_testArConditions ($pEntree,$pSPConditions->conditions,strtoupper ($pSPConditions->kind));
        if ( strtoupper ($pSPConditions->kind) == 'AND' ) {
            foreach( $pSPConditions->group as $group ) {
                if ($result === false) {
                    break;
                }
                $result = ($result && $this->_testGroupConditions ($pEntree,$group));
            }
        }else if ( strtoupper ($pSPConditions->kind) == 'OR' ) {
            foreach ( $pSPConditions->group as $group ) {
                if ($result === true) {
                    break;
                }
                $result = ($result || $this->_testGroupConditions ($pEntree,$group));
            }
        }else {
            throw new CopixException (_i18n ('copix:copixsimplearraydao.error.unknowkind',array ($this->getDAOId (),$pSPConditions->kind,$this->_currentSP)));
        }
        return $result;
    }
    /**
     * Test si oui ou non l'entrée est valide par rapport aux conditions
     *
     * @param array $pEntree tableau représentant une entrée des données
     * @param array $pArCondtions liste de conditions à vérifier
     * @param string $pKind : genre de vérification (AND ou OR)
     * @throws CopixException : genre de condition inconnu (AND et OR uniquement)
     * @return boolean
     */
    protected function _testArConditions ($pEntree,$pArCondtions,$pKind) {
        if ( $pKind == 'AND' ) {
            $result = true;
            foreach ( $pArCondtions as $condition ) {
                $result = ($result && $this->_testCondition ($pEntree,$condition));
                if ($result === false) {
                    break;
                }
            }
        }else if ( $pKind == 'OR' ) {
            $result = false;
            foreach ( $pArCondtions as $condition ) {
                $result = ($result || $this->_testCondition ($pEntree,$condition));
                if ($result === true) {
                    break;
                }
            }
        }else {
            throw new CopixException (_i18n ('copix:copixsimplearraydao.error.unknowkind',array ($this->getDAOId (),$pKind,$this->_currentSP)));
        }
        return $result;
    }
    /**
     * Test si oui ou non l'entr�e est valide par rapport à la condition
     *
     * @param array $pEntree tableau tableau représentant une entrée des données
     * @param array $pCondtion condition à vérifier
     * @throws CopixException : il s'agit d'une condition sql, non supportée
     * @return boolean
     */
    protected function _testCondition ($pEntree,$pCondition) {
        $result = false;
        if (isset ($pCondition['sql'])) {
            throw new CopixException (_i18n ('copix:copixsimplearraydao.error.nosqlcondition',array ($this->getDAOId (),$this->_currentSP)));
        }else {
            $champ = $pEntree[$pCondition['field_id']];
            $comparateur = $pCondition['condition'];
            if (!is_array ($pCondition['value'])) {
                $result = $this->_testConditionValue ($champ,$comparateur,$pCondition['value']);
            }else {
                foreach ($pCondition['value'] as $value) {
                    $result = ($result || $this->_testConditionValue ($champ,$comparateur,$value));
                    if ($result === true) {
                        break;
                    }
                }
            }
        }
        return $result;
    }
    /**
     * Compare le champ et la valeur
     * Comparaisons supportée :
     * si valeur est null  =, != et <>
     * sinon  =, !=, <>, >, >=, <, <=, like
     *
     * @param mixed $pChamp donnée à comparer
     * @param string $pComparateur comparateur
     * @param mixed $pValue valeur de comparaison
     * @throws CopixException : comparaison non supportée
     * @return boolean
     */
    protected function _testConditionValue ($pChamp,$pComparateur,$pValue) {
        $result = false;
        if ( $pValue === null ) {
            if ( $pComparateur == '=') {
                $result = ($pChamp === null);
            }else if ( $pComparateur == '!=' || $pComparateur == '<>') {
                $result = ($pChamp !== null);
            }else {
                throw new CopixException (_i18n ('copix:copixsimplearraydao.error.unsupportnullcomp',array ($this->getDAOId (),$pComparateur,$this->_currentSP)));
            }
        }else {
            switch ( $pComparateur ) {
                case "=" :
                    $result = ( $pChamp == $pValue );
                    break;
                case "!=":
                case "<>":
                    $result = ( $pChamp != $pValue );
                    break;
                case ">":
                    $result = ( $pChamp > $pValue );
                    break;
                case ">=":
                    $result = ( $pChamp >= $pValue );
                    break;
                case "<":
                    $result = ( $pChamp < $pValue );
                    break;
                case "<=":
                    $result = ( $pChamp < $pValue );
                    break;
                case "LIKE":
                case "like":
                    $atester = preg_quote($pValue);
                    $atester = str_replace ('%','.*',$atester);
                    $atester = "/^".$atester."$/";
                    $result = (preg_match ($atester,$pChamp) != 0);
                    break;
                default:
                    throw new CopixException (_i18n ('copix:copixsimplearraydao.error.unsupportcomp',array ($this->getDAOId (),$pComparateur,$this->_currentSP)));
                    break;
            }
        }
        return $result;
    }
    /**
     * Retourne les données comprises entre offSet et count si renseignés
     *
     * @param array $pData tableau de données
     * @param CopixDAOSearchParams $pSearchParams critères à appliquer
     * @return array
     */
    protected function _applyLimit ($pData,$pSearchParams) {
        if ( ($begin = $pSearchParams->getOffset ()) === null ) {
            $begin = 0;
        }
        if ( $pSearchParams->getCount () !== null ) {
            $result = array_slice ($pData,$begin,$pSearchParams->getCount ());
        }else {
            $result = array_slice ($pData,$begin);
        }
        return $result;
    }
    /**
     * Retourne les données triées selon le critères order
     *
     * @param array $pData tableau de données
     * @param CopixDAOSearchParams $pSearchParams critères à appliquer
     * @return array
     */
    protected function _applyOrder ($pData,$pSearchParams) {
        if ( count ($pSearchParams->order) == 0 ) {
            return $pData;
        }else {
            $tri = array ();
            foreach ($pSearchParams->order as $critere=>$sens) {
                $tri[] = array ('key'=>$critere,'sort'=>$sens);
            }
            return $this->_multisort ($pData,$tri);
        }
    }
    /**
     * Tri les données selon un tableau des critères
     *
     * @param array $pData tableau de données
     * @param array $pKeys tableau critères au format ('key'=>colonne,'sort'=>ASC|DESC)
     * @return array
     */
    protected function _multisort ($pData,$pKeys) {
        // List As Columns
        foreach ($pData as $key => $row) {
            foreach ($pKeys as $k) {
                $cols[$k['key']][$key] = $row[$k['key']];
            }
        }
        // List original keys
        $idkeys=array_keys ($pData);
        // Sort Expression
        $i=0;
        foreach ($pKeys as $k) {
            if($i>0) {
                $sort.=',';
            }
            $sort.='$cols['.$k['key'].']';
            if($k['sort']) {
                $sort.=',SORT_'.strtoupper ($k['sort']);
            }
            if($k['type']) {
                $sort.=',SORT_'.strtoupper ($k['type']);
            }
            $i++;
        }
        $sort.=',$idkeys';
        // Sort Funct
        $sort='array_multisort('.$sort.');';
        eval($sort);
        // Rebuild Full Array
        $result = array ();
        foreach ($idkeys as $idkey) {
            $result[$idkey]=$pData[$idkey];
        }
        return $result;
    }
    /**
     * Charge les données depuis un fichier au format
     * $data[] = array('champ1'=>valeur11,'champ2'=>valeur12...)
     * $data[] = array('champ1'=>valeur21,'champ2'=>valeur22...)
     *
     * @throws CopixException : si le fichier est introuvable, fournit la liste des fichiers trouvés
     * @throws CopixException : si le fichier ne peut être lu
     * @return array
     */
    protected function _getData () {
        $data = array ();
        $files = CopixFile::search ($this->getDAOId ().self::EXT_FILE,$this->_connectionName);
        if ( count ($files) == 0 ) {
            $files = CopixFile::search ("*".self::EXT_FILE,$this->_connectionName);
            $tables = array ();
            foreach ($files as $file) {
                preg_match ("/^[^\/]+".self::EXT_FILE."$/i",$file,$matches);
                $tables[] = $matches[0];
            }
            throw new CopixException (_i18n ('copix:copixsimplearraydao.error.founddata',array ($this->getDAOId (),$this->_connectionName,"[".implode (", ",$tables)."]")));
        }
        try {
            include($files[0]);
        }catch (Exception $e) {
            throw new CopixException (_i18n ('copix:copixsimplearraydao.error.readdata',array ($this->getDAOId (),$files[0])));
        }
        return $data;
    }
}