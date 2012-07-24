<table>
{loop:items}
<tr>
<td><img width="18" height="18" src="{items:item_image}" alt="{items:item_name}" name="{items:item_name}"></td>
<td align="right">{items:item_time}</td>
<td>{items:item_name}</td>
</tr>
{stop:items}
</table>
