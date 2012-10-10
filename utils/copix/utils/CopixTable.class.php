<?php
/**
 * @package		copix
 * @subpackage	utils
 * @experimental
 * @author		Salleyron Julien
 * @copyright	2006-2007 CopixTeam
 * @link		http://copix.org
 * @license 	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de création de tableau (HTML ou Excel)
 *
 * @package copix
 * @subpackage utils
 */
class CopixTable {

    // Le tableau des données
    private $_array=array();

    // Tableau contenant le mappage des objets
    private $_mapObject=array();

    // Tableau de titre
    private $_title = array();

    // Tableau contenant la taille de chaque colonne
    private $_size = array();

    // Taille par défaut
    private $_defaultSize=100;

    // Tableau d'alternate pour les lignes HTML
    private $_alternate=array();

    // Tableau de paramètre du tableau html (optionnel)
    private $_htmlProperties=array();

    // htmlentities ??
    private $_htmlentities=true;
    /**
     * Setteur de $_array
     *
     * @param array $pArray le tableau a attribuer
     */
    function setArray($pArray) {
        $this->_array=$pArray;
    }

    /**
     * Setteur de $_title
     *
     * @param array $pTitle Le tableau de titre
     */
    function setTitle($pTitle) {
        $this->_title = $pTitle;
    }

    /**
     * Setteur de $_mapObject
     *
     * @param array $pMap Le tableau de mapping
     */
    function setMapping($pMap) {
        $this->_mapObject = $pMap;
    }

    /**
     * Setteur de $_size
     *
     * @param array $pSize Le tableau de taille
     */
    function setSize($pSize) {
        $this->_size= $pSize;
    }

    /**
     * Setteur de $_alternaye
     *
     * @param array $pAlternate Le tableau d'alternate
     */
    function setAlternate($pAlternate) {
        $this->_alternate=$pAlternate;
    }

    /**
     * Setteur de $_htmlProperties
     *
     * @param array $pHtmlProperties Tableau de propriétés
     */
    function setHtmlProperties($pHtmlProperties) {
        $this->_htmlProperties=$pHtmlProperties;
    }

    /**
     * Setteur de htmlentities
     * Si on veux que le htmlentities se fasse en auto, mettre true
     *
     * @param bool $pHtmlEntities
     */
    function setHtmlEntities($pHtmlEntities) {
        $this->_htmlEntities=$pHtmlEntities;
    }

    /**
     * Retourne le tableau au format HTML
     *
     */
    function getHTML() {

        //entete du tableau avec ces propriétés
        $toReturn="<table";
        if (count($this->_htmlProperties)>0) {
            foreach ($this->_htmlProperties as $key=>$value)
            $toReturn.=" $key=\"$value\"";
        }
        $toReturn.=">";

        //Titre du tableau (avec des balises th)
        if (count($this->_title)>0) {
            $toReturn.="<tr>";
	        foreach($this->_title as $titre) {
	            $nbsp="";
	            if (substr($titre,strlen($titre)-1,1)=="\n") {
	                $nbsp="&nbsp;";
	            }
	            if ($this->_htmlEntities) {
	                $toReturn.="<th>".nl2br(htmlentities(utf8_decode($titre))).$nbsp."</th>";
	            }else{
	                $toReturn.="<th>".$titre."</th>";
	            }
	        }
	        $toReturn.="</tr>";
        }


        //Ligne du tableau avec gestion des alternates
        foreach ($this->_array as $key=>$line) {
            $strAlternate="";
            if (count($this->_alternate)>0) {
                if (count($this->_alternate)==1) {
                    if ($key%2==0) {
                        $strAlternate=" id=\"".$this->_alternate[0]."\"";
                    }
                } else {
	                $tempKey=$key%count($this->_alternate);
	                $strAlternate=" id=\"".$this->_alternate[$tempKey]."\"";
                }
            }
            $toReturn.="<tr$strAlternate>";
            if (count($this->_mapObject)>0) {
                foreach ($this->_mapObject as $map) {
                    if ($this->_htmlEntities) {
                        $toReturn.="<td>".nl2br(htmlentities(utf8_decode($line->$map)))."</td>";
                    } else {
                        $toReturn.="<td>".$line->$map."</td>";
                    }
                }
            } else {
	            foreach ($line as $cell) {
	                    if ($this->_htmlEntities) {
	                        $toReturn.="<td>".nl2br(htmlentities(utf8_decode($cell)))."</td>";
	                    }else{
	                        $toReturn.="<td>".$cell."</td>";
	                    }
	            }
            }
            $toReturn.="</tr>";
        }

        //Fin du tableau
        $toReturn.="</table>";
        return $toReturn;
    }

