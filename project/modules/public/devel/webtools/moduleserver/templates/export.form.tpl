<form method="POST" action="{copixurl dest='moduleserver|admin|export'}" >
<label style="width:70px;" for="name">Nom</label><input type="text" id="name" name="name" value="{$ppo->name}" readonly="readonly" /><br />
<label style="width:70px;"  for="description">Description</label><input type="text" id="description" name="description" value="{$ppo->description}" /><br />
<label style="width:70px;"  for="version">Version</label><input type="text" id="version" name="version" value="{$ppo->version}" /><br />
<input type="submit" />
</form>