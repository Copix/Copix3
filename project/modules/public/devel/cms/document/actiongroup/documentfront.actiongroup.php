<?php
/**
* @package	 cms
* @subpackage document
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
 * @package	 cms
 * @subpackage document
 * handle the front of the documents
 */
class ActionGroupDocumentFront extends CopixActionGroup {
   /**
   * Téléchargement du fichier
   */
   function doDownload (){
      //do we ask for a document ?
      if (!isset ($this->vars['id_doc'])){
         header("HTTP/1.0 404 Not Found");
         return new CopixActionreturn (CopixactionReturn::NONE);
      }

      //try to get the document
      $dao = & CopixDAOFactory::getInstanceOf ('Document');
      $version = isset ($this->vars['version']) ? $this->vars['version'] : $dao->getLastVersion ($this->vars['id_doc']);
      if (($document = $dao->get ($this->vars['id_doc'], $version)) !== false) {
         $title = str_replace('"' , '_', $document->title_doc);
         $title = str_replace(' ' , '_', $title);
         $title = str_replace('/' , '_', $title);
         $title = str_replace('\\', '_', $title);
         $title = str_replace(':' , '_', $title);
         $title = str_replace('?' , '_', $title);
         $title = str_replace('*' , '_', $title);
         $title = str_replace('>' , '_', $title);
         $title = str_replace('<' , '_', $title);
         $title = str_replace('|' , '_', $title);
         if (($documentPath = $this->_getDocumentPath ($document->id_doc, $document->extension_doc, $version)) === null){
            break;
         }
         return new CopixActionReturn (CopixactionReturn::DOWNLOAD, $documentPath, $title.'.'.$document->extension_doc);
      }
      header("HTTP/1.0 404 Not Found");
      return new CopixActionreturn (CopixactionReturn::NONE);
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
