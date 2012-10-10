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
* Test des classes de profil
* @package		standard
* @subpackage	copixtest
*/
class CopixTest_CopixSelectorTest extends CopixTest {
   /**
   * Test des quelques fonctions de base
   */
   function testNormalization (){
      //Test des éléments de module simple
   	  $this->assertEquals (CopixSelectorFactory::normalize ('module:copixtest|classedetest'), 'module:copixtest|classedetest');
   	  $this->assertEquals (CopixSelectorFactory::normalize ('copixtest|classedetest'), 'module:copixtest|classedetest');
   	  
   	  CopixContext::push ('copixtest');
   	  $this->assertEquals (CopixSelectorFactory::normalize ('classedetest'), 'module:copixtest|classedetest');
   	  $this->assertEquals (CopixSelectorFactory::normalize ('module:classedetest'), 'module:copixtest|classedetest');
   	  CopixContext::pop ();
   	  
   	  CopixContext::push ('default');
   	  $this->assertEquals (CopixSelectorFactory::normalize ('classedetest'), 'module:default|classedetest');
   	  $this->assertEquals (CopixSelectorFactory::normalize ('module:classedetest'), 'module:default|classedetest');
   	  CopixContext::pop ();
   	  
   	  //Test des éléments ActionGroup
   	  CopixContext::push ('copixtest');
   	  $this->assertEquals (CopixSelectorFactory::normalize ('module:copixtest|ActionGroup::methode'), 'module:copixtest|ActionGroup::methode');
   	  $this->assertEquals (CopixSelectorFactory::normalize ('copixtest|ActionGroup::methode'), 'module:copixtest|ActionGroup::methode');
   	  $this->assertEquals (CopixSelectorFactory::normalize ('ActionGroup::methode'), 'module:copixtest|ActionGroup::methode');
   	  CopixContext::pop ();
   	  
   	  //Test des éléments de type "non modifiables" lors de la normalisation
      $this->assertEquals (CopixSelectorFactory::normalize ('file:/mon/chemin/de/fichier'), 'file:/mon/chemin/de/fichier');
      $this->assertEquals (CopixSelectorFactory::normalize ('var:/mon/chemin/de/fichier'), 'var:/mon/chemin/de/fichier');
      $this->assertEquals (CopixSelectorFactory::normalize ('resource:/mon/chemin/de/fichier'), 'resource:/mon/chemin/de/fichier');
      $this->assertEquals (CopixSelectorFactory::normalize ('copix:/mon/chemin/de/fichier'), 'copix:/mon/chemin/de/fichier');
   }
   
   /**
    * On retente de faire les tests de normalisation pour voir si le cache n'a pas d'impact sur les bons résultats
    */
   function testNormalizationSecondCall (){
   	$this->testNormalization ();
   }
   
   /**
    * Test de la récupération des chemins
    */
   function testPath (){
        $this->markTestIncomplete('Manque les tests sur les sélecteurs de type classe');   	
   	/*
      $this->assertEquals (CopixSelectorFactory::getClass ('module:copixtest|classedetest')->getPath (), 
         CopixModule::getPath ('copixtest').'/classes/classedetest.class.php');
    */
   }
}
?>