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
 * @package standard
 * @subpackage auth
 */
class dbDynamicCredentialHandler implements ICopixCredentialHandler {
	
	/**
	 * S'assure que l'utilisateur peut réaliser la chaine de droit donnée
	 *
	 * @param	string		$pString	La chaine de droit à tester
	 * @param 	CopixUser	$pUser		L'utilisateur dont on teste les droits
	 * @return	boolean
	 */
	public function assert ($pStringType, $pString, $pUser){
		switch ($pStringType){
			case 'dynamic':
				return $this->_module ($pString, $pUser);
			default: 
				return null;
		}
	}
	
	/**
	 * Gestion du type module
	 *
	 * @param string $pString	la chaine à tester
	 * @param CopixUser $pUser	l'utilisateur dont on teste les droits
	 */
	private function _module ($pString, $pUser){
	    foreach ($pUser->getGroups () as $handler=>$arGroupForHandler) {
	        foreach ($arGroupForHandler as $id=>$groupCaption){
	            _classInclude('auth|dbdynamicgrouphandler');
	            $handlerCredential = new dbDynamicGroupHandler ($handler,$id);
	            if ($handlerCredential->isOk ($pString)) {
	                return true;
	            }
	        }
	    }
	    return false;
	}
	
    public function registerCredential ($pName, $pValues = array()) {
        $result = _dao('dynamiccredentials')->findBy(_daoSP()->addCondition('name_dc','=',$pName));
        if (!isset($result[0])) {
            $record = _record ('dynamiccredentials');
            $record->name_dc = $pName;
            _dao('dynamiccredentials')->insert($record);
        } else {
            $record = $result[0];
        }
        
        foreach ($pValues as $key=>$value) {
            $resultValue = _dao('dynamiccredentialsvalues')->findBy(_daoSP()->addCondition('value_dcv','=',$key)->addCondition('id_dc','=',$record->id_dc));
            if (!isset($resultValue[0])) {
                $recordValue = _record ('dynamiccredentialsvalues');
                $recordValue->id_dc = $record->id_dc;
                $recordValue->value_dcv = $key;
                $recordValue->level_dcv = $value;
                _dao('dynamiccredentialsvalues')->insert($recordValue);                
            } else {

            }
        }
    }
	
}
?>