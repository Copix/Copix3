<?php
/**
 * @package     tools
 * @subpackage  tags
 * @author      Duboeuf Damien
 * @copyright   CopixTeam
 * @link        http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Test de la class de service du module tags
 * @package tools
 * @subpackage tags
 */
class tagstest_tagservices extends CopixTest {

    
    public function setUp () {
        $this->_clean ();
    }

    public function tearDown () {
        $this->_clean ();
    }
    
    
    /**
     *  Préfixe pour l'ajout en base
     * 
     */
    private static $prefixe = 'TESTTAGS_';
    
    /**
     * Nettoie la base pour les tests
     *
     */
    private function _clean () {
        $critere = _daoSP ()->addCondition ('name_tag', 'like', 'TESTTAGS_%', 'or')
                            ->addCondition ('name_tag', 'like', 'null', 'or');
        _ioDAO ('tags')             -> deleteBy ($critere);
        _ioDAO ('tags_content')     -> deleteBy ($critere);
        _ioDAO ('tags_informations')-> deleteBy ($critere);
    }
    
    
    /**
     * Ajout normal d'un tag
     *
     */
    public function testAddTags () {
        
        $tagSeul = self::$prefixe.'TAG_Seul';
        $tag1    = self::$prefixe.'TAG_1';
        $tag2    = self::$prefixe.'TAG_2';
        $tag3    = self::$prefixe.'TAG_3';
        
        
        // Ajout d'un tag seul
        _class('tags|tagservices')->add ($tagSeul);
        
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_Seul') !== false);
        
        // Ajout de plusieurs tags seul
        _class('tags|tagservices')->add (array ($tag1, $tag2, $tag3));
        
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_1') !== false);
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_2') !== false);
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_3') !== false);
        
    }
    
    /**
     * Ajout d'un tag dupliqué
     *
     */
    public function testAddTagsDuplicate () {
        
        $tagSeul = self::$prefixe.'TAG_Seul';
        $tag1 = self::$prefixe.'TAG_1';
        $tag2 = self::$prefixe.'TAG_2';
        $tag3 = self::$prefixe.'TAG_3';
        $tag4 = self::$prefixe.'TAG_4';
        
        // Ajout initial des tags
        _class('tags|tagservices')->add (array ($tagSeul, $tag1, $tag2, $tag3));
        
        // Duplication d'un tags seul
        try {
            _class('tags|tagservices')->add ($tagSeul);
            $this->assertTrue (false);
        } catch (CopixException $e) {
            $this->assertTrue (true);
        }
        
        // Duplication de plusieurs tags
        try {
            _class('tags|tagservices')->add (array ($tag1, $tag2, $tag3, $tag4));
            $this->assertTrue (false);
        } catch (CopixException $e) {
            $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_4') === false);
        }
    }
    
    /**
     * Ajout d'un tag vide ou nulle
     *
     */
    public function testAddTagsEmptyOrNull () {
        
        $tagValeurNull = 'null';
        $tag1          = self::$prefixe.'TAG_1';
        $tag3          = self::$prefixe.'TAG_3';
        
        try {
            _class('tags|tagservices')->add ('');
            $this->assertTrue (false);
        } catch (CopixException $e) {
            $this->assertTrue (_ioDAO ('tags')->get ('') === false);
        }
        
        try {
            _class('tags|tagservices')->add (array ($tag1, '', $tag3));
            $this->assertTrue (false);
        } catch (CopixException $e) {
            $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_Seul') === false);
            $this->assertTrue (_ioDAO ('tags')->get ('') === false);
            $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_Seul') === false);
        }
        
        // Ajout de la chaine 'null'
        _class('tags|tagservices')->add ($tagValeurNull);
        $this->assertTrue (_ioDAO ('tags')->get ($tagValeurNull) !== false);
        
    }
    
    
    /**
     * Supression normal des tags
     *
     */
    public function testDeleteTags () {
        
        $tagSeul = self::$prefixe.'TAG_Seul';
        $tag1    = self::$prefixe.'TAG_1';
        $tag2    = self::$prefixe.'TAG_2';
        $tag3    = self::$prefixe.'TAG_3';
        
        // Ajout des tags pour suppression
        _class('tags|tagservices')-> add (array ($tagSeul, $tag1, $tag2, $tag3));
        
        // Suppression d'un tag seul
        _class('tags|tagservices')->delete ($tagSeul);
        
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_Seul') === false);
        
        // Suppresion de plusieurs tags
        _class('tags|tagservices')->delete (array ($tag1, $tag2, $tag3));
        
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_1') === false);
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_2') === false);
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_3') === false);
        
    }
    
