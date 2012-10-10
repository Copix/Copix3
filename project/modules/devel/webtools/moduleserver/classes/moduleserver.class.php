<?php

class ModuleServer {
    
    
    /**
     * @return string
     */
    public function getList () {
        try {
            $listModule = array();
            foreach (_dao('moduleserver')->findAll() as $module) {
                $listModule[] = $module;
            }
            return serialize($listModule);
        } catch (Exception $e) {
            $test = new stdClass();
            $test->erreur = $e->getMessage();
            return serialize(array($test));
        }
    }
    
    /**
     * @param string $id
     * @return string
     */
    public function getModule ($id) {
        return base64_encode(file_get_contents(COPIX_TEMP_PATH.'/module_'.$id.'.zip'));
    }
    
    /**
     * Renvoi le nom du module correspondant a l'id
     *
     * @param string $pId
     * @return string
     */
    public function getNameById ($pId) {
    	return _dao('moduleserver')->get($id)->module_name;
    }
    /**
     * @return string
     *
     */
    public function test () {
        return 'test';
    }
}

?>