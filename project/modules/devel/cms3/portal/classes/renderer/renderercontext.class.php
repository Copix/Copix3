<?php
/**
 * Les contextes de rendus possible pour les éléments du CMS
 * @package cms
 * @subpackage portal 
 */
class RendererContext {
	/**
	 * En cours de modification
	 */
	const UPDATED = 1;

	/**
	 * Pour le moteur de recherche
	 */
	const SEARCHED = 2;

	/**
	 * En cours d'affichage
	 */
	const DISPLAYED = 3;
	
	/**
	 * En cours d'impression
	 */
	const PRINTED = 4;
	
	/**
	 * En cours d'affichage (ou de prévisualisation) dans les écrans d'administration
	 */
	const DISPLAYED_ADMIN = 5; 

	/**
	 * Vérifie que le contexte donné en paramètre est valide
	 *
	 * @param int $pContext
	 */
	public function assertIsValid ($pContext){
		if (!in_array ($pContext, array (self::UPDATED, self::SEARCHED, self::DISPLAYED, self::PRINTED, self::DISPLAYED_ADMIN))){
			throw new CopixException ("Contexte de rendu inconnu [$pContext]");
		}
	}
}