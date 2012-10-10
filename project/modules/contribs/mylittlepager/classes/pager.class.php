<?php
class pager
{
public $par_page; // nombre d'enregistrements par page
public $total;
public $nbre_pages;
public $depart; // première ligne affichée par la page
public $base_url;
public $pageactuelle;

//########################################################################
// $letotal             : est le nombre total d'enregistrement dans la BDD
// $page                : est la page en cours à afficher
// $base_url_page       : est la page en cours qui affiche les enregistrements paginés
// $affichages_par_page : est explicite.
function __construct($letotal, $page, $base_url_page, $affichages_par_page)
 {
 $lapage = intval($page);
 if($lapage<=0) $lapage=1;

 $this->par_page   = $affichages_par_page;

 $this->base_url   = $base_url_page;
 
 $this->total      = $letotal;
 
 $this->nbre_pages = ceil( $this->total / $this->par_page );
 
 if( $lapage > $this->nbre_pages) $this->pageactuelle = $this->nbre_pages;
                             else $this->pageactuelle = $lapage;

 $this->depart = ($this->pageactuelle-1) * $this->par_page; // On calcule la première entrée à lire
 }
//########################################################################
public function getdepart()
 {
 return $this->depart;
 }
//########################################################################
public function getparpage()
 {
 return $this->par_page;
 }
//########################################################################
// cette fonction est à transformer selon vos besoins. C'est elle qui
// formatte l'affichage du navigateur en fonction de la façon dont vous
// voulez voir les différents liens vers chaque page. Le navigateur est ici
// renvoyé sous forme de chaine contenant tous les liens.
public function getnavigateur()
 {
 $navig = "Page(s): ";
 for($i=1; $i<=$this->nbre_pages; $i++)
   {
     //On va faire notre condition
     if($i==$this->pageactuelle) //Si il s'agit de la page actuelle...
         {
         $navig .= "<b>".$i."</b> "; 
         }	
     else //Sinon...
         {
         $navig .= '<a href="'.$this->base_url.'?page='.$i.'">'.$i.'</a> ';
         }
   }

 return $navig;
 }
//########################################################################
}
?>
