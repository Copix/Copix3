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
        $csvLogFile->addLine($this->_getArInfosLog ($pType, $pDate, $pMessage, $pLevel, $pArExtra));
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
    }

    /**
     * Retourne les logs sous forme d'itérateur
     */
    public function getLog ($pProfil){
        $page = CopixSession::get('log|numpage')-1;
        $nbItemsByPage = 20;
         
        if (file_exists ($this->_getFileName ($pProfil))){

            // Création d'un objet CopixCSV pour contenir le contenu du fichier
            $csvLog = new CopixCsv($this->_getFileName ($pProfil));
            $time_start = microtime(true);
            $csvLines      = $csvLog->getIterator();
            
            $csvNbLines    = $csvLines->count();
            
            
            // Calcul de la position et des offset
            $pPosition     = ($csvNbLines - ($page*$nbItemsByPage))-$nbItemsByPage;
            
            
            // Attention dans le cas des logs on parcourt le fichier à rebours
            if ($pPosition < 0) {
                $pOffset = $nbItemsByPage + $pPosition;
                $pPosition = 0; 
            } else {
                $pOffset = $nbItemsByPage;
            }
            
            $csvLines->seek($pPosition);
            
            for ($i = 0 ; $i < $pOffset ; $i++) {
                $content[] = $csvLines->next();
            }

            $content = array_reverse ($content);
            // Bug sur l'itérateur	
            // array_shift($content);
            CopixSession::set ('log|nbpage', ceil($csvNbLines/$nbItemsByPage));
            	
            $time_end = microtime(true);
            $time = $time_end-$time_start;
            _log("Temps de récupérations des lignes CSV:".$time,"performance",CopixLog::INFORMATION);

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