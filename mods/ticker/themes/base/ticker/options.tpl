<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="headb">{lang:mod_name} - {lang:options}</td>
	</tr>
	<tr>
		<td class="leftb">{lang:options_text}</td>
	</tr>
</table>
<br />

<form method="post" id="ticker_options" action="{url:ticker_options}">
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="leftc">{icon:editcut} {lang:separator}</td>
		<td class="leftb"><input type="text" name="separator" value="{op:separator}" maxlength="80" size="15" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:stop} {lang:stop_mo}</td>
		<td class="leftb">{lang:yes} <input type="radio" name="stop_mo" value="1"{op:stopmo_yes}> / <input type="radio" name="stop_mo" value="0"{op:stopmo_no}> {lang:no} </td>
	</tr>
	<tr>
		<td class="leftc">{icon:contents} {lang:max_news}</td>
		<td class="leftb"><input type="text" name="max_news" value="{op:max_news}" maxlength="2" size="3" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:personal} {lang:max_user}</td>
		<td class="leftb"><input type="text" name="max_user" value="{op:max_user}" maxlength="2" size="3" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:download} {lang:max_dls}</td>
		<td class="leftb"><input type="text" name="max_dls" value="{op:max_dls}" maxlength="2" size="3" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:wp} {lang:max_online}</td>
		<td class="leftb"><input type="text" name="max_online" value="{op:max_online}" maxlength="2" size="3" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:tutorials} {lang:max_threads}</td>
		<td class="leftb"><input type="text" name="max_threads" value="{op:max_threads}" maxlength="2" size="3" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:log} {lang:max_wars}</td>
		<td class="leftb"><input type="text" name="max_wars" value="{op:max_wars}" maxlength="2" size="3" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:ksysguard} {lang:options}</td>
		<td class="leftb"><input type="submit" name="submit" value="{lang:edit}" /></td>
    </tr>
</table>
</form>
