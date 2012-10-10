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
 * Représente un champ de saisie pour une valeur booléenne
 * @package copix
 * @subpackage forms 
 */
class CopixFieldBoolean extends CopixAbstractField implements ICopixField  {
	/**
	 * Retourne la HTML pour afficher le controle de saisie
	 *
	 * @param string $pName  le nom du champ
	 * @param mixed  $pValue la valeur du champ
	 * @param unknown_type $pMode
	 * @return unknown
	 */	
	public function getHTML ($pName, $pValue, $pMode = 'edit') {
		return '<input type="checkbox" value="1" name="'.$pName.'" id="'.$pName.'" '.(($pValue === '1') ? 'checked' : null ).' />';
	}
	
	/**
	 * Mise à jour de la valeur depuis la requête  
	 *
	 * @param string $pName    Le nom du champ dans la requête
	 * @param mixed  $pDefault la valeur par défaut a saisir si rien n'est trouvé dans la requête
	 * @return mixed 
	 */
	public function fillFromRequest ($pName, $pDefault = null, $pValue = null) {
		return _request ($pName, $pDefault === null ? '0' : $pDefault);
	}
}