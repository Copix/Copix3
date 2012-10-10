<?php
/**
 * @package standard
 * @subpackage auth
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Gestion de droits pour les modules
 * Test effectivement les droits en base
 * @package standard
 * @subpackage auth
 */
class dbModuleGroupHandler {
    
    private $_group        = null;
    
    private $_groupHandler = null;
    
    public function __construct ($pGroupHandler, $pGroup) {
        $this->_group        = $pGroup;
        $this->_groupHandler = $pGroupHandler;
    }
    
    /**
     * Décomposition de la chaine de droit
     *
     * @param string $pString La chaine de droit
     * @return boolean Le droit
     */
    public function isOk ($pString) {
        $arString = explode('@',$pString);
	    $module   = null;
	    if (isset($arString[1])) {
	        $module = $arString[1];
	    }
	    $arName = explode('|',$arString[0]);
	    $value  = null;
	    if (isset($arName[1])) {
	        $value = $arName[1];
	    }
	    $name = $arName[0];

	    return $this->test($module,$name,$value);
    }

    /**
     * Test les droits grace a une requete
     * nom|sousnom@module
     *
     * @param string $pModule Le module
     * @param string $pName Nom principal du droit
     * @param string $pValue Sous nom du droit
     * @return boolean Le droit
     */
    public function test($pModule,$pName,$pValue) {
        //echo "[".$pModule."][".$pName."][".$pValue."][".$this->_group."][".$this->_groupHandler.']<br />';
        $arValue = array();
        $query = "select * from modulecredentials mc, modulecredentialsgroups mcg";
        if ($pValue !== null) {
            $query .= ", modulecredentialsvalues mcv1, modulecredentialsvalues mcv2";
        }
		$query .= " where mc.id_mc = mcg.id_mc
						and mc.name_mc = :name
						and mcg.id_group = :id_group
						and mcg.handler_group = :handler_group
						";
        $arValue[':name'] = $pName;
        $arValue[':id_group'] = $this->_group;
        $arValue[':handler_group'] = $this->_groupHandler;
        if ($pValue !== null) {
            $query .= "
						and mcv2.id_mcv = mcg.id_mcv
  						and mcv1.id_mc = mc.id_mc
  						and mcv1.value_mcv = :value
  						and ((mcv2.level_mcv > mcv1.level_mcv and mcv1.level_mcv is not null) or mcv2.value_mcv = mcv1.value_mcv)";
            $arValue[':value'] = $pValue;
        }
        
        if ($pModule !== null) {
            $query .= " and mc.module_mc = :module ";
            $arValue[':module'] = $pModule;
        } else {
            $query .= " and mc.module_mc is null ";
        }

        return (count(_doQuery($query,$arValue))>0);
        
    }
   
}
?>