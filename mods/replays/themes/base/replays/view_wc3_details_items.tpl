<table cellspacing="0" cellpadding="1">
	{loop:items}
	<tr>
		<td style="text-align: left;">{items:item_name}</td>
		<td style="text-align: right;"><img style="width: 14px; height: 14px;" src="{items:item_image}" alt="{items:item_name}" title="{items:item_name}" /></td>
		<td style="text-align: right;"><b>{items:item_info}</b></td>
		<td><div style="height: 10px; width: {items:item_length}px; background-color: #c0c0c0;""></div></td>
		<td><img style="width: 14px; height: 14px;" src="{item:item_goldimage}" /></td>
		<td align="right">{items:item_gold}</td>
		<td><img style="width: 14px; height: 14px;" src="{item:item_woodimage}" /></td>
		<td align="right">{items:item_wood}</td>
	</tr>
	{stop:items}
	<tr>
		<td colspan="8" height="3px"><hr></td>
	</tr>
	<tr>
		<td style="text-align: left;" colspan="2">Total</td>
		<td align="right"><b>{item:total}</b></td>
		<td></td>
		<td><img style="width: 14px; height: 14px;" src="{item:item_goldimage}" /></td>
		<td align="right">{item:total_gold}</td>
		<td><img style="width: 14px; height: 14px;" src="{item:item_woodimage}" /></td>
		<td align="right">{item:total_wood}</td>
	</tr>
</table>