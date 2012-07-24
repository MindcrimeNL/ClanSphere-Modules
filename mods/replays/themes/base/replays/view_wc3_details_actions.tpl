<table cellspacing="0" cellpadding="1">
	{loop:actions}
	<tr>
		<td style="text-align: left;">{actions:action_name}</td>
		<td style="text-align: right;"><b>{actions:action_info}</b></td>
		<td><div style="height: 10px; width: {actions:action_length}px; background-color: #c0c0c0;"></div></td>
	</tr>
	{stop:actions}
</table>