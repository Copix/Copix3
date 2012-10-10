<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Gérald Croes
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * permet de récupèrer un objet selecteur selon le type du selecteur
 * un selecteur dans copix permet de spécifier un fichier/composant à utiliser et le module dan
 * lequel il se trouve.
 * le format d'un selecteur : "type:module|fichier"
 * @package copix
 * @subpackage core
 */
class CopixSelectorFactory {
	/**
	 * Type module qui représente une ressource située dans un module
	 *
	 * C'est le sélecteur par défaut si rien n'est spécifié
	 */
	const MODULE	= 'module:';

	/**
	 * Type fichier qui représente un chemin absolu
	 */
	const FILE		= 'file:';

	/**
	 * Type var qui représente des ressources situées dans le répertoire de variable de Copix
	 */
	const VAR_PATH = 'var:';

	/**
	 * Type copix qui représente des ressources situées dans le répertoire de Copix lui même
	 */
	const COPIX = 'copix:';

	/*
	 * Type Resource qui représente un élément situé dans la partie WWW
	 */
	const RESOURCE = 'resource:';

	/**
	 * Type theme qui représente les ressources situées dans le répertoire d'un thème/XX/resources
	 */
	const THEME = 'theme:';

	/**
	 * Liste des sélecteurs déjà créés en mémoire
	 */
	private static $_selector = array ();

	/**
	 * On ne veut pas que cette classe soit instanciée
	 */
	private function __construct (){}

	/**
	 * Création d'un sélecteur
	 * @param string $id l'identifiant Copix de l'élément.
	 * @return CopixFileSelector
	 */
	public static function create ($id, $type = null){
		if (substr ($id, 0, 5) !== 'file:'){
			$id = strtolower ($id);
		}
		$context = CopixContext::get ();

		//Regarde si cela existe déjà dans le cache
		if (isset (self::$_selector[$context][$id])){
			return self::$_selector[$context][$id];
		}

		if (($colon = strpos ($id, ':')) !== false) {
			switch (substr ($id, 0, $colon + 1)) {
				case self::COPIX:
					self::$_selector[$context][$id] = new CopixCopixFileSelector ($id);
					break;

				case self::FILE:
					self::$_selector[$context][$id] = new CopixFileFileSelector ($id);
					break;

				case self::VAR_PATH:
					self::$_selector[$context][$id] = new CopixVarFileSelector($id);
					break;
						
				case self::THEME:
					self::$_selector[$context][$id] = new CopixThemeFileSelector ($id);
					break;

				default:
					throw new CopixException (_i18n ('copix:copix.error.unknownSelector', $id));
			}
		}else{
			if (strpos($id, '|') === false){
				$fullId = $context.'|'.$id;
			}else{
				$fullId = $id;
			}

			//on regarde dans le cache avec l'identifiant complet
			if (isset (self::$_selector[$context][$fullId])){
				self::$_selector[$context][$id] = self::$_selector[$context][$fullId];
				return self::$_selector[$context][$id];
			}
			self::$_selector[$context][$id] = new CopixModuleFileSelector($fullId);
			self::$_selector[$context][$fullId] = self::$_selector[$context][$id];
		}
		return self::$_selector[$context][$id];
	}

	protected static $_cache = array ();
	/**
	 *
	 * @return CopixZoneSelector
	 */
	public static function getZone ($pId){
		//On regarde si le sélecteur existe en cache "tel quel"
		if (isset (self::$_cache['zone'][$pId])){
			return self::$_cache['zone'][$pId];
		}

		//On regarde si la version normalisée du sélecteur existe en cache
		$normalizedId = CopixZoneSelector::normalize ($pId);
		$normalizedIdString = $normalizedId->asString ();
		if (isset (self::$_cache['zone'][$normalizedIdString])){
			return self::$_cache['zone'][$normalizedIdString];
		}
		return self::$_cache['zone'][$normalizedIdString] = new CopixZoneSelector ($normalizedId);
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $pId
	 * @return CopixClassSelector
	 */
	public static function getClass ($pId){
		//On regarde si le sélecteur existe en cache "tel quel"
		if (isset (self::$_cache['class'][$pId])){
			return self::$_cache['class'][$pId];
		}

		//On regarde si la version normalisée du sélecteur existe en cache
		$normalizedId = CopixClassSelector::normalize ($pId);
		$normalizedIdString = $normalizedId->asString ();
		if (isset (self::$_cache['class'][$normalizedIdString])){
			return self::$_cache['class'][$normalizedIdString];
		}
		return self::$_cache['class'][$normalizedIdString] = new CopixClassSelector ($normalizedId);
	}

	/**
	 * Supression des caractèrse inutiles pour la description de la classe a considérer l'autoload mis en place
	 *
	 * @param string $pId
	 * @return string
	 */
	public static function purge ($pId){
		if (($pos = strrpos ($pId, '/')) !== false){
			$pId = substr ($pId, $pos+1);
		}
		if (($pos = strrpos ($pId, '\\')) !== false){
			$pId = substr ($pId, $pos+1);
		}
		if (($pos = strrpos ($pId, '|')) !== false){
			$pId = substr ($pId, $pos+1);
		}
		if (($pos = strrpos ($pId, ':')) !== false){
			$pId = substr ($pId, $pos+1);
		}
		return strtolower ($pId);
	} 
}