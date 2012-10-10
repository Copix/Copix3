<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Gérald Croës
 * @copyright	2000-2006 CopixTeam
 * @link			http://www.copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagHtmlEditor extends CopixTemplateTag {
    public function process($pParams) {
        static $_init = false;
        static $htmlPath = '';

        extract($pParams);

        //check the initialisation
        if (! $_init){
            $path = CopixModule::getPath ('htmleditor').COPIX_CLASSES_DIR;
            $htmlPath = CopixUrl::get ().'js/FCKeditor/';
            require_once( $path.'fckeditor.php' );
            $_init = true;
        }

        if (empty ($content)){
            $content = '&nbsp;';
        }
        //name of the textarea.
        if (empty ($name)){
            throw new CopixTemplateTagException ('htmleditor: missing name parameter');
        }else{
            if (!isset ($width)) {
                $width = CopixConfig::get ('htmleditor|width');
                //$width = '100%';
            }
            if (!isset ($height)) {
                $height = CopixConfig::get('htmleditor|height');
                //$height = '450px';
            }
            /*
             * ATTENTION les éléments de config viewPhototèque etc font doublon avec la sélection de la toolbarset, mais sont nécessaire à Copix
             * Par contre si on ne les load pas, on a une erreur de FCKeditor, il faut donc supprimer ce gestionnaire d'erreur sinon on se prend un alert javascript
             * le gestionnaire en question se trouve dans "FCKToolbarItems.GetItem" (chercher cette chaîne pour le trouver) et désactiver "alert( FCKLang.UnknownToolbarItem.replace( /%1/g, itemName ) ) ;
             */
            $oFCKeditor = new FCKeditor( $name ) ;
            $oFCKeditor->BasePath  = $htmlPath ;
            $oFCKeditor->Value     = $content ;
            $oFCKeditor->ToolbarSet = 'Copix' ;
            $oFCKeditor->Width         = $width ;
            $oFCKeditor->Height        = $height ;
            $oFCKeditor->Config['viewPhototheque']=   (CopixModule::isEnabled('pictures')) ? 'true' : 'false';
            $oFCKeditor->Config['viewCmsLink']    =   (CopixModule::isEnabled('cms'))      ? 'true' : 'false';
            $oFCKeditor->Config['viewLinkPopup']  =   (CopixModule::isEnabled('cms'))      ? 'true' : 'false';
            $oFCKeditor->Config['viewDocument']   =   (CopixModule::isEnabled('document')) ? 'true' : 'false';
            $oFCKeditor->Config['viewMailto']     =   'true';
            // Configuration de la feuille de style à utiliser.
            //$oFCKeditor->Config['EditorAreaCSS'] = CopixUrl::get ().'styles/themes/hivers/hivers.css';
            $out = $oFCKeditor->CreateHtml() ;
        }
        return $out;
    }
}

?>