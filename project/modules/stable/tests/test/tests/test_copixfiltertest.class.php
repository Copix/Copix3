<?php
/**
* @package		standard
* @subpackage	test
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package		standard
 * @subpackage	test
 */
class Test_CopixFilterTest extends CopixTest {
	public function testFilter (){
		$this->assertEquals (10, CopixFilter::getInt ('10'));
		$this->assertEquals (10, CopixFilter::getInt ('10.1'));
		$this->assertEquals (10, CopixFilter::getInt ('aaaa10.1'));
		
		$this->assertEquals (-1, CopixFilter::getInt ('-1'));
		$this->assertEquals (-2, CopixFilter::getNumeric ('-2'));
		
		$this->assertEquals (10.0, CopixFilter::getFloat ('10.0'));
		$this->assertEquals (10.1, CopixFilter::getFloat ('10.1'));
		$this->assertEquals (10.1, CopixFilter::getFloat ('a1d0.fg1'));
		
		$this->assertEquals (true, CopixFilter::getBoolean ('true'));
		$this->assertEquals (false, CopixFilter::getBoolean ('false'));

		$this->assertEquals ('Bonjour toi', CopixFilter::getAlpha ('B\\""\'o56njour toi'));
		$this->assertEquals ('Bonjourtoi', CopixFilter::getAlpha ('B\\""\'o56njour t\\oi', false));

		$this->assertEquals ('Bonjour 17', CopixFilter::getAlphaNum ('B\\""\'o-{}°njour 1__-()@7'));
		$this->assertEquals ('Bonjour17', CopixFilter::getAlphaNum ('B\\""\'o-{}°njour 1__-()@7', false));
	}
	
	public function testFilterAlpha (){
		$adresse = '12 rue des alouettes';
		$rue = _filter ('alpha')->get ($adresse);
		$this->assertEquals ($rue, ' rue des alouettes');

		$prenom = '    Jean 4 ';
		$prenom = _filter ('alpha', array ('allowSpaces'=>false))->get ($prenom);
		$this->assertEquals ($prenom, 'Jean');
	}
	
	public function testTrim (){
		$this->assertEquals ('a', _filter ('trim')->get ('   a   '));
		$this->assertEquals ('a', _filter ('trim', array ('charList'=>'"'))->get ('"a"'));
		$this->assertEquals ('jean paul', _filter ('trim', array ('charList'=>'"< >'))->get ('< "jean paul" >'));
	}
	
	public function testCase (){
		$this->assertEquals ('ABCD', _filter ('uppercase')->get ('AbCd'));
		$this->assertEquals ('abcd', _filter ('lowercase')->get ('AbCd'));
	}
	
	public function testFilterUpdate (){
		$adresse = '12 rue des alouettes';
		$rue = _filter ('alpha')->update ($adresse);
		$this->assertEquals ($rue, ' rue des alouettes');
		$this->assertEquals ($rue, $adresse);
	}
	
	public function testFilterAlphaNum (){
		$adresse = '12 rue des alouettes';
		$rue = _filter ('alphanum')->get ($adresse);
		$this->assertEquals ($rue, '12 rue des alouettes');

		$age = ' 5<>|%1 ans';
		$age = _filter ('alphanum', array ('allowSpaces'=>false))->get ($age);
		$this->assertEquals ($age, '51ans');
	}
	
