<?php
class ZonePermission extends CopixZone{

	protected function _createContent (& $toReturn){
		$url = _url ('fileexplorer|default|permission');
		$txtok =  _i18n ('copix:common.buttons.ok');
		$lbUser = _i18n ('fileexplorer.user');
		$lbGroup = _i18n ('fileexplorer.group');
		$lbOther = _i18n ('fileexplorer.others');
		$lbOwner = _i18n ('fileexplorer.owner');
		$lbRecursive = _i18n ('fileexplorer.recursive');
		
		$file = $this->getParam('file');
		$perms = fileperms($file);
		$owner = false;
		$group = false;
		if(function_exists('posix_getpwuid')){
			$owner = posix_getpwuid(fileowner($file));
			$owner = $owner['name'];
		}
		if(function_exists('posix_getgrgid')){
			$group = posix_getgrgid(filegroup($file));
			$group = $group['name'];
		}
		
		$canEdit = ($owner == get_current_user()) || $owner === false;
		
		// define offset for groups
		$arOwnerPerms = array(2*3 => 'user', 1*3 => 'group', 0*3 => 'other');
		// define offset for rights
		$arTypePerms = array(0x0004 => 'r', 0x0002 => 'w', 0x0001 => 'x');
		$toReturn = <<<BEGIN
		<a name="permissions"></a>
		<form action="$url" method="POST" enctype="multipart/form-data">
			<input type="hidden" name="file" value="$file"/>
			<p>
				<label for="chmod">
					Permissions
				</label>
				<div>
					<table class="CopixVerticalTable">
						<thead>
							<tr>
BEGIN;
				if($owner){
					$toReturn .= "<th>$lbUser</th>";
				}
				if($group){
					$toReturn .= "<th>$lbGroup</th>";
				}
				
								
				// table header
				foreach ($arOwnerPerms as $offsetOwner => $labelOwner){
					$label = null;
					switch ($labelOwner){
						case 'user' : $label = $lbUser; break;
						case 'group' : $label = $lbGroup; break;
						case 'other' : $label = $lbOther; break;
					}
					$toReturn .='<th>'.$label.'</th>'; 
				}
				if($canEdit){
					if(is_dir($file)){
					$toReturn .='<th>'.$lbRecursive.'</th>';
					}
					$toReturn .='<th></th>';
				}
				$toReturn .='</tr><tbody><tr>';
				if($owner){
					$toReturn .='<td><input type="text" name="owner" value="'.$owner.'" '.(($canEdit) ? '':'disabled="disabled"' ).'/></td>';
				}
				if($group){
					$toReturn .='<td><input type="text" name="usergroup" value="'.$group.'" '.(($canEdit) ? '':'disabled="disabled"' ).'/></td>';
				}
				
				// table content
				foreach ($arOwnerPerms as $offsetOwner => $labelOwner){
					$toReturn .='<td>';
					// get permissiton for right
					$permToCheck = ($perms >> $offsetOwner);
					foreach ($arTypePerms as $offsetPerm => $labelPerm){
						$active = (($permToCheck & $offsetPerm) == $offsetPerm);  
						$toReturn .='<label for="'.$labelOwner.$labelPerm.'"><input type="checkbox" name="'.$labelOwner.$labelPerm.'" id="'.$labelOwner.$labelPerm.'" '.(($active) ? 'checked="checked"':'').' value ="1" '. (($canEdit) ? '':'disabled="disabled"' ).'/> '.$labelPerm.' </label>';
					}
					$toReturn .='</td>';
				}
				
				if($canEdit){
					if(is_dir($file)){
						$toReturn .='<td><input type="checkbox" name="recursive" value="1"/></td>';		
					}
					$toReturn .= '<td><input type="submit" value="'.$txtok.'" /></td>';
				}
				$toReturn .= <<<END
							
							</tr>
						</tbody>
					</table>
				</div>
			</p>
	</form>
END;
		return true;
	}
}