    /**
     * Supression de tags vide ou null
     *
     */
    public function testDeleteTagsEmptyOrNull () {
        
        $tagValeurNull = 'null';
        $tag1          = self::$prefixe.'TAG_1';
        $tag3          = self::$prefixe.'TAG_3';
        
        // Ajout des tags pour suppression
        _class('tags|tagservices')-> add (array ($tagValeurNull, $tag1, $tag3));
        
        // Suppression d'un tag seul ''
        try {
            _class('tags|tagservices')->delete ('');
            $this->assertTrue (false);
        }
        catch (CopixException $e)
        {
            $this->assertTrue (true);
        }
        // Suppression d'un tag seul null
        try {
            _class('tags|tagservices')->delete (null);
            $this->assertTrue (false);
        }
        catch (CopixException $e)
        {
            $this->assertTrue (_ioDAO ('tags')->get ($tagValeurNull) !== false);
        }
        
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_Seul') === false);
        
        // Suppresion de plusieurs tags ''
        try {
            _class('tags|tagservices')->delete (array ($tag1, '', $tag3));
        }
        catch (CopixException $e) 
        {
            $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_1') !== false);
            $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_3') !== false);
        }
        
    
        // Suppresion de plusieurs tags null
        try {
            _class('tags|tagservices')->delete (array ($tag1, null, $tag3));
        }
        catch (CopixException $e) 
        {
            $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_1') !== false);
            $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_3') !== false);
            $this->assertTrue (_ioDAO ('tags')->get ($tagValeurNull) !== false);
        }
        
        // Supression du tag avec la chaine 'null'
        _class('tags|tagservices')->delete ($tagValeurNull);
        $this->assertTrue (_ioDAO ('tags')->get ($tagValeurNull) === false);
        
    }
    
    
    /**
     * Ajoute une association avec un tag et plusieurs qui n'existe pas en base
     * On testera aussi l'ajout d'un nouveau tag d'une manière dupliquée avec une casse différent
     */
    public function testAddAssociationTagNoExist () {
        
        $kind  = 'tags';
        $idobj = 'test1';
        
        // Ajout de 1 tag seul
        _class('tags|tagservices')->addAssociation ($idobj, $kind, self::$prefixe.'TAG_Seul');
        
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_Seul') !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe.'TAG_Seul', $kind, $idobj) !== false);
        
        // Ajout de plusieurs tags à la fois
        _class('tags|tagservices')->addAssociation ($idobj, $kind, array (self::$prefixe.'TAG_1', self::$prefixe.'TAG_2', self::$prefixe.'TAG_3'));
        
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_1') !== false);
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_2') !== false);
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_3') !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe.'TAG_1', $kind, $idobj) !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe.'TAG_2', $kind, $idobj) !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe.'TAG_3', $kind, $idobj) !== false);
        
        // Ajout de plusieurs tags à la fois d'une manière dupliquer avec une casse différente
        _class('tags|tagservices')->addAssociation ($idobj, $kind, array (self::$prefixe.'TAG_D', self::$prefixe.'TAG_D', self::$prefixe.'TAG_D'));
        
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_D') !== false);
        
    }
    
