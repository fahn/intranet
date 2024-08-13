 <!-- ADMIN Category UPDATE -->

{include file="page_wrap_header.tpl"}

<h1 class="display-3 mb-5">Kategorie {if $action == 'edit'}editieren{else}hinzufügen{/if}</h1>

<form action="" method="post">
    <input type="hidden" name="categoryFormAction" value="{if $action == 'edit'}Update{else}Insert{/if}">
    {if $action == 'edit'}
        <input type="hidden" name="categoryCategoryId" value="{$item.newsId|default:''}">
    {/if}

    <div class="form-group">
        <label for="newsTitle">Titel</label>
        <input type="text" class="form-control" id="newsTitle" name="newsTitle" value={$item.title|default:''}>
    </div>
    <div class="form-group">
        <label for="newsTitle">PID</label>
        <input type="text" class="form-control" id="newsTitle" name="newsTitle" value={$item.pid|default:''}>
    </div>
    <button type="submit" class="btn btn-primary">{if $action == 'edit'}Editieren{else}Submit{/if}</button>

    <a href="{$links.home}" class="btn btn-danger">Zurück</a>

</form>


{include file="page_wrap_footer.tpl"}