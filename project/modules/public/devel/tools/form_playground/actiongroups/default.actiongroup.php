<?php

/**
 * Exemple d'utilisation de CopixForms
 */

class ActionGroupDefault extends CopixActionGroup {
	
	/**
	 * action par défaut (liste)
	 */
	public function processDefault (){
		return $this->processSimpleList ();
	}

	/**
	 * Liste des éléments que l'on souhaite modifier
	 */
	public function processList (){
		return _arPpo (new CopixPpo (array ('test'=>'putain ca roxx')), 'data.list.tpl');
	}

	/**
	 * Formulaire de modification
	 */
	function processForm (){
		return _arPpo (new CopixPPO (), 'data.form.tpl');
	}
	
	function processSimpleList () {
		return _arPpo (new CopixPpo (), 'datasimple.list.tpl');
	}
	
    public function processAjax () {
        return _arPpo (new CopixPPO (), 'test.tpl');
    }
} 
?>