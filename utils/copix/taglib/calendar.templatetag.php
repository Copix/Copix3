<?php
/**
* @package		copix
* @subpackage	taglib
* @authors		Gérald Croës, Estelle Fersing
* @copyright	CopixTeam
* @link			http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Génération d'une boite de saisie pour les dates
 * @package copix
 * @subpackage taglib
 * @example {calendar name="maDate"	value="31/12/1970"}
 * Paramètre requis
 * 		name : nom et identifiant de l'input qui sera créé et qui contiendra la date
 * Paramètre recommandé
 * 		image : url de l'image à lier à l'input qui sera créé
 * Paramètres optionnels
 * 		size : taille de l'input qui sera créé : par défaut 8
 * 		format : Format utilisé par le calendrier, par défaut DD/MM/YYYY
 * 		lang : langue dans laquelle on affiche les nom des mois, jour dans le calendier
 * 		sizeday : Nombre de caractères utilisés pour afficher les jours - par défaut 3
 * 		value : Valeur à afficher directement dans l'input
 * 		yyyymmdd : Sera converti au format local et affiché dans l'input
 * 		timestamp : Sera converti au format local et affiché dans l'input
 * 		beforeyear : nombre d'année précédant l'année courante (ou l'année contenue dans value) et disponible dans la liste déroulante (par défaut 10)
 *  	afteryear : nombre d'année suivant l'année courante (ou l'année contenue dans value) et disponible dans la liste déroulante (par défaut 10)
 *		duration : Temps en ms mis pour afficher le calendier lors du clic sur l'image de celui-ci
 * 		tabindex : La position du champ dans l'ordre des tabulations.
 * 		classe : Class a attribuer aux éléments du calendrier : la table, le header, le jour courant, le jour de la valeur, les autres jours, les cases vides
 * 			(voir le tutorial de création de tags : tags_demos)
 * 		extra : valeur ou autre option a lié à l'input
 */
class TemplateTagCalendar extends CopixTemplateTag {
   /**
    * Construction du code HTML
    * On utilise également les modifications d'en tête HTML
    */
   public function process ($pParams, $pContent=null){
	    extract ($pParams);
	    if (empty ($name)){
	      throw new CopixTemplateTagException ('[calendar] missing name parameter');
	    }

	    // verification que le name ne contient que des caractères alphanumériques ou _
	    preg_match_all('/^\w+$/', $name, $out_pma);
	    if(sizeof($out_pma[0]) == 0){
	    	throw new CopixTemplateTagException ('[Calendar] name should only contain alpha numeric and underscore characters : '.$name);
	    }

   		if (empty ($image)){
   			$image = '"'._resource ("img/tools/calendar.png").'"';
	    }else{
	    	$image = '"'.$image.'"';
	    }

		if (empty ($extra)){
           $extra = '';
	    }

	    if (empty ($size)){
           $size = 8;
	    }

	    if (!isset ($mask)) {
	        $mask = true;
	    }
		_tag ('mootools', array ('plugin'=>array ('datepicker')));

        if ($mask) {
			_tag ('mootools', array ('plugin'=>array ('imask')));
             CopixHTMLHeader::addJsCode ("
             window.addEvent('domready', function () {
                    new iMask({
                         onFocus: function(obj) {
                         },

                         onBlur: function(obj) {
                         },

                         onValid: function(event, obj) {
                         },

                         onInvalid: function(event, obj) {
                             if(!event.shift) {
                             }
                         }
                     });
             });
             ",'calendarmask');
        }

        if (empty ($lang)){
	       $lang = CopixI18N::getLang ();
	    }

		if (empty ($format)){
	       $format = str_replace (array ('d', 'm', 'Y'), array ('dd', 'mm', 'yyyy'), CopixI18N::getDateFormat ('/'));
	    }else{
	    	$format = strtolower($format);
	    }
	    $maskFormat = str_replace (array ('d', 'm', 'y'), array ('9', '9', '9'), $format);


	    if (!empty ($yyyymmdd)){

			$value = date (str_replace (array ('dd', 'mm', 'yyyy'), array ('d', 'm', 'Y'), $format), CopixDateTime::yyyymmddToTimestamp ($yyyymmdd));
		}

		if (!empty ($timestamp)){
			$value = date (str_replace (array ('dd', 'mm', 'yyyy'), array ('d', 'm', 'Y'), $format), $timestamp);
		}


	    if (empty ($sizeday)){
	       $sizeday = 3;
	    }

   		if (!isset ($beforeyear)){
	       $beforeyear = 10;
	    }

		if (!isset ($afteryear)){
			$afteryear = 10;
	    }

   		if (!isset ($duration)){
			$duration = 500;
	    }

	    if (!isset ($closebuttonsrc)) {
	    	$closebuttonsrc = null;
	    } else {
	    	$closebuttonsrc = _resource ($closebuttonsrc);
	    }

	    if (!isset ($draggable)) {
	    	$draggable = true;
	    }
	    $draggable = ($draggable) ? 'true' : 'false';

	    if (!isset ($title)) {
	    	$title = null;
	    }

        if (empty ($value)){
           if ($mask) {
	           $value = str_replace('9','_',$maskFormat);
           } else {
               $value = null;
           }
	    }

	    $strMask = '';
	    if ($mask) {
	    	//CLIC DROIT interdi pour éviter tous probleme de formatage
	        $strMask = 'style="text-align:center" oncontextmenu="return false" class="iMask" alt="{  type:\'fixed\', mask:\''.$maskFormat.'\', stripMask: false }"';
	    }

   		$out = '<input type="text" id="'.$name.'" name="'.$name.'" value="'._copix_utf8_htmlentities ($value).'" '.$extra.' size="'.$size.'" ';
	    //name of the input.
   		if (!empty ($tabindex)){
			$out .= ' tabindex="'.$tabindex.'" ';
   		}
   		$out .= $strMask.' />'."\n\r";
   		$out.= '<script type="text/javascript">'."\n\r";
   		$out.= "$('".$name."').makeDatePicker({draggable:$draggable, title: '" . str_replace ("'", "\'", $title) . "', closebuttonsrc: '" . $closebuttonsrc . "', value: '" .$value . "', format: '".$format."', language: '".$lang."', sizeday:".$sizeday.", beforeyear:".$beforeyear.", afteryear:".$afteryear.", duration:".$duration.", imageCalendar:".$image;
    	//class du calendrier.
   		if (!empty ($classe)){
			$out .= ', classe:"'.$classe.'"';
   		}
   		$out.="});"."\n\r";
   		return $out.= '</script>';
   }
}
?>