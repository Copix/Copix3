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
 * Balise capable d'afficher un selectionneur de langue
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagCopixLanguageChooser extends CopixTemplateTag {
	/**
	 * Input:    name         = (required) name of the select box
	 *           id           = (optional) id of the select box
	 *           selected     = (optional) id of the selected element
	 *           extra        = (optional) if given, will be added directly in the select tag
	 *           style        = (optional) if given, will be added directly in the select tag
	 *           emptyShow    = [true] / false - wether to show or not the "emptyString"
	 *           emptyValues  = id / value for the empty selection
	 *           widthSelect  = Taille de la zone de selection valeur 'auto' possible
	 *           heightSelect = Taille de la zone de selection valeur 'auto' nonpossible
	 *           arrow        = affiche ou non la flèche de selection
	 *           emptyValuesAlternatives  = id / value for the empty selection alternative select
	 *           emptyValuesView          = id / value for the empty selection for the view
	 *           extraAlternative         = (optional) if given, will be added directly in the select tag alternative
	 *           extraStyle               = (optional) if given, will be added directly in the select tag alternative
	 */
	public function process ($pContent=null) {
		//input check
		$this->assertParams ('name');
		
		$tpl = new CopixTpl ();
		$ppo = _rPPO ();
		$ppo->name         = $this->getParam ('name');
		$ppo->class        = $this->getParam ('class');
		$ppo->id           = $this->getParam ('id', $ppo->name);
		$ppo->selected     = $this->getParam ('selected', CopixI18N::getLocale ());
		$ppo->extra        = $this->getParam ('extra', '');
		$ppo->style        = $this->getParam ('style', '');
		$ppo->emptyShow    = $this->getParam ('emptyShow', false);
		$ppo->emptyValues  = $this->getParam ('emptyValues', '-----');
		$ppo->arrow        = $this->getParam ('arrow', true);
		$ppo->widthSelect  = $this->getParam ('widthSelect', 'auto');
		$ppo->heightSelect = $this->getParam ('heightSelect', 200);
		
		$ppo->emptyValuesAlternatives = $this->getParam ('emptyValuesAlternatives', '-----');
		$ppo->emptyValuesView         = $this->getParam ('emptyValuesView', '');
		$ppo->extraAlternative        = $this->getParam ('extraAlternative', $ppo->extra);
		$ppo->extraStyle              = $this->getParam ('extraStyle', $ppo->style);
		if ($ppo->alternatives && $ppo->class) {
			$ppo->extraAlternative .= ' class="'.$ppo->class.'" ';
		}
		
		$tpl->assign ('ppo', $ppo);
		
		return $tpl->fetch ('selectcomplex|copixlanguagechooser.php');
	}
}