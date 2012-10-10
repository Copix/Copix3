<?php
/**
 * @package		copix
 * @subpackage	cache
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Interface commune aux stratégies de cache
 * 
 * @package 	copix
 * @subpackage	cache
 */
interface ICopixCacheStrategy {
	/**
	 * Indique si la stratégie est active ou non (certaines stratégies peuvent demander la présence de librairies externes)
	 * 
	 * @return boolean
	 */
	public function isEnabled ();
	
	/**
	 * Constructeur qui accepte les paramètres de configuration
	 */
	public function __construct ($pExtra);
	
	/**
	 * Ecriture dans le cache
	 * 
	 * @param string $pId l'identifiant de l'élément à mettre dans le cache
	 * @param mixed $pContent le contenu à mettre dans le cache
	 * @param string $pType le type de cache dans lequel on souhaite stocker l'élément
     */
	public function write ($pId, $pContent, $pType);

	/**
	 * Lecture depuis le cache
	 *
	 * @param string $pId Identifiant du cache que l'on souhaite récupérer
	 * @param string $pType	Type de cache depuis lequel on souhaite lire
	 * @return mixed Contenu du cache
	 * @throws CopixCacheException si l'élément n'est pas trouvé
	 */
	public function read ($pId, $pType);

	/**
	 * Supprime du contenu dans le cache
	 * 
	 * @param string $pId Identifiant de l'élément à supprimer du cache. Si null, tout le type est supprimé
	 * @param string $pType	Type de cache depuis lequel on va supprimer les éléments
	 */
	public function clear ($pId, $pType);

	/**
	 * Indique si un élément existe dans le cache
	 *
	 * @param string $pId Identifiant de l'élément dans le cache
	 * @param string $pType	Type de cache dans lequel on va tester la présence de l'élément
     * @return boolean 
	 */
	public function exists ($pId, $pType);
}