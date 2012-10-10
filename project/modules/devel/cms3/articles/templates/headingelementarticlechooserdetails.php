<div class="HeadingElementChooserDetail">
	<table width="100%" cellspacing="0">
		<tr>
			<th></th>
			<th>Nom de l'article</th>
			<th>Description</th>
			<th>Modifié</th>
			<th>Dernier auteur</th>
		</tr>
	<?php
		foreach ($ppo->children as $children){
			$element = _ioClass('articles|articleservices')->getByPublicId($children->public_id_hei);
			$article = new ArticleProxy ($element);
			echo "<tr>";
			echo "<td><input type='checkbox' ";
			if (sizeof($ppo->children) == 1){
				echo "checked='checked' class='elementchooserfileselectedstate' ";
			} else {
				echo "class='elementchooserfilenoselectedstate' ";
			}
			echo " name='' libelle='".$element->caption_hei."' pih='".$element->public_id_hei."' /></td>";
			$js = new CopixJSWidget();
			$js->showArticleContent($article->content_article, $ppo->formId);
			echo '<td><a id="captionArticle'.$children->id_helt.'" href="javascript:void(0);">'.$element->caption_hei."</a></td>";	
			CopixHTMLHeader::addJSDOMReadyCode("$('captionArticle".$children->id_helt."').addEvent('click', function(){".$js."});");
						
			echo "<td>".$element->description_hei."</td>";			
			echo "<td>".CopixDateTime::yyyymmddhhiissToFormat($element->date_update_hei, 'Y-m-d')."</td>";
			echo "<td>".$element->author_caption_update_hei."</td>";
			echo "</tr>";
		}
	?>
	</table>
	<?php
		if (count($ppo->children) == 1){
			CopixHTMLHeader::addJSDOMReadyCode($js); 
		} else {
			CopixHTMLHeader::addJSDOMReadyCode("showArticleContent(\"<div style='text-align: center;'>Aperçu<br />Cliquez sur un article pour afficher son contenu</div>\", \"".$ppo->formId."\");"); 		
		}
	?>
</div>