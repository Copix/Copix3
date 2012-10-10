<?php

/**
 * @package cms
 * @subpackage cms_portlet_searchengine_detail
 */

/**
 * @ignore
 */

CopixClassesFactory::fileInclude ('searchengine|searchengine');
CopixClassesFactory::fileInclude ('cms|portlet');


/**
 * @package cms
 * @subpackage cms_portlet_searchengine_detail
 * PortletSearchEngine_detail
 */


class PortletSearchEngine_detail extends Portlet {
    /**
    * launch the search and show results
    */
    function getParsed ($context) {
        if (!isset ($this->params['criteria'])||$this->params['criteria']==""){
            $tpl = & new CopixTpl ();
            return $tpl->fetch ('cms_portlet_searchengine_detail|search.error.tpl');
        }

        $this->params['criteria'] = htmlentities ($this->replaceAccent($this->params['criteria']));

		//Création de l'objet de recherche.
        $objLook = CopixClassesFactory::create ('searchengine|SearchEngine');

		//Remise à zéro de l'objet de recherche
		$objLook->ClearAll ();

		//Définition des paramètres de la recherche.
		$objLook->AdvancedSearch = true;
		$objLook->TableName   = "searchindex";
		$objLook->FieldIdName = "id_srch";

		//Définition des poids de rcherche
		$objLook->AssignLookParams(array ("keywords_srch"=>CopixConfig::get ('searchengine|keywords_srch'), "title_srch"=>CopixConfig::get ('searchengine|title_srch'), "resume_srch"=>CopixConfig::get ('searchengine|resume_srch'), "content_srch"=>CopixConfig::get ('searchengine|content_srch')));
		$objLook->AddFields (array ("title_srch", "type_srch", "resume_srch", "url_srch" ));

		$nbResult = $objLook->ExecuteRequest ($this->params['criteria']);
        return CopixZone::process ('searchengine|SearchShow', array ('results'=>$objLook, 'nbResult'=>$nbResult));
    }

    function replaceAccent($string){
        $Caracs = array("¥" => "Y", "µ" => "u", "À" => "A", "Á" => "A",
        "Â" => "A", "Ã" => "A", "Ä" => "A", "Å" => "A",
        "Æ" => "A", "Ç" => "C", "È" => "E", "É" => "E",
        "Ê" => "E", "Ë" => "E", "Ì" => "I", "Í" => "I",
        "Î" => "I", "Ï" => "I", "Ð" => "D", "Ñ" => "N",
        "Ò" => "O", "Ó" => "O", "Ô" => "O", "Õ" => "O",
        "Ö" => "O", "Ø" => "O", "Ù" => "U", "Ú" => "U",
        "Û" => "U", "Ü" => "U", "Ý" => "Y", "ß" => "s",
        "à" => "a", "á" => "a", "â" => "a", "ã" => "a",
        "ä" => "a", "å" => "a", "æ" => "a", "ç" => "c",
        "è" => "e", "é" => "e", "ê" => "e", "ë" => "e",
        "ì" => "i", "í" => "i", "î" => "i", "ï" => "i",
        "ð" => "o", "ñ" => "n", "ò" => "o", "ó" => "o",
        "ô" => "o", "õ" => "o", "ö" => "o", "ø" => "o",
        "ù" => "u", "ú" => "u", "û" => "u", "ü" => "u",
        "ý" => "y", "ÿ" => "y");

        return strtr($string, $Caracs);
    }

    function getGroup (){
        return 'general';
    }
    function getGroupI18NKey (){
        return 'cms_portlet_searchengine_detail|searchengine_detail.group';
    }
    function getI18NKey (){
        return 'cms_portlet_searchengine_detail|searchengine_detail.description';
    }
}
/**
 * @package cms
 * @subpackage cms_portlet_searchengine_detail
* Pour des raisons de compatibilité, l'ancienne convention étant de nommer les portlet XXXPortlet et non PortletXXX
*/
class SearchEngine_detailPortlet extends PortletSearchEngine_detail {}
?>
