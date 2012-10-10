<?php

class pictures {
    public function addImage ($pImage) {
        $path = COPIX_VAR_PATH.CopixConfig::get ('pictures|path').'/';
        CopixFile::createDir ($path);
        $id = uniqid ('pictures_');
        CopixFile::write ($path.$id, CopixFile::read($pImage));
        return $id;
    }
    
    public function addUploadedImage ($pImage) {
        $path = COPIX_VAR_PATH.CopixConfig::get ('pictures|path').'/';
        $id = uniqid ().$pImage->getName ();
        $pImage->move ($path, $id);
        return $id;
    }
    
    public function getImage ($pId,$pWidth=null,$pHeight=null) {
        $path = COPIX_VAR_PATH.CopixConfig::get ('pictures|path').'/';
        CopixFile::createDir ($path);
        $image = $path .$pId;
        
        if (!file_exists($image)) {
            throw new CopixException ('');
        }
        
        $imageFile = $path;
        if ($pWidth!==null){
            $pWidth = intval ($pWidth);
            $imageFile .= '_w_'.$pWidth;
        }
        if ($pHeight!==null){
            $pHeight = intval ($pHeight);
            $imageFile .= '_h_'.$pHeight;
        }
        $imageFile .= $pId;
        if (file_exists ($imageFile)){
           return $imageFile;
        }
        
        //L'image n'existe pas.
        $size = GetImageSize($image);
        
        //Si nous ne sommes pas capable de modifier l'image, on renvois telle qu'elle.
        if (($src_im = $this->_imageCreateFromFile ($image, $size[2])) === null){
            return $image;
        }
        
//        Header("Content-type: image/".$picture->format_pict);
        $src_w = $size[0];
        $src_h = $size[1];
        
        //par défaut les tailles originales
        $dst_w = $src_w;
        $dst_h = $src_h;

        if (isset ($pWidth) && isset ($pHeight)){
            if (($pWidth < $src_w) || ($pHeight < $src_h)){
                if (($pWidth/$src_w) > ($pHeight/$src_h)) {
                    $dst_h = $pHeight;
                    $dst_w = ($pHeight/$src_h) * $src_w;
                }else{
                    $dst_w = $pWidth;
                    $dst_h = ($pWidth/$src_w) * $src_h;                    
                }
            }
        }else if (isset ($pHeight)){
            $dst_h = $pHeight;
            $dst_w = ($pHeight/$src_h) * $src_w;
        }else if (isset ($pWidth)){
            $dst_w = $pWidth;
            $dst_h = ($pWidth/$src_w) * $src_h;
        }

        $dst_im = ImageCreateTrueColor($dst_w,$dst_h);
        /* ImageCopyResampled copie et rééchantillonne l'image originale*/
        ImageCopyResampled($dst_im,$src_im,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);
        CopixFile::createDir ($path);
        $this->_imageCacheAndOutput ($dst_im, $src_im, $imageFile, $size[2]);
        imagedestroy($src_im);
        imagedestroy($dst_im);

        return $imageFile;
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