<?php

class ActionGroupDefault extends CopixActionGroup{
	
	function processDefault(){		
		$source = _ioDao('wikipages','1');
	  /*public 'title_wiki' => string 'Accueil' (length=7)
      public 'content_wiki' => string 'a:3:{i:0;a:2:{i:0;s:39:"====== Copix, Framework pour PHP ======";i:1;s:132:"Ce wiki est **disponible Ã  l'ensemble de la communautÃ©** Copix. **Contribuez** Ã  ce dernier pour faire progresser le projet ! :-)";}i:1;i:1;i:2;a:14:{i:0;s:108:"Pour partager votre opinion ou pour poser des questions, utilisez plutÃ´t le forum (http://forum.copix.org).";i:1;s:36:"   * [[Documentation|Documentation]]";i:2;s:16:"   * [[Roadmap]]";i:3;s:12:"   * [[FAQ]]";i:4;s:45:"   * [[RÃ©fÃ©rences & Sites utilisant Co'... (length=953)
      public 'author_wiki' => string 'Julien' (length=6)
      public 'keywords_wiki' => string 'accueil' (length=7)
      public 'description_wiki' => string 'Accueil' (length=7)
      public 'modificationdate_wiki' => string '20070409164929' (length=14)
      public 'creationdate_wiki' => string '20070409164929' (length=14)
      public 'lock_wiki' => string '0' (length=1)
      public 'deleted_wiki' => string '0' (length=1)*/

	  /*
	   *   `title_wiki` varchar(50) NOT NULL,
  `displayedtitle_wiki` varchar(50) default '',
  `heading_wiki` varchar(255) default '',
  `content_wiki` text NOT NULL,
  `author_wiki` varchar(80) default NULL,
  `keywords_wiki` varchar(255) default NULL,
  `description_wiki` varchar(255) default NULL,
  `lang_wiki` varchar (3) default NULL,
  `translatefrom_wiki` varchar(50) default NULL,
  `fromlang_wiki` varchar(3) default NULL,
  `modificationdate_wiki` varchar(14) NOT NULL,
  `creationdate_wiki` varchar(14) NOT NULL,
  `lock_wiki` varchar(1) NOT NULL default '0',
  `deleted_wiki` varchar(1) NOT NULL default '0',
	   */
        $pages = $source->findBy(_daoSp()->orderBy('title_wiki','modificationdate_wiki'));
                
        $arTitles = array();
		foreach ($pages as $page){
			if(!in_array($page->title_wiki,$arTitles)){
				$arTitles[]= $page->title_wiki;
			}
		}
		echo "Pages à traiter: ".count($arTitles);
		_classInclude ('generictools|diff');
		$pages=array();
		$i = 0;
		echo "<br />";
		$sql="drop table if exists wikipages;\n
CREATE TABLE `wikipages` (
  `title_wiki` varchar(50) NOT NULL,
  `displayedtitle_wiki` varchar(50) default '',
  `heading_wiki` varchar(255)  default '',
  `content_wiki` text NOT NULL,
  `author_wiki` varchar(80) default NULL,
  `keywords_wiki` varchar(255) default NULL,
  `description_wiki` varchar(255) default NULL,
  `lang_wiki` varchar (3) default NULL,
  `translatefrom_wiki` varchar(50) default NULL,
  `fromlang_wiki` varchar(3) default NULL,
  `modificationdate_wiki` varchar(14) NOT NULL,
  `creationdate_wiki` varchar(14) NOT NULL,
  `lock_wiki` varchar(1) NOT NULL default '0',
  `deleted_wiki` varchar(1) NOT NULL default '0',
   PRIMARY KEY(`title_wiki`,`lang_wiki`,`modificationdate_wiki`)
);
\n\n";
		$i = 0;
		foreach($arTitles as $title){
			$page = $this->getPatched($title);
			$i++;
			$sql .= "\n\ninsert into wikipages (title_wiki , 
					content_wiki , 
					author_wiki , 
					keywords_wiki,
					description_wiki, 
					lang_wiki ,
					modificationdate_wiki , 
					creationdate_wiki , 
					deleted_wiki)";
			$sql.= "values (";
			$sql.= "'".preg_replace("/'{1}/","''",$page->title_wiki)."',\n";
			$sql.= "'".preg_replace("/'{1}/","''",$page->content_wiki)."',\n";
			$sql.= "'".$page->author_wiki."',\n";
			$sql.= "'".preg_replace("/'{1}/","''",$page->keywords_wiki)."',\n";
			$sql.= "'".preg_replace("/'{1}/","''",$page->description_wiki)."',\n";
			$sql.= "'fr',\n";
			$sql.= "'".$page->modificationdate_wiki."',\n";
			$sql.= "'".$page->creationdate_wiki."',\n";
			$sql.= "'".$page->deleted_wiki."'\n";
			$sql.= ");\n";
		}
		
		$fp = fopen("/tmp/wiki.sql","w");
		fwrite($fp,$sql,strlen($sql));
		fclose($fp);
		echo "<br />Fichier sql créé de $i pages: "."/tmp/wiki.sql";
		exit;
	  
	}
	
	
	public function getPatched ($title) {
		$pages = _ioDao ('wikipages',1)->findBy (_daoSp ()->addCondition ('title_wiki', "=", $title)
						 	    	 ->orderBy("modificationdate_wiki"));
		$lines = array ();
		$content = array ();
		$p = null;
		if ($count = count($pages)) {
			//get last informations
			$p = $pages[$count -1];
			//apply patches
							
				for ($i = 0; $i < $count; $i++) {
					$content[]="";
					$diff = new Diff ($pages[$i]->content_wiki);
					$content = $diff->apply ($content);
				}
			

			$lines = $content;
			$content = "";
			foreach ($lines as $line) {
				$content .= $line . "\n";
			}
			$p->content_wiki = $content;
		}
		return $p;
	}
	
	
}

?>