<?php
/**
 * @package standard
 * @subpackage test
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Test des validators
 * @package standard
 * @subpackage test
 */
class Test_CopixValidator extends CopixTest {

	/**
	 * Test d'un simple validateur
	 */
	function testCustomErrorMessage (){
		$formuleDePolitesse = _validator ('preg', '/Merci Copix pour tout le bien que tu nous procure/',
                                                'Il manque la formule de politesse');
		$mailALaCopixTeam = 'Bonjour tout le monde !';

		$this->assertTrue ($formuleDePolitesse->check ($mailALaCopixTeam) !== true);
		$errors = $formuleDePolitesse->check ($mailALaCopixTeam);

		$this->assertEquals (count ($errors), 1);
		foreach ($errors as $error){
			$this->assertEquals ($error, 'Il manque la formule de politesse');
		}
	}

	/**
	 * Test du validateur email
	 */
	function testValidatorEmail (){
		$validator = _validator ('email');
		$this->assertTrue ($validator->check ('monmail@domaine.fr'));
		$this->assertTrue ($validator->check ('12@invaliddomain') !== true);

		$this->assertEquals ('copixerrorobject',  strtolower (get_class ($validator->check ('12@invaliddomain'))));
		$this->assertTrue ($validator->check ('12@invaliddomain')->isError ());

		$this->assertTrue ($validator->check ('invaliddomain') !== true);
		$this->assertTrue ($validator->check ('invaliddomain')->isError ());

		try {
			$validator->assert ('invaliddomain');
			$this->assertTrue (false);
		}catch (CopixValidatorException $e){
			$this->assertTrue (true);
		}

		try {
			$validator->assert ('monmail@domaine.fr');
			$this->assertTrue (true);
		}catch (CopixValidatorException $e){
			$this->assertTrue (false);
		}
	}

	/**
	 * Test du validateur de numéro de Securité Sociale
	 */
	function testValidatorNumsecu (){
		$validator = _validator ('numsecu');
		$this->assertTrue ($validator->check ('111111111111120'));
		$this->assertTrue ($validator->check ('812145402548402') !== true);

		try {
			$validator->assert ('812145402548402');
			$this->assertTrue (false);
		}catch (CopixValidatorException $e){
			$this->assertTrue (true);
		}

		try {
			$validator->assert ('111111111111120');
			$this->assertTrue (true);
		}catch (CopixValidatorException $e){
			$this->assertTrue (false);
		}
	}

	/**
	 * Test du validateur preg
	 */
	function testValidatorPreg (){
		//En passant une chaine de caractère
		$validator = new CopixValidatorPReg ('/test/');
		$this->assertTrue ($validator->check ('test'));
		$this->assertTrue ($validator->check ('tes01') !== true);

		//En passant un tableau avec un pattern simple
		$validator = new CopixValidatorPReg (array ('pattern'=>'/test/'));
		$this->assertTrue ($validator->check ('test'));
		$this->assertTrue ($validator->check ('tes01') !== true);

		//En passant aucun paramètre (qui génère une erreur)
		try {
			$validator = new CopixValidatorPReg ();
			$this->assertFalse (true);
			//il doit y avoir une exception (pas de pattern fourni)
		}catch (CopixException $e){
			$this->assertTrue (true);
		}

		//En passant un tableau vide (erreur)
		try {
			$validator = new CopixValidatorPReg (array ());
			$this->assertFalse (true);
			//il doit y avoir une exception (pas de pattern fourni)
		}catch (CopixException $e){
			$this->assertTrue (true);
		}

		//En passant un tableau de pattern directement
		$validator = new CopixValidatorPReg (array ('/test/', '/autre/'));
		$this->assertTrue ($validator->check ('testautre'));
		$this->assertTrue ($validator->check ('test') !== true);
		$this->assertTrue ($validator->check ('autre') !== true);

		//En passant un tableau de pattern dans array ('pattern')
		$validator = new CopixValidatorPReg (array ('pattern'=>array ('/test/', '/autre/')));
		$this->assertTrue ($validator->check ('testautre'));
		$this->assertTrue ($validator->check ('test') !== true);
		$this->assertTrue ($validator->check ('autre') !== true);

		try {
			$validator->assert ('testautre');
			$this->assertTrue (true);
		}catch (CopixValidatorException $e){
			$this->assertTrue (false);
		}

		try {
			$validator->assert ('tst');
			$this->assertTrue (false);
		}catch (CopixValidatorException $e){
			$this->assertTrue (true);
			$this->assertTrue (count ($e->getErrorObject ()->asArray ()) == 2);
		}
	}
	
