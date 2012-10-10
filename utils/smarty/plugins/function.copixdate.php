<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     copixdate
 * Version:  1
 * Date:     May 21, 2002
 * Author:   Gérald Croes
 * input: lang - la langue par défaut
 *        timestamp - timestamp to use instead of the current date.
 * Examples: {copixlogo}
 * Simply output the made with Copix Logo
 * -------------------------------------------------------------
 */
function smarty_function_copixdate($params, &$smarty) {
    extract($params);

    //checking required params.
    if (empty ($lang)){
     $smarty->_trigger_fatal_error("[plugin copixdate] parameter 'lang' cannot be empty");
    }

    //check the timestamp
    if (empty ($timestamp)){
       $timestamp = time ();
    }
    
    //check if the language is supported.
    if (!in_array ($lang, array ('fr', 'it', 'pg', 'en', 'pl', 'nl'))){
     $smarty->_trigger_fatal_error("[plugin copixdate] unsuported language: $lang");
    }else{
       //init the convertion arrays
       switch ($lang){
          case 'fr':
             $converter['day'] = array ("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
             $converter['month'] = array (" ", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Décembre");
             $converter['format'] = "[DAY] [DAYNUM] [MONTH] [YEAR]";
             break;
          case 'en':
             $converter['day'] = array ("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
             $converter['month'] = array (" ", "January","February","March","April","May","June","July","August","September","October","November","December");
             $converter['format'] = "[DAY] [DAYNUM] [MONTH] [YEAR]";
             break;
          case 'it':
             $converter['day'] = array ("Domenica","Lunedì","Martedì","Mercoledì","Giovedì","Venerdì","Sabato");
             $converter['month'] = array (" ", "Gennaio","Febbraio","Marzo","Aprile","Maggio","Giugno","Luglio","Agosto","Settembre","Ottobre","Novembre","Dicembre");
             $converter['format'] = "[DAY] [DAYNUM] [MONTH] [YEAR]";
             break;
          case 'pg':
             $converter['day'] = array ("Domingo","Segunda-feira","Terça-feira","Quarta-feira","Quinta-feira","Sexta-feira","Sábado");
             $converter['month'] = array(" ","Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
             $converter['format'] = "[DAY], [DAYNUM] de [MONTH] de [YEAR]";
             break;
          case 'pl':
             $converter['day'] = array ("Niedziela","Poniedzialek","Wtorek","Sroda","Czwartek","Piatek","Sobota");
             $converter['month'] = array (" ","Styczen","Luty","Marzec","Kwiecien","Maj","Czerwiec","Lipiec","Sierpien","Wrzesien","Pazdziernik","Listopad","Grudzien");
             $converter['format'] = "[DAY] [DAYNUM] [MONTH] [YEAR]";
             break;
          case 'nl':
             $converter['day'] = array('zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag');
             $converter['month'] = array(' ', 'januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december');
             $converter['format'] = "[DAY] [DAYNUM] [MONTH] [YEAR]";
             break;
       }
    }
    $dayNum    = date ("w", $timestamp);
/*
    $day       = $converter['day'][$dayNum];
    $month     = $converter['month'][date ("n", $timestamp)];
    $year      = date ("Y", $timestamp);
*/
    return str_replace (array ('[DAY]', '[DAYNUM]', '[MONTH]', '[YEAR]'),
                               array ($converter['day'][$dayNum], $dayNum, $converter['month'][date ("n", $timestamp)], date ("Y", $timestamp)),
                               $converter['format']);
}
?>