	public function testBoolean (){
		$this->assertTrue (_filter ('boolean')->get ('yes'));
		$this->assertTrue (_filter ('boolean')->get ('y'));
		$this->assertTrue (_filter ('boolean')->get ('1'));
		$this->assertTrue (_filter ('boolean')->get (1));
		$this->assertTrue (_filter ('boolean')->get ('enable'));
		$this->assertTrue (_filter ('boolean')->get ('enabled'));
		$this->assertTrue (_filter ('boolean')->get ('oui'));
		$this->assertTrue (_filter ('boolean')->get ('O'));
		$this->assertTrue (_filter ('boolean')->get ('OUI'));
		$this->assertTrue (_filter ('boolean')->get (12));
		
		$this->assertFalse (_filter ('boolean')->get ('NON'));
		$this->assertFalse (_filter ('boolean')->get ('Non'));
		$this->assertFalse (_filter ('boolean')->get ('N'));
		$this->assertFalse (_filter ('boolean')->get ('False'));
		$this->assertFalse (_filter ('boolean')->get (false));
		$this->assertFalse (_filter ('boolean')->get ('disabled'));
		$this->assertFalse (_filter ('boolean')->get ('disable'));
		$this->assertFalse (_filter ('boolean')->get (0));
		$this->assertFalse (_filter ('boolean')->get (-10));
		
		//On vérifie le paramètre defaultIsFalse (qui retourne false si la valeur n'est pas dans le tableau des valeurs vraies)
		$this->assertFalse (_filter ('boolean', array ('defaultIsFalse'=>true))->get (12));
		
		//On vérifie le paramètre true (en ajoutant 12 dans les valeurs vraies)
		$this->assertTrue (_filter ('boolean', array ('defaultIsFalse'=>true, 'true'=>array (12)))->get (12));
		//On regarde au passage que les anciennes valeurs sont toujours correctes		
		$this->assertTrue (_filter ('boolean', array ('defaultIsFalse'=>true, 'true'=>array (12)))->get ('yes'));
		$this->assertTrue (_filter ('boolean', array ('defaultIsFalse'=>true, 'true'=>array (12)))->get ('y'));
		
		//On vérifie le paramètre false (en ajoutant 12 dans les valeurs false
		$this->assertFalse (_filter ('boolean', array ('false'=>array (12)))->get (12));
		//On vérifie les anciennes valeurs
		$this->assertFalse (_filter ('boolean', array ('false'=>array (12)))->get ('False'));
		$this->assertFalse (_filter ('boolean', array ('false'=>array (12)))->get (false));
		
		//On vérifie le paramètre false avec replaceFalseValues
		$this->assertFalse (_filter ('boolean', array ('false'=>array (false), 'replaceFalseValues'=>true))->get (false));
		$this->assertTrue  (_filter ('boolean', array ('false'=>array (false), 'replaceFalseValues'=>true))->get (1));
		
		//On vérifie le paramètre true avec replaceTrueValues
		$this->assertTrue  (_filter ('boolean', array ('true'=>array (false), 'replaceTrueValues'=>true))->get (false));
		$this->assertFalse (_filter ('boolean', array ('true'=>array (false), 'replaceTrueValues'=>true, 'defaultIsFalse'=>true))->get (true));
		$this->assertTrue  (_filter ('boolean', array ('true'=>array (false), 'replaceTrueValues'=>true))->get (1));
		$this->assertFalse (_filter ('boolean', array ('true'=>array (false), 'replaceTrueValues'=>true, 'defaultIsFalse'=>true))->get (1));
	}
	
