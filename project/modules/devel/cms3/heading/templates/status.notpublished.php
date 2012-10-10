<?php

$message = '<p>La page <strong><a href="'._url('heading|element|', array('heading' => $ppo->element->parent_heading_public_id_hei, 'selected[0]' => $ppo->element->id_helt.'|'.$ppo->element->type_hei)).'">'. $ppo->element->caption_hei . '</a></strong> (identifiant public : ' . $ppo->element->public_id_hei . ") n'est pas publiée.</p>";
$message .= '<ul>';
$message .= '<li><a href="'._url('heading|element|prepareEdit', array('type' => $ppo->element->type_hei, 'id' => $ppo->element->id_helt, 'heading' => $ppo->element->parent_heading_public_id_hei)).'">Editer cette page</a></li>';
$message .= '</ul>';

_eTag ('error', array ('message' => $message));
?>