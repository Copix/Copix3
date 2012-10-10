<?php
/**
* @package		copix
* @subpackage	core
* @author		Croës Gérald
* @copyright	CopixTeam
* @link 		http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Classe de gestion des contextes de l'application.
* Nous allons gérer le fait des entrées sorties dans les différents modules.
* Correction du problème "module|name".
* @package copix
* @subpackage core
*/
class CopixContext {
    /**
    * Pile de gestion des contextes. Cette propriété n'est plus utilisée 
    * @var array
    */
    private static $_contextStack = array ();

    /**
    * Empilement d'un contexte.
    * <code>
    *    //on récupère une classe d'un module X ou Y
    *    $object = CopixClassesFactory::create ('moduleY|ClasseExemple');
    *    //cette classe utilise d'autres sous classes, en considérant qu'elle est exécutée
    *    //dans le module pour lequel elle a été écrite.
    *    //On va donc forcer le contexte d'exécution
    *    CopixContext::push ('moduleY');
    *    $object->doStuff ();
    *    //On rétabli le contexte d'exécution
    *    CopixContext::pop (); 
    * </code>
    * 
    * @param	string	$pModule  le nom du module dont on empile le contexte
    */
    public static function push ($pModule){
        CopixContext::$_contextStack[] = $pModule;
    }

    /**
    * Dépilement d'un contexte.
    * @return	string	élement dépilé. (le contexte qui n'est plus d'atualité.)
    */
    public static function pop (){
       return ($value = array_pop (CopixContext::$_contextStack)) === null ? 'default' : $value;
    }

    /**
    * Récupère le contexte actuel
    * <code>
    *    echo "Le code suivant s'exécute dans le module ".CopixContext::get ();
    * </code>
    * 
    * @return string le nom du contexte actuel si défini, si pas de contexte retourne default
    */
    public static function get (){
        return (($last = (count (CopixContext::$_contextStack)-1)) >= 0) ? CopixContext::$_contextStack[$last] : "default";
    }

    /**
    * Réinitialise le contexte.
    * 
    * Il existe très peu de cas ou vous devrez vous même appeler cette méthode.
    * Cette méthode existe principalement pour permettre à CopixController de manipuler
    * la pile de contexte complète
    */
    public static function clear (){
        CopixContext::$_contextStack = array ();
    }
}
?>