<?php
/**
 * @package standard
 * @subpackage admin 
 * 
 * @copyright CopixTeam
 * @license lgpl
 * @author Salleyron Julien 
 */

/**
 * Permet la gestion du répertoire TEMP
 * @package standard
 * @subpackage admin 
 */
class AdminTemp {
    
    /**
     * Pour vider le répertoire temp
     *
     */
    public function clearTemp() {
        CopixFile::removeFileFromPath(COPIX_TEMP_PATH);
    }
    
    /**
     * TODO testTempTree
     *
     */
    public function testTempTree() {
        
    }
    
    /**
     * TODO makeTempTree
     *
     */
    public function makeTempFree() {
       
    }
}

?>