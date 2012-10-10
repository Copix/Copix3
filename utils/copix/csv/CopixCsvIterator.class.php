<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Favre Brice, Croes Gérald
 * @copyright	2001-2007 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe Iterateur de parcours de fichiers CSV
 * @package copix
 * @subpackage utils
 */
class CopixCsvIterator extends LimitIterator implements Countable {

    protected $_data;
    protected $_current;
    protected $_filehandler;
    protected $_counter;
    protected $_delimiter;
    protected $_enclosure;
    protected $_keys = null;
    protected $_headed;
    const ROW_SIZE = 4096;
    
    protected $_filename = null;

    /**
     * Constructeur
     *
     * @param string  $pFileName  Le nom du fichier CSV a parcourir
     * @param string  $pDelimiter La chaine qui représente le délimiteur
     * @param string  $pEnclosure La chaine qui représente les caractères qui entourent les champs du fichier
     * @param boolean $pIsHeaded  Si la première ligne contient les noms des colonnes ou non
     */
    function __construct ($pFile, $pDelimiter, $pEnclosure, $pIsHeaded){
    	$this->_filename = $pFile;
        $this->_filehandler = fopen ($pFile,'r');

        $this->_delimiter = $pDelimiter;
        $this->_enclosure = $pEnclosure;
        $this->_headed = $pIsHeaded;
        $this->rewind ();
    }
    
    /**
     * Destructeur 
     * 
     * Fermeture du fichier
     */
    function __destruct (){
        fclose ($this->_filehandler);
    }
        
    /**
     * Lecture d'une ligne et retour des éléments dans un tableau
     * @param  boolean  $pWithKeys indique s'il faut prendre en compte les clefs lors de la lecture ou non
     * @return array
     */
    protected function _readLine ($pWithKeys = false){
		if ($pWithKeys === false || $this->_keys === null) {
			return fgetcsv ($this->_filehandler, self::ROW_SIZE, $this->_delimiter, $this->_enclosure);			
		}
		if ($readed = $this->_readLine (false)){
			return array_combine ($this->_keys, $readed);	
		}
		return false;
    }

    /**
     * Retourne l'élément courant
     * @return array
     */
    function current (){
        return $this->_current;
    }

    /**
     * Retourne la position courante dans le fichier (la position de l'enregistrement ligne)
     *
     * @return int
     */
    function key (){
        return $this->_counter;
    }

    /**
     * Déplace le pointeur interne sur la ligne suivante
     *
     * @return boolean	false s'il n'existe plus de ligne, $this->current sinon
     */
    function next (){
        if ($this->_current = $this->_readLine (true)){
        	$this->_counter++;
        }
        return $this->_current;
    }

    /**
     * Retourne sur le premier élément du fichier
     */
    function rewind (){
		rewind( $this->_filehandler );
    	if ($this->_headed === CopixCSV::HEADED) {
	        $this->_keys = $this->_readLine (false);
        }
        $this->_counter = -1;
        $this->next ();
    }

    /**
     * Indique si la position courante est valide
     *
     * @return boolean
     */
    function valid (){
    	return $this->_current !== false;
    }

    function seek ($position){
        if ($position == 0) {
            $this->rewind ();
        } else {
            if ($position < $this->_counter) {
                $this->rewind ();
            }
            while ($this->next ()) {
                if ($this->_counter == $position) {
                    break;
                }
            }
        }
    }

    /**
     * Compte le nombre d'éléments dans le fichier CSV (les en tête ne comptent pas pour 1)
     *
     * @return int
     */
    function count () {
    	//@todo pour les fichiers peu volumineux, un return count (file ($this->_filename)); est plus rapide
    	$file = fopen ($this->_filename,'r');
    	$count = 0;
		while (fgets ($file)){
			$count++;
		}
		fclose ($file);
    	if ($this->_headed == false){
			return $count;
    	}
    	//N'est pas un fichier avec en tête
    	if ($count > 0){
    		return $count - 1;
    	}
    	return 0;
    }
}