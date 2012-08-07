<table cellspacing="1" cellpadding="1">
	{loop:units}
	<tr>
		<td style="text-align: right;">{units:time}</td>
		<td style="text-align: center;">{units:img}</td>
		<td style="text-align: left;"><b>{units:name}</b></td>
		<td style="text-align: right;">{units:count}</td>
		<td><div style="height: 10px; width: {units:count}px; background-color: #c0c0c0;""></div></td>
		<td><img style="width: 14px; height: 14px;" src="mods/replays/plugins/sc2/images/icons/minerals.png" /></td>
		<td align="right">{units:minerals}</td>
		<td><img style="width: 14px; height: 14px;" src="mods/replays/plugins/sc2/images/icons/gas.png" /></td>
		<td align="right">{units:gas}</td>
		<td><img style="width: 14px; height: 14px;" src="mods/replays/plugins/sc2/images/icons/supply_{unit:race}.png" /></td>
		<td align="right">{units:supply}</td>
	</tr>
	{stop:units}
</table>