<?php 
if (isset ($ppo->applyingCondition)) :
?>
	On applique la condition <?php echo $ppo->applyingCondition;?><br/>
<?php
endif;
echo $ppo->form; 

if  (isset ($ppo->return_url)):
?>
<a href="<?php echo $ppo->return_url?>">Retour au formulaire</a>
<?php
endif;
?>