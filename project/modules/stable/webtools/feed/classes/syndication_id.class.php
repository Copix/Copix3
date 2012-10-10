<?php
/**
 * Indentifiant unique
 */
class SyndicationId {
	/**
	 * Valeur unique
	 *
	 * @var string
	 */
	public $value = null;
	
	/**
	 * Indique si c'est un lien permanent
	 *
	 * @var boolean
	 */
	public $isPermaLink = null;
	
	/**
	 * Génère un identifiant unique, le met dans value, et le retourne
	 * 
	 * @param string $pPrefix Préfixe à appliquer à l'identifiant généré
	 * @return string
	 */
	public function generate ($pPrefix = null) {
		$this->value = uniqid ($pPrefix);
		return $this->value;
	}
}