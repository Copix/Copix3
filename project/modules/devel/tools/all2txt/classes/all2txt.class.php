<?php

class all2Txt {
    
    /**
     * Taille minimal des mot dans la recherche du texte d'un document word 
     * lors de l'utilisation de la méthode PHP
     *
     * @var int
     */
    public static $SIZE_WORD_DOC = 10;
    
    /**
     * Nombre de ligne maximal dans la recherche du texte d'un document word 
     * lors de l'utilisation de la méthode PHP
     * A -1 on parcourt tous le fichier
     *
     * @var int
     */
    public static $SIZE_LIGNE_DOC = -1;
    
    
    /**
     * Méthode appelé avant chaque convertion
     * Test l'existance des fichier et si il peuvent être ecrit
     * 
     * @param string $fileSource    Fichier Source
     * @param string $fileDest      Fichier Destination
     */
    private static function _beforeConvert ($fileSource, $fileDest) {
        
        // Test si la source existe
        if (!file_exists ($fileSource)) {
            throw new CopixException(_i18n ('all2txt|all2txt.exception.fileNotFound', $fileSource));
        }
        
        if (!file_exists ($fileDest)) {
            try {
                CopixFile::write ($fileDest, '');
            } catch (Exception $e) {
                throw new CopixException(' Impossible de creer le document \''.$fileDest.'\'');
            }
        }
        if (is_dir ($fileDest)) {
            try {
                CopixFile::removeDir ($fileDest);
            } catch (Exception $e) {
                throw new CopixException(' Impossible de remplacer le document \''.$fileDest.'\'');
            }
        } else {
            @unlink ($fileDest);
        }
        if (file_exists ($fileDest)) {
            throw new CopixException(' Impossible de remplacer le document \''.$fileDest.'\'');
        }
    }
    
    /**
     * Test si un caractère un imprimable
     * 
     * @param string $charData
     * @return boolean
     */
    private static function _isPrintable ($charData) {
        if ((base_convert (bin2hex ($charData), 16, 10) >= 0x20 && base_convert (bin2hex ($charData), 16, 10) <= 0x7E) || 
            (base_convert (bin2hex ($charData), 16, 10) >= 0xA0 && base_convert (bin2hex ($charData), 16, 10) <= 0xFE)) {
                
                return true;
        }
        return false;
    }
    
    /**
     * Convertit un Fichier PDF en chaine. Fonction expérimental
     * 
     * @param string $sourcefile Fichier Source
     * @return string
     */
    private static function _pdf2string ($sourcefile) {
        $content = CopixFile::read($sourcefile);
        
        # Locate all text hidden within the stream and endstream tags
        $searchstart = 'stream';
        $searchend = 'endstream';
        $pdfdocument = '';
    
        $pos = 0;
        $pos2 = 0;
        $startpos = 0;
        
        while ($pos !== false && $pos2 !== false ) {
            // Recherche les mots clef de début et de fin de paragraphe
            $pos = strpos($content, $searchstart, $startpos);
            $pos2 = strpos($content, $searchend, $startpos + 1);
            if( $pos !== false && $pos2 !== false ) {
                # Extrait les données compréssée. Si la donné n'est pas valide on passe à la suivante
                $textsection = substr($content, $pos + strlen($searchstart) + 1, $pos2 - $pos - strlen($searchstart) - 1);
                
                $data = @gzuncompress($textsection);
                
                if( $data !== false ) {
                    # Clean up text via a special function
                    $data = self::_extractPdfText($data);
                    $pdfdocument .= $data;
                }
                
                # Increase our PDF pointer past the section we just read
                $startpos = $pos2 + strlen($searchend) - 1;
            }
        }
    
        return $pdfdocument;
    }       
    
