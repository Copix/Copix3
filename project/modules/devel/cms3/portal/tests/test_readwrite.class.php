<?php

class Test_ReadWrite extends CopixTest {
	
	public function testWritePage(){
		$page = _class ('page');

		$page->description_hei = "test write";
		$page->template_page = "portal|colonne_1.page.tpl";
		$page->caption_hei = "test save";
		$page->parent_heading_public_id_hei = 0;
		
		$article = new PortletArticle();
		$article->content = "article";
		$article->parent_heading_public_id_hei = 0;
		$article->caption_hei = "article";
		_class ('articles|articleServices')->insert($article);

		$portlet1 = _class ('PortletArticle');
		$portlet1->type_portlet = "PortletArticle";
		$portlet1->content_portlet = "test_portlet1";  			
		$portlet1->attach ($article->id_hei);

		$portlet2 = _class ('PortletArticle');
		$portlet2->type_portlet = "PortletArticle";
		$portlet2->content_portlet = "test_portlet2";	
		$portlet2->attach ($article->id_hei);
		
		$page->addPortlet($portlet1, 0);
		$page->addPortlet($portlet2, 0);
		
		//verification des positions
		$this->assertTrue($portlet1->position == 0);
		$position = $page->getPortletPosition($portlet1->getRandomId());
		$this->assertTrue($position['position'] == 0);
		$this->assertTrue($portlet2->position == 1);
		$position = $page->getPortletPosition($portlet2->getRandomId());
		$this->assertTrue($position['position'] == 1);
		
		//
		$this->assertEquals($page->findPortletById($portlet1->getRandomId()), $portlet1);
		$this->assertEquals($page->findPortletById($portlet2->getRandomId()), $portlet2);
		
		$elements = $portlet1->getElements ();
		$this->assertEquals($elements[$article->id_hei]->id_hei, $article->id_hei);
		$elements = $portlet2->getElements ();
		$this->assertEquals($elements[$article->id_hei]->id_hei, $article->id_hei);
		
		_class ('articles|articleServices')->deleteById($article->id_hei);
	}
	
	public function testInsertUpdatePage(){
		$page = _class ('page');
		$page->description_hei = "test write";
		$page->template_page = "portal|colonne_1.page.tpl";
		$page->caption_hei = "test save";
		$page->parent_heading_public_id_hei = 0;
		
		$article = new PortletArticle();
		$article->content = "article";
		$article->parent_heading_public_id_hei = 0;
		$article->caption_hei = "article";
		_class ('articles|articleServices')->insert($article);

		$portlet1 = _class ('PortletArticle');
		$portlet1->type_portlet = "PortletArticle";
		$portlet1->content_portlet = "test_portlet1";  			
		$portlet1->attach ($article->id_hei);

		$portlet2 = _class ('PortletArticle');
		$portlet2->type_portlet = "PortletArticle";
		$portlet2->content_portlet = "test_portlet2";	
		$portlet2->attach ($article->id_hei);
		
		$page->addPortlet($portlet1, 0);
		$page->addPortlet($portlet2, 0);
		
		_class('portal|pageservices')->insert($page);
		
		$id = $page->id_page;
		$page = null;
		
		$page = _class('portal|pageservices')->getById($id);
		$portlets = $page->getPortlets();
		$this->assertEquals($portlets[0][0]->id_portlet, $portlet1->id_portlet);
		$this->assertEquals($portlets[0][1]->id_portlet, $portlet2->id_portlet);
		
		$elements = $portlets[0][0]->getElements ();
		$this->assertEquals($elements[$article->id_hei]->id_hei, $article->id_hei);
		$elements = $portlets[0][1]->getElements ();
		$this->assertEquals($elements[$article->id_hei]->id_hei, $article->id_hei);
		
		$portlets[0][1]->dettach($article->id_hei);
		$elements = $portlets[0][1]->getElements ();
		$this->assertTrue(empty($elements));
		
		$portlets[0][1]->content_portlet = "test_portlet2_update";
		$page->deletePortlet($portlets[0][0]->getRandomId());
		
		_class('portal|pageservices')->update($page);
		
		$page = _class('portal|pageservices')->getById($id);
		$this->assertTrue(sizeof($page->getPortlets()) == 1);
		
		$portlets = $page->getPortlets();

		$this->assertEquals($portlets[0][0]->content_portlet, "test_portlet2_update");
		
		_class('portal|pageservices')->deleteById($id);
		_class ('articles|articleServices')->deleteById($article->id_hei);
	}
	
