<?php

interface ICopixPlugin {
	public function __construct ($pConfig = null);
	public function getConfig ();
	
	public function getDescription ();
	public function getCaption ();
}