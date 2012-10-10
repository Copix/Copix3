<?php
class CopixClassSelector {
	/**
	 * Le sélecteur normalisé qu'on a passé a la construction de la class
	 *
	 * @var unknown_type
	 */
	private $_normalizedSelector = null;

	/**
	 * Lorsque l'on construit directement un sélecteur de classe, il est complètement normalisé
	 * 
	 * @param CopixNormalizedSelector $pNormalizedSelector le sélecteur normalisé 
	 */
	public function __construct (CopixNormalizedSelector $pNormalizedSelector){
		$this->_normalizedSelector = $pNormalizedSelector;
	}

	/**
	 * Retourne le sélecteur "normalisé"
	 *
	 * @return string
	 */
	public function getNormalized (){
		return $this->_normalizedSelector->asString ();
	}
	
	/**
	 * Retourne le fichier a utiliser
	 *
	 * @return string
	 */
	public function getFilePath (){
		return $this->getPath ().strtolower ($this->_normalizedSelector->element).'.class.php';
	}
	
	/**
	 * Retourne le répertoire de classes
	 *
	 * @return string
	 */
	public function getPath (){
		return CopixModule::getPath ($this->_normalizedSelector->module).'classes/'.$this->_normalizedSelector->relativePath;
	}

	/**
	 * Retourne le nom de la classe
	 *
	 * @return string
	 */
	public function getClassName (){
		return $this->_normalizedSelector->element;
	}

	/**
	 * Retourne le module auquel appartient la classe
	 *
	 * @return unknown
	 */
	public function getModule (){
		return $this->_normalizedSelector->module;
	}
	
	/**
	 * Normalise un identifiant de classe (en fonction du contexte dans lequel on appel la méthode)
	 *
	 * @param string $pSelector 
	 * @return string
	 */
	public static function normalize ($pSelector){
		//Extraction du container s'il existe
		if (($pos = strpos ($pSelector, ':')) !== false) {
			$container = substr ($pSelector, 0, $pos);
			$selector = substr ($pSelector, $pos + 1);  
		}else{
			$container = 'module';
			$selector = $pSelector;
		}

		//on regarde si le container est sensible au contexte (pour savoir s'il est nécessaire de le calculer)
		if ($container === 'module'){
			if (($counted = count ($explodedContext = explode ('|', $selector))) === 2){
				$context  = $explodedContext[0]; 
				$selector = $explodedContext[1];
			}elseif ($counted === 1){
				$context = CopixContext::get ();
				//selector reste inchangé  
			}else{
				throw new CopixException ("Le sélecteur de classe [$pSelector] n'est pas valide. Format attendu : (container:[module|][chemin/sous_chemin/]element)");
			}
		}else{
			//Non sensible au contexte
			$context = null;
			throw new CopixException ("Le sélecteur de classe [$pSelector] n'est pas valide. Elles ne peuvent être situées que dans des modules.");
		}

		//on regarde s'il existe un chemin
		if (($pos = strrpos ($selector, '/')) !== false){
			$relativePath = substr ($selector, 0, $pos + 1);
			$selector = substr ($selector, $pos + 1);
		}else{
			$relativePath = '';
			//selector est inchangé
		}		

		return new CopixNormalizedSelector (array ('container'=>$container, 'module'=>$context, 'relativePath'=>$relativePath, 'element'=>$selector));
	}
}