	public function forTestValidation ($pTest){
		return true;
	}

	public function forTestNotValidation ($pTest){
		return false;
	}
	
	/**
	 * Test du validateur avec fonction call back
	 */
	function testValidatorCallback (){
		$func = create_function ('$pValue', 'return $pValue == 2;');

		$validator = new CopixValidatorCallback ($func);
		$this->assertTrue ($validator->check (2));
		$this->assertTrue ($validator->check (3) !== true);

		$validator = new CopixValidatorCallback (array ('callback'=>$func));
		$this->assertTrue ($validator->check (2));
		$this->assertTrue ($validator->check (3) !== true);

		//En passant aucun paramètre (qui génère une erreur)
		try {
			$validator = new CopixValidatorCallback ();
			$this->assertFalse (true);
			//il doit y avoir une exception (pas de pattern fourni)
		}catch (CopixException $e){
			$this->assertTrue (true);
		}

		//Test de la fonction assert
		try {
			$validator = new CopixValidatorCallback (array ('callback'=>$func));
			$validator->assert (3);
			$this->assertTrue (false);
			//il doit y avoir une exception (pas de pattern fourni)
		}catch (CopixValidatorException $e){
			$this->assertTrue (true);
		}

		try {
			$validator = new CopixValidatorCallback (array ('callback'=>$func));
			$validator->assert (2);
			$this->assertTrue (true);
			//il doit y avoir une exception (pas de pattern fourni)
		}catch (CopixValidatorException $e){
			$this->assertTrue (false);
		}
		
		//en passant directement un tableau pour le callback
		$this->assertTrue  (_validator ('callback', array ($this, 'forTestValidation'))->check (1));
		$this->assertFalse (_validator ('callback', array ($this, 'forTestNotValidation'))->check (1) === true);
	}

