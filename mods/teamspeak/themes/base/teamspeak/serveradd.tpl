<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="headb">{lang:mod_name} - {link:teamspeak_manage}</td>
	</tr>
	<tr>
		<td class="leftc">{head:body}</td>
	</tr>
</table>
<br />

<form method="POST" id="teamspeak_serveradd" action="{url:teamspeak_serveradd}" enctype="multipart/form-data">
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="leftc">{icon:ts1} {lang:serverip} *</td>
		<td class="leftb"><input type="text" name="teamspeak_ip" value="{teamspeak:teamspeak_ip}" maxlength="80" size="30" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:ts1} {lang:serverversion} *</td>
		<td class="leftb"><select name="teamspeak_version">
		<option value="0"{teamspeak:teamspeak_version_0}>{lang:version_0}</option>
		<option value="1"{teamspeak:teamspeak_version_1}>{lang:version_1}</option>
		</select></td>
	</tr>
	<tr>
		<td class="leftc">{icon:ts1} {lang:serverudp} *</td>
		<td class="leftb"><input type="text" name="teamspeak_udp" value="{teamspeak:teamspeak_udp}" maxlength="5" size="5" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:ts1} {lang:servertcp} *</td>
		<td class="leftb"><input type="text" name="teamspeak_tcp" value="{teamspeak:teamspeak_tcp}" maxlength="5" size="5" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:yast_user_add} {lang:admin} *</td>
		<td class="leftb"><input type="text" name="teamspeak_admin" value="{teamspeak:teamspeak_admin}" maxlength="25" size="25" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:password} {lang:adminpw} *</td>
		<td class="leftb"><input type="password" name="teamspeak_adminpw" value="{teamspeak:teamspeak_adminpw}" maxlength="25" size="25" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:yast_user_add} {lang:sadmin}</td>
		<td class="leftb"><input type="text" name="teamspeak_sadmin" value="{teamspeak:teamspeak_sadmin}" maxlength="25" size="25" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:password} {lang:sadminpw}</td>
		<td class="leftb"><input type="password" name="teamspeak_sadminpw" value="{teamspeak:teamspeak_sadminpw}" maxlength="25" size="25" /></td>
	</tr>
    <tr>
      <td class="leftc">{icon:kuser} {lang:allow_register} *</td>
      <td class="leftb">{select:teamspeak_register}<br><span class="fontsizesmall">{lang:allow_register_info}</span></td>
    </tr>
    <tr>
      <td class="leftc">{icon:access} {lang:access} *</td>
      <td class="leftb">{select:teamspeak_access}</td>
    </tr>
	<tr>
		<td class="leftc">{icon:locale} {lang:charset}</td>
		<td class="leftb"><select name="teamspeak_charset">
		<option value="ISO-8859-1"{teamspeak:teamspeak_charset_iso8859_1}>{lang:iso_8859_1}</option>
		<option value="UTF-8"{teamspeak:teamspeak_charset_utf_8}>{lang:utf_8}</option>
		</select><br><span class="fontsizesmall">{lang:charset_info}</span></td>
	</tr>
	<tr>
		<td class="leftc">{icon:ksysguard} {lang:options}</td>
		<td class="leftb"><input type="submit" name="submit" value="{lang:create}" /></td>
	</tr>
</table>
</form>
