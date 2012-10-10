<?php
/**
 * @package copix
 * @subpackage core
 * @author Croes Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * CopixRecursivePpo
 * Identique à CopixPpo sauf qu'il permet l'utilisation des appels imbriqués sans notice du style $ppo['test']->foo->foo['test'][] = 1
 * Attention : if ($ppo->unsetValue){} vaudra vrai car chaque valeur non définie retournera un objet vide
 * 
 * @package copix
 * @subpackage core
 */
class CopixRPPO extends CopixPpo {
	/**
	 * Retourne l'élément où sauvegarder la donnée
	 * 
	 * @param string $pName Nom de la propriété à récupérer
	 * @return mixed
	 */
	public function &__get ($pName) {
		$this->$pName = new CopixRPPO ();
		return $this->$pName;
	}
}