	/**
	 * Test du validateur de date
	 */
	function testValidatorDate (){
		$validator = new CopixValidatorDate ();
		$this->assertTrue ($validator->check ('01/02/2003'));
		$this->assertTrue ($validator->check ('29/02/2008'));//bissextile
		$this->assertTrue ($validator->check ('01/22/2003') !== true);
		$this->assertTrue ($validator->check ('29/02/2007') !== true);//pas bissextile

		//Vérification du mini
		$validator = new CopixValidatorDate (array ('min'=>'15/01/2007'));
		$this->assertTrue ($validator->check ('01/02/2007'));
		$this->assertTrue ($validator->check ('16/01/2007'));
		$this->assertTrue ($validator->check ('15/01/2007'));

		$this->assertTrue ($validator->check ('14/01/2007') !== true);
		$this->assertTrue ($validator->check ('14/02/2007'));

		//Vérification du maxi
		$validator = new CopixValidatorDate (array ('max'=>'15/02/2007'));
		$this->assertTrue ($validator->check ('14/02/2007'));
		$this->assertTrue ($validator->check ('15/02/2007'));
		$this->assertTrue ($validator->check ('16/01/2007'));

		$this->assertTrue ($validator->check ('14/03/2007') !== true);
		$this->assertTrue ($validator->check ('16/02/2007') !== true);

		//Vérification des créneaux
		$validator = new CopixValidatorDate (array ('min'=>'15/01/2007', 'max'=>'15/02/2007'));
		$this->assertTrue ($validator->check ('01/02/2007'));
		$this->assertTrue ($validator->check ('16/01/2007'));
		$this->assertTrue ($validator->check ('15/01/2007'));

		$this->assertTrue ($validator->check ('14/01/2007') !== true);
		$this->assertTrue ($validator->check ('14/02/2007'));

		$validator = new CopixValidatorDate (array ('min'=>'15/01/2007', 'max'=>'15/02/2007'));
		$this->assertTrue ($validator->check ('14/02/2007'));
		$this->assertTrue ($validator->check ('15/02/2007'));
		$this->assertTrue ($validator->check ('16/01/2007'));
		
		$this->assertTrue ($validator->check ('14/03/2007') !== true);
		$this->assertTrue ($validator->check ('16/02/2007') !== true);

		$this->assertTrue (_validator ('date', array ('format'=>'date', 'min'=>'15/01/2007', 'max'=>'15/02/2007'))->check ('16/02/2007') !== true);
		$this->assertTrue (_validator ('date', array ('format'=>'date'))->check ('16/02/2007') === true);		
		
		try {
			$this->assertTrue (_validator ('date', array ('format'=>'space', 'min'=>'15/01/2007', 'max'=>'15/02/2007'))->check ('16/02/2007') !== true);
			$this->assertFalse (true);//on aurait du générer une exception car space n'est pas un format de date valide
		}catch (CopixException $e){
		}
		try {
			$this->assertTrue (_validator ('date', array ('format'=>'space'))->check ('16/02/2007') === true);
			$this->assertFalse (true);//on aurait du générer une exception car space n'est pas un format de date valide
		}catch (CopixException $e){
		}		

		$this->assertTrue (_validator ('date')->check ('00/14/2019') !== true);
		
		//On test avec le format datetime
		$validator = _validator ('date', array ('format'=>'datetime'));
		$this->assertTrue ($validator->check ('01/02/2003 14:05:12'));
		$this->assertTrue ($validator->check ('29/02/2008 18:06:14'));//bissextile
		$this->assertTrue ($validator->check ('01/22/2003 18:06:14') !== true);
		$this->assertTrue ($validator->check ('29/02/2007 14:05:12') !== true);//pas bissextile
		
		//on vois qu'on ne vérifie absolument pas la partie heure : 
		$this->assertFalse ($validator->check ('01/02/2003 tomates!') === true);
	}

	/**
	 * Test d'un validateur composé de validateur
	 */
	function testCompositeValidator (){
		$validator = _cValidator ()	->attach (_validator ('preg', '/01/', 'CustomError1'))
		->attach (_validator ('preg', '/02/'))
		->attach (_validator ('date'));

		$this->assertTrue ($validator->check ('01/02/2009'));
		$this->assertTrue ($validator->check ('01/01/2009') !== true);

		$this->assertEquals (count ($validator->check ('01/01/2009')->asArray ()), 1);
		$this->assertEquals (count ($errors = $validator->check ('03/03/2009')->asArray ()), 2);
		$this->assertEquals ($errors[0], 'CustomError1');

		$this->assertEquals (count ($validator->check ('00/14/2039')->asArray ()), 3);

		//Test des messages d'erreurs "remplacés"
		$validator = _cValidator ('Valeur pas ok')	->attach (_validator ('preg', '/01/'))
		->attach (_validator ('preg', '/02/'))
		->attach (_validator ('date'));
		$this->assertEquals (count ($validator->check ('00/14/2039')->asArray ()), 1);
		$this->assertEquals ('Valeur pas ok', _toString ($validator->check ('00/14/2039')));

		$test = new CopixCompositeValidator ();
		$test->attach (new CopixValidatorEmail ());
		$this->assertTrue ($test->check ('sedsdg') !== true);
		$this->assertTrue ($test->check ('sedsdg')->isError ());

		//test des asserts
		$validator = _cValidator ()	->attach (_validator ('preg', '/01/'))
		->attach (_validator ('preg', '/02/'))
		->attach (_validator ('date'));
		try {
			$validator->assert ('01/14/2039');
			$this->assertTrue (false);
		}catch (CopixValidatorException $e){
			$this->assertTrue (true);
		}

		try {
			$validator->assert ('01/02/2039');
			$this->assertTrue (true);
		}catch (CopixValidatorException $e){
			$this->assertTrue (false);
		}
	}

