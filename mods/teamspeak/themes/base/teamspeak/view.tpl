<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<tr>
		<td class="headb" colspan="6">{lang:mod} - {link:ts_connect}</td>
	</tr>
	<tr>
		<td class="centerb" colspan="6">{head:body}</td>
	</tr>
	<tr>
		<td class="leftb">{lang:sid}</td>
		<td class="leftb">{lang:sname}</td>
		<td class="leftb">{lang:smusers}</td>
		<td class="leftb">{lang:sprot}</td>
		<td class="leftb">{lang:schannels}</td>
		<td class="leftb">{lang:sclan}</td>
	</tr>
	<tr>
		<td class="leftb">{server:server_id}</td>
		<td class="leftb">{server:server_name}</td>
		<td class="leftb">{server:server_musers}</td>
		<td class="leftb">{server:server_prot}</td>
		<td class="leftb">{server:server_channels}</td>
		<td class="leftb">{server:server_clan}</td>
	</tr>
</table>
<br />

<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<tr>
		<td class="centerb" colspan="3">{lang:plist}</td>
	</tr>
{if:show_players}
	<tr>
		<td class="leftb" width="10%">{lang:pid}</td>
		<td class="leftb">{lang:pname}</td>
		<td class="leftb">{lang:ponline}</td>
	</tr>
	{loop:players}
	<tr>
		<td class="rightb">{players:pid}</td>
		<td class="leftb">{players:pname}</td>
		<td class="leftb">{players:ponline}</td>
	</tr>
	{stop:players}
{stop:show_players}
{if:show_players_empty}
	<tr>
		<td class="leftb" colspan="3">{lang:pempty}</td>
	</tr>
{stop:show_players_empty}
</table>
<br />

<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="1">
	<tr>
		<td class="centerb" colspan="2">{server:uptime}</td>
	</tr>
	<tr>
		<td class="leftb" width="50%">{server:name}<br />{server:output}</td>
		<td class="leftb" width="50%" valign="top" id="teamspeak_channelinfo">
		{lang:sname}<br />{server:name}<br><br>
		{lang:sip}<br />{server:server_address}<br><br>
		{lang:sversion}<br />{server:server_version}<br><br>
		{lang:splatform}<br />{server:server_platform}<br><br>
		</td>
	</tr>
</table>
<br />