<?php
$html = array ();
foreach ($checkboxes as $checkbox) {
	$temp = '<input type="checkbox" id="' . $checkbox['id'] . '" name="' . $params['name'] . '[]" value="' . $checkbox['value'] . '" ' . $checkbox['selected'] . ' ' . $params['extra'] . ' ';
	if (isset ($params['disabled'])) {
		$temp .= (is_bool ($params['disabled']) && $params['disabled']) ? 'disabled="disabled" ' : 'disabled="' . $params['disabled'] . '" ';
	}
	$temp .= ' />';
	if (!empty ($checkbox['caption'])) {
		$temp .= '<label for="' . $checkbox['id'] . '"> ' . $checkbox['caption'] . '</label>';
	}
	$html[] = $temp;
}

echo implode ($params['separator'], $html);

if (!empty ($params['help'])) {
	echo ' ' . _tag ('popupinformation', array ('width' => 300), $params['help']);
}