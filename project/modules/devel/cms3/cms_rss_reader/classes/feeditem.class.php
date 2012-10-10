<?php
class FeedItem {

    // obligatoires
    public $title;
    public $description;

    // facultatifs
    public $link;
    public $pubDate;    // Date de publication
    public $guid;       // Identifiant unique de l'item

    public $author;
    public $category;
    public $comments;   // Lien vers une page de ccommentaires sur l'item
    public $enclosure;  // Objet media attaché à l'item
    public $source;     // Channel de l'item

}
?>