<?php
/**
 * Implémentation de ICopixModuleInstaller pour faciliter l'implémentation de ICopixModuleInstaller
 */
abstract class CopixAbstractModuleInstaller implements ICopixModuleInstaller {
    /**
     * @see ICopixModuleInstaller::processPreInstall();
     */
	public function processPreInstall () {}

    /**
     * @see ICopixModuleInstaller::processPostInstall()
     */
    public function processPostInstall () {}

    /**
     * @see ICopixModuleInstaller::processPreDelete()
     */
	public function processPreDelete () {}

	/**
     * @see ICopixModuleInstaller::processPostDelete()
	 */
	public function processPostDelete () {}

	/**
	 * Ne fait rien, pour passer une version sans avoir de scripts
	 */
	public function processDoNothing () {}
}