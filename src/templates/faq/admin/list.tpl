<!-- ADMIN FAQ LIST -->

{include file="page_wrap_header.tpl"}

<h1>FAQ</h1>
<div class="row">
    <div class="col-lg-12 text-right">
        <a class="btn btn-success" href="{$links.add}" role="button">Hinzufügen</a>
    </div>
</div>
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
            <td scope="row">{$item.title}</td>
            <td scope="row">{$item.categoryTitle}</td>
            <td class="text-center"><a class="btn btn-info" href="{$item.editLink}">editieren</a> <a class="btn btn-danger" href="{$item.deleteLink}">Löschen</a></td>
        </tr>
        {/foreach}
    </tbody>
</table>


{include file="page_wrap_footer.tpl"}