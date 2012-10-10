<?php
/**
* @package	copix
* @subpackage ldap
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Element d'un annuaire ldap
 * @package copix
 * @subpackage ldap
 */
class CopixLDAPEntry {
    /**
    * the properties list
    */
    var $_properties = array ();

    /**
    * The result set identifier
    * @var resource
    */
    var $_rsID = null;

    /**
    * the entry ID
    * @var resource
    */
    var $_entryID = null;

    /**
    * Initialize attributes values of the given entry
    */
    private function _initValues ($array){
        foreach ($array as $key=>$value){
            if ((! is_numeric ($key)) && ($key != 'count')){
                if (isset ($value['count']) && ($value['count'] > 1)){
                    array_walk ($value, '_copix_ldap_decode_string');
                    unset ($value['count']);
                }else if ($key == 'dn'){
                    $value = utf8_decode ($value);
                }else{
                    $value = utf8_decode ($value[0]);
                }
                $key = strtolower ($key);
                $this->$key = $value;
                $this->_properties[$key] = count ((array)$value);
            }
        }
    }

    /**
    * Adds a value to an attribute. We only add the value if the attribute exists
    * @param string $attribute the attribute name
    * @param mixed  $value the value to append. If an array is given, adds each attribute
    * @return int the number of values added to the attribute
    */
    public function addAttributeValue ($attribute, $value){
        $added = 0;
        if (is_array ($value)){
            foreach ($value as $key=>$valueToSet){
                $added += $this->_addSingleAttributeValue($attribute, $valueToSet) ? 1 : 0;
            }
        }else{
            return $this->_addSingleAttributeValue($attribute, $value) ? 1 : 0;
        }
        return $added;
    }

    /**
    * Supression d'une valeu d'attribut, à la position index
    * @param string $attribute le nom de l'attribute
    * @param int $index l'index à supprimer
    */
    public function deleteAttributeValue ($attribute ,$index){
        if (isset ($this->$attribute)){
            if (is_array ($this->$attribute)) {
                array_splice ($this->$attribute, $index, 1);

                if (count ($this->$attribute) == 1){
                    $this->$attribute = $this->{$attribute}[0];
                }
            }else{
                $this->$attribute = array();
            }
        }
    }

    /**
    * Adds a value to an attribute. We only adds the value if the attribute exists
    * @param string $attribute the attribute name
    * @param mixed but array the value to add
    * @return booleans added or not
    */
    private function _addSingleAttributeValue ($attribute, $value){
        if (isset ($this->$attribute)){
            if (!is_array($this->$attribute)) {
                $this->$attribute   = array ($this->$attribute);
            }
        }else{
            $this->$attribute   = array ();
        }
        $this->{$attribute}[] = $value;
        return true;
    }

    /**
    * Initialize an entry, from an array
    */
    public function CopixLDAPEntry ($_connectionID = null, $entryID = null) {
        if ($_connectionID !== null && $entryID !== null){
            $this->_rsID    = $_connectionID;
            $this->_entryID = $entryID;
            $this->_initValues(ldap_get_attributes($this->_rsID, $this->_entryID));
            $this->dn = ldap_get_dn($this->_rsID, $this->_entryID);
        }
    }

    /**
    * gets the value of a binary entry.
    * @param string $name the attribute name we wants to retrieve
    * @return binary
    * @access public
    */
    public function getBinary ($name) {
        return ldap_get_values_len($this->_rsID, $this->_entryID, $name);
    }

    /**
    * gets the entry properties.
    */
    public function getProperties (){
        return $this->_properties;
    }

    /**
    * gets the first value of the given object
    */
    public function first ($propertyName) {
        if (isset ($this->$propertyName)){
            $value = & $this->$propertyName;
            if (is_array ($value)){
                return $value[0];
            }else{
                return $value;
            }
        }
    }

    /**
    * tests purposes.
    */
    private function _copix_ldap_decode_string (& $value, $key) {
        $value = utf8_decode ($value);
    }

    /**
    * gets the entry as an array
    * @return array
    */
    public function asArray () {
        $toReturn = array ();
        foreach (get_object_vars ($this) as $attr=>$value) {
            if ($attr !== '_properties' && $attr !== '_entryID' && $attr != '_rsID' && $attr != 'dn'){
                if (is_array ($value)){
                    if (strtolower ($attr) != 'jpegphoto'){
                        array_walk ($value, '_copix_ldap_encode_string');
                    }
                }else{
                    if (strtolower ($attr) != 'jpegphoto'){
                        $value = _copix_ldap_encode_string($value);
                    }
                }
                $toReturn[$attr] = $value;
            }
        }
         return $toReturn;
    }
}

/**
* Fonction de décodage des éléments chaine de caractère
*/
function _copix_ldap_decode_string (& $value, $key) {
    $value = utf8_decode ($value);
}

/**
* Fonction d'encodage des éléments chaine de caractère
*/
function _copix_ldap_encode_string (& $value) {
    return utf8_encode ($value);
}