	function testComplexTypeValidatorWithObject (){
		$validator = _ctValidator ()->attachTo (_validator ('date'), 'datenaissance')
		->attachTo (_validator ('date'), 'dateinscription')
		->required ('datenaissance')
		->required ('dateinscription');
		$personne = new StdClass ();
		$this->assertTrue ($validator->check ($personne) !== true);
		$errors = $validator->check ($personne)->asArray ();
		$this->assertTrue (isset ($errors['datenaissance']));
		$this->assertTrue (isset ($errors['dateinscription']));

		$personne->datenaissance   = '25/12/1976';
		$personne->dateinscription = '01/01/1989';
		$this->assertTrue ($validator->check ($personne));

		//test lorsqu'une propriété est indiquée comme obligatoire sans y attacher de validateur
		$validator = _ctValidator ()->attachTo (_validator ('date'), 'datenaissance')
		->required ('datenaissance')
		->required ('dateinscription');
		$personne = new StdClass ();
		$personne->datenaissance = '25/12/1976';
		$this->assertTrue ($validator->check ($personne) !== true);

		//Avec la factory dédiée aux objets
		$validator = CopixValidatorFactory::createObject ()->attachTo (_validator ('date'), 'datenaissance')
		->attachTo (_validator ('date'), 'dateinscription')
		->required (array ('datenaissance', 'dateinscription'));
		$personne = new StdClass ();
		$this->assertTrue ($validator->check ($personne) !== true);
		$errors = $validator->check ($personne)->asArray ();
		$this->assertTrue (isset ($errors['datenaissance']));
		$this->assertTrue (isset ($errors['dateinscription']));

		$personne->datenaissance   = '25/12/1976';
		$personne->dateinscription = '01/01/1989';
		$this->assertTrue ($validator->check ($personne));

		//test des asserts
		$validator = _ctValidator ()->attachTo (_validator ('date'), 'datenaissance')
		->required ('datenaissance')
		->required ('dateinscription');
		$personne = new StdClass ();
		$this->assertTrue ($validator->check ($personne) !== true);
		try {
			$validator->assert ($personne);
			$this->assertTrue (false);
		}catch (CopixValidatorException $e){
			$this->assertTrue (true);
		}

		$personne->datenaissance = '25/12/1976';
		try {
			$validator->assert ($personne);
			$this->assertTrue (false);
		}catch (CopixValidatorException $e){
			$this->assertTrue (true);
		}

		$personne->dateinscription = '01/12/2010';
		try {
			$validator->assert ($personne);
			$this->assertTrue (true);
		}catch (CopixValidatorException $e){
			$this->assertTrue (false);
		}

		//test du remplacement des messages d'erreur
		$validator = _ctValidator ('Pas une personne valide')->attachTo (_validator ('date'), 'datenaissance')
		->required ('datenaissance')
		->required ('dateinscription');
		$this->assertTrue ($validator->check (new StdClass ()) !== true);
		$this->assertEquals (count ($validator->check (new StdClass ())->asArray ()), 1);
		$this->assertEquals ('Pas une personne valide', _toString ($validator->check (new StdClass ())));
	}

	function testComplexTypeValidatorWithArray (){
		$validator = _ctValidator ()->attachTo (_validator ('date'), 'datenaissance')
		->attachTo (_validator ('date'), 'dateinscription')
		->required ('datenaissance')
		->required ('dateinscription');
		$personne = array ();
		$this->assertTrue ($validator->check ($personne) !== true);
		$errors = $validator->check ($personne)->asArray ();
		$this->assertTrue (isset ($errors['datenaissance']));
		$this->assertTrue (isset ($errors['dateinscription']));

		$personne['datenaissance']   = '25/12/1976';
		$personne['dateinscription'] = '01/01/1989';
		$this->assertTrue ($validator->check ($personne));

		//Avec la factory dédiée aux tableaux
		$validator = CopixValidatorFactory::createArray ()->attachTo (_validator ('date'), 'datenaissance')
		->attachTo (_validator ('date'), 'dateinscription')
		->required ('datenaissance')
		->required ('dateinscription');
		$personne = array ();
		$this->assertTrue ($validator->check ($personne) !== true);
		$errors = $validator->check ($personne)->asArray ();
		$this->assertTrue (isset ($errors['datenaissance']));
		$this->assertTrue (isset ($errors['dateinscription']));

		$personne['datenaissance']   = '25/12/1976';
		$personne['dateinscription'] = '01/01/1989';
		$this->assertTrue ($validator->check ($personne));
	}

