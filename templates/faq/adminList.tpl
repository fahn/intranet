<h1>FAQ List</h1>
<table class="table">
  <thead>
    <tr>
      <th scope="col">Titel</th>
      <th scope="col">Kategorie</th>
      <th scope="col">Option</th>
    </tr>
  </thead>
  <tbody>
      {foreach item=item from=$FaqList}
    <tr>
      <th scope="row">{$item.title}</th>
      <th scope="row">{$item.categoryTitle}</th>
      <td><a href="{$item.editLink}">editieren</a> <a href="{$item.deleteLink}">Löschen</a></td>
    </tr>
    {/foreach}
  </tbody>
</table>



<h1>Kategorien</h1>
<table class="table">
  <thead>
    <tr>
      <th scope="col">Titel</th>
      <th scope="col">Option</th>
    </tr>
  </thead>
  <tbody>
      {foreach item=cat from=$FaqCategory}
    <tr>
      <th scope="row">{$cat.title}</th>
      <td><a href="#">editieren</a> <a href="#">Löschen</a></td>
    </tr>
    {/foreach}
  </tbody>
</table>
