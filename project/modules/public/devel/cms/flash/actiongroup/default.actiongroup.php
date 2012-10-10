<?php

class ActionGroupDefault extends CopixActionGroup {
   /**
   * Téléchargement du fichier
   */
   function get (){
      echo file_get_contents (COPIX_VAR_PATH.'data/flash/'.CopixRequest::get ('id').'.swf');
      exit;
   }

   /**
   * Récupère le fichier du document d'identifiant donné (toutes les versions
   *  ne disposent pas forcéement d'un fichier, on va donc rechercher
   *  le premier fichier existant en partant de la version la plus récente).
   * @param string $pIdDoc l'identifiant du document recherché
   * @param string $pExtension l'extension du document recherché
   * @param string $pFromVersion la version de laquelle on part pour rechercher le fichier du document
   * @return string le chemin du fichier. null si non trouvé.
   */
   function _getDocumentPath ($pIdDoc, $pExtension, $pFromVersion){
      for ($version = $pFromVersion; $version >= 0; $version--){
         $fileName = CopixConfig::get ('document|path').$pIdDoc.'_v'.$version.'.'.$pExtension;
         if (is_readable ($fileName)){
            return $fileName;
         }
      }
      return null;
   }
}
?>
