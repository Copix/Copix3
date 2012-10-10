<?php
/**
 * @package copix
 * @subpackage core
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Facilite la génération d'un pager
 *
 * @package copix
 * @subpackage core
 */
class CopixPager {
	/**
	 * Retourne l'HTML avec les liens vers les pages
	 *
	 * @param int $pCount Nombre d'éléments au total
	 * @param int $pCountPerPage Nombre d'éléments par page
	 * @param string $pLink Lien à pointer, :page sera remplacé par le numéro de la page
	 * @param int $pCurrentPage Page ouverte actuellement
	 * @param string $pTemplate Nom du template à utiliser
	 * @return string
	 */
	public static function getHTML ($pCount, $pCountPerPage, $pLink, $pCurrentPage, $pTemplate = 'default|pager.php') {
		$pages = self::get ($pCount, $pCountPerPage, $pLink);
		$tpl = new CopixTPL ();
		$tpl->assign ('pages', $pages);
		$tpl->assign ('count', $pCount);
		$tpl->assign ('first', str_replace ('__page__', 1, $pLink));
		$tpl->assign ('previous', str_replace ('__page__', ($pCurrentPage == 1) ? 1 : $pCurrentPage - 1, $pLink));
		$tpl->assign ('next', str_replace ('__page__', ($pCurrentPage == count ($pages)) ? count ($pages) : $pCurrentPage + 1, $pLink));
		$tpl->assign ('last', str_replace ('__page__', count ($pages), $pLink));
		$tpl->assign ('showPrevious', ($pCurrentPage > 1));
		$tpl->assign ('showNext', (count ($pages) > $pCurrentPage));
		$tpl->assign ('currentPage', $pCurrentPage);
		return $tpl->fetch ($pTemplate);
	}

	/**
	 * Retourne les liens vers les pages
	 *
	 * @param int $pCount Nombre d'élélements au total
	 * @param int $pCountPerPage Nombre d'éléments par page
	 * @param string $pLink Lien à pointer, __page__ sera remplacé par le numéro de la page
	 * @return array
	 */
	public static function get ($pCount, $pCountPerPage, $pLink) {
		$nbrPages = ceil ($pCount / $pCountPerPage);
		$toReturn = array ();
		for ($x = 0; $x < $nbrPages; $x++) {
			$toReturn[] = str_replace ('__page__', ($x + 1), $pLink);
		}
		return $toReturn;
	}
}