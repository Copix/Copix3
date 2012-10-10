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
 * Classe effectuant le rendu d'une news, en fonction du type de rédaction utilisée
 * @package webtools
 * @subpackage news
 */
class newsrender { //cf http://www.copix.org/index.php/wiki/CopixClassesFactory pour l'appel
	/**
	 * Fonction qui retraite le contenu d'une DAO de news afin de l'afficher à l'écran
	 *
	 * @param CopixDao $newsDao
	 * @return CopixDao Dao modifiée
	 */
	public static function renderNewsContent($newsDao) {
		CopixHtmlHeader::addCSSLink (_resource ('styles/news.css'));
		switch ($newsDao->type_news) {
			case 'wiki' :
				$newsDao->content_news = _class ('wikirender|wiki')->render ($newsDao->content_news, false);
				break;
			case 'wysiwyg' :
				break;
			default :
				$newsDao->content_news = nl2br($newsDao->content_news);
				break;
		}
		return $newsDao;
	}
	
	/**
	 * Fonction récupérant la liste des tags associés à une news
	 *
	 * @param int $idNews
	 */
	public static function renderTags($idNews) {
		
	}
}
?>