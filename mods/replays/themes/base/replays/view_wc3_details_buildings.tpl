<table cellspacing="0" cellpadding="1">
	{loop:buildings}
	<tr>
		<td style="text-align: left;">{buildings:building_name}</td>
		<td style="text-align: right;"><img style="width: 14px; height: 14px;" src="{buildings:building_image}" alt="{buildings:building_name}" title="{buildings:building_name}" /></td>
		<td style="text-align: right;"><b>{buildings:building_info}</b></td>
		<td><div style="height: 10px; width: {buildings:building_length}px; background-color: #c0c0c0;""></div></td>
		<td><img style="width: 14px; height: 14px;" src="{building:building_goldimage}" /></td>
		<td align="right">{buildings:building_gold}</td>
		<td><img style="width: 14px; height: 14px;" src="{building:building_woodimage}" /></td>
		<td align="right">{buildings:building_wood}</td>
	</tr>
	{stop:buildings}
	<tr>
		<td colspan="8" height="3px"><hr></td>
	</tr>
	<tr>
		<td style="text-align: left;" colspan="2">Total</td>
		<td align="right"><b>{building:total}</b></td>
		<td></td>
		<td><img style="width: 14px; height: 14px;" src="{building:building_goldimage}" /></td>
		<td align="right">{building:total_gold}</td>
		<td><img style="width: 14px; height: 14px;" src="{building:building_woodimage}" /></td>
		<td align="right">{building:total_wood}</td>
	</tr>
	<tr>
		<td colspan="8" height="3px"><b>{building:order}</b></td>
	</tr>
	{loop:buildingsorder}
	<tr>
		<td style="text-align: left;">{buildingsorder:building_name}</td>
		<td style="text-align: right;"><img style="width: 14px; height: 14px;" src="{buildingsorder:building_image}" alt="{buildingsorder:building_name}" title="{buildingsorder:building_name}" /></td>
		<td style="text-align: right;" colspan="6">{buildingsorder:building_time}</td>
	</tr>
	{stop:buildingsorder}
</table>