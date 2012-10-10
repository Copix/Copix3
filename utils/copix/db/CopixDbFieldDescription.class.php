<?php
/**
* @package		copix
* @subpackage	db
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Description des champs d'une table
 * @package copix
 * @subpackage db
 */
class CopixDBFieldDescription {
    /**
     * Nom du champ
     */
    public $name;
    
    /**
     * Est ce que le champ accepte les valeurs null
     */
    public $notnull;
    
    /**
     * Clé primaire
     */
    public $pk;
    
    /**
     * Type du champ
     */
    public $type;
    
    /**
     * Longueur du champ
     */
    public $length;
    
	/**
	 * Construction de la description d'un champ
	 *
	 * @param string $pName	le nom du champ
	 */
    public function __construct ($pName){
	    $this->name = $pName;
	} 
}