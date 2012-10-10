<?php

interface ICopixBeforeProcessPlugin extends ICopixPlugin {
	public function beforeProcess (& $pAction);
}