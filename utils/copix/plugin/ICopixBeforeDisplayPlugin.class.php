<?php

interface ICopixBeforeDisplayPlugin extends ICopixPlugin {
	public function beforeDisplay (& $pContent);
}