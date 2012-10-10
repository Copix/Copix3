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
interface ICopixDataSource {

    public function __construct ($pParams) ;

    public function addCondition ($pField, $pCond, $pValue);
    
    public function find ();
    
    public function count ();
    
    public function save ($pRecord);
    
    public function get ($Id);
    
}

class CopixDatasourceException extends Exception {
	
}

/**
 * 
 */
class CopixDatasourceFactory {
    
    /**
     * 
     */
    public static function get ($pType, $pParams) {
        switch ($pType) {
            case 'dao':
                return new CopixDaoDatasource ($pParams);
            default:
                CopixClassesFactory::fileInclude ($pType);
                $arDatasource = explode('|',$pType);
                return new $arDatasource[1] ($pParams);
                break;
        }
    }
    
}

?>