	function testComplexTypeValidator (){
		$personneValidate = _ctValidator ()->attachTo (_validator ('preg', '/[a-zA-Z]+$/'), array ('nom', 'prenom'))
		->attachTo (_validator ('date', array ('max'=>date (CopixI18N::getDateFormat ()))), 'datenaissance');

		$personne = array ('nom'=>'Nom', 'prenom'=>'prenom', 'datenaissance'=>'01/01/1978');
		$this->assertTrue ($personneValidate->check ($personne));

		$personne = array ('nom'=>'Nom3', 'prenom'=>'prenom1', 'datenaissance'=>'01/01/9978');
		$this->assertTrue ($personneValidate->check ($personne) !== true);
		$this->assertTrue (count ($personneValidate->check ($personne)->asArray ()) === 3);
	}

	function testInArrayValidator (){
		$validator = _validator ('inarray', array ('values'=>array ('1', '2', '3')));
		$this->assertTrue ($validator->check ('1'));
		$this->assertTrue ($validator->check ('2'));
		$this->assertTrue ($validator->check ('3'));
		$this->assertTrue ($validator->check ('4') !== true);
	}

	function testValidatorArray (){
		//c'est un tableau
		$validator = _validator ('array');
		$this->assertTrue ($validator->check (array ()));
		$this->assertTrue ($validator->check ('') !== true);

		//C'est un tableau qui contient 1
		$validator = _validator ('array', array ('contains'=>1));
		$this->assertTrue ($validator->check (array (1, 2, 3)));
		$this->assertTrue ($validator->check (array ()) !== true);

		//C'est un tableau qui contient au moins 1 élément
		$validator = _validator ('array', array ('minSize'=>1));
		$this->assertTrue ($validator->check (array (1, 2, 3)));
		$this->assertTrue ($validator->check (array ()) !== true);

		//C'est un tableau qui contient au maximum 2 éléments
		$validator = _validator ('array', array ('maxSize'=>2));
		$this->assertTrue ($validator->check (array (1, 2)));
		$this->assertTrue ($validator->check (array (1, 2, 3)) !== true);

		//C'est un tableau qui contient 1 à 2 éléments
		$validator = _validator ('array', array ('minSize'=>1, 'maxSize'=>2));
		$this->assertTrue ($validator->check (array (1, 2)));
		$this->assertTrue ($validator->check (array (1, 2, 3)) !== true);
		$this->assertTrue ($validator->check (array ()) !== true);

		//C'est un tableau qui contient 1 à 2 éléments
		$validator = _validator ('array', array ('size'=>2));
		$this->assertTrue ($validator->check (array (1, 2)));
		$this->assertTrue ($validator->check (array (1, 2, 3)) !== true);
	}

	function testValidatorBetween (){
		$validator = _validator ('between', array ('min'=>1, 'max'=>10));
		$this->assertTrue ($validator->check (1));
		$this->assertTrue ($validator->check (15) !== true);
	}

	function testValidatorGT (){
		$validator = _validator ('gt', 10);
		$this->assertTrue ($validator->check (11));
		$this->assertTrue ($validator->check (10));
		$this->assertTrue ($validator->check (9) !== true);
	}

	function testValidatorLT (){
		$validator = _validator ('lt', 10);
		$this->assertTrue ($validator->check (9));
		$this->assertTrue ($validator->check (10));
		$this->assertTrue ($validator->check (11) !== true);
	}

	function testValidatorObject (){
		$validator = _validator ('object');
		$this->assertTrue ($validator->check (new StdClass ()));
		$this->assertTrue ($validator->check (array ()) !== true);

		$validator = _validator ('object', array ('implements'=>'ICopixValidator'));
		$this->assertTrue ($validator->check (_validator ('object')));
		$this->assertTrue ($validator->check (new StdClass ()) !== true);
	}

