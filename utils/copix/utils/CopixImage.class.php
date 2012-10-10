<?php
/**
* @package  	copix
* @subpackage	core
* @author		Gérald CROËS, Alexandre JULIEN
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* permet d'instancier des classes via les identifiant Copix
* @package copix
* @subpackage	core
*/

class CopixImage {
	
	/**
	 * Image GD
	 *
	 * @var imagegd
	 */
	private $_img = null;
	
	/**
	 * Chemin de l'image
	 *
	 * @var String : chemin de l'image
	 */
	private $_imgFileName = null;
	
	/**
	 * Informations consernant une image
	 *
	 * @var unknow
	 */
	private $_imgInfo = null;

	/**
	 * Largeur de l'image
	 *
	 * @var int : largeur en pixels
	 */
	private $_width = 0;
	
	/**
	 * Hauteur de l'image
	 *
	 * @var int : hauteur en pixels
	 */
	private $_height = 0;
	
	/**
	 * Format de l'image
	 *
	 * @var String : format de l'image (bmp, jpg, gif, png, ...)
	 */
	private $_format = null;
	
	/**
	 * Chargement d'une image dans l'attribut $_img
	 *
	 * @param String $pFileName
	 * @return CopixImage or bool : Objet CopixImage si le chargement � r�ussi, faux sinon
	 */
	public static function load ($pFileName) {
		if (isset($pFileName) && CopixFile::read($pFileName) == false) {
			return false;
		} else {
			return new CopixImage ($pFileName);
		}
	}
	
	/**
	 * Récupère les informations d'une image dans $_imgInfo
	 *
	 * @param void
	 * @return void
	 */
	private function _getInfo () {
		$this->_imgInfo = getimagesize($this->_imgFileName);
		return $this->_imgInfo;
	}
	
	/**
	 * Vérifie si l'extension est supportée et retourne cette derni�re
	 *
	 * @return String or bool : nom de l'extension ou false si elle n'est pas support�e
	 */
	private function _getExtension () {
		if ($this->_imgInfo === null) {
				$this->_getInfo();
		}
		if ($this->_format === null) {
			$mime = image_type_to_mime_type($this->_imgInfo[2]);
			$ext = strtolower (str_replace ('.', null, CopixFile::extractFileExt ($this->_imgFileName)));
			if ($ext === 'jpg') {
				$ext = 'jpeg';
			}
			
			if (strpos ($mime, $ext) !== false) {
				$this->_format = $ext;			
				return $ext;
			} else {
				return false;
			}
		} else {
			return $this->_format;
		}
		
	}
	
	/**
	 * Retourne le format de l'image (gif, bmp, jpg, png, ...)
	 *
	 * @return string : extension de l'image
	 */
	public function getFormat () {
		return $this->_format;
	}
	
	
	/**
	 * Constructeur générant une nouvelle image GD
	 * @param int $pWidth : largeur de l'image en pixels
	 * @param int $pHeight : hauteur de l'image en pixels
	 */
	public function __construct ($pFileName = null, $pWidth = 0, $pHeight = 0) {
		// Vérification de l'extension gd2
		if (!function_exists ('imagegd')){
			throw new Exception ('L\'extension GD ou GD2 est nécessaire pour pouvoir utiliser CopixImage');
		}
		if ($pFileName == null) {
			if (!$this->_img = imagecreatetruecolor ($pWidth, $pHeight)) {
				throw new CopixException (_i18n('copix:copixcopiximage.error.loadfailure'));
			}
		} elseif ($pFileName !== null) {
			$this->_imgFileName = $pFileName;
			if (function_exists('imagecreatefrom'.$this->_getExtension ())) {				
				if (!$this->_img = call_user_func('imagecreatefrom'.$this->_getExtension(), $this->_imgFileName)) {					
					if (!$this->_getExtension()) {
						throw new CopixException (_i18n('copix:copiximage.error.loadfailure'));
					}
				}
			} else {
				throw new CopixException (_i18n('copix:copiximage.error.badformat'));
			}
		}
	}
	
