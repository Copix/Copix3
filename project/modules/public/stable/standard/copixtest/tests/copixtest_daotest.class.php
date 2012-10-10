<?php
/**
 * @package standard
 * @subpackage copixtest
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Tests divers sur les DAO avec les DAO du module
 * @package standard
 * @subpackage copixtest
*/
class CopixTest_DAOTest extends CopixTest {
	/**
	 * Contiendra l'identifiant du premier élément crée dans la table copixtestforeignkey
	 * @var int
	 */
	var $_firstIDFK = null;
	
	/**
	 * Contiendra l'identifiant du premier enregistrement crée dans la tablea copixtestmain.
	 * @var int
	 */
	var $_firstID = null;

	/**
	 * On va vider la base au départ
	 */
	function setUp (){
		CopixContext::push ('copixtest');
		$ct = CopixDB::getConnection ();
		$ct->doQuery ('delete from copixtestmain');
		$ct->doQuery ('delete from copixtestforeignkeytype');
		$ct->doQuery ('delete from copixtestautodao');
    }
    
    /**
     * Remet le contexte d'origine 
     */
    function tearDown (){
    	CopixContext::pop ();
    }

  /**
  * On test la population des données
  */
  function testDAOSimples (){
     //on crée les enregistrements FK
     $this->_testCreationEnregistrementsFK ();//va rensigner $this->_firstIDFK;
     $this->_testGetEnregistrementsFK ();

     //on crée les enregistrements principaux
     $this->_testCreationEnregistrement ();
     $this->_testGetEnregistrement ();
     
     $this->_testOrderBy ();
     $this->_testMethodeXml();
     
     $this->_testVersion ();
  }
  
  /**
   * Test la création des enregistrements de la table étrangère
   */
  function _testCreationEnregistrementsFK (){
     //création de l'enregistrement
     $recordFK = CopixDAOFactory::createRecord ('copixtest|copixtestforeignkeytype');

     //Création de la dao
     $daoFK = CopixDAOFactory::create ('copixtest|copixtestforeignkeytype');     

     $recordFK->caption_typetest = "catégorie 1";
     $daoFK->insert ($recordFK);
     $this->assertTrue (($firstID = $recordFK->type_test) !== null);

     $recordFK->caption_typetest = "catégorie 2";
     $daoFK->insert ($recordFK);
     $this->assertTrue ($recordFK->type_test == ($firstID + 1));

     $recordFK->caption_typetest = "catégorie 3";
     $daoFK->insert ($recordFK);
     $this->assertTrue ($recordFK->type_test == ($firstID + 2));

     $recordFK->caption_typetest = "catégorie 4";
     $daoFK->insert ($recordFK);
     $this->assertTrue ($recordFK->type_test == ($firstID + 3));
     
     $recordFK->caption_typetest = "catégorie 5";
     $daoFK->insert ($recordFK);
     $this->assertTrue ($recordFK->type_test == ($firstID + 4));

     //on test le fait que la méthode check fonctionne
     $recordFK->caption_typetest = null;
     $this->assertTrue (is_array ($daoFK->check ($recordFK)));
     $this->assertTrue (count ($daoFK->check ($recordFK)) == 1);
     $recordFK->caption_typetest = 'valeur correcte';
     $this->assertTrue ($daoFK->check ($recordFK));
     
     $this->_firstIDFK = $firstID;

     //on test que l'on dispose bien de 5 enregistrements dans la base     
     $this->assertTrue (count ($daoFK->findAll ()) === 5);
  }
  
  /**
   * Test de la méthode get pour la table simple
   */
  function _testGetEnregistrementsFK (){
     //Création de la dao
     $daoFK = CopixDAOFactory::create ('copixtest|copixtestforeignkeytype');     

     //on test la récupération de nos éléments de façon individuelle
     foreach ($daoFK->findAll () as $record){
     	$recordGot = $daoFK->get ($record->type_test);
     	$this->assertTrue ($recordGot->caption_typetest == $record->caption_typetest);
     }
  }
  
