<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Goulven CHAMPENOIS
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagVille extends CopixTemplateTag {
	/**
    * Construction du champ
    * @param	mixed	$pParams	tableau de paramètre ou clef
    * @param 	mixed	$pContent	
    * @return 	string	le champ fabriqué (input ou select)
    * 	Paramètres possibles :
    * 		name : pour récupérer la valeur lorsque le formulaire est envoyé
    * 		codePostal : si passé en paramètres, affiche la ville ou liste correspondante
    * 		singleMatchReadOnly : si une seule ville correspond au codepostal, le champ est readonly (et possède la classe readonly)
    * 		et tous les attributs autorisés en HTML. (Compléter la liste pour HTML 5 ou des attributs "custom")
    */
    public function process($pParams) {
        if ( !isset($pParams['name']) ){
	   		throw new CopixTagException ( '[CopixTagVille] Missing name parameter' );
        }
        
        // Get list from service
        if( array_key_exists('codepostal', $pParams) ){
	        $villes = _service( 'alptis|ville::getVilles', array( 'codepostal' => $pParams['codepostal'] ) );
	        unset( $pParams['codepostal'] );
        } else {
        	$villes = array();
        }
        
        if( count( $villes ) > 1 ){
        	$type = 'select';
        	$toReturn  = '<select %s>';
			foreach( $villes as $index => $ville ){
				$toReturn .= '<option value="'.$ville.'">'.$ville.'</option>';
			}
        	$toReturn .= '</select>';
        } else {
        	$type = 'input';
        	if( count( $villes ) == 1 ){
        		$pParams['value'] = $villes[0];
        		if( array_key_exists('singleMatchReadOnly', $pParams) && $pParams['singleMatchReadOnly'] ){
        			$pParams['readonly'] = true;
        			if( is_array( $pParams['class'] ) ){
        				$pParams['class'][] = 'readonly';
        			} else {
        				$pParams['class'] = trim( 'readonly '.$pParams['class'] );
        			}
        		}
        	} else {
        		if (isset ($pParams['value']) && !empty( $pParams['value']) ){
		        	$pParams['value'] = htmlspecialchars( $pParams['value'], ENT_QUOTES );
		        }
        	}
        	$toReturn = '<input type="text" %s />';
        }
        $toReturn = sprintf( $toReturn, $this->_combineAttributes( $pParams, $type ) );
        return $toReturn;
    }
    
    private function _combineAttributes( $params, $type ){
    	$allowedAttributes = array(
        	'input' => array( 'accesskey', 'class', 'dir', 'disabled', 'id', 'maxlength', 'name', 'readonly', 'size', 'value', 'title', 'style', 'lang', 'xml:lang', 'tabindex' ),
        	'select' => array('disabled', 'size', 'multiple', 'class', 'id', 'title', 'style', 'name', 'dir', 'lang', 'xml:lang', 'accesskey', 'tabindex' )
        );
        $toReturn = '';
        foreach ($params as $key => $value) {
        	if( array_search( $key, $allowedAttributes[ $type ] ) !== false ){
        		$toReturn .= $this->_formatAttributes( $key, $value );
        	}
        }
     	return $toReturn;
    }
    
    private function _formatAttributes( $key, $value = '' ){
    	if( $value === '' ){
    		return '';
    	}
    	if( is_array( $value ) ){
    		$value = implode( ' ', $value );
    	}
    	if( in_array( $value, array( 'disabled', 'readonly' ) ) ){
    		$value = $key;
    	}
    	return $key.'="'.$value.'" ';	
    }
}