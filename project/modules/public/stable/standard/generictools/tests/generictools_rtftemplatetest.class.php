<?php
/**
* @package		standard 
 * @subpackage	generictools
* @author	Croes Gérald
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package		standard 
 * @subpackage	generictools
 */
class GenericTools_RTFTemplateTest extends CopixTest {
  function testSimpleRTFTemplate () {
     $template = CopixClassesFactory::create ('genericTools|RTFTemplate');

     $template->assign ('VARIABLE_1', 'Voici une belle présentation <b>avec des infos importantes</b> ', true);
     $template->assign ('VARIABLE_2', '<ul><li>puce 1</li><li>puce 2</li><li>puce 3</li></ul>', true);
     $template->assign ('VARIABLE_3', 'Contenu simple', true);

     $codeDuDocumentRTFFinal = $template->fetch ('generictools|rtftest.rtf');

     $selector = CopixSelectorFactory::create ('generictools|rtftest.expectedresult.rtf');
     $expectedResultFilePath = $selector->getPath (COPIX_TEMPLATES_DIR).$selector->fileName;
     $codeDuDocumentRTFTest = file_get_contents ($expectedResultFilePath);
     
     CopixFile::write ('/tmp/document_sortie.rtf', $codeDuDocumentRTFFinal);
     $this->assertEquals ($codeDuDocumentRTFFinal, $codeDuDocumentRTFTest);
  }
}
?>