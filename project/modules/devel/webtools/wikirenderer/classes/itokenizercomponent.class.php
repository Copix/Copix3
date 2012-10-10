<?php
/**
 * @package		webtools
 * @subpackage	wikirenderer
 * @copyright	CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author		Julien SALLEYRON
 * @link		http://www.copix.org
 */

/**
 * Interface d'nu composant de wiki
 * 
 * @package		webtools
 * @subpackage	wikirenderer
 */
interface ITokenizerComponent {
	/**
	 * Lance le rendu du composant
	 *
	 * @param string $pText Le texte contenu dans le token
	 * @param Token $pToken Le token contenant
	 */
	public function render ($pText, $pToken);
	
	/**
	 * Retourne la taille du tag start
	 *
	 * @param string $pData La chaine que le composant a retourné a l'ouverture (peut servir dans le cas de startTag variable)
	 */
	public function getStartTagLength ($pData = null);
	
	/**
	 * Retourne la taille du tag start
	 *
	 * @param string $pData La chaine que le composant a retourné a l'ouverture (peut servir dans le cas de endTag variable)
	 */
	public function getEndTagLength ($pData = null);
	
	/**
	 * Retourne si la chaine commence par le startTag du composant
	 *
	 * @param string $pString
	 * @return mixed false ou le tag ouvrant
	 */
	public function getStartingTag ($pString);
	
	
	/**
	 * Retourne si la chaine commence par le endTag du composant
	 *
	 * @param string $pString
	 * @return mixed false ou le tag fermant
	 */
	public function getEndingTag ($pString, $pToken);
	
	/**
	 * Retourne une info pour savoir si l'interieur du tag doit etre parsé
	 *
	 */
	public function contentMustBeParse ();
	
	/**
	 * Retourne une info pour savoir si le tag peut contenir d'autre tag ou si il est fermé directement au moment de son ouverture
	 *
	 */
	public function isContainerComponent ();
	
	/**
	 * Retourne si ce tag est un caractère d'echappement
	 *
	 */
	public function isEscapeComponent ();
	
	/**
	 * Retourne le taille du tag ouvrant par défaut (null si on ne sais pas sa taille) sert pour le classement d'excution des composants
	 *
	 */
	public function getLength ();
	
}
 ?>