<?php

class reader{
    
    public $title;
    public $link;
    public $description;
    public $feeds=array();
    public $image;
    public $imagetitle;
    public $imagelink;
    
    public function read($url){
            $content = _ioClass('generictools|Socket')->getHttpContent($url);
            try{
                if(preg_match('/<body(.*?)>/',$content)){
                    throw new CopixException("$url is not XML Format");
                }
                $xml = @simplexml_load_string($content);
                $this->_setProperties($xml);
            }catch(CopixException $e){
                return false;
            }
            return $this;
    }
    
    private function _setProperties($xml){
        if(isset($xml->channel->link))
            $this->link = $xml->channel->link;
        if(isset($xml->channel->title))
            $this->title = $xml->channel->title;
        if(isset($xml->channel->description))
            $this->description = $xml->channel->description;
        if(isset($xml->channel->image->url))
            $this->image = $xml->channel->image->url;
        if(isset($xml->channel->image->title))
            $this->imagetitle = $xml->channel->image->title;
        if(isset($xml->channel->image->link))
            $this->imagelink = $xml->channel->image->link;
        
        if(count($xml->channel->item)>0){
            $this->feeds = $xml->channel->item;            
        }
        else{
            $this->feeds = $xml->item;
        }
    }
    
}

?>