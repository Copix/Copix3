<?php
/**
 * @package		webtools
 * @subpackage	quicksearch
* @author	Croës Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package		webtools
 * @subpackage	quicksearch
* Ecran d'affichage des résultats de la recherche
*/
class ZoneQuickSearchresults extends CopixZone {
	/**
	 * Ecran de résultat d'une recherche
	 *
	 * @param string $toReturn le contenu à afficher par la zone
	 * @return boolean
	 */
	function _createContent (& $toReturn) {
		$tpl        = new CopixTpl ();
		$listResult = "";
		$i = 0;
		if( CopixModule::isEnabled('xiti') ){
			$xiti = true;
			$motcle = $this->_sanitize($this->getParam('criteria'));
		}
		
		$limit = CopixConfig::get ('resultsLimit');
		$shown=0;

		//On liste tous les résultats récupérés pour les affihcer dans le template
		foreach ($this->getParam ('results')->TableResult as $key=>$elem) {
			$i++;
			if (!isset ($poidsMax)){
			   $poidsMax = $this->getParam ('results')->TableResult[$key];
			}

			$tpl_line = new CopixTpl ();
			if (strpos ($this->getParam ('results')->TableLineResult[$key]->url_srch, 'http') !== 0 && 
			    strpos ($this->getParam ('results')->TableLineResult[$key]->url_srch, 'ftp') !== 0){
	            $this->getParam ('results')->TableLineResult[$key]->url_srch = CopixUrl::get () . $this->getParam ('results')->TableLineResult[$key]->url_srch;
			}
			if($xiti){
				// Pour tracking Xiti, on doit ajouter le mot cle et sa position dans les resultats dans l'URL
				$this->getParam ('results')->TableLineResult[$key]->url_srch .= "&xtmc=$motcle&xtcr=$i";
			}
			$tpl_line->assign ('objResult', $this->getParam ('results')->TableLineResult[$key] );
			$tpl_line->assign ('weight', round ($this->getParam ('results')->TableResult[$key]/$poidsMax * 100,0) );
			$listResult .= $tpl_line->fetch ('line.result.tpl');
			$tpl_line = null;
			
			if (++$shown >= $limit){
				break;
			}
		}
		$tpl->assign ('listResult', $listResult );

		//Affichage du nombre de résultats
		$tpl->assign ('nbResult', $this->getParam ('nbResult'));
		$toReturn = $tpl->fetch ('search.show.tpl');
		return true;
	}
	
	private function _sanitize($str){
		$str = strtolower($str);
		$charset = 'UTF-8'; // ISO8859-1';
		$str = preg_replace('/&(.)(acute|caron|cedil|circ|grave|ring|slash|tilde|uml);/', '$1', $str);
		$str = html_entity_decode($str, ENT_NOQUOTES, $charset);
		
		$str = preg_replace('"[^a-z0-9_:~\\\/\-]"i','_',$str);
		return $str;
	}
}
?>