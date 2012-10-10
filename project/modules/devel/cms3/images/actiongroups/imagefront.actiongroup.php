<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Frontoffice pour les images  
 * 
 * @package cms
 * @subpackage images
 */
class ActionGroupImageFront extends CopixActionGroup {
	/**
	 * Fonction qui ne sert que dans les écrans d'administration, pour les personnes ayant les droits d'écriture sur l'image
	 * (en gros utile pour prévisualiser les images en cours de création, ou au statut brouillon) 
	 */
	public function processGetImage (){
		//on regarde si on dispose bien de notre id
		if (!CopixRequest::exists('id_image')) {
			if (CopixConfig::instance ()->getMode () == CopixConfig::PRODUCTION) {
				return new CopixActionReturn (CopixActionreturn::HTTPCODE, CopixHTTPHeader::get404 ());
			} else {
				CopixRequest::assert('id_image');
			}
		}
		
		//récupération de l'élément (pour vérifier les droits)
		$element = _class ('image|imageservices')->getById(_request ('id_image'));
		if (! HeadingElementCredentials::canWrite ($element->public_id_hei)){
			return new CopixActionReturn (CopixActionreturn::HTTPCODE, CopixHTTPHeader::get403 ()); 
		}

		return $this->_getImage ($element);
	}

	/**
	 * URL front pour la récupération des images (appelé par heading||)
	 */
    public function processDefault (){
		//HeadingFront utilise Copixregistry pour indiquer les public_id dont il a demandé l'affichage
		$front = CopixRegistry::instance ()->get ('headingfront');

		if ($front !== _request('public_id')){
			throw new CopixCredentialException ('basic:admin'); 
		}
    	
		//On défini dans la requête le id_image pour pouvoir appeler par la suite _getImage
    	CopixRequest::assert ('public_id');
		$editedElement = _ioClass('images|imageservices')->getByPublicId (_request('public_id'));

		//retour de l'image 
		return $this->_getImage ($editedElement);
	}