	function testValidatorString (){
		$validator = _validator ('string');
		$this->assertTrue ($validator->check ('test'));
		$this->assertTrue ($validator->check (array ()) !== true);
		$this->assertTrue ($validator->check (1) !== true);

		$validator = _validator ('string', array ('maxLength'=>10));
		$this->assertTrue ($validator->check ('petit'));
		$this->assertTrue ($validator->check ('tres tres tres tres grand') !== true);

		$validator = _validator ('string', array ('minLength'=>10));
		$this->assertTrue ($validator->check ('tres tres tres tres grand'));
		$this->assertTrue ($validator->check ('petit') !== true);

		$validator = _validator ('string', array ('contains'=>'test'));
		$this->assertTrue ($validator->check ('tres tres tres tres grand test'));
		$this->assertTrue ($validator->check ('petit a petit ça va marcher') !== true);
	}

	function testNumeric (){
		$validator = _validator ('numeric');

		$this->assertTrue ($validator->check (1));
		$this->assertTrue ($validator->check (-1));
		$this->assertTrue ($validator->check (-255000));
		$this->assertTrue ($validator->check (255000));
		$this->assertTrue ($validator->check ('1'));
		$this->assertTrue ($validator->check ('-1'));
		$this->assertTrue ($validator->check ('-255000'));
		$this->assertTrue ($validator->check ('255000'));

		$this->assertFalse ($validator->check ('10 petit a petit ça 25') === true);
		$this->assertFalse ($validator->check ('petit a petit ça va marcher') === true);
		$this->assertFalse ($validator->check (10.2) === true);
		$this->assertFalse ($validator->check ('10.2') === true);
		$this->assertFalse ($validator->check ('10,2') === true);

		//valide que cela fonctionne avec les valeurs décimales si donnée
		$validator = _validator ('numeric', array ('allowDecimal'=>true));
		$this->assertTrue ($validator->check (1));
		$this->assertTrue ($validator->check (-1));
		$this->assertTrue ($validator->check (-255000));
		$this->assertTrue ($validator->check (255000));
		$this->assertTrue ($validator->check ('1'));
		$this->assertTrue ($validator->check ('-1'));
		$this->assertTrue ($validator->check ('-255000'));
		$this->assertTrue ($validator->check ('255000'));
		$this->assertTrue ($validator->check (10.2));
		$this->assertTrue ($validator->check ('10.2'));
		$this->assertTrue ($validator->check ('-10.2'));
		$this->assertTrue ($validator->check (-10.2));
		$this->assertFalse ($validator->check ('petit a petit ça va marcher') === true);
		$this->assertFalse ($validator->check ('10 petit . a petit ça 25') === true);
		$this->assertFalse ($validator->check ('10,2') === true);
		$this->assertFalse ($validator->check ('-10,2') === true);

		//valide que cela fonctionne avec les valeurs bornées
		$validator = _validator ('numeric', array ('min'=>10));
		$this->assertTrue ($validator->check (255000));
		$this->assertTrue ($validator->check ('255000'));

		$this->assertFalse ($validator->check ('10 petit a petit ça 25') === true);
		$this->assertFalse ($validator->check ('petit a petit ça va marcher') === true);
		$this->assertFalse ($validator->check (10.2) === true);
		$this->assertFalse ($validator->check ('10.2') === true);
		$this->assertFalse ($validator->check ('10,2') === true);
		$this->assertFalse ($validator->check (1) === true);
		$this->assertFalse ($validator->check (-1) === true);
		$this->assertFalse ($validator->check (-255000) === true);
		$this->assertFalse ($validator->check ('1') === true);
		$this->assertFalse ($validator->check ('-1') === true);
		$this->assertFalse ($validator->check ('-255000') === true);

		$validator = _validator ('numeric', array ('min'=>-10));
		$this->assertTrue ($validator->check (255000));
		$this->assertTrue ($validator->check ('255000'));
		$this->assertTrue ($validator->check (1));
		$this->assertTrue ($validator->check (-1));
		$this->assertTrue ($validator->check ('1'));
		$this->assertTrue ($validator->check ('-1'));

		$this->assertFalse ($validator->check ('10 petit a petit ça 25') === true);
		$this->assertFalse ($validator->check ('petit a petit ça va marcher') === true);
		$this->assertFalse ($validator->check (10.2) === true);
		$this->assertFalse ($validator->check ('10.2') === true);
		$this->assertFalse ($validator->check ('10,2') === true);
		$this->assertFalse ($validator->check (-255000) === true);
		$this->assertFalse ($validator->check ('-255000') === true);
	}

