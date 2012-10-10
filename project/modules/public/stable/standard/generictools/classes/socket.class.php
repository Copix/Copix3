<?php

/**
 * Socket methods
 * 
 */
class Socket {
    private $sock;
    
    public function getHttpContent($url){
       $h = new HTTPRequest($url);
       return $h->DownloadToString();
    }
    
    public function open($host,$port){
        $this->sock = @fsockopen($host, $port);
        if(!$this->sock){
            throw new CopixException("Error while open $host:$port");
        }
        return $this;
    }
    
    public function close(){
        fclose($this->sock);
    }
    
    public function write($req){
        fwrite($this->$sock, $req);
    }
    
    public function read(){
        $response = "";
        while(is_resource($this->sock) && $this->sock && !feof($this->sock))
            $response .= fread($this->sock, 1024);
        return $response;
    }
}

/**
 * Based on http://fr.php.net/manual/fr/function.fopen.php#58099
 *
 */
class HTTPRequest{
    private $_fp;        // HTTP socket
    private $_url;        // full URL
    private $_host;        // HTTP host
    private $_protocol;    // protocol (HTTP/HTTPS)
    private $_uri;        // request URI
    private $_port;        // port
     
    // scan url
    private function _scan_url()    {
        $req = $this->_url;
         
        $pos = strpos($req, '://');
        $this->_protocol = strtolower(substr($req, 0, $pos));
         
        $req = substr($req, $pos+3);
        $pos = strpos($req, '/');
        if($pos === false)
        $pos = strlen($req);
        $host = substr($req, 0, $pos);
         
        if(strpos($host, ':') !== false){
            list($this->_host, $this->_port) = explode(':', $host);
        }
        else{
            $this->_host = $host;
            $this->_port = ($this->_protocol == 'https') ? 443 : 80;
        }
         
        $this->_uri = substr($req, $pos);
        if($this->_uri == '')
        $this->_uri = '/';
    }
     
    // constructor
    public function HTTPRequest($url){
        if(empty($url)){
            throw new CopixException("Url is empty");
        }
        $this->_url = $url;
        $this->_scan_url();
    }
     
    // download URL to string
    public function DownloadToString(){
        $crlf = "\r\n";
         
        // generate request
        $req = 'GET ' . $this->_uri . ' HTTP/1.0' . $crlf
        .    'Host: ' . $this->_host . $crlf
        .    $crlf;
         
        // fetch
        $this->_fp = @fsockopen(($this->_protocol == 'https' ? 'ssl://' : '') . $this->_host, $this->_port);
        if(!$this->_fp){
            throw new CopixException("Error while trying to open ".$this->_url);
        }
        $response="";
        fwrite($this->_fp, $req);
        while(is_resource($this->_fp) && $this->_fp && !feof($this->_fp))
        $response .= fread($this->_fp, 1024);
        fclose($this->_fp);
         
        // split header and body
        $pos = strpos($response, $crlf . $crlf);
        if($pos === false)
        return($response);
        $header = substr($response, 0, $pos);
        $body = substr($response, $pos + 2 * strlen($crlf));
         
        // parse headers
        $headers = array();
        $lines = explode($crlf, $header);
        foreach($lines as $line)
        if(($pos = strpos($line, ':')) !== false)
        $headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));
         
        // redirection?
        if(isset($headers['location'])){
            $http = new HTTPRequest($headers['location']);
            return($http->DownloadToString($http));
        }
        else{
            return($body);
        }
    }
}



?>