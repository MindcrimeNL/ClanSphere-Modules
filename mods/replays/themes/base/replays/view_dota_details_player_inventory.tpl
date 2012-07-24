<table>
{loop:inventory}
<tr>
<td><img width="18" height="18" src="{inventory:inventory_image}" alt="{inventory:inventory_name}" name="{inventory:inventory_name}"></td>
<td>{inventory:inventory_name}</td>
</tr>
{stop:inventory}
</table>