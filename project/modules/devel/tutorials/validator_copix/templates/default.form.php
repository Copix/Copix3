<?php
if (isset ($ppo->errors)){
	echo '<h2>'._i18n ('validator_copix.form.error').'</h2>';
	_etag ('ulli', array ('values'=>$ppo->errors));
}
?>
<form action="<?php echo _url ("validator_copix||valid");?>">
Nom : <input type="text" name="nom"/><br/>
Prénom : <input type="text" name="prenom"/><br/>
Date de naissance : <input type="text" name="datenaissance"/><br/>
Téléphone : <input type="text" name="telephone"/><br/>
Technique de validation : <select name="method"><option value="std">Standard</option>
<option value="i18n">Internationalisée</option>
<option value="complex">Validateurs complexes</option>
</select>

<br/>
<input type="submit" value="Valider les données"/>
</form>