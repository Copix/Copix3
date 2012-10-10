
<form action="<?php echo _url ('');?>">
<h1>Choix du routage</h1>
<table>
<tr>
	<td colspan="4"><input type="radio" name="choice" value="all"/> Pour tous les champs envoyer un mail à <input type="text" name="mailto" /></td>
</tr>
<tr>
	<td colspan="4"><input type="radio" name="choice" value="rules"/> Traitement conditionnel</td>
</tr>
<tr>
	<th>Champ</th>
	<th>Condition</th>
	<th>Valeurs</th>
	<th>Action</th>
</tr>
<?php
foreach ($ppo->rules as $rule):

?>
<tr>
	<td><?php _etag ('select', array ('name'=>'field[]', 'values'=>$ppo->fieldList, 'selected'=> $rule->field)) ?></td>
	<td><?php _etag ('select', array ('name'=>'condition[]', 'values'=>$ppo->arConditions, 'selected'=> $rule->condition)) ?></td>
	<td><input type="text" name="values[]" value="<?php echo implode (' ', $rule->values);?>"/></td>
	<td>envoyer un mail à <input type="text" name="mailto" value="<?php list ($kind, $value) = explode (':', $rule->way); echo $value;?>"/></td>
</tr>
<?php
endforeach;
?>
<tr>
	<td><?php _etag ('select', array ('name'=>'field[]', 'values'=>$ppo->fieldList)) ?></td>
	<td><?php _etag ('select', array ('name'=>'condition[]', 'values'=>$ppo->arConditions)) ?></td>
	<td><input type="text" name="values[]" /></td>
	<td>envoyer un mail à <input type="text" name="mailto" /></td>
</tr>
<tr>
	<td><?php _etag ('select', array ('name'=>'field[]', 'values'=>$ppo->fieldList)) ?></td>
	<td><?php _etag ('select', array ('name'=>'condition[]', 'values'=>$ppo->arConditions)) ?></td>
	<td><input type="text" name="values[]" /></td>
	<td>envoyer un mail à <input type="text" name="mailto" /></td>
</tr>
<tr>
	<td><?php _etag ('select', array ('name'=>'field[]', 'values'=>$ppo->fieldList)) ?></td>
	<td><?php _etag ('select', array ('name'=>'condition[]', 'values'=>$ppo->arConditions)) ?></td>
	<td><input type="text" name="values[]" /></td>
	<td>envoyer un mail à <input type="text" name="mailto" /></td>
</tr>
<tr>
	<td colspan="4">Routage par défaut (si aucune condition n'est vérifié) envoyer un mail à <input type="text" name="other"/></td>
</tr>
<tr>
	<td colspan="4"><input type="submit" values="Sauvegarder les règles"></td>
</tr>
</table>
</form>