<h1 class="display-3">News</h1>
{if isset($links.add)}
    <div class="row mb-5">
        <div class="col-lg-12 text-right">
            <a class="btn btn-success" href="{$links.add}" role="button"><i class="fas fa-plus"></i> Hinzufügen</a>
        </div>
    </div>
{/if}
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col">Titel</th>
                <th scope="col">Datum</th>
                <th scope="col">User</th>
                <th scope="col">Kategorie</th>
                <th scope="col">Option</th>
            </tr>
        </thead>
        <tbody>
            {foreach item=item from=$newsList}
            <tr>
                <td scope="row">{$item.title}</td>
                <td scope="row">{$item.createdBy|date_format:"d.m.Y H:i"}</td>
                <td scope="row">{$item.userName}</td>
                <td scope="row">{$item.categoryTitle}</td>
                <td class="text-center">
                    <a class="btn btn-info" href="{$item.editLink}">editieren</a>
                    <a class="btn btn-danger" href="{$item.deleteLink}">Löschen</a>
                </td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="5" class="text-center">Keine News</td>
            {/foreach}
        </tbody>
    </table>
</div>
