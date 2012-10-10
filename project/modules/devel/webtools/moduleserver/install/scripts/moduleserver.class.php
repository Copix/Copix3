<?php
class CopixModuleInstallerModuleServer extends CopixAbstractModuleInstaller {
    public function processInstall () {
		CopixDB::getConnection ()->doQuery ("insert into wsservices (name_wsservices, module_wsservices, file_wsservices, class_wsservices) values ('moduleserver','moduleserver','moduleserver.class.php', 'moduleserver')");
    }
}