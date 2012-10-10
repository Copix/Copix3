<?php
/**
 * Classe qui contient la liste des modes de rendus pour les pages 
 *
 */
class RendererMode {
	/**
	 * Mode de rendu HTML
	 */
	const HTML = 1;

	/**
	 * Mode de rendu Texte
	 */
	const TEXT = 2;

	public function assertIsValid ($pMode){
		if (!in_array ($pMode, array (self::HTML, self::TEXT))){
			throw new CopixException ("Mode de rendu inconnu [$pMode]");
		}
	}
}