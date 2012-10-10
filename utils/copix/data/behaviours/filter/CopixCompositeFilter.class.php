<?php
/**
 * @package		copix
 * @subpackage	filter
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Filtre composite (applique successivement plusieurs filtres)
 * 
 * @package		copix
 * @subpackage	filter
 */
class CopixCompositeFilter extends CopixAbstractFilter {
	/**
	 * Filtres associés au composite
	 *
	 * @var array
	 */
	protected $_filters = array ();

	/**
	 * Construction 
	 *
	 * @param array $pParams liste des validateurs a appliquer
	 *             
	 * Si on donne plus d'un paramètre a la fonction, alors chaque paramètre doit être 
	 * un ICopixFilter a ajouter au composite 
	 */
	public function __construct ($pParams = array ()){
		//C'est un peu mal de faire ça, mais bon....
		parent::__construct ($pParams);
		
		foreach ($this->getParams () as $arg){
			$this->attach ($arg);
		}
	}
	
	/**
	 * Récupération de la valeur filtrée
	 *
	 * @param mixed $pValue la valeur a filtrer
	 */
	public function get ($pValue){
		foreach ($this->_filters as $filter){
			$pValue = $filter->get ($pValue);
		}
		return $pValue;
	}
	
	/**
	 * Attache un filtre supplémentaire a appliquer
	 *
	 * @param ICopixFilter $pFilter
	 * @return ICopixFilter $this
	 */
	public function attach ($pFilter){
		if (is_array ($pFilter)){
			foreach ($pFilter as $filter){
				$this->attach ($filter);
			}
			return $this;
		}elseif (is_object ($pFilter)){
			if ($pFilter instanceof ICopixFilter){
				$this->_filters[] = $pFilter;
				return $this; 				
			}else{
				throw new CopixException (_i18n ('[CopixCompositeFilter] attach accepts an array of ICopixFilter an ICopixFilter or a string representing a CopixFilter ID'));
			}
		}elseif (is_string ($pFilter)){
            $this->_filters[] = _filter ($pFilter);
            return $this;
	    }
		throw new CopixException (_i18n ('[CopixCompositeFilter] attach accepts an array of ICopixFilter an ICopixFilter or a string representing a CopixFilter ID'));
	}
}