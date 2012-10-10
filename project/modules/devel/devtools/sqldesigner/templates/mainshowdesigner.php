<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--
	WWW SQL Designer, (C) 2005-2007 Ondra Zara, o.z.fw@seznam.cz
	Version: 2.1.1
	See gpl.txt for licencing information.
-->
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
	<title>WWW SQL Designer</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?php echo _resource ('sqldesigner|styles/style.css'); ?>" type="text/css" />
	<script type="text/javascript" src="<?php echo _resource ('sqldesigner|js/oz.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo _resource ('sqldesigner|js/config.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo _resource ('sqldesigner|js/wwwsqldesigner.js'); ?>"></script>
	<?php echo $HTML_HEAD; ?>
</head>
<body>
	<div id="area"></div>
	<div id="bar">
		<div class="shadow-left"></div>
		<div class="shadow-corner"></div>
		<div class="shadow-bottom"></div>
		
		<input type="button" id="saveload" />

		<hr/>

		<input type="button" id="addtable" />
		<input type="button" id="edittable" />
		<input type="button" id="tablekeys" />
		<input type="button" id="removetable" />
		<input type="button" id="aligntables" />
		<input type="button" id="cleartables" />
		
		<hr/>
		
		<input type="button" id="addrow" />
		<input type="button" id="editrow" />
		<input type="button" id="uprow" class="small" /><input type="button" id="downrow" class="small"/>
		<input type="button" id="foreigncreate" />
		<input type="button" id="removerow" />
		
		<hr/>
		
		<input type="button" id="options" />
		<a href="doc/" target="_blank"><input type="button" id="docs" value="" /></a>
	</div>
	
	<div id="minimap"></div>
	
	<div id="background"></div>
	
	<div id="window">
		<div id="windowtitle"><img id="throbber" src="<?php echo _resource ('img/tools/load.gif'); ?>" alt="" title=""/></div>
		<div id="windowcontent"></div>
	</div>
	
	<div id="opts">
		<table>
			<tbody>
				<tr>
					<td>
						* <label id="language" for="optionlocale"></label>
					</td>
					<td>
						<select id="optionlocale"></select>
					</td>
				</tr>
				<tr>
					<td>
						* <label id="db" for="optiondb"></label> 
					</td>
					<td>
						<select id="optiondb"></select>
					</td>
				</tr>
				<tr>
					<td>
						<label id="snap" for="optionsnap"></label> 
					</td>
					<td>
						<input type="text" size="4" id="optionsnap" />
						<span class="small" id="optionsnapnotice"></span>
					</td>
				</tr>
				<tr>
					<td>
						<label id="pattern" for="optionpattern"></label> 
					</td>
					<td>
						<input type="text" size="6" id="optionpattern" />
						<span class="small" id="optionpatternnotice"></span>
					</td>
				</tr>
				<tr>
					<td>
						<label id="hide" for="optionhide"></label> 
					</td>
					<td>
						<input type="checkbox" id="optionhide" />
					</td>
				</tr>
				<tr>
					<td>
						* <label id="vector" for="optionvector"></label> 
					</td>
					<td>
						<input type="checkbox" id="optionvector" />
					</td>
				</tr>
			</tbody>
		</table>

		<hr />

		* <span class="small" id="optionsnotice"></span>
	</div>
	
	<div id="io">
		<table>
			<tr>
				<td>Module :</td>
				<td>
					<select id="savemodule">
						<?php
						foreach (CopixModule::getList (false) as $module) {
							echo '<option value="' . $module . '">' . $module . '</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Nom du fichier :</td>
				<td><input type="text" id="savefile" value="test" /></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center">
					<br />
					<input type="button" id="clientsavecancel" />
					<input type="button" id="clientsave" />
				</td>
			</tr>
		</table>
	</div>
	
	<div id="keys">
		<fieldset>
			<legend id="keyslistlabel"></legend> 
			<select id="keyslist"></select>
			<input type="button" id="keyadd" />
			<input type="button" id="keyremove" />
		</fieldset>
		<fieldset>
			<legend id="keyedit"></legend>
			<table>
				<tbody>
					<tr>
						<td>
							<label for="keytype" id="keytypelabel"></label>
							<select id="keytype"></select>
						</td>
						<td></td>
						<td>
							<label for="keyname" id="keynamelabel"></label>
							<input type="text" id="keyname" size="10" />
						</td>
					</tr>
					<tr>
						<td colspan="3"><hr/></td>
					</tr>
					<tr>
						<td>
							<label for="keyfields" id="keyfieldslabel"></label><br/>
							<select id="keyfields" size="5" multiple="multiple"></select>
						</td>
						<td>
							<input type="button" id="keyleft" value="&lt;&lt;" /><br/>
							<input type="button" id="keyright" value="&gt;&gt;" /><br/>
						</td>
						<td>
							<label for="keyavail" id="keyavaillabel"></label><br/>
							<select id="keyavail" size="5" multiple="multiple"></select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	
	<div id="table">
		<table>
			<tbody>
				<tr>
					<td>
						<label id="tablenamelabel" for="tablename"></label>
					</td>
					<td>
						<input id="tablename" type="text" />
					</td>
				</tr>
				<tr>
					<td>
						<label id="tablecommentlabel" for="tablecomment"></label> 
					</td>
					<td>
						<textarea rows="5" cols="40" id="tablecomment"></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

<div style="display: none">
<fieldset>
	<legend id="output"></legend>
</fieldset>
<fieldset>
							<legend id="server"></legend>
							<label for="backend" id="backendlabel"></label> <select id="backend"></select>
							<hr/>
							<input type="button" id="serversave" /> 
							<input type="button" id="serverload" /> 
							<input type="button" id="serverlist" /> 
							<input type="button" id="serverimport" /> 
						</fieldset>
<textarea id="textarea" style="display: none"></textarea>

<fieldset>
							<legend id="client"></legend>

							</select>
							 
							<input type="button" id="clientload" />
							<hr/>
							<input type="button" id="clientsql" />
						</fieldset>
						
						<input type="button" id="windowok" />
		<input type="button" id="windowcancel" />
</div>

</body>
</html>