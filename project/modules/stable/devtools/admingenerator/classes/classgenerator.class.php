<?php
/**
 * @package devtools
 * @subpackage admingenerator
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Génération d'une classe
 *
 * @package devtools
 * @subpackage admingenerator
 */
class ClassGenerator {
	/**
	 * Nom du module
	 * 
	 * @var string
	 */
	private $_module = null;

	/**
	 * Répertoire
	 *
	 * @var string
	 */
	private $_directory = null;

	/**
	 * Nom de la classe
	 *
	 * @var string
	 */
	private $_className = null;

	/**
	 * Propriétés
	 *
	 * @var array
	 */
	private $_properties = array ();

	/**
	 * Définit le nom du module
	 *
	 * @param string $pModule
	 */
	public function setModule ($pModule) {
		$this->_module = $pModule;
	}

	/**
	 * Retourne le nom du module
	 *
	 * @return string
	 */
	public function getModule () {
		return $this->_module;
	}

	/**
	 * Définit le répertoire
	 *
	 * @param string $pDirectory
	 */
	public function setDirectory ($pDirectory) {
		$this->_directory = $pDirectory;
	}

	/**
	 * Retourne le répertoire
	 *
	 * @return string
	 */
	public function getDirectory () {
		return $this->_directory;
	}

	/**
	 * Définit le nom de la classe
	 * 
	 * @param string $pClassName 
	 */
	public function setClassName ($pClassName) {
		$this->_className = $pClassName;
	}

	/**
	 * Retourne le nom de la classe
	 *
	 * @return string
	 */
	public function getClassName () {
		return $this->_className;
	}

	/**
	 * Définit les propriétés
	 *
	 * @param array $pProperties
	 */
	public function setProperties ($pProperties) {
		$this->_properties = $pProperties;
		foreach ($this->_properties as $name => &$infos) {
			if ($infos['value'] == null) {
				$infos['value'] = 'null';
			}
		}
	}

	/**
	 * Ajoute une propriété
	 *
	 * @param string $pName Nom
	 * @param string $pComment Commentaire
	 * @param string $pType Type (int, string, etc)
	 * @param string $pValue Valeur par défaut
	 */
	public function addProperty ($pName, $pComment, $pType, $pValue) {
		$this->_properties[$pName] = array (
			'comment' => $pComment,
			'type' => $pType,
			'value' => ($pValue == null) ? 'null' : $pValue
		);
	}

	/**
	 * retourne les propriétés
	 *
	 * @return array
	 */
	public function getProperties () {
		return $this->_properties;
	}

	/**
	 * Génère la classe
	 */
	public function generate () {
		if ($this->isValid () instanceof CopixErrorObject) {
			throw new AdminGeneratorException ('Objet non valide.');
		}

		$path = CopixFile::trailingSlash (CopixModule::getPath ($this->getModule ()) . 'classes/' . $this->getDirectory ());
		$file = $path . strtolower ($this->getClassName ()) . '.class.php';
		if (!is_dir ($path)) {
			CopixFile::createDir ($path);
		}

		$generator = _ioClass ('PHPGenerator');
		$php = new CopixPHPGenerator ();
		$content = $php->getLine ('<?php');
		$content .= $php->getLine ('class ' . $this->getClassName () . ' {');

		// propriétés
		foreach ($this->getProperties () as $name => $infos) {
			$content .= $php->getPHPDoc (array ($infos['comment'], null, '@var ' . $infos['type']), 1);
			$content .= $php->getLine ('private $_' . lcfirst ($name) . ' = ' . $infos['value'] . ';', 1, 2);
		}

		// méthodes
		$index = 0;
		$count = count ($this->getProperties ());
		foreach ($this->getProperties () as $name => $infos) {
			// setteur
			$content .= $generator->getPHP4Settor ('set' . ucfirst ($name), $infos['type'], lcfirst ($name), $infos['comment']);
			$content .= $php->getEndLine ();
			
			// getteur
			$content .= $generator->getPHP4Gettor ('get' . ucfirst ($name), $infos['type'], lcfirst ($name), $infos['comment']);
			$content .= $php->getEndLine (($index == $count - 1) ? 0 : 1);
			$index++;
		}

		$content .= '}';
		CopixFile::write ($file, $content);
		chmod ($file, 0777);
	}

	/**
	 * Indique si l'objet est valide
	 *
	 * @return mixed
	 */
	public function isValid () {
		return _validator ('admingenerator|classgeneratorvalidator')->check ($this);
	}
}