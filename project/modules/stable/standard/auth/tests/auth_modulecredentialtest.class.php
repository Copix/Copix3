<?php
/**
* @package		standard
* @subpackage	auth
* @author		Salleyron Julien
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Test des droits par module de CopixAuth
 * @package		standard
 * @subpackage	auth
 */
class auth_moduleCredentialTest extends CopixTest {

    //Tableau servant a la destruction finale des enregistrements pour les tests unitaires
    private $_arMC = array();
    private $_arMCG = array();
    private $_arMCV = array();

    /**
     * Vide les enregistrements créé
     */
    public function __destruct () {
        
        foreach ($this->_arMC as $mc) {
            _dao('modulecredentials')->delete($mc);
        }
        
        foreach ($this->_arMCG as $mcg) {
            _dao('modulecredentialsgroups')->delete($mcg);
        }
        
        foreach ($this->_arMCV as $mcv) {
            _dao('modulecredentialsvalues')->delete($mcv);
        }
    }

    /**
     * Test de droit simple (sans level, sans sous level)
     *
     */
    public function testSimpleCredentiel () {
        _classInclude('auth|dbmodulegrouphandler');
        

        //Creation du droit lecteur
        $record1            = _record('modulecredentials');
        $record1->module_mc = 'test_news';
        $record1->name_mc   = 'lecteur';
        _dao ('modulecredentials')->insert($record1);
        
        $this->_arMC[] = $id = $record1->id_mc;
        
        //Creation du groupe pour test
        $record2 = _record('modulecredentialsgroups');
        $record2->id_mc = $id;
        $record2->handler_group = 'test|test';
        $record2->id_group = '1';
        _dao ('modulecredentialsgroups')->insert ($record2);
        
        $this->_arMCG[] = $record2->id_mcg;
         
        //Avec droit spécifique au module
	    $handlerCredential = new dbmodulegrouphandler ('test|test','1');
        $this->assertTrue($handlerCredential->isOk ('lecteur@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ('lecteur'));
        $this->assertTrue(!$handlerCredential->isOk ('lecteur@test'));
        $handlerCredential = new dbmodulegrouphandler ('test|aaaa','1');
        $this->assertTrue(!$handlerCredential->isOk ('lecteur@test_news'));
        $handlerCredential = new dbmodulegrouphandler ('test|test','2');
        $this->assertTrue(!$handlerCredential->isOk ('lecteur@test_news'));
        
        $record1            = _record('modulecredentials');
        $record1->module_mc = null;
        $record1->name_mc   = 'test_notspecific';
        _dao ('modulecredentials')->insert($record1);
        
        $this->_arMC[] = $id = $record1->id_mc;
        
        $record2 = _record('modulecredentialsgroups');
        $record2->id_mc         = $id;
        $record2->module_mc = null;
        $record2->handler_group = 'test|aaaa';
        $record2->id_group      = '1';
        _dao ('modulecredentialsgroups')->insert ($record2);
        
        $this->_arMCG[] = $record2->id_mcg;
        
        //Avec droit non spécifique au module
	    $handlerCredential = new dbmodulegrouphandler ('test|aaaa','1');
        $this->assertTrue($handlerCredential->isOk ('test_notspecific'));
        $this->assertTrue(!$handlerCredential->isOk ('test_notspecific@test'));
        $this->assertTrue(!$handlerCredential->isOk ('test_notspecific@test_news'));
        
        $handlerCredential = new dbmodulegrouphandler ('test|aaaa','2');
        $this->assertTrue(!$handlerCredential->isOk ( 'test_notspecific'));
        $handlerCredential = new dbmodulegrouphandler ('test|eeee','1');
        $this->assertTrue(!$handlerCredential->isOk ('test_notspecific'));
        
    }
    
    public function testValueCredential () {
        _classInclude('auth|dbmodulegrouphandler');
        
        //Creation du droit lecteur
        $record_mc            = _record('modulecredentials');
        $record_mc->module_mc = 'test_news';
        $record_mc->name_mc   = 'commentaires';
        _dao ('modulecredentials')->insert($record_mc);
        
        $this->_arMC[] = $id = $record_mc->id_mc;
        
        $record_mcv = _record('modulecredentialsvalues');
        $record_mcv->id_mc = $id;
        $record_mcv->value_mcv = 'lecture';
        _dao ('modulecredentialsvalues')->insert($record_mcv);
        
        $this->_arMCV[] = $id_mcv = $record_mcv->id_mcv;
        
        //Creation du groupe pour test
        $record2 = _record('modulecredentialsgroups');
        $record2->id_mc = $id;
        $record2->handler_group = 'test|test';
        $record2->id_group = '1';
        $record2->id_mcv = $id_mcv;
        _dao ('modulecredentialsgroups')->insert ($record2);
        
        $this->_arMCG[] = $record2->id_mcg;
        
        $handlerCredential = new dbmodulegrouphandler ('test|test','1');
        $this->assertTrue($handlerCredential->isOk ( 'commentaires|lecture@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|ecriture@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|lecture@test'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|lecture'));
        
        
        
        
        //Creation du droit lecteur
        $record_mc            = _record('modulecredentials');
        $record_mc->name_mc   = 'commentaires_nomodule';
        _dao ('modulecredentials')->insert($record_mc);
        
        $this->_arMC[] = $id = $record_mc->id_mc;
        
        $record_mcv = _record('modulecredentialsvalues');
        $record_mcv->id_mc = $id;
        $record_mcv->value_mcv = 'test';
        _dao ('modulecredentialsvalues')->insert($record_mcv);
        
        $this->_arMCV[] = $id_mcv = $record_mcv->id_mcv;
        
        //Creation du groupe pour test
        $record2 = _record('modulecredentialsgroups');
        $record2->id_mc = $id;
        $record2->handler_group = 'test|test';
        $record2->id_group = '1';
        $record2->id_mcv = $id_mcv;
        _dao ('modulecredentialsgroups')->insert ($record2);
        
        $this->_arMCG[] = $record2->id_mcg;
        
        $handlerCredential = new dbmodulegrouphandler ('test|test','1');
        $this->assertTrue($handlerCredential->isOk ( 'commentaires_nomodule|test'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires_nomodule|test@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires_nomodule|ecriture@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires_nomodule|test@test'));
        
    }
    
    public function testValueCredentialLevel () {
         _classInclude('auth|dbmodulegrouphandler');

        
        //Creation du droit lecteur
        $record_mc            = _record('modulecredentials');
        $record_mc->module_mc = 'test_news';
        $record_mc->name_mc   = 'commentaires';
        _dao ('modulecredentials')->insert($record_mc);
        
        $this->_arMC[] = $id = $record_mc->id_mc;
        
        
        $record_mcv = _record('modulecredentialsvalues');
        $record_mcv->id_mc = $id;
        $record_mcv->value_mcv = 'null';
        _dao ('modulecredentialsvalues')->insert($record_mcv);
        $this->_arMCV[] = $id_null = $record_mcv->id_mcv;
        
        $record_mcv = _record('modulecredentialsvalues');
        $record_mcv->id_mc = $id;
        $record_mcv->value_mcv = 'lecture';
        $record_mcv->level_mcv = 1;
        _dao ('modulecredentialsvalues')->insert($record_mcv);
        $this->_arMCV[] = $id_lecture = $record_mcv->id_mcv;
        
        $record_mcv = _record('modulecredentialsvalues');
        $record_mcv->id_mc = $id;
        $record_mcv->value_mcv = 'lecture2';
        $record_mcv->level_mcv = 1;
        _dao ('modulecredentialsvalues')->insert($record_mcv);
        $this->_arMCV[] = $id_lecture2 = $record_mcv->id_mcv;
        
        
        $record_mcv = _record('modulecredentialsvalues');
        $record_mcv->id_mc = $id;
        $record_mcv->value_mcv = 'ecriture';
        $record_mcv->level_mcv = 2;
        _dao ('modulecredentialsvalues')->insert($record_mcv);
        $this->_arMCV[] = $id_ecriture = $record_mcv->id_mcv;
        
        $record_mcv = _record('modulecredentialsvalues');
        $record_mcv->id_mc = $id;
        $record_mcv->value_mcv = 'moderation';
        $record_mcv->level_mcv = 3;
        _dao ('modulecredentialsvalues')->insert($record_mcv);
        $this->_arMCV[] = $id_moderation = $record_mcv->id_mcv;
        
        
        //Creation du groupe pour test
        $record2 = _record('modulecredentialsgroups');
        $record2->id_mc = $id;
        $record2->handler_group = 'test|null';
        $record2->id_group = '1';
        $record2->id_mcv = $id_null;
        _dao ('modulecredentialsgroups')->insert ($record2);
        
        //Creation du groupe pour test
        $record2 = _record('modulecredentialsgroups');
        $record2->id_mc = $id;
        $record2->handler_group = 'test|lecture';
        $record2->id_group = '1';
        $record2->id_mcv = $id_lecture;
        _dao ('modulecredentialsgroups')->insert ($record2);

        $this->_arMCG[] = $record2->id_mcg;
        
        //Creation du groupe pour test
        $record2 = _record('modulecredentialsgroups');
        $record2->id_mc = $id;
        $record2->handler_group = 'test|lecture2';
        $record2->id_group = '1';
        $record2->id_mcv = $id_lecture2;
        _dao ('modulecredentialsgroups')->insert ($record2);
        
        $this->_arMCG[] = $record2->id_mcg;
        
        //Creation du groupe pour test
        $record2 = _record('modulecredentialsgroups');
        $record2->id_mc = $id;
        $record2->handler_group = 'test|ecriture';
        $record2->id_group = '1';
        $record2->id_mcv = $id_ecriture;
        _dao ('modulecredentialsgroups')->insert ($record2);
        
        $this->_arMCG[] = $record2->id_mcg;
        
        //Creation du groupe pour test
        $record2 = _record('modulecredentialsgroups');
        $record2->id_mc = $id;
        $record2->handler_group = 'test|moderation';
        $record2->id_group = '1';
        $record2->id_mcv = $id_moderation;
        _dao ('modulecredentialsgroups')->insert ($record2);
        
        $this->_arMCG[] = $record2->id_mcg;
        
        $handlerCredential = new dbmodulegrouphandler ('test|null','1');
        $this->assertTrue($handlerCredential->isOk ( 'commentaires|null@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|lecture2@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|ecriture@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|moderation@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|lecture@test'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|ecriture@test'));
        
        $handlerCredential = new dbmodulegrouphandler ('test|lecture','1');
        $this->assertTrue($handlerCredential->isOk ( 'commentaires|lecture@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|lecture2@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|ecriture@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|moderation@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|lecture@test'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|ecriture@test'));

        
        $handlerCredential = new dbmodulegrouphandler ('test|lecture2','1');
        $this->assertTrue($handlerCredential->isOk ( 'commentaires|lecture2@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|lecture1@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|ecriture@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|moderation@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|lecture2@test'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|ecriture@test'));
        
        $handlerCredential = new dbmodulegrouphandler ('test|ecriture','1');
        $this->assertTrue($handlerCredential->isOk ( 'commentaires|lecture@test_news'));
        $this->assertTrue($handlerCredential->isOk ( 'commentaires|lecture2@test_news'));
        $this->assertTrue($handlerCredential->isOk ( 'commentaires|ecriture@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|null@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|moderation@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|lecture@test'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|ecriture@test'));

        
        $handlerCredential = new dbmodulegrouphandler ('test|moderation','1');
        $this->assertTrue($handlerCredential->isOk ( 'commentaires|lecture@test_news'));
        $this->assertTrue($handlerCredential->isOk ( 'commentaires|lecture2@test_news'));
        $this->assertTrue($handlerCredential->isOk ( 'commentaires|ecriture@test_news'));
        $this->assertTrue($handlerCredential->isOk ( 'commentaires|moderation@test_news'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|lecture@test'));
        $this->assertTrue(!$handlerCredential->isOk ( 'commentaires|ecriture@test'));

    }
    
}
?>