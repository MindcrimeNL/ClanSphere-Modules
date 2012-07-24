<table cellspacing="1" cellpadding="1">
	{loop:actions}
	<tr>
		<td style="width: 50px; text-align: right;">{actions:time}</td>
		<td style="text-align: left;"><b>{actions:message}</b></td>
	</tr>
	{stop:actions}
</table>