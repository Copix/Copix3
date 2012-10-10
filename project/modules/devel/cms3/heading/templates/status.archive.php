<?php
if ($ppo->canWrite){
	$message = 'L\'élément ' . $ppo->element->caption_hei . ' ('.$ppo->captionType.' d\'identifiant public : ' . $ppo->element->public_id_hei . ') n\'est pas disponible.';
} else {
	$message = $ppo->captionType . ' non disponible';
}
if ($ppo->search != null) {
	$message .= '<br /><a href="' . $ppo->search . '">Chercher des éléments similaires</a>';
}
_eTag ('error', array ('message' => $message));
?>