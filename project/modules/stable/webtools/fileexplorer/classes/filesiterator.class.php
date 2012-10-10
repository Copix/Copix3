<?php
/**
 * @package		webtools
 * @subpackage	fileexplorer
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Itérateurs de fichiers et répertoires avec diverses possibilités de tri
 * @package		webtools
 * @subpackage	fileexplorer
 */
class FilesIterator implements Iterator, ArrayAccess, Countable { 
	/**
	 * Le filter a appliquer durant la recherche des fichiers
	 * @var string
	 */
	protected $_filter = '*';
	
	/**
	 * S'il faut parcourir les sous éléments
	 * @var boolean
	 */
	protected $_recursive = false;
	
	/**
	 * Le chemin de base de l'iterateur
	 * @var string
	 */
	private $_path = null;
	
	/**
	 * L'iterateur de tableau interne 
	 * @var ArrayIterator
	 */
	private $_arrayIterator = null;
	
	/**
	 * Indique si le paramétrage de l'iterateur à changé, pour savoir s'il est nécessaire de reparcourir 
	 * les fichiers
	 * @var boolean
	 */
	private $_changed = true;
	
	/**
	 * Les critères de tri
	 * @var FileSortParams
	 */
	private $_sortParams = null;

	/**
	 * Construction de l'iterateur avec les paramètres de filtre
	 *
	 * @param string $pPath	le chemin de parcours
	 * @param string $pFilterParameters la restriction de parcours
	 */
	function __construct ($pPath, $pFilter = '*', $pRecursive = false){
		$this->_path = $pPath;
		$this->setFilter ($pFilter);
		$this->setRecursive ($pRecursive);
		$this->_sortParams = new FileSortParams ();
	}
	
	/**
	 * Définition d'un filtre 
	 * @param	string	$pFilter	le filtre à appliquer sur l'iterateur
	 */
	public function setFilter ($pFilter){
		if ($pFilter != $this->_filter){
			$this->_filter = $pFilter;
			$this->_changed = true;
		}
	}

	/**
	 * Définit si l'on doit rechercher de façon récursive ou non
	 * @param 
	 */
	public function setRecursive ($pRecursive){
		if ($pRecursive != $this->_recursive){
			$this->_recursive = $pRecursive;
			$this->_changed = true;
		}	
	}

	/**
	 * Remplissage de l'itérateur
	 */
	private function _fillIterator (){
		$elements = array ();
		foreach (glob ($this->_calcPath ()) as $element){
			$elements[] = new FileDescription ($element);
		}
		usort ($elements, array ($this->_sortParams, 'compare'));
		$this->_arrayIterator = new ArrayIterator ($elements);
		$this->_changed = false;
	}
	
	/**
	 * Retourne le chemin à utiliser pour la fonction glob
	 * @return string 
	 */
	private function _calcPath (){
		return CopixFile::trailingSlash ($this->_path).$this->_filter;
	}

	/**
	 * Retourne le nombre d'éléments
	 * @return int
	 */
	public function count (){
		if ($this->_changed){
			$this->_fillIterator ();
		}
		return count ($this->_arrayIterator);
	}
	
	/**
	 * Rembobine l'iterateur
	 */
	public function rewind (){
		if ($this->_changed){
			$this->_fillIterator ();
		}
		return $this->_arrayIterator->rewind ();
	}
	
	/**
	 * Retourne l'élément courant.
	 */
	public function current (){
		if ($this->_changed){
			$this->_fillIterator ();
		}
		return $this->_arrayIterator->current ();
	}
	
	/**
	 * Retourne la clef courante
	 */
	public function key (){
		if ($this->_changed){
			$this->_fillIterator ();
		}
		return $this->_arrayIterator->key ();
	}
	
	/**
	 * Passe à l'élément suivant 
	 */
	public function next (){
		if ($this->_changed){
			$this->_fillIterator ();
		}
		return $this->_arrayIterator->next ();
	}
	
