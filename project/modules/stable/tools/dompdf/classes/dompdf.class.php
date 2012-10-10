<?php
if (! in_array ('DOMPDF_autoload', spl_autoload_functions ())){
	if (include_once (CopixModule::getPath ('dompdf').'dompdf/dompdf_config.inc.php')){
		spl_autoload_register ('DOMPDF_autoload');
	}	
}
?>