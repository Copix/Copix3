<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Color Picker</title>
		
		{$HTML_HEAD}
		
		<script language="javascript">
			//<!--{literal}
			
			try {
				if($_GET['color'].length<1)
					delete $_GET['color'];
			} catch(e){}

			try {
				if($_GET['pre_color'].length<1)
					delete $_GET['pre_color'];

			} catch(e){}

			var cur_color    = new Color ($pick($_GET['color'],"#FF0000"));
			var pre_color    = new Color($pick($_GET['pre_color'],"#FF0000"));
			var holder_color = new Color($pick($_GET['color'],"#FF0000"));
			var hue_cord;
			var spectrum_slider;
			var isOK = false;
			
			window.addEvent('load', ini_picker);
			function ini_picker(){
				hue_cord = $('hue_color_area_container').getCoordinates();
				spectrum_slider = new Slider(
					$('spectrum_slider'),
					$('spectrum_arrows'),
					{
						steps:360,mode:'vertical',offset:3,onChange:function(a){
							cur_color = new Color([a,cur_color.hsb[1],cur_color.hsb[2]],"hsb");
							setColorPicker();
						},
						onTick:function(a){
							var b=new Fx.Morph(
								this.knob,
								{
									duration:250,
									transition:Fx.Transitions.Quart.easeOut
								}
							);
							b.start({
								'top':a
							});
						}
							
					}
				);
				
				var c = new Drag.Move(
					$('circle'),
					{
						'container':$('hue_color_area_container'),
						onDrag:function() {
							x=$('circle').getStyle("left").toInt()-hue_cord.left;
							y=$('circle').getStyle("top").toInt()-hue_cord.top;
							x=((x/(hue_cord.width-10))*100).toInt();
							y=((y/(hue_cord.height-10))*100).toInt();
							cur_color=new Color(
								[cur_color.hsb[0],
								checkNum(x),
								checkNum(100-y)],
								"hsb"
							);
							setColorPicker();
						}
					}
				);
				
				$('circle').addEvent ("click", function(e) {
					var e=new Event(e);
					e.stopPropagation();
				});
				
				$('des_button').addEvent("click",function(){
					cur_color=cur_color.desaturate();
					setColorPicker();moveSliders();
				});
				
				$('websafe_check').addEvent("click",function(){
					if($('websafe_check').checked){
						holder_color=cur_color;
						$('spectrum_slider').setStyle("backgroundImage","url({/literal}{copixresource path='moocolorpicker|img/colorpicker/side_slider_ws.jpg' }{literal})");
					} else {
						cur_color=holder_color;
						$('spectrum_slider').setStyle("backgroundImage","url({/literal}{copixresource path='moocolorpicker|img/colorpicker/side_slider.jpg' }{literal})");
					}
					setColorPicker();
					moveSliders();
				});
				
				$('invert_button').addEvent("click",function(){
					cur_color=cur_color.invert();
					setColorPicker();
					moveSliders();
				});
				
				$('previous_preview').addEvent("click",function(){
					cur_color=pre_color;
					setColorPicker();
					moveSliders();
				});
				var cliquer = false;
				var cliquerSpect = false;
				
				$('hue_color_area').addEvent("mousemove",function(e){
					if (cliquer) {
						var e=new Event(e);
						x=e.client.x-hue_cord.left-5;
						y=e.client.y-hue_cord.top-5;
						x=((x/(hue_cord.width-10))*100).toInt();
						y=((y/(hue_cord.height-10))*100).toInt();
						cur_color=new Color([cur_color.hsb[0],checkNum(x),checkNum(100-y)],"hsb");
						setColorPicker();
						var xx = e.client.x-5;
						var yy = e.client.y-5;
						
						if (xx > 270) {
							xx = 270;
						}
						if (yy > 280) {
							yy = 280;
						}
						if (xx < 15) {
							xx = 15;
						}
						if (yy < 25) {
							yy = 25;
						}
						$('circle').setStyles({'top':yy, 'left':xx});
					}
				});
				
				$('hue_color_area').addEvent("mousedown",function(e){
					cliquer = true;
					var e=new Event(e);
					x=e.client.x-hue_cord.left-5;
					y=e.client.y-hue_cord.top-5;
					x=((x/(hue_cord.width-10))*100).toInt();
					y=((y/(hue_cord.height-10))*100).toInt();
					cur_color=new Color([cur_color.hsb[0],checkNum(x),checkNum(100-y)],"hsb");
					setColorPicker();
					var a=new Fx.Morph($('circle'),{
						duration:250,
						transition:Fx.Transitions.Quart.easeOut
					});

					$(document.body).addEvent("mouseup",function(e){
						cliquer = false;
						cliquerSpect = false;
					});
					
					a.start({
						'top':e.client.y-5,
						'left':e.client.x-5
					});
				});
				
				$('h_selector_val').addEvent("keyup",function(){
					if(this.value!=""){
						var a=checkNum(this.value.toInt(),360);
						cur_color=new Color([a,cur_color.hsb[1],cur_color.hsb[2]],"hsb");
						setColorPicker();moveSliders();
					}
				});
					
				$('s_selector_val').addEvent("keyup",function(){
					if(this.value!=""){
						var a=checkNum(this.value.toInt());
						cur_color=new Color([cur_color.hsb[0],a,cur_color.hsb[2]],"hsb");
						setColorPicker();moveSliders();
					}
				});
				
				$('l_selector_val').addEvent("keyup",function(){
					if(this.value!=""){
						var a=checkNum(this.value.toInt());
						cur_color=new Color([cur_color.hsb[0],cur_color.hsb[1],a],"hsb");
						setColorPicker();moveSliders();
					}
				});
				
				$('r_selector_val').addEvent("keyup",function(){
					if(this.value!=""){
						var a=checkNum(this.value.toInt(),255);
						cur_color=new Color([a,cur_color[1],cur_color[2]],"rgb");
						setColorPicker();moveSliders();
					}
				});
				
				$('g_selector_val').addEvent("keyup",function(){
					if(this.value!=""){
						var a=checkNum(this.value.toInt(),255);
						cur_color=new Color([cur_color[0],a,cur_color[2]],"rgb");
						setColorPicker();moveSliders();
					}
				});
				
				$('b_selector_val').addEvent("keyup",function(){
					if(this.value!=""){
						var a=checkNum(this.value.toInt(),255);
						cur_color=new Color([cur_color[0],cur_color[1],a],"rgb");
						setColorPicker();moveSliders();
					}
				});
				
				$('hex_val').addEvent("keyup",function(){
					if(this.value.test(/^#[ABCDEFabcdef0123456789]{6}$/)){
						cur_color=new Color(this.value);
						setColorPicker();moveSliders();
					}
				});
				
				$('cancel_button').addEvent("click",function(){
					window.close();
				});
				
				$('ok_button').addEvent("click",function(){
					window.opener.$color_picker_object[$_GET['pickerObject']].fireEvent("onChange",cur_color.hex.toUpperCase());
					isOK = true;
					window.close();
					
				});
				
				setColorPicker(true);
				moveSliders();
			};
				
			function setColorPicker(init){
				if($('websafe_check').checked)
					cur_color=cur_color.webSafe();
				$('r_selector_val').value=cur_color[0];
				$('g_selector_val').value=cur_color[1];
				$('b_selector_val').value=cur_color[2];
				$('h_selector_val').value=cur_color.hsb[0];
				$('s_selector_val').value=cur_color.hsb[1];
				$('l_selector_val').value=cur_color.hsb[2];
				$('hex_val').value=cur_color.hex.toUpperCase();
				if(init)
					$('previous_preview').setStyle("background-color",cur_color);
				$('current_preview').setStyle("background-color",cur_color);
				$('hue_color_area').setStyle("background-color",new Color([cur_color.hsb[0],100,100],"hsb"));
				window.opener.$color_picker_object[$_GET['pickerObject']].fireEvent("onPreview",cur_color.hex.toUpperCase());
				return false;
			};

			function moveSliders(){
				spectrum_slider.set(cur_color.hsb[0]);
				var a=new Fx.Morph($('circle'),{
					duration:250,
					transition:Fx.Transitions.Quart.easeOut
				});
				
				a.start({
					'top':hue_cord.top+((hue_cord.height-10)-((cur_color.hsb[2]/100)*(hue_cord.height-10))),
					'left':hue_cord.left+((cur_color.hsb[1]/100)*(hue_cord.width-10))
				});
			};

			function checkNum(a,b,c){
				b=$pick(b,100);
				if(a>b)
					a=b;
				else if (a<0)
					a=0;
				return a;
			};
			
			$(window).addEvent ('unload', function () {
				if (!isOK)
					window.opener.$color_picker_object[$_GET['pickerObject']].fireEvent("onCancel",cur_color.hex.toUpperCase());
			});
			
			//-->{/literal}
		</script>
	</head>
		
	<body>
		<div id="np_cp">
			<table cellpadding="0" cellspacing="0" class="main_table">
				<tr>
					<td>
						Select a Color:
						<div id="hue_color_area_container">
							<div id="hue_color_area">
								<img src="{copixresource path='moocolorpicker|img/colorpicker/circle.gif'}" border="0" alt="" id="circle" />
							</div>
						</div>
					</td>
					
					<td>
						&nbsp;
						<div id="spectrum_slider">
							<img src="{copixresource path='moocolorpicker|img/colorpicker/arrows.gif'}" border="0" alt="" id="spectrum_arrows" />
						</div>
					</td>
					
					<td>
						<div class="side_info">
							<div class="color_preview">
								<div class="preview_channel" id="current_preview">
								</div>
								<div class="preview_channel" id="previous_preview">
								</div>
							</div>
							<table>
								<tr>
									<td>
										H:
									</td>
									<td>
										<input type="text" name="h_selector_val" id="h_selector_val" size="2" value="0" maxlength="3"/>
										&deg;
									</td>
								</tr>
								<tr>
									<td>
										S:
									</td>
									<td>
										<input type="text" name="s_selector_val" id="s_selector_val" size="2" value="100" maxlength="3" />
										%
									</td>
								</tr>
								<tr>
									<td>
										L:
									</td>
									<td>
										<input type="text" name="l_selector_val" id="l_selector_val" size="2" value="100" maxlength="3" />
										%
									</td>
								</tr>
								<tr>
									<td>
										R:
									</td>
									<td>
										<input type="text" name="r_selector_val" id="r_selector_val" size="2" value="255" maxlength="3" />
									</td>
								</tr>
								<tr>
									<td>
										G:
									</td>
									<td>
										<input type="text" name="g_selector_val" id="g_selector_val" size="2" value="0" maxlength="3" />
									</td>
								</tr>
								<tr>
									<td>
										B:
									</td>
									<td>
										<input type="text" name="b_selector_val" id="b_selector_val" size="2" value="0" maxlength="3" />
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<input type="text" name="hex_val" id="hex_val" size="6" value="#FF0000" maxlength="7" />
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" name="websafe_check" id="websafe_check" />
						<label for="websafe_check" id="websafe_txt">
							Only Websafe Colors
						</label>
						<br />
						<input type="button" id="invert_button" value="Invert Color" />
						&nbsp;&nbsp;
						<input type="button" id="des_button" value="Desaturate" />
					</td>
					<td colspan="2" align="right">
						<input type="button" value="OK" id="ok_button" class="button" />
						&nbsp;
						<input type="button" value="Cancel" id="cancel_button" class="button" />
						<a href="http://www.nogray.com" target="_blank" id="nogray_logo" title="NoGray.com Color Picker">
							&nbsp;
						</a>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>
								
								
								
								