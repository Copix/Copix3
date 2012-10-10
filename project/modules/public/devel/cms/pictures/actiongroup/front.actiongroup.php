<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan, Croës Gérald see copix.org for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage pictures
* Simplement la fonction d'affichage.
*/
class ActionGroupFront extends CopixActionGroup {
    /**
    * Affichage d'une image.
    */
    function get () {
        //do we ask for a picture ?
        if (CopixRequest::get ('id_pict', null, true) === null){
            return new CopixActionReturn (CopixactionReturn::HTTPCODE, array ("HTTP/1.0 404 Not Found"));
        }

        //essaye de récupérer l'image
        $daoPicture = & CopixDAOFactory::getInstanceOf ('pictures');
        if (($picture = $daoPicture->get (CopixRequest::get ('id_pict'))) === false){
            return new CopixActionReturn (CopixactionReturn::HTTPCODE, array ("HTTP/1.0 404 Not Found"));
        }

        //Maj de la date de consultation
        $picture->last_consultation_pict = date('Ymd');
        $daoPicture->update($picture);

        //pas de paramètre hauteur ou largeur > on renvoie l'image
        if ((!isset ($this->vars['width'])) && (!isset ($this->vars['height']))) {
        	return new CopixActionReturn (CopixactionReturn::BINARY, CopixConfig::get ('pictures|path').$picture->id_pict.'.'.$picture->format_pict, "image/".$picture->format_pict);
        }

        //Il existe des paramètres de hauteur ou de largeur, il nous faut travailler.
        $cachePath = CopixConfig::get ('pictures|path').$picture->id_pict.'/';
        $imageFile = $cachePath;
        if (isset ($this->vars['width'])){
            $this->vars['width'] = intval ($this->vars['width']);
            $imageFile .= '_w_'.$this->vars['width'];
        }
        if (isset ($this->vars['height'])){
            $this->vars['height'] = intval ($this->vars['height']);
            $imageFile .= '_h_'.$this->vars['height'];
        }
        if (isset ($this->vars['force'])){
            $this->vars['force'] = intval ($this->vars['force']);
            $imageFile .= '_f_'.$this->vars['force'];
        }
        $imageFile .= '.'.$picture->format_pict;
        
        if (file_exists ($imageFile)){
           return new CopixActionReturn (CopixactionReturn::BINARY, $imageFile, "image/".$picture->format_pict);
        }

        //L'image n'existe pas.
        $image = CopixConfig::get ('pictures|path').$picture->id_pict.'.'.$picture->format_pict;
        $size = GetImageSize($image);
        
        //Si nous ne sommes pas capable de modifier l'image, on renvois telle qu'elle.
        if (($src_im = $this->_imageCreateFromFile ($image, $size[2])) === null){
            return new CopixActionReturn (CopixactionReturn::BINARY, CopixConfig::get ('pictures|path').$picture->id_pict.'.'.$picture->format_pict, "image/".$picture->format_pict);
        }

//        Header("Content-type: image/".$picture->format_pict);
        $src_w = $size[0];
        $src_h = $size[1];
        
        //par défaut les tailles originales
        $dst_w = $src_w;
        $dst_h = $src_h;

        if (isset ($this->vars['force']) && isset ($this->vars['height']) && isset ($this->vars['width'])){
            $dst_h = $this->vars['height'];
            $dst_w = $this->vars['width'];
        }else if (isset ($this->vars['width']) && isset ($this->vars['height'])){
            if (($this->vars['width'] < $src_w) || ($this->vars['height'] < $src_h)){
                if (($this->vars['width']/$src_w) > ($this->vars['height']/$src_h)) {
                    $dst_h = $this->vars['height'];
                    $dst_w = ($this->vars['height']/$src_h) * $src_w;
                }else{
                    $dst_w = $this->vars['width'];
                    $dst_h = ($this->vars['width']/$src_w) * $src_h;                    
                }
            }
        }else if (isset ($this->vars['height'])){
            $dst_h = $this->vars['height'];
            if (!isset ($this->vars['force'])){
                $dst_w = ($this->vars['height']/$src_h) * $src_w;
            }
        }else if (isset ($this->vars['width'])){
            $dst_w = $this->vars['width'];
            if (!isset ($this->vars['force'])){
               $dst_h = ($this->vars['width']/$src_w) * $src_h;
            }
        }

        $dst_im = ImageCreateTrueColor($dst_w,$dst_h);
        /* ImageCopyResampled copie et rééchantillonne l'image originale*/
        ImageCopyResampled($dst_im,$src_im,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);
        if (!is_dir($cachePath)) {
            mkdir($cachePath);
        }
        $this->_imageCacheAndOutput ($dst_im, $src_im, $imageFile, $size[2]);
        imagedestroy($src_im);
        imagedestroy($dst_im);

        return new CopixActionReturn (CopixactionReturn::BINARY, $imageFile, "image/".$picture->format_pict);
    }

    /**
    * affichage de l'image en pleine page.
    */
    function showFullScreen (){
        if (!isset ($this->vars['id_pict'])){
            return CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('pictures.error.missingParameters'),
            'back'=>'index.php?module=pictures&desc=browser&id_head='.$this->vars['id_head']));
        }

        $tpl = & new CopixTpl ();
        $tpl->assign ('TITLE_BAR', CopixI18N::get ('pictures.titlePage.showPicture'));
        $tpl->assign ('MAIN',CopixZone::process ('FullScreen', array('id_pict'=>$this->vars['id_pict'],'id_head'=>$this->vars['id_head'])));
        return new CopixActionReturn (CopixactionReturn::DISPLAY, $tpl);
    }

    /**
    * Ouputs the given picture
    * @param $this->vars['id_pict'] the picture id
    */
    function download () {
        //do we ask for a picture ?
        if (!isset ($this->vars['id_pict'])){
            header("HTTP/1.0 404 Not Found");
            return new CopixActionReturn (CopixactionReturn::NONE);
        }

        //essaye de récupérer l'image
        $daoPicture = & CopixDAOFactory::getInstanceOf ('Pictures');
        $picture    = $daoPicture->get ($this->vars['id_pict']);

        if ($picture !== null) {
            $image = CopixConfig::get ('pictures|path').$picture->id_pict.'.'.$picture->format_pict;
            return new CopixActionReturn (CopixactionReturn::DOWNLOAD, $image);
        }
    }

    /**
    * Creates the buffer from an imageFile.
    * @param $imageFile the filepath
    * @param $type the type of the image (1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF (Ordre des octets Intel), 8 = TIFF (Ordre des octets Motorola), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF)

    */
    function _imageCreateFromFile ($imageFile, $type){
        switch ($type){
            case 1: $funcName = 'imagecreatefromgif';
            break;
            case 2: $funcName = 'imagecreatefromjpeg';
            break;
            case 3: $funcName = 'imagecreatefrompng';
            break;
            case 6: $funcName = 'imagecreatefrombmp';
            break;
            default :
            return null;
        }
        if (function_exists($funcName)){
            return $funcName($imageFile);
        }
        return null;
    }

    /**
    * Creates the cache for the given picture
    */
    function _imageCacheAndOutput ($dst_im, $src_im, $pictureName, $type){
    	switch ($type){
            case 1:
            imagegif  ($dst_im, $pictureName);
            break;
            case 2:
            imagejpeg ($dst_im, $pictureName);
            break;
            case 3:
            imagepng  ($dst_im, $pictureName);
            break;
            case 6:
            imagebmp ($dst_im, $pictureName);
            break;
            default:
        }
    }
}
?>