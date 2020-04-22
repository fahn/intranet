<h1 class="display-1 mb-5">Kategorien</h1>
{if isset($links.add)}
    <div class="row mb-5">
        <div class="col-lg-12 text-right">
            <a class="btn btn-success" href="{$links.add}" role="button"><i class="fas fa-plus"></i> Hinzufügen</a>
        </div>
    </div>
{/if}

<div class="alert alert-warning fade in alert-dismissible show">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true" style="font-size:20px">×</span>
    </button>
    <strong>Info!</strong> Diese Kategorien gelten für den FAQ- und News-Bereich.
</div>

<table class="table">
    <thead>
        <tr>
            <th scope="col">Titel</th>
            <th scope="col">PID</th>
            <th scope="col" width="300" class="text-center">Option</th>
        </tr>
    </thead>
    <tbody>
        {foreach item=item from=$list}
        <tr>
            <td scope="row">{$item.title}</td>
            <td>{if $item.pidName}{$item.pidName}{else}--{/if}</td>
            <td class="text-center"><a class="btn btn-info" href="{$item.editLink}">editieren</a> <a class="btn btn-danger" href="{$item.deleteLink}">Löschen</a></td>
        </tr>
        {/foreach}
    </tbody>
</table>
