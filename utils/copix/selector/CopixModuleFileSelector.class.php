<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Gérald Croes
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Implémente les selecteurs de fichiers/composant de modules
 * @package copix
 * @subpackage core
 */
class CopixModuleFileSelector extends CopixAbstractSelector {
	/**
	 * Le nom du module attaché ('' ou null si projet)
	 * @var string
	 */
	var $module = "default";

	/**
	 * Constructeur
	 * @param string $selector le sélecteur Copix "module|element"
	 */
	function __construct($selector){
		$this->type = 'module';
		//ok, I don't use regexp here cause it's 0,40 ms slower :-)
		$tab = explode ('|', $selector);
		if (($counted = count ($tab)) > 1){
			$this->module = $tab[0] == '' ? "default" : $tab[0];
			$this->fileName=$tab[1];
		}else if ($counted == 1){
			$this->module = CopixContext::get ();
			$this->fileName = $tab[0];
		}else{
			throw new CopixException (_i18n ('copix:copix.error.fileselector.invalidSelector', $selector));
		}
		$this->module = strtolower ($this->module);
	}

	/**
	 * Indique le chemin d'accès à la ressource.
	 * @param string $directory
	 */
	protected function _getPath ($directory){
		return CopixModule::getPath ($this->module).$directory;
	}

	/**
	 * Retourne le chemin surchargé pour l'élément.
	 * @todo chemin surchargé des ressources
	 */
	protected function _getOverloadedPath ($directory){
		return $directory.$this->module.'/';
	}

	/**
	 * Récupère le sélecteur Copix complet de l'élément
	 * @return string
	 */
	protected function _getSelector(){
		return $this->module.'|'.$this->fileName;
	}

	/**
	 * Récupère la partie qualifier de l'élément
	 * @return string
	 */
	protected function _getQualifier (){
		return $this->module.'|';
	}
}