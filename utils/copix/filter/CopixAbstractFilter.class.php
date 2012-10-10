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
 * Classe de base pour les filtres Copix
 * 
 * @package		copix
 * @subpackage	filter
 */
abstract class CopixAbstractFilter implements ICopixFilter {
	//--START OF <FIXME FOR BRANCH>
	//L'id�e est d'inclure directement dans CopixFilter les modifications apport�es a CopixParameterHandler pour ne
	//pas perturber le fonctionnement du reste des applications qui utilisent CopixParameterHandler

	/**
	 * Tableau de l'ensemble des param�tres
	 *
	 * @var array
	 */
	protected $_params = array ();

	/**
	 * D�finit les param�tres.
	 *
	 * @param array $pParams Nouveaux param�tres.
	 */
	public function setParams ($pParams) {
		$this->_params = $pParams;
	}

	/**
	 * Retourne l'ensemble des param�tres.
	 *
	 * @return array
	 */
	public function getParams () {
		return $this->_params;
	}

	/**
	 * R�cup�re un param�tre optionnel.
	 *
	 * Si $pName est un tableau, getParam() agit de fa�on r�cursive. Elle retourne un tableau
	 * de valeurs, une pour chaque entr�es de $pName ; la clef �tant le nom du param�tre.
	 * Il est alors possible de fournir un tableau de valeurs par d�faut, qui
	 * seront utilis�es dans la m�me ordre que $pName. Si $pDefault n'est pas un tableau, il 
	 * est utilis� comme valeur par d�faut de tous les param�tres. De la m�me fa�on, $pType
	 * peut �tre un tableau de type.
	 * 
	 * Attention, si $pName est un tableau, il ne peut contenir qu'une seule dimension sans quoi 
	 *  une erreur surviendra.
	 * 
	 * @param mixed   $pName    Nom du param�tre, ou tableau de noms de param�tres.
	 * @param mixed   $pDefault Valeur par d�faut, ou tableau de valeurs par d�faut.
	 * @param mixed   $pType    Type de la valeur, ou tableau de types par d�faut.
	 * @param boolean $pDefaultIfNotValidate Si vrai, retourne la valeur par d�faut si la validation ne passe pas au lieu de lancer une exception
	 * 
	 * @return mixed La valeur du param�tre ou un tableau des valeurs.
	 */
	public function getParam ($pName, $pDefault = null, $pType = null, $pDefaultIfNotValidate = false) {
		if (is_array ($pName)){
			$toReturn = array ();
			foreach ($pName as $name){
				$toReturn[$name] = $this->getParam ($name, 
				                                    isset ($pDefault[$name]) ? $pDefault[$name] : null, 
				                                    is_string ($pType) ? $pType : (isset ($pType[$name]) ? $pType[$name] : null));
			}
			return $toReturn;
		}else{
			//On regarde si le param�tre existe (pour �viter de cr�er un validateur inutilement)
			if (!array_key_exists ($pName, $this->_params)){
				return $pDefault;
			}
	
			//On regarde le type de la variable pour d�terminer le validateur � utiliser
			if ($pType !== null){
				if (is_string ($pType)){
					$validator = _validator ($pType);
				}elseif ($pType instanceof ICopixValidator){
					$validator = $pType;
				}else{
					throw new CopixException ('Le troisi�me param�tre a getParam doit repr�senter soit un identifiant de validateur, soit un validateur');
				}
	
				//on regarde si le param�tre est d'un type correct
				if ($validator->check ($this->_params[$pName]) !== true){
					if ($pDefaultIfNotValidate){
						return $pDefault;
					}
					throw new CopixParameterHandlerValidationException ($pName, $pType);
				}
			}
			return $this->_params[$pName];
		}
	}

	/**
	 * R�cup�re un param�tre obligatoire.
	 *
	 * Enregistre une erreur "missing" si le param�tre n'est pas d�fini.
	 *
	 * @param mixed pName Nom du param�tre, ou un tableau de noms de param�tres.
	 * @param mixed $pType Type de la valeur, ou un tableau des types de param�tres.
	 * @return mixed La valeur du param�tre ou null s'il n'est pas pr�sent.
	 */
	public function requireParam ($pName, $pType = null) {
		if (is_array ($pName)){
			foreach ($pName as $name){
				if (! array_key_exists ($name, $this->_params)){
					throw new CopixParameterHandlerMissingException ($name);			
				}				
			}
		}else{
			if (! array_key_exists ($pName, $this->_params)){
				throw new CopixParameterHandlerMissingException ($pName);			
			}
		}

		return $this->getParam ($pName, $pType);
	}
	
	/**
	 * V�rifie que les param�tres list�s sont bien pr�sents dans l'objet
	 * @param mixed $all liste des param�tres dont la pr�sence est a v�rifier 
	 */
	public function assertParams (){
		$missingKeys = array ();
		$keys = array_keys ($this->_params);
		foreach (func_get_args () as $varName) {
			if (!in_array ($varName, $keys)) {
				$missingKeys[] = $varName;
			}
		}
		if (count ($missingKeys)) {
			throw new CopixParameterHandlerMissingException ($missingKeys);
		}
	}
	//--END OF <FIXME FOR BRANCH>
	
	/**
	 * Construction de l'objet filtre
	 *
	 * @param unknown_type $pParams
	 */
	public function __construct ($pParams = array ()){
		$this->setParams ($pParams);
	}

	/**
	 * Modification de $pValue avec le filtre.
	 * 
	 * @param  mixed $pValue la valeur à modifier avec le filtre
	 * @return mixed la valeur modifiée  
	 */
	public function update (& $pValue){
		return $pValue = $this->get ($pValue);
	} 
}