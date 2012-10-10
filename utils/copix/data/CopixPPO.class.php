<?php
/**
 * @package copix
 * @subpackage core
 * @author Croes Gérald
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de transport d'informations (utiliser pour les actions, le transfère de paramètres, etc...)
 * 
 * @package copix
 * @subpackage core
 */
class CopixPPO implements ArrayAccess {
	/**
	 * Retourne l'élément où sauvegarder la donnée
	 * 
	 * @param string $propertyName Nom de la propriété à récupérer
	 * @return mixed
	 */
	public function &__get ($pName) {
		return $this->$pName;
	}
	
	/**
	 * Surcharge de isset pour que les sous éléments "CopixPPO" soient correctement détectés comme vide lorsqu'il y a lieu
	 *
	 * @param string $propertyName Nom de la proriété
	 * @return boolean
	 */
	public function __isset ($pName) {
		if ($this->$pName instanceof CopixPpo) {
			$test = get_object_vars ($this->$pName);
			return !empty ($test);
		}
		return isset ($this->$pName); 
	}

	/**
	 * Constructeur
	 * 
	 * @param array $pArInit Tableau de variables pour initialiser le ppo
	 */
	public function __construct ($pArInit = array ()) {
		//FIXME Je ne savais pas dans quelle classe mettre cette fonction, 
		// alors je l'ai copié / collé, extraite de smarty_plugin_modifier_toarray
		if (is_string ($pArInit)){
			$exploded = explode (';', $pArInit);
			$array = array ();
		
			foreach ($exploded as $item){
				$item = explode ('=>', $item);
				if (count ($item) == 2){
					$array[$item[0]] = $item[1];
				}else{
					$array[] = $item[0];
				}
			}
			$pArInit = $array; 
		}		

		if (is_array ($pArInit)) {
			foreach ($pArInit as $key => $item) {
				$this->$key = $item;
			}
		} else if (is_object ($pArInit)) {
			foreach (get_object_vars ($pArInit) as $key => $item){
				$this->$key = $item;
			}
		}
	}
	
	/**
	 * Implémentation de ArrayAccess, pour la récupération de $pOffset
	 *
	 * @param string $pOffset Offset à lire
	 * @return mixed
	 */
	public function offsetGet ($pOffset) {
		return $this->$pOffset;
	}
	
	/**
	 * Implémentation de ArrayAccess, pour la définition de $pOffset
	 *
	 * @param string $pOffset Offset à écrire
	 * @param mixed $pValue
	 */
	public function offsetSet ($pOffset, $pValue) {
		if ($pOffset === null) {
			$vars = get_object_vars ($this);
			if (count ($vars) === 0) {
				$pOffset = 0;
			} else {
		   		$pOffset = max (array_keys (get_object_vars ($this))) + 1;
			}
		}
		$this->$pOffset = $pValue;
	}
	
	/**
	 * Implémentation de ArrayAccess, vérifie l'existance de $pOffset
	 *
	 * @param string $pOffset Offet à vérifier
	 * @return boolean
	 */
	public function offsetExists ($pOffset) {
		return isset ($this->$pOffset); 
	}
	
	/**
	 * Implémentation de ArrayAccess, supprime $pOffset
	 *
	 * @param string $pOffset Offset à supprimer
	 */
	public function offsetUnset ($pOffset) {
		$this->$pOffset = null;
	}
	
	/**
	 * En cas de demande d'affichage directe
	 * 
	 * @return string
	 */
	public function __toString () {
		return '';
	}
	
	
	/**
	 * Merge, assigne les propriétés de l'objet en cours avec celles d'un autre objet/tableau/element. Les propriétés existantes ne sont pas ajoutées
	 * 
	 * @param mixed $pToMerge Objet à ajouter
	 * @return CopixPPO
	 */
	public function merge ($pToMerge) {
		if (is_array ($pToMerge)) {
			$pToMerge = new CopixPPO ($pToMerge);
		} else if (is_object ($pToMerge)) {
			if (!($pToMerge instanceof CopixPPO)) {
				$pToMerge = new CopixPPO (get_object_vars ($pToMerge));
			}			
		} else {
			$pToMerge = array ($pToMerge);
		}

		foreach (get_object_vars ($pToMerge) as $name => $prop) {
			if (!isset ($this->$name)){
				$this->$name = $prop;
			}
		}
		return $this;
	}
	
	/**
	 * Chargement de données a partir d'un objet / tableau
	 *
	 * @param mixed  $pData      les données à charger
	 * @param bool   $pCreateNew s'il faut créer ou non les propriétés qui n'existent pas dans l'objet courant
	 * @return this mis à jour
	 */
	public function loadFrom ($pData, $pCreateNew = true){
		_ppo ($pData)->saveIn ($this, $pCreateNew);
		return $this;
	}
	
	/**
	 * Applique toutes les propriétés de l'objet PPO dans les propriétés de l'objet cible
	 * 
	 * @param object $pDest Objet de destination
	 * @param boolean $pCreateNew Indique si on veut créer un nouvel objet
	 */
	public function saveIn (&$pDest, $pCreateNew = true) {
		//détermine le "type" de l'objet
		if (!($array = is_array ($pDest))) {
			if (!($object = is_object ($pDest))) {
                //type naturel, on l'écrase
                $pDest = _ppo();
                return $this->saveIn($pDest);
			}
		}
		$elementVars = array ();
		if ($array || $object) {
			$elementVars = array_keys ($this->_getElementVars ($pDest));
		}

		//on parcours chacune des propriétés de l'élément
		foreach (get_object_vars ($this) as $name => $element) {
			//on regarde si la propriété existe dans la destination
			if (($inArray = in_array ($name, $elementVars)) || $pCreateNew) {
		    	if ($inArray && (is_object ($element) || is_array ($element))) {
		    		//la propriété existait déja et c'est un tableau ou un objet, 
		    		//on reparcours le tout pour y appliquer les changements
		    		if ($array) {
		    			_ppo ($element)->saveIn ($pDest[$name], $pCreateNew);
		    		} else {
		    			_ppo ($element)->saveIn ($pDest->$name, $pCreateNew);
		    		}
		    		// NOTE : il n'est pas possible d'avoir recours a l'opérateur
		    		// ternaire pour les passages par référence
		    	} else {
		    		// la propriété n'existait pas => il faut la créer a l'identique
		    		// ou la propriété existait sous sa forme naturelle et il faut la remplacer
			    	if ($array) {
		    			$pDest[$name] = $element;
			    	} else {
			    		$pDest->$name = $element;
			    	}
		    	}
			}
		}
		
		return $this;
	}
	
	/**
	 * Récupération des clefs/valeur d'un tableau ou des propriétés/valeurs d'un objet
	 * 
	 * @param array/object $pElement Elément dont on souhaite connaitre les clefs / valeurs
	 * @return array
	 */
	protected function _getElementVars ($pElement) {
		return (is_array ($pElement)) ? $pElement : get_object_vars ($pElement);
	}
}