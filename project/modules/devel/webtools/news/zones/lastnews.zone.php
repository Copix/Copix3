<?php
/**
 * @package		webtools
 * @subpackage	news
 * @author		Florian JUDITH
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Zone qui affiche les x dernières news
 * @package webtools
 * @subpackage news
 */

class ZoneLastNews extends CopixZone {
	function _createContent(&$toReturn) {
		if (CopixAuth::getCurrentUser()->testCredential('module:Voir@news')) {
			CopixHtmlHeader::addCSSLink (_resource ('styles/news.css'));
			$nbNewsToShow = $this->getParam('nbNewsToShow',5);
			if (!is_int($nbNewsToShow) || $nbNewsToShow < 1) { //copixfilter
				$nbNewsToShow=5;
			}
			$nbCarMaxResume = $this->getParam('nbCarMaxResume',80); //copixfilter getint
			if (!is_int($nbCarMaxResume) || $nbCarMaxResume < 1) {
				$nbCarMaxResume=80;
			}
			$nbCarMaxTitle = $this->getParam('nbCarMaxTitle',30);
			if (!is_int($nbCarMaxTitle) || $nbCarMaxTitle < 1) {
				$nbCarMaxTitle=80;
			}
			$arNews = _dao ('news')	->findBy (_daoSp () ->orderBy (array ('date_news', 'DESC'))
														->addSQL('`news`.`date_news` < CURDATE() OR (`news`.`date_news`= CURDATE() AND `news`.`heure_news` <= CURTIME() )')
														//ne pas utiliser curdate mais fabriquer les dates et heures en copix
														//faire un seul champs datetime
									->setLimit (0, $nbNewsToShow));
			$tpl = new CopixTpl();
			$tpl->assign('newsList',$arNews);
			$tpl->assign('nbCarMaxResume',$nbCarMaxResume);
			$tpl->assign('nbCarMaxTitle',$nbCarMaxTitle);
			$toReturn = $tpl->fetch ('lastnews.zone.tpl');
			return true;
		} else {
			$toReturn = "";
			return false;
		}
	}
}
?>