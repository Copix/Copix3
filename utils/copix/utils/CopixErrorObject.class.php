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
class CopixErrorObject {
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
       $this->addErrors($params);
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
    public function addErrors ($toAdd){
        if (is_array ($toAdd)){
            foreach ($toAdd as $code=>$elem){
                $this->addError ($code, $elem);
            }
        }elseif (is_object ($toAdd)){
        	if ($toAdd instanceof CopixErrorObject){
        		$this->addErrors ($toAdd->asArray ());
        	}else{
        		foreach (get_object_vars ($toAdd) as $varName=>$varValue){
        			$this->addError ($varName, $varValue);
        		}
        	}
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
    public function asString ($glueString = '<br />'){
        return implode ($glueString, array_values ($this->_errors));
    }
}
?>