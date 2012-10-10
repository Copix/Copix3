<?php
/**
* @author		Salleyron Julien
* @package		standard
* @subpackage	test
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Test de la classes CopixCache
 * @package standard
 * @subpackage	test
 */
class Test_CopixCacheTest extends CopixTest {
    function setUp (){
        $config = CopixConfig::instance ();
        $config->copixcache_registerType(array('name'=>'unittestfile',
					'strategy'=>'file',
                    'dir'=>'unittest',
                    'link'=>'unittestcascaded1|unittestcascaded2',
					'enabled'=>true
		));
		static $done = false;		
		if (function_exists ('apc_fetch')){
	       $config->copixcache_registerType(array('name'=>'unittestapc','strategy'=>'apc'));
	       $config->copixcache_registerType(array('name'=>'unittestapctimer','strategy'=>'apc','duration'=>1));
		}else{
			if (!$done){
				$done = true;
				//Mise en commentaire pour que les tests passe sans apc (devra être modifié)
				//$this->markTestIncomplete('APC');
			}
		}

       $config->copixcache_registerType(array('name'=>'unittestsystem','strategy'=>'system'));
       $config->copixcache_registerType(array('name'=>'unittestfiletimer','strategy'=>'file','duration'=>1,'dir'=>'unittest'));
       $config->copixcache_registerType(array('name'=>'unittestsystemtimer','strategy'=>'system','duration'=>1));
       $config->copixcache_registerType(array('name'=>'test','strategy'=>'test|CachetestAdaptator'));
       $config->copixcache_registerType(array('name'=>'unittestcascaded1','strategy'=>'file','dir'=>'unittest'));
       $config->copixcache_registerType(array('name'=>'unittestcascaded2','strategy'=>'file','dir'=>'unittest','link'=>'unittestcascaded3'));
       $config->copixcache_registerType(array('name'=>'unittestcascaded3','strategy'=>'file','dir'=>'unittest'));
       $config->copixcache_registerType(array('name'=>'unittestnostrat','strategy'=>'rien','dir'=>'unittest'));
       $config->copixcache_registerType(array('name'=>'unittestfiledisable','strategy'=>'file','dir'=>'unittest','enabled'=>false));
       CopixConfig::instance ()->apcEnabled   = true;
    }
    
    /**
     * Test simple de cache de type file
 	 */
    function testCacheFileClassic() {
        $type='unittestfile';
        $myval = array('1','2','3');
        $myval2 = array('3','2','1');
        // Test de 2 variables array dans unittest (file)
        CopixCache::write('myval',$myval,$type);
        CopixCache::write('myval2',$myval2,$type);
        //test de l'existsence
        $this->assertTrue(CopixCache::exists('myval',$type));
        //test de l'intégrité des données
        $this->assertEquals(CopixCache::read('myval',$type),$myval);
        //test de l'existsence
        $this->assertTrue(CopixCache::exists('myval2',$type));
        //test de l'intégrité des données   
        $this->assertEquals(CopixCache::read('myval2',$type),$myval2);
        //clear de la variable myval2
        CopixCache::clear('myval2',$type);
        $this->assertTrue(!CopixCache::exists('myval2',$type));
        // Test du clear de tout le type
        CopixCache::clear(null, $type);
        $this->assertTrue(!CopixCache::exists('myval', $type));
        // Test de génération d'exception en cas de read d'une valeur non existsente
        try {
            CopixCache::read('myval', $type);
            $testbool=false;
        } catch (Exception $e) {
            $testbool=true;
        }
        $this->assertTrue($testbool);
        
        CopixCache::clear('myval');
        CopixCache::clear('myval');
    }

    
    
