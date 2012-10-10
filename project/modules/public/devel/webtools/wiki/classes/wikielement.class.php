<?php
/**
 * @package	webtools
 * @subpackage	wiki
* @author	Patrice Ferlet
* @copyright CopixTeam
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Représentation d'un élément de type wiki
 * @package	webtools
 * @subpackage	wiki
 */
class WikiElement {
    /**
	 * Le type de l'élément 
	 * @var string
	 */
	var $type = null;

	/**
	 * La valeur de l'élément
	 * @var mixed 
	 */
	var $data = null;

	/**
	 * Constructeur
     * @param string type (header, paragraphe, code, lists) 
     * @param mixed data value
	 */
    public function __construct ($type=null, $data=null) {
		$this->setType ($type);
		$this->setData ($data);    
    }
    
    /**
     * Défini le type de l'élément
     * @param string type (header, paragraphe, code, lists)
     */
    public function setType ($pType){
    	$this->type = $pType;
    }
    
    /**
     * Défini la valeur de l'élément
     * @param mixed data value
     */
    public function setData ($pData){
    	$this->data = $pData;
    }
}
?>