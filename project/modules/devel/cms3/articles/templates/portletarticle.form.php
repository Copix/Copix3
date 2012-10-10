<?php 
	//initialization des variables	
	$identifiantFormulaire = $portlet->getRandomId ()."_pos_".$position;
	$articleOptions  = array ();
	if (isset($article)){
		$articleOptions = $portlet->getPortletElementAt($position)->getOptions ();	
	}
	
	//si on veut afficher un article de la portlet
	if (!$justAddArticle){
		//si on veut afficher un article existant, on l'insere dans une div qui l'identifie, sinon pour un nouvel article ce sera fait par l'appel javascript
		if (!$newArticleVide){?>
<div id="div_<?php echo $identifiantFormulaire;?>">
<?php 	} ?>
<div class="articleBloc">
		<div class="thumbArticle" id="article_<?php echo $identifiantFormulaire;?>">
		<?php		
			//affichage du image
			if(isset($article)){
				if ($article->type_hei == "heading"){
					$results = _ioClass('heading|headingelementinformationservices')->getChildrenByType ($article->public_id_hei, 'article');					
					$children = _ioClass('heading|headingelementinformationservices')->orderElements ($results, array_key_exists('order', $articleOptions) ? $articleOptions['order'] : 'display_order_hei');
					foreach ($children as $child){
						$listeArticles[] = _ioClass('articles|articleservices')->getByPublicId ($child->public_id_hei);
					} 				
				} else {
					$listeArticles[] = $article;			
				}
				$tpl = new CopixTpl();
				$tpl->assign('options', $articleOptions);
				$tpl->assign('listeArticles', $listeArticles);
				$tpl->assign('identifiantFormulaire', $identifiantFormulaire);
				echo $tpl->fetch('articleformadminview.php');
			} else {
				echo "<div style='text-align:center;cursor:pointer;padding:20px;' id='docClicker".$identifiantFormulaire."'><span id='articleChoix".$portlet->getRandomId ()."'>Ajouter un article</span></div>";	
				CopixHTMLHeader::addJSDOMReadyCode("$('docClicker".$identifiantFormulaire."').addEvent('click', function(){ $('clicker".$identifiantFormulaire."').fireEvent('click');});");
			}
		?>
		</div>
		<div class="optionsFile">
			<?php //div des options 
		
			//options
			echo CopixZone::process ('articles|articleOptionMenu', array ('options'=>$articleOptions, 'identifiantFormulaire'=>$identifiantFormulaire, 'portlet_id'=>$portlet->getRandomId (), 'position'=>$position, 'heading'=>isset($article) && $article->type_hei == "heading"));
			
			$selected = (isset($article)) ? $article->public_id_hei : '';
			
			echo CopixZone::process ('heading|headingelement/headingelementchooser', array('arTypes'=>array('article'), 'mode'=>ZoneHeadingElementChooser::ARTICLE_CHOOSER_MOD, 'selectedIndex'=>$selected, 'inputElement'=>'id_article_'.$identifiantFormulaire, 'identifiantFormulaire'=>$identifiantFormulaire, 'multipleSelect'=>true));
			CopixHTMLHeader::addJSDOMReadyCode("
				$('libelleElement".$identifiantFormulaire."').setStyle('display','none');
				$('clicker".$identifiantFormulaire."').setStyle('display','none');");
		
			?>
			<form id="form_<?php echo $identifiantFormulaire;?>" class="headForm">
				<input type="hidden" id="position_article_<?php echo $identifiantFormulaire; ?>" name="position_article_<?php echo $identifiantFormulaire; ?>" value="<?php echo $position; ?>" />
			</form>
		</div>
	</div>
	<?php 
		//pour l'article existant, on ferme la div
		if (!$newArticleVide){ ?>
</div>
		<?php
		}
		CopixHTMLHeader::addJSDOMReadyCode ("
		$('id_article_".$identifiantFormulaire."').addEvent('change', function(){updateArticle('".$identifiantFormulaire."', '".$portlet->getRandomId()."', '"._request('editId')."');});
		");
	}
	//si on ne veut afficher que le bouton ajouter
	else{ 
		CopixHTMLHeader::addJSLink(_resource('articles|js/tools.js'));
	?>
	<div id="addArticle_<?php echo $portlet->getRandomId ();?>">
		<input type="hidden" id="position_<?php echo $portlet->getRandomId ();?>" value="<?php echo $position; ?>"/>
	</div> 
	<?php
	}
?>
<script>
function addElements<?php echo $identifiantFormulaire;?> (mutex){
	
	var selectedElements = getSelectedElements ('<?php echo $identifiantFormulaire;?>');
	if (selectedElements.length == 1){
		updateArticle('<?php echo $identifiantFormulaire;?>', '<?php echo $portlet->getRandomId ();?>', '<?php echo _request('editId');?>');
	} else {
		selectedElements.each(function (el){
			addArticle('<?php echo $identifiantFormulaire;?>', '<?php echo $portlet->getRandomId ();?>', '<?php echo _request('editId');?>', el.get('pih'), false);
		});
	}
}
</script>