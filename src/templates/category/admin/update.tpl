<form action="" method="post">
    <input type="hidden" name="categoryFormAction" value="{if $action == 'edit'}Update{else}Insert{/if}">
    {if $action == 'edit'}
    <input type="hidden" name="categoryCategoryId" value="{$item.newsId}">
    {/if}
    <div class="form-group">
        <label for="newsTitle">Titel</label>
        <input type="text" class="form-control" id="newsTitle" name="newsTitle" value={$item.title}>
    </div>
    <div class="form-group">
        <label for="newsTitle">PID</label>
        <input type="text" class="form-control" id="newsTitle" name="newsTitle" value={$item.pid}>
    </div>
    <button type="submit" class="btn btn-primary">{if $action == 'edit'}Editieren{else}Submit{/if}</button>

    <a href="{$links.home}" class="btn btn-danger">Zurück</a>

</form>
