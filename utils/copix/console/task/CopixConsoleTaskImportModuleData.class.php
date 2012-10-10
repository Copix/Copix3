<?php
/**
 * @package		copix
 * @subpackage	console
 * @author		Nicolas Bastien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

/**
 * Importe les données d'un module (fichier et données en base)
 *
 * Cette tâche charge les données situés dans COPIX_PROJECT_PATH / data / import
 *
 * @package		copix
 * @subpackage	console
 */
class CopixConsoleTaskImportModuleData extends CopixConsoleAbstractTask {

	public $description = "Import des donnees d'un module dont le nom est fourni en parametre";
	
	/**
	 * (non-PHPdoc)
	 * @see action/CopixConsoleAbstractTask#execute()
	 */
	public function execute() {
		echo "[import-module-data] Import des donnees : \n";
		$path = COPIX_PROJECT_DATA_PATH . 'import' . DIRECTORY_SEPARATOR;
		
		try {
			$DI = new DirectoryIterator($path);
		} catch ( Exception $e ){
			echo "\n";
			echo "Erreur: Le dossier '$path' n'est pas lisible.\n";
			exit();
		}

		$i = 0;
		//Récupération des archives à importer
		foreach (new DirectoryIterator(COPIX_PROJECT_DATA_PATH . 'import/') as $fileInfo) {
			if($fileInfo->isDot() || substr($fileInfo->getFilename(),0,1) == '.') continue;
			$ext = CopixFile::extractFileExt($fileInfo->getFilename());
			if ($ext != '.zip'){continue;}
			echo "\tImport du module {$fileInfo->getFilename()}\n";
			$this->_importZipFile($fileInfo->getFilename());
			$i++;
		}

		echo "[import-module-data] termine ($i modules importes)\n";
	}

	/**
	 * Importe les données et fichiers contenu dans $pZipName.
	 * $pZipName générer à partir de la tâche d'export de module
	 *
	 * @param string $pZipName nom de l'archive à importer
	 */
	private function _importZipFile($pZipName) {

		echo "\tImport de '$pZipName'\n";

		//extraction de l'archive
		echo "\textraction de l'archive\n";
		$tmpDirectory = COPIX_PROJECT_DATA_PATH . 'import' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
		$zip = new CopixZip(COPIX_PROJECT_DATA_PATH . 'import' . DIRECTORY_SEPARATOR . $pZipName);
		$zip->extractTo($tmpDirectory);
		$zip->close();

		//import sql
		if (file_exists($tmpDirectory . 'data.sql')) {
			echo "\timport des donnees sql...\n";
			CopixDB::getConnection()->doSQLScript ($tmpDirectory . 'data.sql');
			echo "\timport des donnees terminee.\n";
		}

		//copie des fichiers
		if (file_exists($tmpDirectory . 'files.zip')) {
			echo "\tCopie des fichiers...\n";
			$zip = new CopixZip($tmpDirectory . 'files.zip');
			$zip->extractTo(COPIX_VAR_PATH);
			$zip->close();
			echo "\tCopie des fichiers terminee\n";
		}

		//Suppression des fichiers temporaire
		echo "\tSuppression des fichiers temporaire... \n";
		CopixFile::removeDir($tmpDirectory);
		echo "\tSuppression des fichiers temporaire terminee\n";

		//Suppression de l'archive
		echo "\tSuppression de l'archive\n";
		CopixFile::delete(COPIX_PROJECT_DATA_PATH . 'import' . DIRECTORY_SEPARATOR . $pZipName);

		echo "\tImport de '$pZipName' terminee.\n";
	}
}

