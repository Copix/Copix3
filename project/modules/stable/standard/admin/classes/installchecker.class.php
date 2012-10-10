<?php
/**
 * @package standard
 * @subpackage admin 
 * 
 * @copyright CopixTeam
 * @license lgpl
 * @author Salleyron Julien 
 */

/**
 * classe regroupant les tests pour afficher les différentes tâches administrable
 * @package standard
 * @subpackage admin 
 */
class installChecker {
    
	/**
     * Test si une base par défaut est configuré
     *
     * @return boolean
     */
	public function isValidDefaultDatabase() {
		try {
			$profilName=CopixConfig::instance ()->copixdb_getDefaultProfileName ();
			if ($profilName===null) return false;
			$ct = CopixDb::getConnection($profilName);
		} catch (Exception $e) {
			return false;
		}
		return true;
	}
    
    /**
     * Retourne vrai ou faux selon que l'admin est possible ou pas
     *
     * @return boolean
     */
    public function adminIsInstall() {
        return CopixModule::isEnabled ('admin');
    }
    
    /**
     * Est ce que le type de la base est disponible?
     *
     * @param string $pTypeDb
     * @return boolean
     */
    public function typeDbInstalled ( $pTypeDb ) {
        $availableDriver = CopixDB::getAvailableDrivers ();
        return (in_array ($pTypeDb, $availableDriver));
    }

    /**
     * Est-ce qu'apc est installé?
     *
     * @return boolean
     */
    public function apcInstalled() {
        return function_exists ('apc_fetch');
    }

    /**
     * Est ce que magicquotes est en place?
     *
     * @return boolean
     */
    public function magicquotesInstalled() {
        return get_magic_quotes_gpc ();
    }
    
    /**
     * Est ce que le plugin magicquote est installé?
     * 
     * @return boolean
     */
    public function magicquotesPluginInstalled() {
        return CopixPluginRegistry::isRegistered ('default|magicquotes');
    }
}