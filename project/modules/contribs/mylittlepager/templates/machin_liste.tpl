<h2>Listing des machins :</h2>
<br />
<a href="{copixurl dest="mylittlepager|default|creermachin"}">Nouveau machin</a>

<p>
{$ppo->navigateur}
</p>

<table border="1">
 <tr>
   <th>ID</th>
   <th>Machin</th>
   <th>Action</th>
 </tr>
{foreach from=$ppo->donnees item=element}
 <tr>
   <td>{$element->id_machin}</td>
   <td>{$element->titre_machin}</td>
   <td>
    <a href="{copixurl dest="mylittlepager|default|supprimermachin" id=$element->id_machin}">Supprimer</a>
   </td>
 </tr>
{/foreach}
</table>

