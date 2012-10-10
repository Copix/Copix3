<table class="CopixVerticalTable">
	<tr>
		<th style="width: 60px">Temps</th>
		<td id="time"><?php echo $ppo->time ?> sec</td>
	</tr>
	<tr class="alternate">
		<th>Retour</th>
		<td id="result">
		<?php 
			if(is_object($ppo->result)){
				echo CopixDebug::getDump($ppo->result);
			}else{
				echo htmlentities ($ppo->result);
			} 
		?>
		</td>
	</tr>
</table>