	public function testCapitalize (){
		$name = _filter ('capitalize')->get ('jean-dupont de la fontaine');
		$this->assertEquals ($name, 'Jean-Dupont De La Fontaine');
		
		//Mots à passer en minuscule
		$name = _filter ('capitalize', array ('lowerCaseWords'=>array ('de', 'la')))->get ('jean-dupont de la fontaine');
		$this->assertEquals ($name, 'Jean-Dupont de la Fontaine');
		
		//On essaye de passer les paramètres de façon distordue
		$name = _filter ('capitalize', array ('lowerCaseWords'=>array ('De', 'lA')))->get ('jean-dupont de la fontaine');
		$this->assertEquals ($name, 'Jean-Dupont de la Fontaine');
		$name = _filter ('capitalize', array ('lowerCaseWords'=>array ('de', 'la')))->get ('jean-dupont dE La fontaine');
		$this->assertEquals ($name, 'Jean-Dupont de la Fontaine');
		$name = _filter ('capitalize', array ('lowerCaseWords'=>array ('De', 'La')))->get ('jean-dupont dE La fontaine');
		$this->assertEquals ($name, 'Jean-Dupont de la Fontaine');
		
		$assertion = _filter ('capitalize')->get ('i love php');
		$this->assertEquals ($assertion, 'I Love Php');
		
		//Mots à passer en majuscule
		$assertion = _filter ('capitalize', array ('upperCaseWords'=>array ('php')))->get ('i love php');
		$this->assertEquals ($assertion, 'I Love PHP');

		//On essaye de passer les paramètres de façon distordue
		$assertion = _filter ('capitalize', array ('upperCaseWords'=>array ('pHp')))->get ('i love php');
		$this->assertEquals ($assertion, 'I Love PHP');
		$assertion = _filter ('capitalize', array ('upperCaseWords'=>array ('php')))->get ('i love phP');
		$this->assertEquals ($assertion, 'I Love PHP');
		$assertion = _filter ('capitalize', array ('upperCaseWords'=>array ('pHp')))->get ('i love phP');
		$this->assertEquals ($assertion, 'I Love PHP');
	}
	
	public function testDefault (){
		$this->assertEquals ('default', _filter ('default', 'default')->get (null));
		$this->assertEquals ('default', _filter ('default', array ('default'=>'default'))->get (null));
		$this->assertNotEquals ('default', _filter ('default', 'default')->get ('test'));

		$this->assertEquals (array (1, 2, 3), _filter ('default', array (1, 2, 3))->get (null));
		$this->assertNotEquals (array (1, 2, 3), _filter ('default', array (1, 2, 3))->get ('test'));

		//On vérifie que les valeurs "vides" ne soient pas considérées comme défaut
		$this->assertNotEquals (array (1, 2, 3), _filter ('default', array (1, 2, 3))->get (''));
		$this->assertNotEquals (array (1, 2, 3), _filter ('default', array (1, 2, 3))->get (array ()));

		$stdClass = new StdClass ();
		$this->assertEquals ($stdClass, _filter ('default', $stdClass)->get (null));
		$this->assertNotEquals ($stdClass, _filter ('default', $stdClass)->get (array (1, 2, 3)));
		
		//Test avec les valeur vides
		//Ici, on doit retourner les valeurs par défaut car le paramètre a filtrer est vide
		$this->assertEquals (array (1, 2, 3), _filter ('default', array ('default'=>array (1, 2, 3),
		                                                                    'empty'=>true))->get (''));
		$this->assertEquals (array (1, 2, 3), _filter ('default', array ('default'=>array (1, 2, 3),
		                                                                    'empty'=>true))->get (array ()));
		//Ici, on doit retourner les valeurs passées car elles sont non vide.
		$this->assertNotEquals (array (1, 2, 3), _filter ('default', array ('default'=>array (1, 2, 3),
		                                                                    'empty'=>true))->get ('not empty'));
		$this->assertNotEquals (array (1, 2, 3), _filter ('default', array ('default'=>array (1, 2, 3),
		                                                                    'empty'=>true))->get (array (1)));

		//Test avec un validateur associé
		//Ici on doit retourner les valeurs par défaut car les valeurs à filtrer ne passent pas au validateur
		$this->assertEquals (array (1, 2, 3), _filter ('default', array ('default'=>array (1, 2, 3),
		                                                                    'validator'=>'array'))->get ('pas un tableau'));
		$this->assertEquals (array (1, 2, 3), _filter ('default', array ('default'=>array (1, 2, 3),
		                                                                    'validator'=>_validator ('string')))->get (array ('pas une chaine'=>'value')));
		//Ici on doit retourner les valeurs passées car elles sont valides		
		$this->assertNotEquals (array (1, 2, 3), _filter ('default', array ('default'=>array (1, 2, 3),
		                                                                    'validator'=>'array'))->get (array ('bien un tableau')));
		$this->assertNotEquals (array (1, 2, 3), _filter ('default', array ('default'=>array (1, 2, 3),
		                                                                    'validator'=>_validator ('string')))->get ('bien une chaine'));

		//On va passer un objet qui n'est pas un validateur comme validateur
		$this->setExpectedException ('CopixException');
		$this->assertNotEquals (array (1, 2, 3), _filter ('default', array ('default'=>array (1, 2, 3),
		                                                                    'validator'=>new StdClass ()))->get ('bien une chaine'));
	}
	
