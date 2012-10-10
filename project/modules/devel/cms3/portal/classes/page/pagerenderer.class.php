<?php
/**
 * Classe de rendu abstraite pour les pages
 */
abstract class PageRenderer {
	/**
	 * La page en cours de rendu
	 *
	 * @var Page
	 */
	protected $_page = null;
	
	/**
	 * Le mode de rendu demandé
	 *
	 * @var int
	 * @see RendererMode
	 */
	protected $_rendererMode = null;
	
	/**
	 * Le contexte de rendu demandé.
	 *
	 * @var int
     * @see RendererContext 
	 */
	protected $_rendererContext = null;
	
	/**
	 * Les paramètres supplémentaires qui ont étés envoyés au renderer
	 *
	 * @var unknown_type
	 */
	protected $_params = array ();
	
	/**
	 * Trouve les variables de template dont le contenu est passé en paramètre
	 * 
	 * @param string $pTemplateContent le contenu du template dans lequel on va chercher les variables
	 * @return array  
	 */
	protected function _extractVarsFromTemplateContent ($pTemplateContent){
		$out = array ();
		preg_match_all('{\$(\w+)}', $pTemplateContent, $out);
		return count ($out) ? $out[1] : $out; 
	}
	
	/**
	 * Trouve les variables de template dont l'identifiant est passé en paramètre
	 * 
	 * @param string $pTemplateFileId l'identifiant de template
	 * @return array
	 */
	protected function _extractVarsFromTemplateFile ($pTemplateFileId){
		return $this->_extractVarsFromTemplateContent (CopixFile::read ($pTemplateFileId));
	}

	/**
	 * Demande le rendu d'un élément
	 *
	 * @param page $pPage    la page a afficher
	 * @param string  $pRenderedId le nom du renderer demandé
	 * @param unknown_type $pExtra
	 */
	public function render (Page $pPage, $pMode, $pContext, $pExtra = array ()){
		$this->_page = $pPage;

		$this->_rendererMode    = $pMode;
		$this->_rendererContext = $pContext;

		$this->_params = $pExtra;
	}
}