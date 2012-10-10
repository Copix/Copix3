<?php
/**
* @package		copix
* @subpackage	core
* @author		Croes Gérald, Jouanneau Laurent
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Permet de manipuler l'en tête de la sortie HTML au développeur.
* CopixHTMLHeader placera ses informations dans la variable du template principal nommé
* {$HTML_HEAD}
* @package copix
* @subpackage core
*/
class CopixHTMLHeader {
    /**
    * Tableau de liens sur des feuilles de style
    * @var array
    */
	private static $_CSSLink = array ();
	/**
	* Styles CSS définis
	* @var array
	*/
    private static $_Styles  = array ();
    /**
    * Tableau de liens sur des fichiers javascript à inclure
    * @var array
    */
    private static $_JSLink  = array ();
    /**
    * Array of js code
    */
    private static $_JSCode  = array ();
    /**
    * Array with more content
    */
    private static $_Others  = array ();

    /**
    * Ajoute un lien vers un fichier Javascript. N'ajoutera pas deux fois un même lien 
    * @param string $src le chemin vers le javascript (tel qu'il apparaitra)
    * @param array $params tableau de paramètres suppélemntaires à ajouter à l'inclusion du fichier
    */
    public static function addJSLink ($src, $params=array()){
        if (! isset (self::$_JSLink[$src])){
            self::$_JSLink[$src] = $params;
        }
    }
    
    /**
    * Ajoute un lien vers un fichier CSS. N'ajoutera pas deux fois le même lien
    * @param string $src le chemin vers le fichier CSS (tel qu'il apparaitra)
    * @param array $params tableau de paramètres suppélmentaires à ajouter dans l'inclusion du fichier
    */
    public static function addCSSLink ($src, $params=array ()){
        if (!isset (self::$_CSSLink[$src])){
            self::$_CSSLink[$src] = $params;
        }
    }

    /**
    * Ajoute la définition d'un style CSS
    * @param string $selector le nom du sélecteur que l'on souhaites définir
    * @param string $def la définition complète u style que l'on souhaites 
    *    définir tel qu'il apparaitra dans la feuille de style)
    *  Si $def vaut null, alors on considère que $selector contient en fait un ensemble de 
    *  style valides
    */
    public static function addStyle ($selector, $def = null){
        if (!isset (self::$_Styles[$selector])){
            self::$_Styles[$selector] = $def;
        }
    }

    /**
    * Ajoute d'autres élements au code HTML d'en tête
    * @param string $content le contenu que l'on souhaite rajouter
    * @param string $key la clef pour identifier la chaine ajoutée  
    */
    public static function addOthers ($content, $key = null){
        if ($key === null){
    		self::$_Others[] = $content;
        }else{
    		self::$_Others[$key] = $content;
        }
    }

    /**
    * Ajoute du javascript dans le header
    * @param string $code  la chaine à rajouter
    * @param string $key la clef pour identifier la chaine javascript
    */
    public static function addJSCode ($code, $key = null){
        if ($key === null){
            self::$_JSCode[] = $code;
        }else{
            self::$_JSCode[$key] = $code;
        }
    }

    /**
    * récupère le contenu à rajouter dans l'en tête
    * @return string
    */
    public static function get (){
        return self::getCSSLink () . "\n\r" . self::getJSLink () . "\n\r" . self::getStyles ()."\n\r" . self::getJSCode ().self::getOthers ();
    }

    /**
    * Récupération de la partie d'en tête "autres"
    * @return string
    */
    public static function getOthers (){
        return implode ("\n\r", self::$_Others);
    }
    
    /**
    * Récupération du code javascript ajouté
    * @return string <head> HTML Content
    */
    public static function getJSCode (){
        if(($js= implode ("\n", self::$_JSCode)) != ''){
        return '<script type="text/javascript">
// <![CDATA[
 '.$js.'
// ]]>
</script>';
        }
        return '';
    }
    
    /**
    * Récupération des styles ajoutés à l'en tête
    * @return string <head> Contenu HTML
    */
    public static function getStyles (){
        $built = array ();
        foreach (self::$_Styles as $selector=>$value){
            if (strlen (trim($value))){
                //il y a une paire clef valeur.
                $built[] = $selector.' {'.$value.'}';
            }else{
                //il n'y a pas de valeur, c'est peut être simplement une commande.
                //par exemple @import qqchose, ...
                $built[] = $selector;
            }
        }
        if(($css=implode ("\n", $built)) != ''){
        return '<style type="text/css"><!--
         '.$css.'
         //--></style>';
        }
    }

    /**
    * Récupération des liens vers les feuilles de styles 
    * @return string <head> Contenu HTML
    */
    public static function getCSSLink (){
        $built = array ();
        foreach (self::$_CSSLink as $src=>$params){
            //the extra params we may found in there.
            $more = '';
            foreach ($params as $param_name=>$param_value){
                $more .= $param_name.'="'.$param_value.'" ';
            }
            $built[] = '<link rel="stylesheet" type="text/css" href="'.$src.'" '.$more.' />';
        }
        return implode ("\n\r", $built);
    }

    /**
    * Récupération des liens vers les fichiers javascript
    * @return string <head> En tête HTML
    */
    public static function getJSLink (){
        $built = array ();
        foreach (self::$_JSLink as $src=>$params){
            //the extra params we may found in there.
            $more = '';
            foreach ($params as $param_name=>$param_value){
                $more .= $param_name.'="'.$param_value.'" ';
            }
            $built[] = '<script type="text/javascript" src="'.$src.'" '.$more.'></script>';
        }
        return implode ("\n\r", $built);
    }

    /**
    * supression de tous les éléments définis dans l'en tête HTML
    * @return void
    */
    public static function clear ($what){
        $cleanable = array ('CSSLink', 'Styles', 'JSLink', 'JSCode', 'Others');
        foreach ($what as $elem){
            if (in_array ($elem, $cleanable)){
                $name = '_'.$elem;
                self::$$name = array ();
            }
        }
    }
    
    /**
    * Ajout d'une icone "favicone"
    * @param string $pPicturePath le chemin de l'image
    */
    public static function addFavIcon ($pPicturePath){
    	self::addOthers ('<link rel="icon" href="'.$pPicturePath.'" />');
    }
}
?>