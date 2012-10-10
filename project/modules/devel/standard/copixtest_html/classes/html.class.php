<?php
 _classinclude ('copixtest|copixtesttest');
//include ('C:\Documents and Settings\AlexandreJ\workspace\Copix trunk\project\modules\stable\standard\copixtest\classes\copixtesttest.class.php');

/**
 * @package standard
 * @subpackage copixtest_html
 * @author		Croës Gérald, Julien Alexandre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

class Html extends CopixAbstractTestTest {
	
	/**
	 * Lance le test HTML et remplie un XML contenant les résultats des tests et les éventuelles erreurs
	 * @param void
	 * @return void
	 */
	protected function _validate () {
			$test = _dao ('copixtesthtml')->get (_request('id'));
			$copixHttpClientRequest = new CopixHTTPClientRequest ($test->url);
			$copixHttpClientRequest->setCookie($test->param_cookies);
			$copixHttpClientRequest->setProxy ($test->proxy);
			$copixHttpClientRequest->setFollowRedirect (true);
			if ($test->session == null) {
				$copixHttpClient = new CopixHttpClient ();
				$result = $copixHttpClient->launch ($copixHttpClientRequest);
				$result = $result[0];
			} else {
				$session = _dao('copixtesthtmlsession')->get ($test->session);
				$login = _dao('copixtesthtml')->get ($session->login_session);
				$logout = _dao('copixtesthtml')->get ($session->logout_session);
				
				$request_login = new CopixHttpClientRequest ($login->url);
				$request_login->setCookie($login->param_cookies);
				$request_login->setFile($login->param_file);
				if ($login->param_post) {
					$request_login->setPost($login->param_post);
				}
				$request_login->setProxy($login->proxy);
				$request_login->setFollowRedirect(true);
				
				$request_logout = new CopixHttpClientRequest ($logout->url);
				$request_logout->setCookie($logout->param_cookies);
				$request_logout->setFile($logout->param_file);
				if ($logout->param_post) {
					$request_logout->setPost($logout->param_post);
				}
				$request_logout->setProxy($logout->proxy);
				$request_logout->setFollowRedirect(true);
				
				CopixClassesFactory::fileInclude('copixtest_html|session');
				$copixSession = new Session($request_login, $request_logout);
				$copixSession->addHttpClientRequest($copixHttpClientRequest);
				$copixHttpClient = $copixSession->getHttpClientRequest();
				$result = $copixHttpClient->launch();
				$result = $result[1];
			}
			
			self::verifyTiming ($result);
			$header = _ioDAO('copixtesthtmlheader')->findBy (_daoSp()->addCondition('id_test', '=', $test->id_test));
			$body = _ioDAO('copixtesthtmlbody')->findBy (_daoSp()->addCondition('id_test', '=', $test->id_test));
			self::verifyHeader ($result->getHeader (), $header);
			self::verifyBody ($result->getBody (), $body, split('\n', $result->getHeader ()));
	}
	
	/**
	 * @param $result: CopixHttpClientResult résultat de la requête HTTP
	 * @return unknow
	 * Vérifie si la requête HTTP a abouti à une réponse et renvoie au fichier XML 
	 * le temps de réponse de la page
	 */
	public function verifyTiming ($result) {
		if ($result->getStartTransferTime () >= 0 && $result->getTotalTime () >= 0) {
			$this->_addResult (true, $result->getConnectTime ().'|'.$result->getStartTransferTime ().'|'.$result->getTotalTime ()); 
		} else {
			$this->_addResult (false, 'Connection à la page impossible');
		}
	}
	
	/**
	 * Vérifie si le HEADER correspond à ce que l'on attend
	 * 
	 * @param $headerRequest: String contenant le Header du document
	 * @param $header: array contient la liste des balises du Header à vérifier
	 * @return unknown
	 */
	public function verifyHeader ($headerRequest, $header) {
		 foreach ($header as $value) {
			if (strpos($headerRequest, $value->value_mark) !== false) {
				$this->_addResult (true);
			}
			else {
				$this->_addResult(false, 'Echec HEADER : '.$value->value_mark);
			}
		}
	}
	
	/**
	 * Vérifie si les balises du body sont conformes à ce que l'on veut
	 * @param $bodyRequest: String contenant le corps du document HTML
	 * @param $bodytest: Array contenant les balises à vérifier
	 * @return unknown
	 */
	public function verifyBody ($bodyRequest, $bodytest, $header) {
		CopixClassesFactory::fileInclude('copixtest_html|encoding');
		$tidy = tidy_parse_string ($bodyRequest, array ('output-xhtml'=>true));
		$tidy->cleanRepair ();
		$bodyTidy = $tidy->body ();
		$result = _toString ($bodyTidy->value);
		if (str_replace ('&#233', '', $result) == $result) {
			$result = html_entity_decode ($result); 
		} else {
			$result = htmlspecialchars_decode ($result);
		}
		// $result = html_entity_decode ($result);
		$result = str_replace ('&', '&amp;', $result);
		
		$xml = simplexml_load_string (Encoding::checkEncoding($result, $header));

		$simpleXmlElement = new stdClass ();

		foreach ($bodytest as $value) {
			if ($value->checkType !== 'notest') {
				
			$simpleXmlElement = $xml->xpath ($value->path_tag);
			if (empty($simpleXmlElement)) {
				if ($value->validType == 'exclude') {
					$this->_addResult (true);
					break;
				} else if ($value->validType == 'exist') {
					$this->_addResult (false, 'Le chemin Xpath du test n\'existe pas');
					break;
				}
			}
			$exist = false;
				foreach ($simpleXmlElement as $xmlObject) {
					if ($xmlObject->getName() === $value->name_tag) {
						if ($exist !== true) {
							switch ($value->checkType) {
								case 'notest' : $exist = true;
												break;
								case 'moderate' : $exist = self::moderateBodyValidation ($xmlObject, $value);
										 		  break;
								case 'simple' : $exist = self::simpleBodyValidation ($xmlObject, $value);
												break;
								case 'absolute' : $exist = self::absoluteBodyValidation ($xmlObject, $value);
												  break;
							}
						}
					}
					if ($value->validType === 'exclude') {
						var_dump('erreur');
					}
				}
				// Gestion des erreurs
				if ($exist !== true) {
					self::sendError($exist, $xmlObject, $value);
				}
		}
		}
	}
	
	/**
	 * @param $exist: int or bool contenant le résultat d'un test ou un code d'erreur
	 * @param $xmlObject: Object de type SimpleXMLElement
	 * @param $value: Object de type _record contenant les informations d'une balise
	 * @return unknown
	 * Gestion des erreurs
	 */
	 public function sendError ($exist, $xmlObject, $value) {
		CopixClassesFactory::fileInclude('copixtest_html|tag');
		$tag = new Tag ();
		switch ($exist) {
			
			case 3 : $this->_addResult(false, 'Echec BODY sur '.$value->path_tag.': problème de contenu '.'(id:'.$value->id_tag.') '.htmlspecialchars($value->contains));
					break;
		
			case 2 : $this->_addResult(false, 'Echec BODY sur '.$value->path_tag.': problèmes d\'attributs '.'(id:'.$value->id_tag.') '.htmlspecialchars($tag->getAttributes($xmlObject->attributes())));
					 break;
					 
			case 1 : $this->_addResult (false, 'Echec BODY sur '.$value->path_tag.': la balise recherchée n\'existe pas !');
					 break;
					 
			default : $this->_addResult(false, 'Echec BODY sur '.$value->path_tag.' : le tag &lt;'.$value->name_tag.'&gt; n\'est pas conforme');
					break;
		}
	}
	/**
	 * @param $xmlObject: Objet de type SimpleXMLElement
	 * @param $value: Objet de type _record contenant les informations d'une balise
	 * @return int or bool
	 * Vérifie l'existance des balises et les attributs
	 */
	public function moderateBodyValidation ($xmlObject, $value) {
		CopixClassesFactory::fileInclude ('copixtest_html|tag');
		$tag = new Tag ();

		if ($xmlObject->getName() === $value->name_tag &&
		 $tag->getAttributes($xmlObject->attributes()) === $value->attributes_tag) {
			$this->_addResult (true);
			return true;
		} elseif ($xmlObject->getName() === $value->name_tag &&
		 		  $tag->getAttributes($xmlObject->attributes()) == null && 
				  $value->attributes_tag == null) {
			$this->_addResult (true);
			return true;
		} else {
			return 2;
		}
	}
	
	/**
	 * Vérifie l'existance de la balise
	 * @param SimpleXmlElement $xmlObject : Objet XML
	 * @param CopixRecord _record : Information d'une balise
	 * @return int or bool
	 */
	public function simpleBodyValidation ($xmlObject, $value) {
		CopixClassesFactory::fileInclude ('copixtest_html|tag');
		$tag = new Tag ();
		if ($xmlObject->getName () === $value->name_tag) {
			$this->_addResult (true);
			return true;
		}  else {
			return 1;
		}
		
	}
	
	/**
	 * Vérifie l'existance de la balise, les attributs et le contenu
	 * @param SimpleXmlElement $xmlObject : Object XML
	 * @param CopixRecord _record : Information d'une balise
	 * @return int or bool
	 */
	public function absoluteBodyValidation ($xmlObject, $value) {
		CopixClassesFactory::fileInclude ('copixtest_html|tag');
		$tag = new Tag ();
		
		/* Permet de vérifier le contenu d'une balise */
		//$containsCheck = preg_match('['.(string)$xmlObject.']', $value->contains) !== 0;
		if (str_replace ((string)$xmlObject, '',$value->contains) == (string)$xmlObject) {
			$containsCheck = false;
		} else {
			$containsCheck = true;
		}
		
		if ($xmlObject->getName() === $value->name_tag &&
		$tag->getAttributes($xmlObject->attributes()) === $value->attributes_tag &&
		$containsCheck) {
			$this->_addResult (true);
			return true;
		} elseif ($xmlObject->getName() === $value->name_tag &&
		 		  $tag->getAttributes($xmlObject->attributes()) == null &&
		  		  $value->attributes_tag == null &&
		  		  $containsCheck === false) {
		  		  $this->_addResult (true);
		  		  	return true;
		  }	elseif ($xmlObject->getName() === $value->name_tag &&
				$tag->getAttributes($xmlObject->attributes()) === $value->attributes_tag &&
		  		  $containsCheck === false) {
		  		  	$this->_addResult (true);
		  		  	return true;
		  } elseif ($xmlObject->getName() === $value->name_tag &&
				  $tag->getAttributes($xmlObject->attributes()) == null &&
		  		  $value->attributes_tag == null &&
		  		  $containsCheck) {
		  		  	$this->_addResult (true);
		  		  	return true;
		  } elseif ($xmlObject->getName () !== $value->name_tag) {
		  			return 1;
		  } elseif ($xmlObject->getName () === $value->name_tag
		  			&& $tag->getAttributes($xmlObject->attributes ()) !== $value->attributes_tag) {
		  			return 2;
		  } else {
				return 3;
			}
	}
}
?>