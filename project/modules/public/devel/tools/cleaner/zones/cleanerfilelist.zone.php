<?php
/**
 * @package		tools
 * @subpackage	cleaner
 * @author		Brice Favre
 * @copyright	2001-2007 CopixTeam
 * @link		http://copix.org
 * @licence		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package tools
 * @subpackage cleaner
 */
class ZoneCleanerFileList extends CopixZone {

    function _createContent (& $toReturn){
        $tpl  = new CopixTpl ();
        $forbiddenExt = array ("htaccess", "htpasswd");

        $arDirectory = explode (";",CopixConfig::get("cleanedDirectory"));
        $pFilter = $this->getParam('filter');
        if ($pFilter == "") {
            $pFilter = ".";
        }

        $pDirtoList = $this->getParam('directory');

        $dh = opendir($pDirtoList);
        while (($file = readdir($dh)) !== false) {
            if ($file != "." && $file != '..' && ! is_dir($pDirtoList.$file) && ereg($pFilter, $file)) {
                $fileInfo = explode (".", $file);
                $extension = $fileInfo[count($fileInfo)-1];
                if (! in_array($extension, $forbiddenExt)) {
                    $arFiles[] = $file;
                }
            }
        }

        $tpl->assign ('arFiles', $arFiles);
        $tpl->assign ('arDirectory', $arDirectory);
        $tpl->assign ('directory', str_replace (COPIX_TEMP_PATH, "", $pDirtoList));

        $toReturn = $tpl->fetch ('cleanerfile.list.tpl');
        return true;
    }
}
?>
