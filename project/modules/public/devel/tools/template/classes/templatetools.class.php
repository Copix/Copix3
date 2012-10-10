<?php
class TemplateTools {
	/**
	* gets the list of the dynamic templates
	* @return array($id=>$caption)
	*/
	function getDynamicTemplates ($template){
		$dao = CopixDAOFactory::getInstanceOf ('template|copixtemplate');
		$sp = CopixDAOFactory::createSearchParams ();
		$sp->addCondition ('modulequalifier_ctpl', '=', $template.'|');
		$sp->addCondition ('qualifier_ctpl', '=', null);
		$sp->addCondition ('id_ctpt', '=', null);
		$results = array ();
		foreach ($dao->findBy ($sp) as $result){
			$results['dynamic/'.$result->publicid_ctpl] = $result->caption_ctpl;
		}
		return $results;
	}

	/**
	* gets a template
	* @param $id the template id
	*/
	function getTemplate ($id){
		$dao = CopixDAOFactory::getInstanceOf ('template|copixtemplate');
		return $dao->get ($id);
	}

	/**
	* gets the template path
	* @param $id - the template public id (integer. If not integer, is returned as is as we expect it to be a Copix Id)
	* @param $theme - the template theme id
	* @param $forceReal Force à donner le vrai sélecteur attendu et ne cherche pas
	*   a vérifier l'existance du fichier
	*/
	function getSelector ($id, $theme, $forceReal = false){
    	if (strpos ($id, '.')){
	        return $id;
      }

      $selectorPath     = 'var:data/dyntemplates/'.$theme.'/'.$id.'.tpl';

    	$fileSelector      = & CopixSelectorFactory::create ($selectorPath);
      $fileName          = $fileSelector->fileName;
		if ($forceReal || file_exists (COPIX_VAR_PATH.$fileName)){
         return $selectorPath;
		}

		return 'var:data/dyntemplates/'.$id.'.tpl';
	}
}
?>
