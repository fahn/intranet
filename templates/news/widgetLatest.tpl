<div class="card mt-3 last news">
    <h5 class="card-header"><i class="fas fa-newspaper"></i> Letzte Neuigkeiten</h5>
    <ul class="list-group list-group-flush active">
        {foreach item=$item from=$data}
            <li class="list-group-item">{$item.lastEdited|date_format:"d.m.Y"} // {$item.title} <span class="badge badge-primary">{$item.categoryTitle}</span></li>
        {/foreach}
    </ul>
</div>
