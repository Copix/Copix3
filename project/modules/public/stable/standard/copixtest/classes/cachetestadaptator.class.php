<?php
/**
 * @package standard
 * @subpackage copixtest
* @author		Salleyron julien
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.htmlGNU Leser General Public Licence, see LICENCE file
*/

/**
 * Class adaptateur permettant de tester les cache
 * @package standard
 * @subpackage copixtest
 */
class CacheTestAdaptator implements ICopixCacheStrategy  {
    
    private $_test=null;

    public function isEnabled($pExtra) {
        return true;
    }

    public function write ($pId, $pContent, $pType, $pExtra){
        $this->_test=$pContent;
    }

    public function read ($pId, $pType, $pExtra){
        return $this->_test;
    }

    public function clear ($pId, $pType, $pExtra){
        $this->_test=null;
    }

    public function exists($pId, $pType, $pExtra) {
        if ($this->_test==null) {
            return false;
        }
        return true;
    }
}
?>