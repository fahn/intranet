<form action="" method="post">
    <input type="hidden" name="newsFormAction" value="{if $action == 'edit'}Update{else}Insert{/if}">
    {if $action == 'edit'}
    <input type="hidden" name="newsNewsId" value="{$item.newsId}">
    {/if}
    <div class="form-group">
        <label for="newsTitle">Titel</label>
        <input type="text" class="form-control" id="newsTitle" name="newsTitle" value={$item.title}>
    </div>
    <div class="row initline">
        <div class="col-md-6">
            <div class="form-group date">
                <label for="newsCategoryId">Datum</label>
                <input type="text" class="form-control" id="newsTitle" name="newsTitle" value={$item.title}>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="faqCategoryId">Kategorie</label>
                <select class="form-control" id="newsCategoryId" name="newsCategoryId">
                    {html_options options=$CategoryHtmlOptions selected=$item.categoryId}
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="summernote">Text</label>
        <textarea id="summernote" class="form-control" name="newsText" rows="3">{$item.text}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">{if $action == 'edit'}Editieren{else}Submit{/if}</button>
    
    <a href="{$links.home}" class="btn btn-danger">Zurück</a>

</form>
