<h1 class="display-3 mb-5">FAQ</h1>

<div id="accordion_search_bar_container mb-5">
    <div class="input-group mb-3">

        <input class="form-control" type="search" id="accordion_search_bar" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
            <span class="input-group-text" id="inputGroup-sizing-default">Search</span>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <ul class="list-group">
                {foreach item=category from=$FaqGroupedByCategory}
                <li class="list-group-item"><a href="#">{$category.title}</a> <span class="badge badge-primary">{$category.rows|@count}</span><br></li>
                {/foreach}
            </ul>
        </div>
        <div class="col-md-9">
            <div id="accordion">


            </div>
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                {foreach item=category from=$FaqGroupedByCategory}
                <!-- <h2>{$category.title}</h2> -->
                {foreach key=itemKey item=item from=$category.rows}
                <div class="card">
                    <div class="card-header">
                        <a class="card-link" data-toggle="collapse" href="#collapse{$itemKey}">
                            {$item.title}
                        </a>
                    </div>
                    <div id="collapse{$itemKey}" class="collapse show" data-parent="#accordion">
                        <div class="card-body">
                            {$item.text|unescape:"htmlall"}
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