	public function testFloat (){
		$this->assertEquals (100.25, _filter ('float')->get ('ça coute 100.25 euros'));
		$this->assertEquals (1123.25, _filter ('float')->get ('ça coute 1 123.25 euros'));
		$this->assertEquals (1123.25, _filter ('float')->get ('ça coute 1 123,25 euros'));
		$this->assertEquals (-234.56, _filter ('float')->get ('des sous en moins : -234,56'));

		$this->assertNotEquals (-234, _filter ('float')->get ('des sous en moins : -234,56'));

		//On vérifie le paramètre decimal avec les règles d'arrondit
		$this->assertEquals (-235, _filter ('float', array ('decimal'=>0))->get ('des sous en moins : -234,56'));
		$this->assertEquals (-234, _filter ('float', array ('decimal'=>0))->get ('des sous en moins : -234,46'));
		$this->assertEquals (-234.6, _filter ('float', array ('decimal'=>1))->get ('des sous en moins : -234,56'));
		$this->assertEquals (-234.6, _filter ('float', array ('decimal'=>1))->get ('des sous en moins : -234,55'));
		$this->assertEquals (-234.5, _filter ('float', array ('decimal'=>1))->get ('des sous en moins : -234,54'));
		
		$this->assertEquals (235, _filter ('float', array ('decimal'=>0))->get ('des sous en plus : 234,56€'));
		$this->assertEquals (234, _filter ('float', array ('decimal'=>0))->get ('des sous en moins : 234,46€'));
		$this->assertEquals (234.6, _filter ('float', array ('decimal'=>1))->get ('des sous en moins : 234,56€'));
		$this->assertEquals (234.6, _filter ('float', array ('decimal'=>1))->get ('des sous en moins : 234,55€'));
		$this->assertEquals (234.5, _filter ('float', array ('decimal'=>1))->get ('des sous en moins : 234,54€'));
	}

	public function testInt (){
		$this->assertEquals (125, _filter ('int')->get ('ça coute en gros 125.10 euros'));
		$this->assertEquals (125, _filter ('int')->get ('ça coute en gros 125.90 euros'));
		$this->assertEquals (1126, _filter ('int')->get ('ça coute 1 126 euros'));
		$this->assertEquals (-567, _filter ('int')->get ('En moins ça donne -567'));
		
		$this->assertEquals (15, _filter ('int')->get ('En moins ça donne 15-5'));
		$this->assertEquals (-15, _filter ('int')->get ('En moins ça donne -15-5'));
		$this->assertEquals (-1555,  _filter ('int')->get ('En moins ça donne -15 5 et des chiffres "différents" 5'));
	}
	
	public function testNumeric (){
		$this->assertEquals (1252, _filter ('numeric')->get ('en gros 125.2'));
		$this->assertEquals (-1252, _filter ('numeric')->get ('puis aussi - en gros 125.2'));
		$this->assertEquals (-125.2, _filter ('numeric', array ('withComma'=>true))->get ('puis aussi en - gros 125.2'));
		$this->assertEquals (125.2, _filter ('numeric', array ('withComma'=>true))->get ('puis aussi en gros 125.2'));		
		
		$this->assertEquals (1252, _filter ('numeric')->get ('en gros 125,2'));
		$this->assertEquals (-1252, _filter ('numeric')->get ('puis aussi - en gros 125,2'));
		$this->assertEquals (-125.2, _filter ('numeric', array ('withComma'=>true))->get ('puis aussi en - gros 125,2'));
		$this->assertEquals (125.2, _filter ('numeric', array ('withComma'=>true))->get ('puis aussi en gros 125,2'));		
	}
	
