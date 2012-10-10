<?php
/**
 * @package     standard
 * @subpackage  test_soap
 * @author		Croës Gérald, Julien Alexandre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @ignore
 */
_classinclude ('test|test');

/**
 * Classe de test pour les services SOAP
 * @package standard
 * @subpackage test_soap
 */
class Soap extends CopixAbstractTestTest {
	/**
	 * Validation du test
	 */
	protected function _validate () {
		$test = _dao('testsoap')->get(CopixRequest::get('id'));
		if (self::checkTimeResponse($test->address_soap, $test->proxy)) {
			$soapClient = new CopixSoapClient ($test->address_soap);
			$soapClient->setProxy($test->proxy);
			$soap = $soapClient->getSOAP();
			$soapCheck = self::checkSOAP ($soap);
			self::checkFunctions ($soap, CopixRequest::get('id'));
		} else {
			$this->_addResult(false, 'Erreur le service ne répond pas');
		}
	}

	/**
	 * Vérification de la disponibilité du serveur
	 * 
	 * @param  string  $wsdl  adresse du WebService
	 * @return SoapClient connexion SOAP
	 *  un serveur ou soap ou retourne faux si ce n'est pas le cas 
	 */
	public function checkSOAP ($wsdl) {
		$soapCheck = CopixSoapClient::verify ($wsdl);
		if ($soapCheck == true) {
			$this->_addResult(true);
		} else {
			$this->_addResult(false, 'Erreur le service renvoie une erreur');
		}
		return $soapCheck;
	}

	/**
	 * Permet de vérifier si le serveur répond
	 * 
	 * @param string $wsdl adresse du Webservice
	 * @return bool 
	 */
	public function checkTimeResponse ($wsdl, $proxy) {
		$httpRequest = new CopixHTTPClientRequest($wsdl);
		$httpRequest->setTimeout(5);
		$copixHttpClient = new CopixHttpClient();
		$request = $copixHttpClient->launch ($httpRequest);
		if ($request) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Vérification des méthodes du WebService
	 * 
	 * @param SoapClient $soapCheck Connexion SOAP
	 * @param int Identifiant du test
	 */
	public function checkFunctions ($soapCheck, $id) {
		$parameters = _daoSP()->addCondition ('id_test','=',$id);
		$record = _dao ('testsoapfunctions')->findBy ($parameters);
		foreach ($soapCheck->__getFunctions () as $function) {
			foreach ($record as $value) {
				if ($function == $value->name_function) {
					$this->_addResult (true);
				}
			}
		}
	}
}