<?php

$message = '<p>La rubrique <strong><a href="'._url('heading|element|', array('heading' => $ppo->element->parent_heading_public_id_hei)).'">'. $ppo->element->caption_hei . '</a></strong> (identifiant public : ' . $ppo->element->public_id_hei . ") n'a pas de page d'accueil.</p>";
$message .= '<ul>';
$message .= '<li><a href="'._url('heading|element|prepareEdit', array('type' => $ppo->element->type_hei, 'id' => $ppo->element->id_helt, 'heading' => $ppo->element->parent_heading_public_id_hei)).'">Editer cette rubrique</a></li>';
$message .= '</ul>';

_eTag ('error', array ('message' => $message));
?>