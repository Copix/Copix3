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
	 * Détermine si un fichier doit être enlevé du répertorie temp.
	 *
	 * @param string $path
	 * @return boolean true : le fichier doit être enlevé
	 */
	public function _tempFileFilter($path) {
		$basename = basename($path);
		if(is_dir($path) && ($basename == '.svn' || $basename == 'CVS')) {
			return false;
		}
		return true;		
	}
	

    /**
     * Pour vider le répertoire temp
     *
     */
    public function clearTemp() {
        CopixFile::removeFileFromPath(COPIX_TEMP_PATH, false, array($this, '_tempFileFilter'));
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