    /**
     * Extrait le texte des données décrompressées du PDF
     * 
     * @param string $postScriptData Données extraites du pdf
     * @return string
     */
    private static function _extractPdfText ($postScriptData) {
        $textStart    = 0;
        $textStartOld = 0;
        $plainText    = '';
        
        // Recherche les caractères en hexadecimal et les convertit en caractères imprimables
        while( (($textStart = strpos($postScriptData, '<', $textStart))  && 
               ($textEnd = strpos($postScriptData, '>', $textStart + 1)))) {
            if ((strpos (substr($postScriptData, $textStartOld, $textStart - $textStartOld - 1), 'Q') !== false)) {
                $plainText .= "\n";
            }
            
            $ligne = substr($postScriptData, $textStart + 1, $textEnd - $textStart - 1);
            
            for ($cut = 0; $cut < strlen ($ligne); $cut = $cut + 2){
                $charac = substr ($ligne, $cut, 2);
                $charFormated = chr(base_convert ($charac, 16, 10));
                
                // Gestion de l'utf8 non faite
                if (self::_isPrintable ($charFormated)) {
                    $plainText .= $charFormated;
                }
            }
            $textStartOld = $textStart;
            $textStart    = $textStart < $textEnd ? $textStart+2 : $textStart + 1;
        }
        $pos = 0;
        while (($textStart + $pos) < strlen ($postScriptData) && ($pos = strpos ($postScriptData, 'Q', $textStart + $pos)) !== false) {
            $plainText    .= "\n";
            $textStart    = $pos + 1;
        }
        
        // Cpnvertie les caractères specials.
        $trans = array(
            '\\'                => '\\\\'
        );
        $plainText = strtr($plainText, $trans);
        
        return stripslashes($plainText);
    }
    
    /**
     * Extrait le texte d'un fichier DOC. Fonction expérimental
     * 
     * @param string $rawData Données brute du fichier
     * @param $repeatSearch Nombre de fois ou l'on doit chercher des blocs de caractères imprimables
     * @return string
     */
    private static function _extractDocText ($rawData, $repeatSearch = null) {
         
        if ($repeatSearch === null) {
            $repeatSearch = self::$SIZE_LIGNE_DOC;
        }
        
        $text = '';
        $posBegin = self::_beginDocText ($rawData);
        $posEnd   = self::_endDocText($rawData, $posBegin + 1); 
        
        while ($posBegin !== false) {
            $text .= substr ($rawData, $posBegin, $posEnd - $posBegin) . "\n";
            
            if ($repeatSearch == 0) {
                break;
            }
            $repeatSearch --;
            $posBegin = self::_beginDocText ($rawData, $posEnd + 1);
            $posEnd   = self::_endDocText($rawData, $posBegin + 1); 
        }
        return $text;
    }
    
    /**
     * Trouve le début du texte du fichier DOC (non fonctionnel)
     */
    private static function _beginDocText ($rawData, $startSearch = 0) {
        
        $size = strlen ($rawData);
        $suite = 0;
        for ($i = $startSearch; $i < $size; $i++) {
            if ((int)self::_isPrintable ($rawData[$i])) {
                $suite ++;
            } else  {
                $suite = 0;
            }
            
            if ($suite == self::$SIZE_WORD_DOC) {
                return $i - self::$SIZE_WORD_DOC + 1;
            }
        }
        return false;
    }
    