	/**
	 * Accesseur pour la largeur
	 *
	 * @return int $_width
	 */
	public function getWidth () {
		if ($this->_imgInfo == null) {
			$this->_getInfo();
		}
		if ($this->_width == 0) {
			$info = $this->_imgInfo;
			$this->_width = $info[0];
		}
		return $this->_width;
	}
	
	/**
	 * Accesseur pour la hauteur
	 *
	 * @return int $_weight
	 */
	public function getHeight () {
		if ($this->_imgInfo == null) {
			$this->_getInfo();
		}
		if ($this->_height == 0) {
			$info = $this->_imgInfo;
			$this->_height = $info[1];
		}
		return $this->_height;
	}
	
	/**
	 * Sauvegarde de l'image dans un fichier de la même extension
	 *
	 * @param string $pFileName : emplacement de l'image
	 */
	public function save ($pFileName = null) {
		$funcName = 'image'.$this->_getExtension();
		$funcName ($this->_img, $pFileName ? $pFileName : $this->_imgFileName);
	}
	
	/**
	 * Retourne l'image actuelle sous la forme d'une variable
	 * @param String $pFormat : format de l'image que l'on veut afficher (bmp, gif, png, jpeg, ...)
	 * @return unknown $_img
	 */
	public function getContent ($pFormat = null) {
		$pFormat = ($pFormat == null) ? $this->_getExtension () : $pFormat;
		if (function_exists('image'.$pFormat)) {
			$funcName = 'image'.$pFormat;
			ob_start ();
			$funcName($this->_img);
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		} else  {
			throw new CopixException (_i18n('copix:copiximage.error.badchoice'));
		}
	}
	
	/**
	 * Accesseur retournant l'objet image gd
	 *
	 * @return unknown $_img
	 */
	public function getImg () {
		return $this->_img;
	}
	
	/**
	 * Redimensionnement de l'image
	 *
	 * @param int $pWidth Largeur en pixel
	 * @param int $pHeight Hauteur en pixel
	 * @param String $pKeepProportions : "true" ou "false" 
	 */
	public function resize ($pWidth, $pHeight, $pKeepProportions = true) {
		$pWidth = (int)$pWidth;
		$pHeight = (int)$pHeight;
		// Chargement		
		$source = $this->_img;
		$size = $this->_imgInfo;
		if ($pKeepProportions) {
			if ($pWidth > 0 && $pHeight > 0) {
				if (($pWidth / $size[0]) > ($pHeight / $size[1])) {
					$pWidth = ($pHeight / $size[1]) * $size[0];
				}else{
					$pHeight = ($pWidth / $size[0]) * $size[1];
	            }
			} else if ($pHeight > 0) {
				$pWidth = ($pHeight / $size[1]) * $size[0];
			} else if ($pWidth > 0) {
				$pHeight = ($pWidth / $size[0]) * $size[1];
			}
			
		}

		// Redimensionnement
		if (function_exists ('imagecreatetruecolor')) {
			$this->_img = imagecreatetruecolor ((int)$pWidth, (int)$pHeight);
		} else {
			$this->_img = imagecreate ((int)$pWidth, (int)$pHeight);
		}

		if (!$this->_img) {
			throw new CopixException (_i18n('copix:copiximage.error.createfailure'));
		}
		if (!imagecopyresized ($this->_img, $source, 0, 0, 0, 0, (int)$pWidth, (int)$pHeight, $size[0], $size[1])) {
			throw new CopixException (_i18n('copix:copiximage.error.resizefailure'));
		}

		imagedestroy ($source);
	}
	
