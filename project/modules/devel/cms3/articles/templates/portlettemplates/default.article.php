<div>
	<?php 
	if ($isDateCreate){
		echo "<span class='dateCmsArticle'>Ajouté le : " . CopixDateTime::yyyymmddhhiissToFormat($article->date_create_hei, 'd/m/Y à H:i:s') . "</span><br />";
	}
	if ($isDateUpdate){
		echo "<span class='dateCmsArticle'>Mis à jour le : " . CopixDateTime::yyyymmddhhiissToFormat($article->date_update_hei, 'd/m/Y à H:i:s') . "</span><br />";
	}
	if ($isSummary){
		echo $article->summary_article;
	}
	if ($isContent){
		echo $article->content_article;
	}
	?>
</div>