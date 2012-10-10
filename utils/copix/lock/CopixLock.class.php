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
 * Interface de CopixLock
 *
 */
interface ICopixLock {
    public static function lock ($pId);
    public static function unlock ($pId);
}

/**
 * Permet de locker
 * @package copix
 * @subpackage core
 */
class CopixLock implements ICopixLock {
    
    /**
	 * Stockage des locks pour ne pas faire de locks qui boucle dans un meme script
	 *
	 * @var array
	 */
    private static $_arLock = array ();
    
    /**
     * Génère une clé string depuis un objet complexe
     *
     * @param mixed $pId
     * @return string clé
     */
    private static function getKey ($pId) {
        return md5 (serialize ($pId));
    }
    
    /**
     * Méthode qui attend de ne plus etre locké, et lock après
     *
     * @param mixed $pId l'id du lock (peut etre un objet complexe)
     * @return boolean
     */
    public static function lock ($pId) {
        if (isset (self::$_arLock[self::getKey($pId)]) && self::$_arLock[self::getKey($pId)]) {
            return false;
        }
        self::$_arLock[self::getKey($pId)] = true;
        if (function_exists('sem_acquire')) {
            return CopixLockSemaphore::lock(self::getKey($pId));
        } else {
            return CopixLockFile::lock(self::getKey($pId));
        }
        return true;
    }
    
    /**
     * Libère le lock
     *
     * @param mixed $pId l'id du lock (peut etre un objet complexe)
     * @return boolean
     */
    public static function unlock ($pId) {
        if (self::$_arLock[self::getKey($pId)]) {
            self::$_arLock[self::getKey($pId)] = false;
            if (function_exists('sem_acquire')) {
                return CopixLockSemaphore::unlock(self::getKey($pId));
            } else {
                return CopixLockFile::unlock(self::getKey($pId));
            }
        } else {
            return false;
        }
    }
}