<br />
{if $ppo->nbTotal >0}
	<form action="{copixurl dest="export|export"}" method="POST" enctype="multipart/form-data">
		<table class="CopixTable">
			<thead>
				<tr>
					<th>
						{i18n key='csv.nomfichier'}
						{ if ($ppo->tri == 'nomfichier_csvfile') && ($ppo->sens == 'ASC') }
							{copixurl dest="export|list" tri='nomfichier_csvfile' sens='DESC' assign=nameUrl}
							{CopixIcon type="down" href=$nameUrl title="Trier par ordre décroissant"}
						{else}
							{copixurl dest="export|list" tri='nomfichier_csvfile' sens='ASC' assign=nameUrl}
							{CopixIcon type="up" href=$nameUrl title="Trier par ordre croissant"}
						{/if}
					</th>
					<th>
						{i18n key='csv.datehourcreation'}
						{ if ($ppo->tri == 'date_csvfile') && ($ppo->sens == 'ASC') }
							{copixurl dest="export|list" tri='date_csvfile' sens='DESC' assign=nameUrl}
							{CopixIcon type="down" href=$nameUrl title="Trier par ordre décroissant"}
						{else}
							{copixurl dest="export|list" tri='date_csvfile' sens='ASC' assign=nameUrl}
							{CopixIcon type="up" href=$nameUrl title="Trier par ordre croissant"}
						{/if}
					</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{assign var=date value=$ppo->tabDate}
				{assign var=heure value=$ppo->tabHeure}
				{foreach from=$ppo->csvFile item=unFichier}
					{assign var=id value=$unFichier->id_csvfile}
				    <tr {cycle values=",class='alternate'"}>
				      <td>{$unFichier->nomfichier_csvfile}</td>
				      <td>{$date.$id}&nbsp;&nbsp;{$heure.$id}</td>
				      <td>
				      	 {assign var=fileName value=$unFichier->nomfichier_csvfile}
				      	 {copixurl dest="export|download" nomfichier=$fileName assign=nameUrl}
						 {CopixIcon type="save" href=$nameUrl}
				      </td>
				      <td>
				      		{copixurl dest="export|delete" nomfichier=$fileName assign=nameUrl}
						    {CopixIcon type="delete" href=$nameUrl}
				      </td>
				{/foreach}
			</tbody>
		</table>	
		<br />
	</form>
	
	<div name="pagination" align="center">
		
		<!-- Nombre de page total -->
		{assign var=nbPage value=$ppo->nbPage}
		<!-- Tableau contenant le numéro du premier enregistrement a afficher pour chaque page -->
		{assign var=min value=$ppo->tabMin}
		<!-- Numéro de la page sur laquelle on se trouve actuellement -->
		{assign var=numPage value=$ppo->numPage}
		<!-- Numéro de la page suivante -->
		{assign var=numPageSuivante value=$numPage+1}
		<!-- Numéro de la page precedente -->
		{assign var=numPagePrecedente value=$numPage-1}
		<!-- Nom du champ sur lequel est effecuté le tri -->
		{assign var=tri value=$ppo->tri}
		<!-- Sens du tri (ASC ou DESC) -->
		{assign var=sens value=$ppo->sens}
		
		<!-- Flèche à gauche pour premier et précédent -->
		{if $numPage >1}
			<!-- Double flèche à gauche pour se positionner sur les premiers enregistrements -->
			{copixurl dest="export|list" numpage=1 min=0 tri=$tri sens=$sens assign=nameUrl}
			{copixicon type="first" href=$nameUrl}
			<!-- Flèche simple à gauche pour se positionner sur la page précédente 
			 On ne peut aller sur la page précédente que si l'on ne se trouve pas sur la première page -->
			 {assign var=minPagePrecedente value=$min.$numPagePrecedente}
			 {copixurl dest="export|list" numpage=$numPagePrecedente min=$minPagePrecedente tri=$tri sens=$sens assign=nameUrl}
			 {copixicon type="previous" href=$nameUrl}
		{else}
			{copixicon type="first"}|
			{copixicon type="previous"}
		{/if}
		
		<!-- Numéro de page -->
		{foreach from=$ppo->tabPage item=page}
			<!-- Le numéro de page qu'on affiche -->
			{assign var=page_a_afficher value=$page}
			<!-- Si le numéro de page que l'on va afficher est le meme que celui sur laquelle on se situe -->
			{if $numPage == $page_a_afficher}
				{$page}|
			{else}
				<a href="{copixurl dest="export|list" numpage=$page min=$min.$page tri=$tri sens=$sens}">{$page}</a>|
			{/if}
		{/foreach} 
		
		
		<!-- Flèches à droite pour suivant et dernier -->
		{if $numPage == $nbPage}
			{copixicon type="next"}|
			{copixicon type="last"}
		{else}
			{assign var=minPageSuivante value=$min.$numPageSuivante}
			<!-- Flèche simple à droite pour se positionner sur la page suivante -->
			{copixurl dest="export|list" numpage=$numPageSuivante min=$minPageSuivante tri=$tri sens=$sens assign=nameUrl}
			{copixicon type="next" href=$nameUrl}
			<!-- Double flèche à droite pour se positionner sur les derniers enregistrements -->
			{assign var=minDernierePage value=$min.$nbPage}
			{copixurl dest="export|list" numpage=$nbPage min=$minDernierePage tri=$tri sens=$sens assign=nameUrl}
			{copixicon type="last" href=$nameUrl}
		{/if}
		
	</div>
	<br />
{else}
	<h2>{i18n key="csv.export.list.nofile"}</h2>
	<br />
{/if}
<input type="button" value="{i18n key='csv.return'}" onclick="javascript:document.location.href='{copixurl dest="admin|default|"}'" />