<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Services pour a recherche avancée du CMS
 * 
 * @package cms
 * @subpackage heading
 */
class AdvancedSearchServices {
	/**
	 * Effectue une recherche
	 *
	 * @param CopixPPO $pConditions Conditions de recherche
	 * @param int $pStart Index du premier élément à retourner
	 * @param int $pCount Nombre d'éléments à retourner
	 * @return array
	 */
	public function search ($pConditions, $pStart = 0, $pCount = 20) {
		$toReturn = array ('count' => 0, 'results' => array (), 'errors' => array ());

		// recherche d'un identifiant public
		if ($pConditions->resolve_public_id > 0) {
			$heiService = _ioClass ('heading|HeadingElementInformationServices');

			// recherche de l'identifiant public directement
			try {
				$toReturn['results'][] = $heiService->get ($pConditions->resolve_public_id);
			} catch (Exception $e) {
				$message = 'L\'élément d\'identifiant public "' . $pConditions->resolve_public_id . '" n\'existe pas ou a été supprimé.';
				$message .= '<br />Si vous trouvez des éléments qui ont un lien avec cet identifiant public, ces liens sont probablement invalides.';
				$toReturn['errors']['public_id'] = $message;
			}

			// types dans lesquels rechercher
			$types = array ();
			$typesServices = _ioClass ('HeadingElementType');

			if (is_array ($pConditions->type_hei) && !empty ($pConditions->type_hei)) {
				foreach ($pConditions->type_hei as $name) {
					$types[$name] = $typesServices->getInformations ($name);
				}
			} else {
				$types = _ioClass ('HeadingElementType')->getList ();
			}
			foreach ($types as $name => $infos) {
				foreach (HeadingElementServices::call ($name, 'getDependencies', $pConditions->resolve_public_id) as $element) {
					try{
						if (
						// caption
						(!$pConditions->caption_hei || strpos ($element->caption_hei, $pConditions->caption_hei) !== false)
						// status
						&& (!is_numeric ($pConditions->status_hei) || $pConditions->status_hei == $element->status_hei)
						) {
							$toReturn['results'][] = $element;
						}
					}catch(Exception $e){}
				}
			}

			$toReturn['count'] = count ($toReturn['results']);
			$toReturn['results'] = $pCount ? array_slice ($toReturn['results'], $pStart, $pCount) : $toReturn['results'];

		// recherche sur tous les autres critères
		} else {
			$params = array ();
			$endQuery = " version_hei = (SELECT max(version_hei) FROM cms_headingelementinformations h2 WHERE public_id_hei = h.public_id_hei) ";
			if ($pConditions){
				if (is_numeric($pConditions->status_hei)){
					$endQuery .= " AND status_hei = :status_hei ";
					$params[':status_hei'] = $pConditions->status_hei;
				}
				if ($pConditions->caption_hei){
					$endQuery .= " AND caption_hei LIKE :caption_hei";
					$params[':caption_hei'] = '%'.$pConditions->caption_hei.'%';
				}
				if (is_array($pConditions->type_hei) && !empty($pConditions->type_hei)){
					$endQuery .= " AND type_hei IN ('".implode("','", $pConditions->type_hei)."') ";
				}
				if ($pConditions->inheading){
					$endQuery .= ' AND h.hierarchy_hei LIKE :heading';
					$params[':heading'] = $pConditions->inheading == 0 ? $pConditions->inheading.'-%' : '%-'.$pConditions->inheading.'-%';
				}
				if ($pConditions->url_id_hei){
					$endQuery .= ' AND h.url_id_hei LIKE :url_id_hei';
					$params[':url_id_hei'] = '%'.$pConditions->url_id_hei.'%';
				}
				if ($pConditions->content){		
					$types = _ioClass ('HeadingElementType')->getList ();
					$publicIds =array();
					foreach ($types as $name => $infos) {
						$publicIds = array_merge($publicIds,HeadingElementServices::call ($name, 'search', strtolower($pConditions->content)));
					}		
					if(count($publicIds) > 0){
						$publicIds = array_unique($publicIds);
						$endQuery .= ' AND public_id_hei in ('.join(',', $publicIds).')';
					}
				}
				if ($pConditions->sort){
					$endQuery .= " ORDER BY ".$pConditions->sort." " . $pConditions->sortOrder ." ";
				} else {
					$endQuery .= " ORDER BY date_update_hei DESC ";
				}
			} else {
				$endQuery .= " ORDER BY date_update_hei DESC ";
			}

			$resultCount = _doQuery ('SELECT COUNT(*) count FROM cms_headingelementinformations h WHERE ' . $endQuery, $params);
			$toReturn['count'] = $resultCount[0]->count;
			// on spécifie bien la date au format Copix, puisque les DAO le font automatiquement
			$query = 'SELECT *, DATE_FORMAT(date_update_hei, \'%Y%m%d%H%i%s\') date_update_hei FROM cms_headingelementinformations h';
			$query .= ' WHERE ' . $endQuery;
			$query .= $pCount ? ' LIMIT ' . $pStart . ', ' . $pCount : '';
			$toReturn['results'] = _doQuery ($query, $params);
		}
		return $toReturn;
	}
}