<?php
/**
 * @package    copix
 * @subpackage taglib
 * @author     Damien Duboeuf
 * @copyright  CopixTeam
 * @link       http://www.copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Balise capable d'afficher une liste déroulante complexe
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagSelectComplex extends CopixTemplateTag {
	/**
	 * Input:    name         = (required) name of the select box
	 *           id           = (optional) id of the select box
	 *           options      = (required) liste des options du select à afficher - array ('value_1'=>'html_1'...)
	 *           selected     = (optional) id of the selected element
	 *           alternatives = (optional) liste des options du select à afficher - array ('value_1'=>'text'...)
	 *                          alternatives si le js n'est p&s activé
	 *           selectedView = (optional) Tabelau de l'affichage a utilisé une fois l'option selectionné
	 *                          array ('value_1'=>'html_de_selection_1'...)
	 *           extra        = (optional) if given, will be added directly in the select tag
	 *           style        = (optional) if given, will be added directly in the select tag
	 *           width        = (optional) taille de la box
	 *           arrow        = affiche ou non la flèche de selection
	 *           arrowImg     = image à utiliser pour la flèche de selection
	 *           emptyShow    = [true] / false - wether to show or not the "emptyString"
	 *           emptyValues  = id / value for the empty selection
	 *           widthSelect  = Taille de la zone de selection valeur 'auto' possible
	 *           heightSelect = Taille de la zone de selection valeur 'auto' nonpossible
	 *           emptyValuesAlternatives  = id / value for the empty selection alternative select
	 *           emptyValuesView          = id / value for the empty selection for the view
	 *           extraAlternative         = (optional) if given, will be added directly in the select tag alternative
	 *           extraStyle               = (optional) if given, will be added directly in the select tag alternative
	 */
	public function process ($pContent=null) {
		//input check
		$this->assertParams ('name', 'options');
		
		$tpl = new CopixTpl ();
		$ppo = _rPPO ();
		
		$ppo->name         = $this->getParam ('name');
		$ppo->class        = $this->getParam ('class');
		$ppo->id           = $this->getParam ('id', $ppo->name);
		$ppo->options      = $this->getParam ('options');
		$ppo->selected     = $this->getParam ('selected', '');
		$ppo->alternatives = $this->getParam ('alternatives', array());
		$ppo->selectedView = $this->getParam ('selectedView', array());
		$ppo->extra        = $this->getParam ('extra', '');
		$ppo->style        = $this->getParam ('style', '');
		$ppo->emptyShow    = $this->getParam ('emptyShow', true);
		$ppo->emptyValues  = $this->getParam ('emptyValues', '-----');
		$ppo->width        = $this->getParam ('width', 0);
		$ppo->arrow        = $this->getParam ('arrow', true);
		$ppo->widthSelect  = $this->getParam ('widthSelect', 'auto');
		$ppo->heightSelect = $this->getParam ('heightSelect', 200);
		$ppo->arrowImg     = $this->getParam ('arrowImg', _resource ('selectcomplex|img/tools/arrow.gif'));
		
		$ppo->emptyValuesAlternatives = $this->getParam ('emptyValuesAlternatives', '-----');
		$ppo->emptyValuesView         = $this->getParam ('emptyValuesView', $ppo->emptyValues);
		$ppo->extraAlternative        = $this->getParam ('extraAlternative', $ppo->extra);
		$ppo->extraStyle              = $this->getParam ('extraStyle', $ppo->style);
		if ($ppo->alternatives && $ppo->class) {
			$ppo->extraAlternative .= ' class="'.$ppo->class.'" style="'.$ppo->extraStyle.'" ';
		}
		
		$tpl->assign ('ppo', $ppo);
		
		//Ajout du JS
		$optionsJS ['id']           = $ppo->id;
		$optionsJS ['selected']     = $ppo->selected;
		$optionsJS ['name']         = $ppo->name;
		
		_tag ('mootools');
		CopixHTMLHeader::addJSLink (_resource ('selectcomplex|js/selectcomplex.js'));
		$js = new CopixJSWidget ();
		$js->Copix->registerSelectcomplex ($optionsJS);
		CopixHTMLHeader::addJSCode ($js, NULL, CopixHTMLHeader::DOMREADY_ALWAYS);
		
		//Ajout du CSS
		CopixHTMLHeader::addCSSLink (_resource ('selectcomplex|styles/selectcomplex.css'));
		CopixHTMLHeader::addCSSLink (_resource ('selectcomplex|styles/default.css'));
		
		return $tpl->fetch ('selectcomplex|selectcomplex.tpl');
	}
}