	function testCustom (){
		try {
			$validator = CopixValidatorFactory::create ('test|notexists');
			$this->assertTrue (false);
		}catch (CopixException $e){
			$this->assertTrue (true);
		}

		try {
			$validator = CopixValidatorFactory::create ('test|validatorNoInterface');
			$this->assertTrue (false);
		}catch (CopixException $e){
			$this->assertTrue (true);
		}

		try {
			$validator = CopixValidatorFactory::create ('test|validatorConstructNoInterface');
			$this->assertTrue (false);
		}catch (CopixException $e){
			$this->assertTrue (true);
		}

		$validator = CopixValidatorFactory::create ('test|validatorMod2');
		$this->assertTrue ($validator->check (4));
		$this->assertTrue ($validator->check (5) !== true);

		$validator = _validator ('test|validatorMod2');
		$this->assertTrue ($validator->check (4));
		$this->assertTrue ($validator->check (5) !== true);

		//test avec des paramètres
		$validator = CopixValidatorFactory::create ('test|validatorMod', array ('mod'=>5));
		$this->assertTrue ($validator->check (10));
		$this->assertTrue ($validator->check (6) !== true);

		$validator = _validator ('test|validatorMod', array ('mod'=>5));
		$this->assertTrue ($validator->check (10));
		$this->assertTrue ($validator->check (6) !== true);

		//test avec des paramètres (non tableau)
		$validator = CopixValidatorFactory::create ('test|validatorMod', 6);
		$this->assertTrue ($validator->check (12));
		$this->assertTrue ($validator->check (7) !== true);

		$validator = _validator ('test|validatorMod', 6);
		$this->assertTrue ($validator->check (12));
		$this->assertTrue ($validator->check (7) !== true);

		//test sans passer tous les paramètres
		$validator = _validator ('test|validatorMod');
		try {
			$this->assertTrue ($validator->check (12));
			$this->assertTrue (false);
		}catch (CopixException $e){
			$this->assertTrue (true);
		}
	}

	public function testNotEmpty (){
		$v = _validator ('NotEmpty');

		$this->assertTrue ($v->check ('contenu'));
		$this->assertTrue ($v->check (array ('contenu')));
		$this->assertTrue ($v->check (_ppo (array ('Value'=>'Value'))));

		$this->assertFalse ($v->check (null) === true);
		$this->assertFalse ($v->check (array ()) === true);
		$this->assertFalse ($v->check (0) === true);
		$this->assertFalse ($v->check ('') === true);
		$this->assertFalse ($v->check ('    ') === true);

		$v = _validator ('NotEmpty', false);

		$this->assertTrue ($v->check ('contenu'));
		$this->assertTrue ($v->check (array ('contenu')));
		$this->assertTrue ($v->check (_ppo (array ('Value'=>'Value'))));
		$this->assertTrue ($v->check ('    ') === true);

		$this->assertFalse ($v->check (null) === true);
		$this->assertFalse ($v->check (array ()) === true);
		$this->assertFalse ($v->check (0) === true);
		$this->assertFalse ($v->check ('') === true);

		$v = _validator ('NotEmpty', array ('trim'=>false));

		$this->assertTrue ($v->check ('contenu'));
		$this->assertTrue ($v->check (array ('contenu')));
		$this->assertTrue ($v->check (_ppo (array ('Value'=>'Value'))));
		$this->assertTrue ($v->check ('    ') === true);

		$this->assertFalse ($v->check (null) === true);
		$this->assertFalse ($v->check (array ()) === true);
		$this->assertFalse ($v->check (0) === true);
		$this->assertFalse ($v->check ('') === true);
	}
}