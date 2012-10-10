<?php
if ($ppo->uploadedFile != null) {
	echo '<h2>';
	_etag ('i18n', array ('key'=>'repository.upload.successful', $ppo->uploadedFile));
	echo '</h2>';
}

echo $ppo->zoneform;
?>