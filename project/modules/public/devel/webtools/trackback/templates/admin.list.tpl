<h2>{i18n key="trackback.list.title"}</h2>
<p>
{if $ppo->frompage<>"Spam"}
<a href="{copixurl dest="trackback|admin|Spam"}">Voir les Spams</a>
<a href="{copixurl dest="trackback|admin|" all="true"}">Voir aussi les messages validés</a>
{else}
<a href="{copixurl dest="trackback|admin|"}">Page d'administration des trackbacks</a>
{/if}
</p>
<form action="{copixurl dest="trackback|admin|doAdminActions"}" method="POST">
<input type="hidden" name="frompage" value="{$ppo->frompage}" />
<table class="CopixTable">
<tbody>
	<tr>
		<th>Title</th>
		<th>Target</th>
		<th>from</th>
		<th>Validate</th>
		<th>Add to spam</th>
		<th>Niveau spam</th>		
		<th>Delete</th>		
	</tr>
{foreach from=$ppo->trackbacks item=tb}
	<tr>
		<td><a href="javascript:void(null);" class="tb_titles" onmouseout="javascript:hideTb('tb_{$tb->id_tb}');" onmouseover="javascript:showTb('tb_{$tb->id_tb}');">{$tb->title_tb}</a>			
			<div class="tb_excerpt" id="tb_{$tb->id_tb}">{$tb->excerpt_tb|nl2br}</div>
		</td>
		<td>{$tb->target_tb}</td>
		<td><a href="{$tb->url_tb}" onclick="javascript: window.open(this.href);return false;">{$tb->blogname_tb}</a></td>
		<td><input type="radio" name="validate_{$tb->id_tb}" value="1" {if $tb->valid_tb==1}CHECKED{/if} /> ok
		    <input type="radio" name="validate_{$tb->id_tb}" value="0" {if $tb->valid_tb==0}CHECKED{/if} /> no</td>
		<td><input type="hidden" name="lastspam_{$tb->id_tb}" value="{$tb->spam_tb}" />
			<input type="radio" name="spam_{$tb->id_tb}" value="1" {if $tb->spam_tb==1}CHECKED{/if} /> ok
		    <input type="radio" name="spam_{$tb->id_tb}" value="0" {if $tb->spam_tb<>1}CHECKED{/if} /> no</td>
		<td>
			{$tb->danger} %
		</td>
		<td>
			<input type="checkbox" name="todelete[]" value="{$tb->id_tb}" />
		</td>
	</tr>
{/foreach}
</tbody>
</table>
<input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
</form>

<script type="text/javascript">
{literal}
var tb_sliders = new Array();
window.addEvent('domready',function(){
	$$('.tb_excerpt').each(function (el,i){
		tb_sliders[i] = new Fx.Slide(el,{duration: 500, wait: false});
		tb_sliders[i].id = el.id;
		tb_sliders[i].hide();
	});
})

function showTb(tbid){
	for (i in tb_sliders){
		if (tb_sliders[i].id==tbid){
			tb_sliders[i].slideIn();
		}
	} 
}

function hideTb(tbid){
	for (i in tb_sliders){
		if (tb_sliders[i].id==tbid){
			tb_sliders[i].slideOut();
		}
	} 
}
{/literal}
</script>