  /**
   * test de création des enregistrements dans la table principale 
   */
  function _testCreationEnregistrement (){
     //création des enregistrements de l'autre table
     $record = CopixDAOFactory::createRecord ('copixtest|copixtestmain');
     //Création de la dao de l'autre table
     $dao = CopixDAOFactory::create ('copixtest|copixtestmain');     
     
     //insertion d'enregistrements exemples
     $record->type_test  = $this->_firstIDFK;//catégorie 1
     $record->title_test = 'Titre 1';
     $record->description_test = 'Description du premier élément';
     $record->date_test = '20060201';
     $dao->insert ($record);
     $this->assertTrue (($firstID = $record->id_test) !== null);

     $record->type_test  = $this->_firstIDFK + 1;//catégorie 2
     $record->title_test = 'Titre 2';
     $record->description_test = 'Description du deuxième élément';
     $record->date_test = '20060202';
     $dao->insert ($record);
     $this->assertTrue ($record->id_test == ($firstID + 1));

     $record->type_test  = $this->_firstIDFK + 2;//catégorie 3
     $record->title_test = 'Titre 3';
     $record->description_test = 'Description du troisième élément';
     $record->date_test = '20060203';
     $dao->insert ($record);
     $this->assertTrue ($record->id_test == ($firstID + 2));
     
     $record->type_test  = $this->_firstIDFK + 2;//catégorie 3
     $record->title_test = 'Titre 4';
     $record->description_test = 'Description du quatrième élément';
     $record->date_test = '20060204';
     $dao->insert ($record);
     $this->assertTrue ($record->id_test == ($firstID + 3));

     $this->_firstID = $firstID;
  }
  