    /**
     * Ajoute une association avec un tag et plusieurs qui existe en base
     * On testera aussi l'ajout d'un nouveau tag d'une manière dupliquée avec une casse différent
     */
    public function testAddAssociationTagExist () {
        
        $kind  = 'tags';
        $idobj = 'test2';
        
        // Pré-création des tags en base
        _class('tags|tagservices')->add (self::$prefixe.'TAG_Seul');
        _class('tags|tagservices')->add (self::$prefixe.'TAG_1');
        _class('tags|tagservices')->add (self::$prefixe.'TAG_2');
        _class('tags|tagservices')->add (self::$prefixe.'TAG_3');
        _class('tags|tagservices')->add (self::$prefixe.'TAG_D');
        
        
        // Ajout de 1 tag seul
        _class('tags|tagservices')->addAssociation ($idobj, $kind, self::$prefixe.'TAG_Seul');
        
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_Seul') !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe.'TAG_Seul', $kind, $idobj) !== false);
        
        // Ajout de plusieurs tags à la fois
        _class('tags|tagservices')->addAssociation ($idobj, $kind, array (self::$prefixe.'TAG_1', self::$prefixe.'TAG_2', self::$prefixe.'TAG_3'));
        
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_1') !== false);
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_2') !== false);
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_3') !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe.'TAG_1', $kind, $idobj) !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe.'TAG_2', $kind, $idobj) !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe.'TAG_3', $kind, $idobj) !== false);
        
        // Ajout de plusieurs tags à la fois d'une manière dupliquer avec une casse différent
        _class('tags|tagservices')->addAssociation ($idobj, $kind, array (self::$prefixe.'TAG_D', self::$prefixe.'TAg_D', self::$prefixe.'taG_D'));
        
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe.'TAG_D') !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe.'TAG_D', $kind, $idobj) !== false);
        
    }
    
    /**
     * Ajoute une association qui existe déjà
     *
     */
    public function testAddAssociationAlreadyExist () {
        
        $kind  = 'tags';
        $idobj = 'test3';
        
        // Ajout de 1 tag seul
        _class('tags|tagservices')->addAssociation ($idobj, $kind, self::$prefixe.'TAG_Seul');
        _class('tags|tagservices')->addAssociation ($idobj, $kind, self::$prefixe.'TAG_Seul');
        
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe . 'TAG_Seul') !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe . 'TAG_Seul', $kind, $idobj) !== false);
        
        // Ajout de plusieurs tags à la fois
        _class('tags|tagservices')->addAssociation ($idobj, $kind, array (self::$prefixe . 'TAG_1', self::$prefixe . 'TAG_2', self::$prefixe . 'TAG_3'));
        _class('tags|tagservices')->addAssociation ($idobj, $kind, array (self::$prefixe . 'TAG_1', self::$prefixe . 'TAG_2', self::$prefixe . 'TAG_3', self::$prefixe . 'TAG_4'));
        
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe . 'TAG_1') !== false);
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe . 'TAG_2') !== false);
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe . 'TAG_3') !== false);
        $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe . 'TAG_4') !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe . 'TAG_1', $kind, $idobj) !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe . 'TAG_2', $kind, $idobj) !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe . 'TAG_3', $kind, $idobj) !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe . 'TAG_4', $kind, $idobj) !== false);
        
        $this->assertTrue (true);
    }
    
    /**
     * Ajoute une association vide ou null
     * 
     */
    public function testAddAssociationEmptyOrNull () {
        
        $kind  = 'tags';
        $idobj = 'test';
        
        // Ajout de 1 tag seul
        try {
            
            _class('tags|tagservices')->addAssociation ($idobj, $kind, '');
            $this->assertTrue (false);
            
        }
        catch (CopixException $e)
        {
            
            $this->assertTrue (_ioDAO ('tags')->get ('') === false);
            $this->assertTrue (_ioDAO ('tags_content')->get ('', $kind, $idobj) === false);
        }
        
        // Ajout de plusieurs tags à la fois
        try {
            
            _class('tags|tagservices')->addAssociation ($idobj, $kind, array (self::$prefixe . 'TAG_1', '', self::$prefixe . 'TAG_3'));
        $this->assertTrue (false);
            
        }
        catch (CopixException $e)
        {
            
            $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe . 'TAG_1') === false);
            $this->assertTrue (_ioDAO ('tags')->get ('') === false);
            $this->assertTrue (_ioDAO ('tags')->get (self::$prefixe . 'TAG_3') === false);
            $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe . 'TAG_1', $kind, $idobj) === false);
            $this->assertTrue (_ioDAO ('tags_content')->get ('', $kind, $idobj) === false);
            $this->assertTrue (_ioDAO ('tags_content')->get (self::$prefixe . 'TAG_3', $kind, $idobj) === false);
        }
    }
    
    
    /**
     * Recupere les associations quand elles existent
     *
     */
    public function testGetAssociationWhenExist () {
        
        $kind   = 'tags';
        $idobj1 = 'test1';
        $idobj2 = 'test2';
        
        // Ajout de plusieurs tags à la fois
        _class('tags|tagservices')-> addAssociation ($idobj1, $kind, self::$prefixe.'TAG_Seul');
        _class('tags|tagservices')-> addAssociation ($idobj2, $kind, array (self::$prefixe.'TAG_2.1', self::$prefixe.'TAG_2.2', self::$prefixe.'TAG_2.3'));
        
        // Test un get seul
        $tab1 = array ();
        $tab1 [] = self::$prefixe.'TAG_Seul';
        
        $this->assertEquals ($tab1, _class('tags|tagservices')-> getAssociation ($idobj1, $kind));
        
        // Test un get multiple
        $tab2 = array ();
        $tab2 [] = self::$prefixe.'TAG_2.1';
        $tab2 [] = self::$prefixe.'TAG_2.2';
        $tab2 [] = self::$prefixe.'TAG_2.3';
        sort ($tab2);
        
        $this->assertEquals ($tab2, _class('tags|tagservices')-> getAssociation ($idobj2, $kind));
    }
    
    /**
     * Recupere les associations quand elles n'existent pas
     *
     */
    public function testGetAssociationWhenNotExist () {
        
        $kind  = 'tags';
        $idobj = 'test';
        
        // Test un get seul
        $this->assertEquals (array (), _class('tags|tagservices')-> getAssociation ($idobj, $kind));
        
    }
    
    
    /**
     * Supprime des associtions
     *
     */
    public function testDeleteAssociation () {
        
        $kind   = 'tags';
        $idobj1 = 'test1';
        $idobj2 = 'test2';
        $idobj3 = 'test3';
        
        
        // Ajout des tags à la fois
        _class('tags|tagservices')-> addAssociation ($idobj1, $kind, self::$prefixe.'TAG_Seul');
        _class('tags|tagservices')-> addAssociation ($idobj2, $kind, array (self::$prefixe.'TAG_2.1', self::$prefixe.'TAG_2.2'));
        _class('tags|tagservices')-> addAssociation ($idobj3, $kind, array (self::$prefixe.'TAG_3.1', self::$prefixe.'TAG_3.2', self::$prefixe.'TAG_3.3'));
        
        // Test la supression de un seul tag
        _class('tags|tagservices')->deleteAssociation ($idobj1, $kind, self::$prefixe.'TAG_Seul');
        
        $this->assertEquals (array (), _class('tags|tagservices')-> getAssociation ($idobj1, $kind, self::$prefixe.'TAG_Seul'));
        
        
        // Test la supression de plusieurs tags
        _class('tags|tagservices')->deleteAssociation ($idobj2, $kind);
        
        $this->assertEquals (array (), _class('tags|tagservices')-> getAssociation ($idobj2, $kind, self::$prefixe.'TAG_2.1'));
        $this->assertEquals (array (), _class('tags|tagservices')-> getAssociation ($idobj2, $kind, self::$prefixe.'TAG_2.2'));
        
        
        // Test la supression de plusieurs tags avec tableau
        _class('tags|tagservices')->deleteAssociation ($idobj3, $kind, array (self::$prefixe.'TAG_3.1', self::$prefixe.'TAG_3.2'));
        
        $this->assertEquals (array (self::$prefixe.'TAG_3.3'), _class('tags|tagservices')-> getAssociation ($idobj3, $kind));
        
    }
    
    
    /**
     * Déplace une association en supprimant l'ancien tag
     *
     */
    public function testmoveAssociationWithDeleteTag () {
        
        $tagOld = self::$prefixe.'TAG_1';
        $tagNew = self::$prefixe.'TAG_2';
        
        // Pré-création des tags en base
        $dao1    = _record ('tags');
        $dao2    = _record ('tags');
        $dao1->name_tag = self::$prefixe.'TAG_1';
        $dao2->name_tag = self::$prefixe.'TAG_2';
        _ioDAO ('tags')->insert ($dao1);
        _ioDAO ('tags')->insert ($dao2);
        
        // Ajout de plusieurs tags à la fois
        _class('tags|tagservices')->addAssociation ('id1', 'kind1', $tagOld);
        _class('tags|tagservices')->addAssociation ('id2', 'kind1', $tagOld);
        _class('tags|tagservices')->addAssociation ('id1', 'kind2', $tagOld);
        
        // Déplacement des association du tags
        _class('tags|tagservices')->move ($tagOld, $tagNew, true);
        
        $this->assertTrue (_ioDAO ('tags')->get ($tagOld) === false);
        $this->assertEquals (array ($tagNew), _class('tags|tagservices')-> getAssociation ('id1', 'kind1'));
        $this->assertEquals (array ($tagNew), _class('tags|tagservices')-> getAssociation ('id2', 'kind1'));
        $this->assertEquals (array ($tagNew), _class('tags|tagservices')-> getAssociation ('id1', 'kind2'));
        
    }
    
    /**
     * Déplace une association sans supprimant l'ancien tag
     *
     */
    public function testMoveAssociationWithoutDeleteTag () {
                
        $tagOld = self::$prefixe.'TAG_1';
        $tagNew = self::$prefixe.'TAG_2';
        
        // Pré-création des tags en base
        $dao1    = _record ('tags');
        $dao2    = _record ('tags');
        $dao1->name_tag = self::$prefixe.'TAG_1';
        $dao2->name_tag = self::$prefixe.'TAG_2';
        _ioDAO ('tags')->insert ($dao1);
        _ioDAO ('tags')->insert ($dao2);
        
        // Ajout de plusieurs tags à la fois
        _class('tags|tagservices')->addAssociation ('id1', 'kind1', $tagOld);
        _class('tags|tagservices')->addAssociation ('id2', 'kind1', $tagOld);
        _class('tags|tagservices')->addAssociation ('id1', 'kind2', $tagOld);
        
        // Déplacement des association du tags
        _class('tags|tagservices')->move ($tagOld, $tagNew);
        
        $this->assertTrue (_ioDAO ('tags')->get ($tagOld) !== false);
        $this->assertEquals (array ($tagNew), _class('tags|tagservices')-> getAssociation ('id1', 'kind1'));
        $this->assertEquals (array ($tagNew), _class('tags|tagservices')-> getAssociation ('id2', 'kind1'));
        $this->assertEquals (array ($tagNew), _class('tags|tagservices')-> getAssociation ('id1', 'kind2'));
    }
    
    /**
     * Tente le déplacement d'un tags quand il n'existe pas
     *
     */
    public function testMoveAssociationWhenTagNotExist () {
                
        $tagOld = self::$prefixe.'TAG_1';
        $tagNew = self::$prefixe.'TAG_2';
        $fake   = self::$prefixe.'FAKE';
        
        // Pré-création des tags en base
        $dao1    = _record ('tags');
        $dao2    = _record ('tags');
        $dao1->name_tag = self::$prefixe.'TAG_1';
        $dao2->name_tag = self::$prefixe.'TAG_2';
        _ioDAO ('tags')->insert ($dao1);
        _ioDAO ('tags')->insert ($dao2);
        
        // Ajout de plusieurs tags à la fois
        _class('tags|tagservices')->addAssociation ('id1', 'kind1', $tagOld);
        _class('tags|tagservices')->addAssociation ('id2', 'kind1', $tagOld);
        _class('tags|tagservices')->addAssociation ('id1', 'kind2', $tagOld);
        
        // Déplacement des association du tags si l'ancien tag n'existe pas
        try {
            _class('tags|tagservices')->move ($fake, $tagNew);
            $this->assertTrue (false);
        }
        catch (CopixException $e)
        {
            $this->assertTrue (true);
        }
        
        // Déplacement des association du tags si l'ancien tag n'existe pas
        _class('tags|tagservices')->move ($tagOld, $fake);
        $this->assertTrue (_ioDAO ('tags')->get ($fake) !== false);
        
    }

    
    /**
     * Renomme un tag 
     *
     */
    public function testRenameTagNewTag () {
        
        $tagOld = self::$prefixe.'TAG_Old';
        $tagNew = self::$prefixe.'TAG_New';
        
        
        // Ajout des tags pour suppression
        _class('tags|tagservices')->addAssociation ('id1', 'kind1', $tagOld);
        _class('tags|tagservices')->addAssociation ('id2', 'kind1', $tagOld);
        _class('tags|tagservices')->addAssociation ('id1', 'kind2', $tagOld);
        
        // Renomme le tag
        _class('tags|tagservices')-> rename ($tagOld, $tagNew);
        $this->assertTrue (_ioDAO ('tags')->get ($tagOld) === false);
        $this->assertTrue (_ioDAO ('tags')->get ($tagNew) !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get ($tagOld, 'kind1', 'id1') === false);
        $this->assertTrue (_ioDAO ('tags_content')->get ($tagOld, 'kind1', 'id2') === false);
        $this->assertTrue (_ioDAO ('tags_content')->get ($tagOld, 'kind2', 'id1') === false);
        $this->assertTrue (_ioDAO ('tags_content')->get ($tagNew, 'kind1', 'id1') !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get ($tagNew, 'kind1', 'id2') !== false);
        $this->assertTrue (_ioDAO ('tags_content')->get ($tagNew, 'kind2', 'id1') !== false);
        
        
    }

     /**
     * Ne renomme pas le tag si la destination existe déja
     *
     */
    public function testRenameTagNewTagDestExist () {
        
        $tagOld = self::$prefixe.'TAG_Old';
        $tagNew = self::$prefixe.'TAG_New';
        
        
        // Ajout des tags pour suppression
        _class('tags|tagservices')->add ($tagNew);
        _class('tags|tagservices')->addAssociation ('id1', 'kind1', $tagOld);
        _class('tags|tagservices')->addAssociation ('id2', 'kind1', $tagOld);
        _class('tags|tagservices')->addAssociation ('id1', 'kind2', $tagOld);
        
        // Renomme le tag vesr un tag existant
        try {
            _class('tags|tagservices')-> rename ($tagOld, $tagNew);
            $this->assertTrue (false);
        }
        catch (CopixException $e)
        {
            $this->assertTrue (_ioDAO ('tags')->get ($tagOld) !== false);
            $this->assertTrue (_ioDAO ('tags')->get ($tagNew) !== false);
            $this->assertTrue (_ioDAO ('tags_content')->get ($tagOld, 'kind1', 'id1') !== false);
            $this->assertTrue (_ioDAO ('tags_content')->get ($tagOld, 'kind1', 'id2') !== false);
            $this->assertTrue (_ioDAO ('tags_content')->get ($tagOld, 'kind2', 'id1') !== false);
            $this->assertTrue (_ioDAO ('tags_content')->get ($tagNew, 'kind1', 'id1') === false);
            $this->assertTrue (_ioDAO ('tags_content')->get ($tagNew, 'kind1', 'id2') === false);
            $this->assertTrue (_ioDAO ('tags_content')->get ($tagNew, 'kind2', 'id1') === false);
        }
    }
    
    /**
     * Ne renomme pas le tag si la destination est null ou vide
     *
     */
    public function testRenameTagDestNullOrEmpty () {
        
        $tagOld = self::$prefixe.'TAG_Old';
    
        // Ajout des tags pour suppression
        _class('tags|tagservices')->addAssociation ('id1', 'kind1', $tagOld);
        _class('tags|tagservices')->addAssociation ('id2', 'kind1', $tagOld);
        _class('tags|tagservices')->addAssociation ('id1', 'kind2', $tagOld);
        
        // Renomme le tag vers un tag null
        try {
            _class('tags|tagservices')-> rename ($tagOld, null);
            $this->assertTrue (false);
        }
        catch (CopixException $e)
        {
            $this->assertTrue (_ioDAO ('tags')->get ($tagOld) !== false);
            $this->assertTrue (_ioDAO ('tags_content')->get ($tagOld, 'kind1', 'id1') !== false);
            $this->assertTrue (_ioDAO ('tags_content')->get ($tagOld, 'kind1', 'id2') !== false);
            $this->assertTrue (_ioDAO ('tags_content')->get ($tagOld, 'kind2', 'id1') !== false);
        }
        
    
        // Renomme le tag vers un tag avec la chaine ''
        try {
            _class('tags|tagservices')-> rename ($tagOld, '');
            $this->assertTrue (false);
        }
        catch (CopixException $e)
        {
            $this->assertTrue (_ioDAO ('tags')->get ($tagOld) !== false);
            $this->assertTrue (_ioDAO ('tags_content')->get ($tagOld, 'kind1', 'id1') !== false);
            $this->assertTrue (_ioDAO ('tags_content')->get ($tagOld, 'kind1', 'id2') !== false);
            $this->assertTrue (_ioDAO ('tags_content')->get ($tagOld, 'kind2', 'id1') !== false);
        }
    }
    
}

?>