<?php
/**
 * @package		tools
 * @subpackage	compressor
 * @author       Ferlet Patrice
 * @copyright    2001-2006 CopixTeam
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Pagination à partir d'un tableau de données.
 * @package		tools
 * @subpackage	compressor
 */
class CopixCompressorFactory{
	/**
	 * Creates instances of compressor by type, and includes required files
	 * @param string type (zip, bzip or gzip)
	 * @return instance of compressor object
	 */
	public function create($type){
		switch (strtoupper($type)){
			case "ZIP":
				return _class ('compressor|CopixZipFile');
				break;
			case "BZ":
				return _class ('compressor|CopixBZipFile');
				break;
			case "GZ":
				return _class ('compressor|CopixGZipFile');
				break;
			default:
				return null;
		}
		return null;
	}
}
?>