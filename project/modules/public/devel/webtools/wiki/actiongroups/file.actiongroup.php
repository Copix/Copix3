<?php
/**
 * @package	webtools
 * @subpackage	wiki
 * @author	Patrice Ferlet
 * @copyright 2001-2006 CopixTeam
 * @link      http://copix.org
 * @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions d'affichage sur les fichiers et les images
 * @package	webtools
 * @subpackage	wiki
 */
class ActionGroupFile extends CopixActionGroup {
    /**
     * Affichage d'une image
     */
    public function processShow () {
        CopixRequest::assert ('page');
        if (!_ioClass ('wiki|wikiauth')->canRead ()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>_i18n ('wiki.error.cannot.show'),
            'back'=>_url ()));
        }

        $tpl = new CopixTpl();
        $tpl->assign ('TITLE_PAGE', _i18n('wiki.image.page.edit'));
        $tpl->assign ('MAIN', CopixZone::process('Image', array (
        'page' => _request ('page',null),
        'heading' => _request('heading',"")
        )));
        return new CopixActionReturn (CopixActionReturn::DISPLAY_IN, $tpl, "wiki|blank.tpl");
    }

    /**
     * Sauvegarde d'une image
     */
    public function processSaveImage(){
        return $this->processSaveFile(); //compatibilité
    }

    /**
     * Sauvegarde d'un fichier
     */
    public function processSaveFile () {
    	CopixRequest::assert('title_wikiimage');
    	
        // Verification des droits de l'utilisateurs
        if(!_ioClass ('wiki|wikiauth')->canWrite ()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>_i18n ('wiki.error.cannot.edit'),
            'back'=>_url ()));
        }
        
        if (! $this->_verifFormFile(_request ('title_wikiimage'))) {
            return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>_i18n ('wiki.error.fileexists'),
                    'back'=>_url ('wiki|file|show', array (
                    'page' => _request ('page'),
                'heading' => _request('heading',"")))));
        }
      
        CopixLog::log('Ajoute '.$_FILES['image']['tmp_name']);

        $imgname = uniqid('wiki_image.') . str_replace(" ","_",$_FILES['image']['name']);
        $path = COPIX_VAR_PATH . '/' . CopixConfig::get('wiki|imagepath') . '/';

        if (CopixRequest::getFile('image', $path, $imgname)) {
            // Then we record file in db
            $image = _record ('wikiimages');

            $image->title_wikiimage = _request ('title_wikiimage');
            $image->file_wikiimage = $imgname;
            $image->page_wikiimage = _request('heading',"")."/"._request ('page');

            _dao ('wikiimages')->insert($image);
        } else {
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>_i18n ('wiki.error.cannot.add.file',array('path'=>$path)),
            'back'=>"javascript:window.close()"));
        }
         
        return _arRedirect (_url ('wiki|file|Show', array (
        'page' => _request ('page'),
        'heading' => _request('heading',"")
        )));
    }

    /**
     * Récupération d'une image
     */
    public function processGetFile () {
        if (!_ioClass ('wiki|wikiauth')->canRead ()){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>_i18n ('wiki.error.cannot.show'),
            'back'=>_url ()));
        }
        $path = COPIX_VAR_PATH . '/' . CopixConfig::get('wiki|imagepath') . '/';
        $dao = _dao ('wikiimages');
        $image = $dao->get(_request ('title'));
        $ext="";
        if($image){
            $path .= $image->file_wikiimage;
            $tmp = explode(".", $path);
            $ext = $tmp[count($tmp) - 1];

            if ($size = _request ("size",false,false)){
                if ($this->resizeImage ($path, $size, $ext)) {
                    //@TODO ??? BUG
                    exit;
                }
            }
        }
        return _arFile ($path, _request ('title').".", array ('filename'=>_request ('title').".".$ext));
    }

    /**
     * Affichage d'une image redimensionnée
     */
    function ResizeImage ($filename,$size,$ext){
        if ($ext=="jpg"){
            $ext="jpeg";
        }
        list ($width, $height) = getimagesize ($filename);

        if (preg_match("/%+/","$size")){
            $size = str_replace("%","",trim($size));
            $percent = $size/100;
            $percent = round($percent,1);
        }else{
            $size = preg_replace("/[a-zA-Z]*/","",trim($size));
            $percent=$size*100/$width;
            $percent = $size/100;
            $percent = round($percent,1);
        }
        $newwidth = $width * $percent;
        $newheight = $height * $percent;
        // Content type
        $func="imagecreatefrom".$ext;
        if(!function_exists($func)){
            //echo "Function $func not defined";
            throw new CopixException("Function $func not defined");
            return false;
            //exit;
        }
        // Chargement
        $thumb = imagecreatetruecolor($newwidth, $newheight);
        $source = $func($filename);

        // Redimensionnement
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        // Affichage
        header('Content-type: image/png');
        imagepng($thumb);
        exit;
    }

    public function processgetMathImage (){
        $img = _request ('math');
        $path = COPIX_CACHE_PATH."/math/";
        return _arFile ($path.$img);
    }

    public function processgetGraphViz (){
        $img = _request ('graph');
        $path = COPIX_CACHE_PATH."/graphviz/";
        return _arFile ($path.$img);
    }

    /**
     * Fonction de verification du formulaire d'envoie d'images
     * @param $pFileName Titre de l'image
     * @return boolean
     */
    private function _verifFormFile ($pTitleFile){
        if ($pTitlefile == '' ) return false; 
        $nb = _dao ('wikiimages')->countBy( _daoSp ()->addCondition ('title_wikiimage', '=', $pTitleFile));
                
        if ($nb != 0) {
            return false;
        } else {
            return true;
        }
    }
}
?>