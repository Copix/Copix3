<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet de locker en utilisant les fonctions de semaphore php
 * @package copix
 * @subpackage core
 */
class CopixLockSemaphore implements ICopixLock {
    
	/**
	 * Stockage des clés pour sem_get
	 *
	 * @var array
	 */
    static private $_arKey = array ();
    
    /**
	 * Stockage des locks pour ne pas faire de locks qui boucle dans un meme script
	 *
	 * @var array
	 */
    static private $_arLock = array ();
    
    /**
     * Méthode qui attend de ne plus etre locké, et lock après
     *
     * @param mixed $pId l'id du lock
     * @return boolean
     */
    public static function lock ($pId) {
        sem_acquire (self::getKey($pId));
        return true;
    }
    
    /**
     * Génère le fichier servant de clé
     *
     * @param string $pId Une clé
     * @return string filepath de la clé
     */
    private static function getKey ($pId) {
        if (!isset (self::$_arKey[$pId])) {
            //On génère un fichier qui nous servira a généré la clé de lock
            $filepath = COPIX_TEMP_PATH.'/lock/'.$pId.'sem';
            if (!file_exists($filepath)) {
                CopixFile::write ($filepath, 'locker');
            }
            //On récupère la clé depuis le fichier, on mets le paramètre projet a m de manière complètement arbitraire
            $key = ftok ($filepath, 'm');
            self::$_arKey[$pId] = sem_get ($key);
        }
        return self::$_arKey[$pId]; 
    }
    
    /**
     * Libère le lock
     *
     * @param mixed $pId l'id du lock
     * @return boolean
     */
    public static function unlock ($pId) {
        sem_release (self::getKey($pId));
        return true;
    }
}