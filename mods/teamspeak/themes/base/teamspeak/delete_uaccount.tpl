<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<tr>
		<td class="headb">{lang:mod} - {link:head_center}</td>
	</tr>
	<tr>
		<td class="leftc">{head:body}</td>
	</tr>
</table>
<br />
<form method="POST" id="teamspeak_user_delete" action="{url:teamspeak_delete_uaccount}" enctype="multipart/form-data">
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="leftc" colspan="2">{lang:myaccount}</td>
	</tr>
	<tr>
		<td class="leftc">{icon:yast_user_add} {lang:login_nick}</td>
		<td class="leftb">{teamspeak:user_nick}</td>
	</tr>
	<tr>
		<td class="leftc">{icon:ksysguard} {lang:options}</td>
		<td class="leftb"><input type="hidden" name="cldbid" value="{teamspeak:cldbid}"><input type="submit" name="submit" value="{lang:del}" /></td>
	</tr>
</table>
</form>
