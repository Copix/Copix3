<?php
/**
 * @package		tools
 * @subpackage	compressor
 * @author       Ferlet Patrice
 * @copyright    2001-2006 CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */


/**
 * Pagination à partir d'un tableau de données.
 * @package		tools
 * @subpackage	compressor
 */
class CopixBzipFile {
	/**
	 * Buffer utilisé pour la compression / décompression
	 */
	private $_buffer = null;

	/**
	 * Constructeur
	 */
	public function __construct (){
		if (! @function_exists ('bzcompress')){
			throw new CopixException ('Compression BZ indisponible');
		}
	}

	/**
	 * Compression du contenu
	 * @param string content
	 * @return void
	 */
	public function compressContent($content){
		$this->_buffer = bzcompress($content);
	}

	/**
	 * Récupération du buffer de compression
	 * @return string encoded buffer
	 */
	public function getBuffer(){
		return $this->_buffer;
	}

	/**
	 * Décompression du contenu. Si path est donné les fichiers sont décompressés dans le répertoire en question, 
	 * sinon, ils sont retournés "en mémoire".
	 *
	 * @param filename, path where uncompress
	 * @return buffer uncompressed or boolean for save success if path given
	 */
	public function uncompress ($filename, $path=null){
		if(!($bz = bzopen ($filename, "r"))){
			return null;
		}
		$decompressed_file = '';
		while (!feof($bz)) {
			$decompressed_file .= bzread ($bz, 4096);
		}
		bzclose ($bz);
		if ($path){
			if($fp = fopen ($path,'w')){
				fwrite ($fp,$decompressed_file, strlen($decompressed_file));
				fclose ($fp);
				return true;
			}
			return false;
		}
		return $decompressed_file;
	}
}
?>