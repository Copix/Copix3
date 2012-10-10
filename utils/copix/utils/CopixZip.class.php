<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Nicolas Bastien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

if (!class_exists ('ZipArchive', false)) {
	$message = 'La librairie ZipArchive n\'existe pas.';
	$message .= '<br /><br />Les utilisateurs de Windows doivent activer la bibliothèque php_zip.dll dans le php.ini afin d\'utiliser ces fonctions.';
	$message .= '<br />Pour Linux avec PHP < 5.2.0, vous devez compiler PHP avec le support Zip en utilisant l\'option de configuration --with-zip[=DIR], où [DIR] est le préfixe de l\'installation de la bibliothèque ZZIPlib.';
	$message .= '<br />Pour Linux avec PHP >= 5.2.0, vous devez compiler PHP avec le support Zip en utilisant l\'option de configuration --enable-zip.';
	throw new CopixException ($message);
}

/**
 * Surcharge de ZipArchive afin de créer plus facile de archive de répertoire complet
 *
 * @package		copix
 * @subpackage	utils
 */
class CopixZip extends ZipArchive {

	private $_fileName = null;

	// Sur Linux, la destruction de l'objet provoque une erreur "segmentation fault"
	// Garder une référence jusqu'au dernier moment retarde l'erreur jusqu'**après** le dernier script
	private $_PHPZipBugFix = null;

	public function __construct($pZipName = null, $pZipComment = null) {
		$this->_PHPZipBugFix = $this;
		if (!is_null($pZipName)) {
			$this->_fileName = realpath( dirname( $pZipName ) ) . DIRECTORY_SEPARATOR . basename( $pZipName );
		}

		if (!is_null($pZipName) && $this->open($pZipName, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
			throw new CopixException("L'archive ne peut être créée");
		}

		if ($pZipComment) {
			$this->setArchiveComment($pZipComment);
		}
	}

	/**
	 * Archivage d'un répertoire complet au format zip
	 *
	 * @param string $pZipName le nom de l'archive à créer (chemin complet)
	 * @param string $pDirectoryName le nom du répertoire à archiver (chemin complet)
	 * @param string $pZipComment commentaire optionnel pour l'archive
	 * @return boolean
	 */
	public function zipDirectory($pZipName, $pDirectoryName, $pZipComment = null) {

		$this->_fileName = $pZipName;

		//Création de l'archive
		if ($this->open($pZipName, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
			return FALSE;
		}

		$this->_addDirectory($pDirectoryName, false);

		if (!is_null($pZipComment)) {
			$this->setArchiveComment($pZipComment);
		}
		return $this->close();
	}

	/**
	 * Ajout d'un répertoire à une archive existante
	 *
	 * @param array $pDirectoryName nom du répertoire
	 */
	public function addDirectory($pDirectoryName) {
		$this->_addDirectory($pDirectoryName);
		return $this;
	}

	/**
	 * Ajout du contenu d'un répertoire à une archive existante
	 *
	 * @param array $pDirectoryName nom du répertoire
	 */
	public function addDirectoryContent($pDirectoryName) {
		$this->_addDirectory($pDirectoryName, false);
		return $this;
	}

	/**
	 * Affichage des informations formattées pour l'appel via la ligne de commande
	 * @return void
	 */
	public function printInfosForCmdLine() {

		if ($this->open($this->_fileName) !== TRUE) {
			return FALSE;
		}
		echo '--- ' . basename($this->_fileName) . " ---\n";
		echo "  Nombre de fichiers archivés : " . $this->numFiles ."\n";
		echo "  Taille de l'archive : " . filesize($this->_fileName) . "\n";

		if ($this->getArchiveComment()) {
			echo "  Commentaire : " . $this->getArchiveComment() . "\n";
		}
		echo "------\n";
		$this->close();
	}

	/**
	 * Ajout du contenu d'un répertoire (récursif) à l'archive courante
	 *
	 * @param string $pDirectoryName nom du répertoire
	 * @param string $pPrefix prefixe courant, si = FALSE la racine ne sera pas inclue dans l'archive
	 */
	protected function _addDirectory($pDirectoryName, $pPrefix = '') {
		$pDirectoryName = CopixConfig::getRealPath ($pDirectoryName) . DIRECTORY_SEPARATOR;

		if (!file_exists($pDirectoryName)) {
			throw new CopixException("Erreur: Le chemin '$pDirectoryName' n'existe pas");
		}

		if (!is_dir($pDirectoryName)) {
			throw new CopixException("Erreur: Le chemin '$pDirectoryName' existe mais n'est pas un répertoire");
		}

		//Ajout du répertoire
		if ($pPrefix !== false) {
			$this->addEmptyDir($pPrefix . basename($pDirectoryName));
			$pPrefix = $pPrefix . basename($pDirectoryName) . DIRECTORY_SEPARATOR;
		}
		foreach (new DirectoryIterator($pDirectoryName) as $fileInfo) {
			if ($fileInfo->getFilename () == '.' || $fileInfo->getFilename () == '..') {
				continue;
			}
			if ($fileInfo->isDir()){
				$this->_addDirectory($fileInfo->getPathname  ());
				continue;
			}
			//Ajout du fichier
			if (!$this->addFile($fileInfo->getPathname  (), $pPrefix . $fileInfo->getFilename())) {
				throw new CopixException("Le fichier '{$fileInfo->getPathname ()}' n'a pu être ajouté à l'archive");
			}
		}
		return;
	}

}
