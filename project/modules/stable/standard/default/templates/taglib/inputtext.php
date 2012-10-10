<?php
// icone d'erreur
if (isset ($params['error']) && $params['error']) {
	$htmlParams['class'] = (isset ($htmlParams['class'])) ? $htmlParams['class'] .= ' error' : 'error';
}

$htmlParams['class'] = (isset ($htmlParams['class'])) ? $htmlParams['class'] .= ' inputText' : 'inputText';

// tag input text
$html  = '<input ';
foreach ($htmlParams as $key => $param) {
	$html .= $key . '="' . $param . '" ';
}
if (isset ($params['extra'])) {
	$html .= $params['extra'] . ' ';
}
$html .= '/>';

// popup d'aide
$htmlAide = null;
if (!empty ($params['help'])) {
	$htmlAide = ' ' . _tag ('popupinformation', array ('width' => 300), $params['help']);
}

echo $html . $htmlAide;