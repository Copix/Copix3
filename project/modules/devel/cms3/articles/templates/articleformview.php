<?php
$params = new CopixParameterHandler();
$params->setParams($options);

if ($params->getParam ('caption', true)){ ?>
	<fieldset><legend>Titre</legend>
		<?php 
		echo $article->caption_hei;
		if($params->getParam ('isReplaceCaption', true) == 'true'){
			 echo " (".$params->getParam ('replacementCaption', true).")";
		} 
		?>
	</fieldset>
<?php
}
if ($params->getParam ('summary', true)){ ?>
	<fieldset><legend>Résumé</legend><?php echo $article->summary_article; ?></fieldset>
<?php
}
if ($params->getParam ('content', true)){ ?>
	<fieldset><legend>Contenu</legend><?php echo $article->content_article; ?></fieldset>
<?php
}
if ($params->getParam ('description', true)){ ?>
	<fieldset><legend>Description</legend><?php echo $article->description_hei; ?></fieldset>
<?php
}
?>