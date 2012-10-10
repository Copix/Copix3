<?php
$indexCrumb = 0;
if (count ($links)) {
	echo '<p style="padding: 0px;margin: 0px; spacing:0px;">';
	foreach ($links as $link) {
		$indexCrumb++;
		$isLast = ($indexCrumb == count ($links));
	
		if ((!$isLast && $link->getShowLink ()) || ($isLast && $link->getShowLink () && $showLastLink)) {
			echo '<a href="' . $link->getURL () . '" ' . $link->getExtras ('url') . '>' . $link->getCaption () . '</a>';
		} else {
			echo '<b>' . $link->getCaption () . '</b>';
		}
		if (!$isLast) {
			echo ' <img src="' . _resource ('img/tools/next.png') . '" /> ';
		}
	}
	echo '</p>';
}