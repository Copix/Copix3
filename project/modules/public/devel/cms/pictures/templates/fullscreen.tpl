<h2>{$picture->name_pict}</h2>
{if $picture->url_pict}
 <img src="{$picture->url_pict}" />
{else}
 <img src="{copixurl dest="pictures||get" id_pict=$picture->id_pict}" />
{/if}

<br />

{if $moderateEnabled}
<a href="{copixurl dest="pictures|admin|deletePicture" id_pict=$picture->id_pict id_head=$picture->id_head}"><img src="{copixresource path="img/tools/delete.gif"}" alt="{i18n key=copix:common.buttons.delete}" title="{i18n key=copix:common.buttons.delete}" border="0" /></a>&nbsp;
<a href="{copixurl dest="pictures|admin|prepareEditPicture" id_pict=$picture->id_pict id_head=$picture->id_head}"><img src="{copixresource path="img/tools/update.png"}" alt="{i18n key=copix:common.buttons.update}" title="{i18n key=copix:common.buttons.update}" border="0" /></a>
<br />
{/if}
<a href="{copixurl dest="pictures|browser|" id_head=$id_head}">{i18n key="copix:common.buttons.back"}</a>