    /**
     * Trouve la fin du texte du fichier DOC (non fonctionnel)
     */
    private static function _endDocText ($rawData, $startSearch) {
        
        $size = strlen ($rawData);
        for ($i = $startSearch + self::$SIZE_WORD_DOC - 1; $i < $size; $i++) {
            if (!self::_isPrintable ($rawData[$i])) {
                return $i;
            }
        }
        return $size;
    }
    
    
    /**
     * Convertit un fichier PDF vers un fichier Texte
     * 
     * @param string    $fileSource         Fichier Source
     * @param string    $fileDest           Fichier Destination
     * @param boolean   $phpMethodeForce    Si se paramètre est à true force l'utilisation des méthodes PHP
     */
    public function pdf2txt ($filePDF, $fileTXT, $phpMethodeForce = false) {
        
        self::_beforeConvert($filePDF, $fileTXT);
        
        if ($phpMethodeForce || CopixConfig::get ('all2txt|PDFUsePHP')) {
            
            $text = self::_pdf2string ($filePDF);
            CopixFile::write($fileTXT ,$text);
            
        } else {
           
            $pathPDF = '';
            $pathTXT = '';
            
            if (($pos = strrpos($filePDF, '/')) !== false) {
                $pathPDF = substr ($filePDF, 0, $pos + 1);
                $filePDF = substr ($filePDF, $pos + 1);
            }
            
            if (($pos = strrpos($fileTXT, '/')) !== false) {
                $pathTXT = substr ($fileTXT, 0, $pos + 1);
                $fileTXT = substr ($fileTXT, $pos + 1);
            }
            
            $commande = CopixConfig::get ('all2txt|commandPDF2TXT');
            $commande = str_replace ('{pathpdf}', $pathPDF, $commande);
            $commande = str_replace ('{filepdf}', $filePDF, $commande);
            $commande = str_replace ('{pathtxt}', $pathTXT, $commande);
            $commande = str_replace ('{filetxt}', $fileTXT, $commande);
            
            shell_exec ($commande);
            
            for ($i = 0; !file_exists ($pathTXT.$fileTXT); $i++) {
                // Stop pour 1 dixieme de seconde
                usleep (100000);
                // Si cela fait 30 seconde revoi une exception
                if ($i == 300) {
                    throw new CopixException('Génération de document texte impossible pour document PDF');
                }
            }
            
        }
    }
    
    /**
     * Convertit un fichier DOC vers un fichier Texte
     * 
     * @param string    $fileSource         Fichier Source
     * @param string    $fileDest           Fichier Destination
     * @param boolean   $phpMethodeForce    Si se paramètre est à true force l'utilisation des méthodes PHP
     */
    public function doc2txt ($fileDOC, $fileTXT, $phpMethodeForce = false) {
        
        self::_beforeConvert($fileDOC, $fileTXT);
        
        if ($phpMethodeForce || CopixConfig::get ('all2txt|DOCUsePHP')) {
            
            $text = self::_extractDocText (CopixFile::read ($fileDOC));
            CopixFile::write($fileTXT ,$text);
            
        } else {
           
            $pathDOC = '';
            $pathTXT = '';
            
            if (($pos = strrpos($fileDOC, '/')) !== false) {
                $pathDOC = substr ($fileDOC, 0, $pos + 1);
                $fileDOC = substr ($fileDOC, $pos + 1);
            }
            
            if (($pos = strrpos($fileTXT, '/')) !== false) {
                $pathTXT = substr ($fileTXT, 0, $pos + 1);
                $fileTXT = substr ($fileTXT, $pos + 1);
            }
            
            $commande = CopixConfig::get ('all2txt|commandDOC2TXT');
            $commande = str_replace ('{pathdoc}', $pathDOC, $commande);
            $commande = str_replace ('{filedoc}', $fileDOC, $commande);
            $commande = str_replace ('{pathtxt}', $pathTXT, $commande);
            $commande = str_replace ('{filetxt}', $fileTXT, $commande);
            
            shell_exec ($commande);
            
            for ($i = 0; !file_exists ($pathTXT.$fileTXT); $i++) {
                // Stop pour 1 dixieme de seconde
                usleep (100000);
                // Si cela fait 30 seconde revoi une exception
                if ($i == 300) {
                    throw new CopixException('Génération de document texte impossible pour document DOC');
                }
            }
            
        }
    }
    
    /**
     * Convertit un fichier HTML vers un fichier Texte
     * 
     * @param string    $fileSource         Fichier Source
     * @param string    $fileDest           Fichier Destination
     */
    public function html2txt ($fileHTML, $fileTXT) {
        
        self::_beforeConvert($fileHTML, $fileTXT);

		$content = CopixFile::read ($fileHTML);
		// suppression du contenu des tags javascripts
		$text = preg_replace ('@<script[^>]*?>.*?</script>@si', null, $content);
		// suppression du contenu des tags noscript
		$text = preg_replace ('@<noscript>.*?</noscript>@si', null, $text);
        $text = @strip_tags ($text);
        CopixFile::write ($fileTXT ,$text);
    }
}