  /**
   * test de récupération des enregistrements depuis la table principale 
   */
  function _testGetEnregistrement (){
     //Création de la dao de la table principale
     $dao = _dao ('copixtest|copixtestmain');     
     //Création de la dao de la table étrangère
     $daoFK = _dao ('copixtest|copixtestforeignkeytype');     
     
     //récupération du premier enregistrement
     $record = $dao->get ($this->_firstID);
     //on a donné au premier enregistrement la catégorie 1
     $this->assertTrue ($record->type_test == $this->_firstIDFK);
     $recordFK = $daoFK->get ($this->_firstIDFK);

     //on vérifie que le libellé récupéré depuis la clef étrangère est le même que celui de la table principale
     $this->assertTrue ($record->caption_typetest == $recordFK->caption_typetest);
     
     //on vérifie qu'il y a bien 4 enregistrements en base
     $this->assertTrue (count ($dao->findAll ()) == 4);
     //on vérifie les libellés pour toutes les clefs étrangères
     foreach ($dao->findAll () as $recordGot){
     	$record = $dao->get ($recordGot->id_test);
     	$recordFK = $daoFK->get ($recordGot->type_test);
     	$this->assertTrue ($record->caption_typetest == $recordFK->caption_typetest);
     }
     
     //on vérifie qu'un findBy vide n'est pas impactant
     $this->assertEquals (4, count ($dao->findBy (CopixDAOFactory::createSearchParams ())));
     
     //on test le findby avec des conditions sur les champs principaux
     //1 seul élément de catégorie 1
     $sp  = _daoSP ()->addCondition ('type_test', '=', $this->_firstIDFK);
     $this->assertTrue (count ($dao->findBy ($sp)) == 1);
     
     //1 seul élément de catégorie 2
     $sp  = _daoSP ()->addCondition ('type_test', '=', $this->_firstIDFK + 1);
     $this->assertTrue (count ($dao->findBy ($sp)) == 1);
     
     //2 éléments de catégorie 3
	 $sp  = _daoSP ()->addCondition ('type_test', '=', $this->_firstIDFK + 2);
     $this->assertTrue (count ($dao->findBy ($sp)) == 2);

     //aucun éléments de catégorie 4
     $sp  = _daoSP ()->addCondition ('type_test', '=', $this->_firstIDFK + 3);
     $this->assertTrue (count ($dao->findBy ($sp)) == 0);

     //Test sur le libellé principal
     $sp  = _daoSP ()->addCondition ('title_test', '=', 'Titre 1');
     $this->assertTrue (count ($dao->findBy ($sp)) == 1);

     //like
     $sp  = _daoSP ()->addCondition ('title_test', 'like', 'Titre%');
     $this->assertTrue (count ($dao->findBy ($sp)) == 4);

     //test sur le libellé de la clef étrangère
     $sp  = _daoSP ()->addCondition ('caption_typetest', '=', 'catégorie 3');
     $this->assertTrue (count ($dao->findBy ($sp)) == 2);
     
     //test de récupération des deux premiers enregistrements
	 $this->assertEquals (count ($dao->findBy (_daoSP ()->setLimit (0, 2))), 2);
     $records = $dao->findBy (_daoSP ()->setLimit (0, 2)->orderBy ('title_test'));	 
     $this->assertEquals ($records[0]->title_test, 'Titre 1');
     $this->assertEquals ($records[1]->title_test, 'Titre 2');
     
     //Le même test avec un simple appel à count
	 $this->assertEquals (count ($dao->findBy (_daoSP ()->setCount (2))), 2);
     $records = $dao->findBy (_daoSP ()->setCount (2)->orderBy ('title_test'));	 
     $this->assertEquals ($records[0]->title_test, 'Titre 1');
     $this->assertEquals ($records[1]->title_test, 'Titre 2');
     
     //Le même test avec un appel à count et offset séparé
	 $this->assertEquals (count ($dao->findBy (_daoSP ()->setCount (2)->setOffset (0))), 2);
     $records = $dao->findBy (_daoSP ()->setCount (2)->setOffset (0)->orderBy ('title_test'));	 
     $this->assertEquals ($records[0]->title_test, 'Titre 1');
     $this->assertEquals ($records[1]->title_test, 'Titre 2');
     
     //test de récupération des 3 derniers enregistrements
	 $this->assertEquals (count ($dao->findBy (_daoSP ()->setLimit (1, 3))), 3);
     $records = $dao->findBy (_daoSP ()->setLimit (1, 3)->orderBy ('title_test'));	 
     $this->assertEquals ($records[0]->title_test, 'Titre 2');
     $this->assertEquals ($records[1]->title_test, 'Titre 3');
     $this->assertEquals ($records[2]->title_test, 'Titre 4');
     
     //Le même test avec juste un appel à offset
	 $this->assertEquals (count ($dao->findBy (_daoSP ()->setOffset (1))), 3);
     $records = $dao->findBy (_daoSP ()->setOffset (1)->orderBy ('title_test'));	 
     $this->assertEquals ($records[0]->title_test, 'Titre 2');
     $this->assertEquals ($records[1]->title_test, 'Titre 3');
     $this->assertEquals ($records[2]->title_test, 'Titre 4');
     
  }

  
  /**
   * Test de la clause orderBy
   */
  function _testOrderBy (){
     //tri par clef étrangère
     $sp  = _daoSP ()->orderBy ('caption_typetest');
     $dao = _dao ('copixtest|copixtestmain');
     $dao->findBy ($sp);

     //tri par un champ connu uniquement par un alias
     $sp  = _daoSP ()->orderBy ('title_test');
     $dao = _dao ('copixtest|copixtestmain');
     $dao->findBy ($sp);
  }
  
  /**
   * Test de l'utilisation des méthodes décrites en XML
   */
  function _testMethodeXml (){
     $dao = _dao ('copixtest|copixtestmain');
     $results = $dao->findByTitleOrderById ('Titre 1');
     $this->assertTrue (count ($results) === 1);

     $dao = _dao ('copixtest|copixtestmain');
     $results = $dao->findByTitleOrderByTitle ('Titre 1');
     $this->assertTrue (count ($results) === 1);
  }
  
