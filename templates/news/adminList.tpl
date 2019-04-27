<h1 class="display-3">News</h1>
<div class="row">
    <div class="col-lg-12 text-right">
        <a class="btn btn-success" href="{$links.add}" role="button">Hinzufügen</a>
    </div>
</div>
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
            {foreach item=item from=$NewsList}
            <tr>
                <th scope="row">{$item.title}</th>
                <th scope="row">{$item.createdBy|date_format:"d.m.Y H:i"}</th>
                <th scope="row">{$item.userName}</th>
                <th scope="row">{$item.categoryTitle}</th>
                <td class="text-center">
                    <a class="btn btn-info" href="{$item.editLink}">editieren</a>
                    <a class="btn btn-danger" href="{$item.deleteLink}">Löschen</a>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
