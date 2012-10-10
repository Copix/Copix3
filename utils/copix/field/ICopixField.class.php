<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

/**
 * Interface de base pour les CopixField
 * @package copix
 * @subpackage forms
 */
interface ICopixField {
	public function __construct ($pType, $pParams = array ());
	public function getParams ();
	public function getType ();
	public function fillFromRequest ($pName);
	public function fillFromRecord ($pField, $pRecord);
	public function fillRecord ($pRecord, $pField, $pValue);
	public function attach ($pValidator);
	public function getValidators ();
	public function getHTML ($pName, $pValue, $pMode = 'edit');
}