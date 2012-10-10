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
 * Sélecteur capable de prendre en charge les chemins absolus de fichier (file:)
 * @package copix
 * @subpackage core
 */
class CopixFileFileSelector extends CopixAbstractSelector {
	/**
	 * Le nom du répertoire que l'on précalcule à la construction du sélecteur
	 */
	var $dirName = null;

	/**
	 * Constructeur
	 */
	public function __construct ($selector){
		$this->type = 'file';
		if (($pos = strpos ($selector, 'file:')) === 0){
			$this->fileName = basename (substr ($selector, 5));
			$this->dirName  = dirname (substr ($selector, 5)).'/';
		}else{
			throw new CopixException (_i18n ('copix:copix.error.fileselector.invalidSelector', $selector));
		}
	}

	/**
	 * récupère le chemin du fichier (sans son nom
	 * @param string $pDirectory n'est pas utilisé ici, comme le sélecteur utilise des noms absolus
	 * @return string le chemin du fichier sans son nom
	 */
	protected function _getPath ($pDirectory){
		return $this->dirName;
	}

	/**
	 * récupère le sélecteur complet 
	 */
	protected function _getSelector(){
		return 'file:'.$this->dirName.$this->fileName;
	}

	/**
	 * retourne le qualificateur
	 * @return string
	 */
	protected function _getQualifier () {
		return $this->type;
	}
}