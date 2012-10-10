<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Favre Brice, Croes Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
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
     * Constante définissant un iterateur ave entête 
     */
    const HEADED = true;

    /**
     * Constante définissant un iterateur sans entête 
     */
    const NUMBERED = false;
    
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
     * @param string $pFilename nom du fichier à parcourir
     * @param string $pDelimiter le délimiteur des champs du fichier
     * @param string $pEnclosure s'il existe un caractère d'encapsulation des champs (" par défaut)
     * @param boolean $pArrayHead si la première ligne correspond aux en têtes
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
    public function getIterator ($pIsHeaded = CopixCsv::NUMBERED){
        return new CopixCsvIterator($this->_filename, $this->_delimiter, $this->_enclosure, $pIsHeaded);
    }

    /**
     * Fonction d'ajout de ligne à un fichier CSV
     * @param array $arParams
     */
    function addLine ($arParams){
        $_dirname = dirname ($this->_filename);
        Copixfile::createDir ($_dirname);
		if ($fd = @ fopen ($this->_filename, "a")){
			fputcsv ($fd, $arParams, $this->_delimiter, $this->_enclosure);
			$this->_nblines++;
			fclose ($fd);
			return true;
		}else{
			return false;
		}
    }
}