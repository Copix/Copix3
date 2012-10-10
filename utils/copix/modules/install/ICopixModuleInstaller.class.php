<?php
/**
 * Interface à respecter pour les opérations d'installation de module.
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