    /**
     * Retourne le tableau au format excel
     *
     */
    function getExcel($format = 'default') {
        //Inclusion de la classe pour gnération des fichiers excel
        
        if ($format == 'default') {
            require_once(COPIX_PATH.'../excelwriter/excelwriter.inc.php');
            $excel = new ExcelWriter("temp.xls", "", "");
            if ($excel == false)
                throw new Exception($excel->error);
        } else {
            $excel  = "ID;PWXL;N;E\r\nP;FArial;M200\r\nP;FArial;M200\r\nP;FArial;M200\r\nP;FArial;M200;SB\r\n";
            
            if ($this->_size != null) {
                foreach ($this->_size as $key=>$size) {
                    $excel .= "F;W".($key+1)." ".($key+1)." ".round(($size/6))."\r\n";
                }
            }
        }
        
            $lineNum = 1;
        
        //Création de la ligne de titre
        if (count($this->_title)>0) {
            $tabTitle=array();
            $col = 1;
	        foreach($this->_title as $key=>$titre) {
                    if ($format == 'default') {
                        $tabTitle[utf8_decode($titre)]= (isset($this->_size[$key])) ? $this->_size[$key] : $this->_defaultSize;
                    } else {
                        $excel  .= "F;X$col;Y$lineNum;SDM3\r\n";
                        $excel .= "C;X$col;Y$lineNum;K\"".utf8_decode($titre)."\"\r\n";
                    }
                    $col++;
	        }
                if ($format == 'default') {
                    $excel->writeLine($tabTitle, "gras");
                }
                $lineNum++;
	    }

	    //Création des lignes de données
        foreach ($this->_array as $line) {
            if ($format == 'default') {
                $excel->writeRow();
            }
            if (count($this->_mapObject)>0) {
                $col = 1;
                foreach ($this->_mapObject as $key=>$map) {
                    $size=(isset($this->_size[$key])) ? $this->_size[$key] : $this->_defaultSize;
                    if ($format == 'default') {
                        $excel->writeCol(utf8_decode($line->$map), $size);
                    } else {
                        $data = $line->$map;
                        if (!is_numeric($data)) {
                            $data = str_replace(';', ';;', $data);
                            $data = str_replace("\r\n", ' :',$data);
                            $data = '"'.$data.'"';
                        }
                        $excel .= "C;X$col;Y$lineNum;K".utf8_decode($data)."\r\n";
                    }
                    $col++;
                }
            } else {
                $col = 1;
	            foreach ($line as $key=>$cell) {
                        $size=(isset($this->_size[$key])) ? $this->_size[$key] : $this->_defaultSize;
                        if ($format == 'default') {
                            $excel->writeCol(utf8_decode($cell), $size);
                        } else {
                            $data = $cell;
                            if (!is_numeric($data)) {
                                $data = '"'.$data.'"';
                            }
                            $excel .= "C;X$col;Y$lineNum;K".utf8_decode($data)."\r\n";
                        }
                        $col++;
	            }
            }
            $lineNum++;
        }

        if ($format == 'default') {
            //Fin du document
            $excel->close();
            return $excel->getData();
        } else {
            $excel .= "E";
            return $excel;
        }

    }

}
?>