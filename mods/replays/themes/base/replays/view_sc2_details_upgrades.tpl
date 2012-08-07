<table cellspacing="1" cellpadding="1">
	{loop:upgrades}
	<tr>
		<td style="text-align: right;">{upgrades:time}</td>
		<td style="text-align: center;">{upgrades:img}</td>
		<td style="text-align: left;"><b>{upgrades:name}</b></td>
		<td style="text-align: right;">{upgrades:count}</td>
		<td><div style="height: 10px; width: {upgrades:count}px; background-color: #c0c0c0;""></div></td>
		<td><img style="width: 14px; height: 14px;" src="mods/replays/plugins/sc2/images/icons/minerals.png" /></td>
		<td align="right">{upgrades:minerals}</td>
		<td><img style="width: 14px; height: 14px;" src="mods/replays/plugins/sc2/images/icons/gas.png" /></td>
		<td align="right">{upgrades:gas}</td>
	</tr>
	{stop:upgrades}
</table>