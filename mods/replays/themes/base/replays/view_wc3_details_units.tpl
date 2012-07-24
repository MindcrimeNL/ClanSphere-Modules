<table cellspacing="0" cellpadding="1">
	{loop:units}
	<tr>
		<td style="text-align: left;">{units:unit_name}</td>
		<td style="text-align: right;"><img style="width: 14px; height: 14px;" src="{units:unit_image}" alt="{units:unit_name}" title="{units:unit_name}" /></td>
		<td style="text-align: right;"><b>{units:unit_info}</b></td>
		<td><div style="height: 10px; width: {units:unit_length}px; background-color: #c0c0c0;""></div></td>
		<td><img style="width: 14px; height: 14px;" src="{unit:unit_goldimage}" /></td>
		<td align="right">{units:unit_gold}</td>
		<td><img style="width: 14px; height: 14px;" src="{unit:unit_woodimage}" /></td>
		<td align="right">{units:unit_wood}</td>
	</tr>
	{stop:units}
	<tr>
		<td colspan="8" height="3px"><hr></td>
	</tr>
	<tr>
		<td style="text-align: left;" colspan="2">Total</td>
		<td align="right"><b>{unit:total}</b></td>
		<td></td>
		<td><img style="width: 14px; height: 14px;" src="{unit:unit_goldimage}" /></td>
		<td align="right">{unit:total_gold}</td>
		<td><img style="width: 14px; height: 14px;" src="{unit:unit_woodimage}" /></td>
		<td align="right">{unit:total_wood}</td>
	</tr>
</table>