  function _testVersion (){
  	$dao = _dao ('copixtest|copixtestmain');
  	$record = $dao->get ($this->_firstID);
  	$record2 = clone ($record);

  	$this->assertTrue ($record !== null);
  	$dao->update ($record);
  	
  	$this->assertTrue ($record->version_test != $record2->version_test);
  	$this->assertTrue ($record->version_test == $record2->version_test + 1);
  	
  	try {
  		$dao->update ($record2);
  		$this->assertTrue (false);//pas d'exception, il y a un problème, onp aurait du avoir "objet déja modifié"
  	}catch (CopixDAOVersionException $e){
  		$this->assertTrue (true);//ok, y'a bien eu une exception de levée
  	}
  }
  
  /**
   * Test d'une DAO automatique
   */
  function testDAOAuto (){
    CopixDAOFactory::getInstanceOf ('copixtestautodao')->findAll ();
  }

  /**
   * Test de groupe OR
   */
  function testDAOConditionGroup() {
  	$ct = CopixDB::getConnection ();
  	$ct->doQuery ('delete from copixtestautodao');
  	
  	$dao = CopixDAOFactory::create ('copixtestautodao');
  	$sp  = CopixDAOFactory::createSearchParams (); 
  	 
  	$record = CopixDAOFactory::createRecord ('copixtestautodao');
  	 
  	$record->type_test  = '1';
    $record->titre_test = 'Titre 3';
    $record->description_test = 'Description du troisième élément';
    $record->date_test = '20060203';
    $dao->insert ($record);

    $record->type_test  = '3';
    $record->titre_test = 'Titre 4';
    $record->description_test = 'Description du quatrième élément';
    $record->date_test = '20060204';
    $dao->insert ($record);     

  	$sp->addCondition ('titre_test','=','Titre 3','or');
  	$sp->addCondition ('titre_test','=','Titre 4','or');
  		
  	$this->assertTrue (count($dao->findBy($sp))>1);
  	
  	//On vide les titres Titre 3 et vérifie le delete by
  	$sp = CopixDAOFactory::createSearchParams ();
  	$sp->addCondition ('titre_test','=','Titre 3');
  	$dao->deleteBy ($sp);
  	
  	$this->assertTrue (count ($dao->findBy ($sp)) == 0);

  	//On vide tout et on vérifie le findAll == 0 
  	$sp = CopixDAOFactory::createSearchParams ();
  	$dao->deleteBy ($sp);
  	$this->assertTrue (count ($dao->findAll ()) == 0);
  	 
  }
  
  /**
   * Test de groupe OR sur un array
   */
  function testDAOConditionGroupArray() {
  	$ct = CopixDB::getConnection ();
  	$ct->doQuery ('delete from copixtestautodao');
  	
  	$dao = CopixDAOFactory::create ('copixtestautodao');
  	$sp  = CopixDAOFactory::createSearchParams (); 
  	 
  	$record = CopixDAOFactory::createRecord ('copixtestautodao');
  	 
  	$record->type_test  = '1';
    $record->titre_test = 'Titre 3';
    $record->description_test = 'Description du troisième élément';
    $record->date_test = '20060203';
    $dao->insert ($record);

    $record->type_test  = '3';
    $record->titre_test = 'Titre 4';
    $record->description_test = 'Description du quatrième élément';
    $record->date_test = '20060204';
    $dao->insert ($record);     
    $tab = array();
    $tab[]='Titre 3';
    $tab[]='Titre 4'; 
  	$sp->addCondition ('titre_test','=',$tab);
  	$this->assertTrue (count ($dao->findBy($sp))>1);
  }
  
  /**
   * test de conditions vides avec juste une order by
   */
  function testDAOConditionEmptyWithOrderBy (){
  	$sp = CopixDAOFactory::createSearchParams ();
  	$sp->orderBy ('type_test');
  	_ioDAO ('copixtestautodao')->findBy ($sp);
  }
  
