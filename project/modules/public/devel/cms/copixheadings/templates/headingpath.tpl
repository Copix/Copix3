<ul>
  <li style="float: left"><a href="">{$root}</a></li>
  {foreach from=$path item=dir}
  {if $dir->caption_head neq $root}
     &nbsp;&gt;&nbsp;<li style="float: left">&nbsp;{if $dir->profileInformation >= PROFILE_CCV_SHOW}<a href="{copixurl dest="|show" id_head=$dir->id_head}">{/if}{$dir->caption_head}{if $dir->profileInformation >= PROFILE_CCV_SHOW}</a>{/if}</li>
  {/if}{/foreach}
</ul>