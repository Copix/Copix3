<?php
if ($ppo->preview !== false) {
?>
<div id="wiki_preview">
<?php echo $ppo->content; ?>
</div>
<?php } ?>
<form action="<?php echo $ppo->action;?>" method="POST">
<input type="hidden" name="WikiName" value="<?php echo $ppo->WikiName;?>"/>
<textarea name="content" cols="100" rows="15">
<?php echo $ppo->content; ?>
</textarea><br/>
Commentaires : <input type="text" name="comment" value="<?php echo $ppo->comment; ?>" /><br/>
<input type="submit" value="Valider" name="valid">
<input type="submit" value="Prévisualisation" name="preview">
</form>