<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Favre Brice, Croes Gérald, Jouanneau Laurent
 * @copyright	2001-2007 CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe pour manipuler des fichiers CSV
 *
 * @package copix
 * @subpackage utils
 */
class CopixCsv {

    /**
     * Nom du fichier CSV
     */
    private $_filename;

    /**
     * Délimiteur de champs 
     */
    private $_delimiter = ',';

    /**
     * Délimiteur de chaine de caractères
     */
    private $_enclosure = '"';


    /**
     * Nombre de lignes du fichier
     */
    private $_nblines ;

    /**
     * Constructeur de classe
     *
     * @param string $pFilename
     * @param string $pDelimiter
     * @param string $pEnclosure
     */
    public function __construct ($pFileName, $pDelimiter=',', $pEnclosure = '"'){
        $this->_filename = $pFileName;
        $this->_delimiter = $pDelimiter;
        $this->_enclosure = $pEnclosure;
    }

    /**
     * Récupération de l'iterateur CSV sur le fichier
     *
     */
    public function getIterator (){
        return new CopixCsvIterator($this->_filename, $this->_delimiter, $this->_enclosure);
    }

    /**
     * Fonction d'ajout de ligne à un fichier CSV
     * @param array $arParams
     */
    function addLine ($arParams){
        $_dirname = dirname($this->_filename);
        // On teste l'existence du répertoire contenant le fichier
        if (Copixfile::createDir($_dirname)) {
            if ($fd = @ fopen ($this->_filename, "a")){
                fputcsv ($fd, $arParams, $this->_delimiter, $this->_enclosure);
                $this->_nblines++;
                fclose ($fd);
            }else{
                return false;
            }
        } else {
            trigger_error(CopixI18N::get ("copix:copix.error.cache.creatingDirectory", array ($_dirname)));
            return false;
        }
    }

}

/**
 * Classe Iterateur de parcours de fichiers CSV
 */
class CopixCsvIterator extends LimitIterator implements Countable {

    protected $_data;
    protected $_current;
    protected $_filehandler;
    protected $_counter;
    protected $_delimiter;
    protected $_enclosure;
    const ROW_SIZE = 4096;


    function __construct ($pFile, $pDelimiter, $pEnclosure){
        // $this->_data = array();
        $this->_filehandler = fopen ($pFile,'r');
        $this->_delimiter = $pDelimiter;
        $this->_enclosure = $pEnclosure;
        $this->_current = fgetcsv($this->_filehandler, self::ROW_SIZE, $this->_delimiter, $this->_enclosure);
        $this->_counter = 0;
    }

    function current (){
        return $this->_current;
        /*$this->_current = fgetcsv($this->_filehandler, self::ROW_SIZE, $this->_delimiter, $this->_enclosure);
        $this->_counter++;
        return $this->_current;*/
    }

    function key (){
        return $this->_counter;
    }

    function next (){
        $this->_current = fgetcsv($this->_filehandler, self::ROW_SIZE, $this->_delimiter, $this->_enclosure);
        if ($this->_current !== false) {
            $this->_counter++;
        }
        return $this->_current;
        
        /*return !feof( $this->_filehandler );*/
    }

    function rewind (){
        $this->_counter = 0;
        rewind ($this->_filehandler);
        $this->_current = null; 
        // fgetcsv($this->_filehandler, self::ROW_SIZE, $this->_delimiter, $this->_enclosure);
    }

    function valid (){
        if ( ! $this->current() ) {
            return FALSE;
        }
        return TRUE;
    }

    function seek ($position){
        if ($position == 0) {
            $this->rewind();
        } else {
            if ($position < $this->_counter) {
                $this->rewind();
            }
            while ($this->next()) {
                if ($this->_counter == $position) {
                    break;
                }
            }
        }
    }
    
    function __destruct (){
        fclose ($this->_filehandler);
    }

    function count() {
        $this->rewind();
        while ($this->next()) {
            continue;
        }
        return $this->_counter;
    }
}

/* Classe obsolète
class CopixCsvReverseIterator extends CopixCsvIterator  {

    protected $_currentpos = 0;

    function __construct($pFile, $pDelimiter, $pEnclosure) {
         
        parent::__construct($pFile, $pDelimiter, $pEnclosure);
        fseek($this->_filehandler, $this->_currentpos, SEEK_END);
        // $this->_data = array_reverse($this->_data);
    }

    private function _rewindLine () {
        while ($car != "\n") {
            if (!fseek ($fp, $this->_currentpos, SEEK_END)) {
                $car = fgetc($this->_filehandler);
                $this->_currentpos--;
            } else {
                break;
            }
        }
    }

    function current() {
        $this->_rewindLine();
        $this->_current = fgetcsv($this->_filehandler, self::ROW_SIZE, $this->_delimiter, $this->_enclosure);
        $this->_counter++;
        return $this->_current;
    }

    function rewind() {
        $this->_counter = 0;
        $this->_currentpost = 0;
        fseek ($this->_filehandler, $this->_currentpos, SEEK_END);
         
    }

    function next() {
        return fseek( $this->_filehandler, $this->_currentpos, SEEK_END);
    }

    function valid() {
        if ( ! $this->next() ) {
            fclose ($this->_filehandler);
            return FALSE;
        }
        return TRUE;
        /*if (fseek ($fp, $this->_currentpos, SEEK_END)) {
         return TRUE;
         } else {
         fclose ($this->_filehandler);
         return FALSE ;
         }
         
    }

}*/

?>