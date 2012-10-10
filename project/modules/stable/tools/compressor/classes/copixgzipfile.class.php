<?php
/**
 * @package		tools
 * @subpackage	compressor
 * @author       Ferlet Patrice
 * @copyright    2001-2006 CopixTeam
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Compression au format GZip
 * @package		tools
 * @subpackage	compressor
 */
class CopixGzipFile {
	var $buffer=null;

	/**
	 * Construction de l'objet
	 */
	public function __construct (){
		if(! function_exists ('gzencode')){
			throw new CopixException ('Compression GZ indisponible');
		}
	}

	/**
	 * Compress some content into gz
	 * @param string content, int compression
	 * @return void
	 */
	public function compressContent ($content, $compression = "9"){
		$this->buffer = gzencode ($content, $compression);
	}

	/**
	 * Return the encoded buffer
	 * @param void
	 * @return string encoded buffer
	 */
	public function getBuffer (){
		return $this->buffer;
	}

	/**
	 * TEST , try to uncompress a file and return the content or save on server
	 * @param filename, path where uncompress
	 * @return buffer uncompressed or boolean for save success if path given
	 */
	public function uncompress ($filename, $path = null){
		if ($path){
			if ($fp = fopen ($path,'w')){
				fwrite ($fp, implode ('', gzfile($filename)), strlen (implode ('', gzfile($filename))));
				fclose ($fp);
				return true;
			}
			return false;
		}
		return implode('', gzfile($filename));
	}
}
?>