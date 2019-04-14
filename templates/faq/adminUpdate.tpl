<form action="" method="post">
    <input type="hidden" name="faqFormAction" value="{if $action == 'edit'}Update{else}Insert{/if}">
    {if $action == 'edit'}
        <input type="hidden" name="faqFaqId" value="{$item.faqId}">
    {/if}
    <div class="form-group">
        <label for="faqTitle">Titel</label>
        <input type="text" class="form-control" id="faqTitle" name="faqTitle" value={$item.title}>
    </div>
    <div class="form-group">
        <label for="faqCategoryId">Kategorie</label>
        <select class="form-control" id="faqCategoryId" name="faqCategoryId">
            {html_options options=$CategoryHtmlOptions selected=$item.categoryId}
        </select>
    </div>
    <div class="form-group">
        <label for="summernote">Text</label>
        <textarea id="summernote" class="form-control" name="faqText" rows="3">{$item.text}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">{if $action == 'edit'}Editieren{else}Submit{/if}</button>
</form>
