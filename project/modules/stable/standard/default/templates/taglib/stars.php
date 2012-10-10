<input type="hidden" name="<?php echo $name ?>" id="<?php echo $name ?>" value="<?php echo $value ?>" />
<?php
$stars = array ();
for ($x = 1; $x <= $max; $x++) {
	$stars[$x - 1] = '<img src="' . ($value >= $x ? $star : $starDisabled) . '" id="' . $name . '_' . $x . '" onclick="javascript: taglibstars_change (\'' . $name . '\', ' . $x . ')" style="cursor: pointer" ';
	if (array_key_exists ($x, $captions)) {
		$stars[$x - 1] .= ' alt="' . $captions[$x] . '" title="' . $captions[$x] . '"';
	}
	$stars[$x - 1] .= ' />';
}
echo implode ('&nbsp;', $stars);

if (count ($captions) > 0) {
	echo '<br />';
	foreach ($captions as $index => $caption) {
		$style = ($value == $index) ? null : 'style="display: none"';
		echo '<div id="' . $name . '_caption_' . $index . '" ' . $style . '>' . $caption . '</div>';
	}
}