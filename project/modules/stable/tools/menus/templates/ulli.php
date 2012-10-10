<?php
if (!function_exists ('generateMenu')) {
	function _generateMenu ($pMenu) {
		echo '<ul>';
		foreach ($pMenu->getChildren () as $menu) {
			echo '<li>';
			if ($menu->getUrl () != null) {
				echo '<a href="' . $menu->getUrl () . '">';
			}
			echo $menu->getCaption ();
			if ($menu->getUrl () != null) {
				echo '</a>';
			}
			if (count ($menu->getChildren ()) > 0) {
				_generateMenu ($menu);
			}
			echo '</li>';
		}
		echo '</ul>';
	}
}

_generateMenu ($menu);