<?php
$html = array ();
foreach ($radios as $radio) {
	$temp = '<input type="radio" id="' . $radio['id'] . '" name="' . $params['name'] . '" value="' . $radio['value'] . '" ' . $radio['selected'] . ' ' . $params['extra'] . ' ';
	if (isset ($params['disabled'])) {
		$temp .= (is_bool ($params['disabled']) && $params['disabled']) ? 'disabled="disabled" ' : 'disabled="' . $params['disabled'] . '" ';
	}
	$temp .= ' />';
	if (!empty ($radio['caption'])) {
		$temp .= '<label for="' . $radio['id'] . '"> ' . $radio['caption'] . '</label>';
	}
	$html[] = $temp;
}

echo implode ($params['separator'], $html);

if (!empty ($params['help'])) {
	echo ' ' . _tag ('popupinformation', array ('width' => 300), $params['help']);
}