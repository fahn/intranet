 <!-- ADMIN NEWS UPDATE -->

{include file="page_wrap_header.tpl"}

<h1 class="display-3 mb-5">News {if $action == 'edit'}editieren{else}hinzufügen{/if}</h1>
<form action="" method="post">
    <input type="hidden" name="newsFormAction" value="{if $action == 'edit'}Update{else}Insert{/if}">
    {if $action == 'edit'}
    <input type="hidden" name="newsNewsId" value="{$item.newsId}">
    {/if}
    <div class="form-group">
        <label for="newsTitle">Titel</label>
        <input type="text" class="form-control" id="newsTitle" name="newsTitle" value="{$item.title|default:''}">
    </div>
    <div class="row initline">
        <div class="col-md-6">
            <div class="form-group">
                <label for="newsDate">Datum</label>
                <input type="text" class="form-control datepicker" id="newsDate" name="newsDate" value="{$item.createdBy|date_format:"d.m.Y"}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="newsCategoryId">Kategorie</label>
                <select class="form-control" id="newsCategoryId" name="newsCategoryId">
                    {html_options options=$categoryHtmlOptions selected=$item.categoryId}
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="summernote">Text</label>
        <textarea id="summernote" class="form-control" name="newsText" rows="3">{$item.text|default:''}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">{if $action == 'edit'}Editieren{else}Submit{/if}</button>

    <a href="{$links.home}" class="btn btn-danger">Zurück</a>
</form>


{include file="page_wrap_footer.tpl"}