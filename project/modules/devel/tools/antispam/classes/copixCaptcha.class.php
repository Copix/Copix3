<?php
/**
 * @package     tools
 * @subpackage  antispam
 * @author      Duboeuf Damien
 * @copyright   CopixTeam
 * @link        http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion des Image de protection anti-spam
 * @package tools
 * @subpackage antispam
 */
class CopixCaptcha extends Captcha {
	
	/**
	 * HTML de la question qui sera demandé
	 * @param	string	$namespace permet de différencier les captchas quand il sont mutltiple dans la meme page
	 * @param	array	$params Listes de prametre spécifique
	 * @return string
	 */
	public function getHTMLQuestion ($namespace = NULL, $params=NULL) {
		
		return '<img src="'. _url('antispam|default|getimagecaptcha') .'?id='. ($id=uniqid()) .'" alt="' . _i18n ('antispam|antispam.img_captcha') . '" />'.
		       '<input type="hidden" name="captcha_session_id'.$namespace.'" value="'.$id.'" />';
	}
	
	/**
	 * HTML de la reponse qui sera demandé
	 * @param	string	$namespace permet de différencier les captchas quand il sont mutltiple dans la meme page
	 * @param	array	$params Listes de prametre spécifique
	 * @return string
	 */
	public function getHTMLResponse ($namespace = NULL, $params=NULL) {
		
		$params['name'] = 'captcha_response'.$namespace;
		
		// autocomplete="off" est en JS pour validation xHTML
		return _tag('inputtext', $params).
		'<script type="text/javascript">
			//<!--
			$(\'password_confirmation_dbuser\').setProperty (\'autocomplete\',\'off\');
			//-->
		</script>';
	}
	
	
	/**
	 * Fonction de vérification des captcha
	 * @param	string	$namespace permet de différencier les captchas quand il sont mutltiple dans la meme page
	 */
	public function check ($namespace = NULL){
		
		$namespace = ($namespace) ? '_' . $namespace : NULL;
		
		CopixRequest::assert('captcha_session_id'.$namespace);
		CopixRequest::assert('captcha_response'.$namespace);
		
		$session_id = _request ('captcha_session_id'.$namespace);
		$response = _request ('captcha_response'.$namespace);
		
		$codeSession = CopixSession::get ('antispam_captcha_code', $session_id);
		if ($codeSession === null) {
			return false;
		}
		return (strtolower ($codeSession) == strtolower ($response));
	}
	
	
	/**
	 * Crée une image de type jpeg contenant un code aléatoire
	 * 
	 * @param string    $sessionId  Identifiant avec lequel sera enregistré le code de confirmation en session
	 * @param string    $pathFile   Adresse du fichier image à créer
	 */
	public function createImage ($sessionId, $pathFile) {
		$code           = _class('generictools|PasswordGenerator')->generate ();
		$spaceSeparator = 23;
		$largeur        = strlen($code)*$spaceSeparator + 15;
		$hauteur        = 30;
		
		// Définition des dimensions1
		$img = imagecreate($largeur, $hauteur);
		
		// Défintion des couleurs
		$bgc       = imagecolorallocate($img, 255, 255, 255);
		$black     = imagecolorallocate($img, 0, 0, 0);
		$red	   = imagecolorallocate($img, 200, 0, 0);
		$green     = imagecolorallocate($img, 0, 200, 0);
		$blue      = imagecolorallocate($img, 0, 0, 200);
		$lightblue = imagecolorallocate($img, 0, 200, 200);
		$purple    = imagecolorallocate($img, 200, 0, 200);
		$yellow    = imagecolorallocate($img, 150, 150, 0);
		
		$color = array ();
		$color [] = $red;
		$color [] = $green;
		$color [] = $blue;
		$color [] = $lightblue;
		$color [] = $purple;
		$color [] = $yellow;
		
		// Chargement des fonts
		$font = array ();
		$font [] = CopixModule::getPath('antispam').COPIX_RESOURCES_DIR.'FreeMonoBold.ttf';
		$font [] = CopixModule::getPath('antispam').COPIX_RESOURCES_DIR.'FreeSerifBold.ttf';
		
		// Remplissage du fond
		imagefilledrectangle($img, 0, 0, $largeur, $hauteur, $bgc);
		
		
		// Ecriture du code
		// Le 2eme parametre est la taille de la font
		// Le 3eme parametre est l'orientation de la font
		// Le 4eme parametre est la position x (gauche du texte)
		// Le 5eme parametre est la position y (bas du texte)
		for ($i = 0; $i < strlen($code); $i++) {
			imagettftext($img, 20, 0, 10 + $i * $spaceSeparator, 23, $color[rand (0, count($color)-1)], $font[rand (0, count($font)-1)], $code[$i]);
		}
		// Ajout d'un bruit
		for($i = 0; $i < 150; $i++) {
			imagesetpixel($img, rand(0, $largeur), rand(0, $hauteur), $black);
		}for($i = 0; $i < 100; $i++) {
			imagesetpixel($img, rand(0, $largeur), rand(0, $hauteur), $color[rand (0, count($color)-1)]);
		}
		
		
		// Création de l'image (qualité 25% par default : très médiocre)
		imagejpeg($img, $pathFile, (int) CopixConfig::get ('antispam|imageQuality'));
		imagedestroy($img); 
		
		// Ajout en session de la valeur du code de confirmation
		CopixSession::set('antispam_captcha_code', $code, $sessionId);
	}
}