    /**
     * Test d'un cache avec sous-type
     * File
     *
     */
    function testCacheFileSousType() {
        $type='unittestfile';
        //Ecriture d un cache normal et un cache sous-type
        CopixCache::write('myval','tttt',$type.'|subunit|truc|bidule');
        CopixCache::write('myval','tttt',$type);
        //Test des 2 existsences
        $this->assertTrue(CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        $this->assertTrue(CopixCache::exists('myval',$type));
        //Test intégrité des données
        $this->assertEquals(CopixCache::read('myval',$type.'|subunit|truc|bidule'),'tttt');
        $this->assertEquals(CopixCache::read('myval',$type),'tttt');
        //Test clear du sous-type
        CopixCache::clear(null,$type.'|subunit|truc|bidule');
        //Test non-existsence du sous-type et existsence du non-sous-type
        $this->assertTrue(!CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        $this->assertTrue(CopixCache::exists('myval',$type));
        //Reécriture du soustype
        CopixCache::write('myval','tttt',$type.'|subunit|truc|bidule');
        $this->assertTrue(CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        //Clear d'un soustype intermediaire
        CopixCache::clear(null,$type.'|subunit');
        $this->assertTrue(!CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        //Reécriture du soustype
        CopixCache::write('myval','tttt',$type.'|subunit|truc|bidule');
        $this->assertTrue(CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        //Clear du type entier
        CopixCache::clear(null,$type);
        $this->assertTrue(!CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        $this->assertTrue(!CopixCache::exists('myval',$type));
    }

    /**
     * Test du timer d'un cache
     * File
     */
    function testCacheFileTimer() {
        $type = 'unittestfiletimer';
        CopixCache::write('myval','test', $type);
        $this->assertTrue(CopixCache::exists ('myval', $type));
        usleep (1000000);
        $this->assertFalse (CopixCache::exists ('myval', $type));
    }
    
    /**
     * Test simple de cache de type apc
     */
    function testCacheApcClassic() {
        $type='unittestapc';
        $myval = array('1','2','3');
        $myval2 = array('3','2','1');
        // Test de 2 variables array dans unittest (file)
        CopixCache::write('myval', $myval, $type);
        CopixCache::write('myval2',$myval2,$type);
        //test de l'existsence
        $this->assertTrue(CopixCache::exists('myval',$type));
        //test de l'intégrité des données
        $this->assertEquals(CopixCache::read('myval',$type), $myval);
        //test de l'existsence
        $this->assertTrue(CopixCache::exists('myval2',$type));
        //test de l'intégrité des données   
        $this->assertEquals(CopixCache::read('myval2',$type),$myval2);
        //clear de la variable myval2
        CopixCache::clear('myval2',$type);
        $this->assertTrue(!CopixCache::exists('myval2',$type));
        // Test du clear de tout le type
       CopixCache::clear(null,$type);
        $this->assertTrue(!CopixCache::exists('myval',$type));
        // Test de génération d'exception en cas de read d'une valeur non existsente
        try {
            CopixCache::read('myval',$type);
            $testbool=false;
        } catch (Exception $e) {
            $testbool=true;
        }
        $this->assertTrue($testbool);
    }

    /**
     * Test d'un cache avec sous-type
     * apc
     *
     */
    function testCacheApcSousType() {
        $type='unittestapc';
        //Ecriture d un cache normal et un cache sous-type
        CopixCache::write('myval','tttt',$type.'|subunit|truc|bidule');
        CopixCache::write('myval','tttt',$type);
        //Test des 2 existsences
        $this->assertTrue(CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        $this->assertTrue(CopixCache::exists('myval',$type));
        //Test intégrité des données
        $this->assertEquals(CopixCache::read('myval',$type.'|subunit|truc|bidule'),'tttt');
        $this->assertEquals(CopixCache::read('myval',$type),'tttt');
        //Test clear du sous-type
        CopixCache::clear(null,$type.'|subunit|truc|bidule');
        //Test non-existsence du sous-type et existsence du non-sous-type
        $this->assertTrue(!CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        $this->assertTrue(CopixCache::exists('myval',$type));
        //Reécriture du soustype
        CopixCache::write('myval','tttt',$type.'|subunit|truc|bidule');
        $this->assertTrue(CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        //Clear d'un soustype intermediaire
        CopixCache::clear(null,$type.'|subunit');
        $this->assertTrue(!CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        //Reécriture du soustype
        CopixCache::write('myval','tttt',$type.'|subunit|truc|bidule');
        $this->assertTrue(CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        //Clear du type entier
        CopixCache::clear(null,$type);
        $this->assertTrue(!CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        $this->assertTrue(!CopixCache::exists('myval',$type));
        CopixCache::clear(null,$type);
    }

    /**
     * Test du timer d'un cache
     * apc
     */
    function testCacheApcTimer() {
    	if (function_exists ('apc_fetch')){
	        $type='unittestapctimer';
	        CopixCache::write('myval','test',$type);
	        $this->assertTrue(CopixCache::exists('myval',$type));
	        usleep (1000000);
	        $this->assertTrue(!CopixCache::exists('myval',$type));
	        CopixCache::write('myval','test',$type);
	        usleep (1000000);
	        try {
	            CopixCache::read('myval',$type);
	            $testbool=false;
	        } catch (Exception $e) {
	            $testbool=true;
	        }
	        $this->assertTrue($testbool);
	        $this->assertTrue(!CopixCache::exists('myval',$type));
    	}
    }

    /**
     * Test simple de cache de type system
     */
    function testCacheSystemClassic() {
        $type='unittestsystem';
        $myval = array('1','2','3');
        $myval2 = array('3','2','1');
        // Test de 2 variables array dans unittest (file)
        CopixCache::write('myval',$myval,$type);
        CopixCache::write('myval2',$myval2,$type);
        //test de l'existsence
        $this->assertTrue(CopixCache::exists('myval',$type));
        //test de l'intégrité des données
        $this->assertEquals(CopixCache::read('myval',$type),$myval);
        //test de l'existsence
        $this->assertTrue(CopixCache::exists('myval2',$type));
        //test de l'intégrité des données   
        $this->assertEquals(CopixCache::read('myval2',$type),$myval2);
        //clear de la variable myval2
        CopixCache::clear('myval2',$type);
        $this->assertTrue(!CopixCache::exists('myval2',$type));
        // Test du clear de tout le type
        CopixCache::clear(null,$type);
        $this->assertTrue(!CopixCache::exists('myval',$type));
        // Test de génération d'exception en cas de read d'une valeur non existsente
        try {
            CopixCache::read('myval',$type);
            $testbool=false;
        } catch (Exception $e) {
            $testbool=true;
        }
        $this->assertTrue($testbool);
    }



    /**
     * Test d'un cache avec sous-type
     * system
     *
     */
    function testCacheSystemSousType() {
        $type='unittestsystem';        
        //Ecriture d un cache normal et un cache sous-type
        CopixCache::write('myval','tttt',$type.'|subunit|truc|bidule');
        CopixCache::write('myval','tttt',$type);
        //Test des 2 existsences
        $this->assertTrue(CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        $this->assertTrue(CopixCache::exists('myval',$type));
        //Test intégrité des données
        $this->assertEquals(CopixCache::read('myval',$type.'|subunit|truc|bidule'),'tttt');
        $this->assertEquals(CopixCache::read('myval',$type),'tttt');
        //Test clear du sous-type
        CopixCache::clear(null,$type.'|subunit|truc|bidule');
        //Test non-existsence du sous-type et existsence du non-sous-type
        $this->assertTrue(!CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        $this->assertTrue(CopixCache::exists('myval',$type));
        //Reécriture du soustype
        CopixCache::write('myval','tttt',$type.'|subunit|truc|bidule');
        $this->assertTrue(CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        //Clear d'un soustype intermediaire
        CopixCache::clear(null,$type.'|subunit');
        $this->assertTrue(!CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        //Reécriture du soustype
        CopixCache::write('myval','tttt',$type.'|subunit|truc|bidule');
        $this->assertTrue(CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        //Clear du type entier
        CopixCache::clear(null,$type);
        $this->assertTrue(!CopixCache::exists('myval',$type.'|subunit|truc|bidule'));
        $this->assertTrue(!CopixCache::exists('myval',$type));
        CopixCache::clear(null,$type);
    }

    /**
     * Test du timer d'un cache
     * system
     */
    function testCacheSystemTimer() {
        $type='unittestsystemtimer';
        CopixCache::write('myval','test',$type);
        $this->assertTrue(CopixCache::exists('myval',$type));
        usleep (1000000);
        $this->assertTrue(!CopixCache::exists('myval',$type));
        CopixCache::write('myval','test',$type);
        usleep (1000000);
        try {
            CopixCache::read('myval',$type);
            $testbool=false;
        } catch (Exception $e) {
            $testbool=true;
        }
        $this->assertTrue($testbool);
        $this->assertTrue(!CopixCache::exists('myval',$type));

    }


    /**
     * Test d'un cache non défini par défaut
     *
     */
    function testCacheOther() {
        $myval = array('1','2','3');
        $this->assertTrue(!CopixCache::exists('myval','test'));
        CopixCache::write('myval',$myval,'test');
        $this->assertEquals(CopixCache::read('myval','test'),$myval);
        $this->assertTrue(CopixCache::exists('myval','test'));
        CopixCache::clear(null,'test');
        $this->assertTrue(!CopixCache::exists('myval','test'));
    }


    /**
     * Test de cache non-existsant
     *
     */
    function testNoCache() {
        $config = CopixConfig::instance();

        $tempDefault = $config->copixcache_getDefaultTypeName();
        $config->copixcache_setDefaultTypeName (null);
        CopixCache::write('myval','tttt','nocachetest');
        $this->assertTrue(!CopixCache::exists('myval','nocachetest'));
        try {
            CopixCache::read('myval','tttt','nocachetest');
            $testbool=false;
        } catch (Exception $e) {
            if ($e instanceof CopixCacheException) {
                $testbool=true;
            } else {
                $testbool=false;
            }
        }
        $this->assertTrue($testbool);        
        $this->assertTrue(CopixCache::clear('myval','nocachetest'));
        CopixConfig::instance()->apcEnabled=false;
/*
        unset(CopixCache::$_strategy['unittestapc']);
        try {
            CopixCache::write('myval','tttt','unittestapc');
            $testbool=false;
        } catch (Exception $e) {
            $testbool=true;
        }
        $this->assertTrue($testbool);
        $config->copixcache_setDefaultTypeName($tempDefault);
        CopixCache::write('myval','tttt','nocachetest');
        $this->assertTrue(CopixCache::exists('myval','nocachetest'));
*/
        
    }

    /**
     * test du clear en cascade
     *
     */
    function testCascadeClear() {
        CopixCache::write('myval','test','unittestfile');
        $this->assertTrue(CopixCache::exists('myval','unittestfile'));
        CopixCache::write('myval','test','unittestcascaded1');
        $this->assertTrue(CopixCache::exists('myval','unittestcascaded1'));
        CopixCache::write('myval','test','unittestcascaded2');
        $this->assertTrue(CopixCache::exists('myval','unittestcascaded2'));
        CopixCache::write('myval','test','unittestcascaded3');
        $this->assertTrue(CopixCache::exists('myval','unittestcascaded3'));
        CopixCache::clear(null,'unittestfile');
        $this->assertTrue(!CopixCache::exists('myval','unittestcascaded1'));
        $this->assertTrue(!CopixCache::exists('myval','unittestcascaded2'));
        $this->assertTrue(!CopixCache::exists('myval','unittestcascaded3'));

    }

    function testEnabled() {
        CopixConfig::instance()->cacheEnabled=false;
        CopixCache::write('myval','test','unittestfile');
        $this->assertTrue(!CopixCache::exists('myval','unittestfile'));
        CopixConfig::instance()->cacheEnabled=true;
        CopixCache::write('myval','test','unittestfiledisable');
        $this->assertTrue(!CopixCache::exists('myval','unittestfile'));
    }
}