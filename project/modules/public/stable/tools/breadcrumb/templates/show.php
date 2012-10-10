<?php
$indexCrumb = 0;
foreach ($links as $link) {
	$indexCrumb++;
	$isLast = ($indexCrumb == count ($links));
	
	if ((!$isLast && $link->getShowLink ()) || ($isLast && $link->getShowLink () && $showLastLink)) {
		echo '<a href="' . $link->getURL () . '" ' . $link->getExtras ('url') . '>' . $link->getCaption () . '</a>';
	} else {
		echo '<strong>' . $link->getCaption () . '</strong>';
	}

	if (!$isLast) {
		echo ' <img src="' . _resource ('img/tools/next.png') . '" /> ';
	}
}
?>