  /**
   * Test que lorsque l'on a défini des groupes qui ne contiennent rien, ça marche quand même
   */
  function testDAOEmptyGroups (){
  	$this->_testInsertTestData ();
  	 
  	$sp = CopixDAOFactory::createSearchParams ();
  	$sp->startGroup ();
  	$sp->endGroup ();
  	$this->assertEquals (count (_ioDAO ('copixtestautodao')->findBy ($sp)), 4);  
  	 
  	$sp = CopixDAOFactory::createSearchParams ();
  	$sp->startGroup ();
  	$sp->startGroup ();
  	$sp->endGroup ();
  	 
  	$sp->startGroup ();
  	$sp->endGroup ();
  	$sp->endGroup ();
  	$sp->startGroup ();
  	$sp->startGroup ();
  	$sp->endGroup ();
  	 
  	$sp->startGroup ();
  	$sp->endGroup ();
  	$sp->endGroup ();
  	$this->assertEquals (count (_ioDAO ('copixtestautodao')->findBy ($sp)), 4);  
  	 
  }
  
  /**
   * test de conditions multiples sur un même champ 
   */
  function testSeveralConditionsOnSameField (){
  	$this->_testInsertTestData ();
  	 
  	//Dans un même groupe
  	$sp = CopixDAOFactory::createSearchParams ();
  	$sp->addCondition ('titre_test', '=', 'Titre 3', 'or'); 
  	$sp->addCondition ('titre_test', '=', 'Titre 4', 'or');
  	$this->assertEquals (count (_ioDAO ('copixtestautodao')->findBy ($sp)), 2);  

  	//Avec un tableau
  	$sp = CopixDAOFactory::createSearchParams ();
  	$sp->addCondition ('titre_test', '=', array ('Titre 3', 'Titre 4')); 
  	$this->assertEquals (count (_ioDAO ('copixtestautodao')->findBy ($sp)), 2);  
  	 
  	//Dans des groupes différents
  	$sp = CopixDAOFactory::createSearchParams ();
  	$sp->addCondition ('titre_test', '=', 'Titre 2', 'or');
  	 
  	$sp->startGroup ('OR');
  	$sp->addCondition ('titre_test', '=', 'Titre 3', 'or');
  	$sp->endGroup ();

  	$sp->startGroup ('OR');
  	$sp->addCondition ('titre_test', '=', 'Titre 4', 'or');
  	$sp->endGroup ();

  	$this->assertEquals (count (_ioDAO ('copixtestautodao')->findBy ($sp)), 3);  
  }
  
  function testGroupAndWithFieldOr (){
  	$this->_testInsertTestData ();
  	//Dans des groupes différents
  	$sp = CopixDAOFactory::createSearchParams ();
  	$sp->addCondition ('titre_test', '=', 'Titre 3');
  	$sp->startGroup ();
  	$sp->addCondition ('titre_test', '=', 'Titre 3', 'or');
  	$sp->addCondition ('titre_test', '=', 'Titre 4', 'or');
  	$sp->endGroup ();
  	$this->assertEquals (count (_ioDAO ('copixtestautodao')->findBy ($sp)), 1);
  }
  
  /**
   * Test pour voir si la requête est correcte lorsque l'on recherche avec des groupes vides
   */
  function testEmptyGroups (){
  	$this->_testInsertTestData ();
  	$sp = _daoSP ()->startGroup ()
  	               ->endGroup ()
  	               ->startGroup ()
  	               ->addCondition ('titre_test', '=', 'Titre 3')
  	               ->endGroup ();
  	               $this->assertEquals (count (_ioDAO ('copixtestautodao')->findBy ($sp)), 
  	                                    1);
  }
  
  /**
   * Test que si on spécifie un DAO avec module cela ne fonctionne pas
   */
  function testDAONoModule (){
  	try {
  		_ioDAO ('copixtest|copixtestautodao'); 
  		$this->fails ('copixtest|copixtestautodao');
  	}catch (Exception $e){	}
  }
  
  /**
   * Test des noms de table en majuscule
   */
  public function testDAOCase (){
  	//_ioDAO ('Test')->findAll ();
  }
 
