<?php
class FeedReader {
    private $_url;
    private $_title;
    private $_description;
    private $_link;
    private $_pubDate;
    private $_lastBuildDate;
    private $_image;
    private $_language;
    private $_items;
    private $_iterator;
    
    public function  __construct($pUrl) {
        $this->_url = $pUrl;
        $this->_items = array ();
        $this->read ();
    }
    
    private function read () {
        try {
        	
            if (!($oSimpleXMLElement = self::test ($this->_url))) {
                throw new CopixException('Flux introuvable : '.$this->_url);
            }
            if (empty ($oSimpleXMLElement->channel->title) /*|| empty ($oSimpleXMLElement->channel->description)*/ || empty ($oSimpleXMLElement->channel->link)) {
                throw new CopixException('Flux invalide : '.$this->_url);
            }
            $this->_title           = (string) $oSimpleXMLElement->channel->title;
            $this->_description     = (string) $oSimpleXMLElement->channel->description;
            $this->_link            = (string) $oSimpleXMLElement->channel->link;
            $this->_pubDate         = (string) $oSimpleXMLElement->channel->pubDate;
            $this->_lastBuildDate   = (string) $oSimpleXMLElement->channel->lastBuildDate;
            $this->_image           = (string) $oSimpleXMLElement->channel->image;
            $this->_language        = (string) $oSimpleXMLElement->channel->language;

            foreach($oSimpleXMLElement->channel->item as $item){
                $oItem = _class('cms_rss_reader|feedItem');
                $oItem->title       = (string) $item->title;
                $oItem->link        = (string) $item->link;
                $oItem->description = (string) $item->description;
                $oItem->author      = (string) $item->author;
                $oItem->category    = (string) $item->category;
                $oItem->comments    = (string) $item->comments;
                $oItem->enclosure   = (string) $item->enclosure;
                $oItem->guid        = (string) $item->guid;
                $oItem->pubDate     = (string) $item->pubDate;
                $oItem->source      = (string) $item->source;
                $this->_items[]     = $oItem;
                unset ($oItem);
            }
            $this->_iterator = new ArrayIterator ($this->_items);
        }
        catch (Exception $e) {
            _log($e->getMessage());
            $this->_iterator = new ArrayIterator ($this->_items);
        }
    }

    public function getTitle () {
        return $this->_title;
    }
    
    public function getDescription () {
        return $this->$_description;
    }

    public function valid () {
       return $this->_iterator->valid ();
    }

    public function count () {
       return $this->_iterator->count ();
    }

    
    public function next () {
       $this->_iterator->next ();
    }

    /**
     * @return <type>
     */
    public function current () {
       return $this->_iterator->current ();
    }

    /**
     * Test si un flux rss est correct
     * @param string $pUrl adresse du flux
     * @return <type>
     */
    public static function test ($pUrl) {
        return @simplexml_load_file ($pUrl);
    }
}
?>