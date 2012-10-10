<h2>Nouveau rapport de bug</h2>
<script>
{literal}
   function copixBug(version,heading){
       this.version = version
       this.heading = heading
   }
   var headings = new Array();
{/literal}
   {foreach from=$ppo->headings item=head}
   headings.push(new copixBug('{$head->version_bughead}','{$head->heading_bughead}'));
   {/foreach}
{literal}   
	
	function setVersionForHeading(id){
		$('version').empty();
		for (i in headings){
			if(headings[i].heading==id){
				var option = new Element('option').setProperty('value',headings[i].version).set('html',headings[i].version);				
				option.injectInside('version');
			}
		}
   	}
{/literal}
</script>



<form method="POST" action="{copixurl dest="bugtrax|default|add"}">
<input type="hidden" name="author_bug" value="{$ppo->author}" />
<p>
Thème <select name="heading_bughead">
<option value="">----</option>
{foreach from=$ppo->list item=head}
<option onclick="javascript:setVersionForHeading(this.value)" value="{$head->heading_bughead}">{$head->heading_bughead}</option>
{/foreach}
</select>
 - version <select id="version" name="version_bughead"> 
 </select>
 <br />
 Severité : <select name="severity_bug">
 {foreach from=$ppo->severities item=sev}
 <option value="{$sev}">{$sev}</option>
 {/foreach}
 </select>
 </p>
 <p>
 Titre : <input name="name_bug" /><br />
 Description du problème:
 <br />
 <textarea rows="15" cols="50" name="description_bug">
 </textarea>
 </p>
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
</form>