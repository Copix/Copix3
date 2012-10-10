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
class dbDynamicGroupHandler {
    
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
	    $arName = explode('|',$pString);
	    $value  = null;
	    if (isset($arName[1])) {
	        $value = $arName[1];
	    }
	    $name = $arName[0];

	    return $this->test($name,$value);
    }

    /**
     * Test les droits grace a une requete
     * nom|sousnom
     *
     * @param string $pName Nom principal du droit
     * @param string $pValue Sous nom du droit
     * @return boolean Le droit
     */
    public function test($pName,$pValue) {
        //echo "[".$pName."][".$pValue."][".$this->_group."][".$this->_groupHandler.']<br />';
        $arValue = array();
        $query = "select * from dynamiccredentials dc, dynamiccredentialsgroups dcg";
        if ($pValue !== null) {
            $query .= ", dynamiccredentialsvalues dcv1, dynamiccredentialsvalues dcv2";
        }
		$query .= " where dc.id_dc = dcg.id_dc
						and dc.name_dc = :name
						and dcg.id_group = :id_group
						and dcg.handler_group = :handler_group
						";
        $arValue[':name'] = $pName;
        $arValue[':id_group'] = $this->_group;
        $arValue[':handler_group'] = $this->_groupHandler;
        if ($pValue !== null) {
            $query .= "
						and dcv2.id_dcv = dcg.id_dcv
  						and dcv1.id_dc = dc.id_dc
  						and dcv1.value_dcv = :value
  						and ((dcv2.level_dcv > dcv1.level_dcv and dcv1.level_dcv is not null) or dcv2.value_dcv = dcv1.value_dcv)";
            $arValue[':value'] = $pValue;
        }
        
        return (count(_doQuery($query,$arValue))>0);
        
    }
}
?>