<table class="CopixTable">
	<thead>
		<tr>
			<th><?php echo _i18n ('copix:Module'); ?></th>
			<?php
			foreach ($ppo->locales as $locale){
				echo "<th>$locale</th>";
			}
			?>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ($ppo->list as $module=>$locales){
		$class = '';
		$more = '';
		foreach ($locales->getDiff () as $locale=>$diffs){
			$count = count ($diffs);
			if ($count > 0){
				$more .= " $locale [$count] ";
				$class = ' style="background-color: #992222;" '; 
			}
		}

		echo "<tr "._tag ('cycle', array ('values'=>',class="alternate"'))." $class >";
		echo "<td><a href=\""._url ('i18n|file', array ('file'=>$module))."\" />$module</a>$more</td>";
		foreach ($ppo->locales as $locale){
			echo "<td width='20'>".(in_array ($locale, $locales->getLocales ()) ? 'X' : '&nbsp;' )."</td>";
		}
		echo "</tr>";
	}
	?>
	</tbody>
</table>