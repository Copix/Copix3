<?php
/**
 * @package module_credentiel
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Gestion de droits pour les modules
 * @package module_credentiel
 */
class module_groupHandler {
    
    public function isOk ($pGroupHandler, $pGroup, $pString) {
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

	    return $this->test($module,$name,$value,$pGroupHandler,$pGroup);
    }

    public function test($pModule,$pName,$pValue,$pGroupHandler,$pGroup) {
        $arValue = array();
        $query = "select * from modulecredentials, modulecredentialsgroups
				  where modulecredentials.id_mc = modulecredentials.id_mc
						and modulecredentials.name_mc = :name
						and modulecredentialsgroups.id_group = :id_group
						and modulecredentialsgroups.handler_group = :handler_group
						";
        $arValue[':name'] = $pName;
        $arValue[':id_group'] = $pGroup;
        $arValue[':handler_group'] = $pGroupHandler;
        if ($pModule !== null) {
            $query .= " and modulecredentials.module_mc = :module ";
            $arValue[':module'] = $pModule;
        } else {
            $query .= " and modulecredentials.module_mc is null ";
        }
        return (count(_doQuery($query,$arValue))>0);
        
    }
   
}
?>