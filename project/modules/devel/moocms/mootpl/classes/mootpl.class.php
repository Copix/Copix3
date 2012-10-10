<?php
/**
 * MooTPL service
 * 
 * @package MooCMS
 * @subpackage MooTPL
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * 
 */

class MooTPL {
	
	/**
	 * Template list found in mootpl templates path
	 *
	 * @var array of PPO
	 */
	private $_tpls = array();
	
	/**
	 * Constructor
	 *
	 */
	public function __construct(){		
		$dir= CopixModule::getPath('mootpl').'templates/';
		$tpls = CopixFile::findFiles($dir);		
		foreach($tpls as $tpl){
			$tpl= basename($tpl);
			$tplname = preg_replace('/\.tpl/','',$tpl);
			$tplname = preg_replace('/display./','',$tplname);
			$tplname = str_replace('.','_',$tplname);
			$desc = CopixI18N::get('mootpl|mootpl_'.$tplname.'.description');
			$this->_tpls [] = _ppo(array(
				'name' => $tpl,
				'desc' => $desc
			));
		}
	}
	
	/**
	 * Returns mootpl templates list
	 *
	 * @return array of PPO
	 */
	public function getTemplates (){
		return $this->_tpls;		
	}
	
}
?>