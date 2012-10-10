<?php
/**
 * @package copix
 * @subpackage utils
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Informations sur un thème
 * 
 * @package copix
 * @subpackage utils
 */
class CopixThemeDescription {
	/**
	 * Propriétés que l'on peut lire avec __get, et qui retournent un htmlentities, pour la compatibilité avec Copix 3.0.x
	 * 
	 * @var array
	 */
	private $_allowedGetHTMLEntities = array ('name', 'description', 'author', 'website');
	
	/**
	 * Propriétés que l'on peut lire avec __get, et qui retournent un htmlentities, pour la compatibilité avec Copix 3.0.x
	 * 
	 * @var array
	 */
	private $_allowedGet = array ('id', 'tpl', 'image');
	
	/**
	 * Identifiant, nom du répertoire
	 *
	 * @var string
	 */
	private $_id = null;
	
	/**
	 * Nom, node name dans theme.xml
	 *
	 * @var string
	 */
	private $_name = null;
	
	/**
	 * Description, node description dans theme.xml
	 *
	 * @var sting
	 */
	private $_description = null;
	
	/**
	 * Auteur, node author dans theme.xml
	 *
	 * @var string
	 */
	private $_author = null;
	
	/**
	 * Adresse du site de l'auteur, node website dans theme.xml
	 *
	 * @var string
	 */
	private $_website = null;
	
	/**
	 * ???
	 *
	 * @var string
	 */
	private $_tpl = null;
	
	/**
	 * Miniature de présentation, node image dans theme.xml
	 *
	 * @var string
	 */
	private $_image = null;
	
	/**
	 * Pour la compatibilité avec __get sur image, on stocke le _resource dans une autre propriété
	 *
	 * @var string
	 */
	private $_imageURL = null;
	
	/**
	 * Liste des templates, peut être vide
	 *
	 * @var array
	 */
	private $_templates = array ('main.php' => 'Gabarit principal');
	
	/**
	 * 
	 * Enter informations supplementaires du module
	 * @var xml
	 */
	private $_registry = null;
	
	/**
	 * Constructeur
	 *
	 * @param string $pId Identifiant du thème (nom du répertoire pour les thèmes locaux)
	 * @param string $pXMLPath Lien vers un fichier theme.xml qui contient des informations sur le thème
	 */
	public function __construct ($pId, $pXMLPath) {
		if (!is_readable ($pXMLPath)) {
			throw new CopixException (_i18n ('copix:copixthemedescription.error.xmlNotReadable', array ($pXMLPath, $pId)));
		}
		
		$this->_id = $pId;
		if (!($xml = @simplexml_load_file ($pXMLPath))) {
			throw new CopixException (_i18n ('copix:copixthemedescription.error.invalidXML', array ($pXMLPath, $pId)));
		}
		$this->_name = (isset ($xml->name)) ? (string)$xml->name : null;
		$this->_author = (isset ($xml->author)) ? (string)$xml->author : null;
		$this->_website = (isset ($xml->website)) ? (string)$xml->website : null;
		$this->_tpl = (isset ($xml->_tpl)) ? (string)$xml->_tpl : null;
		if (isset ($xml->image) && is_readable (_resourcePath ((string)$xml->image, $pId))) {
			$this->_image = (string)$xml->image;
			$this->_imageURL = _resource ((string)$xml->image, $pId);
		} else {
			$this->_image = 'img/theme.png';
			$this->_imageURL = _resource ('img/theme.png', $pId);
		}
		$this->_name = (isset ($xml->name)) ? (string)$xml->name : null;
		if (isset ($xml->description)) {
			$this->_description = (string)$xml->description;
		} else if (isset ($xml->descriptioni18n)) {
			$this->_description = _i18n ('theme:' . $pId . '|' . (string)$xml->descriptioni18n);
		}
		if (isset ($xml->templates)) {
			foreach ($xml->templates->template as $template) {
				$attributes = $template->attributes ();
				$this->_templates[(string)$attributes['file']] = (string)$template;
			}
		}
		$this->_registry = (isset ($xml->registry)) ? $xml->registry : null;
	}
	
	/**
	 * Dans Copix 3.0.x, on n'avait pas de méthodes, uniquement des propriétés d'un stdclass
	 * Le __get sert donc à garder cette compatibilité en lecture
	 * La compatibilité en écriture est conservée pour ce genre d'appel, mais les appels aux getteurs ne retournent pas les propriétés modifiées
	 *
	 * @param string $pVar Propriété dont on veut la valeur
	 * @return mixed
	 */
	public function __get ($pVar) {
		// propriétés qui n'ont pas besoin d'un htmentities
		if (in_array ($pVar, $this->_allowedGet)) {
			return $this->{'_' . $pVar};
		
		// propriétés qui ont besoin d'un htmentities
		} else if (in_array ($pVar, $this->_allowedGetHTMLEntities)) {
			return htmlentities (utf8_decode ($this->{'_' . $pVar}));
		}
	}
	
	/**
	 * Retourne l'identifiant du thème
	 *
	 * @return string
	 */
	public function getId () {
		return $this->_id;
	}
	
	/**
	 * Retourne le nom du thème
	 *
	 * @return string
	 */
	public function getName () {
		return $this->_name;
	}
	
	/**
	 * Retourne la description du thème
	 *
	 * @return string
	 */
	public function getDescription () {
		return $this->_description;
	}
	
	/**
	 * Retourne l'auteur du thème
	 *
	 * @return string
	 */
	public function getAuthor () {
		return $this->_author;
	}
	
	/**
	 * Retourne le site web de l'auteur
	 *
	 * @return string
	 */
	public function getWebSite () {
		return $this->_website;
	}
	
	/**
	 * ???
	 *
	 * @return string
	 */
	public function getTPL () {
		return $this->_tpl;
	}
	
	/**
	 * Retourne l'adresse de la miniature du thème
	 *
	 * @return string
	 */
	public function getImage () {
		// pour la compatibilité avec le __get sur image, on ne stocke pas le _resource dans $this->_image mais dans _imageURL
		return $this->_imageURL;
	}
	
	/**
	 * Retourne la liste des templates du thème
	 *
	 * @return array
	 */
	public function getTemplates () {
		return $this->_templates;
	}
	
	/**
	 * Retourne le registry
	 *
	 * @return xml
	 */
	public function getRegistry () {
		return $this->_registry;
	}
}