<table cellspacing="0" cellpadding="1">
	{loop:upgrades}
	<tr>
		<td style="text-align: left;">{upgrades:upgrade_name}</td>
		<td style="text-align: right;"><img style="width: 14px; height: 14px;" src="{upgrades:upgrade_image}" alt="{upgrades:upgrade_name}" title="{upgrades:upgrade_name}" /></td>
		<td style="text-align: right;"><b>{upgrades:upgrade_info}</b></td>
		<td><div style="height: 10px; width: {upgrades:upgrade_length}px; background-color: #c0c0c0;""></div></td>
		<td><img style="width: 14px; height: 14px;" src="{upgrade:upgrade_goldimage}" /></td>
		<td align="right">{upgrades:upgrade_gold}</td>
		<td><img style="width: 14px; height: 14px;" src="{upgrade:upgrade_woodimage}" /></td>
		<td align="right">{upgrades:upgrade_wood}</td>
	</tr>
	{stop:upgrades}
	<tr>
		<td colspan="8" height="3px"><hr></td>
	</tr>
	<tr>
		<td style="text-align: left;" colspan="2">Total</td>
		<td align="right"><b>{upgrade:total}</b></td>
		<td></td>
		<td><img style="width: 14px; height: 14px;" src="{upgrade:upgrade_goldimage}" /></td>
		<td align="right">{upgrade:total_gold}</td>
		<td><img style="width: 14px; height: 14px;" src="{upgrade:upgrade_woodimage}" /></td>
		<td align="right">{upgrade:total_wood}</td>
	</tr>
</table>