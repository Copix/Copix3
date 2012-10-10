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
 * Fabrique de champs (CopixField)
 * @package copix
 * @subpackage forms
 */
class CopixFieldFactory {
	/**
	 * Demande de création d'un objet CopixField de type $pType
	 *
	 * @param string  $pType   le type du champ que l'on souhaite récupérer
	 * @param array   $pParams tableau de pramètres a passer au type de champ crée
	 * @param boolean $pAssert Indique si l'on souhaite vérifier que le type de champ demandé existe.
	 *                         Si false est donné, alors on retournera un CopixFieldVarchar s'il n'existe pas.
	 * @return ICopixField
	 */
	public static function get ($pType, $pParams = array (), $pAssert = true) {
		if ($pType === null) {
			$pType = 'varchar';
		}

		if (strpos ($pType, '|')) {
			return CopixClassesFactory::create ($pType, array ($pType, $pParams));
		} else {
			if (class_exists ('CopixField'.$pType)) {
				$class = 'CopixField'.$pType;
				$object =  new $class ($pType, $pParams);
				if (! $object instanceof ICopixField){
					throw new CopixException ("La classe $class devrait implémenter ICopixField pour pouvoir être utilisée dans CopixForm");
				}
				return $object;
			} else {
				if ($pAssert) {
					throw new CopixException ('Ce type n\'existe pas ['.$pType.']');
				} else {
					return new CopixFieldVarchar ($pType, $pParams);
				}
			}
		}
	}
}