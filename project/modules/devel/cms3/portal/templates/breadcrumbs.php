<?php
$indexCrumb = 0;
if (count ($links)){
	foreach ($links as $link) {
		$indexCrumb++;
		$isLast = ($indexCrumb == count ($links));	
		echo ' &gt; ';
		if ((!$isLast && $link->getShowLink ()) || ($isLast && $link->getShowLink () && $showLastLink)) {
			echo '<a href="' . $link->getURL () . '" ' . $link->getExtras ('url') . '>' . $link->getCaption () . '</a>';
		} else {
			echo '<span>'.$link->getCaption ().'</span>';
		}
	}
}