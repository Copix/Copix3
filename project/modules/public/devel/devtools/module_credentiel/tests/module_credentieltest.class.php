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
 * Test de la classes CopixAuth
 * @package		standard
 * @subpackage	copixtest
 */
class Module_Credentieltest extends CopixTest {
	
/*	function aaaaa (){
	    
//"INSERT INTO modulecredentials (id_mc , module_mc , name_mc) VALUES (1, 'news', 'commentaires'), (2, 'news', 'lecteur'), (3, 'news', 'ecrivain'), (4, 'news', 'moderateur'), (5, null, 'administrateur');"

//"INSERT INTO modulecredentialsvalues VALUES ('news','lecture',1, 1), ('news', 'ecriture',1, 2), ('news', 'moderation',1, 3);"

//"INSERT INTO modulecredentialsoverpass VALUES (3, null, 'commentaires', 'news'), (5, null, 'lecteur', 'news'), (5, null, 'ecrivain', 'news'), (5, null, 'moderateur', 'news'), (5, null, 'commentaires', 'news');"

//"INSERT INTO modulecredentialsgroups VALUES (2, null, 'auth|dbgrouphandler', '2');"

        //Creation du droit commentaires
        $record            = _record('modulecredentials');
        $record->module_mc = 'test_news';
        $record->name_mc   = 'commentaires';
        _dao ('modulecredentials')->insert($record);
        
        $commentaires_id = $record->id_mc;
        
        //Creation de la value lecture (1) pour commentaires
        $record = _record('modulecredentialsvalues');
        $record->module_mcv = 'test_news';
        $record->value_mcv  = 'lecture';
        $record->level_mcv  = 1;
        $record->id_mc = $commentaires_id;
        _dao ('modulecredentialsvalues')->insert($record);
        
        //Creation de la value ecriture (2) pour commentaires
        $record = _record('modulecredentialsvalues');
        $record->module_mcv = 'test_news';
        $record->value_mcv  = 'ecriture';
        $record->level_mcv  = 2;
        $record->id_mc = $commentaires_id;
        _dao ('modulecredentialsvalues')->insert($record);
        
        //Creation de la value moderation (3) pour commentaires
        $record = _record('modulecredentialsvalues');
        $record->module_mcv = 'test_news';
        $record->value_mcv  = 'moderation';
        $record->level_mcv  = 3;
        $record->id_mc = $commentaires_id;
        _dao ('modulecredentialsvalues')->insert($record);
        
        //Creation du droit lecteur
        $record            = _record('modulecredentials');
        $record->module_mc = 'test_news';
        $record->name_mc   = 'lecteur';
        _dao ('modulecredentials')->insert($record);
        
        //Creation du droit ecrivain
        $record            = _record('modulecredentials');
        $record->module_mc = 'test_news';
        $record->name_mc   = 'ecrivain';
        _dao ('modulecredentials')->insert($record);
        
        $ecrivain_id = $record->id_mc;
        
        //Creation de l'overpass ecrivain pour lecteur
        $record = _record('modulecredentialsoverpass');
        $record->id_mc = $ecrivain_id;
        $record->overpass_module_mco = 'test_news';
        $record->overpass_name_mco   = 'lecteur';

        //Creation du droit administrateur (qui ne possède pas de modules)
        $record            = _record('modulecredentials');
        $record->module_mc = null;
        $record->name_mc   = 'administrateur';
        _dao ('modulecredentials')->insert($record);
        
        $administrateur_id = $record->id_mc;
        
        //Creation de l'overpass administrateur pour lecteur
        $record = _record('modulecredentialsoverpass');
        $record->id_mc = $administrateur_id;
        $record->overpass_module_mco = 'test_news';
        $record->overpass_name_mco   = 'lecteur';
        
        
        
        
		$sp = _daoSP ();
		$sp->addCondition ('login_dbuser', '=', 'CopixTest');		
		_dao ('dbuser')->deleteBy ($sp);
		
		$record = CopixDAOFactory::createRecord ('dbuser');
		$record->login_dbuser = 'CopixTest';
		$record->password_dbuser = md5 ('CopixTestPassword');
		$record->enabled_dbuser = 1;
		$record->email_dbuser = 'test@test.com';
		
		_dao ('dbuser')->insert ($record);
	}*/




