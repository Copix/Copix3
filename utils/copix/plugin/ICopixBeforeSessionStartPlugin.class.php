<?php
interface ICopixBeforeSessionStartPlugin extends ICopixPlugin {
	public function beforeSessionStart ();
}