	public function testInsertVersionPage(){
		$page = _class ('page');
		$page->description_hei = "test write";
		$page->template_page = "portal|colonne_1.page.tpl";
		$page->caption_hei = "test save";
		$page->parent_heading_public_id_hei = 0;
		
		$article = new PortletArticle();
		$article->content = "article";
		$article->parent_heading_public_id_hei = 0;
		$article->caption_hei = "article";
		_class ('articles|articleServices')->insert($article);

		$portlet1 = _class ('PortletArticle');
		$portlet1->type_portlet = "PortletArticle";
		$portlet1->content_portlet = "test_portlet1";  			
		$portlet1->attach ($article->id_hei);

		$portlet2 = _class ('PortletArticle');
		$portlet2->type_portlet = "PortletArticle";
		$portlet2->content_portlet = "test_portlet2";	
		$portlet2->attach ($article->id_hei);
		
		$page->addPortlet($portlet1, 0);
		$page->addPortlet($portlet2, 0);
		
		_class('portal|pageservices')->insert($page);
		
		$id = $page->id_page;
		$page = null;
		
		$page = _class('portal|pageservices')->getById($id);
		$portlets = $page->getPortlets();
		$this->assertEquals($portlets[0][0]->id_portlet, $portlet1->id_portlet);
		$this->assertEquals($portlets[0][1]->id_portlet, $portlet2->id_portlet);
		
		$elements = $portlets[0][0]->getElements ();
		$this->assertEquals($elements[$article->id_hei]->id_hei, $article->id_hei);
		$elements = $portlets[0][1]->getElements ();
		$this->assertEquals($elements[$article->id_hei]->id_hei, $article->id_hei);
		
		$portlets[0][1]->dettach($article->id_hei);
		$elements = $portlets[0][1]->getElements ();
		$this->assertTrue(empty($elements));
		
		$portlets[0][1]->content_portlet = "test_portlet2_update";
		$page->deletePortlet($portlets[0][0]->getRandomId());
		
		_class('portal|pageservices')->version($page);
		
		$idVersion = $page->id_page;
		
		$pageOrigine = _class('portal|pageservices')->getById($id);
		$pageVersion = _class('portal|pageservices')->getById($idVersion);
		$this->assertTrue(sizeof($pageOrigine->getPortlets()) == sizeof($pageVersion->getPortlets()));
		
		$portlets = $pageOrigine->getPortlets();
		$this->assertNotEquals($portlets[0][0]->content_portlet, "test_portlet2_update");
		$portlets = $pageVersion->getPortlets();
		$this->assertEquals($portlets[0][0]->content_portlet, "test_portlet2_update");
		
		_class('portal|pageservices')->deleteById($id);
		_class ('articles|articleServices')->deleteById($article->id_hei);
	}
	
	public function testDelete(){
		$page = _class ('page');
		$page->description_hei = "test write";
		$page->template_page = "portal|colonne_1.page.tpl";
		$page->caption_hei = "test save";
		$page->parent_heading_public_id_hei = 0;
		
		$article = new PortletArticle();
		$article->content = "article";
		$article->parent_heading_public_id_hei = 0;
		$article->caption_hei = "article";
		_class ('articles|articleServices')->insert($article);

		$portlet1 = _class ('PortletArticle');
		$portlet1->type_portlet = "PortletArticle";
		$portlet1->content_portlet = "test_portlet1";  			
		$portlet1->attach ($article->id_hei);

		$portlet2 = _class ('PortletArticle');
		$portlet2->type_portlet = "PortletArticle";
		$portlet2->content_portlet = "test_portlet2";	
		$portlet2->attach ($article->id_hei);
		
		$page->addPortlet ($portlet1, 0);
		$page->addPortlet ($portlet2, 0);
		
		_class('portal|pageservices')->insert ($page);
		
		$id = $page->id_page;
		
		_class ('portal|pageservices')->deleteById ($id);
		_class ('articles|articleServices')->deleteById ($article->id_hei);

		$result = DAOcms_pages::instance ()->findBy (_daoSP()->addCondition('id_page', '=', $id))->fetchAll();
		$this->assertTrue(empty($result));
		$result = DAOcms_portlets::instance ()->findBy (_daoSP()->addCondition('id_page', '=', $id))->fetchAll();
		$this->assertTrue(empty($result));
		$result = DAOcms_portlets_headingelementinformations::instance ()->findBy (_daoSP()->addCondition('public_id_hei', '=', $article->id_hei))->fetchAll();
		$this->assertTrue(empty($result));
	}
	
}