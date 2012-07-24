<table cellspacing="1" cellpadding="1">
	{loop:buildings}
	<tr>
		<td style="text-align: right;">{buildings:time}</td>
		<td style="text-align: center;">{buildings:img}</td>
		<td style="text-align: left;"><b>{buildings:name}</b></td>
		<td style="text-align: right;">{buildings:count}</td>
		<td><div style="height: 10px; width: {upgrades:count}px; background-color: #c0c0c0;""></div></td>
		<td><img style="width: 14px; height: 14px;" src="mods/replays/plugins/sc2/images/icons/minerals.png" /></td>
		<td align="right">{buildings:minerals}</td>
		<td><img style="width: 14px; height: 14px;" src="mods/replays/plugins/sc2/images/icons/gas.png" /></td>
		<td align="right">{buildings:gas}</td>
	</tr>
	{stop:buildings}
</table>