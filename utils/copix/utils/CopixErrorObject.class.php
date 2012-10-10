<?php
/**
* @package   copix
* @subpackage core
* @author   Croes Gérald
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Objet pour transporter des erreurs
* @package copix
* @subpackage core
*/
class CopixErrorObject implements ArrayAccess, Countable, Iterator {
    /**
    * Associative array that carries errors.
    * @var array
    * @access private
    */
    private $_errors = array ();

    /**
    * constructor...
    * @param   mixed   $params      liste d'erreurs
    */
    public function __construct ($params = null) {
       $this->addErrors ($params);
    }

    /**
    * Sets an error.
    * override the actual error if it already exists.
    * @param mixed   code the code error
    * @param mixed   value the error message
    */
    public function addError ($code, $value){
        $this->_errors[$code] =  $value;
    }

    /**
    * add multiple errors.
    * @param array   $toAdd    associative array[code] = error or an object
    */
    public function addErrors ($toAdd, $pForceAppend = false){
        if (is_array ($toAdd)){
            foreach ($toAdd as $code=>$elem){
            	if ($pForceAppend === false){
            		$this->addError ($code, $elem);
            	}else{
            		$this->_errors[] = $elem;
            	}
            }
        }elseif (is_object ($toAdd)){
        	if ($toAdd instanceof CopixErrorObject){
        		$this->addErrors ($toAdd->asArray (), $pForceAppend);
        	}else{
        		$this->addErrors (get_object_vars ($toAdd), $pForceAppend);
        	}
        }elseif (is_string ($toAdd)){
        	$this->_errors[] = $toAdd;
        }
    }

    /**
    * gets the error from its code
    * @return string error message
    */
    public function getError ($code){
        return isset ($this->_errors[$code]) ? $this->_errors[$code] : null;
    }
    
    /**
    * says if the error $code actually exists.
    * @param   mixed   $code   code error
    * @return boolean
    */
    public function errorExists ($code){
        return array_key_exists ($code, $this->_errors);
    }
    /**
    * says if there are any error in the object
    * @return boolean
    */
    public function isError (){
        return count ($this->_errors) > 0;
    }
    /**
    * indique le nombre d'erreurs assignées.
    * @return int
    */
    public function countErrors (){
        return count ($this->_errors);
    }
    /**
    * gets the errors as an object, with properties for each error codes
    * If there are numbers for code errors, convert them into _Code
    * @return object
    */
    public function asObject (){
        $toReturn = (object) null;
        foreach ($this->_errors as $code=>$value){
            if (!is_integer (substr ($code, 0, 1))){
                $toReturn->$code = $value;
            }else{
                $toReturn->{'_'.$code} = $value;
            }
        }
        return $toReturn;
    }
    /**
    * gets the errors as an array
    * @return array  associative array [code] = message
    */
    public function asArray (){
        return $this->_errors;
    }
    /**
    * gets the errors as a single string.
    * @return string error messages
    */
    public function asString ($pGlueString = '<br />'){
        return implode ($pGlueString, array_values ($this->_errors));
    }
    
    /**
     * Conversion auto en chaine de caractères
     * @return string
     */
    public function __toString (){
    	return $this->asString ();
    }
    
    /**
     * Récupération d'une erreur d'un code donné
     *
     * @param mixed $pOffset	le code erreur
     * @return mixed l'erreur
     */
    public function offsetGet ($pOffset){
    	return $this->getError ($pOffset);
    }
    
    /**
     * Définition d'une erreur à un offset donné
     *
     * @param unknown_type $pOffset
     * @param unknown_type $pValue
     */
    public function offsetSet ($pOffset, $pValue){
    	$this->addError ($pOffset, $pValue);
    }

    /**
     * Supression d'une erreur à un offset donné
     *
     * @param mixed $pOffset le code erreur a supprimer
     */
    public function offsetUnset ($pOffset){
    	$this->_errors[$pOffset] = null;
    }
    
    /**
     * Indique si une erreur de code donné existe
     *
     * @param mixed	$pOffset	le code erreur
     * @return boolean
     */
    public function offsetExists ($pOffset){
    	return $this->errorExists ($pOffset);
    }
    
	/**
	 * Indique le nombre d'erreur
	 *
	 * @return int
	 */
    public function count (){
		return count ($this->_errors);
	}
	
	//Iterator
	private $_index = 0;
	
	public function current (){
		return $this->_errors[$this->key ()];
	}
	
	public function key (){
		$keys = array_keys ($this->_errors);
		return $keys[$this->_index];
	}
	
	public function valid (){
		return $this->_index < count ($this->_errors);
	}
	
	public function next (){
		$this->_index++;
	}
	
	public function rewind (){
		$this->_index = 0;
	}
}
?>