<?php
/**
 * @package		webtools
 * @subpackage	quicksearch
* @author	Croës Gérald
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package		webtools
 * @subpackage	quicksearch
* Listener en charge des événements relatifs aux publications / suppression de documents.
*/
class ListenerQuickSearch extends CopixListener {
	/**
	* Gestion des contenux qui sont placés en ligne
	* @param CopixEvent $event l'événement et ses paramètres
	* @param CopixEventResposne $eventResponse la réponse que l'on va passer à l'événement
	*/
	public function processVisited ($pEvent, $pEventResponse){
		$this->processContent($pEvent,$pEventResponse);
	}
	
	
	/**
	* Gestion des contenux qui sont placés en ligne
	* @param CopixEvent $event l'événement et ses paramètres
	* @param CopixEventResposne $eventResponse la réponse que l'on va passer à l'événement
	*/
	public function processContent ($pEvent, $pEventResponse){
/*		
ne supporte pas encore les documents 
		if (($content = $pEvent->getParam ('content')) === null){
			$content = $this->_getDocumentContent ($event->getParam ('filename'));
		}
*/
		$url = $pEvent->getParam ('url');

		_service ('quicksearch|quicksearch::addOrUpdateIndex', array ('id'=>$pEvent->getParam ('id'),
			'kind'=>$pEvent->getParam ('kind'),
			'keywords'=>$pEvent->getParam ('keywords'),
			'title'=>$pEvent->getParam ('title'),
			'summary'=>$pEvent->getParam ('summary'),
			'content'=>$pEvent->getParam ('content'),
			'url'=>$pEvent->getParam ('url')));
	}

	/**
	* Gestion des contenus qui sont supprimés
	* @param CopixEvent $event l'événement et ses paramètres
	* @param CopixEventResposne $eventResponse la réponse que l'on va passer à l'événement
	*/
	public function processDeletedContent ($pEvent, $pEventResponse){
		_service ('quicksearch|quicksearch::deleteIndex', 
		   array ('id'=>$pEvent->getParam ('id'),
		   		  'kind'=>$pEvent->getParam ('kind')));
	}

    /**
    * Récupération du contenu du document passé en paramètre.
    * @param string $pFileName le chemin du fichier à analyser.
    private function _getDocumentContent ($pFileName){
       //récupération des informations sur le fichier.
       $filePath  = $pFileName;
       $fileInfos = pathinfo ($filePath);

       if (!isset ($fileInfos['extension'])){
          return null;
       }

      $filePath    = '"'.realpath ($filePath).'"';
      //Détermine les actions à mener en fonction du type de fichier
     
      switch (strtolower ($fileInfos['extension'])){
         case 'doc': 
         $convert = intval (CopixConfig::get('convertDoc')) === 1;
         $commandLine = str_replace('%%__FILENAME__%%', $filePath, CopixConfig::get('commandLine4Doc'));
         $isHtml = CopixConfig::get('docFormatAfterConvert') == 'HTML';
         $outputFileName = null;
         break;

         case 'xls': 
         $convert = intval (CopixConfig::get('convertXls')) === 1; 
         $commandLine = str_replace('%%__FILENAME__%%', $filePath, CopixConfig::get('commandLine4Xls'));
         $isHtml = CopixConfig::get('xlsFormatAfterConvert') == 'HTML';
         $outputFileName = null;
         break;

         case 'pdf': 
         $convert = intval (CopixConfig::get('convertPdf')) === 1;
         $commandLine = str_replace('%%__FILENAME__%%', $filePath, CopixConfig::get('commandLine4Pdf').' '.CopixConfig::get('pathToExePdf').'searchText');
         $isHtml = CopixConfig::get('pdfFormatAfterConvert') == 'HTML';
         $outputFileName = CopixConfig::get('pathToExePdf').'searchText';
         break;

         case 'ppt': 
         $convert = intval (CopixConfig::get('convertPpt')) === 1;
         $commandLine = str_replace('%%__FILENAME__%%', $filePath, CopixConfig::get('commandLine4Ppt'));
         $isHtml = CopixConfig::get('pptFormatAfterConvert') == 'HTML';
         $outputFileName = null;
         break;
         
         default:
         //type inconnu
         return null;
      }

      //Lance les actions à mener.
      $out = '';
      $execResult = exec($commandLine, $out);
      if ($outputFileName !== null){
         //Si sortie dans un fichier, lecture du fichier
         $content = implode (' ', $outputFileName);
      }else{
         //sinon lecture de la sortie standard
         $content = implode (' ', $out);
      }

      if ($isHtml){
         //Si sortie effectuée en HTML
          return html_entity_decode(strip_tags($content));
      }
      return $content;
    }
*/
}
?>