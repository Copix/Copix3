<?php
/**
* @package	cms
* @author	Croes Gérald, see copix.org for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package cms
* Classe de base pour les portlets
*/
class Portlet {
    /**
    * L'identifiant de la porlet
    * @var string
    */
    var $id = null;

    /**
    * Page à laquelle la portlet appartient
    * @var CMSPage
    */
    var $_page = null;

    /**
    * Paramètres assignés à la portlet
    * @var array
    */
    var $params = array ();
    
    /**
    * The template id we're using for the portlet
    */
    var $templateId = null;

    /**
    * Constructor
    * @param int id l'identifiant de la portlet
    */
    function Portlet ($pId) {
        $this->id    = $pId;
    }

    /**
    * Récupère la portlet transformée en HTML (généralement HTML, plus généralement en code d'affichage)
    * @param string $pContext le contexte dans lequel on demande l'affichage (edit/front/...)
    */
    function getParsed ($pContext){
        return '';
    }

    /**
    * Retourne le nom du module qui gère la portlet (cms_portlet_MODULE)
    * @return string 
    */
    function getAddOnName () {
       $className = strtolower (get_class ($this));
       if (strpos ($className, 'portlet') === 0){
          return substr ($className, 7);
       }else{
          return substr ($className, 0, -7);
       }
    }

    /**
    * Retourne le nom du groupe auquel la portlet appartient
    * @return string
    */
    function getGroupI18NKey (){
        trigger_error ('abstract method call [Portlet::getGroup]');
    }

    /**
    * Retourne le nom de la portlet
    * @return string
    */
    function get18NKey (){
        trigger_error ('abstract method call [Portlet::getI18NCaption]');
    }

    /**
    * Assignation de paramètres à la portlet
    * @param array $pParams un tableau de paramètres à assigner à la portlet
    */
    function setParams (& $pParams){
        $this->params = $pParams;
    }

    /**
    * Défini la page dans laquelle la portlet est utilisée
    * @param  CMSPage $pPage la page dans laquelle est située la portlet
    */
    function setPage (& $pPage){
        $this->_page = $pPage;
    }
    
    /**
    * Retourne la page associée à la portlet
    * @return CMSPage
    */
    function & getPage (){
        return $this->_page;
    }

    /**
    * Défini le template à utiliser pour la portlet
    * @param string $pTemplateName l'identifiant du template à utiliser. Si numérique,
    */
    function setTemplate ($pTemplateName){
   		$this->templateId = $pTemplateName;
    }
    
    /**
    * Récupère le sélecteur Copix pour le template actuel de la portlet (var:/chemin/ ou module|chemin.tpl)
    * @return string
    */
    function getTemplateSelector (){
    	return $this->templateId;
    }

    /**
    * Récupère la valeur d'un paramètre passé à la portlet.
    * @param string $pParamName le nom du paramètre à tester
    * @param mixed $pParamDefaultValue la valeur par défaut si le paramètre n'est pas défini
    * @return mixed la valeur du paramètre
    */
    function getParam ($pParamName, $pParamDefaultValue = null){
       return array_key_exists ($pParamName, $this->params) ? $this->params[$pParamName] : $pParamDefaultValue;
    }
}
?>