  private function _testInsertTestData (){
  	$dao = CopixDAOFactory::create ('copixtestautodao');
  	$sp  = CopixDAOFactory::createSearchParams (); 
  	$record = CopixDAOFactory::createRecord ('copixtestautodao');
  	 
  	$record->type_test  = '1';
    $record->titre_test = 'Titre 1';
    $record->description_test = 'Description du premier élément';
    $record->date_test = '20060203';
    $dao->insert ($record);

  	$record->type_test  = '2';
    $record->titre_test = 'Titre 2';
    $record->description_test = 'Description du deuxième élément';
    $record->date_test = '20060203';
    $dao->insert ($record);

    $record->type_test  = '3';
    $record->titre_test = 'Titre 3';
    $record->description_test = 'Description du troisième élément';
    $record->date_test = '20060204';
    $dao->insert ($record);     

    $record->type_test  = '4';
    $record->titre_test = 'Titre 4';
    $record->description_test = 'Description du troisième élément';
    $record->date_test = '20060204';
    $dao->insert ($record);
  }
  
  function testDAOSearchFirstArrayEmpty (){
  	$this->_testInsertTestData ();
  	$sp = _daoSP ()->addCondition ('titre_test', '=', array ())
  					->addCondition ('titre_test', '=', 'Titre 3');
    $this->assertEquals (count (_ioDAO ('copixtestautodao')->findBy ($sp)),
  	                                    1);  	
  }
  
  function testDAOSearchGroupWithEmptyArray (){
  	$this->_testInsertTestData ();
  	$sp = _daoSP ()->startGroup ()->addCondition ('titre_test', '=', array ())->endGroup ()
  					->startGroup ()
  					->addCondition ('titre_test', '=', array ())
  					->addCondition ('titre_test', '=', 'Titre 3')
  					->endGroup ();
    $this->assertEquals (count (_ioDAO ('copixtestautodao')->findBy ($sp)),
  	                                    1); 
  }
  
  function testDAOAddSQL (){
  	$this->_testInsertTestData ();
  	$sp = _daoSP ()->addCondition ('titre_test', '=', 'Titre 3')
  					->addSQL ('not exists (select * from copixtestautodao where titre_test = :titre_test)', array (':titre_test'=>2038));
    $this->assertEquals (count (_ioDAO ('copixtestautodao')->findBy ($sp)),
  	                                    1); 
  }
  
  /**
   * Enter description here...
   *
   */
  function testDAORecordIteratorMoreProperties (){
  	  	$this->_testInsertTestData ();
  	  	$elements = _ioDAO ('copixtestautodao')->findAll ();
  	  	foreach ($elements as $var){
  	  		$var->newProp = 10;
  	  	}

  	  	foreach ($elements as $var){
  	  		$this->assertEquals ($var->newProp, 10);
  	  	}
  }
  
  /**
   * On test le fetchAll pour pouvoir y rajouter des inforamtions
   */
  function testDAORecordIteratorMorePropertiesWithFor (){
  	  	$this->_testInsertTestData ();
  	  	$elements = _ioDAO ('copixtestautodao')->findAll ();
  	  	$elements = $elements->fetchAll ();
  	  	for ($i=0; $i<count ($elements); $i++){
  	  		$elements[$i]->newProp = $i;
  	  	}

  	  	for ($i=0; $i<count ($elements); $i++){
  	  		$this->assertEquals ($elements[$i]->newProp, $i);
  	  	}
  }

  
  /**
   * On test que les dao avec les noms de champs différents des propriétés fonctionnent correctement
   */
  function testDAOOverLoadedFieldNames (){
  	 $this->testDAOSimples ();

     //Lecture complète
  	 foreach (_ioDAO ('copixtestmain_overloaded')->findAll () as $values){
     	foreach (array ('id', 'type', 'caption', 'titre', 'description', 'date', 'version') as $property){
     		$this->assertContains ($property, array_keys (get_object_vars ($values)));
     	}
     }

	 //test de get
	 $record = _ioDAO ('copixtestmain_overloaded')->get ($this->_firstID);
	 $this->assertNotEquals ($record, false);
	 $this->assertNotEquals ($record, null);
	 
	 //test de findBy
	 $this->assertEquals (count (_ioDAO ('copixtestmain_overloaded')->findBy (_daoSP ()->addCondition ('id', '=', $this->_firstID))), 1);
  }
  
