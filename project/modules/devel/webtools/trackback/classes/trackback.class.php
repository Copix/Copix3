<?php
/**
 * Trackback class
 * Based on http://silent-strength.com/?articles/php/trackbacks
 * @author Patrice Ferlet - <metal3d@copix.org>
 * @package webtools
 * @subpackage trackback
 * @copyright Copix Team (c) 2007-2008
 */

/**
 * @package webtools
 * @subpackage trackback
 */
class TrackBack {
	
	public function send(){
		$donnees =  'title='.urlencode(_request('title'))
		.'&url='.urlencode(_request('url'))
		.'&excerpt='.urlencode(_request('excerpt'))
		.'&blog_name='.urlencode(_request('blogname'));
		
		preg_match('#http(s)?://(.*?)/(.*)#',_request('target'),$matches);
		
		$host = $matches[2];
		$script = $matches[3];
		
		$toReturn = new stdClass();
		# Ouverture du socket
		$sock = fsockopen($host, 80, $errno, $errstr);


		fputs($sock, "POST /".$script." HTTP/1.0\r\n");
		fputs($sock, "Host:".CopixUrl::getRequestedDomain()."\r\n");
		fputs($sock, "Content-type: application/x-www-form-urlencoded\r\n");  
		fputs($sock, "Content-length: " . strlen($donnees) . "\r\n"); 
		fputs($sock, "Accept: */*\r\n"); 
		
		fputs($sock, "\r\n"); 
		fputs($sock, "$donnees\r\n"); 
		fputs($sock, "\r\n"); 
		$headers="";
		while ($str = @trim(@fgets($sock, 4096)))
			$headers .= "$str\n";

		$body="";
		while (!@feof($sock))
			$body .= @fgets($sock, 4096);


		$toReturn->error = false;
		if(ereg('<error>1</error>',$body)) {
			$toReturn->error = true;
			$toReturn->message= _i18n('trackback|trackback.send.non.ok')."<br />";
			preg_match('#<message>(.*)</message>#is',$body,$text);
			$toReturn->message.= nl2br($text[1]);
		}
		else {
			$toReturn->message = _i18n("trackback|trackback.send.ok");
		}
		fclose($sock);
		return $toReturn;
	}


	public function recieve(){
		$toReturn = new stdClass();
		
		$post = array(
		'blog_name'    =>    _i18n('trackback|trackback.err.blog_name'),
		'url'        =>    _i18n('trackback|trackback.err.url'),
		'title'        =>    _i18n('trackback|trackback.err.title'),
		'excerpt'    =>    _i18n('trackback|trackback.err.excerpt')
         );

		$erreurs = array();
		$toReturn->error=false;
		$toReturn->errors = array();
		foreach($post as $nom => $valeur) {
			if(!_request($nom,false)){
				$toReturn->errors[] = $valeur;
				$toReturn->error=true;
			}
		}
		$toReturn->message= '<?xml version="1.0" encoding="iso-8859-1"?>'."\n";
		if(count($toReturn->errors) > 0) {
			$toReturn->message.="<response>
   <error>1</error>
   <message>".implode("\n\t\t",$toReturn->errors)."</message>
</response>
";
		} else {			
			self::save();			
			$toReturn->message.=<<<EOF
<response>
   <error>0</error>
</response>
EOF;
		}
		
		return $toReturn;
	}

	private function save(){
		$rec = _record('trackbacks');
		$rec->target_tb = _request('id');
		$rec->blogname_tb=_request('blog_name');
		$rec->title_tb=_request('title');
		$rec->excerpt_tb=_request('excerpt');
		$rec->url_tb=_request('url');
		$rec->date_tb = date('YmdHis');
		$rec->valid_tb=0;
		$rec->spam_tb=-1; //not set
		_ioDAO('trackbacks')->insert($rec);
		
	}
	
}
?>