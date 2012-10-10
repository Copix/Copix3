<?php
interface ICopixCatchExceptionsPlugin extends ICopixPlugin {
	public function catchExceptions ($pException);
}