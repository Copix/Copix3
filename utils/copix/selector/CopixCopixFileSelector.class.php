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
 * Implémente les selecteurs de fichiers/composant du noyau copix (dans COPIX_PATH/core)
 * @package copix
 * @subpackage core
 */
class CopixCopixFileSelector extends CopixAbstractSelector {
	var $module='[copix]';
	public function __construct($selector){
		$this->type = 'copix';
		if (($pos = strpos ($selector, 'copix:')) === 0){
			$this->fileName = substr ($selector, 6);//we know 'copix:' len is 6.
		}else{
			throw new CopixException (_i18n ('copix:copix.error.fileselector.invalidSelector', $selector));
		}
	}
	protected function _getPath($directory){
		return COPIX_PATH.$directory;
	}
	protected function _getSelector(){
		return 'copix:'.$this->fileName;
	}
	protected function _getQualifier () {
		return 'copix:';
	}
}