<table cellspacing="0" cellpadding="1">
	{loop:hero_details}
	<tr>
		<td style="text-align: left;"><img style="width: 14px; height: 14px;" src="{hero_details:detail_image}" alt="{hero_details:detail_name}" title="{hero_details:detail_name}" /></td>
		<td style="text-align: left;">{hero_details:detail_name}</td>
		<td style="text-align: left;">{hero_details:detail_level}</td>
	</tr>
	{stop:hero_details}
</table>