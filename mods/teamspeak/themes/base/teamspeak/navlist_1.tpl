<table cellpadding="0" cellspacing="0">
	<tr>
		<td class="rightb">
			{lang:serverip} {teamspeak_info:serverip}<br />
			{lang:online} {teamspeak_info:actuser} / {teamspeak_info:maxuser}<br />
			<hr />
		</td>
	</tr>
	{loop:teamspeak}
	<tr>
		<td class="leftb">{teamspeak:p_img} {teamspeak:player}</td>
	</tr>
	{stop:teamspeak}
</table>