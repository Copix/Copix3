<?php
/**
 * @package 	copix
 * @subpackage	taglib
 * @author		Sabine CIMAPONTI
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Tag permettant de générer facilement une image et éventuellement un lien avec une image dans 
 *  img/tools
 */
class TemplateTagCopixIcon extends CopixTemplateTag {
	/**
	 * Génération du code HTML
	 * @return string
	 */
	public function process($pParams) {
		
		//Test si le parametre correspondant au type existe
	 	if (isset($pParams['type'])) {
            $type = $pParams['type'];
        }else{
        	//Sinon on génère une exception précisant que le type est manquant
        	throw new CopixTemplateTagException ('CopixImage: missing type parameter');
        }
		
        //Si une propriété correspond au type saisi
        if (CopixI18N::exists('copix:common.buttons.'.$type)){
        	//On récupère le libellé de ce type
        	$alt = _i18n ('copix:common.buttons.'.$type);
        }else{
        	//Sinon on génère une erreur
        	throw new CopixException ('You must enter an existing type');
        }
        
        //identifiant sur le href
		$idimg = '';
		$idhref = '';
	 	if (isset ($pParams['id'])){
        	$idimg =  'id="'.$pParams['id'].'_img"';
        	$idhref = 'id="'.$pParams['id'].'_href"';
	 	}
	
        //Initialisation du type
        if (isset ($pParams['title'])){
        	$title = $pParams['title'];	
        }else{
        	$title = $alt;
        }
        
        if (isset ($pParams['class'])){
        	$class = 'class="'.$pParams['class'].'"';
        }else{
			$class = '';
        }

        //Création du chemin ou se trouve l'image
        $fileName = str_replace (CopixUrl::getRequestedBaseUrl (), './', _resource ("img/tools/".$type.".png"));

        //Test si le fichier existe
        if (file_exists ($fileName)) {
		    $src  = _resource ("img/tools/".$type.".png") ;
		} else {
		    throw new CopixException ('No icon does not correspond to your application');
		}
		
		if (isset ($pParams['text'])){
			$text = $pParams['text'];
		}else{
			$text = '';
		}
	
		//si une url a été renseignée
		if (isset ($pParams['href'])){
			$href = $pParams['href'];
			return '<a href="'.$href.'" '.$idhref.' title="'.$title.'" '.$class.'><img src="'.$src.'" '.$idimg.' alt="'.$alt.'"/>'.$text.'</a>';
		}else{
			return '<img src="'.$src.'" '.$idimg.' alt="'.$alt.'" title="'.$title.'"  '.$class.' />'.$text;
		}
    }
}
?>