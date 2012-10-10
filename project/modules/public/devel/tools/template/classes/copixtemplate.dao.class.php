<?php
class DAORecordCopixTemplate {
    /**
    * Définition du module attaché
    */
    function setQualifier ($qualifier_ctpl){
		$this->qualifier_ctpl = $qualifier_ctpl;
		if (empty ($qualifier_ctpl)){
			$this->modulequalifier_ctpl = null;
		}else{
			try {
      		   $this->modulequalifier_ctpl = CopixSelectorFactory::create ($qualifier_ctpl)->getQualifier ();
			}catch (Exception $e){}
		}
    }
    
    /**
    * récupère le chemin physique du template sur le système de fichier
    */
    function getTemplatePath (){
        if (!empty ($this->qualifier_ctpl)){
			//Using a selector to find out the fileName
			$fileSelector      = & CopixSelectorFactory::create ($this->qualifier_ctpl);
			$fileName          = $fileSelector->fileName;
			if ($fileSelector->module){
			   $overloadedDirectory = $fileSelector->module.'/';
			}else{
				$overloadedDirectory = '';
			}
			$filePath = COPIX_VAR_PATH.'data/templates/'.$this->id_ctpt.'/'.$overloadedDirectory.$fileName;
		}else{
			//Dyntemplate, we'r gonna use TemplateTools to know the filepath
    	    CopixClassesFactory::fileInclude ('template|templatetools');
    	    $path = TemplateTools::getSelector ($this->publicid_ctpl, $this->id_ctpt, true);
    	    $fileSelector = & CopixSelectorFactory::create ($path);
    	    $filePath = $fileSelector->getPath ().$fileSelector->fileName;
		}
		return $filePath;
    }

    /**
    * Sauvegarde le template sur le système de fichier
    */
    function writeOnHardDrive (){
		Copix::RequireClass ('CopixFile');
		return CopixFile::write ($this->getTemplatePath (), $this->content_ctpl);
    }
}

class DAOCopixTemplate {
   /**
   * Getting all the module qualifiers for the given theme
   * @param int $themeId the themeId we wants the module qualifiers for
   */ 
   function getModuleQualifierListForTheme ($themeId){
		$arTemplates = CopixDB::getConnection ()->doQuery ('select DISTINCT (modulequalifier_ctpl) from '.$this->_table.' where id_ctpt '.(is_null($themeId) ? 'IS NULL ' : ' = '.intval ($themeId)).' order by modulequalifier_ctpl');
		$toReturn = array ();
		foreach ($arTemplates as $result){
			$toReturn[] = $result->modulequalifier_ctpl;
		}
		return $toReturn;
   }

   /**
   * gets the numbers of templates declared in the given theme
   * @param id $id_ctpt the theme id
   * @return int Nombre de template qu'il y a dans le thème
   */
   function countByTheme ($id_ctpt){
		$results = CopixDB::getConnection ()->doQuery ('select count(id_ctpl) counter from '.$this->_table.' where id_ctpt = '.sprintf ("%d", intval ($id_ctpt)));
		if (isset ($results[0])){
			return $results[0]->counter;            			
		}
		return 0;
   }

   /**
   * Deletes templates by theme
   */
   function deleteByTheme ($id_ctpt){
		$query = 'delete from '.$this->_table.' where id_ctpt = :id_ctpt';
		return CopixDB::getConnection ()->doQuery ($query, array (':id_ctpt'=>sprintf ("%d", intval ($id_ctpt))));
   }

   function check ($pRecord){
		$result = $this->_compiled_check ($pRecord);
		if ($result === true){
			$result = array ();
		}
        Copix::RequireClass ('CopixError');
        $errorObject = new CopixErrorObject ();
		if (empty($pRecord->qualifier_ctpl) && empty($pRecord->modulequalifier_ctpl)){
           $errorObject->addError ('qualifier_ctpl', CopixI18N::get ('copix:dao.errors.required',CopixI18N::get ('template.dao.qualifier_ctpl').'/'.CopixI18N::get ('template.dao.modulequalifier_ctpl')));
		}
		$result = array_merge ($errorObject->asArray(), $result);
	    return (count ($result)>0) ? $result : true;
   } 
}
?>