  public function testInsertNotNullWithEmptyValues (){
  	//on test que chaine vide passe.
  	$record = _record ('copixtestautodao');
  	$record->type_test  = '1';
    $record->titre_test = 'Titre X';
    $record->description_test = '';
    $record->date_test = '20060203';
    _dao ('copixtestautodao')->insert ($record);
  	
    //On test que null ne passe pas.
    try {
	  	$record = _record ('copixtestautodao');
	  	$record->type_test  = '1';
	    $record->titre_test = 'Titre X';
	    $record->description_test = null;
	    $record->date_test = '20060203';
	    _dao ('copixtestautodao')->insert ($record);
	    $this->assertTrue (false);
    }catch (CopixDAOCheckException $e){
    	$this->assertTrue (true);
    }
  }
  
  public function testInsertNullInIntenger (){
  	$record = _record ('copixtestautodao');
  	$record->type_test  = '1';
    $record->titre_test = 'Titre XXXX';
    $record->description_test = '';
    $record->date_test = '20060203';
    _dao ('copixtestautodao')->insert ($record);
    
    $results = _dao ('copixtestautodao')->findBy (_daoSp ()->addCondition ('titre_test', '=', 'Titre XXXX'));
    $this->assertEquals (count ($results), 1);
    $this->assertEquals ($results[0]->nullable_test, null);
  }
  
  /**
   * On test l'insertion forcée des autoincrement (et on test aussi au passage que cela rempli correctement les non forcés)
   */
  public function testInsertForceAutoincrement (){
  	//Test les identifiants automatiques
  	$record = _record ('copixtestautodao');
  	$record->type_test  = '1';
    $record->titre_test = 'Titre XXXX';
    $record->description_test = '';
    $record->date_test = '20060203';
    _dao ('copixtestautodao')->insert ($record);
    $this->assertNotEquals ($record->id_test, null);
    
  	//Test les identifiants forcés
    $record->id_test = $newId = $record->id_test + 10;
    _dao ('copixtestautodao')->insert ($record, true);
    $this->assertEquals ($record->id_test, $newId);
    
    //Test l'échec de contrainte en clef primaire
    try {
	    _dao ('copixtestautodao')->insert ($record, true);
	    $this->assertTrue (false);
    }catch (CopixException $e){
    	$this->assertTrue (true);
    }
  }
  
