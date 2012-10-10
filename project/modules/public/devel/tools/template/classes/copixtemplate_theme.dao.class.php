<?php
class DAORecordCopixTemplate_Theme {
	/**
    * Vérifie en plus l'unicité de "caption_ctpt"
    */
	function check (){
		if (($checkResult = $this->_compiled->_compiled_check ()) === true){
			$checkResult = array ();
		}

		$dao = & CopixDAOFactory::getInstanceOf ('CopixTemplate_theme');
		$sp  = & CopixDAOFactory::createSearchParams ();
		$sp->addCondition ('caption_ctpt', '=', $this->_compiled->caption_ctpt);
		$sp->addCondition ('id_ctpt', '<>', $this->_compiled->id_ctpt);
		$result = $dao->findBy ($sp);
		if (count ($result) > 0){
			$checkResult = array_merge ($checkResult, (array) CopixI18N::get ('template.error.nameAlreadyExists', array ($this->caption_ctpt)));
		}
		return ((count($checkResult) > 0) ? $checkResult : true);
	}
}
?>