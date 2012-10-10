<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      VUIDART Sylvain
 */

/**
 * Serices de menu
 * @package     cms
 * @subpackage  heading
 */
class HeadingElementChooserServices {

    const AFFICHAGE_MINIATURES = 0;

    const AFFICHAGE_DETAIL = 1;

    /**
     * Reordonne les enfants selon leur display_order_hei
     * Gere les erreurs de dupplication d'ordre : si 2 elements ont le même rang.
     *
     * @param Array $pArChildren
     * @return Array
     */
    public function orderChildren ($pArChildren) {
        $order = array();
        //tableau des elements qu'on n'a pas pu mettre dans le tableau car 2 éléments avaient la même clé.
        $notSorted = array();
        foreach ($pArChildren as $child) {
            if (array_key_exists($child->caption_hei.$child->display_order_hei, $order)) {
                $order[$child->display_order_hei.$child->display_order_hei.$child->caption_hei] = $child;
                //$notSorted[$child->caption_hei.$child->display_order_hei] = $child;
            } else {
                $order[$child->display_order_hei.$child->caption_hei] = $child;
            }
        }
        ksort($order);

        //Si 2 fois le meme rang
        /*	if (!empty($notSorted)){
			$temp = array();	
			foreach ($notSorted as $itemToSort){
				foreach ($order as $key => $itemSorted){
					if ($key < $itemToSort->display_order_hei){
						$temp[$key] = $itemSorted;
					}
					else if ($key == $itemToSort->display_order_hei){
						$temp[$key] = $itemToSort;
						$temp[$key+1] = $itemSorted;
					}
					else if ($key > $itemToSort->display_order_hei){
						$temp[$key + 1] = $itemSorted;
					}
				}
				$order = $temp;
			}
		}*/
        return $order;
    }

    /* Regarde dans la branche donné si des elements sont de type compris dans $pFilter
	 *
	 * @param array $pElements
	 * @param array $pFilter
	 * @return boolean
    */
    public function checkFilter ($pElements, $pFilter) {
        if (empty($pFilter)) {
            return true;
        }

        $heiServices = _ioClass('heading|headingElementInformationServices');
        $lServices = _ioClass('heading|linkservices');

        foreach ($pElements as $element) {
            if (in_array($element->type_hei, $pFilter)) {
                return true;
            }
            
            //on regarde si un element de type donné existe dans la rubrique
            if ($element->type_hei == "heading"){
	        	$sp = _daoSP()->addCondition("type_hei", "=", $pFilter)->addCondition("hierarchy_hei", "LIKE", "%-".$element->public_id_hei."-%");     	
	        	$results = DAOcms_headingelementinformations::instance ()->findBy($sp);
	        	if (count($results)){
	        		return true;
	        	}
            }
        }
        return false;
    }
}