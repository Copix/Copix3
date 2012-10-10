<?php
/**
* @package	copix
* @subpackage ldap
* @author	Croes Gérald
* @copyright 2001-2006 CopixTeam
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
* explodeDN, reverseDN, compareDN Functions where directly imported from the
*  PHPLdapAdmin Project.
* Tools for LDAP manipulations
* @package copix
* @subpackage ldap
*/
class CopixLDAPTools {
   /**
    * Explode a DN into an array of its RDN parts. This function is UTF-8 safe
    * and replaces the buggy PHP ldap_explode_dn() which does not properly
    * handle UTF-8 DNs and also causes segmentation faults with some inputs.
    *
    * @param string $dn The DN to explode.
    * @param int $with_attriutes (optional) Whether to include attribute names (see http://php.net/ldap_explode_dn for details)
    *
    * @return array An array of RDN parts of this format:
    * <code>
    *   Array
    *    (
    *       [0] => uid=ppratt
    *       [1] => ou=People
    *       [2] => dc=example
    *       [3] => dc=com
    *    )
    * </code>
    */
    public function explodeDn ($dn, $with_attributes=0) {
      // replace "\," with the hexadecimal value for safe split
      $var = preg_replace("/\\\,/","\\\\\\\\2C",$dn);

      // split the dn
      $result = explode(",",$var);

      //translate hex code into ascii for display
      foreach( $result as $key => $value )
        $result[$key] = preg_replace("/\\\([0-9A-Fa-f]{2})/e", "''.chr(hexdec('\\1')).''", $value);

      return $result;
    }

    /**
     * Reverses a DN such that the top-level RDN is first and the bottom-level RDN is last
     * For example:
     * <code>
     *   cn=Brigham,ou=People,dc=example,dc=com
     * </code>
     * Becomes:
     * <code>
     *   dc=com,dc=example,ou=People,cn=Brigham
     * </code>
     * This makes it possible to sort lists of DNs such that they are grouped by container.
     *
     * @param string $dn The DN to reverse
     *
     * @return string The reversed DN
     *
     * @see pla_compare_dns
     */
    public function reverseDn($dn) {
    	foreach ($this->explodeDn ($dn) as $key => $branch) {

    		// pla_expode_dn returns the array with an extra count attribute, we can ignore that.
    		if ( $key === "count" ) continue;

    		if (isset($rev)) {
    			$rev = $branch.",".$rev;
    		} else {
    			$rev = $branch;
    		}
    	}
    	return $rev;
    }

    /**
     * Compares 2 DNs. If they are equivelant, returns 0, otherwise,
     * returns their sorting order (similar to strcmp()):
     *      Returns < 0 if dn1 is less than dn2.
     *      Returns > 0 if dn1 is greater than dn2.
     *
     * The comparison is performed starting with the top-most element
     * of the DN. Thus, the following list:
     *    <code>
     *       ou=people,dc=example,dc=com
     *       cn=Admin,ou=People,dc=example,dc=com
     *       cn=Joe,ou=people,dc=example,dc=com
     *       dc=example,dc=com
     *       cn=Fred,ou=people,dc=example,dc=org
     *       cn=Dave,ou=people,dc=example,dc=org
     *    </code>
     * Will be sorted thus using usort( $list, "pla_compare_dns" ):
     *    <code>
     *       dc=com
     *       dc=example,dc=com
     *       ou=people,dc=example,dc=com
     *       cn=Admin,ou=People,dc=example,dc=com
     *       cn=Joe,ou=people,dc=example,dc=com
     *       cn=Dave,ou=people,dc=example,dc=org
     *       cn=Fred,ou=people,dc=example,dc=org
     *    </code>
     *
     * @param string $dn1 The first of two DNs to compare
     * @param string $dn2 The second of two DNs to compare
     * @return int
     */
    public function compareDn ( $dn1, $dn2 ){
        // If they are obviously the same, return immediately
        if( 0 === strcasecmp( $dn1, $dn2 ) )
        	return 0;

        $dn1_parts = $this->explodeDn ( $this->reverseDn($dn1) );
        $dn2_parts = $this->explodeDn ( $this->reverseDn($dn2) );
        assert( is_array( $dn1_parts ) );
        assert( is_array( $dn2_parts ) );

        // Foreach of the "parts" of the smaller DN
        for( $i=0; $i<count( $dn1_parts ) && $i<count( $dn2_parts ); $i++ ) {
        	// dnX_part is of the form: "cn=joe" or "cn = joe" or "dc=example"
        	// ie, one part of a multi-part DN.
        	$dn1_part = $dn1_parts[$i];
        	$dn2_part = $dn2_parts[$i];

        	// Each "part" consists of two sub-parts:
        	//   1. the attribute (ie, "cn" or "o")
        	//   2. the value (ie, "joe" or "example")
        	$dn1_sub_parts = explode( '=', $dn1_part, 2 );
        	$dn2_sub_parts = explode( '=', $dn2_part, 2 );

        	$dn1_sub_part_attr = trim( $dn1_sub_parts[0] );
        	$dn2_sub_part_attr = trim( $dn2_sub_parts[0] );
        	if( 0 != ( $cmp = strcasecmp( $dn1_sub_part_attr, $dn2_sub_part_attr ) ) )
        		return $cmp;

        	$dn1_sub_part_val = trim( $dn1_sub_parts[1] );
        	$dn2_sub_part_val = trim( $dn2_sub_parts[1] );
        	if( 0 != ( $cmp = strcasecmp( $dn1_sub_part_val, $dn2_sub_part_val ) ) )
        		return $cmp;
        }

        // If we iterated through all entries in the smaller of the two DNs
        // (ie, the one with fewer parts), and the entries are different sized,
        // then, the smaller of the two must be "less than" than the larger.
        if( count($dn1_parts) > count($dn2_parts) ) {
            return 1;
        } elseif( count( $dn2_parts ) > count( $dn1_parts ) ) {
            return -1;
        } else {
            return 0;
        }
    }
    
    /**
    * Says if the given dn belongs to the other dn
    */
    public function dnBelongsTo ($dn, $dnFather){
        $dn_parts = $this->explodeDn ( $this->reverseDn($dn) );
        $dnFather_parts = $this->explodeDn ( $this->reverseDn($dnFather) );
        
        //the child must have more elements than its father
        if (count ($dn_parts) < count ($dnFather_parts)){
           return false;
        }
        
        //hashTable for dnParts attributes
        $hashDn = array ();
        foreach ($dn_parts as $parts){
           $elem = explode ('=', $parts);
           $hashDn[$elem[0]][] = $elem[1];
        }

        //go throw the father parts elements to test if they exist in the child
        foreach ($dnFather_parts as $parts) {
           $elem = explode ('=', $parts);
           //if the curent attribute does not exists in the child, false
           if (!isset ($hashDn[$elem[0]])){
              return false;
           }
           
           //if the curent attribute value does not exists in the child, return false
           if (!in_array ($elem[1], $hashDn[$elem[0]])){
              return false;
           }
        }

        //seems ok
        return true;
    }
}
?>