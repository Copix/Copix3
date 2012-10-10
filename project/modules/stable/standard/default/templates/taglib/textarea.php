<?php
// erreur
if (isset ($params['error']) && $params['error']) {
	$htmlParams['class'] = (isset ($htmlParams['class'])) ? $htmlParams['class'] .= ' error' : 'error';
}

// tag
$html  = '<textarea ';
foreach ($htmlParams as $key => $param) {
	$html .= $key . '="' . $param . '" ';
}
if (isset ($params['extra'])) {
	$html .= $params['extra'] . ' ';
}
$html .= '>' . (isset ($params['value']) ? $params['value'] : null) . '</textarea>';

// popup d'aide
$htmlAide = null;
if (isset ($params['help']) && $params['help'] = null) {
	$htmlAide = ' ' . _tag ('popupinformation', array ('width' => 300), $params['help']);
}

echo $html . $htmlAide;