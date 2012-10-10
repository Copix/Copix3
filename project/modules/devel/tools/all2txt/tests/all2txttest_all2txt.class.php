<?php

class all2txtTest_all2txt extends CopixTest {
    
    const TEST_PATH = 'tests/';
    const FILETEST_NAME = 'documentTest';
    
    public function setUp () {
        
        if (!file_exists (COPIX_TEMP_PATH.self::TEST_PATH)) {
            CopixFile::createDir (COPIX_TEMP_PATH.self::TEST_PATH);
        } else{
            //CopixFile::removeFileFromPath (COPIX_TEMP_PATH.self::TEST_PATH);
        }
        
    }
    
    
    public function testPDF2TXT_default () {
        
        $filePDF = CopixModule::getPath ('all2txt').COPIX_RESOURCES_DIR.self::TEST_PATH.self::FILETEST_NAME.'.pdf';
        $fileTXT = COPIX_TEMP_PATH.self::TEST_PATH.self::FILETEST_NAME.'.txt';
        
        _class ('all2txt|all2txt')->pdf2txt ($filePDF, $fileTXT);
        $this->assertTrue (file_exists ($fileTXT));
    }
    
    public function testPDF2TXT_phpMethode () {
        
        $filePDF = CopixModule::getPath ('all2txt').COPIX_RESOURCES_DIR.self::TEST_PATH.self::FILETEST_NAME.'.pdf';
        $fileTXT = COPIX_TEMP_PATH.self::TEST_PATH.self::FILETEST_NAME.'2.txt';
        
        _class ('all2txt|all2txt')->pdf2txt ($filePDF, $fileTXT, true);
        
        // Expérimental
        $this->assertTrue (file_exists ($fileTXT));
        chmod ($fileTXT, 0777);
    }
    
    public function testDOC2TXT_default () {
        
        $fileDOC = CopixModule::getPath ('all2txt').COPIX_RESOURCES_DIR.self::TEST_PATH.self::FILETEST_NAME.'.doc';
        $fileTXT = COPIX_TEMP_PATH.self::TEST_PATH.self::FILETEST_NAME.'3.txt';
        
        _class ('all2txt|all2txt')->doc2txt ($fileDOC, $fileTXT);
        $this->assertTrue (file_exists ($fileTXT));
        chmod ($fileTXT, 0777);
    }
    
    public function testDOC2TXT_phpMethode () {
        
        $fileDOC = CopixModule::getPath ('all2txt').COPIX_RESOURCES_DIR.self::TEST_PATH.self::FILETEST_NAME.'.doc';
        $fileTXT = COPIX_TEMP_PATH.self::TEST_PATH.self::FILETEST_NAME.'4.txt';
        
        _class ('all2txt|all2txt')->doc2txt ($fileDOC, $fileTXT, true);
        $this->assertTrue (file_exists ($fileTXT));
        chmod ($fileTXT, 0777);
        
        // Expérimental
        $this->assertTrue (file_exists ($fileTXT));
    }
    
    public function testHTML2TXT () {
        
        $fileHTML = CopixModule::getPath ('all2txt').COPIX_RESOURCES_DIR.self::TEST_PATH.self::FILETEST_NAME.'.html';
        $fileTXT = COPIX_TEMP_PATH.self::TEST_PATH.self::FILETEST_NAME.'5.txt';
        
        _class ('all2txt|all2txt')->html2txt ($fileHTML, $fileTXT);
        $this->assertTrue (file_exists ($fileTXT));
        chmod ($fileTXT, 0777);
        
        $this->assertTrue (file_exists ($fileTXT));
    }
    
    public function testFileNotFound () {
        
        $file = CopixModule::getPath ('all2txt').COPIX_RESOURCES_DIR.self::TEST_PATH.self::FILETEST_NAME.'.NotFound';
        $fileTXT = COPIX_TEMP_PATH.self::TEST_PATH.self::FILETEST_NAME.'NotFound.txt';
        
        try {
            _class ('all2txt|all2txt')->pdf2txt ($file, $fileTXT, true);
            $this->assertTrue (false);
        } catch (CopixException $e) {
            $this->assertTrue (true);
        }
        
    try {
            _class ('all2txt|all2txt')->doc2txt ($file, $fileTXT, true);
            $this->assertTrue (false);
        } catch (CopixException $e) {
            $this->assertTrue (true);
        }
    }
    
    public function testFileNotCreate () {
        $path = COPIX_TEMP_PATH.self::TEST_PATH.self::FILETEST_NAME.'NotReplace/';
        $fileDOC = CopixModule::getPath ('all2txt').COPIX_RESOURCES_DIR.self::TEST_PATH.self::FILETEST_NAME.'.doc';
        $filePDF = CopixModule::getPath ('all2txt').COPIX_RESOURCES_DIR.self::TEST_PATH.self::FILETEST_NAME.'.pdf';
        $fileTXT = $path.'texte.txt';
        
        if (file_exists($path)) {
            chmod($path, 0777);
            CopixFile::removeDir ($path);
        }
        
        CopixFile::createDir($path);
        chmod($path, 0000);
        try {
            _class ('all2txt|all2txt')->pdf2txt ($filePDF, $fileTXT);
            $this->assertTrue (false);
        } catch (CopixException $e) {
            $this->assertTrue (true);
        }
        chmod($path, 0777);
        CopixFile::removeDir ($path);
    }
    
    public function testFileNotReplace () {
        $path = COPIX_TEMP_PATH.self::TEST_PATH.self::FILETEST_NAME.'NotReplace/';
        $fileDOC = CopixModule::getPath ('all2txt').COPIX_RESOURCES_DIR.self::TEST_PATH.self::FILETEST_NAME.'.doc';
        $filePDF = CopixModule::getPath ('all2txt').COPIX_RESOURCES_DIR.self::TEST_PATH.self::FILETEST_NAME.'.pdf';
        $fileTXT = $path.'texte.txt';
        
        if (file_exists($path)) {
            chmod($path, 0777);
            CopixFile::removeDir ($path);
        }
        
        CopixFile::createDir($path);
        CopixFile::write ($fileTXT, '');
        chmod($path   , 0333);
        chmod($fileTXT, 0222);
        //try {
            //_class ('all2txt|all2txt')->pdf2txt ($filePDF, $fileTXT);
        //    $this->assertTrue (false);
        //} catch (CopixException $e) {
        //    $this->assertTrue (true);
        //}
        chmod($path, 0777);
        unlink($fileTXT);
        /*
        CopixFile::createDir ($fileTXT);
        chmod($path   , 0333);
        chmod($fileTXT, 0333);
        try {
            _class ('all2txt|all2txt')->pdf2txt ($filePDF, $fileTXT);
            $this->assertTrue (false);
        } catch (CopixException $e) {
            $this->assertTrue (true);
        }
        chmod($path, 0777);
        CopixFile::removeDir ($fileTXT);
        */
        
        CopixFile::removeDir ($path);
    }
}

?>