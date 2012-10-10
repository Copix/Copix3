<MARQUEE DIRECTION="up" scrollamount="2" onmouseover="this.stop();" onmouseout="this.start();" SCROLLDELAY="5">
	{foreach from=$elementsTemplate item=template key=elementIndex}
		{$template}
	{/foreach}
</MARQUEE>