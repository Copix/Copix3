<?php
/**
 * @package		tools
 * @subpackage	menu
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Dao sur les menus
 * @package tools
 * @subpackage menu
 */
class DAOMenus {
	/**
	 * On remplace basiquement les libellés sur les noms de menu
	 *
	 * @param CopixDAORecord $pRecord	l'enregistrement à vérifier
	 * @return array / true si ok
	 */
	public function check ($pRecord){
		//Appel de la méthode parente
		if (($arErrors = $this->_compiled_check ($pRecord)) === true){
			$arErrors = array ();
		}

		//on remplace avec les bons libellés
		foreach ($arErrors as $key => $error) {
			$arErrors[$key] = str_replace (
				array ('name_menu'),
			    array (_i18n ('admin.name_menu')),
				$error
			);
		}
		
		//erreurs s'il en existe, true sinon
		return (count ($arErrors) == 0) ? true : $arErrors; 
	}
	
	/**
	 * Récupère l'élément qui d'ordre minimum pour le parent et le menu donné
	 * @param	int		$pIdParent	l'identifiant du parent
	 * @param	mixed	$pIdMenu	l'identifiant du menu
	 * @return CopixDAORecord
	 */
	public function getFirstItemForParent ($pIdParent, $pIdMenu){
		$sp = _daoSP ()
			->addCondition ('id_parent_item', '=', $pIdParent)
			->addCondition ('id_menu', '=', $pIdMenu)				
			->orderBy (array('order_item', 'ASC'));
		$results = _ioDao ('menusitems')->findBy ($sp);
		if (isset ($results[0])){
			return $results[0];
		}
		return null;
	}
	
	/**
	 * récupère l'élément d'ordre maximum pour le parent donné et le menu donné
	 * @param	int		$pIdParent	l'identifiant du parent
	 * @param	mixed	$pIdMenu	l'identifiant du menu
	 * @return CopixDAORecord
	 */
	public function getLastItemForParent ($pIdParent, $pIdMenu){
		$sp = _daoSP ()
			->addCondition ('id_parent_item', '=', $pIdParent)
			->addCondition ('id_menu', '=', $pIdMenu)				
			->orderBy (array('order_item', 'DESC'));
		$results = _ioDao ('menusitems')->findBy ($sp);
		if (isset ($results[0])){
			return $results[0];
		}
		return null;			
	}
	
	/**
	 * Récupère l'élément d'ordre précédent à l'élément de menu passé en paramètre
	 * @param CopixDAORecord $pRecord
	 * @return CopixDAORecord
	 */
	public function getPreviousItem ($pRecord){
		$sp = _daoSP ()
			->addCondition ('id_parent_item', '=', $pRecord->id_parent_item)
			->addCondition ('id_menu', '=', $pRecord->id_menu)
			->addCondition ('order_item', '<=', $pRecord->order_item)				
			->addCondition ('id_item', '<>', $pRecord->id_item)
			->orderBy (array('order_item', 'DESC'));
		$results = _ioDao ('menusitems')->findBy ($sp);
		if (isset ($results[0])){
			return $results[0];
		}
		return null;			
	}
	
	/**
	 * retourne l'élément d'ordre supérieur à l'élement de menu passé en paramètre
	 * @param CopixDAORecord	$pRecord
	 * @return CopixDAORecord
	 */
	public function getNextItem ($pRecord){
		$sp = _daoSP ()
			->addCondition ('id_parent_item', '=', $pRecord->id_parent_item)
			->addCondition ('id_menu', '=', $pRecord->id_menu)
			->addCondition ('order_item', '>=', $pRecord->order_item)
			->addCondition ('id_item', '<>', $pRecord->id_item)				
			->orderBy (array('order_item', 'ASC'));
		$results = _ioDao ('menusitems')->findBy ($sp);
		if (isset ($results[0])){
			return $results[0];
		}
		return null;			
	}
}
?>