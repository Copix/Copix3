<?php
/**
 * @package		simplehelp
 * @author		Audrey Vassal, Brice Favre
 * @copyright	2001-2006 CopixTeam
 * @link		http://copix.org
 * @licence		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		tools
 * @subpackage	simplehelp
 */
class ZoneShowAide extends CopixZone {
	
	/**
	 * Visualisation de l'aide
	 *
	 * @param string $toReturn
	 * @return string
	 */
	function _createContent (&$toReturn) {
		$tpl = new CopixTpl ();
		$displayAide = true;
		$daoAide = _ioDao ('simplehelp');
		$sp = _daoSp() -> addCondition('page_sh', '=', $this->getParam('page_sh', null, null))
		               -> addCondition('key_sh' , '=', $this->getParam('key_sh', null, null));

		$arAide = $daoAide->findBy($sp);

		if(count($arAide) > 0){
			$aide = $arAide[0];
		}else{
			$displayAide = false;
			$aide = null;
		}
		 
		// cette variable est crée pour différencier si l'aide doit être afficher dans un popup classique ou un popuinformation
		$popup = false;
		if ($this->getParam('popup', null, true) === "true"){
			$popup = true;
		}

		$tpl->assign ('aide'        , $aide);
		$tpl->assign ('displayAide'	, $displayAide);
		$tpl->assign ('popup'       , $popup);
		// $tpl->assign ('nofloat', $this->getParam ('nofloat', false));

		$toReturn = $tpl->fetch ('showaide.tpl');
		return true;
	}
}
?>