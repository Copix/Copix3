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
 * Iterateur pour parcourir les résultats des bases de données ainsi que les parcourir comme des tableaux.
 * @package copix
 * @subpackage db 
 */
class CopixDBPDOResultSetIterator implements Iterator, ArrayAccess, Countable {
	/**
	 * Le statement que l'on est en train de lire 
	 */
	private $_statement = null;
	
	/**
	 * Offset maximum s'il est connu
	 */
	private $_maxOffset = null;

	/**
	 * Offset courant en cours de parcours 
	 */
	private $_currentOffset = 0;

	/**
	 * Derniers résultats que l'on a chargé
	 */
	private $_loadedResults = array ();
	
	/**
	 * Dernières clefs que l'on a chargé
	 */
	private $_loadedKeys = array ();

	/**
	 *  Nombre d'éléments que l'on garde en mémoire avant de virer les anciens
	 */
	private $_bufferSize = null;

	/**
	 * Retourne l'élément courant
	 * @return StdClass 
	 */
	function current (){
		return $this->_loadedResults[$this->_currentOffset];
	}

	/**
	 * Récupère un enregistrement à une position donnée
	 * @return StdClass
	 */
	private function _fetch ($pOffset){
		if (in_array ($pOffset, $this->_loadedKeys[false]) || in_array ($pOffset, $this->_loadedKeys[true])){
			return $this->_loadedResults[$pOffset];
		}
		
		return $this->_loadKey ($pOffset);
	}
	
	/**
 	 * Récupère un enregistrement dans une clef donnée
 	 * @return StdClass
	 */
	private function _loadKey ($pKey){
		if ($pKey > $this->_currentOffset){
			$this->rewind ();
		}

		$offset = $this->_currentOffset;

		while ($offset <= $pKey){
			$this->_currentOffset = $offset;
			if (!isset ($this->_loadedResults[$offset])){
				if (($fetched = $this->_statement->fetch ()) === false){
					$this->_maxOffset = $offset-1;
					$this->_currentOffset = $this->_maxOffset;
					break;					
				}else{
					$this->_store ($offset, $fetched, $pKey === $offset);
				}
			}
			$offset++;
		}

		return isset ($this->_loadedResults[$pKey]) ? $this->_loadedResults[$pKey] : false;
	}

	/**
	 * Réduit le buffer de résultat au strict nécessaire
	 */
	private function _trimBuffer (){
		if ((count ($this->_loadedKeys[false]) + count ($this->_loadedKeys[true])) >= $this->_bufferSize){
			if (count ($this->_loadedKeys[false])){
				$toRemove = array_shift ($this->_loadedKeys[false]);
			}else{
				$toRemove = array_shift ($this->_loadedKeys[true]);				
			}
			unset ($this->_loadedResults[$toRemove]);
		}
	}
	
	/**
	 * Stockage de l'élément
	 *
	 * @param int $pOffset			l'offset de l'élément demandé
	 * @param StdClass $pElement	l'élément à sauvegarder dans la pile des résultats
	 * @param boolean	$pAsked		Si c'est l'utilisateur qui a demandé l'élément ou si c'est un fetch automatique (pour affecter un poids au buffer)
	 */
	private function _store ($pOffset, $pElement, $pAsked){
		$this->_trimBuffer ();//on supprime l'élément à remplacer
		array_push ($this->_loadedKeys[$pAsked], $pOffset);
		$this->_loadedResults[$pOffset] = $pElement;
	}

	/**
	 * Construction avec le statement en paramètre
	 * @param	PDOStatement	$pStatement	le statement à parcourir grâce à un itérateur
	 */
	public function __construct ($pStatement, $pBufferSize = 10){
		$this->_statement = $pStatement;
		$this->_statement->setFetchMode(PDO::FETCH_CLASS, 'StdClass');
		$this->_loadedKeys[true] = array ();
		$this->_loadedKeys[false] = array ();
		$this->setBuffer ($pBufferSize);
	}
	
