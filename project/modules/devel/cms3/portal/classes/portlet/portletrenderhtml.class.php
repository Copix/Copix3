<?php
/**
 * Facilite la création de portlet avec le rendu HTML uniquement
 */
abstract class PortletRenderHTML extends Portlet {
	/**
	 * rendu du contenu du text
	 *
	 * @param string $pRendererContext le contexte de rendu (Modification, Moteur de recherche, affichage, ....)
	 * @param string $pRendererMode    le mode de rendu demandé (généralement le format de sortie attendu)
	 * @return string
	 */
	protected function _renderContent ($pRendererMode, $pRendererContext){
		if ($pRendererMode == RendererMode::HTML) {
			if ($pRendererContext == RendererContext::DISPLAYED || $pRendererContext == RendererContext::DISPLAYED_ADMIN || $this->getEtat () == self::DISPLAYED) {
				$isAdmin = ($pRendererContext == RendererContext::DISPLAYED_ADMIN || $pRendererContext == RendererContext::UPDATED);
				return $this->_renderHTMLDisplay ($isAdmin);
			} else {
				return $this->_renderHTMLUpdate ();
			}
		}
		throw new CopixException ('Mode de rendu non pris en charge.');
	}

	/**
	 * Effectue le rendu pour la partie mise à jou rde la portlet (administration)
	 */
	abstract protected function _renderHTMLUpdate ();

	/**
	 * Effectue le rendu pour la partie affichage de la porlet
	 *
	 * @param boolean $pIsAdmin Indique si on est dans la partie admin ou la partie publique
	 */
	abstract protected function _renderHTMLDisplay ($pIsAdmin);
}