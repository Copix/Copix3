<?php
/**
 * @package		copix
 * @subpackage	filter
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Interface pour les filtres Copix
 * 
 * @package copix
 * @subpackage filter
 */
interface ICopixFilter extends ICopixDataBehaviour {
	/**
	 * Construction avec un tableau de paramètres
	 *
	 * @param array $pParams
	 */
	public function __construct ($pParams = array ());

	/**
	 * Application du filtre sur $pValue et modification de $pValue en conséquence
	 *
	 * @param mixed $pValue la valeur sur laquelle appliquer le filtre
	 * @return mixed la valeur $pValue sur laquelle le filtre a été appliqué 
	 */
	public function update (& $pValue);
}