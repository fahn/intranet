{include file="page_wrap_header.tpl"}

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
        <div class="col-3">
            <div class="list-group" id="list-tab" role="tablist">
                {foreach item=item from=$Categories}
                    <a class="list-group-item list-group-item-action" id="list-{$item.title}-list" data-toggle="list" href="#list-{$item.title}" role="tab" aria-controls="{$item.title}">{if $item.pid > 0}--{/if} {$item.title} <span class="badge badge-primary">{$item.items}</span></a>
                {/foreach}
            </div>
        </div>
        <div class="col-9">
            <div class="tab-content" id="nav-tabContent">
                {foreach item=category from=$FaqGroupedByCategory}
                  <div class="tab-pane fade show active" id="list-{$category.title}" role="tabpanel" aria-labelledby="list-{$category.title}-list">
                      <h2 class="display-5 mb-3 mt-3">{$category.title}</h2>
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
                                  <span class="small">Erstellt: {$item.createdBy}; Bearbeitet: {$item.lastEdited}</span>
                              </div>
                          </div>
                      </div>
                      {/foreach}
                  </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>


{include file="page_wrap_footer.tpl"}