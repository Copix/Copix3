<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croes Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe qui gère la possibilité de placer des objets en session sans se préocuper de 
 * la sérialisation / désérialisation.
 */
class CopixSerializableObject extends CopixClassProxy {
	/**
	 * Constante pour indiquer que cet objet fait partit de l'autoload
	 */
	const AUTOLOADED = 0;
	
	/**
	 * Le nom du fichier qui contient la définition de l'objet
	 * @var string
	 */
	protected $_fileName = null;
	
	/**
	 * Le type de l'élément (dao, class, object)
	 * @var string
	 */
	protected $_type = null;
	
	/**
   	 * Constructeur, l'objet et sa définition s'il y a lieu
   	 * @param	object	$pObject	l'objet à placer dans la session
   	 * @param	string	$pFileName	le chemin de la définition du fichier
   	 */
	public function __construct ($pObject, $pFileName = null){
		parent::__construct ($pObject);
	
		//Filename === null ? on essaye de déterminer automatiquement le type de l'élément.
		if ($pObject instanceof ICopixDAO){
			$this->_type = 'dao';
			if ($pFileName === null){
				$this->_fileName = $pObject->getDAOId ();
			}else{
				$this->_fileName = $pFileName;
			}
		}elseif ($pObject instanceof ICopixDAORecord){
			$this->_type = 'dao';
			if ($pFileName === null){
				$this->_fileName = $pObject->getDAOId ();
			}else{
				$this->_fileName = $pFileName;
			}
		}elseif ($pObject instanceof StdClass){
			$this->_type = 'stdclass';
		}elseif (($pFileName !== null) && (is_readable ($pFileName))){
			//On a spécifié un $pFileName et il est lisible, c'est donc un objet
			//libre
			$this->_type = 'object';
			$this->_fileName = $pFileName;
		}elseif (stripos (get_class ($pObject), "copix") === 0){
			$this->_type = 'autoload';
		}else{
			//soit on a spécifié un chemin et il n'est pas lisible, soit on a rien 
			//spécifié auquel cas on va considérer que la classe est dans un module normal de copix
			$this->_type = 'class';
			if (($this->_fileName = $pFileName) === null){
				$this->_fileName = CopixContext::get ().'|'.get_class ($pObject);
			}
		}			
   	}

   	/**
   	 * Avant la sérialisation (qui va se produire pour la session)
   	 * @return array	liste des propriétés à récupérer
   	 */
   	public function __sleep (){
   		if ($this->_type != 'autoload'){
   			$this->_object = serialize ($this->_object);
   		}
   		return array ('_object', '_fileName', '_type');
   	}
   	
   	/**
   	 * Après la sérialisation, pour une récupération correcte de l'objet
   	 */
   	public function __wakeup (){
   		switch ($this->_type){
   			case 'dao':
   				_daoInclude ($this->_fileName);
   				break;
   			case 'object':
   				Copix::RequireOnce ($this->_fileName);
   				break;
   			case 'class':
   				_classInclude ($this->_fileName);
   				break;
   			case 'stdclass':
   				break;
   		}
   		if ($this->_type != 'autoload'){
   			$this->_object = unserialize ($this->_object);
   		}
   	}
}
?>