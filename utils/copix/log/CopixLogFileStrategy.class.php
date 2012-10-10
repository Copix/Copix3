<?php
/**
 * @package   copix
 * @subpackage log
 * @author    Landry Benguigui
 * @copyright 2001-2006 CopixTeam
 * @link      http://copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Log dans un fichier
 *
 * @package copix
 * @subpackage log
 */
class CopixLogFileStrategy implements ICopixLogStrategy {

    /**
     * Séparateur entre les éléments de lo
     * @var string
     */
    private $_separateur = "\n";

    /**
     * Le profil en cours de lecture
     * @var string
     */
    private $_profil = null;

    /**
     * Sauvegarde les logs dans le fichier
     *
     * @param String $pMessage log à sauvegarder
     * @param String $tab tableau d'option
     * @return boolean si le fichier a bien été écrit
     */
    public function log ($pProfil, $pType, $pLevel, $pDate, $pMessage, $pArExtra){
        $csvLogFile = new CopixCsv($this->_getFileName($pProfil));
        return $csvLogFile->addLine($this->_getArInfosLog ($pType, $pDate, $pMessage, $pLevel, $pArExtra));
    }

    /**
     * Formate le message à sauvegarder
     * @param String $pProfil nom du profil configurer dans copixConfig
     * @param Array $tab tableau d'option
     * @return array l'ensemble des infos dans un tableau ordonné
     */
    private function _getArInfosLog ($pType, $pDate, $pMessage, $pLevel, $tab){
        $date = $pDate;
        $classe = "";
        $line = "";
        $file = "";
        $function = "";
        $user = "";
        if (isset ($tab['classname'])){
            $classe = $tab['classname'];
        }
        if (isset ($tab['line'])){
            $line = $tab['line'];
        }
        if (isset ($tab['file'])){
            $file = $tab['file'];
        }
        if (isset ($tab['functionname'])){
            $function = $tab['functionname'];
        }
        if (isset ($tab['user'])){
            $user = $tab['user'];
        }

        return array ($pType, $date, $pLevel, $classe, $line, $file, $function, $user, str_replace($this->_separateur," ",$pMessage));
    }

    /**
     * Conversion du tableau en objet
     */
    function toObject ($arInfos){
        $object = new StdClass ();
        if (count ($arInfos)>1){
            $object->type         = $arInfos[0];
            $object->date         = $arInfos[1];
            $object->level        = $arInfos[2];
            $object->classname    = $arInfos[3];
            $object->line         = $arInfos[4];
            $object->file         = $arInfos[5];
            $object->functionname = $arInfos[6];
            $object->user         = $arInfos[7];
            $object->message      = $arInfos[8];
        }
        $object->profil = $this->_profil;
        return $object;
    }

    /**
     * Supprime tout les log du profil donné
     * @param	string	$pProfil	le nom du profil de log que l'on souhaite supprimer
     * @return void
     */
    public function deleteProfile ($pProfil){
        if (file_exists ($fileName = $this->_getFileName ($pProfil))){
            unlink ($fileName);
        }
        // Suppression des variables de sessions
        CopixSession::set ('log|numpage', 1);
        CopixSession::set ('log|nbpage', 1);
    }

    /**
     * Retourne les logs sous forme d'itérateur
     */
    public function getLog ($pProfil, $pNbItems = 20){
        $page = CopixSession::get('log|numpage')-1;
         
        if (file_exists ($this->_getFileName ($pProfil))){
            // Création d'un objet CopixCSV pour contenir le contenu du fichier
            $csvLog = new CopixCsv($this->_getFileName ($pProfil));

            
            // Récupération de l'itérateur et compte du nombre de ligne
            $csvLines = $csvLog->getIterator();
            $csvNbLines = $csvLines->count();
            
            // Calcul de la position et des offset
            $pPosition = ($csvNbLines - ($page*$pNbItems))-$pNbItems;
            
            // Calcul de la position de départ pour parcourir la portion du fichier à afficher
            if ($pPosition < 0) {
                $pOffset = $pNbItems + $pPosition;
                $pPosition = 0; 
            } else {
                $pOffset = $pNbItems;
                $pPosition -= 1;
            }
            
            $csvLines->seek($pPosition);
            $content = array();
            for ($i = 0 ; $i < $pOffset ; $i++) {
                $content[] = $csvLines->current ();
                $csvLines->next ();
            }

            $content = array_reverse ($content);

            CopixSession::set ('log|nbpage', ceil($csvNbLines/$pNbItems));
            	
            $arrayObject = new ArrayObject (array_map (array ($this, 'toObject'), $content));
            return $arrayObject->getIterator ();
        } 
        return new ArrayObject ();
    }

    /**
     * Fonction qui retourne le nom du fichier de log, si il n'existe pas on le génère
     * @return String nom du fichier de log
     */
    private function _getFileName ($pProfil){
        return COPIX_LOG_PATH.$pProfil.'.log';
    }
}
?>