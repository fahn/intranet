<!-- NEWS DETAILS -->

{include file="page_wrap_header.tpl"}

<h1 class="display-3 mb-5">Newsdetails</h1>
<div class="row equal">
    <div class="col-md-12 mb-3  align-items-stretch">
        <div class="card">
            <h5 class="card-header">{$news.title}</h5>
            <div class="card-body">
                {$news.text|default:''}
                <hr>
                Erstellt am: {$news.createdBy} - Aktualisiert am: {$news.lastEdited}<br>
                <a href="/news">zurück</a>
            </div>
        </div>    
    </div>
</div>


{include file="page_wrap_footer.tpl"}