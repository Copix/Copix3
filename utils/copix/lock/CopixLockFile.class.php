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
 * Permet de locker en utilisant les fonctions de lock des fichiers
 * @package copix
 * @subpackage core
 */
class CopixLockFile implements ICopixLock {
    
    /**
     * Tableau contenant les différents handle de fichier
     *
     * @var array
     */
    private static $_arHandle = array (); 
    
    /**
     * Méthode qui attend de ne plus etre locké, et lock après
     *
     * @param mixed $pId l'id du lock
     * @return boolean
     */
    public static function lock ($pId) {
        self::$_arHandle[$pId] = fopen(COPIX_TEMP_PATH.'/lock/'.$pId, 'a');
        flock(self::$_arHandle[$pId], LOCK_EX);
        return true;
    }
    
    /**
     * Libère le lock
     *
     * @param mixed $pId l'id du lock
     * @return boolean
     */
    public static function unlock ($pId) {
        if (isset (self::$_arHandle[$pId])) {
            fclose(self::$_arHandle[$pId]);
            self::$_arHandle[$pId] = null;
            return true;
        }
        return false;
    }
}