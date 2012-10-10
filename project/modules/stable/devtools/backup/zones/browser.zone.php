<?php
/**
 * Informations sur un backup
 */
class ZoneBrowser extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn Contenu à retourner
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$tpl = new CopixTPL ();
		
		$files = array ();
		$dirs = array ();
		$path = CopixFile::getRealPath ($this->getParam ('path', COPIX_VAR_PATH));
		foreach (CopixFile::glob ($path . '*') as $file) {
			if (is_dir ($file)) {
				$dirs[] = $file;
			} else {
				$files[] = $file;
			}
		}
		$tpl->assign ('dirs', $dirs);
		$tpl->assign ('files', $files);
		$tpl->assign ('pathParts', explode (DIRECTORY_SEPARATOR, substr ($path, 0, -1)));
		$tpl->assign ('path', $path);
		
		$pToReturn = $tpl->fetch ('backup|backups/browse.zone.php');
		
		$urlRequest = _url ('backup|browser|');
		$srcDir = _resource ('backup|img/folder.png');
		$srcFile = _resource ('backup|img/file.png');
		$srcDelete = _resource ('img/tools/delete.png');
		$js = <<<JS
function loadPath (pPath) {
	Copix.setLoadingHTML ($ ('backupBrowserFiles'));
	new Request.HTML({
		url : '$urlRequest',
		update : 'backupBrowser'
	}).post({'path': pPath});
}

function cancelFiles () {
	Copix.get_copixwindow ('backupBrowser').close ();
}

function addFiles () {
	var elements = document.getElementsByName ('browserFiles');
	for (x = 0; x < elements.length; x++) {
		if (elements[x].checked) {
			addFile (elements[x].value);
		}
	}
	Copix.get_copixwindow ('backupBrowser').close ();
}

function addFile (pPath, pKind) {
	var inputId = 'file_' + Math.floor (Math.random () * 10000);
	var inputHidden = document.createElement ('input');
	inputHidden.type = 'hidden';
	inputHidden.name = 'files[]';
	inputHidden.value = pPath;
	inputHidden.id = inputId;
	$ ('backupProfile').appendChild (inputHidden);

	var myTable = $ ('tableFiles');
	var myTr = document.createElement ('tr');
	myTr.id = 'tr_' + inputId;
	
	var myTdImg = document.createElement ('td');
	if (pKind == 'dir') {
		myTdImg.innerHTML = '<img src="$srcDir" alt="Répertoire" title="Répertoire" />';
	} else if (pKind == 'file') {
		myTdImg.innerHTML = '<img src="$srcFile" alt="Fichier" title="Fichier" />';
	}
	myTr.appendChild (myTdImg);
	
	var myTdCaption = document.createElement ('td');
	myTdCaption.innerHTML = pPath;
	myTr.appendChild (myTdCaption);
	
	var myTdDelete = document.createElement ('td');
	myTdDelete.innerHTML = '<img src="$srcDelete" alt="Supprimer" title="Supprimer" onclick="deleteFile (\'' + inputId + '\')" style="cursor: pointer" />';
	myTr.appendChild (myTdDelete);
	
	myTable.appendChild (myTr);
}

function deleteFile (pId) {
	if (confirm ('Etes-vous sur de ne plus vouloir sauvegarder ce répertoire ou ce fichier ?')) {
		$ (pId).destroy ();
		$ ('tableFiles').deleteRow ($ ('tr_' + pId).rowIndex);
	}
}
JS;
		CopixHTMLHeader::addJSCode ($js, 'backupBrowser');
		
		return true;
	}
}