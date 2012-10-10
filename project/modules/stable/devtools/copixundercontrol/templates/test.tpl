<h2>Rapport des tests unitaires</h2>


<div id="log_result">
	{foreach from=$ppo->result item=moduleSuite}
		<h3>
			<div class="span-13">{$moduleSuite->name}</div>
			<div class="span-8 info">Infos</div>
			<div class="span-3 info last">Time</div>
			<div class="clear"/>
		</h3>
		{foreach from=$moduleSuite->testSuite item=classSuite}
			<div class="prepend-1 span-23">
				<h4>
					<div class="span-12">{$classSuite->name}</div>
					<div class="span-2 info">Tests : {$classSuite->tests}</div>
					<div class="span-2 info">Assert : {$classSuite->assertions}</div>
					<div class="span-2 info">Failures : {$classSuite->failures}</div>
					<div class="span-2 info">Errors : {$classSuite->errors}</div>
					<div class="span-3 last">{$classSuite->time}</div>
					<div class="clear"/>
				</h4>
				{foreach from=$classSuite->testCase item=testCase}
					<div class="{if $testCase->failure || $testCase->error} test_error {else} test_success {/if} prepend-1 span-22 last">
						<div class="span-17">{$testCase->name}</div>
						<div class="span-2 info">Assert : {$testCase->assertions}</div>
						<div class="span-3 last">{$testCase->time}</div>
						{if $testCase->failure || $testCase->error}
							{foreach from=$testCase->failure item=failure}
								<div class="prepend-1 span-1">|--------</div>
								<div class="span-20 last">{$failure}</div>
							{/foreach}
							{foreach from=$testCase->error item=error}
								<div class="prepend-1 span-1">|--------</div>
								<div class="span-20 last">{$error}</div>
							{/foreach}
						{/if}
					</div>
				{/foreach}
			</div>

		{/foreach}
	{/foreach}
</div>