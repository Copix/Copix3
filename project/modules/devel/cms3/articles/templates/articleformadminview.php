<?php
foreach ($listeArticles as $key=>$article){
	$article = new ArticleProxy($article);
	$border = "border-bottom:1px solid #cccccc";
	?>
<div style="cursor: pointer;" id="docClicker<?php echo $identifiantFormulaire;?>" onClick="$('clicker<?php echo $identifiantFormulaire; ?>').fireEvent('click');">
	<?php 
		echo "<div style='border-bottom:1px solid #cccccc;padding:5px 10px;background-color:#F0F0F0'>Nom de l'article : " . $article->caption_hei . "</div>";
		$content = $options['date_create'] ? "Ajouté le : " . CopixDateTime::yyyymmddhhiissToFormat($article->date_create_hei, 'd/m/Y à H:i:s') . "<br />" : "";
		$content.= $options['date_update'] ? "Mis à jour le : " . CopixDateTime::yyyymmddhhiissToFormat($article->date_update_hei, 'd/m/Y à H:i:s') . "<br />" : "";
		$content.= $options['content'] ? $article->content_article : "";
		$content.= $content && $options['summary'] ? "<hr />" : "";
		$content.= $options['summary'] ? $article->summary_article : "";
		echo "<div style='padding:5px 10px;max-height:100px;overflow-y:auto;".(count($listeArticles)>$key+1 ? $border : "")."'>" . $content . "</div>"; 
	?>
</div>
<?php } ?>