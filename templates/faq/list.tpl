<h1 class="display-1 mb-5">FAQ</h1>

<div id="accordion_search_bar_container mb-5">
    <input class="form-control" type="search" id="accordion_search_bar" placeholder="Search" aria-label="Search">
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-mt-3">
            {foreach item=category from=$FaqGroupedByCategory}
                - <a href="#">{$category.title} <span class="badge">{$category.rows|@count}</span></a><br>
            {/foreach}
        </div>
        <div class="col-mt-9">
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                {foreach item=category from=$FaqGroupedByCategory}
                <h2>{$category.title}</h2>
                {foreach key=itemKey item=item from=$category.rows}
                <div class="panel panel-success" id="collapseOne_container">
                    <div class="panel-heading" role="tab" id="heading{$itemKey}">
                        <h4 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{$itemKey}" aria-expanded="true" aria-controls="collapse{$itemKey}">{$item.title}</a></h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            {$item.text}
                            <hr>
                            Erstellt: {$item.createdBy}<br>
                            Bearbeitet: {$item.lastEdited}<br>
                        </div>
                    </div>
                </div>
                {/foreach}
                {/foreach}
            </div>
        </div>
    </div>
</div>
