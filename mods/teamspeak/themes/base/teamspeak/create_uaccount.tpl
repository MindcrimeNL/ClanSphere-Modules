<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<tr>
		<td class="headb">{lang:mod} - {link:head_center}</td>
	</tr>
	<tr>
		<td class="leftc">{head:body}</td>
	</tr>
</table>
<br />
<form method="POST" id="teamspeakuser_create" action="{url:teamspeak_create_uaccount}" enctype="multipart/form-data">
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="leftc">{icon:yast_user_add} {lang:login_nick}</td>
		<td class="leftb">{teamspeak:user_nick}</td>
	</tr>
	{unless:ts3}
	<tr>
		<td class="leftc">{icon:password} {lang:login_pw} *</td>
		<td class="leftb"><input type="password" name="login_pw" value="{teamspeak:login_pw}" maxlength="8" size="8" /></td>
	</tr>
	{stop:ts3}
	<tr>
		<td class="leftc">{icon:ksysguard} {lang:options}</td>
		<td class="leftb"><input type="submit" name="submit" value="{lang:create}" /></td>
	</tr>
</table>
</form>