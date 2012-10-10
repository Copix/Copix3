<?php
/**
 * @package	webtools
 * @subpackage	wiki
* @author	Patrice Ferlet
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Zone d'affichage d'une image
 * @package	webtools
 * @subpackage	wiki
 */
class ZoneImage extends CopixZone {
	function _createContent(& $toReturn) {
		$dao = _dao ('wikiimages');
		$name = $this->getParam('heading',"")."/".$this->getParam('page');		
		if (!$this->getParam('page', false)) {
			$files = $dao->findAll();
		} else {
			$heading = $this->getParam('heading',"");
			$pagename = $this->getParam('page');
			$parts = explode('/',$this->getParam('page'));
			if(count($parts)>1){
				$heading = $parts[0];
				$pagename=$parts[1];
				$name = $headings."/".$pagename;
			}
			$sp = _daoSp ()->addCondition('page_wikiimage', '=', $name);
			$files = $dao->findBy($sp);
		}

		$images=array();
		foreach ($files as $file){
			$tmp = explode(".", $file->file_wikiimage);
			$ext = $tmp[count($tmp) - 1];
			$file->type="file";
			$imgtypes = array("gif","png","jpg","jpeg","pnm","bmp");
			if(in_array (strtolower($ext),$imgtypes)){
				$file->type="image";
			}
			$images[] = $file;
		}
		
		$tpl = new CopixTpl();
		$tpl->assign('images', $images);
		$tpl->assign('page', $pagename);
		$tpl->assign('selected', $name);
		$tpl->assign('heading',$heading);
		$tpl->assign('pageswithimage', $this->getPageHavingImg());

		$toReturn = $tpl->fetch('image.tpl');
		return true;

	}

	function getPageHavingImg() {
		$dao = _dao ('wikiimages');
		$images = $dao->findAll();
		$pages = array ();
		foreach ($images as $img) {
			if (!in_array($img->page_wikiimage, $pages)) {
				$pages[] = $img->page_wikiimage;
			}
		}
		return $pages;
	}
}
?>