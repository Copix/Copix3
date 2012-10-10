<?php
/**
* @package		copix
* @subpackage	core
* @author		Salleyron Julien
* @copyright	CopixTeam
* @link 		http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe pour gérer les uploaded file
 * @package copix
 * @subpackage core
 */
class CopixUploadedFile {
    /**
     * Nom du fichier uploadé
     * @var string
     */
    private $_name    = null;
    
    /**
     * Type du fichier uploadé
     *
     * @var string
     */
    private $_type    = null;
    
    /**
     * Chemin temporaire du fichier
     *
     * @var string
     */
    private $_tempPath = null;
    
    /**
     * Taille du fichier uploadé
     *
     * @var int
     */
    private $_size    = 0;

    /**
     * Renvoi un objet CopixUploadedFile si le fichier à bien été uploadé sur le serveur (vérification effectuée)
     * @param string $pVarName Le nom du champ de formulaire à partir duquel le fichier à été envoyé
     * @return CopixUploadedFile / false si échec
     */
    public static function get ($pVarName) {
        if (! isset ($_FILES[$pVarName]) || !is_uploaded_file ($_FILES[$pVarName]['tmp_name'])) {
            return false;
        }
        return new CopixUploadedFile ($pVarName);
    }
    
   
    /**
     * Constructeur
     *
     * @param string $pVarName Fichier a recupérer dans $_FILES
     */
    private function __construct ($pVarName) {
        $this->_name      = $_FILES[$pVarName]['name'];
        $this->_type      = $_FILES[$pVarName]['type'];
        $this->_tempPath  = $_FILES[$pVarName]['tmp_name'];
        $this->_error     = $_FILES[$pVarName]['error'];
        $this->_size      = $_FILES[$pVarName]['size'];
    }
    
    /**
     * Retourne l'erreur générée lors du téléchargement d'un fichier donné
     * @param	string 	$pVarName	le nom de la variable fichier dans le formulaire 
     * @return string
     */
    public static function getError ($pVarName){
		return isset ($_FILES[$pVarName]['error']) ? $_FILES[$pVarName]['error'] : null;
    }
    
    /**
     * Retourne le chemin temporaire
     *
     * @return string Le chemin temporaire
     */
    public function getTempPath () {
        return $this->_tempPath;
    }
    
    /**
     * Retourne le type
     *
     * @return string Le type du fichier
     */
    public function getType () {
        return $this->_type;
    }
    
    /**
     * Retourne le nom du fichier
     *
     * @return string Le nom du fichier
     */
    public function getName () {
        return $this->_name;
    }
    
    /**
     * Retourne la taille du fichier
     *
     * @return int la taille du fichier en octets
     */
    public function getSize () {
        return $this->_size;
    }
    
    /**
     * Déplace le fichier uploadé
     *
     * @param string $pPath Repertoire ou mettre le fichier
     * @param string $pFileName Nom donné au fichier (par défaut nom d'origine)
     * @return boolean true si tout va bien false sinon
     */
    public function move ($pPath, $pFileName = null) {
        if ($pFileName === null) {
            $pFileName = $this->_name;
        }
        if (substr ($pPath, strlen ($pPath)-1) != '/' && substr ($pPath, strlen ($pPath)-1) != '\\') {
            $pPath .= '/'; 
        }
        return move_uploaded_file ($this->_tempPath, $pPath.$pFileName);
    }
}
?>