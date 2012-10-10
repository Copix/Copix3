<?php
/**
 * MooBox which fetch last RSS from an url and display it on reader
 * 
 * @package MooCMS
 * @subpackage MooBox
 * @subpackage rssnews
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 
 * 
 *
 */
class MooboxRssNews extends MooBox {
	
	/**
	 * Return the RSS reader
	 * RSS url is on parameters $params['url']
	 *
	 * @param array $params
	 * @return string to display
	 */
	public function getContent($pParams){
		$rss = _ioClass('rss|reader')->read($pParams['url']);
		$tpl = new CopixTpl();
		$tpl->assign('rss', $rss);
		return $tpl->fetch("moobox_rssnews|lastnews.php");
	}
	
	/**
	 * Get edit page
	 *
	 * @return string display
	 */
	public function getEdit(){
		$tpl= new CopixTpl();
		return $tpl->fetch("moobox_rssnews|edit.php");		
	}
	
}
?>