    public function testSimpleCredentiel () {
        
        _doQuery('delete from modulecredentialsgroups');
        _doQuery('delete from modulecredentials');
        //Creation du droit lecteur
        $record1            = _record('modulecredentials');
        $record1->module_mc = 'test_news';
        $record1->name_mc   = 'lecteur';
        _dao ('modulecredentials')->insert($record1);
        
        $id = $record1->id_mc;
        
        //Creation du groupe pour test
        $record2 = _record('modulecredentialsgroups');
        $record2->id_mc = $id;
        $record2->handler_group = 'test|test';
        $record2->id_group = '1';
        _dao ('modulecredentialsgroups')->insert ($record2);
        
        //Avec droit spécifique au module
        $this->assertTrue(_ioClass('module_credentiel|module_groupHandler')->isOk ('test|test', '1', 'lecteur@test_news'));
        $this->assertTrue(!_ioClass('module_credentiel|module_groupHandler')->isOk ('test|test', '1', 'lecteur'));
        $this->assertTrue(!_ioClass('module_credentiel|module_groupHandler')->isOk ('test|test', '1', 'lecteur@test'));
        $this->assertTrue(!_ioClass('module_credentiel|module_groupHandler')->isOk ('test|aaaa', '1', 'lecteur@test_news'));
        $this->assertTrue(!_ioClass('module_credentiel|module_groupHandler')->isOk ('test|test', '2', 'lecteur@test_news'));
        
        $record1            = _record('modulecredentials');
        $record1->module_mc = null;
        $record1->name_mc   = 'test_notspecific';
        _dao ('modulecredentials')->insert($record1);
        
        $record2 = _record('modulecredentialsgroups');
        $record2->id_mc         = $id;
        $record2->handler_group = 'test|aaaa';
        $record2->id_group      = '1';
        _dao ('modulecredentialsgroups')->insert ($record2);
        
        //Avec droit non spécifique au module
        $this->assertTrue(_ioClass('module_credentiel|module_groupHandler')->isOk ('test|aaaa', '1', 'test_notspecific'));
        $this->assertTrue(!_ioClass('module_credentiel|module_groupHandler')->isOk ('test|aaaa', '1', 'test_notspecific@test'));
        $this->assertTrue(!_ioClass('module_credentiel|module_groupHandler')->isOk ('test|aaaa', '1', 'test_notspecific@test_news'));
        $this->assertTrue(!_ioClass('module_credentiel|module_groupHandler')->isOk ('test|eeee', '1', 'test_notspecific'));
        $this->assertTrue(!_ioClass('module_credentiel|module_groupHandler')->isOk ('test|aaaa', '2', 'test_notspecific'));
        
    }
    
    public function testValueCredential () {
        _doQuery('delete from modulecredentialsgroups');
        _doQuery('delete from modulecredentials');
        _doQuery('delete from modulecredentialsvalues');
        
        //Creation du droit lecteur
        $record_mc            = _record('modulecredentials');
        $record_mc->module_mc = 'test_news';
        $record_mc->name_mc   = 'commentaires';
        _dao ('modulecredentials')->insert($record_mc);
        
        $id = $record_mc->id_mc;
        
        $record_mcv = _record('modulecredentialsvalues');
        $record_mcv->id_mc = $id;
        $record_mcv->value_mcv = 'lecture';
        _dao ('modulecredentialsvalues')->insert($record_mcv);
        
        $id_mcv = $record_mcv->id_mcv;
        
        //Creation du groupe pour test
        $record2 = _record('modulecredentialsgroups');
        $record2->id_mc = $id;
        $record2->handler_group = 'test|test';
        $record2->id_group = '1';
        $record2->id_mcv = $id_mcv;
        _dao ('modulecredentialsgroups')->insert ($record2);
        
    }
}
?>