<?php
interface ICopixModuleInstaller {
    
    public function processInstall ();
    
    public function processDelete ();
    
    public function processUpdate ();

}
?>