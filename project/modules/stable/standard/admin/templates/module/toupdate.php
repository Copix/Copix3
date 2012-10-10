<?php
$message = 'Les modules suivants ont une version de fichiers différente de celle installée dans Copix :';
$message .= '<ul>';
foreach ($ppo->updates as $module => $update) {
	$message .= '<li><a href="' . _url ('admin|install|updateModule', array ('moduleName' => $module)) . '">' . $update . '</a></li>';
}
$message .= '</ul>';
_eTag ('error', array ('title' => 'Modules à mettre à jour', 'message' => $message));