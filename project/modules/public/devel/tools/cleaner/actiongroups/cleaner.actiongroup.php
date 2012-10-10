<?php
/**
 * @package tools
 * @subpackage cleaner
 * @author		Brice Favre
 * @copyright	2001-2007 CopixTeam
 * @link		http://copix.org
 * @licence		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file 
 */

/**
 * @package tools
 * @subpackage cleaner
 */
class ActionGroupCleaner extends CopixActionGroup {
    public function beforeAction ($actionName){
        // verification si l'utilisateur est connecte
        CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
    }

    /**
     * 
     */
    function processDefault (){
        $tpl = new CopixTpl();
        $pDirectory = $this->_getDirectoryOk(CopixRequest::get('directory'));
        $pFilter = CopixRequest::get('filter');
        if ($pDirectory === false) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>_i18n ('cleaner.error.notAllowed'),
            'back'=>_url ('cleaner|cleaner|', array ('directory'=>'.'))));
        }

        $tpl->assign ('TITLE_PAGE', _i18n ('cleaner.title'));
        $tpl->assign ('MAIN', CopixZone::process('cleanerfilelist', array('directory'=> $pDirectory, 'filter' => $pFilter)));
        return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl) ;
    }

    /**
     *
     */
    function processDeleteFile() {
        $pDirectory= $this->_getDirectoryOk (_request ('directory'));
        
        $pFiles = _request ('file');
        if ($pDirectory === false) {
            // On renvoie une erreur
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>_i18n ('cleaner.error.notAllowed'),
            'back'=>_url ('cleaner|cleaner|', array ('directory'=>'.'))));
        }
        if (is_array($pFiles)) {
            foreach ($pFiles as $files) {
                @unlink ($pDirectory.$files);
            }
        } else {
            @unlink ($pDirectory.$pFiles);
        }
        return _arRedirect(_url("cleaner|cleaner|", array("directory"=>str_replace(COPIX_TEMP_PATH,"",$pDirectory))));

    }

    /**
     *
     */
    private function _getDirectoryOk($pDirectory) {
        $pDirectory = CopixFile::trailingSlash ($pDirectory);
        $arDirectory = explode (";", CopixConfig::get("cleanedDirectory"));
        if (ereg ("\.\.", $pDirectory)) {
            return false;
        }
        if (ereg ("^/", $pDirectory)) {
            if (in_array ($pDirectory, $arDirectory)) {
                return $pDirectory;
            } else {
                return false;
            }
        }
        return COPIX_TEMP_PATH.$pDirectory;
    }
}
?>