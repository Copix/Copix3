<?php
/**
 * @package     standard
 * @subpackage  authextend
 * @author      Duboeuf Damien
 * @copyright   CopixTeam
 * @link        http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */


/**
 * Affiche la liste des paramètres
 */
class ZoneAdminEditExtendParam extends CopixZone {
	function _createContent (& $toReturn){
		
		$id_user    = $this->getParam ('id_user', NULL);
		$id_handler = $this->getParam ('id_handler', NULL);
		$idForm = $this->getParam ('idForm', NULL);
		
		$ppo             = _rPPO ();
		$tpl             = new CopixTpl ();
		$ppo->extends    = _ioClass ('authextend|authextend')->getAll ($id_user, $id_handler, $idForm);
		$ppo->id_user    = $id_user;
		$ppo->id_handler = $id_handler;
		
		$tpl->assign ('ppo', $ppo);
		
		$toReturn  = $tpl->fetch ('authextend|module.edit.tpl');
		
		return true;
	}
}

?>
