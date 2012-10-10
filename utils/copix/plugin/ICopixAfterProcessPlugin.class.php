<?php
interface ICopixAfterProcessPlugin extends ICopixPlugin {
	public function afterProcess ($pActionReturn);
}