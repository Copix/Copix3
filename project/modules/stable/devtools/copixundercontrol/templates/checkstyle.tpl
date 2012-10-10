<h2>Checkstyle</h2>

<h3 class="build_info span-18 prepend-6">
	<div class="span-3">Files :</div><div class="span-5"><em>NN</em></div>
	<div class="span-3"><span class="span_error">Errors :</span></div><div class="span-2"><em>NN</em></div>
	<div class="span-3"><span class="span_warning">Warnings :</span></div><div class="span-2 last"><em>NN</em></div>
</h3>
<br/>
<hr/>

<div id="log_result">
	{foreach from=$ppo->result item=file}
		<h3 class="file_error">
			<div class="span-18">{$file->name}</div>
			<div class="span-3 info">Errors : {$file->errors}</div>
			<div class="span-3 info last">Warnings : {$file->warnings}</div>
			<div class="span-24 path">{$file->path}</div>
			<div class="clear"/>
		</h3>
		<div class="prepend-1 span-23 log_line info">
			<div class="span-7">Rule</div>
			<div class="span-1 info">Line</div>
			<div class="span-1 info">Col.</div>
			<div class="span-14 last info">Description</div>
		</div>
		{foreach from=$file->arErrors item=error}
			<div class="prepend-1 span-23 {cycle values='oddrow,'} log_line">
				<div class="span-7"><span class="span_error">{$error->source}</span></div>
				<div class="span-1 info">{$error->line}</div>
				<div class="span-1 info">{$error->column}</div>
				<div class="span-14 last info">{$error->string}</div>
			</div>
		{/foreach}
		<hr/>
	{/foreach}
</div>