	/**
     * 
     * Fonction de redecouppage d'image (crop)
     * @param int $pDestW
     * @param int $pDestH
     * @param String $pImage adresse de l'image
     */
	public function crop($pDestW, $pDestH, $pX = 0, $pY = 0) {
	      // recuperation des dimensions de l'image Source
	      $src_w = $this->getWidth();
	      $src_h = $this->getHeight();
	      
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
	      $Ress_Src = $this->_img;
	
	      // creation d une ressource-image "Dst" aux dimensions finales
	      $Ress_Dst = $this->_createResource ($W, $H, $this->_imgInfo[2]);
	
	      if (!$pX && !$pY){
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
			imagecopyresampled($Ress_Dst,$Ress_Src,$X_Dst,$Y_Dst,$X_Src,$Y_Src,$W_copy,$H_copy,$W_copy,$H_copy);
		} else {
			imagecopyresampled($Ress_Dst,$Ress_Src,0, 0, $pX, $pY, $pDestW, $pDestH, $pDestW, $pDestH);
		}
		$this->_img = $Ress_Dst;
	}
	
	/**
	 * Superpose une image de chemin $pFileName sur l'image courante
	 *
	 * @param String $pFileName : chemin de l'image à superposer
	 */
	public function superpose ($pFileName) {
		$filter = self::load($pFileName)->getImg();
		$tampon = imagecolorallocatealpha ($filter, 0, 0, 0, 127);
		imagecolortransparent ($filter, $tampon);
		
		$filter_width = imagesx($filter);
		$filter_height = imagesy($filter);
		
		$size = $this->_imgInfo;
		
		$dest_x = $size[0] - $filter_width;
		$dest_y = $size[1] - $filter_height;
		
		$image = $this->getImg();
		if (!imagecopymerge ($image, $filter, $dest_x, $dest_y, 0, 0, $filter_width, $filter_height, 100)) {
			throw new CopixException (_i18n('copix:copiximage.error.superpose'));
		}
		
		$this->_img = $image;
	}
	
	/**
	 * Applique un filtre de transparence sur l'image courante
	 *
	 * @param String $pFileNameFilter : chemin du filtre
	 * @param int $pRedFilter : intensité de rouge du filtre
	 * @param int $pGreenFilter : intensité de vert du filtre
	 * @param int $pBlueFilter : intensité de bleu du filtre
	 */
	public function applyTransparencyFilter ($pFileNameFilter, $pRedFilter, $pGreenFilter, $pBlueFilter) {
		// On superpose le filtre sur l'image
		$this->superpose($pFileNameFilter);
		
		// Changement de couleur pour la transparence
		if (!$maskbackground = imagecolorallocatealpha ($this->_img, $pRedFilter, $pGreenFilter, $pBlueFilter, 127)) {
			throw new CopixException (_i18n('copix:copiximage.error.filtercolors'));
		}
		if (!imagecolortransparent ($this->_img, $maskbackground)) {
			throw new CopixException (_i18n('copix:copiximage.error.filter'));
		}
	}
	
	/**
	 * 
	 * Permet d'applique une rotation à l'image
	 * @param int $pAngle
	 * @param int $pCoul
	 */
	public function rotate($pAngle, $pCoul = 0){
		$this->_img = imagerotate($this->_img, $pAngle, $pCoul);
	}
	
    /**
     * 
     * Créé une image, applique les fonctions de transparence et de couleur en fonction des types
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
    
    public function blackAndWhite(){
    	for ($i=0; $i < $this->getWidth(); ++$i) {
		    for ($j=0; $j < $this->getHeight(); ++$j) {
		        $pxl_color = imagecolorsforindex ($this->_img, imagecolorat ($this->_img, $i, $j));
		        $gray = intval (($pxl_color['blue'] + $pxl_color['green'] + $pxl_color['blue'])/3);
		        $color = imagecolorallocatealpha ($this->_img, $gray, $gray, $gray, $pxl_color['alpha']);
		        imagesetpixel ($this->_img, $i, $j, $color);
		    }
		}
    }
}