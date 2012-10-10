<?php
_eTag ('inputtext', array (
	'name' => $params['name'] . 'day_date',
	'value' => $params['value_day'],
	'size' => '1', 'maxlength' => '2',
	'next' => $params['name'] . 'month_date'
));

echo ' ';
_eTag ('inputtext', array (
	'name' => $params['name'] . 'month_date',
	'value' => $params['value_month'],
	'size' => '1', 'maxlength' => '2',
	'next' => $params['name'] . 'year_date', 'previous' => $params['name'] . 'day_date'
));

echo ' ';
_eTag ('inputtext', array (
	'name' => $params['name'] . 'year_date',
	'value' => $params['value_year'],
	'size' => '3', 'maxlength' => '4',
	'previous' => $params['name'] . 'month_date'
));

if (isset ($params['help']) && $params['help'] != null) {
	echo ' ';
	_eTag ('popupinformation', array ('width' => 300), $params['help']);
}