<h2>Code Duplication</h2>

<div id="log_result">
	{foreach from=$ppo->result->duplications item=duplication}
		<h3>Lines : {$duplication->lines} - Tokens : {$duplication->tokens}</h3>
		{foreach from=$duplication->files item=file}
		<h4 class="file_error">
			<div class="span-20">{$file->name}</div>
			<div class="span-3 last info">Line : {$file->line}</div>
			<div class="span-24 path">{$file->path}</div>
			<div class="clear"/>
		</h4>
		{/foreach}
		<div class="code">
			{$duplication->codefragment}
		</div>
	{/foreach}
</div>

<h2>Possible Mess Detection</h2>

<div id="log_result">
	<h3>Violation global</h3>
	{foreach from=$ppo->result->violation item=violation}
		<h4 class="file_error">
			<span class="span_error">{$violation->rule} - {$violation->package}</span><br/><br/>
			<div class="span-24">{$violation->string}</div>
			<div class="clear"/><br/>
		</h4>
	{/foreach}

	<h3>Violation global</h3>
	{foreach from=$ppo->result->pmd item=file}
		<h3 class="file_error">
			<div class="span-18">{$file->name}</div>
			<div class="span-24 path">{$file->path}</div>
			<div class="clear"/>
		</h3>
		
		{foreach from=$file->arViolations item=violation}
			<div class="prepend-1 span-23 {cycle values='oddrow,'} log_line">
				<div class="span-7"><span class="span_error">{$violation->rule}</span></div>
				<div class="span-2 info">line : {$violation->line}</div>
				<div class="span-2 info">to : {$violation->toline}</div>
				<div class="span-6 info">{$violation->class}</div>
				<div class="span-6 last info">{$violation->method}</div>
				<hr/>
				<div class="span-23 last">{$violation->string}</div>
			</div>
		{/foreach}
	{/foreach}
</div>