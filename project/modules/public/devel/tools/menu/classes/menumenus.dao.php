<?php
class DAOMenuMenus {

	function check ($pRecord){
		
		//Appel de la méthode parente
		if (($arErrors = $this->_compiled_check ($pRecord)) === true){
			$arErrors = array ();
		}

		//on remplace avec les bons libellés
		foreach ($arErrors as $key => $error) {
			$arErrors[$key] = str_replace (
				array ('name_menu'),
			    array (_i18n ('admin.name_menu')),
				$error
			);
		}
		
		//erreurs s'il en existe, true sinon
		return (count ($arErrors) == 0) ? true : $arErrors; 
	}
}
?>