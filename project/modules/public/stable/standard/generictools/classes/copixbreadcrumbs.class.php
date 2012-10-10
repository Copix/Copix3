<?php
/** 
* @package		standard 
 * @subpackage	generictools
* @version    1.0
* @author     Nicolas CHARLOT, nicolas.charlot@isics.fr
* @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Element du fil d'ariane (lien)
* @package		standard 
 * @subpackage	generictools
*/
class CopixBreadcrumbsItem {
    /**
    * Lien
    * @var url
    * @access public
    */
    public $link;
    
    /**
    * Texte de lien
    * @var string
    * @access public
    */
    public $text;
    
    /**
    * Constructeur
    * @param url $link lien
    * @param string $text texte du lien
    */
    public function __construct ($link, $text){
        $this->link = $link;
        $this->text = $text;
    }
    
}
    
/**
* Gestion d'un fil d'ariane
* @package generictools
*/
class CopixBreadcrumbs {
    
    /**
    * Tableau des liens du fil
    * @var array
    * @access private
    */
    private $_arBreadcrumbsItems;
    
    /**
    * Constructeur
    */  
    public function __construct (){
        $this->_init ();
    }
    
    /**
    * Méthode privée initialisation du fil d'ariane
    */    
    function _init (){
        $this->_arBreadcrumbsItems = array ();
    }
    
    /**
    * Méthode public permettant l'ajout d'un nouveau lien au fil en début ou en fin de fil (défaut)
    * @param url $link Le lien a insérer
    * @param string $text Le texte du lien à insérer
    * @param boolean $unshift Si true, insertion en début de fil ou en fin (défaut)
    */
    function addItem ($link, $text, $unshift = false){
        $unshift ? array_unshift ($this->_arBreadcrumbsItems, new CopixBreadcrumbsItem ($link, $text))
                 : array_push ($this->_arBreadcrumbsItems, new CopixBreadcrumbsItem ($link, $text));
    }
    
    /**
    * Méthode public vidant le tableau
    */       
    function clear () {
        $this->_init ();
    }
    
    /**
    * Méthode public testant l'existance d'un lien dans le fil
    * @param url $link Lien à tester
    * @return int la clé du lien dans le tableau ou false si le lien n'existe pas
    */       
    function exists ($link){
        foreach ($this->_arBreadcrumbsItems as $key => $bcItem){
            if ($link == $bcItem->link){
                return $key;
            }
        }
        return false;
    }
    
    /**
    * Méthode public permettant la mise un jour du texte d'un lien
    * @param url $link Lien à mettre à jour
    * @param string $text Nouveau texte
    * @return boolean Le lien a été trouvé (true) ou non (false).
    */   
    function update ($link, $text){
        if ($key = $this->exists($link)){
            $bcItem = $this->_arBreadcrumbsItems[$key];
            $bcItem->text = $text;
            return true;
        }else{
            return false;
        }
    }
    
    /**
    * Méthode public retournant le tableau fil d'ariane à partir de l'offset from
    * @param int $from Si from est positif, la méthode retournera les éléments à partir de cet offset.
    *                  Si from est négatif, la méthode retournera les éléments à partir de cet offset mais en commençant à la fin du tableau
    *                  Par défaut from = 0
    */
    function getItems ($from = 0){
        return array_slice ($this->_arBreadcrumbsItems, $from);
    }
}
?>