<table class="forum" style="width:{page:width}">
  <tr>
    <td class="headb" colspan="2"> {head:mod}  -  {head:action} </td>
  </tr>
  <tr>
    <td class="headb" colspan="2"> <a href="{head:link_challenges}">{lang:challenges}</a>  - <a href="{head:link_players}">{lang:players}</a> </td>
  </tr>
</table><br />
<table class="forum" style="width:{page:width};">
  <tr>
    <td colspan="4" class="headb">{lang:challenge}: {head:challenge}</td>
   </tr>
  <tr>
    <td class="headb">{lang:rank}</td>
    <td class="headb">{lang:name}</td>
    <td class="headb">{lang:score}</td>
    <td class="headb">{lang:date}</td>
   </tr>
  {loop:xaseco}
  <tr>
    <td class="rightc">{xaseco:rank}</td>
    <td class="leftc"><a href="{xaseco:link}">{xaseco:name}</a></td>
    <td class="rightc">{xaseco:score}</td>
    <td class="rightc">{xaseco:date}</td>
  </tr>
  {stop:xaseco}
</table>