	/**
	 * On libère le statement
	 */
	public function __destruct (){
		$this->_statement->closeCursor ();
	}

	/**
	 * Mise à jour du compteur de position
	 * @return void
	 */
	public function next () {
		$this->_currentOffset++;
	}
	
	/**
	 * Retourne la clef courante
	 * @return int
	 */
	public function key (){
		return $this->_currentOffset;
    }
    
    /**
     * Indique si l'élément courant est valide.
     * @return boolean 
     */
    public function valid (){
    	//Si on connait déja l'offset max, alors on le vérifie.
    	if ($this->_maxOffset !== null){
    		if ($this->_currentOffset > $this->_maxOffset){
    			return false;
    		}
    	}

    	//L'enregistrement courant est valide si l'offset existe.
    	if (isset ($this->_loadedResults[$this->_currentOffset])){
			return true;
    	}
    	
    	//L'enregistrement courant n'est pas chargé, on tente de le récupérer
    	return $this->_fetch ($this->_currentOffset) !== false;
    }
    
    /**
     * Réinitialisation du parcours des éléments au premier indice 
     * @return void
     */
    public function rewind (){
    	if ($this->_currentOffset != 0){
    		$this->_statement->execute ();
	    	$this->_currentOffset = 0;
    	}
    }
    
     /**
	 * Impossibilité de définir des valeurs dans un resultset
	 */
	 function offsetSet ($key, $value) {
	 	throw new CopixDBException ('Cannot set directly in a result set');
	 }
	
	 /**
	  * Retourne l'élément en position donnée. On va vérifier la taille du buffer, on lancera une exception si ça ne va pas.
	  * @param	int	$pKey	la clef que l'on souhaite récupérer.
	  * @return mixed 
      */
	 function offsetGet ($pKey) {
	 	if (! $this->offsetExists ($pKey)){
	 		throw new CopixDBException ('Offset incorrect');	 		
	 	}
	 	
	 	if ($this->_loadKey($pKey)){
	 		return $this->_loadedResults[$pKey]; 
	 	}else{
	 		throw new CopixDBException ('Buffer insuffisant pour récupérer l\'ensemble de résultat '.$pKey);
	 	}
	 }

	 /**
  	  * Defined by ArrayAccess interface
	  * Unset a value by it's key e.g. unset($A['title']);
	  * @param mixed key (string or integer)
	  * @return void
 	  */
	 function offsetUnset ($key) {
	 	throw new CopixDBException ('Impossible de supprimer un élément de l ensemble de résultat');
	 }

	 /**
	 * Defined by ArrayAccess interface
	 * Check value exists, given it's key e.g. isset($A['title'])
	 * @param mixed key (string or integer)
	 * @return boolean
	 */
	 function offsetExists ($pOffset) {
	 	//L'offset existe si < max ou si on arrive à le charger
	 	if ($this->_maxOffset !== null){
			return ($pOffset <= $this->_maxOffset) && ($pOffset >= 0) && ($this->_maxOffset !== -1);
	 	}

	 	if (isset ($this->_loadedResults[$pOffset])){
	 		return true;
	 	}

	 	if ($this->_fetch ($pOffset) !== false){
	 		return true;
	 	}
	 	return false;
	 }

	 /**
	  * Définition de la taille du buffer
	  * @param	int	$pBufferSize	La nouvelle taille du buffer
	  * @return void
	  */
	 private function setBuffer ($pBufferSize){
	 	$this->_bufferSize = $pBufferSize;
	 	$this->_trimBuffer ();
	 }

	 /**
	  * Retourne l'ensemble des résultats sous la forme d'un tableau
	  * @return array
	  */
	 public function fetchAll (){
	 	$toReturn = array ();
	 	foreach ($this as $element){
	 		$toReturn[] = $element;
	 	}
	 	return $toReturn;
	 }
	 
	 public function count (){
	 	return count ($this->fetchAll ());
	 }
}
?>