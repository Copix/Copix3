<form action="<?php echo _url ('i18n|save', array ('file'=>$ppo->file)); ?>" method="POST">
<table class="CopixTable">
<thead>
 <tr>
  <th>Clef</th>
  <?php
     foreach ($ppo->locales as $locale){
     	echo "<th>$locale</th>";
     }
  ?>
 </tr>
</thead>
<tbody>
 <tr>
  <td width="200"><input type="text" value="" name="new['key']" /></td>
  <?php
 	foreach ($ppo->locales as $locale){
     	echo '<td><textarea cols="50" rows="2" name="new['.$locale.']"></textarea></td>';
    } 	
  ?>
 </tr>
 <?php
 foreach ($ppo->keys as $key){
 	echo "<tr "._tag ('cycle', array ('values'=>',class="alternate"')).(_checkKey ($key, $ppo->translations, $ppo->locales) ? '' : 'style="background-color: #dd3333"').">
 	  <td>$key</td>";
 	foreach ($ppo->locales as $locale){
     	echo '<td><textarea cols="50" rows="2" name="translations['.$locale.']['.$key.']">'.(isset ($ppo->translations[$locale][$key]) ? _copix_utf8_htmlentities ($ppo->translations[$locale][$key]) : '').'</textarea></td>';
    } 	
 	echo "</tr>";
 }
 ?>
</tbody>
</table>

<input type="submit" value="<?php echo _i18n ('copix:Save'); ?>" />
<input type="button" value="<?php echo _i18n ('copix:Cancel'); ?>" />

</form>
<?php
function _checkKey ($key, $arLocales, $arCheck){
	foreach ($arCheck as $langToCheck){
		if (empty ($arLocales[$langToCheck][$key])){
			return false;
		}
	}
	return true;	
}
?>