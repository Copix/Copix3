<?php
/**
 * @package		webtools
 * @subpackage	wbe
 * @author		Favre Brice
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions de présentation des différentes zones fournies par le module wbe
 * @package webtools
 * @subpackage wbe
 */
class ActionGroupDefault extends CopixActionGroup {
	
	/**
	 * Function de test par défault (pour le moment fait appel à la tinymcearea
	 *
	 * @return CopixActionReturn::PPO
	 */
	function processDefault (){
		$pKind = _request ('kind');
		$ppo = new CopixPPO ();
		switch ($pKind) {
			case 'tinymce':
				$ppo->html_area = CopixZone::process ('wbe|tinymcearea', array ('name'=>'contenu', 'rows'=>15, 'cols'=>'50', 'content'=>'Merci de taper votre texte ici'));
				break;
			case 'fckeditor':
				$ppo->html_area = CopixZone::process ('wbe|fckeditorarea', array ('name'=>'contenu', 'rows'=>15, 'cols'=>'50', 'content'=>'Merci de taper votre texte ici'));
				break;
			default:
				$ppo->html_area = CopixZone::process ('wbe|defaultarea', array ('name'=>'contenu', 'rows'=>15, 'cols'=>'50', 'content'=>'Merci de taper votre texte ici'));
				break;
		}

		return _arPPO ($ppo, 'default.tpl');
	}
	
}
?>