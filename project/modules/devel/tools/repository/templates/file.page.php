<form action="<?php echo _url ('repository|file|list');?>" method="POST" id="listform">
<table>
	<tr>
		<td><?php _etag ('i18n', 'repository.form.category');  ?></td>
		<td>
		<?php _etag ('select',  array ('name'=>'id_category', 
		'id'=>'id_category', 
		'selected'=>$ppo->id_category, 
		'values'=> $ppo->list_categories, 
		'extra'=>'onchange="document.getElementById(\'listform\').submit();"'));?> 
		</td>
		<td><?php _etag ('i18n', 'repository.form.subcategory');  ?></td>
		<td>
		<?php _etag ('select',  array ('name'=>'id_subcategory', 
		'id'=>'id_subcategory', 
		'selected'=>$ppo->id_subcategory, 
		'values'=> $ppo->list_subcategories, 
		'extra'=>'onchange="document.getElementById(\'listform\').submit();"'));?>
		</td>
	</tr>
</table>
<br/>
<?php
echo $ppo->zonelist;
?></form>