	/**
	 * indique si l'élément courant est ok
	 */
	public function valid (){
		if ($this->_changed){
			$this->_fillIterator ();
		}
		return $this->_arrayIterator->valid ();
	}
	
	/**
	 * Indique si l'élement d'offset donné existe dans le tableau
	 * @param int $pOffset l'offset à tester
	 * @return boolean
	 */
	public function offsetExists ($pOffset){
		if ($this->_changed){
			$this->_fillIterator ();
		}				
		return $this->_arrayIterator->offsetExists ($pOffset);
	}
	
	/**
	 * Récupère l'élement d'indice donné 
	 *
	 * @param int $pOffset l'offset de l'élément que l'on souhaite récupérer	
	 */
	public function offsetGet ($pOffset){
		if ($this->_changed){
			$this->_fillIterator ();
		}
		return $this->_arrayIterator->offsetGet ($pOffset);
	}
	
	/**
	 * Définition d'un élément dans un indice donné
	 *
	 * @param int $pOffset l'offset dans lequel on met l'élément
	 */
	public function offsetSet ($pOffset, $pValue){
		if ($this->_changed){
			$this->_fillIterator ();
		}
		$this->_arrayIterator->offsetSet ($pOffset, $pValue);
	}
	
	/**
	 * Supprime un élément à l'offset indiqué
	 *
	 * @param mixed 	$pOffset	L'offset que l'on supprime du tableau
	 */
	public function offsetUnset ($pOffset){
		if ($this->_changed){
			$this->_fillIterator ();
		}
		return $this->_arrayIterator->offsetUnset ($pOffset);
	}
	
	/**
	 * retourne les critères de tri des éléments
	 * @return FileSortParams
	 */
	public function getSortParams (){
		return $this->_sortParams;
	}
}

/**
 * Informations sur un fichier
 */
class FileDescription {
	/**
	 * C'est un répertoire 
	 */
	const DIRECTORY = 1;
	
	/**
	 *  C'est un fichier
	 */
	const FILE = 2;
	
	/**
	 * Type de l'élément
	 * @var const
	 */
	private $_fileType = false;	
	
	/**
	 * Le chemin du fichier
	 * @var string
	 */
	protected $_filepath = null;
	
	/**
	 * Construction de l'objet filepath
	 * @param unknown_type $pFilePath
	 */
	function __construct ($pFilePath){
		$this->_filepath = $pFilePath;
	}
	
	/**
	 * Indique si l'élément courant est un fichier
	 * @return boolean
	 */
	function isFile (){
		return $this->getType () === self::FILE;
	}
	
	/**
	 * Retourne la taille de l'élément courant.
	 * @return int
	 */
	function getSize (){
		if ($this->isFile ()){
			return filesize ($this->_filepath);
		}
		return null; 
	}
	
	/**
	 * Indique si l'élément courant est un répertoire
	 * @return boolean 
	 */
	function isDir (){
		return $this->getType () === self::DIRECTORY;
	}
	
	/**
	 * Retourne le nom du fichier sans son chemin
	 * @return string
	 */
	function getFileName (){
		return CopixFile::extractFileName ($this->_filepath);
	}
	
	/**
	 * Récupère les permissions sur le fichier
	 * @return string
	 */
	function getPermissions (){
		$perms = fileperms ($this->_filepath);
		
		if (($perms & 0xC000) == 0xC000) {
		    // Socket
		    $info = 's';
		} elseif (($perms & 0xA000) == 0xA000) {
		    // Lien symbolique
		    $info = 'l';
		} elseif (($perms & 0x8000) == 0x8000) {
		    // Régulier
		    $info = '-';
		} elseif (($perms & 0x6000) == 0x6000) {
		    // Bloc spécial
		    $info = 'b';
		} elseif (($perms & 0x4000) == 0x4000) {
		    // Dossier
		    $info = 'd';
		} elseif (($perms & 0x2000) == 0x2000) {
		    // Caractère spécial
		    $info = 'c';
		} elseif (($perms & 0x1000) == 0x1000) {
		    // FIFO pipe
		    $info = 'p';
		} else {
		    // Inconnu
		    $info = 'u';
		}
		
		// Propriétaire
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
		            (($perms & 0x0800) ? 's' : 'x' ) :
		            (($perms & 0x0800) ? 'S' : '-'));
		