	/**
	 * Fonction privée qui récupère "en vrai" l'image voulue (donnée par son id_image)
	 * Les droits sont gérés ailleurs, dans les méthodes "front"
	 */
	private function _getImage ($element) {
		$content_disposition = _request ('content_disposition' , 'inline');

		//récupération et validation des paramètres
		$dimension = array ();
		$dimension['width']  = _filter ('int')->get (_request ('width', 0));
		$dimension['height'] = _filter ('int')->get (_request ('height', 0));
		$keepProportions     = _filter ('boolean')->get (_request ('keepProportions', true));
		$resizeIfNecessary	 = _filter ('boolean')->get (_request ('resizeIfNecessary', false));
		$crop = _filter ('boolean')->get (_request ('crop', false));

        //Calcul du chemin en cache
		$cachePath = COPIX_CACHE_PATH.ImageServices::IMAGE_PATH.$element->id_helt.'/';
        $imageFile = $cachePath.'_w_'.$dimension['width'].'_h_'.$dimension['height'].'k_'.$keepProportions.$element->file_image;

        //On regarde si l'image existe en cache
        if (file_exists ($imageFile)){
        	return _arFile ($imageFile, array ('content-disposition'=>$content_disposition));
        }

        //L'image n'existe pas en cache.
        $image = COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$element->file_image;
        if( file_exists( $image) ){
        	$size = GetImageSize($image);
        } else {
			return new CopixActionReturn (CopixActionReturn::HTTPCODE, CopixHTTPHeader::get404 ());
        }
        
        //Les dimensions sources & cibles
        $src_w = $size[0];
        $src_h = $size[1];
        
	    if ($resizeIfNecessary &&  $src_w<= $dimension['width'] && $src_h <= $dimension['height']){
        	$dst_w = $src_w;
        	$dst_h = $src_h;
        } else {
	        $dst_w = $dimension['width'];
	        $dst_h = $dimension['height'];
        }
        
        //On vérifie qu'au moins une des deux valeurs cible est donnée
        if ($dst_w <=0 && $dst_h <=0){
        	$dst_w = $size[0];
        	$dst_h = $size[1];
        }
        
        //On modifie les dimensions cibles s'il est question de garder les proportions
        if ($keepProportions){
	        if ($dst_w > 0 && $dst_h > 0){
	            if (($dst_w/$src_w) > ($dst_h/$src_h)) {
	                $dst_w = ($dst_h/$src_h) * $src_w;
	            }else{
	                $dst_h = ($dst_w/$src_w) * $src_h;                    
	            }
	        }elseif ($dst_h > 0){
	            $dst_w = ($dst_h/$src_h) * $src_w;
	        }elseif ($dst_w > 0){
	            $dst_h = ($dst_w/$src_w) * $src_h;
	        }	            
        }else{
        	//On ne souhaite pas conserver les proportions, on met toutefois un pixel minimum
        	if ($dst_h <= 0){
        		$dst_h = 1;
        	}
            if ($dst_w <= 0){
        		$dst_w = 1;
        	}
        }

        //Filtrage final pour arrondir le tout
        $dst_h = _filter ('int')->get ($dst_h);
        $dst_w = _filter ('int')->get ($dst_w);

        //si l'image a sa taille d'origine, on ne redimensionne pas
        if ($src_w == $dst_w && $src_h == $dst_h){
        	//On copie tout de même l'image en cache pour éviter de procéder à un nouveau getSize au prochain appel
        	CopixFile::write($imageFile, CopixFile::read (COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$element->file_image));
            return _arFile (COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$element->file_image, array ('content-disposition'=>$content_disposition));
        }

        //Si nous ne sommes pas capable de modifier l'image, on renvoi telle qu'elle.
        if (($src_im = $this->_imageCreateFromFile ($image, $size[2])) === null){
        	//On copie tout de même l'image en cache, si on installe les paquets nécessaires, il faudra vider le cache.
			CopixFile::write($imageFile, CopixFile::read (COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$element->file_image));        	
            return _arFile (COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$element->file_image, array ('content-disposition'=>$content_disposition));
        }

        $dst_im = $this->_createResource($dst_w,$dst_h, $size[2]);
        
        if ($crop){
	        $crop_w = 0;
	        $crop_h = 0;
	        $crop_im = $this->_cropImage($crop_h, $crop_w, $image);	        
	        ImageCopyResampled ($dst_im,$crop_im,0,0,0,0,$dst_w,$dst_h,$crop_w,$crop_h);
        } else {
        	ImageCopyResampled ($dst_im,$src_im,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);
        }

        if (!is_dir($cachePath)) {
        	CopixFile::createDir ($cachePath);
        }
        $this->_imageCacheAndOutput ($dst_im, $imageFile, $size[2]);
        imagedestroy ($src_im);
        imagedestroy ($dst_im);

        return _arFile($imageFile, array ('content-disposition'=>$content_disposition));
    }
    
