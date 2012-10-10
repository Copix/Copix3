<?php
class CopixTest_Html_Test extends CopixTest {

	function testParse (){
		foreach (array ('http://www.copix.org', 'http://www.alptis.org', 
						'http://www.google.fr', 'http://www.msn.fr', 
						'http://www.yahoo.fr', 'http://www.wow-europe.fr', 
						'http://forum.copix.org', 'http://www.gameknot.com', 'http://www.youtube.com', 'http://www.microsoft.com', 'http://www.php.net', 'http://www.w3.org')
			as $name){
			echo $name, '<br />';
			$this->assertTrue (simplexml_load_string (utf8_encode ($this->urlCheck ($name))) !== false);
		}
	}
	
	protected function urlCheck ($name){
		$copixHttpClientRequest = new CopixHttpClientRequest ($name);
		$copixHttpClient = new CopixHttpClient ();
		$result = $copixHttpClient->launch ($copixHttpClientRequest);

		//$result = tidy_repair_string ($result[0]->getBody (), array ('output-xml'=>true));
		//$tidy = tidy_parse_string ($result[0]->getBody (), array ('output-xhtml'=>true));
		$tidy = tidy_parse_string ($result[0]->getBody (), array ('output-xhtml'=>true));
		$tidy->cleanRepair ();

		$bodyTidy = $tidy->body ();
		$result = _toString ($bodyTidy);
		$result = html_entity_decode ($result);
		
		return str_replace ('&', '&amp;', $result);
	}

}
?>