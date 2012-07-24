<table class="forum" style="width:{page:width}">
  <tr>
    <td class="headb" colspan="2"> {head:mod}  -  {head:action} </td>
  </tr>
  <tr>
    <td class="headb" colspan="2"> <a href="{head:link_challenges}">{lang:challenges}</a>  - <a href="{head:link_players}">{lang:players}</a> </td>
  </tr>
  <tr>
    <td class="leftb" > {lang:total}: {head:total}</td>
    <td class="rightb" > {head:pages} </td>
  </tr>
</table><br />
<table class="forum" style="width:{page:width};">
  <tr>
    <td class="headb">{lang:name}</td>
    <td class="headb">{lang:wins}</td>
    <td class="headb">{lang:time_played}</td>
   </tr>
  {loop:xaseco}
  <tr>
    <td class="leftc"><a href="{xaseco:link}">{xaseco:name}</a></td>
    <td class="rightc">{xaseco:wins}</td>
    <td class="rightc">{xaseco:time_played}</td>
  </tr>
  {stop:xaseco}
</table>
