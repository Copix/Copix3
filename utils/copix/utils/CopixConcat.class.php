<?php
/**
* @package copix
* @subpackage utils
* @author Steevan BARBOYON
* @copyright CopixTeam
* @link http://copix.org
* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Permet de concaténer plusieurs fichiers
 * L'idée de base étant de concaténer les JS et CSS pour n'avoir qu'une seule requête HTTP, au lieu d'une par fichier
 *
 * @package copix
 * @subpackage utils
 */
class CopixConcat {
	
	private static $_fileUrl;
	
	/**
	 * Retourne l'adresse qui retournera tous les fichiers concaténés
	 *
	 * @param array $pFiles Chemin des fichiers à concaténer
	 * @param string $pMimeType Type mime, null pour une recherche automatique sur le 1er fichier
	 * @param string $pSeparator Séparateur entre 2 fichiers
	 * @param int $pCompress Indique si on veut compresser le fichier, ne fonctionne que pour les JS
	 * @return string
	 */
	public static function getURL ($pFiles, $pMimeType = null, $pSeparator = "\n\n", $pCompress = false) {
		$cachePath = COPIX_CACHE_PATH . 'copixconcat/';
		$cacheConcatFile = $cachePath . 'concat.php';
		$enableProxyCache = CopixConfig::instance ()->copixhtmlheader_concatEnableProxyCache;
		$resourceBasePath = CopixURL::getRequestedBaseUrl ();
		
		// Si on ne précise pas le type MIME, on prend celui du premier fichier
		if ($pMimeType == null) {
			if (count ($pFiles) > 0) {
				$temp = $pFiles;
				$pMimeType = CopixMIMETypes::getFromFileName (array_shift ($temp));
			}
		}
		// Appel du script avec le serveur associé au type de ressource
		if ($pMimeType == 'text/css') {
			$resourceBasePath = CopixResource::getResourceServer('.css');
		} elseif ($pMimeType == 'application/x-javascript') {
			$resourceBasePath = CopixResource::getResourceServer('.js');
		}
		$resourceBasePath .= CopixUrl::getRequestedScriptPath ();
		
		$cacheFiles = array ();
		foreach ($pFiles as $file) {
			$fileWithoutVersion = CopixResource::getPathWithoutVersion($file);
			$cacheFiles[] = $fileWithoutVersion . '-' . filemtime ($fileWithoutVersion);
		}
		
		// recherche du cache de toutes les concaténations
		if (file_exists ($cacheConcatFile)) {
			require ($cacheConcatFile);
		} else {
			$_cache = array ();
		}

		$idCache = md5 (CopixUrl::getRequestedProtocol().CopixUrl::getRequestedDomain ().implode (',', $cacheFiles));
		if (array_key_exists ($idCache, $_cache) && file_exists ($cachePath. $idCache . '.php') && file_exists ($cachePath. $idCache . '.headers.php')){
			if ($enableProxyCache) {
				return $resourceBasePath . 'concat.' . $idCache . '.php';
			} else {
				return $resourceBasePath . 'concat.php?id=' . $idCache;
			}
		}
		
		$_cache[$idCache] = $cacheFiles;
		$cacheFile = $cachePath . $idCache . '.php';
		$cacheHeadersFile = $cachePath . $idCache . '.headers.php';
		
		// cache des fichiers concaténés
		$contentCacheFile = '';
		foreach ($pFiles as $file) {
			$file = CopixResource::getPathWithoutVersion($file);
			$contentFile = file_get_contents ($file).$pSeparator;
			$contentFile = self::_removeBOM ($contentFile);
			
			//Réécriture des chemin CSS
			if ($pMimeType == 'text/css') {
				self::$_fileUrl = $file;
				$contentFile = preg_replace_callback('/url\W*\(([^)]*)\)/i', array ('CopixConcat', 'rewriteUrl'), $contentFile);
			}
			$contentCacheFile .= $contentFile;
		}
		
		//Compression du fichier de type js
		if ($pMimeType == 'application/x-javascript' && $pCompress) {
			Copix::RequireOnce (COPIX_JSXS_PATH . 'PregFile.php');
			Copix::RequireOnce (COPIX_JSXS_PATH . 'Jsxs.php');
			$jsxs = new Jsxs();
			$jsxs->setRegexDirectory (COPIX_JSXS_PATH . 'preg');
			$jsxs->setCompatibility ($pCompress & CopixConfig::COMPRESS_JS_COMPATIBILITY);
			$jsxs->setReduce ($pCompress & CopixConfig::COMPRESS_JS_REDUCE);
			$jsxs->setShrink ($pCompress & CopixConfig::COMPRESS_JS_SHRINK);
			$jsxs->setConcatString ($pCompress & CopixConfig::COMPRESS_JS_CONCATSTRING);
			$contentCacheFile = $jsxs->exec ($contentCacheFile);
		}
		
		// Ecriture du fichier
		file_put_contents ($cacheFile, $contentCacheFile);
		$cacheMTime = filemtime ($cacheFile);

		// headers à ajouter
		$headers = array ();
		// Au cas où ce header ait déjà été ajouté par Apache
		$headers[] = "header('Unset: Cache-Control');";
		if ($enableProxyCache) {
			$headers[] = "header('Cache-Control: public');";
		} else {
			$headers[] = "header('Cache-Control: must-revalidate');";
		}
		$headers[] = "header('Date: " . gmdate ('r') . "');";
		$headers[] = "header('Last-Modified: " . gmdate ('r', $cacheMTime) . "');";
		$headers[] = "header('Expires: " . gmdate ('r', $cacheMTime + (3600 * 24 * 31 * 12)) . "');";
		$headers[] = "header('Content-Length: " . filesize ($cacheFile) . "');";
		// si on n'ajoute pas content-type text/css par exemple, les css seront mal lus
		$headers[] = "header('Content-Type: " . $pMimeType . "');";
		$headers[] = 'if (isset ($_SERVER["HTTP_IF_MODIFIED_SINCE"])) {';
		$headers[] = '	$time = strtotime ($_SERVER["HTTP_IF_MODIFIED_SINCE"]);';
		$headers[] = '	if ($time !== false && ' . $cacheMTime . ' <= $time) {';
		$headers[] = '		header("304 Not Modified", null, 304);';
		$headers[] = '		exit ();';
		$headers[] = '	}';
		$headers[] = '}';
		
		// Cache des en-têtes
		CopixFile::write ($cacheHeadersFile, '<?php' . "\n" . implode ("\n", $headers) . "\n" . '?>');
		
		// Cache des concaténations
		$php = new CopixPHPGenerator ();
		$content = $php->getVariableDeclaration ('$_cache', $_cache);
		CopixFile::write ($cacheConcatFile, $php->getPHPTags ($content));
		
		if ($enableProxyCache) {
			return $resourceBasePath . 'concat.' . $idCache . '.php';
		} else {
			return $resourceBasePath . 'concat.php?id=' . $idCache;
		}
	}
	
	
	public static function rewriteUrl ($pArUrl) {
		if (isset ($pArUrl[1])) {
			$pArUrl[1] = trim($pArUrl[1], '\'"');
			$parseURL = parse_url ($pArUrl[1]);
			
			if (strlen ($pArUrl[1]) > 0 && $pArUrl[1][0] != '/' && !isset ($parseURL['scheme'])) {
				if ($pos = strpos(self::$_fileUrl, COPIX_CACHE_PATH.'copixhtmlheader/css/') !== false) {
					self::$_fileUrl = 'resource.php/' . substr (self::$_fileUrl, $pos+strlen (COPIX_CACHE_PATH.'copixhtmlheader/css/')-1);
				}
				$pArUrl[1] = dirname (self::$_fileUrl).'/'.$pArUrl[1];
			}
		}
		return 'url('.$pArUrl[1].')';
	}
	/**
	 * Supprime les caractères Byte-Order-Marks qui rendent les fichiers illisibles
	 *
	 */
	private static function _removeBOM ($str) {
		if (substr ($str, 0, 3) == "\xef\xbb\xbf") {
			return substr ($str, 3);
		}
		return $str;
	}
	
}