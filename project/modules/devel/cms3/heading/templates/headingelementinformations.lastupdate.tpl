<ul>
{foreach from=$ppo->arLastUpdated item=lastUpdated}
   <li {cycle values=',class="alternate"'}>
       <a href="{copixurl dest="heading||" public_id=$lastUpdated->public_id_hei}"><img src="{copixresource path="img/tools/show.png"}" /></a>
       <a href="{copixurl dest="heading|element|" heading=$lastUpdated->parent_heading_public_id_hei}"><img src="{copixresource path="img/tools/browse.png"}" /></a>
       {$lastUpdated->caption_hei} [V{$lastUpdated->version_hei}] par {$lastUpdated->author_caption_update_hei} le {$lastUpdated->date_update_hei|datetimei18n}</li>
{/foreach}
</ul>