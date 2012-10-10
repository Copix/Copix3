<?php
/**
 * @package copix
 * @subpackage events
 * @author Croës Gérald, Patrice Ferlet
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Représente une réponse à un événement
 * 
 * @package copix
 * @subpackage events
 */
class CopixEventResponse {
	/**
	 * Liste des réponses reçues
	 * 
	 * @var array
	 */
	public $_responses = array ();

	/**
	 * Ajoute une réponse à la liste
	 *
	 * @param array $pResponse Element de réponse. Exemple : array ('element1' => 'valeur1', 'element2' => 'valeur2')
	 */
	public function add ($pResponse) {
		$this->_responses[] = $pResponse;
	}

	/**
	 * Indique s'il existe un élément du nom recherché dans les réponses et qui dispose d'une valeur donnée
	 * <code>
	 * CopixDB::begin ();
	 * //lance une requête de mise à jour
	 * //indique que l'on a lancé la requête aux autres modules (pour qu'ils puissent traiter les données)
	 * $response = CopixEventNotifier::notify (new CopixEvent ('SomeEvent', array ('param'=>$param)));
	 * if ($response->inResponse ('failed', true)){
	 *     //un des modules nous indique ne pas être arrivé à traiter sa part de responsabilité
	 *     CopixDB::rollback ();
	 * } else {
	 *     //tout s'est bien passé
	 *     CopixDB::commit ();
	 * } 
	 * </code>
	 * 
	 * @param string $pResponseName Elément que l'on recherche dans les réponses
	 * @param mixed $pValue Valeur de l'élément que l'on veut tester
	 * @param array $pResponse Liste des réponses où l'on a trouvé la correspondance
	 * @return boolean Si l'on a trouvé l'élément ou non
	 */
	public function inResponse ($pResponseName, $pValue, &$pResponse) {
		$founded  = false;
		$pResponse = array ();
		foreach ($this->_responses as $key => $listenerResponse) {
			if (isset ($listenerResponse[$pResponseName]) && $listenerResponse[$pResponseName] == $pValue) {
				$founded = true;
				$pResponse[] = $this->_responses[$key];
			}
		}
		return $founded;
	}

	/**
	 * Récupère la liste de toutes les réponses retournées
	 * 
	 * @return array
	 */
	public function getResponse () {
		return $this->_responses;
	}
}