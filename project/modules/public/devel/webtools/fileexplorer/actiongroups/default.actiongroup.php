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
 * Actions par défaut pour le module d'exploration de fichiers
 * @package webtools
 * @subpackage fileexplorer
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Contrôle les droits (il faut être administrateur)
	 */
	protected function _beforeAction (){
		CopixAuth::getCurrentUser()->assertCredential ('basic:admin');
	}

	/**
	 * Action par défaut : affichage des fichiers
	 */
	public function processDefault (){
		$ppo = new CopixPpo (array ('TITLE_PAGE'=>'Exploration des fichiers'));
		_classInclude ('filesiterator');
		$ppo->arFiles = new FilesIterator ($ppo->basePath = CopixFile::trailingSlash (realpath (_request ('path', './'))));
		$ppo->arFiles->getSortParams ()->setSortType (CopixSession::get ('fileexplorer|sortby'));
		$ppo->arFiles->getSortParams ()->setReverse (CopixSession::get ('fileexplorer|sortreverse'));
		$ppo->basePathDescription = new FileDescription ($ppo->basePath);
		$ppo->freeSpace = sprintf ("%d", disk_free_space ($ppo->basePath) / 1024);
		$ppo->totalSpace = sprintf ("%d", disk_total_space ($ppo->basePath) / 1024);
		$ppo->error = _request ('error');
		return _arPpo ($ppo, 'files.php');
	}
	
	/**
	 * Définition des critères de tri
	 */
	public function processSetSortParams (){
		_classInclude ('filesiterator');
		$lastSort = CopixSession::get ('fileexplorer|sortby');
		if (in_array ($sortBy = _request ('sortby'), array (FileSortParams::SIZE, FileSortParams::NAME, FileSortParams::NAME_INSENSITIVE, FileSortParams::TYPE))){
			CopixSession::set ('fileexplorer|sortby', $sortBy);
			if ($sortBy === $lastSort){
				CopixSession::set ('fileexplorer|sortreverse', !CopixSession::get ('fileexplorer|sortreverse'));
			}else{
				CopixSession::set ('fileexplorer|sortreverse', false);				
			}
		}
		return _arRedirect (_url ('default', array ('path'=>_request ('path'))));
	}

	/**
	 * Demande d'affichage d'un fichier 
	 */
	public function processShow (){
		require_once (CopixModule::getPath ('geshi').'lib/geshi/geshi.php');
		_classInclude ('filesiterator');
		$ppo = new CopixPpo ();
		$ppo->filePath = _request ('file');
		$ppo->fileDescription = new FileDescription ($ppo->filePath);
		$ppo->TITLE_PAGE = _i18n ('fileexplorer.showFile');
		switch ($ppo->type = $this->_convertType (substr (CopixFile::extractFileExt ($ppo->filePath), 1))){
			case 'image':
				$ppo->image = true;
				break;
			case 'document':
				$ppo->document = true;
				break;
			default:
				if (_request ('update')){
					$ppo->code = CopixFile::read ($ppo->filePath);
				}else{
					$geshi = new GeSHi(CopixFile::read ($ppo->filePath), $ppo->type);
					$geshi->set_header_type (GESHI_HEADER_DIV);
					$ppo->code = $geshi->parse_code ();
				}
				break;
		}
		return _arPpo ($ppo, _request ('update') ? 'update.php' : 'show.php');
	}
	
	/**
	 * demande de téléchargement d'un fichier
	 */
	public function processDownload (){
		return _arFile (_request ('file'));
	}
	
	/**
	 * Conversion du type de contenu en fonction de l'extension
	 */
	private function _convertType ($pExtension){
		if (in_array ($pExtension, array ('ptpl'))){
			return 'php';
		}

		if (in_array ($pExtension, array ('tpl'))){
			return 'smarty';
		}
		
		if (in_array ($pExtension, array ('gif', 'png', 'bmp', 'jpg', 'jpeg'))){
			return 'image';
		}

		if (in_array ($pExtension, array ('doc', 'xls', 'ppt', 'odt', 'pdf'))){
			return 'document';
		}		
		return $pExtension;
	}

	/**
	 * Supression d'un fichier
	 */
	public function processDelete (){
		CopixRequest::assert ('file');
		$fileName = _request ('file');
		_classInclude ('filesiterator');
		if (! _request ('confirm', false)){
			return CopixActionGroup::process ('generictools|Messages::getConfirm', 
				array ('message'=>_i18n ('fileexplorer.confirmDeleteFile', $fileName),
						'confirm'=>_url ('delete', array ('file'=>$fileName, 'confirm'=>1)),
						'cancel'=>_url ('default', array ('path'=>CopixFile::extractFilePath ($fileName)))));
		}

		CopixRequest::assert ('file');
		$fileDescription = new FileDescription ($fileName);
		if ($fileDescription->isDir ()){
			$done = CopixFile::removeDir ($fileName) === true;
		}else{
			$done = @CopixFile::delete ($fileName);
		}

		if (!$done){
			$error = _i18n ('fileexplorer.cannotdelete', $fileName);
		}else{
			$error = null;
		}

		return _arRedirect (_url ('default', array ('path'=>CopixFile::extractFilePath ($fileName), 'error'=>$error)));
	}

	/**
	 * Création d'un répertoire
	 */
	public function processCreateDir (){
		CopixRequest::assert ('dirname', 'path');
		if (is_writable (_request ('path'))){
			CopixFile::createDir (CopixFile::trailingSlash (_request ('path'))._request ('dirname'));
		}
		return _arRedirect (_url ('default', array ('path'=>_request ('path'))));
	}
	
	/**
	 * Compression d'un répertoire et téléchargement du fichier obtenu
	 */
	public function processCompress (){
		CopixRequest::assert ('path');
		$path = _request ('path');

		$basePath = COPIX_TEMP_PATH.'fileexplorer/archives/';
		$tempFileName = $basePath.uniqid ('c_');
		$archiveArg = escapeshellarg ($tempFileName);

		$archiveName = CopixFile::extractFileName ($path).CopixConfig::get ('compressExtension');
		CopixFile::createDir ($basePath);

		set_time_limit (0);
		exec (sprintf (CopixConfig::get ('compressCmdLine'), $archiveArg, $path));
		return _arFile ($tempFileName, array ('filename'=>$archiveName));
	}
	
	/**
	 * Ajout d'un fichier
	 */
	public function processUploadFile (){
		CopixRequest::assert ('path');
		$path = _request ('path');
		if (CopixRequest::getFile ('upload', $path) === false){
			return _arRedirect (_url ('default', array ('error'=>_i18n ('fileexplorer.cannotuploadfile'), 'path'=>$path)));
		}
		return _arRedirect (_url ('default', array ('path'=>$path)));  
	}
	
	/**
	 * Affichage des propriétés d'un fichier
	 */
	public function processProperties (){
		CopixRequest::assert ('file');
		$file = _request ('file');

		$ppo->TITLE_PAGE = _i18n ('fileexplorer.fileProperties');
		$ppo->file = new FileDescription ($file);		
		return _arPpo ($ppo, 'fileproperties.form.tpl');
	}
	
	/**
	 * Modification du contenu d'un fichier
	 */
	public function processValidFileContent (){
		CopixRequest::assert ('file', 'filecontent');
		$file = _request ('file');
		$content = _request ('filecontent');
		
		if (CopixFile::write ($file, $content)){
			return _arRedirect (_url ('show', array ('file'=>$file)));
		}
		throw new CopixException (_i18n ('fileexplorer.cannotWriteFile', $file));
	}

	/**
	 * Création d'un nouveau fichier
	 */
	public function processCreateFile (){
		CopixRequest::assert ('filename', 'path');
		$filename = _request ('filename');
		$filepath = _request ('path');

		if (CopixFile::write ($filepath.$filename, '')){
			return _arRedirect (_url ('show', array ('file'=>$filepath.$filename, 'update'=>1)));
		}
		throw new CopixException (_i18n ('fileexplorer.cannotCreateFile', $file));
	}
}
?>