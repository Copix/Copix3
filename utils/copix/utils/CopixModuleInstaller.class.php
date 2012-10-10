<?php
/**
 * Enter description here...
 *
 */
interface ICopixModuleInstaller {
    
	/**
	 * Méthode à executer juste avant l'installation.
	 * 
	 * Les scripts SQL d'installation ont éte éxécutés.
	 */
    public function processPreInstall ();

    /**
     * Méthode à executer juste après l'installation.
     * 
     * Toutes les fonctionnalités du module sont disponibles.
     */
    public function processPostInstall ();

    /**
     * Méthode à executer juste avant la suppression.
     * 
     * Toutes les fonctionnalités du module sont disponibles
     */
	public function processPreDelete ();

	/**
	 * Méthode à executer juste après la suppression.
	 * 
	 * Les scripts SQL ont été executés.
	 */
	public function processPostDelete ();
    
}

/**
 * Implémentation de ICopixModuleInstaller qui ne fait rien. A surcharger.
 *
 */
class CopixDefaultModuleInstaller implements ICopixModuleInstaller {

    /**
     * @see ICopixModuleInstaller::processPreInstall();
     */
	public function processPreInstall () {
	}

    /**
     * @see ICopixModuleInstaller::processPostInstall()
     */
    public function processPostInstall () {
	}
    
    /**
     * @see ICopixModuleInstaller::processPreDelete()
     */
	public function processPreDelete () {
	}

	/**
     * @see ICopixModuleInstaller::processPostDelete()
	 */
	public function processPostDelete () {
	}
	
}

?>