 <!-- ADMIN SETTINGS LIST -->

{include file="page_wrap_header.tpl"}



{function name=dataTypIcon level=0}
    {if $data == "array"}
        <i class="fas fa-list"></i>
    {elseif $data == "string"}
        <i class="fas fa-font"></i>
    {elseif $data == "bool"}
        <i class="fas fa-check"></i>
    {else}
        <i class="far fa-question-circle"></i>
    {/if}
{/function}

<h1 class="display-3">Settings</h1>

<div class="row mb-5">
    <div class="col-lg-12 text-right">
        <a class="btn btn-success" href="{$settings.add}" role="button">Hinzufügen</a>
    </div>
</div>
<table class="table">
    <thead>
        <tr>
            <th scope="col">Name</th>
            <th scope="col">Wert</th>
            <th scope="col">Datentyp</th>
            <th scope="col" class="text-center">Option</th>
        </tr>
    </thead>
    <tbody>
        {foreach item=item from=$list}
        <tr>
            <td scope="row">{if isset($item.isProtected)}<i class="fas fa-unlock-alt" style="color: red" title="Value is protected"></i>{/if} {$item.name}</td>
            <td scope="row">{$item.value|truncate:30:"..."}</td>
            <td>{dataTypIcon data=$item.dataType}</td>
            <td class="text-center">
                {if !isset($item.isProtected)}
                    <a class="btn btn-info" href="{$item.editLink}">editieren</a> <a class="btn btn-danger" href="{$item.deleteLink}">Löschen</a>
                {/if}
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>


{include file="page_wrap_footer.tpl"}