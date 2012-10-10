<?php
/**
* @package		tools 
 * @subpackage	wikirender
 * @author	Patrice Ferlet
 * @copyright 2001-2006 CopixTeam
 * @link      http://copix.org
 * @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
* @package		tools 
 * @subpackage	wikirender
 */
class MathRender{

    public $mathcode ="";

    public function __construct ($code){
        $this->mathcode=$code;
    }

    function wrap () {
        $code = str_replace("\n","",$this->mathcode);
        $code = str_replace("\r","",$code);
        return <<< EOF
\documentclass[10pt]{article}
% add additional packages here
\usepackage{amsmath}
\usepackage{amsfonts}
\usepackage{amssymb}
\usepackage{pst-plot}
\usepackage{color}
\pagestyle{empty}
\begin{document}
\begin{displaymath}
$code
\end{displaymath}
\end{document}
EOF;
    }

    function render(){
        $path = COPIX_CACHE_PATH."/math/";
        @mkdir($path);
        $md5 = md5($this->mathcode);
        $file = $md5.".png";
        if(!file_exists($path.$file)){
            $this->_render($file);
        }
        return $file;
    }

    function _render($file){
        $thunk=$this->mathcode;
        $unik = uniqid("hash");
        $tex = $unik.".tex";
        $dvi = $unik.".dvi";
        $ps = $unik.".ps";
        $current = getcwd ();
        chdir(COPIX_CACHE_PATH."/math/");
        $thunk = $this->wrap();
        //print_r($thunk);
        $fp = fopen("$tex","w+");
        fputs($fp,$thunk);
        fclose($fp);
        $command="latex --interaction=nonstopmode $tex";
        //print_r($command);
        exec($command);
        $command = "dvips -E $dvi -o $ps";
        //print_r($command);
        exec($command);
        $command="convert -density 120 $ps $file";
        //print_r($command);
        exec($command);
        /*unlink($tex);
         unlink($dvi);
         unlink($ps);*/
        chdir ($current);
    }

}
?>