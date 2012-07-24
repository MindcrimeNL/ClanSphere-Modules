<table class="forum" style="width:{page:width}">
  <tr>
    <td class="headb" colspan="2"> {head:mod}  -  {head:action} </td>
  </tr>
  <tr>
    <td class="headb" colspan="2"> <a href="{head:link_challenges}">{lang:challenges}</a>  - <a href="{head:link_players}">{lang:players}</a> </td>
  </tr>
</table><br />
<table class="forum" style="width:{page:width}">
  <tr>
    <td class="headb">{lang:challenge}</td>
    <td class="headb">{lang:author}</td>
    <td class="headb">{lang:environment}</td>
   </tr>
  {loop:xaseco}
  <tr>
    <td class="leftc"><a href="{xaseco:link}">{xaseco:name}</a></td>
    <td class="leftc">{xaseco:author}</td>
    <td class="leftc">{xaseco:environment}</td>
  </tr>
  {stop:xaseco}
</table>