	public function testCustom (){
		$this->assertEquals ('Copix', _filter ('test|filtertest')->get ('foooooo'));
		_class ('test|filternointerface');//on vérifie qu'on arrive bien a l'instancier tout de même.
		$this->setExpectedException ('CopixException');
		$this->assertEquals ('Copix', _filter ('test|filternointerface')->get ('foooooo'));
	}
	
	public function testComposite (){
		//dans le premier cas, numeric supprime simplement la virgule puis on passe à int
		$this->assertEquals (12323, _cFilter (_filter ('numeric'), _filter ('int'))->get ('123.23'));
		
		//Dans ce cas, on transforme la chaine en float, puis on demande la valeur à int
		$this->assertEquals (123, _cFilter (_filter ('float'), _filter ('int'))->get ('123.23'));
		
		//On test avec des chaines de caractères en entrée
		$this->assertNotEquals (123, _cFilter ('float')->get ('123.23'));
		$this->assertEquals (123, _cFilter ('float', _filter ('int'))->get ('123.23'));
		$this->assertEquals (123, _cFilter ('float', 'int')->get ('123.23'));
		$this->assertEquals (123, _cFilter (_filter ('float'), 'int')->get ('123.23'));
		$this->assertEquals (123, _cFilter (array ('float', 'int'))->get ('123.23'));
		
		$this->assertEquals (123, CopixFilterFactory::createComposite ('float', 'int')->get ('123.23'));
		try {
			CopixFilterFactory::createComposite (new StdClass ())->get ('123.23');
			$this->assertTrue (false);
			//une exception aurait du être levée
		}catch (CopixException $e){
		}
		
		try {
			CopixFilterFactory::createComposite (1)->get ('123.23');
			$this->assertTrue (false);
			//une exception aurait du être levée
		}catch (CopixException $e){
		}
		
		$this->assertEquals ('Jean Paul', _cFilter ('alpha', 'trim', 'capitalize')->get ('jEAn Paul 2'));
	}
	
	public function testBytesToText (){
		$this->assertEquals ('100,00 B', _filter ('bytesToText')->get (100));
		$this->assertEquals ('1.000,00 B', _filter ('bytesToText')->get (1000));
		$this->assertEquals ('4,88 KB', _filter ('bytesToText')->get (5000)); 
		$this->assertEquals ('19,53 KB', _filter ('bytesToText')->get (20000)); 
		$this->assertEquals ('1,15 GB', _filter ('bytesToText')->get (1234567890));
	}
	
	public function testFileName (){
		$this->assertEquals ('fichier1.txt', _filter ('fileName')->get ('fichier1.txt'));
		$this->assertEquals ('fichier1.txt', _filter ('fileName')->get ('fichier1...txt'));
		$this->assertEquals ('fichier1.txt', _filter ('fileName')->get ('\/#@===%fichier1...txt'));
	}
	
	public function testRemoveAccents(){
		$input  = array ('aaaaaa AAAAAA eeee EEEE iiii IIII oooooo OOOOOO uuuu UUUU yy YY c C n N s S', '<a x="c">e</a>');
		$output = array ('áàâäåã ÁÀÂÄÅÃ éèêë ÉÈÊË ìíîï ÌÍÎÏ òóôöõø ÒÓÔÖÕØ ùúûü ÙÚÛÜ ýÿ ÝŸ ç Ç ñ Ñ š Š', '<a x="ç">é</à>');
		$l = count ($output);
		for($i = 0; $i < $l; $i++){
			$this->assertEquals ( $input[$i], _filter ('RemoveAccents')->get ($output[$i]));
		}
		// TODO tester les différents charsets
		// TODO s'assurer que le HTML malformé n'est pas maltraité au passage
	}
}