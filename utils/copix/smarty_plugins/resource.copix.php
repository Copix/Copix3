<?php
/**
* @package 		copix
* @subpackage	smarty_plugins
* @author		Daspet Eric
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Fetches templates from copix file selector
*/
function smarty_resource_copix_source ($tpl_name, &$tpl_source, &$smarty)
{
    // do database call here to fetch your template,
    // populating $tpl_source
	$file = copix_extract_file_path ($tpl_name, COPIX_TEMPLATES_DIR) ;
	if ($file && $fp = fopen($file, 'r')) {
		$tpl_source = fread($fp, filesize($file)) ;
		return true;
	} else {
        return false;
    }
}

function smarty_resource_copix_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
    // do database call here to populate $tpl_timestamp.
	$file = copix_extract_file_path ($tpl_name, COPIX_TEMPLATES_DIR) ;
	$tpl_timestamp = filemtime($file) ;
	return true ;
}

function smarty_resource_copix_secure($tpl_name, &$smarty)
{
    // assume all templates are secure
    return true;
}

function smarty_resource_copix_trusted($tpl_name, &$smarty)
{
    // not used for templates
}

/**
* creates a filePath from a given string module|file and from the given
* type of the file (zone, template, static)
* @param    string  $fileId     "nom de fichier" ou "nom de module|nom de fichier"
* @param    string  $subDir     nom de répertoire relatif (en principe une des valeur COPIX_xxx_DIR definie dans project.inc.php)
* @param    string  $extension
* @return   string  chemin du fichier indiqué ou false si inconnu
*/
 function copix_extract_file_path ($fileId, $subDir , $extension = '') {
	$fileInfo = CopixSelectorFactory::create ($fileId);
	$fileName = $fileInfo->fileName;
	if($extension != ''){
		$fileName = strtolower($fileName).$extension;
	}
	$moduleFile = $fileInfo ->getPath($subDir) . $fileName;
	$projectOverloadedFilePath = $fileInfo->getOverloadedPath($subDir);
	if($projectOverloadedFilePath !== null){
		$projectOverloadedFilePath.=$fileName;
		if(is_readable($projectOverloadedFilePath))
		return $projectOverloadedFilePath;
	}
	if(is_readable($moduleFile)){
		return $moduleFile;
	}else{
		return false;
	}
}
?>