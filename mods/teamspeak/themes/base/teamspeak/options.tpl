<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<tr>
		<td class="headb">{lang:mod} - {link:teamspeak_manage}</td>
	</tr>
	<tr>
		<td class="leftb">{lang:option}</td>
	</tr>
</table>
<br />
{if:view_1}
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<tr>
		<td class="headb" colspan="2">{lang:navliste}</td>
	</tr>
	<form method="post" name="options" action="?mod=teamspeak&amp;action=options">
	<tr>
		<td class="leftb" style="width: 200px;">{icon:groupevent} {lang:uflags}</td>
		<td class="leftc">
			<select name="player_flags" class="form">
		    <option value="0" {options:player_flags_0}>{lang:no}</option>
			<option value="1" {options:player_flags_1}>{lang:yes}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="leftb" style="width: 200px;">{icon:gohome} {lang:cflags}</td>
		<td class="leftc">
			<select name="channel_flags" class="form">
		    <option value="0" {options:channel_flags_0}>{lang:no}</option>
			<option value="1" {options:channel_flags_1}>{lang:yes}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="leftb" style="width: 200px;">{icon:groupevent} {lang:show_empty}</td>
		<td class="leftc">{lang:yes} <input type="radio" name="show_empty" value="1" {options:show_empty_yes}> /
			<input type="radio" name="show_empty" value="0" {options:show_empty_no}> {lang:no}</td>
	</tr>
	<tr>
		<td class="leftb" style="width: 200px;">{icon:groupevent} {lang:show_empty_navlist}</td>
		<td class="leftc">{lang:yes} <input type="radio" name="show_empty_navlist" value="1" {options:show_empty_navlist_yes}> /
			<input type="radio" name="show_empty_navlist" value="0" {options:show_empty_navlist_no}> {lang:no}</td>
	</tr>
	<tr>
		<td class="leftb">{icon:kedit} {lang:timeout}</td>
		<td class="leftc"><input type="text" name="timeout" value="{options:timeout}" maxlength="3" size="3" /></td>
	</tr>
	<tr>
		<td class="leftb">{icon:ksysguard} {lang:options}</td>
		<td class="leftc">
			<input type="submit" name="submit" value="{lang:create}" class="form" />
			<input type="reset" name="reset" value="{lang:reset}" class="form "/>
		</td>
	</tr>
	</form>
</table>
{stop:view_1}
{if:view_2}
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<tr>
		<td class="centerb">{lang:navok}</td>
	</tr>
	<tr>
		<td class="centerb"><a href="?mod=teamspeak&amp;action=manage">{lang:continue}</a></td>
	</tr>
</table>
{stop:view_2}