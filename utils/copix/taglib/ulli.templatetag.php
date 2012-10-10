<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagUlLi {
    public function process ($pParams) {
        extract ($pParams);
        if (!isset ($values)) {
            throw new CopixTemplateTagException('le paramètre values doit être renseigné');
            return;
        }
        return $this->ulli_internal_li (is_array ($values) ? $values : array ($values));
    }
    
    /**
     * Génération des listes
     * @param	mixed 	$values	éléments que l'on souhaite mettre dans la liste.
     * @return string	le code HTML correspondant au UL / LI	
     */
    private function ulli_internal_li ($values) {
        $toReturn = '';
        if (is_array ($values)){
            if (count ($values)){
                $toReturn .= '<ul>';
                foreach ($values as $key=>$item){
                    $toReturn .= $this->ulli_internal_li($item);
                }
                $toReturn .= '</ul>';
            }
        }else{
            $toReturn .= '<li>'.$values.'</li>';
        }
        return $toReturn;
    }
}
?>