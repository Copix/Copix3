<?php
echo CopixZone::process ('UserPreferences', array (
	'mode' => 'full',
	'user' => $ppo->user,
	'userhandler' => $ppo->userhandler,
	'modulePref' => $ppo->modulePref,
	'redirect' => _url ('admin|userpreferences|modules', array ('user' => $ppo->user, 'userhandler' => $ppo->userhandler, 'highlight' => $ppo->modulePref)),
));
_eTag ('back', array ('url' => _url ('admin|userpreferences|modules', array ('user' => $ppo->user, 'userhandler' => $ppo->userhandler))));
?>