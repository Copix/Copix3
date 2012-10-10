<?php
class FeedItemIterator {
    private $_items;
    private $_itemsDate;
    private $_iterator;
    
    public function  __construct () {
        $this->_items     = array ();
        $this->_itemsDate = array ();
        $this->_updateIterator();
    }

    public function addItem (FeedItem $pFeed) {
        $this->_items[] = $pFeed;
        $this->_itemsDate[] = CopixDateTime::GMTToyyyymmdd ($pFeed->pubDate);
        $this->_updateIterator ();
    }

    private function _updateIterator () {
        $this->_iterator = new ArrayIterator ($this->_items);        
    }

    /**
     * Trie les items par date décroissante
     */
    public function sort () {
        asort ($this->_itemsDate);
        $aOrderedKeys = array_keys ($this->_itemsDate);
        $aOrderedItems = array ();
        $this->_itemsDate = array ();

        for($i = sizeof ($this->_items)-1; $i >= 0; $i--) {
            $j = $aOrderedKeys[$i];
            $aOrderedItems[] = $this->_items[$j];
            $this->_itemsDate[] = CopixDateTime::GMTToyyyymmdd ($this->_items[$j]->pubDate);
        }
        $this->_items = $aOrderedItems;
        unset ($aOrderedItems,$aOrderedKeys);
        $this->_updateIterator ();
    }

    public function current () {
       return $this->_iterator->current ();
    }

    public function key () {
       return $this->_iterator->key ();
    }

    public function next () {
       $this->_iterator->next ();
    }

    public function valid () {
       return $this->_iterator->valid ();
    }
    
    public function rewind () {
        $this->_iterator->rewind ();
    }
}
?>