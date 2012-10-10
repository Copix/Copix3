<?php
/**
 * @package standard
 * @subpackage test
 * @author	Gérald Croës
 * @copyright CopixTeam
 * @link      http://copix.org
 * @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe destinée à tester le bon fonctionnement de CopixServices
 * @package standard
 * @subpackage test
 */
class ServicesTest extends CopixServices {
	public function deleteCopixTestMain (){
		_doQuery ('delete from testmain');
		if ($this->getParam ('fail') != null){
			throw new CopixException ('Demandé a échouer, on échoue');
		}
	}
}