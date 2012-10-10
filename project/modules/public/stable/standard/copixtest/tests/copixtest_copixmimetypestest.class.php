<?php
/**
* @package		standard
* @subpackage	copixtest
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package		standard
 * @subpackage	copixtest
 */
class CopixTest_CopixMIMETypesTest extends CopixTest {

	public function testFromFileName (){
		$this->assertEquals (CopixMIMETypes::getFromFileName ('c:\mon\fichier/avec/un/truc long/et des accents é/myFile.txt')
		, 'text/plain');
		$this->assertEquals (CopixMIMETypes::getFromFileName ('c:\mon\fichier.files/avec/un/truc long/et des accents é/myFile.txt')
		, 'text/plain');
		$this->assertEquals (CopixMIMETypes::getFromFileName ('c:\mon\fichier.files/avec/un/truc long/et des accents é/myFile')
		, 'application/octet-stream');
		$this->assertEquals (CopixMIMETypes::getFromFileName ('/avec/un/truc long/et des accents é/myFile.xls')
		, 'application/vnd.ms-excel');

	}

	public function testFromFileExtension (){
		$this->assertEquals (CopixMIMETypes::getFromExtension('.txt'), 'text/plain');
		$this->assertEquals (CopixMIMETypes::getFromExtension('.ai'), 'application/postscript');
		$this->assertEquals (CopixMIMETypes::getFromExtension('doc'), 'application/msword');
		$this->assertEquals (CopixMIMETypes::getFromExtension(''), 'application/octet-stream');
 	}
 	
 	public function testSeveralExt (){
 		$types = array ('jpg', 'pdf', 'doc', 'rtf', 'latex', 'aif', 'wav', 'gif', 'zip', 'gz', 'txt', 'htm', 
 		'ogg', 'xhtml', 'xml', 'ppt', 'pps', 'chm', 'js', 'exe', 'bat', 'mp3', 'wma');
 		foreach ($types as $name){
 			if (CopixMIMETypes::getFromExtension('.'.$name) == 'application/octet-stream'){
 				$this->fail ($name);
 			}
 		}
 	}
}
?>