<?php
/**
 * @package		copix
 * @subpackage	cache
 * @author		Croës Gérald, Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Gère le cache en mode User (c'est a dire qu'il est vidé au niveau d'une déconnexion)
 * 
 * @package		copix
 * @subpackage	cache
 */
class CopixCacheUserStrategy implements ICopixCacheStrategy {
	
	private $_extra;
	public function __construct ($pExtra){
		$this->_extra = $pExtra;
	}

	/**
	 * Indique si le cache est actif
	 * 
	 * @return boolean
	 */
	public function isEnabled () {
		// ce système de cache sera toujours disponible, puisqu'il ne necessite rien de particulier pour fonctionner
		return true;
	}

	/**
	 * Lecture de données depuis le cache
	 *
	 * @param string $pId Identifiant de l'élément à récupérer 
	 * @param string $pType Type de cache ou récupérer les données
	 * @return string
	 * @throws CopixCacheException
	 */	
	public function read ($pId, $pType) {
		if ($return = CopixSession::get ($this->_makeFileName ($pId, $pType), 'CopixCacheUser')) {
			return $return->data;
		}
		throw new CopixCacheException (_i18n ('copix:copixcache.error.contentNotFound', $pId));
	}

	/**
	 * Détermine le nom de fichier du cache
	 * 
	 * @param string $pId Identifiant de l'élément à récupérer 
	 * @param string $pType Type de cache ou récupérer les données
	 */
	private function _makeFileName ($pId, $pType) {
		return md5 ($pId);
	}

	/**
	 * Enregistrement des éléments dans le cache
	 *
	 * @param string $pId Identifiant de l'élméent à écrire dans le cache 
	 * @param string $pType Type de cache ou écrire
	 * @param mixed $pContent Contenu
	 */	
	public function write ($pId, $pContent, $pType) {
	    $toSave = new StdClass();
	    $toSave->data  = $pContent;
	    $toSave->time  = time();
		CopixSession::set ($this->_makeFileName ($pId, $pType), $toSave, 'CopixCacheUser');
	}

	/**
	 * Teste l'existence du cache
	 *
	 * @param string $pId Identifiant du cache
	 * @param string $pType Type de cache
	 * @return boolean
	 */	
	public function exists ($pId, $pType) {
		if (($return = CopixSession::get ($this->_makeFileName ($pId, $pType), 'CopixCacheUser')) != null) {
			if ($this->_extra['duration'] === null || $this->_extra['duration'] == 0) {
				return true;
			}
			if ((time () - $return->time) < $this->_extra['duration']) {
				return true;
			} else {
				$this->clear ($pId, $pType);
			}
		}
		return false;
	}

	/**
	 * Supression des éléments du cache
	 * Si $pId = null tout le type (ou sous-type) passé en paramètre du constructeur est vidé
	 *
	 * @param string $pId Identifiant de l'élément à supprimer
	 * @param string $pType Type de cache
	 */
	public function clear ($pId, $pType) {
		if ($pId !== null) {
		    CopixSession::delete ($this->_makeFileName ($pId, $pType), 'CopixCacheUser');
		} else {
		    // Gestion fausse
			CopixSession::destroyNamespace ('CopixCacheUser');
		}
	}
}