		// Groupe
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
		            (($perms & 0x0400) ? 's' : 'x' ) :
		            (($perms & 0x0400) ? 'S' : '-'));
		
		// Tous
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
		            (($perms & 0x0200) ? 't' : 'x' ) :
		            (($perms & 0x0200) ? 'T' : '-'));
		
		return $info;		
	}
	
	/**
	 * Retourne le nom de l'utilisateur 
	 * @return string
	 */
	function getOwner (){
		if(function_exists('posix_getpwuid')){
			$ownerInformations = posix_getpwuid (fileowner ($this->_filepath));
			return $ownerInformations['name'];
		}else{
			return null;
		}
	}
	
	/**
	 * Récupère le groupe du fichier
	 * @return string
	 */
	function getGroup (){
		if(function_exists('posix_getgrgid')){
			$groupInformations = posix_getgrgid (filegroup ($this->_filepath));
			return $groupInformations['name'];
		}else{
			return null;
		}
	} 
	
	/**
	 * Retourne le nom de l'extension
	 * @return string
	 */
	function getFileExtension (){
		return CopixFile::extractFileExt ($this->_filepath);
	}

	/**
	 * Retourne le nom de l'icone
	 */
	function getFileIcon (){
		switch (strtolower (CopixFile::extractFileExt ($this->_filepath))) {
			case '.gif':
			case '.png':
			case '.jpg':
			case '.jpeg':
			case '.bmp':
				return '|img/mimetypes/image.png';
			case '.doc':
			case '.odt':
				return '|img/mimetypes/office-document.png';
			case '.txt':
				return '|img/mimetypes/text.png';
			case '.sh':
				return '|img/mimetypes/script.png';
			case '.xls':
				return '|img/mimetypes/office-speadshit.png';
			case '.ppt':
			case '.pps':
			case '.odp':
			case '.sxi':
			case '.fodp':
				return '|img/mimetypes/office-presentation.png';
			case '.odg':
			case '.sxd':
				return '|img/mimetypes/office-drawing.png';
			case '.zip':
			case '.gz':
			case '.bz2':
			case '.rar':
				return '|img/mimetypes/archive.png';
			case '.php':
			case '.php5':
			case '.php4':
			case '.php3':
			case '.ptpl':
				return '|img/mimetypes/php.png';
			case '.html':
			case '.xhtml':
			case '.htm':
				return '|img/mimetypes/html.png';
			case '.tpl':
				return '|img/mimetypes/text-template.png';
			case '.avi':
			case '.mpg':
			case '.wmv':
			case '.mp4':
				return '|img/mimetypes/video.png';
			case '.wav':
			case '.mp3':
			case '.ogg':
			case '.wma':
				return '|img/mimetypes/audio.png';
			case '.exe':
				return '|img/mimetypes/executable.png';
			default :
				return '|img/mimetypes/unknown.png';
		}
	}

	/**
	 * Retourne le chemin complet du fichier
	 * @return string
	 */
	function getFilePath (){
		return $this->_filepath;
	}
	
	/**
	 * Retourne la date de dernière modification
	 * @return string
	 */
	function getLastUpdateDate (){
		return CopixDateTime::timestampToDateTime (filemtime ($this->_filepath));
	}
	
	/**
	 * Retourne la date de dernier accès
	 * @return string
	 */
	function getLastAccessDate (){
		return CopixDateTime::timestampToDateTime (fileatime ($this->_filepath));
	}
	
	/**
	 * Conversion de l'objet en chaine de caractères. Par défaut juste le nom du fichier
	 * @return string
	 */
	function __toString (){
		return $this->getFileName ();
	}
	
	/**
	 * Indique si l'élément peut être modifié
	 * @return boolean
	 */
	function isWritable (){
		return is_writable ($this->_filepath);
	}
	
	/**
	 * Indique si c'est un fichier (self::FILE) ou un répertoire (self::DIRECTORY)
	 *
	 * @return unknown
	 */
	function getType (){
		if (!$this->_fileType){
			if (is_dir($this->_filepath)){
				$this->_fileType = self::DIRECTORY;
			}else{
				$this->_fileType = self::FILE;
			}
		}
		return $this->_fileType;
	}
}

