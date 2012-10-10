<?php
/**
* @package		standard
* @subpackage	copixtest
* @author		Benguigui Landry
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Classe de test pour les logs
 * @package		standard
 * @subpackage	copixtest
 */
class CopixTest_CopixLogTest extends CopixTest {

    function testLog() {
        $profil = "copixLogFile";
        $message = "test ceci est une erreur";
        $level = CopixLog::ERROR;
        
		CopixLog::deleteProfile ("production");
        
		CopixLog::log ($message, $profil, CopixLog::ERROR);
		CopixLog::log ($message, $profil, CopixLog::WARNING);        		
	
		/*$iterator = CopixLog::getLog('copixLogFile',CopixLog::ERROR);
		
		$this->assertEquals ($profil,  $iterator[0]->profil);
		$this->assertEquals ($message, $iterator[0]->message);
		$this->assertEquals ($level,   $iterator[0]->level);
		
		CopixLog::deleteProfil ("copixLogSession");*/
		
        $profil = "copixLogSession";
        $message = "test ceci est une erreur";
        $level = CopixLog::ERROR;
		CopixLog::log ($message, $profil, CopixLog::ERROR);        		
		CopixLog::log ($message, $profil, CopixLog::WARNING);		
		CopixLog::log ($message, "testDb", CopixLog::WARNING);
		CopixLog::log ($message, "testSession", CopixLog::WARNING);
		
/*$iterator = CopixLog::getLog('copixLogFile',CopixLog::ERROR);
		
		$this->assertEquals ($profil,  $iterator[0]->profil);
		$this->assertEquals ($message, $iterator[0]->message);
		$this->assertEquals ($level,   $iterator[0]->level);
				
		CopixLog::deleteProfil('copixLogDb');*/
		
		$profil = "copixLogDb";
        $message = "test ceci est une erreur";
        $level = CopixLog::ERROR;
        
		CopixLog::log ($message, $profil, CopixLog::ERROR);        		
		CopixLog::log (':test ceci est un warning', $profil, CopixLog::WARNING);        		
		CopixLog::log ('monLog:test ceci est une fatal error', $profil, CopixLog::FATAL_ERROR);
		
	/*	$iterator = CopixLog::getLog('copixLogDb',CopixLog::ERROR);
		
		$this->assertEquals ($profil, $iterator[0]->profil);
		$this->assertEquals ($message, $iterator[0]->message);
		$this->assertEquals ($level, $iterator[0]->level);
		*/
		$iterator = CopixLog::getLog('production', CopixLog::ERROR);
		$this->assertTrue (true);
    }
}
?>