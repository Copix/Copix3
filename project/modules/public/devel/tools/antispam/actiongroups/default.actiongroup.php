<?php
/**
 * @package standard
 * @subpackage default
 * @author       Duboeuf Damien
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions par défaut réalisées par le framework
 * @package standard
 * @subpackage default
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
    * Par défaut, on redirige vers l'url de renvoie d'image
    */
	public function processDefault (){
		return _arRedirect (_url('antispam|default|getImage', array ('id'=>uniqid())));
	}

	/**
	 * Génèré et renvoie une image de sécurité
	 */
	public function processGetImage (){
	    
        CopixRequest::assert ('id');
	    $id             = _request ('id');
	    $path = COPIX_TEMP_PATH.'imageprotect/';
        $pathFile       = $path . $id;
        
	    CopixFile::createDir($path);
	    
	    $oldimages = glob($path . '*');
	    foreach ($oldimages as $oldimage) {
	        $datefile = @filemtime($oldimage);
	        $date = date('U');
	        if (($date - (($datefile !== false)?$datefile:$date)) > 60) {
	            CopixFile::removeFileFromPath($oldimage);
	        }
	    }
	    
        _classInclude('antispam|imageprotect');
        ImageProtect::createImage ($id, $pathFile);
        
        return _arFile($pathFile);
	}
}
?>