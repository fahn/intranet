{include file="page_wrap_header.tpl"}

<h1 class="display-3 mb-5">{$headline}</h1>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning text-center">
          {$text}
        </div>
        <p class="text-right"><small>Besteht seit {$date}</small></p>
        <p class="text-center">
            <a class="btn btn-warning" href="{$link}">Reload der Webseite</a>
        </p>
    </div>
</div>

{include file="page_wrap_footer.tpl"}