/**
 * Classe de définition de tri
 */
class FileSortParams {
	/**
	 * Indique s'il faut séparer les répertoire des fichiers
	 * @var boolean
	 */
	private $_separateDirFromFiles = true;
	
	/**
	 * Type de tri a appliquer
	 */
	private $_sortType = 5;
	
	/**
	 * Indique l'ordre du tri (normal false ou inverse true)
	 * @var boolean
	 */
	private $_reverse = false;

	/**
	 * Tri par taille du fichier
	 */
	const SIZE = 2;

	/**
	 * Tri par type de fichier
	 */
	const TYPE = 3;
	
	/**
	 * Tri par nom de fichier, sensible à la casse
	 */
	const NAME = 4;
	
	/**
	 * Tri par nom de fichier, insensible à la casse
	 */
	const NAME_INSENSITIVE = 5;

	/**
	 * On compare deux élément en fonction des critères de tri demandés
	 *
	 * @param FileDescription $pFirstFile
	 * @param FileDescription $pSecondFile
	 * @return int
	 */
	public function compare ($pFirst, $pSecond){
		return $this->_reverse ? $this->_compare ($pSecond, $pFirst) : $this->_compare ($pFirst, $pSecond);
	}	
	/**
	 * Compare deux fichiers pour savoir lequel afficher en premier
	 * Ne prends pas en charge l'option reverse.
	 * 
	 * @param FileDescription $pFirstFile
	 * @param FileDescription $pSecondFile
	 * @return int
	 */
	private function _compare (FileDescription $pFirstFile, FileDescription $pSecondFile){
		if ($this->_separateDirFromFiles){
			if ($pFirstFile->getType () != $pSecondFile->getType ()){
				return $pFirstFile->isDir () ? -1 : 1;
			}
		}
		switch ($this->_sortType){
			case self::SIZE:
				if ($pFirstFile->isDir ()){//les deux sont donc des répertoires
					break;
				}
				return $pFirstFile->getSize () > $pSecondFile->getSize () ? 1 : -1;
			case self::TYPE:
				return strcasecmp ($pFirstFile->getType (), $pSecondFile->getType ()) > 0 ? 1 : -1;

			case self::NAME:
				return strcmp ($pFirstFile->getFileName (), $pSecondFile->getFileName ()) > 0 ? 1 : -1;
		}
		//par défaut insensitive
		return strcasecmp ($pFirstFile->getFileName (), $pSecondFile->getFileName ()) > 0 ? 1 : -1;
	}
	
	/**
	 * Définition du type de tri
	 *
	 * @param const $pSortType	type de tri voulu
	 */
	function setSortType ($pSortType){
		if (in_array ($pSortType, array (self::NAME, self::NAME_INSENSITIVE, self::SIZE, self::TYPE))){
			$this->_sortType = $pSortType;	
		}
	}
	
	/**
	 * Définition du caractère inverse ou non du type de tri
	 *
	 * @param boolean $pReverse
	 */
	function setReverse ($pReverse){
		$this->_reverse = (boolean) $pReverse; 
	}
}
?>