<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Brice Favre
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permettre d'afficher du code coloré
 * Utilisation (en PHP)
 * _eTag ('geshi', array ('lang'=>'php','content'=>'<?php echo World;?>');
 *
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagGeshi extends CopixTemplateTag {

	public function process ($pContent = null) {
		$this->assertParams ('content', 'lang');

		require_once (CopixModule::getPath ('geshi').'lib/geshi/geshi.php');
		$geshi = new GeShi ($this->getParam ('content'), $this->getParam ('lang'));

		$geshi->set_header_type (GESHI_HEADER_DIV);
		return $geshi->parse_code ();
	}
}