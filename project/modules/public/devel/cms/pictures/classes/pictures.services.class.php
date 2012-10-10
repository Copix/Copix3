<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage pictures
* PicturesServices
*/

class PicturesServices {
   /**
   * Deletes the cache for the given picture
   * @param string $idPict the picture id
   */
    function clearCacheFor ($idPict){
        $dir = CopixConfig::get ('pictures|path').$idPict;
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..'){
                   unlink ($dir.'/'.$file);
                }
            }
            closedir($handle);
        }
        @rmdir($dir);
    }
}
?>