  public function testDateTime (){
  	_dao ('datetimetests')->deleteBy (_daoSp ());

  	$record = _record ('datetimetests');
  	$record->date_dtt = '20071121';
  	$record->datetime_dtt = '20071129101222';
  	$record->time_dtt = '101222';
  	_dao ('datetimetests')->insert ($record);
  	
  	$record = _record ('datetimetests');
  	$record->date_dtt = '20071120';
  	$record->datetime_dtt = '20071120101242';
  	$record->time_dtt = '101242';
  	_dao ('datetimetests')->insert ($record);

  	$results = _dao ('datetimetests')->findAll ();
  	
  	//premier enregistrement
  	$this->assertEquals ($results[0]->date_dtt, '20071121');
  	$this->assertEquals ($results[0]->datetime_dtt, '20071129101222');
  	$this->assertEquals ($results[0]->time_dtt, '101222');
  	
  	//second enregistrement
  	$this->assertEquals ($results[1]->date_dtt, '20071120');
  	$this->assertEquals ($results[1]->datetime_dtt, '20071120101242');
  	$this->assertEquals ($results[1]->time_dtt, '101242');
  	
  	//vérification des findBy
  	
	//doit récupérer le premier enregistrement
  	$sp = _daoSP ()->addCondition ('date_dtt', '=', '20071121');
  	$results = _dao ('datetimetests')->findBy ($sp);
  	$this->assertEquals (count ($results), 1);
  	$this->assertEquals ($results[0]->date_dtt, '20071121');
  	$this->assertEquals ($results[0]->datetime_dtt, '20071129101222');
  	$this->assertEquals ($results[0]->time_dtt, '101222');
  	
  	$sp = _daoSP ()->addCondition ('datetime_dtt', '=', '20071129101222');
  	$results = _dao ('datetimetests')->findBy ($sp);
  	$this->assertEquals (count ($results), 1);
  	$this->assertEquals ($results[0]->date_dtt, '20071121');
  	$this->assertEquals ($results[0]->datetime_dtt, '20071129101222');
  	$this->assertEquals ($results[0]->time_dtt, '101222');
  	
  	$sp = _daoSP ()->addCondition ('time_dtt', '=', '101222');
  	$results = _dao ('datetimetests')->findBy ($sp);
  	$this->assertEquals (count ($results), 1);
  	$this->assertEquals ($results[0]->date_dtt, '20071121');
  	$this->assertEquals ($results[0]->datetime_dtt, '20071129101222');
  	$this->assertEquals ($results[0]->time_dtt, '101222');
  	
  	//doit récupérer le second enregistrement
  	$sp = _daoSP ()->addCondition ('date_dtt', '=', '20071120');
  	$results = _dao ('datetimetests')->findBy ($sp);
  	$this->assertEquals (count ($results), 1);
	$this->assertEquals ($results[0]->date_dtt, '20071120');
  	$this->assertEquals ($results[0]->datetime_dtt, '20071120101242');
  	$this->assertEquals ($results[0]->time_dtt, '101242');
  	
  	$sp = _daoSP ()->addCondition ('datetime_dtt', '=', '20071120101242');
  	$results = _dao ('datetimetests')->findBy ($sp);
  	$this->assertEquals (count ($results), 1);
	$this->assertEquals ($results[0]->date_dtt, '20071120');
  	$this->assertEquals ($results[0]->datetime_dtt, '20071120101242');
  	$this->assertEquals ($results[0]->time_dtt, '101242');
  	
  	$sp = _daoSP ()->addCondition ('time_dtt', '=', '101242');
  	$results = _dao ('datetimetests')->findBy ($sp);
  	$this->assertEquals (count ($results), 1);
	$this->assertEquals ($results[0]->date_dtt, '20071120');
  	$this->assertEquals ($results[0]->datetime_dtt, '20071120101242');
  	$this->assertEquals ($results[0]->time_dtt, '101242');
  }
  
  /**
   * On test que l'on arrive bien a mettre null dans les datetime
   */
  function testDateTimeNullable (){
  	_dao ('datetimetests')->deleteBy (_daoSp ());
  	$record = _record ('datetimetests');
  	$record->date_dtt = null;
  	$record->datetime_dtt = null;
  	$record->time_dtt = null;
  	_dao ('datetimetests')->insert ($record);

  	$readRecord = _dao ('datetimetests')->get ($record->id_dtt);
  	$this->assertTrue ($readRecord->date_dtt === null); 
  	$this->assertTrue ($readRecord->datetime_dtt === null); 
  	$this->assertTrue ($readRecord->time_dtt === null); 
  }
  
  /**
   * Test le fait que les valeurs par défaut soient prises en compte.
	A faire en 3.1
  public function testDefaultValues (){
  	$record = _record ('copixtestautodao');
  	$this->assertEquals ($record->type_test, 0);//la valeur par défaut dans la base est 0
    $this->assertTrue ($record->type_test === 0);

    $record->titre_test = 'Titre XXXX';
    $record->description_test = '';
    $record->date_test = '20060203';
    
    _dao ('copixtestautodao')->insert ($record);
    $gotRecord = _dao ('copixtestautodao')->get ($record->id_test);
    $this->assertEquals ($gotRecord->type_test, 0);
    $this->assertTrue ($gotRecord->type_test === 0);
  }
   */
}
?>