   /**
    * Creates the buffer from an imageFile.
    * @param $imageFile the filepath
    * @param $type the type of the image (1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF (Ordre des octets Intel), 8 = TIFF (Ordre des octets Motorola), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF)
    * @private
    */
    private function _imageCreateFromFile ($imageFile, $type){
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
    private function _imageCacheAndOutput ($dst_im, $pictureName, $type){
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
    
    /**
     * 
     * Créé l'image, applique les fonctions de transparence et de couleur en fonction des types
     * @param $pWidth
     * @param $pHeight
     * @param $pType le type de l'image (1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF (Ordre des octets Intel), 8 = TIFF (Ordre des octets Motorola), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF)    
     */
    private function _createResource ($pWidth, $pHeight, $pType){  
    	$ressToReturn = imagecreatetruecolor($pWidth, $pHeight); 
    	switch($pType) {
			case 2:
		         // fond blanc
		         $blanc = imagecolorallocate ($ressToReturn, 255, 255, 255);
		         imagefill ($ressToReturn, 0, 0, $blanc);
		         break;
		    case 3:
		         // fond transparent (pour les png avec transparence)
		         imagesavealpha($ressToReturn, true);
		         $trans_color = imagecolorallocatealpha($ressToReturn, 0, 0, 0, 127);
		         imagefill($ressToReturn, 0, 0, $trans_color);
		         break;	
    	}			
		return $ressToReturn;
    }
    
    /**
     * 
     * Fonction de redecouppage d'image (crop)
     * @param int $pDestW
     * @param int $pDestH
     * @param String $pImage adresse de l'image
     */
	private function _cropImage(&$pDestW, &$pDestH, $pImage) {
	      // recuperation des dimensions de l'image Source
	      $img_size = getimagesize($pImage);
	      $src_w = $img_size[0];
	      $src_h = $img_size[1];
	      
	      // Crop aux dimensions indiquees
	      if ($pDestW != 0 && $pDestH != 0) {
	         $W = $pDestW;
	         $H = $pDestH;
	      }      // -----------------------------------------------
	      // Crop en HAUTEUR (meme largeur que la source)
	      if ($pDestW == 0 && $pDestH != 0) {
	         $H = $pDestH;
	         $W = $src_w;
	      }
	      // -----------------------------------------------
	      // Crop en LARGEUR (meme hauteur que la source)
	      if ($pDestW != 0 && $pDestH == 0) {
	         $W = $pDestW;
	         $H = $src_h;         
	      }
	      // Crop "carre" a la plus petite dimension de l'image source
	      if ($pDestW == 0 && $pDestH == 0) {
	        if ($src_w >= $src_h) {
	         $W = $src_h;
	         $H = $src_h;
	        } else {
	         $W = $src_w;
	         $H = $src_w;
	        }   
	      }
	      
	      $pDestW = $W;
	      $pDestH = $H;

	      // creation de la ressource-image "Src" en fonction de l extension
	      $Ress_Src = $this->_imageCreateFromFile ($pImage, $img_size[2]);
	
	      // creation d une ressource-image "Dst" aux dimensions finales
	      $Ress_Dst = $this->_createResource ($W, $H, $img_size[2]);
	
	      // -----------------------------------------------
	      // CENTRAGE du crop
	      // coordonnees du point d origine Scr : $X_Src, $Y_Src
	      // coordonnees du point d origine Dst : $X_Dst, $Y_Dst
	      // dimensions de la portion copiee : $W_copy, $H_copy
	      // -----------------------------------------------
	      // CENTRAGE en largeur
	      if ($pDestW == 0) {
	         if ($pDestH == 0 && $src_w < $src_h) {
	            $X_Src = 0;
	            $X_Dst = 0;
	            $W_copy = $src_w;
	         } else {
	            $X_Src = 0;
	            $X_Dst = ($W - $src_w) /2;
	            $W_copy = $src_w;
	         }
	      } else {
	         if ($src_w > $W) {
	            $X_Src = ($src_w - $W) /2;
	            $X_Dst = 0;
	            $W_copy = $W;
	         } else {
	            $X_Src = 0;
	            $X_Dst = ($W - $src_w) /2;
	            $W_copy = $src_w;
	         }
	      }
	      // -----------------------------------------------
	      // CENTRAGE en hauteur
	      if ($pDestH == 0) {
	         if ($pDestW == 0 && $src_h < $src_w) {
	            $Y_Src = 0;
	            $Y_Dst = 0;
	            $H_copy = $src_h;
	         } else {
	            $Y_Src = 0;
	            $Y_Dst = ($H - $src_h) /2;
	            $H_copy = $src_h;
	         }
	      } else {
	         if ($src_h > $H) {
	            $Y_Src = ($src_h - $H) /2;
	            $Y_Dst = 0;
	            $H_copy = $H;
	         } else {
	            $Y_Src = 0;
	            $Y_Dst = ($H - $src_h) /2;
	            $H_copy = $src_h;
	         }
	      }

	    // CROP par copie de la portion d image selectionnee
		imagecopyresampled($Ress_Dst,$Ress_Src,$X_Dst,$Y_Dst,$X_Src,$Y_Src,$W_copy,$H_copy,$W_copy,$H_copy);
		return $Ress_Dst; 
	}
}