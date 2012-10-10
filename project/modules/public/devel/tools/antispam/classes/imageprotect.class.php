<?php
/**
 * @package     standard
 * @subpackage  default
 * @author      Duboeuf Damien
 * @copyright   CopixTeam
 * @link        http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion des Image de protection anti-spam
 * @package tools
 * @subpackage imageprotect
 */
class ImageProtect {
    
    /**
     * Crée une image de type jpeg contenant un code aléatoire
     * 
     * @param string    $sessionId  Identifiant avec lequel sera enregistré le code de confiramation en session
     * @param string    $pathFile   Adresse du fichier image à créer
     */
    public static function createImage ($sessionId, $pathFile) {
        
        $code           = _class('generictools|PasswordGenerator')->generate ();
        $spaceSeparator = 23;
        $largeur        = strlen($code)*$spaceSeparator + 15;
        $hauteur        = 30;
        
        // Définition des dimensions1
        $img = imagecreate($largeur, $hauteur);
        
        // Défintion des couleurs
        $bgc       = imagecolorallocate($img, 255, 255, 255);
        $black     = imagecolorallocate($img, 0, 0, 0);
        $red       = imagecolorallocate($img, 200, 0, 0);
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
        
        
        // Création de l'image (qualité 12% : très médiocre)
        imagejpeg($img, $pathFile, 12);
        imagedestroy($img); 
        
        // Ajout en session de la valeur du code de confirmation
        CopixSession::set('antispam|imageprotect', $code, $sessionId);
        
    }
    
    public static function getCode ($sessionId, $code) {
        // Recupère le code de confirmation
        $codeSession = CopixSession::get ('antispam|imageprotect', $sessionId);
        if ($codeSession === null) {
            return false;
        }
        
        // Supprime le namespace de la session
        CopixSession::destroyNamespace($sessionId);
        
        return (strtolower ($codeSession) == strtolower ($code));
    }
}
?>